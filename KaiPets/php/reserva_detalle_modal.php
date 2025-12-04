<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../php/conn_db.php';

if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
    echo "<p>Error: ID de reserva inválido.</p>";
    exit;
}

$id = intval($_GET['id']);

$sql = "
    SELECT r.*, 
           u.nombre AS cliente_nombre,
           c.nombre AS cuidador_nombre,
           s.nombre AS servicio_nombre,
           m.nombre AS mascota_nombre
    FROM reservas r
    JOIN usuarios u ON u.id = r.id_usuario
    JOIN usuarios c ON c.id = r.id_cuidador
    JOIN servicios s ON s.id_servicio = r.id_servicio
    LEFT JOIN mascotas m ON m.id = r.id_mascota
    WHERE r.id_reserva = :id
";

$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id]);
$r = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$r) {
    echo "<p>No se encontró la reserva.</p>";
    exit;
}

// Si no tiene mascota asignada
$mascota = $r['mascota_nombre'] ?? "Sin asignar";
?>

<div class="modal-header">
    <h5 class="modal-title">Detalles de la reserva #<?= $id ?></h5>
    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">

    <div class="container">
        <div class="row mb-2">
            <div class="col-5 fw-bold">Servicio:</div>
            <div class="col-7"><?= htmlspecialchars($r['servicio_nombre']) ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-5 fw-bold">Cliente:</div>
            <div class="col-7"><?= htmlspecialchars($r['cliente_nombre']) ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-5 fw-bold">Cuidador:</div>
            <div class="col-7"><?= htmlspecialchars($r['cuidador_nombre']) ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-5 fw-bold">Mascota:</div>
            <div class="col-7"><?= htmlspecialchars($mascota) ?></div>
        </div>

        <hr>

        <div class="row mb-2">
            <div class="col-5 fw-bold">Fecha inicio:</div>
            <div class="col-7"><?= $r['fecha_inicio'] ?></div>
        </div>

        <div class="row mb-2">
            <div class="col-5 fw-bold">Fecha fin:</div>
            <div class="col-7"><?= $r['fecha_fin'] ?></div>
        </div>

        <?php if ($r['horas']): ?>
        <div class="row mb-2">
            <div class="col-5 fw-bold">Horas:</div>
            <div class="col-7"><?= $r['horas'] ?></div>
        </div>
        <?php endif; ?>

        <div class="row mb-2">
            <div class="col-5 fw-bold">Estado:</div>
            <div class="col-7">
                <span class="badge bg-secondary">
                    <?= htmlspecialchars($r['estado_reserva']) ?>
                </span>
            </div>
        </div>

        <?php if ($r['notas']): ?>
        <div class="row mb-2">
            <div class="col-5 fw-bold">Notas:</div>
            <div class="col-7"><?= nl2br(htmlspecialchars($r['notas'])) ?></div>
        </div>
        <?php endif; ?>
    </div>

</div>

<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
        Cerrar
    </button>
</div>
