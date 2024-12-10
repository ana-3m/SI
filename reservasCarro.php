<?php
session_start();
$dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

if (!isset($_GET['matricula'])) {
    echo "Carro não especificado.";
    exit();
}

$matricula = $_GET['matricula'];

// Buscar informações sobre o carro
$carro_query = "SELECT * FROM carro WHERE matricula = $1";
$carro_result = pg_query_params($dbconn, $carro_query, array($matricula));
$carro = pg_fetch_assoc($carro_result);

if (!$carro) {
    echo "Carro não encontrado.";
    exit();
}

// Buscar reservas associadas ao carro
$reservas_query = "
    SELECT r.data_ini, r.data_fim, r.preco_pago, p.email 
    FROM reserva r
    JOIN pessoa p ON r.cliente_pessoa_email = p.email
    WHERE r.carro_matricula = $1
    ORDER BY r.data_ini";
$reservas_result = pg_query_params($dbconn, $reservas_query, array($matricula));
$reservas = pg_fetch_all($reservas_result);

// Buscar histórico de preços do carro
$historico_preco_query = "
    SELECT preco, data
    FROM historico_preco
    WHERE carro_matricula = $1
    ORDER BY data DESC";
$historico_preco_result = pg_query_params($dbconn, $historico_preco_query, array($matricula));
$historico_preco = pg_fetch_all($historico_preco_result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservas do Carro</title>
</head>
<body>
<h1>Reservas para o carro <?php echo htmlspecialchars($carro['marca'] . " " . $carro['modelo'] . ' (' . $carro['matricula'] . ')'); ?></h1>
<a href="frotaAdmin.php">Voltar</a>

<!-- Tabela de Reservas -->
<h2>Reservas</h2>
<table border="1">
    <thead>
    <tr>
        <th>Data de Início</th>
        <th>Data de Fim</th>
        <th>Preço Pago</th>
        <th>Email do Cliente</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($reservas): ?>
        <?php foreach ($reservas as $reserva): ?>
            <tr>
                <td><?php echo htmlspecialchars($reserva['data_ini']); ?></td>
                <td><?php echo htmlspecialchars($reserva['data_fim']); ?></td>
                <td><?php echo htmlspecialchars($reserva['preco_pago']); ?></td>
                <td><?php echo htmlspecialchars($reserva['email']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">Nenhuma reserva encontrada.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<!-- Tabela de Histórico de Preços -->
<h2>Histórico de Preços</h2>
<table border="1">
    <thead>
    <tr>
        <th>Preço</th>
        <th>Data de Alteração</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($historico_preco): ?>
        <?php foreach ($historico_preco as $historico): ?>
            <tr>
                <td><?php echo htmlspecialchars($historico['preco']); ?></td>
                <td><?php echo htmlspecialchars($historico['data']); ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="2">Nenhum histórico de preços encontrado.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

</body>
</html>