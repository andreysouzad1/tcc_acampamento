<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscrição - Acampamento</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #ffffff;
            padding: 30px;
            margin: auto;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
            color: #555;
        }
        input[type="text"],
        input[type="email"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-top: 5px;
        }
        button {
            width: 100%;
            background-color: #28a745;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background-color: #218838;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Formulário de Inscrição</h2>
    
    <form action="processa.php" method="POST">
        
        <div class="form-group">
            <label for="nome">Nome *</label>
            <input type="text" id="nome" name="nome" placeholder="Insira seu nome" required>
        </div>

        <div class="form-group">
            <label for="cpf">CPF:</label>
            <input type="text" id="cpf" name="cpf" required placeholder="000.000.000-00">
        </div>

        <div class="form-group">
            <label>Gênero *</label>
            <div class="radio-group">
                <label><input type="radio" name="genero" value="Mulher" required> Mulher</label>
                <label><input type="radio" name="genero" value="Homem"> Homem</label>
            </div>
        </div>

        <div class="form-group">
            <label for="data_nascimento">Data de Aniversário *</label>
            <input type="date" id="data_nascimento" name="data_nascimento" required>
        </div>

        <div class="form-group">
            <label for="telefone">Telefone *</label>
            <input type="text" id="telefone" name="telefone" placeholder="ex: 4199135-8899" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail *</label>
            <input type="email" id="email" name="email" placeholder="Insira seu email" required>
        </div>

        <div class="form-group">
            <label for="endereco">Endereço *</label>
            <input type="text" id="endereco" name="endereco" placeholder="Insira seu endereço" required>
        </div>

        <div class="form-group">
            <label>Faz uso de medicamento controlado?</label>
            <div class="radio-group">
                <label><input type="radio" name="medicamento_controlado" value="Sim" required> Sim</label>
                <label><input type="radio" name="medicamento_controlado" value="Não"> Não</label>
            </div>
        </div>

        <div class="form-group">
            <label>Possui alguma restrição alimentar?</label>
            <div class="radio-group">
                <label><input type="radio" name="restricao_alimentar" value="Sim" required> Sim</label>
                <label><input type="radio" name="restricao_alimentar" value="Não"> Não</label>
            </div>
        </div>

        <div class="form-group">
            <label>Possui alguma alergia?</label>
            <div class="radio-group">
                <label><input type="radio" name="alergia" value="Sim" required> Sim</label>
                <label><input type="radio" name="alergia" value="Não"> Não</label>
            </div>
        </div>

        <div class="form-group">
            <label for="contato_emergencia">Contato de Emergência *</label>
            <input type="text" id="contato_emergencia" name="contato_emergencia" placeholder="ex: 4199135-8899" required>
        </div>

        <div class="form-group">
            <label for="nome_contato_emergencia">Nome do Contato de Emergência *</label>
            <input type="text" id="nome_contato_emergencia" name="nome_contato_emergencia" placeholder="Insira o nome do contato" required>
        </div>

        <button type="submit">Enviar</button>

    </form>
</div>

</body>
</html>