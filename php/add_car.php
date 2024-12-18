<?php
// Connect to PostgreSQL database
$connection = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");
if (!$connection) {
    die("An error occurred while connecting to the database: " . pg_last_error());
}

// Initialize an array to hold errors
$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $matricula = isset($_POST['matricula']) ? htmlspecialchars($_POST['matricula'], ENT_QUOTES, 'UTF-8') : null;
    if (!$matricula) {
        $errors[] = "Matricula is required and should be valid text.";
    }

    $marca = isset($_POST['marca']) ? htmlspecialchars($_POST['marca'], ENT_QUOTES, 'UTF-8') : null;
    if (!$marca) {
        $errors[] = "Marca is required and should be valid text.";
    }

    $modelo = isset($_POST['modelo']) ? htmlspecialchars($_POST['modelo'], ENT_QUOTES, 'UTF-8') : null;
    if (!$modelo) {
        $errors[] = "Modelo is required and should be valid text.";
    }

    $ano = isset($_POST['ano']) ? $_POST['ano'] : null;
    if (!$ano || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $ano)) {
        $errors[] = "Ano is required and must be a valid date in the format YYYY-MM-DD.";
    }

    $cor = isset($_POST['cor']) ? htmlspecialchars($_POST['cor'], ENT_QUOTES, 'UTF-8') : null;
    if (!$cor) {
        $errors[] = "Cor is required and should be valid text.";
    }

    $kms = filter_input(INPUT_POST, 'kms', FILTER_VALIDATE_INT);
    if ($kms === false || $kms < 0) {
        $errors[] = "KMs is required and must be a valid positive number.";
    }

    $n_de_reservas = filter_input(INPUT_POST, 'n_de_reservas', FILTER_VALIDATE_INT);
    if ($n_de_reservas === false || $n_de_reservas < 0) {
        $errors[] = "Number of reservations is required and must be a valid positive number.";
    }

    $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_INT);
    if ($preco === false || $preco < 0) {
        $errors[] = "Preco is required and must be a valid positive number.";
    }

    // If no errors, insert data into the database
    if (empty($errors)) {
        $query = "INSERT INTO carro (matricula, marca, modelo, ano, cor, kms, n_de_reservas, preco) VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
        $result = pg_query_params($connection, $query, [$matricula, $marca, $modelo, $ano, $cor, $kms, $n_de_reservas, $preco]);

        $query = "INSERT INTO historico_preco (carro_matricula, preco) VALUES ($1, $2)";
        $result = pg_query_params($connection, $query, [$matricula, $preco]);

        if ($result) {
            pg_close($connection);
            header('Location: ../frotaAdmin.php?status=success');
            exit; // Ensure the script halts after the redirect
        } else {
            $errors[] = "An error occurred while adding the car: " . pg_last_error($connection);
        }
    }
}

// If there were errors, redirect back to frota.php with error message
if (!empty($errors)) {
    $error_message = urlencode(implode(', ', $errors));
    pg_close($connection);
    header("Location: ../frotaAdmin.php?status=error&message=$error_message");
    exit; // Ensure the script halts after the redirect
}
?>
