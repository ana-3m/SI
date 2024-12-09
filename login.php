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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="css/login.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<h1>Login</h1>
<form method="POST" action="login.php">
    <label for="email">Email:</label>
    <input type="email" id="email" name="email" placeholder="email" required>

    <label for="password">Password:</label>
    <input type="password" id="password" name="password" placeholder="password" required>

    <button type="submit">Login</button>
</form>
<p id="small">Don't have an account? <a href="signin.php">Signup here</a>.</p>
</body>
</html>