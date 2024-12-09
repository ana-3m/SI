<?php
session_start();

// Login Logic (PHP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve the user from the database
    $result = pg_query_params($dbconn, "SELECT * FROM pessoa WHERE email = $1", array($email));
    $pessoa = pg_fetch_assoc($result);

    if ($pessoa && password_verify($password, $pessoa['password'])) {
        // Set session variables
        $_SESSION['pessoa'] = ['email' => $pessoa['email'], 'nome' => $pessoa['nome']];
        echo "<script>alert('Login successful!'); window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('Invalid email or password.');</script>";
    }
}

// Verificar se há mensagem de redirecionamento
if (isset($_SESSION['redirect_message'])) {
    $redirect_message = $_SESSION['redirect_message'];
    unset($_SESSION['redirect_message']); // Limpar a mensagem após exibição
} else {
    $redirect_message = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script>
        // Exibir mensagem de redirecionamento, se existir
        <?php if ($redirect_message): ?>
        alert("<?php echo htmlspecialchars($redirect_message); ?>");
        <?php endif; ?>
    </script>
    <link href="css/header.css" rel="stylesheet" type="text/css">
    <link href="css/login.css" rel="stylesheet" type="text/css"/>
    <link href="css/footer.css" rel="stylesheet" type="text/css"/>
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

    </div>
</header>
<main>
<h1>Login</h1>
<form method="POST" action="login.php">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" placeholder="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="password" required>

    <button type="submit">Login</button> 
</form>
<p id="small">Don't have an account? <a href="signin.php">Signup here</a>.</p>
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
