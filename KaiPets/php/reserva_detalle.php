<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/../php/conn_db.php';

if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
    echo "<p>Error: ID inválido.</p>";
    exit;
}

$id_reserva = intval($_GET['id']);

$sql = "
    SELECT r.*, 
           u.nombre AS cliente_nombre, u.email AS cliente_email,
           c.usuario_id AS cuidador_usuario,
           s.nombre AS servicio_nombre,
           m.nombre AS mascota_nombre, m.especie, m.raza, m.peso
    FROM reservas r
    JOIN usuarios u ON u.id = r.id_usuario
    JOIN servicios s ON s.id_servicio = r.id_servicio
    LEFT JOIN mascotas m ON m.id = r.id_mascota
    LEFT JOIN cuidadores c ON c.id = r.id_cuidador
    WHERE r.id_reserva = :id
";

$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id_reserva]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    echo "<p>Reserva no encontrada.</p>";
    exit;
}
?>

<div class="p-3">

    <h4 class="mb-3"><?= htmlspecialchars($reserva['servicio_nombre']) ?></h4>

    <table class="table table-bordered">
        <tr>
            <th>Cliente</th>
            <td><?= htmlspecialchars($reserva['cliente_nombre']) ?> (<?= htmlspecialchars($reserva['cliente_email']) ?>)</td>
        </tr>

        <tr>
            <th>Estado</th>
            <td>
                <span class="badge bg-primary fs-6">
                    <?= htmlspecialchars($reserva['estado_reserva']) ?>
                </span>
            </td>
        </tr>

        <tr>
            <th>Fecha inicio</th>
            <td><?= $reserva['fecha_inicio'] ?></td>
        </tr>

        <tr>
            <th>Fecha fin</th>
            <td><?= $reserva['fecha_fin'] ?></td>
        </tr>

        <?php if (!empty($reserva['horas'])): ?>
        <tr>
            <th>Horas contratadas</th>
            <td><?= $reserva['horas'] ?></td>
        </tr>
        <?php endif; ?>

        <tr>
            <th>Notas del cliente</th>
            <td><?= nl2br(htmlspecialchars($reserva['notas'] ?? 'Ninguna')) ?></td>
        </tr>

        <tr>
            <th>Mascota</th>
            <td>
                <?php if ($reserva['mascota_nombre']): ?>
                    <strong><?= htmlspecialchars($reserva['mascota_nombre']) ?></strong><br>
                    Especie: <?= htmlspecialchars($reserva['especie']) ?><br>
                    Raza: <?= htmlspecialchars($reserva['raza']) ?><br>
                    Peso: <?= htmlspecialchars($reserva['peso']) ?> kg
                <?php else: ?>
                    Sin asignar
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <div class="text-end">
        <button class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:20px;">
            Cerrar
        </button>
    </div>

</div>
