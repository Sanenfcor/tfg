<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

/* ==========================================
   1. Validar login
========================================== */
if (empty($_SESSION['usuario'])) {
    header("Location: /KaiPets/php/login.php");
    exit;
}

require_once __DIR__ . '/../php/conn_db.php';

$id_usuario  = $_SESSION['usuario']['id'];
$id_cuidador = isset($_GET['cuidador']) ? intval($_GET['cuidador']) : 0;
$id_servicio = isset($_GET['servicio']) ? intval($_GET['servicio']) : 0;

if ($id_cuidador <= 0) {
    die("<h2>Error: cuidador inválido.</h2>");
}

/* ==========================================
   2. Obtener datos del cuidador
========================================== */
$stmt = $conn->prepare("
    SELECT u.nombre, u.apellido, c.foto_perfil, c.barrio, c.usuario_id
    FROM cuidadores c
    JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.usuario_id = :id
");
$stmt->execute([':id' => $id_cuidador]);
$cuidador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cuidador) {
    die("<h2>El cuidador no existe.</h2>");
}

/* ==========================================
   3. Obtener servicios del cuidador
========================================== */
$stmt = $conn->prepare("
    SELECT s.id_servicio, s.nombre, cs.precio 
    FROM cuidador_servicio cs
    JOIN servicios s ON s.id_servicio = cs.servicio_id
    WHERE cs.cuidador_id = :id
");
$stmt->execute([':id' => $id_cuidador]);
$lista_servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* Mostrar selector si no se eligió servicio */
$mostrar_selector = ($id_servicio === 0);

/* Obtener datos del servicio si ya está seleccionado */
if (!$mostrar_selector) {
    $stmt = $conn->prepare("
        SELECT nombre, descripcion 
        FROM servicios 
        WHERE id_servicio = :id
    ");
    $stmt->execute([':id' => $id_servicio]);
    $servicio = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$servicio) {
        die("<h2>Servicio no válido.</h2>");
    }
}

/* Obtener mascotas */
$stmt = $conn->prepare("SELECT id, nombre, especie FROM mascotas WHERE usuario_id = :id");
$stmt->execute([':id' => $id_usuario]);
$mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar servicio</title>

    <!-- Bootstrap / Estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
    <link rel="stylesheet" href="/KaiPets/src/css/reserva.css">
</head>

<body>

<?php include "../componentes/header.php"; ?>

<div class="container my-5">

    <h2 class="reserva-title">Reservar cuidador</h2>

    <!-- INFO DEL CUIDADOR -->
    <!-- INFO DEL CUIDADOR -->
<div class="cuidador-info-card card p-3 mb-4">
    <div class="d-flex align-items-center">

        <img src="<?= htmlspecialchars($cuidador['foto_perfil']) ?>" 
             class="cuidador-info-img">

        <div class="ms-3">
            <h4 class="cuidador-info-nombre mb-1">
                <?= htmlspecialchars($cuidador['nombre'] . " " . $cuidador['apellido']) ?>
            </h4>

            <p class="cuidador-info-barrio mb-0">
                <?= htmlspecialchars($cuidador['barrio']) ?>
            </p>
        </div>

    </div>
</div>


    <!-- ================================
         4. SELECCIÓN DE SERVICIO
    ================================= -->
    <?php if ($mostrar_selector): ?>
        <div class="card p-4 servicio-selector-card">
            <h4 class="mb-3">Selecciona un servicio</h4>

            <?php foreach ($lista_servicios as $srv): ?>
                <a href="/KaiPets/pags/reserva.php?cuidador=<?= $id_cuidador ?>&servicio=<?= $srv['id_servicio'] ?>"
                   class="btn btn-kai-service w-100 mb-2">
                    <?= htmlspecialchars($srv['nombre']) ?> — <?= $srv['precio'] ?>€
                </a>
            <?php endforeach; ?>
        </div>

        <?php include "../componentes/footer.php"; ?>
        </body>
        </html>
        <?php exit; ?>
    <?php endif; ?>

    <!-- ================================
         5. FORMULARIO DE RESERVA
    ================================= -->
    
    <h4 class="servicio-title">Servicio: <?= htmlspecialchars($servicio['nombre']) ?></h4>

    <form action="/KaiPets/php/reserva_guardar.php" method="POST" class="card p-4 reserva-form-card">

        <input type="hidden" name="cuidador" value="<?= $id_cuidador ?>">
        <input type="hidden" name="servicio" value="<?= $id_servicio ?>">

        <!-- Mascota -->
        <div class="mb-3">
            <label class="form-label">Mascota</label>
            <select name="id_mascota" class="form-select" required>
                <option value="">Selecciona una mascota</option>
                <?php foreach ($mascotas as $m): ?>
                    <option value="<?= $m['id'] ?>">
                        <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['especie']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tipo de reserva -->
        <div class="mb-3">
            <label class="form-label">Tipo de reserva</label>
            <select name="tipo" id="tipo_reserva" class="form-select" onchange="toggleTipo()" required>
                <option value="dias">Por días</option>
                <option value="horas">Por horas</option>
            </select>
        </div>

        <!-- Por Días -->
        <div id="reserva_dias">
            <label class="form-label">Fecha inicio</label>
            <input type="date" name="fecha_inicio" class="form-control mb-2">
            <label class="form-label">Fecha fin</label>
            <input type="date" name="fecha_fin" class="form-control">
        </div>

        <!-- Por Horas -->
        <div id="reserva_horas" style="display:none;">
            <label class="form-label">Fecha</label>
            <input type="date" name="fecha_unica" class="form-control mb-2">
            <label class="form-label">Horas</label>
            <input type="number" min="1" max="24" name="horas" class="form-control">
        </div>

        <!-- Notas -->
        <div class="mb-3">
            <label class="form-label">Notas (opcional)</label>
            <textarea name="notas" class="form-control"></textarea>
        </div>

        <!-- Botón -->
        <button class="btn btn-kai-green w-100 reservar-btn">Confirmar reserva</button>
    </form>

</div>

<script>
function toggleTipo() {
    const tipo = document.getElementById("tipo_reserva").value;
    document.getElementById("reserva_dias").style.display  = tipo === "dias"  ? "block" : "none";
    document.getElementById("reserva_horas").style.display = tipo === "horas" ? "block" : "none";
}
</script>

<?php include "../componentes/footer.php"; ?>
</body>
</html>
