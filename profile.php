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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Personal Page</title>
    <link href="css/style.css" rel="stylesheet" type="text/css" />
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
        <p><a href="logout.php">Logout</a></p>
    </div>
</main>
</body>
</html>
