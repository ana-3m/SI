<?php
session_start();

// Check if a user is logged in
$userLoggedIn = isset($_SESSION['pessoa']);
$loginPlaceholder = $userLoggedIn ? $_SESSION['pessoa']['nome'] : 'login';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>rent-a-car</title>
    <link href="css/header.css" rel="stylesheet" type="text/css"/>
    <link href="css/footer.css" rel="stylesheet" type="text/css"/>
    <link href="css/main.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<script src="js/script.js"></script>

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
    <div class="login">
        <?php if ($userLoggedIn): ?>
            <a href="profile.php" title="Profile">
                <p><?php echo htmlspecialchars($loginPlaceholder); ?></p>
            </a>
        <?php else: ?>
            <a href="login.php" title="Login">
                <p><?php echo htmlspecialchars($loginPlaceholder); ?></p>
            </a>
        <?php endif; ?>
    </div>
</header>
<main>
    <h1>Feedback Form</h1>
    <form id="feedback-form">
        <label for="uname">Name</label>
        <input type="text" id="uname" name="uname" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" required>

        <label>Satisfaction</label>
        <input type="radio" id="yes" name="satisfaction" value="Yes" checked>
        <label for="yes">Yes</label>
        <input type="radio" id="no" name="satisfaction" value="No">
        <label for="no">No</label>

        <label for="suggestions">Suggestions</label>
        <textarea id="suggestions" name="suggestions" required></textarea>

        <button type="button" id="submit-btn">Submit</button>
    </form>

    <h2>Reviews</h2>
    <div id="reviews"></div>
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
<script src="js/script.js"></script>
</body>

</html>