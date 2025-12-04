<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


// Iniciar sesión para detectar si hay usuario logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/php/conn_db.php';

$usuario = $_SESSION['usuario'] ?? null;

// Ciudad predeterminada: Málaga (id = 1 por ejemplo)
$ciudad_id = isset($_GET['ciudad_id']) ? (int)$_GET['ciudad_id'] : 1;

if ($ciudad_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM cuidadores WHERE ciudad_id = :id");
    $stmt->execute([':id' => $ciudad_id]);
} else {
    $stmt = $conn->query("SELECT * FROM cuidadores");
}

$cuidadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kai Pets</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
</head>

<body>

    <!-- HEADER -->
    <?php include __DIR__ . '/componentes/header.php'; ?>

    <div class="carousel-container">
        <?php if (empty($cuidadores)): ?>
            <p class="no-results">No hay cuidadores en esta ciudad.</p>
        <?php else: ?>

            <div class="carousel" id="cuidadorCarousel">
                <?php foreach ($cuidadores as $c): ?>
                    <div class="carousel-item">
                        <img src="<?= htmlspecialchars($c['foto_perfil']) ?>" alt="Foto Cuidador">
                        <h3><?= htmlspecialchars($c['nombre']) ?></h3>
                        <p><?= htmlspecialchars($c['descripcion']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>

            <button class="carousel-btn prev" onclick="moveCarousel(-1)">&#10094;</button>
            <button class="carousel-btn next" onclick="moveCarousel(1)">&#10095;</button>

        <?php endif; ?>
    </div>

    <script>
        let index = 0;

        function moveCarousel(dir) {
            const items = document.querySelectorAll('.carousel-item');
            if (items.length === 0) return;

            index = (index + dir + items.length) % items.length;
            const offset = -index * 300; // 300px = width item
            document.getElementById('cuidadorCarousel').style.transform = `translateX(${offset}px)`;
        }
    </script>

    <!-- FOOTER -->
    <?php include __DIR__ . '/componentes/footer.php'; ?>
</body>

</html>
