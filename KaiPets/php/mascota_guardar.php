<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/conn_db.php';

$usuario_id = $_POST['usuario_id'];
$nombre = $_POST['nombre'];
$especie = $_POST['especie'];
$raza = $_POST['raza'] ?? null;
$edad = intval($_POST['edad']);
$peso = $_POST['peso'];

$sql = "INSERT INTO mascotas (usuario_id, nombre, especie, raza, edad, peso)
        VALUES (:u, :n, :e, :r, :a, :p)";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ":u" => $usuario_id,
    ":n" => $nombre,
    ":e" => $especie,
    ":r" => $raza,
    ":a" => $edad,
    ":p" => $peso
]);

header("Location: /KaiPets/pags/perfil.php?mascota=ok");

require_once __DIR__ . '/email.php';

// Datos del cliente
$emailCliente = $_SESSION['usuario']['email'];
$nombreCliente = $_SESSION['usuario']['nombre'];

// Mensaje
$html = "
    <h2>¡Reserva enviada!</h2>
    <p>Hola {$nombreCliente}, tu solicitud de reserva ha sido enviada correctamente.</p>
    <p><strong>Detalles:</strong></p>
    <p>Mascota: {$nombre_mascota}</p>
    <p>Cuidador: {$cuidador_nombre}</p>
    <p>Servicio: {$servicio_nombre}</p>
    <p>Fecha: {$fecha_mostrar}</p>
    <br>
    <p>Te avisaremos cuando el cuidador acepte o rechace la reserva.</p>
";

enviarEmail($emailCliente, "KaiPets - Tu reserva está en proceso", $html);

exit;
