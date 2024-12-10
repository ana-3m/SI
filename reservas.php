<?php
session_start();

// Verificar se o usuário está logado
$userLoggedIn = isset($_SESSION['pessoa']);
if (!$userLoggedIn) {
    // Armazena mensagem de alerta na sessão
    $_SESSION['redirect_message'] = "Please log in first.";
    // Redirecionar para a página de login
    header("Location: login.php");
    exit();
}

// Dados do usuário logado
$nome = $_SESSION['pessoa']['nome'];
$email = $_SESSION['pessoa']['email'];

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
    <link href="css/main.css" rel="stylesheet" type="text/css"/>
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
        <a href="profile.php" title="Profile">
            <p><?php echo htmlspecialchars($nome); ?></p>
        </a>
    </div>
</header>
<main>
    <div class="container">
        <h1>Reserva de Carros</h1>
        <form action="php/processa_reserva.php" method="post">
            <!-- Campos preenchidos automaticamente -->
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($nome); ?>" readonly>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" readonly>

            <label for="telefone">Telefone:</label>
            <input type="tel" id="telefone" name="telefone" placeholder="+351 912345678" required>

            <label for="carro">Selecione o Carro:</label>
            <select id="carro" name="carro" required>
                <!-- PHP para carregar os carros disponíveis -->
                <?php
                // Conexão com o banco de dados
                $conn = pg_connect("host=localhost dbname=postgres user=postgres password=postgres");

                if (!$conn) {
                    die("<option>Erro ao conectar ao banco de dados: " . pg_last_error() . "</option>");
                }

                // Buscar carros disponíveis
                $query = "SELECT matricula, marca, modelo FROM carro";
                $result = pg_query($conn, $query);

                if (!$result) {
                    echo "<option>Erro ao buscar carros: " . pg_last_error() . "</option>";
                } else {
                    $has_cars = false;
                    while ($carro = pg_fetch_assoc($result)) {
                        $has_cars = true;
                        echo "<option value='{$carro['matricula']}'>{$carro['marca']} {$carro['modelo']}</option>";
                    }

                    if (!$has_cars) {
                        echo "<option>Nenhum carro disponível no momento</option>";
                    }
                }

                // Fechar conexão
                pg_close($conn);
                ?>
            </select>

            <label for="data_ini">Data de Início:</label>
            <input type="date" id="data_ini" name="data_ini" required>

            <label for="data_fim">Data de Fim:</label>
            <input type="date" id="data_fim" name="data_fim" required>

            <button type="submit">Reservar</button>
        </form>
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
<script>
    document.querySelector('form').addEventListener('submit', function (e) {
        const dataInicio = new Date(document.getElementById('data_ini').value);
        const dataFim = new Date(document.getElementById('data_fim').value);

        if (dataInicio > dataFim) {
            e.preventDefault(); // Impede o envio do formulário
            alert('Erro: A data de início não pode ser posterior à data de término.');
        }
    });
</script>
</body>
</html>
