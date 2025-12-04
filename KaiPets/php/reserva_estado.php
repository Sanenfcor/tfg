<?php
session_start();
require_once __DIR__ . '/conn_db.php';

if (!isset($_GET['id']) || !isset($_GET['accion'])) {
    header("Location: /KaiPets/pags/perfil.php?err=estado");
    exit;
}

$id_reserva = intval($_GET['id']);
$accion = $_GET['accion'];

if (!in_array($accion, ['confirmar', 'cancelar'])) {
    header("Location: /KaiPets/pags/perfil.php?err=accion");
    exit;
}

$nuevo_estado = $accion === 'confirmar' ? 'confirmada' : 'cancelada';

$stmt = $conn->prepare("UPDATE reservas SET estado_reserva = :estado WHERE id_reserva = :id");
$stmt->execute([
    ':estado' => $nuevo_estado,
    ':id' => $id_reserva
]);

header("Location: /KaiPets/pags/perfil.php?estado_actualizado=1");
exit;
