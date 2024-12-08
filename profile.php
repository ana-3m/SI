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

if (!$row) {
    $saldo = 0; // Default balance if no record found
} else {
    $saldo = $row['saldo'];
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
</head>
<body>
<header>
    <div class="logo">
        <a href="index.php" title="logotipo">
            <p>logo</p>
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

    </div>
</header>

<main>
    <div class="profile-info">
        <h1>Welcome, <?php echo htmlspecialchars($pessoa['nome']); ?>!</h1>
        <p>Email: <?php echo htmlspecialchars($pessoa['email']); ?></p>
        <p>Saldo: <?php echo htmlspecialchars(number_format($saldo, 2, ',', ' ')); ?> €</p>
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
                <p>NOSSO<br>LOGOTIPO</p>
            </div>
        </div>
    </div>
</footer>
</body>
</html>
