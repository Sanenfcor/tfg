<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();

// ===============================
// 1️⃣ Validar login
// ===============================
if (empty($_SESSION['usuario'])) {
    header("Location: /KaiPets/login.php");
    exit;
}

require_once __DIR__ . '/../php/conn_db.php';

$id_usuario  = $_SESSION['usuario']['id'];
$id_cuidador = isset($_GET['cuidador']) ? intval($_GET['cuidador']) : 0;
$id_servicio = isset($_GET['servicio']) ? intval($_GET['servicio']) : 0;

if ($id_cuidador <= 0 || $id_servicio <= 0) {
    die("<h2>Error: Datos inválidos.</h2>");
}

// ===============================
// 2️⃣ Obtener datos del cuidador
// ===============================
$stmt = $conn->prepare("
    SELECT u.nombre, u.apellido, c.foto_perfil, c.barrio
    FROM cuidadores c
    JOIN usuarios u ON u.id = c.usuario_id
    WHERE c.usuario_id = :id
");
$stmt->execute([':id' => $id_cuidador]);
$cuidador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cuidador) {
    die("<h2>El cuidador no existe.</h2>");
}

// ===============================
// 3️⃣ Obtener datos del servicio
// ===============================
$stmt = $conn->prepare("
    SELECT nombre, descripcion, precio
    FROM servicios
    WHERE id_servicio = :id
");
$stmt->execute([':id' => $id_servicio]);
$servicio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    die("<h2>El servicio no existe.</h2>");
}

// ===============================
// 4️⃣ Obtener mascotas del usuario
// ===============================
$stmt = $conn->prepare("
    SELECT id, nombre, especie, raza
    FROM mascotas
    WHERE usuario_id = :id
");
$stmt->execute([':id' => $id_usuario]);
$mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reservar servicio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
</head>

<body>
<?php include "../componentes/header.php"; ?>

<div class="container my-5">

    <h2 class="mb-4">Reservar: <?= htmlspecialchars($servicio['nombre']) ?></h2>

    <!-- INFO DEL CUIDADOR -->
    <div class="card mb-4 p-3">
        <h4>Cuidador seleccionado</h4>
        <div class="d-flex align-items-center">
            <img src="<?= htmlspecialchars($cuidador['foto_perfil']) ?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;margin-right:15px;">
            <div>
                <strong><?= htmlspecialchars($cuidador['nombre'] . " " . $cuidador['apellido']) ?></strong><br>
                <small><?= htmlspecialchars($cuidador['barrio']) ?></small>
            </div>
        </div>
    </div>

    <!-- FORMULARIO DE RESERVA -->
    <form action="/KaiPets/php/reserva_guardar.php" method="POST" class="card p-4">

        <input type="hidden" name="cuidador" value="<?= $id_cuidador ?>">
        <input type="hidden" name="servicio" value="<?= $id_servicio ?>">

        <!-- MASCOTA -->
        <div class="mb-3">
            <label class="form-label">Selecciona tu mascota</label>

            <?php if (empty($mascotas)): ?>
                <p class="text-danger">
                    No tienes mascotas registradas.  
                    <a href="/KaiPets/pags/anadir_mascota.php" class="btn btn-sm btn-success">Añadir mascota</a>
                </p>
            <?php else: ?>
                <select class="form-select" name="id_mascota" required>
                    <option value="">Selecciona una mascota</option>
                    <?php foreach ($mascotas as $m): ?>
                        <option value="<?= $m['id'] ?>">
                            <?= htmlspecialchars($m['nombre']) ?> (<?= htmlspecialchars($m['especie']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php endif; ?>
        </div>

        <!-- TIPO DE RESERVA -->
        <div class="mb-3">
            <label class="form-label">Tipo de reserva</label>
            <select name="tipo" id="tipo_reserva" class="form-select" onchange="toggleTipo()" required>
                <option value="dias">Por días</option>
                <option value="horas">Por horas</option>
            </select>
        </div>

        <!-- RESERVA POR DIAS -->
        <div id="reserva_dias">
            <div class="mb-3">
                <label class="form-label">Fecha inicio</label>
                <input type="date" name="fecha_inicio" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Fecha fin</label>
                <input type="date" name="fecha_fin" class="form-control">
            </div>
        </div>

        <!-- RESERVA POR HORAS -->
        <div id="reserva_horas" style="display:none;">
            <div class="mb-3">
                <label class="form-label">Fecha</label>
                <input type="date" name="fecha_unica" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Horas</label>
                <input type="number" min="1" max="24" name="horas" class="form-control">
            </div>
        </div>

        <!-- NOTAS -->
        <div class="mb-3">
            <label class="form-label">Notas adicionales (opcional)</label>
            <textarea class="form-control" name="notas" rows="3"></textarea>
        </div>

        <button class="btn btn-primary w-100" style="border-radius: 50px; font-size: 18px; padding: 12px;">
            Confirmar reserva
        </button>

    </form>
</div>

<script>
function toggleTipo() {
    const tipo = document.getElementById("tipo_reserva").value;
    document.getElementById("reserva_dias").style.display = tipo === "dias" ? "block" : "none";
    document.getElementById("reserva_horas").style.display = tipo === "horas" ? "block" : "none";
}
</script>

<?php include "../componentes/footer.php"; ?>
</body>
</html>
