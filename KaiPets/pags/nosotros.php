<?php
//  Iniciar sesión para detectar si hay usuario logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//require_once __DIR__ . '../php/conn_db.php';

$usuario = $_SESSION['usuario'] ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kai Pets</title>
  <link rel="stylesheet" href="../src/css/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

  <!-- HEADER -->
  <?php include __DIR__ . '/../componentes/header.php'; ?>

  <!-- CONTENIDO PRINCIPAL -->
  <main>
      <h1>Nosotros</h1>
  </main>

  <!-- FOOTER -->
  <?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>
</html>
