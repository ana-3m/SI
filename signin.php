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
        // Após a inserção bem-sucedida do usuário, insira na tabela cliente com saldo inicial
        $saldo_inicial = 100; // Saldo inicial de 100€
        $result_cliente = pg_query_params(
            $dbconn,
            "INSERT INTO cliente (pessoa_email, saldo) VALUES ($1, $2)",
            array($email, $saldo_inicial)
        );

        if ($result_cliente) {
            echo "<script>alert('Registration successful! You have been credited with 100€!'); window.location.href = 'profile.php';</script>";
        } else {
            // Adicionando tratamento de erro
            $error = pg_last_error($dbconn);
            echo "<script>alert('Registration successful, but failed to credit your account. Please contact us directly to try to solve this issue. Error: $error');</script>";
        }
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
<form method="POST" action="signin.php">
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
