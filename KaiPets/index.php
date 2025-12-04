<?php
require_once "php/conn_db.php";
session_start();

// --- Ciudad seleccionada ---
$ciudad_id = isset($_GET['ciudad_id']) ? intval($_GET['ciudad_id']) : 1; // Málaga por defecto

// --- Obtener ciudades ---
$ciudades_raw = $conn->query("SELECT * FROM ciudades")->fetchAll(PDO::FETCH_ASSOC);

$ciudades = [];
foreach ($ciudades_raw as $c) {
    $ciudades[$c['id']] = $c;
}

// --- Obtener cuidadores de esa ciudad (los últimos 10) ---
$stmt = $conn->prepare("
    SELECT c.*, u.nombre, u.apellido, u.email 
    FROM cuidadores c
    JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.ciudad_id = ?
    ORDER BY c.id DESC
    LIMIT 10
");
$stmt->execute([$ciudad_id]);
$cuidadores = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener opiniones recientes
$opiniones = $conn->query("
    SELECT o.*, u.nombre AS user_nombre, u.apellido AS user_apellido
    FROM opiniones o
    JOIN usuarios u ON u.id = o.usuario_id
    ORDER BY o.id DESC
    LIMIT 3
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>KaiPets - Tu cuidador de confianza</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="/KaiPets/src/css/style.css">
<style>
.hero {
    background: url('/KaiPets/src/img/hero_pets.jpg') center/cover no-repeat;
    height: 55vh;
    display: flex;
    align-items: center;
    color: white;
    text-shadow: 0 0 10px black;
}
.ciudad-card {
    transition: .2s;
    cursor: pointer;
}
.ciudad-card:hover {
    transform: scale(1.05);
}
.cuidador-card img {
    height: 220px;
    object-fit: cover;
}
.valoracion {
    color: gold;
    font-size: 1.2rem;
}
</style>
</head>

<body>

<?php include "componentes/header.php"; ?>

<!-- ============================= -->
<!-- HERO (BANNER PRINCIPAL) -->
<!-- ============================= -->
<div class="hero mb-5">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Encuentra el cuidador perfecto para tu mascota</h1>
        <p class="lead">Profesionales verificados, cerca de ti</p>
        <a href="/KaiPets/pags/buscador.php" class="btn btn-lg btn-primary">Buscar Cuidadores</a>
    </div>
</div>

<div class="container">

    <!-- ============================= -->
    <!-- SECCIÓN: Selección de Ciudad -->
    <!-- ============================= -->
    <h2 class="mb-4 text-center">Selecciona tu ciudad</h2>

    <div class="row g-4 mb-5">
        <?php foreach ($ciudades as $c): ?>
            <div class="col-md-4">
                <a href="index.php?ciudad_id=<?= $c['id'] ?>" class="text-decoration-none text-dark">
                    <div class="card ciudad-card shadow">
                        <div class="card-body text-center">
                            <h4><?= htmlspecialchars($c['nombre']) ?></h4>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>


    <!-- ============================= -->
    <!-- CARRUSEL DE CUIDADORES -->
    <!-- ============================= -->

    <h2 class="mb-4">
        Cuidadores en <?= htmlspecialchars($ciudades[$ciudad_id]['nombre'] ?? "tu ciudad") ?>
    </h2>


    <?php if (empty($cuidadores)): ?>
        <div class="alert alert-warning">No hay cuidadores registrados en esta ciudad todavía.</div>
    <?php else: ?>

    <div id="cuidadoresCarousel" class="carousel slide mb-5" data-bs-ride="carousel">
        <div class="carousel-inner">

            <?php foreach ($cuidadores as $index => $c): ?>
            <div class="carousel-item <?= $index === 0 ? 'active' : '' ?>">
                <div class="card cuidador-card mx-auto" style="max-width: 700px;">
                    <img src="<?= $c['foto_perfil'] ?>" class="card-img-top" alt="Foto cuidador">
                    <div class="card-body">
                        <h4><?= htmlspecialchars($c['nombre'] . " " . $c['apellido']) ?></h4>
                        <p><?= htmlspecialchars($c['descripcion']) ?></p>
                        <a href="/KaiPets/pags/ver_cuidador.php?id=<?= $c['id'] ?>" class="btn btn-primary">Ver perfil</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>

        </div>

        <button class="carousel-control-prev" type="button" data-bs-target="#cuidadoresCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>

        <button class="carousel-control-next" type="button" data-bs-target="#cuidadoresCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>

    </div>

    <?php endif; ?>


    <!-- ============================= -->
    <!-- SERVICIOS DESTACADOS -->
    <!-- ============================= -->
    <h2 class="mb-4 text-center">Servicios más contratados</h2>

    <div class="row text-center mb-5">
        <div class="col-md-3">
            <div class="card shadow p-3">
                <h4>🐶 Paseo</h4>
                <p>Desde 10€/h</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow p-3">
                <h4>🏠 Cuidado a domicilio</h4>
                <p>Desde 20€/día</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow p-3">
                <h4>🛁 Baño</h4>
                <p>Desde 15€</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow p-3">
                <h4>✂ Corte</h4>
                <p>Desde 12€</p>
            </div>
        </div>
    </div>


    <!-- ============================= -->
    <!-- OPINIONES RECIENTES -->
    <!-- ============================= -->
    <h2 class="text-center mb-4">Opiniones de nuestros usuarios</h2>

    <div class="row g-4 mb-5">
        <?php foreach ($opiniones as $op): ?>
            <div class="col-md-4">
                <div class="card shadow p-3">
                    <h5><?= htmlspecialchars($op['user_nombre'] . " " . $op['user_apellido']) ?></h5>
                    <div class="valoracion">
                        <?= str_repeat("⭐", $op['puntuacion']) ?>
                    </div>
                    <p><?= htmlspecialchars($op['comentario']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php include "componentes/footer.php"; ?>

</body>
</html>
