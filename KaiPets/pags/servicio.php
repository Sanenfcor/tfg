<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../php/conn_db.php';

$id_servicio = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_servicio <= 0) {
    header("Location: /KaiPets/index.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM servicios WHERE id_servicio = :id");
$stmt->execute([':id' => $id_servicio]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    echo "<h2>Servicio no encontrado.</h2>";
    exit;
}

$sql = "
    SELECT c.*, u.nombre, u.apellido, cs.precio AS precio_cuidador, ci.nombre AS ciudad
    FROM cuidadores c
    JOIN usuarios u ON u.id = c.usuario_id
    JOIN cuidador_servicio cs ON cs.cuidador_id = c.id
    JOIN ciudades ci ON ci.id = c.ciudad_id
    WHERE cs.servicio_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->execute([$id_servicio]);
$cuidadores = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($servicio['nombre']) ?> · Kai Pets</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
    <link rel="stylesheet" href="/KaiPets/src/css/servicio.css">
</head>

<body>

<?php include "../componentes/header.php"; ?>

<div class="container mb-5">

    <!-- HERO -->
    <div class="hero-servicio mb-4"
         style="background-image: url('/KaiPets/src/img/servicios/<?= $id_servicio ?>.jpg');">
        <div class="hero-overlay">
            <h1><?= htmlspecialchars($servicio['nombre']) ?></h1>
        </div>
    </div>

    <!-- DESCRIPCIÓN -->
    <div class="servicio-desc">
        <p><?= nl2br(htmlspecialchars($servicio['descripcion'])) ?></p>
    </div>

    <!-- INFO DEL SERVICIO -->
    <div class="info-card">
        <h3 class="mb-3">Información del servicio</h3>

        <p><strong>Precio base:</strong> <?= number_format($servicio['precio'], 2) ?> €</p>
        <p><strong>Disponible en todas las ciudades con cuidadores activos.</strong></p>

        <a href="/KaiPets/index.php" class="btn btn-secondary mt-3">Volver</a>
    </div>

    <!-- CUIDADORES DISPONIBLES -->
    <h2 class="text-center mt-5 mb-4">Cuidadores que ofrecen este servicio</h2>

    <?php if (empty($cuidadores)): ?>
        <p class="text-center text-muted">No hay cuidadores que ofrezcan este servicio actualmente.</p>
    <?php else: ?>

        <div class="row g-4">
            <?php foreach ($cuidadores as $c): ?>
                <div class="col-md-4">
                    <div class="cuidador-card">
                        <img src="<?= htmlspecialchars($c['foto_perfil']) ?>" class="cuidador-img">

                        <div class="p-3">
                            <h4><?= htmlspecialchars($c['nombre'] . " " . $c['apellido']) ?></h4>
                            <p class="text-muted">
                                <?= htmlspecialchars($c['barrio']) ?>, <?= htmlspecialchars($c['ciudad']) ?>
                            </p>

                            <p><strong>Precio desde:</strong> <?= number_format($c['precio_cuidador'], 2) ?> €</p>

                            <a href="/KaiPets/pags/ver_cuidador.php?id=<?= $c['usuario_id'] ?>"
                               class="btn btn-kai w-100 mt-2">
                                Ver perfil
                            </a>

                            <a href="/KaiPets/pags/reserva.php?cuidador=<?= $c['usuario_id'] ?>&servicio=<?= $id_servicio ?>"
                               class="btn btn-primary w-100 mt-2" style="border-radius: 50px;">
                                Reservar ahora
                            </a>

                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<?php include "../componentes/footer.php"; ?>

</body>
</html>
