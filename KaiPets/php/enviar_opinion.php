<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
require_once __DIR__ . '/conn_db.php';

if (!isset($_SESSION['usuario'])) {
    header("Location: /KaiPets/pags/login.php");
    exit;
}

$id_usuario = $_SESSION['usuario']['id'];
$cuidador_id = intval($_POST['cuidador_id']);
$puntuacion = intval($_POST['puntuacion']);
$comentario = trim($_POST['comentario'] ?? "");

/* VALIDACIÓN BÁSICA */
if ($cuidador_id <= 0 || $puntuacion < 1 || $puntuacion > 5) {
    die("Datos inválidos.");
}

/* VALIDACIÓN 1: no permitir opinar sobre uno mismo */
$sql = "SELECT usuario_id FROM cuidadores WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->execute([':id' => $cuidador_id]);
$cuidador_usuario_id = $stmt->fetchColumn();

if ($cuidador_usuario_id == $id_usuario) {
    die("No puedes opinar sobre ti mismo.");
}

/* VALIDACIÓN 2: solo opinar si has tenido una reserva confirmada */
$sql = "
    SELECT COUNT(*) FROM reservas
    WHERE id_usuario = :u
    AND id_cuidador = :c
    AND estado_reserva = 'confirmada'
";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':u' => $id_usuario,
    ':c' => $cuidador_id
]);

$validacion = $stmt->fetchColumn();

if ($validacion == 0) {
    die("Debes tener al menos 1 reserva confirmada con este cuidador para dejar una opinión.");
}

/* INSERTAR OPINIÓN */
$sql = "
    INSERT INTO opiniones (cuidador_id, usuario_id, puntuacion, comentario)
    VALUES (:c, :u, :p, :m)
";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':c' => $cuidador_id,
    ':u' => $id_usuario,
    ':p' => $puntuacion,
    ':m' => $comentario
]);

header("Location: /KaiPets/pags/ver_cuidador.php?id=$cuidador_id&opinion=ok");
exit;

?>
