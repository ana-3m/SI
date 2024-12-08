<?php
session_start();

// Check if a user is logged in
$userLoggedIn = isset($_SESSION['pessoa']);
$loginPlaceholder = $userLoggedIn ? $_SESSION['pessoa']['nome'] : 'login';

if ($userLoggedIn) {

// Check if the logged-in user is a funcionario or a cliente
    $dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

    $userLoggedIn = $_SESSION['pessoa']['email'];

// Query to check if the user is a funcionario
    $result = pg_query_params($dbconn, "SELECT n_fun FROM funcionario WHERE pessoa_email = $1", array($userLoggedIn));
    $isFuncionario = pg_num_rows($result) > 0;

    if ($isFuncionario) {
        header('Location: frotaAdmin.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>rent-a-car</title>
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
    <h1>Rent-A-Car</h1>

    <!-- Display success or error messages -->
    <?php if (!empty($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <p style="color: green;">Car added successfully!</p>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <p style="color: red;">Error: <?= htmlspecialchars($_GET['message'] ?? 'Unknown error') ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Display the car list -->
    <h2>Available Cars:</h2>

    <?php
    // Connect to PostgreSQL database
    $connection = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");
    if (!$connection) {
        echo "An error occurred while connecting to the database.<br>";
        exit;
    }

    // Query the cars table
    $result = pg_query($connection, "SELECT * FROM carro");
    if (!$result) {
        echo "An error occurred while fetching data.<br>";
        exit;
    }
    ?>

    <table border="1px">
        <tr>
            <th>Matricula</th>
            <th>Marca</th>
            <th>Modelo</th>
            <th>Ano</th>
            <th>Cor</th>
            <th>Kms</th>
            <th>N_de_reservas</th>
        </tr>

        <?php
        // Loop through the query results and display them in the table
        if (pg_num_rows($result) > 0) {
            while ($row = pg_fetch_assoc($result)) {
                echo "<tr>
                      <td>" . htmlspecialchars($row['matricula']) . "</td>
                      <td>" . htmlspecialchars($row['marca']) . "</td>
                      <td>" . htmlspecialchars($row['modelo']) . "</td>
                      <td>" . htmlspecialchars($row['ano']) . "</td>
                      <td>" . htmlspecialchars($row['cor']) . "</td>
                      <td>" . htmlspecialchars($row['kms']) . "</td>
                      <td>" . htmlspecialchars($row['n_de_reservas']) . "</td>
                  </tr>";
            }
        } else {
            echo "<tr><td colspan='7'>No cars available.</td></tr>";
        }

        // Free result memory and close connection
        pg_free_result($result);
        pg_close($connection);
        ?>
    </table>
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
