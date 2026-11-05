<?php
include("conexao.php");

// Consulta para buscar todas as inscrições ordenadas da mais recente para a mais antiga
$sql = "SELECT * FROM inscricoes ORDER BY id DESC";
$result = $conn->query($sql);

// Estatísticas rápidas
$total_inscritos = $result->num_rows;
$sql_pendentes = "SELECT COUNT(*) as total FROM inscricoes WHERE status_pagamento = 'Pendente'";
$res_pendentes = $conn->query($sql_pendentes);
$total_pendentes = $res_pendentes->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Gestão - Acampamento</title>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --success: #16a34a;
            --warning: #d97706;
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }

        h2 {
            color: var(--primary);
            margin: 0;
            font-size: 26px;
            font-weight: 600;
        }

        .nav-links a {
            background: var(--primary);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            margin-left: 10px;
            transition: background 0.2s;
        }

        .nav-links a:hover {
            background: var(--primary-dark);
        }

        /* Cartões de Estatísticas */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: 10px;
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
        }

        .stat-card h4 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #64748b;
        }

        .stat-card .number {
            font-size: 28px;
            font-weight: bold;
            color: var(--text-main);
        }

        /* Tabela de Dados */
        .card-table {
            background: var(--card-bg);
            border-radius: 12px;
            border: 1px solid var(--border-color);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }

        th {
            background: #f1f5f9;
            color: #475569;
            padding: 15px;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }

        td {
            padding: 14px 15px;
            border-bottom: 1px solid var(--border-color);
            color: #334155;
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover {
            background-color: #f8fafc;
        }

        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .badge-pending {
            background: #fef3c7;
            color: #b45309;
        }

        .badge-paid {
            background: #dcfce7;
            color: #15803d;
        }

        .codigo-badge {
            font-family: monospace;
            background: #e2e8f0;
            padding: 3px 8px;
            border-radius: 5px;
            font-size: 13px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header-flex">
        <h2>Painel de Gestão - Inscrições</h2>
        <div class="nav-links">
            <a href="index.php">Nova Inscrição</a>
            <a href="buscar_mala.php">Rastreio de Malas</a>
        </div>
    </div>

    <!-- Cards de Resumo -->
    <div class="stats-grid">
        <div class="stat-card">
            <h4>Total de Inscritos</h4>
            <div class="number"><?php echo $total_inscritos; ?></div>
        </div>
        <div class="stat-card">
            <h4>Pagamentos Pendentes</h4>
            <div class="number" style="color: var(--warning);"><?php echo $total_pendentes; ?></div>
        </div>
    </div>

    <!-- Tabela de Registos -->
    <div class="card-table">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Telefone</th>
                    <th>Código da Mala</th>
                    <th>Classe</th>
                    <th>Status Pagamento</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($total_inscritos > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['id']; ?></td>
                            <td><strong><?php echo htmlspecialchars($row['nome']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['cpf']); ?></td>
                            <td><?php echo htmlspecialchars($row['telefone']); ?></td>
                            <td><span class="codigo-badge"><?php echo htmlspecialchars($row['codigo_mala']); ?></span></td>
                            <td><?php echo htmlspecialchars($row['classe_mala']); ?></td>
                            <td>
                                <?php if ($row['status_pagamento'] == 'Pendente'): ?>
                                    <span class="badge badge-pending">Pendente</span>
                                <?php else: ?>
                                    <span class="badge badge-paid">Pago</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px; color: #64748b;">Nenhuma inscrição encontrada na base de dados.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>

<?php $conn->close(); ?>