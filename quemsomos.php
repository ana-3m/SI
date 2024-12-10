<?php
session_start();

// Verifica se o usuário está logado
$userLoggedIn = isset($_SESSION['pessoa']);
$loginPlaceholder = $userLoggedIn ? $_SESSION['pessoa']['nome'] : 'login';

// Verifica se o usuário é funcionário
$isFuncionario = false; // Valor padrão

if ($userLoggedIn) {
    $userEmail = $_SESSION['pessoa']['email']; // Certifique-se de que o e-mail está armazenado na sessão
    $dbconn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

    if ($dbconn) {
        // Executa a consulta para verificar se é funcionário
        $result = pg_query_params($dbconn, "SELECT n_fun FROM funcionario WHERE pessoa_email = $1", array($userEmail));
        $isFuncionario = pg_num_rows($result) > 0;
    } else {
        echo "Erro ao conectar ao banco de dados.";
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
    <link href="css/quemsomos.css" rel="stylesheet" type="text/css"/>
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
            <?php if ($isFuncionario): ?>
                <a class="menu-option" href="estatisticas.php">Estatísticas</a>
            <?php endif; ?>
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
    <h1>Quem Somos</h1>
    <div class="content">
        <p>
            Bem-vindo à Fast & Furious Cars Inc! Somos uma empresa de aluguer de carros dedicada
            a oferecer a melhor experiência de mobilidade para nossos clientes. Desde veículos
            compactos a SUVs luxuosos, nossa frota foi cuidadosamente selecionada para atender
            todas as suas necessidades.
        </p>
        <p>
            Fundada com a paixão por carros e um compromisso com a excelência, a nossa missão
            é tornar sua jornada segura, confortável e inesquecível. Estamos empenhados em
            oferecer um serviço de alta qualidade, com preços acessíveis e total transparência.
        </p>
        <p>
            A equipe da Fast & Furious Cars Inc está aqui para ajudar você a encontrar o carro
            perfeito para cada ocasião. Confie em nós para tornar cada viagem inesquecível.
        </p>
    </div>
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