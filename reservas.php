<?php
session_start();

// Check if a user is logged in
$userLoggedIn = isset($_SESSION['pessoa']);
$loginPlaceholder = $userLoggedIn ? $_SESSION['pessoa']['nome'] : 'login';
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width">
  <title>rent-a-car</title>
  <link href="css/style.css" rel="stylesheet" type="text/css" />
</head>

<body>
  <script src="js/script.js"></script>

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
</body>

</html>