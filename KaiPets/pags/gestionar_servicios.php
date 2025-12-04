<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../php/conn_db.php";

// 🔒 Solo cuidadores
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];

// Obtener el ID del cuidador con ese usuario
$stmt = $conn->prepare("SELECT id FROM cuidadores WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$cuidador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cuidador) {
    die("<h2>Solo los cuidadores pueden acceder a esta página.</h2>");
}

$cuidador_id = $cuidador['id'];

// ============================
// Cargar servicios
// ============================
$servicios = $conn->query("SELECT * FROM servicios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Servicios que ya ofrece
$stmt = $conn->prepare("SELECT servicio_id, precio FROM cuidador_servicio WHERE cuidador_id = ?");
$stmt->execute([$cuidador_id]);
$servicios_actuales = $stmt->fetchAll(PDO::FETCH_KEY_PAIR); 
// formato: [ servicio_id => precio ]
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis servicios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
    <link rel="stylesheet" href="/KaiPets/src/css/perfil.css">
</head>
<body>

<?php include "../componentes/header.php"; ?>

<div class="container mt-5">

    <h2 class="mb-4">Servicios que ofrezco</h2>

    <form action="../php/guardar_servicios.php" method="post" class="card p-4">

        <?php foreach ($servicios as $s): ?>
            <?php 
                $checked = isset($servicios_actuales[$s['id_servicio']]);
                $precio = $checked ? $servicios_actuales[$s['id_servicio']] : "";
            ?>

            <div class="service-item mb-3 p-3 border rounded">
                <div class="d-flex align-items-center justify-content-between">

                    <div>
                        <input type="checkbox" 
                               name="servicios[<?= $s['id_servicio'] ?>][activar]" 
                               <?= $checked ? 'checked' : '' ?>>
                        <strong><?= htmlspecialchars($s['nombre']) ?></strong>
                        <p class="text-muted"><?= htmlspecialchars($s['descripcion']) ?></p>
                    </div>

                    <div>
                        <label>Precio (€)</label>
                        <input type="number" 
                               step="0.01" 
                               min="0" 
                               name="servicios[<?= $s['id_servicio'] ?>][precio]"
                               value="<?= htmlspecialchars($precio) ?>"
                               class="form-control"
                               style="width: 120px;">
                    </div>

                </div>
            </div>

        <?php endforeach; ?>

        <button class="btn btn-primary w-100 mt-4">Guardar cambios</button>
    </form>

</div>

<?php include "../componentes/footer.php"; ?>

</body>
</html>
