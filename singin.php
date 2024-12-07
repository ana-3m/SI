<?php
session_start();

// Signup Logic (PHP)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password for security

    // Insert user into the database
    $result = pg_query_params(
        $dbconn,
        "INSERT INTO pessoa (email, nome, password) VALUES ($1, $2, $3)",
        array($email, $nome, $password)
    );

    if ($result) {
        echo "<script>alert('Registration successful!'); window.location.href = 'profile.php';</script>";
    } else {
        echo "<script>alert('Registration failed. Email may already be in use.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
</head>
<body>
<h1>Signup</h1>
<form method="POST" action="singin.php">
    <label for="nome">Name:</label><br>
    <input type="text" id="nome" name="nome" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" id="email" name="email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" required><br><br>

    <button type="submit">Signup</button>
</form>
<p>Already have an account? <a href="login.php">Login here</a>.</p>
</body>
</html>
