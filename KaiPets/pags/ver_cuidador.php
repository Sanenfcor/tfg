<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../php/conn_db.php';

// Verificar parámetro GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("ID de cuidador no válido");
}

$id = intval($_GET['id']);
$usuario_actual = $_SESSION['usuario']['id'] ?? null;

// Obtener datos del cuidador
$sql = "
    SELECT c.*, u.nombre, u.apellido, u.email, u.telefono, u.direccion 
    FROM cuidadores c
    JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.id = :id
";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$cuidador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cuidador) {
    die("El cuidador no existe.");
}

// Obtener servicios ofrecidos
$sql_servicios = "
    SELECT s.nombre, cs.precio
    FROM cuidador_servicio cs
    JOIN servicios s ON s.id_servicio = cs.servicio_id
    WHERE cs.cuidador_id = :id
";
$stmt = $conn->prepare($sql_servicios);
$stmt->execute([':id' => $id]);
$servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener valoraciones
$sql_reviews = "
    SELECT r.*, u.nombre AS usuario_nombre
    FROM opiniones r
    JOIN usuarios u ON u.id = r.usuario_id
    WHERE r.cuidador_id = :id
    ORDER BY r.fecha DESC
";
$stmt = $conn->prepare($sql_reviews);
$stmt->execute([':id' => $id]);
$reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular media
$media = 0;
if ($reviews) {
    $total = array_sum(array_column($reviews, 'puntuacion'));
    $media = round($total / count($reviews), 1);
}

// Buscar coordenadas del barrio en la tabla
$sql_coords = "SELECT lat, lon FROM barrios WHERE nombre = :b LIMIT 1";
$stmt = $conn->prepare($sql_coords);
$stmt->execute([':b' => $cuidador['barrio']]);
$coords = $stmt->fetch(PDO::FETCH_ASSOC);

if ($coords) {
    $lat = $coords['lat'];
    $lon = $coords['lon'];
} else {
    // Fallback si el barrio no existe
    $lat = 36.7213; // Málaga centro
    $lon = -4.4213;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Perfil del cuidador</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
    <link rel="stylesheet" href="/KaiPets/src/css/servicio.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
</head>

<body>

<?php include __DIR__ . '/../componentes/header.php'; ?>

<div class="container mt-4">

    <!-- TÍTULO -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Cuidador: <?= htmlspecialchars($cuidador['nombre'] . ' ' . $cuidador['apellido']) ?></h2>

        <button class="btn btn-reservar" onclick="abrirReserva(<?= $cuidador['usuario_id'] ?>)">
            Reservar cuidador
        </button>
    </div>

    <div class="row">
        <!-- COLUMNA IZQUIERDA -->
        <div class="col-md-4">

            <!-- FOTO -->
            <div class="cuidador-profile-block cuidador-perfil-card">
                <img src="<?= htmlspecialchars($cuidador['foto_perfil'] ?: 'pred_usu.jpg') ?>"
                     alt="Foto cuidador">
                <div class="mt-3">
                    <h5><?= htmlspecialchars($cuidador['nombre']) ?></h5>
                    <p><strong>Email:</strong> <?= htmlspecialchars($cuidador['email']) ?></p>
                    <p><strong>Teléfono:</strong> <?= htmlspecialchars($cuidador['telefono']) ?></p>
                </div>
            </div>

            <!-- CONTACTAR -->
            <a class="btn btn-whatsapp w-100 mb-3" href="https://wa.me/34<?= $cuidador['telefono'] ?>">
                Contactar por WhatsApp
            </a>
        </div>

        <!-- COLUMNA DERECHA -->
        <div class="col-md-8">

            <!-- INFO CUIDADOR -->
            <div class="cuidador-profile-block">
                <h4>Información del cuidador</h4>
                <p><strong>Barrio:</strong> <?= htmlspecialchars($cuidador['barrio']) ?></p>
                <p><strong>Experiencia:</strong> <?= htmlspecialchars($cuidador['experiencia'] ?: "No indicada") ?></p>
                <p><strong>Descripción:</strong><br><?= nl2br(htmlspecialchars($cuidador['descripcion'] ?: "Sin descripción")) ?></p>
            </div>

            <!-- MAPA -->
            <div class="cuidador-profile-block">
                <h4>Zona de trabajo</h4>
                <div id="map" style="height: 300px;"></div>
            </div>

            <!-- SERVICIOS -->
            <div class="cuidador-profile-block">
                <h4>Servicios que ofrece</h4>

                <?php if ($servicios): ?>
                    <ul class="list-group">
                        <?php foreach ($servicios as $s): ?>
                            <li class="list-group-item d-flex justify-content-between">
                                <strong><?= htmlspecialchars($s['nombre']) ?></strong>
                                <span><?= number_format($s['precio'], 2) ?> €</span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>No ofrece servicios todavía.</p>
                <?php endif; ?>
            </div>

            <!-- OPINIONES -->
            <div class="cuidador-profile-block">
                <h4>Opiniones (<?= $media ?> ⭐)</h4>

                <?php if ($reviews): ?>
                    <?php foreach ($reviews as $r): ?>
                        <div class="review-box">
                            <strong><?= htmlspecialchars($r['usuario_nombre']) ?></strong> — <?= $r['puntuacion'] ?> ⭐
                            <br>
                            <?= nl2br(htmlspecialchars($r['comentario'])) ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No tiene opiniones todavía.</p>
                <?php endif; ?>

                <?php if ($usuario_actual && $usuario_actual != $cuidador['usuario_id']): ?>
                    <form method="POST" action="/KaiPets/php/enviar_opinion.php" class="mt-3">
                        <input type="hidden" name="cuidador_id" value="<?= $cuidador['id'] ?>">

                        <label class="form-label">Puntuación:</label>
                        <select name="puntuacion" class="form-select mb-2" required>
                            <option value="5">5 ⭐</option>
                            <option value="4">4 ⭐</option>
                            <option value="3">3 ⭐</option>
                            <option value="2">2 ⭐</option>
                            <option value="1">1 ⭐</option>
                        </select>

                        <label class="form-label">Comentario:</label>
                        <textarea name="comentario" class="form-control mb-2" rows="3"></textarea>

                        <button class="btn btn-reservar w-100">
                            Enviar opinión
                        </button>
                    </form>
                <?php endif; ?>
            </div>

        </div>
    </div>
</div>

<?php include __DIR__ . '/../componentes/footer.php'; ?>

<script>
function abrirReserva(idCuidador) {
    window.location.href = "/KaiPets/pags/reserva.php?cuidador=" + idCuidador;
}
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const lat = <?= $lat ?>;
    const lon = <?= $lon ?>;

    const map = L.map('map').setView([lat, lon], 14);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([lat, lon])
        .addTo(map)
        .bindPopup("<?= htmlspecialchars($cuidador['barrio']) ?>")
        .openPopup();
});
</script>

</body>
</html>
