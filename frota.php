<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width">
    <title>rent-a-car</title>
    <link href="css/style.css" rel="stylesheet" type="text/css"/>
</head>

<body>
<script src="js/script.js"></script>

<header>
    <div class="logo">
        <a href="index.html" title="logotipo">
            <p>logo</p>
        </a>
    </div>
    <div class="menu">
        Menu
        <div class="menu-options">
            <a class="menu-option" href="frota.php">Frota</a>
            <a class="menu-option" href="quemsomos.html">Quem somos</a>
            <a class="menu-option" href="reservas.html">Reservas</a>
            <a class="menu-option" href="reviews.html">Reviews</a>
        </div>
    </div>
    <div class="login">
        <a href="login.html" title="login">
            <p>login</p>
        </a>
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

    <!-- Form to add a new car -->
    <form method="POST" action="php/add_car.php">
        <label for="matricula">Matricula:</label><br>
        <input type="text" id="matricula" name="matricula" required><br>
        <label for="marca">Marca:</label><br>
        <input type="text" id="marca" name="marca" required><br>
        <label for="modelo">Modelo:</label><br>
        <input type="text" id="modelo" name="modelo" required><br>
        <label for="ano">Ano:</label><br>
        <input type="date" id="ano" name="ano" required><br>
        <label for="cor">Cor:</label><br>
        <input type="text" id="cor" name="cor" required><br>
        <label for="kms">Kms:</label><br>
        <input type="number" id="kms" name="kms" required><br>
        <label for="n_de_reservas">N_de_reservas:</label><br>
        <input type="number" id="n_de_reservas" name="n_de_reservas" required><br>
        <button type="submit">Add Car</button>
    </form>

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

    <table border="1">
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
</body>
</html>
