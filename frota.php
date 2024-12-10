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
    <link href="css/frota.css" rel="stylesheet" type="text/css"/>
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

    <!-- Exibe mensagens de sucesso ou erro -->
    <?php if (!empty($_GET['status'])): ?>
        <?php if ($_GET['status'] === 'success'): ?>
            <p style="color: green;">Car added successfully!</p>
        <?php elseif ($_GET['status'] === 'error'): ?>
            <p style="color: red;">Error: <?= htmlspecialchars($_GET['message'] ?? 'Unknown error') ?></p>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Formulário de pesquisa único -->
    <form method="GET" action="">
        <label for="searchTerm">Pesquisar:</label>
        <input type="text" id="searchTerm" name="searchTerm" placeholder="Por exemplo: Opel">
        <button type="submit"><img id="search" src="data/imagens/search.png" alt="search"></button><br>
        <button type="submit" name="clear" value="1">Limpar Filtros</button>
    </form>


    <!-- Exibe a lista de carros -->
    <h2>Available Cars:</h2>

    <?php
    // Conecta ao banco de dados
    $connection = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

    // Verifica se há um termo de pesquisa
    $searchTerm = $_GET['searchTerm'] ?? '';
    $clear = $_GET['clear'] ?? '';

    // Parâmetro de ordenação
    $sort = $_GET['sort'] ?? ''; // Critério de ordenação
    $allowedSortFields = ['matricula', 'marca', 'modelo', 'ano', 'cor', 'kms', 'n_de_reservas']; // Campos válidos

    // Construção da consulta SQL
    $query = "SELECT * FROM carro WHERE visivel = TRUE";
    $params = [];
    if ($clear) {
        // Se o botão "Limpar" for acionado, nenhuma condição será aplicada
    } elseif ($searchTerm) {
        // Adiciona filtro de pesquisa
        $query .= " WHERE marca ILIKE $1 OR modelo ILIKE $1 OR cor ILIKE $1";
        $params[] = '%' . $searchTerm . '%';
    }

    // Adiciona a cláusula ORDER BY se houver um parâmetro de ordenação válido
    if (in_array($sort, $allowedSortFields)) {
        $query .= " ORDER BY $sort";
    }

    // Executa a consulta com ou sem parâmetros
    if (!empty($params)) {
        $result = pg_query_params($connection, $query, $params);
    } else {
        $result = pg_query($connection, $query);
    }

    // Se a consulta falhar
    if (!$result) {
        echo "Erro ao buscar dados.";
        exit;
    }
    ?>


    <table border="1px">
        <tr>
            <th><a href="?sort=matricula&searchTerm=<?= urlencode($searchTerm) ?>">Matricula</a></th>
            <th><a href="?sort=marca&searchTerm=<?= urlencode($searchTerm) ?>">Marca</a></th>
            <th><a href="?sort=modelo&searchTerm=<?= urlencode($searchTerm) ?>">Modelo</a></th>
            <th><a href="?sort=ano&searchTerm=<?= urlencode($searchTerm) ?>">Ano</a></th>
            <th><a href="?sort=cor&searchTerm=<?= urlencode($searchTerm) ?>">Cor</a></th>
            <th><a href="?sort=kms&searchTerm=<?= urlencode($searchTerm) ?>">Kms</a></th>
            <th><a href="?sort=n_de_reservas&searchTerm=<?= urlencode($searchTerm) ?>">N. de Reservas</a></th>
        </tr>

        <?php
        // Exibe os resultados da consulta na tabela
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

        // Libera a memória do resultado e fecha a conexão
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
                <img src="data/imagens/logo.png" alt="logo" height="200px" width="200px">
            </div>
        </div>
    </div>
</footer>
</body>
</html>
