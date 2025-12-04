<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/conn_db.php';
require_once __DIR__ . '/email.php';

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

// Nuevo estado de la reserva
$nuevo_estado = $accion === 'confirmar' ? 'confirmada' : 'cancelada';

$sql = "
    SELECT r.*, 
           u.email AS cliente_email,
           u.nombre AS cliente_nombre,
           cu.nombre AS cuidador_nombre,     -- ✔ NOMBRE DEL USUARIO CUIDADOR
           cu.apellido AS cuidador_apellido, -- ✔ POR SI QUIERES USARLO
           c.id AS cuidador_id,
           s.nombre AS servicio_nombre
    FROM reservas r
    JOIN usuarios u ON u.id = r.id_usuario
    JOIN usuarios cu ON cu.id = r.id_cuidador
    JOIN cuidadores c ON c.usuario_id = cu.id
    JOIN servicios s ON s.id_servicio = r.id_servicio
    WHERE r.id_reserva = :id
";


$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $id_reserva]);
$reserva = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reserva) {
    header("Location: /KaiPets/pags/perfil.php?err=no_reserva");
    exit;
}

// Datos útiles
// $emailCliente      = $reserva['cliente_email'];
$emailCliente = 'empresa.prueba.correo.s25@gmail.com';
$nombreCliente     = $reserva['cliente_nombre'];
$cuidadorNombre    = $reserva['cuidador_nombre'];
$servicioNombre    = $reserva['servicio_nombre'];

// Actualizar estado
$stmt = $conn->prepare("
    UPDATE reservas 
    SET estado_reserva = :estado 
    WHERE id_reserva = :id
");
$stmt->execute([
    ':estado' => $nuevo_estado,
    ':id' => $id_reserva
]);

if ($nuevo_estado === 'confirmada') {

    // Todo ok
    $html = "
        <h2>¡Tu reserva ha sido aceptada!</h2>
        <p>El cuidador <strong>{$cuidadorNombre}</strong> ha aceptado tu solicitud.</p>
        <p><strong>Servicio:</strong> {$servicioNombre}</p>
        <br>
        <p>Puedes ver tu reserva desde tu panel.</p>
        <p>Gracias por confiar en KaiPets 🐾</p>
    ";

    enviarEmail($emailCliente, "KaiPets - Tu reserva ha sido ACEPTADA", $html);

} else {

    // Se cancela
    $html = "
        <h2>Tu reserva ha sido rechazada</h2>
        <p>El cuidador <strong>{$cuidadorNombre}</strong> ha rechazado tu solicitud.</p>
        <p><strong>Servicio:</strong> {$servicioNombre}</p>
        <br>
        <p>Puedes elegir otro cuidador o intentar otra fecha.</p>
        <p>Gracias por usar KaiPets 🐾</p>
    ";

    enviarEmail($emailCliente, "KaiPets - Tu reserva ha sido CANCELADA", $html);
}

header("Location: /KaiPets/pags/perfil.php?estado_actualizado=1");
exit;
