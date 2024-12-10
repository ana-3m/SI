<?php
session_start();
$dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

if (!$dbconn) {
    die("Erro ao conectar ao banco de dados: " . pg_last_error());
}

// Total de carros
$total_carros_query = "SELECT COUNT(*) AS total_carros FROM carro";
$total_carros_result = pg_query($dbconn, $total_carros_query);
$total_carros = pg_fetch_result($total_carros_result, 0, 'total_carros');

// Total de utilizadores
$total_pessoa_query = "SELECT COUNT(*) AS total_pessoa FROM pessoa";
$total_pessoa_result = pg_query($dbconn, $total_pessoa_query);
$total_pessoa = pg_fetch_result($total_pessoa_result, 0, 'total_pessoa');

// Total de utilizadores que já reservaram carros
$total_utilizadores_query = "SELECT COUNT(DISTINCT cliente_pessoa_email) AS total_utilizadores FROM reserva";
$total_utilizadores_result = pg_query($dbconn, $total_utilizadores_query);
$total_utilizadores = pg_fetch_result($total_utilizadores_result, 0, 'total_utilizadores');

// Número médio de reservas por utilizador
$media_reservas_query = "
    SELECT AVG(total_reservas) AS media_reservas
    FROM (
        SELECT COUNT(*) AS total_reservas
        FROM reserva
        GROUP BY cliente_pessoa_email
    ) AS subquery";
$media_reservas_result = pg_query($dbconn, $media_reservas_query);
$media_reservas = round(pg_fetch_result($media_reservas_result, 0, 'media_reservas'), 2);

// Custo médio de um carro
$custo_medio_query = "SELECT AVG(preco) AS custo_medio FROM carro";
$custo_medio_result = pg_query($dbconn, $custo_medio_query);
$custo_medio = round(pg_fetch_result($custo_medio_result, 0, 'custo_medio'), 2);

// Total de dias reservados por todos os carros
$total_dias_query = "
    SELECT SUM((data_fim - data_ini)) AS total_dias
    FROM reserva";
$total_dias_result = pg_query($dbconn, $total_dias_query);

if ($total_dias_result) {
    $total_dias = round(pg_fetch_result($total_dias_result, 0, 'total_dias'), 2);
} else {
    $total_dias = 0; // Caso não existam reservas
}



pg_close($dbconn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estatísticas</title>
    <link href="css/main.css" rel="stylesheet" type="text/css">
</head>
<body>
<header>
    <h1>Estatísticas do Sistema</h1>
</header>
<main>
    <section>
        <h2>Resumo</h2>
        <ul>
            <li><strong>Total de Carros:</strong> <?php echo htmlspecialchars($total_carros); ?></li>
            <li><strong>Total de Utilizadores:</strong> <?php echo htmlspecialchars($total_pessoa); ?></li>
            <li><strong>Total de Utilizadores com Reservas:</strong> <?php echo htmlspecialchars($total_utilizadores); ?></li>
            <li><strong>Número Médio de Reservas por Utilizador:</strong> <?php echo htmlspecialchars($media_reservas); ?></li>
            <li><strong>Custo Médio de um Carro:</strong> €<?php echo htmlspecialchars($custo_medio); ?></li>
            <li><strong>Total de Dias Reservados:</strong> <?php echo htmlspecialchars($total_dias); ?> dias</li>
        </ul>
    </section>
    <a href="frotaAdmin.php">Voltar</a>
</main>
</body>
</html>
