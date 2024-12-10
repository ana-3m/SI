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
</body>
</html>
