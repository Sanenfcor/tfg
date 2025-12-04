<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

require_once "./conn_db.php";

$errors = [];
$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');

if (!isset($_SESSION['ultima_pagina']) && isset($_SERVER['HTTP_REFERER'])) {
  $referer = $_SERVER['HTTP_REFERER'];
  if (strpos($referer, 'login.php') === false && strpos($referer, 'registrer.php') === false) {
    $_SESSION['ultima_pagina'] = $referer;
  }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if ($email === '') {
    $errors[] = "El email es obligatorio.";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "El formato del email no es válido.";
  }

  if ($password === '') {
    $errors[] = "La contraseña es obligatoria.";
  } elseif (strlen($password) < 8) {
    $errors[] = "La contraseña debe tener al menos 8 caracteres.";
  }

  if (empty($errors)) {
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($password, $usuario['password'])) {

      $_SESSION['usuario'] = $usuario;

      if (!empty($_SESSION['ultima_pagina'])) {
        $destino = $_SESSION['ultima_pagina'];
        unset($_SESSION['ultima_pagina']);
        header("Location: $destino");
        exit;
      }

      header("Location: /KaiPets/index.php");
      exit;
    } else {
      $errors[] = "Email o contraseña incorrectos.";
    }
  }
}
?>

<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KAI PETS · Iniciar sesión</title>
  <link rel="stylesheet" href="../src/css/login-registro.css">
</head>
<?php if (isset($_SESSION['usuario'])): ?>
  <script>
    window.opener.location.reload();
    window.close();
  </script>
<?php endif; ?>

<body class="auth-page">

  <div class="auth-box">
    <h2 class="title">KAI PETS</h2>
    <p class="muted">Introduce tus credenciales para continuar.</p>

    <?php if (!empty($errors)): ?>
      <div class="message">
        <ul>
          <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="/KaiPets/php/login.php" novalidate>
      <label for="email">Email</label>
      <input id="email" name="email" type="email"
        value="<?= htmlspecialchars($email) ?>"
        placeholder="tucorreo@ejemplo.com"
        autocomplete="username" required>

      <label for="password">Contraseña</label>
      <input id="password" name="password" type="password"
        placeholder="••••••••"
        minlength="8"
        autocomplete="current-password" required>

      <button type="submit">Entrar</button>

      <p>¿No tienes cuenta? <a href="registro.php">Crear cuenta nueva</a></p>
    </form>
  </div>

</body>

</html>