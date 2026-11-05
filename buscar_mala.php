<?php
include("conexao.php");

$inscricao = null;
$erro = "";
$sucesso_classe = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['atualizar_classe'])) {
    $id_inscricao = intval($_POST['id_inscricao']);
    $nova_classe = $conn->real_escape_string($_POST['classe_mala']);
    
    $sql_update = "UPDATE inscricoes SET classe_mala = '$nova_classe' WHERE id = $id_inscricao";
    if ($conn->query($sql_update) === TRUE) {
        $sucesso_classe = "Classe atualizada com sucesso!";
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && (!empty($_POST['busca']) || isset($_POST['id_inscricao']))) {
    $termo_busca = isset($_POST['busca']) ? $_POST['busca'] : $_POST['termo_pesquisa_atual'];
    $busca_escape = $conn->real_escape_string($termo_busca);
    $busca_limpa = preg_replace('/[^0-9]/', '', $busca_escape);

    $sql = "SELECT * FROM inscricoes WHERE cpf LIKE '%$busca_limpa%' OR codigo_mala = '$busca_escape' OR nome LIKE '%$busca_escape%' LIMIT 1";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $inscricao = $result->fetch_assoc();
    } else {
        $erro = "Nenhuma inscrição encontrada com esses dados.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Consulta e Rastreio de Mala - Acampamento</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #16a34a;
            --success-dark: #15803d;
            --warning: #d97706;
            --warning-dark: #b45309;
            --bg-main: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --border-color: #e2e8f0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-main);
            color: var(--text-main);
            margin: 0;
            padding: 30px 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        .card {
            background: var(--card-bg);
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
            border: 1px solid var(--border-color);
        }

        h2 {
            color: var(--primary);
            margin-top: 0;
            text-align: center;
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: #475569;
            font-size: 14px;
        }

        input[type="text"], select {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 15px;
            background-color: #f8fafc;
            transition: all 0.3s;
        }

        input[type="text"]:focus, select:focus {
            outline: none;
            border-color: var(--primary);
            background-color: #fff;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .btn {
            width: 100%;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 15px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-primary {
            background: var(--primary);
            color: #fff;
        }
        .btn-primary:hover {
            background: var(--primary-dark);
        }

        .btn-print {
            background: var(--success);
            color: white;
            margin-top: 15px;
        }
        .btn-print:hover {
            background: var(--success-dark);
        }

        .btn-update {
            background: var(--warning);
            color: white;
            padding: 10px;
            font-size: 14px;
            margin-top: 10px;
        }
        .btn-update:hover {
            background: var(--warning-dark);
        }

        .alert-error {
            background-color: #fef2f2;
            color: #dc2626;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            margin-top: 15px;
            border: 1px solid #fecaca;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: var(--success);
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 20px;
            border: 1px solid #bbf7d0;
        }

        .resultado {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            margin-top: 25px;
        }

        .etiqueta-header {
            font-size: 18px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 5px;
        }

        .codigo-badge {
            display: inline-block;
            background: #e2e8f0;
            padding: 4px 12px;
            border-radius: 6px;
            font-family: monospace;
            font-size: 16px;
            color: #0f172a;
            margin: 5px 0 10px 0;
        }

        .classe-badge {
            display: inline-block;
            background: #fee2e2;
            color: #991b1b;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .qr-box {
            margin-top: 15px;
            background: #fff;
            padding: 12px;
            display: inline-block;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        }

        .qr-box img {
            display: block;
        }

        .info-participante {
            text-align: left;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
            line-height: 1.7;
            color: #475569;
        }

        .painel-alterar-classe {
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            margin-top: 15px;
            text-align: left;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            body * {
                visibility: hidden;
            }
            .resultado, .resultado * {
                visibility: visible;
            }
            .resultado {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                border: none !important;
                background: #fff !important;
                margin: 0;
                padding: 10px;
                box-shadow: none;
            }
            .btn-print, .sem-impressao {
                display: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card">
        <h2>Rastreio de Bagagens</h2>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="busca">Buscar Participante:</label>
                <input type="text" id="busca" name="busca" required placeholder="Digite o CPF ou Nome do Inscrito" value="<?php echo isset($_POST['busca']) ? htmlspecialchars($_POST['busca']) : (isset($_POST['termo_pesquisa_atual']) ? htmlspecialchars($_POST['termo_pesquisa_atual']) : ''); ?>">
            </div>
            <button type="submit" class="btn btn-primary">Pesquisar Inscrição</button>
        </form>

        <?php if (!empty($erro)): ?>
            <div class="alert-error"><?php echo $erro; ?></div>
        <?php endif; ?>

        <?php if (!empty($sucesso_classe)): ?>
            <div class="alert-success" style="margin-top: 20px;"><?php echo $sucesso_classe; ?></div>
        <?php endif; ?>

        <?php if ($inscricao): ?>
            <?php 
                $codigo_mala = $inscricao['codigo_mala'];
                $classe_atual = $inscricao['classe_mala'];
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=" . urlencode($codigo_mala);
            ?>
            <div class="resultado">
                <!-- Informações do Participante e Painel de Classe agora ficam no topo -->
                <div class="info-participante">
                    <strong>Inscrito:</strong> <?php echo htmlspecialchars($inscricao['nome']); ?><br>
                    <strong>CPF:</strong> <?php echo htmlspecialchars($inscricao['cpf']); ?><br>
                    <span class="sem-impressao"><strong>Status Pagamento:</strong> <?php echo htmlspecialchars($inscricao['status_pagamento']); ?><br></span>
                    <strong>Telefone:</strong> <?php echo htmlspecialchars($inscricao['telefone']); ?>

                    <!-- Painel para alterar a classe no dia do embarque -->
                    <div class="painel-alterar-classe sem-impressao">
                        <form method="POST" action="">
                            <input type="hidden" name="id_inscricao" value="<?php echo $inscricao['id']; ?>">
                            <input type="hidden" name="termo_pesquisa_atual" value="<?php echo htmlspecialchars($_POST['busca'] ?? $_POST['termo_pesquisa_atual']); ?>">
                            <label for="classe_mala">Alterar Classe da Mala:</label>
                            <select name="classe_mala" id="classe_mala">
                                <option value="Classe A" <?php if($classe_atual == 'Classe A') echo 'selected'; ?>>Classe A</option>
                                <option value="Classe B" <?php if($classe_atual == 'Classe B') echo 'selected'; ?>>Classe B</option>
                                <option value="Classe C" <?php if($classe_atual == 'Classe C') echo 'selected'; ?>>Classe C</option>
                                <option value="Classe D" <?php if($classe_atual == 'Classe D') echo 'selected'; ?>>Classe D</option>
                                <option value="Classe E" <?php if($classe_atual == 'Classe E') echo 'selected'; ?>>Classe E</option>
                            </select>
                            <button type="submit" name="atualizar_classe" class="btn btn-update">Atualizar Classe</button>
                        </form>
                    </div>
                </div>

                <!-- Bloco do QR Code e Identificação da Mala abaixo dos dados -->
                <div class="etiqueta-header">Etiqueta de Bagagem</div>
                <div>Código: <span class="codigo-badge"><?php echo $codigo_mala; ?></span></div>
                <div>Classe: <span class="classe-badge"><?php echo htmlspecialchars($classe_atual); ?></span></div>
                
                <div class="qr-box">
                    <img src="<?php echo $qrUrl; ?>" alt="QR Code Mala <?php echo $codigo_mala; ?>">
                </div>
                
                <div>
                    <button onclick="window.print()" class="btn btn-print">Imprimir Etiqueta da Mala</button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>