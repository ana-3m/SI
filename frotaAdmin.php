<?php
session_start();

$dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

//$email = $_SESSION['pessoa']['email'];
//
//// Query to check if the user is a funcionario
//$result = pg_query_params($dbconn, "SELECT n_fun FROM funcionario WHERE pessoa_email = $1", array($email));
//$isFuncionario = pg_num_rows($result) > 0;

// Handle toggle visibility
if (isset($_POST['toggle_visibility'])) {
    $matricula = $_POST['matricula'];
    $current_visibility = $_POST['current_visibility'];
    $new_visibility = $current_visibility == 't' ? 'f' : 't'; // Toggle between true and false

    pg_query_params(
        $dbconn,
        "UPDATE carro SET visivel = $1 WHERE matricula = $2",
        array($new_visibility, $matricula)
    );
    echo "<script>alert('Car visibility updated.'); window.location.href = 'frotaAdmin.php';</script>";
}

// Handle delete car
if (isset($_POST['delete_car'])) {
    $matricula = $_POST['matricula'];

    pg_query_params(
        $dbconn,
        "DELETE FROM carro WHERE matricula = $1",
        array($matricula)
    );
    echo "<script>alert('Car deleted successfully.'); window.location.href = 'frotaAdmin.php';</script>";
}

// Fetch all cars from the database
$result = pg_query($dbconn, "SELECT * FROM carro ORDER BY matricula");
$cars = pg_fetch_all($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
</head>
<body>
<h1>Admin Panel</h1>

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

<table border="1">
    <thead>
    <tr>
        <th>Matrícula</th>
        <th>Model</th>
        <th>Visible</th>
        <th>Actions</th>
    </tr>
    </thead>
    <tbody>
    <?php if ($cars): ?>
        <?php foreach ($cars as $car): ?>
            <tr>
                <td><?php echo htmlspecialchars($car['matricula']); ?></td>
                <td><?php echo htmlspecialchars($car['modelo']); ?></td>
                <td><?php echo $car['visivel'] === 't' ? 'Yes' : 'No'; ?></td>
                <td>
                    <!-- Toggle visibility form -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="matricula" value="<?php echo $car['matricula']; ?>">
                        <input type="hidden" name="current_visibility" value="<?php echo $car['visivel']; ?>">
                        <button type="submit" name="toggle_visibility">
                            <?php echo $car['visivel'] === 't' ? 'Hide' : 'Show'; ?>
                        </button>
                    </form>
                    <!-- Delete car form -->
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="matricula" value="<?php echo $car['matricula']; ?>">
                        <button type="submit" name="delete_car">Delete</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="4">No cars found.</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>
