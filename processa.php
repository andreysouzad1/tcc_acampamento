<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("conexao.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';

$apiKey = '$aact_hmlg_000MzkwODA2MWY2OGM3MWRlMDU2NWM3MzJlNzZmNGZhZGY6OmRjMWVhOWFjLTc3MzgtNGUxNy04NDM2LTRkNjU2Yjc1MzA1YTo6JGFhY2hfYTVjZWY5MGUtYzhjNC00MmViLTkwZTgtYjYyNDBhZTg1OTU4';

$headers = [
    "Content-Type: application/json",
    "access_token: " . $apiKey,
    "User-Agent: TCC-Acampamento-Andrey"
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $data_nascimento = $_POST['data_nascimento'];
    $genero = $_POST['genero'];
    $telefone = $_POST['telefone'];
    $email = $_POST['email'];
    $endereco = $_POST['endereco'];
    $medicamento_controlado = $_POST['medicamento_controlado'];
    $restricao_alimentar = $_POST['restricao_alimentar'];
    $alergia = $_POST['alergia'];
    $contato_emergencia = $_POST['contato_emergencia'];
    $nome_contato_emergencia = $_POST['nome_contato_emergencia'];

    $codigo_mala = 'MALA-' . strtoupper(substr(md5(uniqid()), 0, 6));

    $sql = "INSERT INTO inscricoes (nome, cpf, data_nascimento, genero, telefone, email, endereco, medicamento_controlado, restricao_alimentar, alergia, contato_emergencia, nome_contato_emergencia, status_pagamento, codigo_mala) 
            VALUES ('$nome', '$cpf', '$data_nascimento', '$genero', '$telefone', '$email', '$endereco', '$medicamento_controlado', '$restricao_alimentar', '$alergia', '$contato_emergencia', '$nome_contato_emergencia', 'Pendente', '$codigo_mala')";

    if ($conn->query($sql) === TRUE) {
        $inscricao_id = $conn->insert_id;
        echo "<!-- Passo 1: Inserido no Banco com Sucesso -->";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://sandbox.asaas.com/api/v3/customers");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
            "name" => $nome,
            "email" => $email,
            "phone" => $telefone,
            "cpfCnpj" => preg_replace('/[^0-9]/', '', $cpf)
        ]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $responseCliente = curl_exec($ch);
        curl_close($ch);
        $clienteData = json_decode($responseCliente, true);

        if (isset($clienteData['id'])) {
            $clienteId = $clienteData['id'];
            echo "<!-- Passo 2: Cliente Criado no Asaas -->";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://sandbox.asaas.com/api/v3/payments");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
                "customer" => $clienteId,
                "billingType" => "PIX",
                "value" => 100.00,
                "dueDate" => date('Y-m-d', strtotime('+3 days')),
                "description" => "Inscrição Acampamento - TCC"
            ]));
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            $responseCobranca = curl_exec($ch);
            curl_close($ch);
            $cobrancaData = json_decode($responseCobranca, true);

            if (isset($cobrancaData['id'])) {
                $paymentId = $cobrancaData['id'];
                $invoiceUrl = isset($cobrancaData['invoiceUrl']) ? $cobrancaData['invoiceUrl'] : '';

                $conn->query("UPDATE inscricoes SET asaas_id = '$paymentId' WHERE id = $inscricao_id");
                echo "<!-- Passo 3: Cobrança Pix Gerada -->";

                $mail = new PHPMailer(true);
                try {
                    $mail->isSMTP();
                    $mail->Host       = 'smtp.gmail.com';
                    $mail->SMTPAuth   = true;
                    $mail->Username   = 'testemail.acampamento@gmail.com';
                    $mail->Password   = 'xcis qwav spui bxxi';
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port       = 587;
                    $mail->CharSet    = 'UTF-8';

                    $mail->setFrom('testemail.acampamento@gmail.com', 'Organização do Acampamento');
                    $mail->addAddress($email, $nome);

                    $mail->isHTML(true);
                    $mail->Subject = 'Confirmação de Inscrição - Acampamento';
                    $mail->Body    = "
                        <div style='font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; border: 1px solid #ddd; padding: 20px; border-radius: 8px;'>
                            <h2 style='color: #2563eb;'>Olá, {$nome}!</h2>
                            <p>A tua inscrição no <strong>Acampamento</strong> foi registada com sucesso!</p>
                            
                            <div style='background: #f8fafc; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0;'>
                                <p style='margin: 0;'><strong>Código da tua Bagagem:</strong> <span style='font-family: monospace; font-size: 18px; color: #1e293b;'>{$codigo_mala}</span></p>
                                <small style='color: #64748b;'>Guarda este código para o dia do embarque.</small>
                            </div>

                            <h3>Orientações Importantes:</h3>
                            <ul>
                                <li><strong>Check-in:</strong> Apresenta o teu CPF no momento do embarque.</li>
                                <li><strong>Bagagem:</strong> Cada participante tem direito a identificar a sua mala no local.</li>
                                <li><strong>Pagamento:</strong> Podes aceder à tua fatura Pix através do botão abaixo.</li>
                            </ul>

                            <br>
                            <div style='text-align: center;'>
                                <a href='{$invoiceUrl}' target='_blank' style='background-color: #16a34a; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; display: inline-block;'>Pagar Inscrição via Pix</a>
                            </div>

                            <br><hr style='border: none; border-top: 1px solid #eee;'><br>
                            <p style='font-size: 12px; color: #94a3b8; text-align: center;'>Esta é uma mensagem automática. Por favor não repondas a este e-mail.</p>
                        </div>
                    ";

                    $mail->send();
                    $email_status = "E-mail de confirmação enviado com sucesso!";
                } catch (Exception $e) {
                    $email_status = "Erro no E-mail: {$mail->ErrorInfo}";
                }

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, "https://sandbox.asaas.com/api/v3/payments/{$paymentId}/pixQrCode");
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $responsePix = curl_exec($ch);
                curl_close($ch);
                $pixData = json_decode($responsePix, true);

                echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ccc; border-radius: 8px;'>";
                echo "<h2 style='color: #2e7d32;'>Inscrição realizada com sucesso!</h2>";
                echo "<p style='color: #2563eb; font-weight: bold;'>{$email_status}</p>";
                
                echo "<div style='background: #e8f5e9; border: 1px dashed #2e7d32; padding: 12px; border-radius: 6px; margin-bottom: 20px; text-align: center;'>";
                echo "<strong>Código de Rastreio da Mala:</strong><br>";
                echo "<span style='font-size: 22px; font-weight: bold; color: #1b5e20; font-family: monospace;'>{$codigo_mala}</span>";
                echo "</div>";

                if (isset($pixData['encodedImage']) && !empty($pixData['encodedImage'])) {
                    echo "<div style='text-align:center; margin-top:20px; background: #f9f9f9; padding: 15px; border-radius: 6px;'>";
                    echo "<strong>QR Code Pix:</strong><br><br>";
                    echo "<img src='data:image/png;base64," . $pixData['encodedImage'] . "' alt='QR Code Pix' style='max-width:200px;' /><br>";
                    echo "</div>";
                }

                echo "<div style='margin-top: 25px; text-align: center;'>";
                echo "<a href='index.php' style='color: #555; text-decoration: underline;'>Fazer nova inscrição</a>";
                echo "</div>";
                echo "</div>";
                
                $conn->close();
                exit();
            } else {
                echo "<h3>Erro ao criar cobrança no Asaas:</h3>";
                echo "<pre>" . htmlspecialchars($responseCobranca) . "</pre>";
            }
        } else {
            echo "<h3>Erro ao criar cliente no Asaas:</h3>";
            echo "<pre>" . htmlspecialchars($responseCliente) . "</pre>";
        }
    } else {
        echo "<h3>Erro no Banco de Dados (CPF duplicado?):</h3>";
        echo "<p>" . $conn->error . "</p>";
    }
}
?>