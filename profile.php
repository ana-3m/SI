<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['pessoa'])) {
    // If no session, redirect to login page
    header("Location: login.php");
    exit;
}

// Retrieve user data from the session
$pessoa = $_SESSION['pessoa'];

//---------
// Connect to the database to retrieve the user's balance
$dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

// Fetch the user's balance from the 'cliente' table
$result = pg_query_params(
    $dbconn,
    "SELECT saldo FROM cliente WHERE pessoa_email = $1",
    array($pessoa['email'])
);

if (!$result) {
    echo "Error querying balance: " . pg_last_error($dbconn);
    exit;
}

$row = pg_fetch_assoc($result);

$saldo = $row ? $row['saldo'] : 0;

// Fetch the user's reservations from the 'reserva' table
$reserva_query = pg_query_params(
    $dbconn,
    "SELECT r.id, r.carro_matricula, c.marca, c.modelo, r.data_ini, r.data_fim 
     FROM reserva r
     JOIN carro c ON r.carro_matricula = c.matricula
     WHERE r.cliente_pessoa_email = $1",
    array($pessoa['email'])
);

if (!$reserva_query) {
    echo "Error querying reservations: " . pg_last_error($dbconn);
    exit;
}

// Store reservations in an array
$reservas = [];
while ($reserva = pg_fetch_assoc($reserva_query)) {
    $reservas[] = $reserva;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Personal Page</title>
    <link href="css/header.css" rel="stylesheet" type="text/css"/>
    <link href="css/footer.css" rel="stylesheet" type="text/css"/>
    <link href="css/main.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<header>
    <div class="logo">
        <a href="index.php" title="logotipo">
            <img src="data/imagens/logo.png" alt="logo" height="50px" width="50px">
        </a>
    </div>
    <div class="menu">
        Menu
        <div class="menu-options">
            <a class="menu-option" href="frota.php">Frota</a>
            <a class="menu-option" href="quemsomos.php">Quem somos</a>
            <a class="menu-option" href="reservas.php">Reservas</a>
            <a class="menu-option" href="reviews.php">Reviews</a>
        </div>
    </div>
    <div style="visibility: hidden">
        <?php echo htmlspecialchars($pessoa['nome']); ?>
    </div>
</header>

<main>
    <div class="profile-info">
        <h1>Welcome, <?php echo htmlspecialchars($pessoa['nome']); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($pessoa['email']); ?></p>
        <p>Saldo: <?php echo htmlspecialchars(number_format($saldo, 2, ',', ' ')); ?> €</p>
        <div class="reservations">
            <h2>Your Reservations</h2>
            <?php if (count($reservas) > 0): ?>
                <table border="1px">
                    <thead>
                    <tr>
                        <th>ID da reserva</th>
                        <th>Carro</th>
                        <th>Data de início</th>
                        <th>Data de fim</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reserva['id']); ?></td>
                            <td><?php echo htmlspecialchars($reserva['marca'] . " " . $reserva['modelo']); ?></td>
                            <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($reserva['data_ini']))); ?></td>
                            <td><?php echo htmlspecialchars(date("d/m/Y", strtotime($reserva['data_fim']))); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No reservations found.</p>
            <?php endif; ?>
        </div>
        <p><a href="php/logout.php">Logout</a></p>
    </div>
</main>

<footer class="footer">
    <div class="footer-content">
        <div class="footer-column">
            <h2>Contactos:</h2>
            <p>
                239 999 999<br/>
                rent.a.car.uc@gmail.com
            </p>
        </div>
        <div class="footer-column">
            <h2>Redes Sociais</h2> <br>
            <img src="data/imagens/facebook.png" alt="facebook" class="icones" id="facebook"/>
            <img src="data/imagens/instagram.png" alt="instagram" class="icones" id="instagram"/>
        </div>
        <div class="footer-logo">
            <div class="logo-box">
                <img src="data/imagens/logo.png" alt="logo" height="200px" width="200px">
            </div>
        </div>
    </div>
</footer>
</body>
</html>
