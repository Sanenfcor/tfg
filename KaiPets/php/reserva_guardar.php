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

/* ============================================
    1️ VALIDAR CAMPOS RECIBIDOS
============================================ */
$id_cuidador = intval($_POST['cuidador'] ?? 0);
$id_servicio = intval($_POST['servicio'] ?? 0);
$id_mascota = intval($_POST['id_mascota'] ?? 0);
$tipo = $_POST['tipo'] ?? "";
$notas = trim($_POST['notas'] ?? "");

if ($id_cuidador <= 0 || $id_servicio <= 0 || $id_mascota <= 0) {
    die("Datos incompletos.");
}

/* Mascota pertenece al usuario */
$sql = "SELECT COUNT(*) FROM mascotas WHERE id = :m AND usuario_id = :u";
$stmt = $conn->prepare($sql);
$stmt->execute([':m' => $id_mascota, ':u' => $id_usuario]);
if ($stmt->fetchColumn() == 0) {
    die("La mascota no pertenece al usuario.");
}

/* ============================================
    2️ PROCESAR FECHAS SEGÚN TIPO
============================================ */
if ($tipo === "dias") {
    $fi = $_POST['fecha_inicio'] ?? "";
    $ff = $_POST['fecha_fin'] ?? "";

    if (!$fi || !$ff) {
        die("Debes seleccionar fecha de inicio y fin.");
    }

    if ($ff < $fi) {
        die("La fecha fin no puede ser anterior a la fecha inicio.");
    }

    $fecha_inicio = $fi . " 00:00:00";
    $fecha_fin = $ff . " 23:59:59";
    $horas = null;

} elseif ($tipo === "horas") {

    $fu = $_POST['fecha_unica'] ?? "";
    $horas = intval($_POST['horas'] ?? 0);

    if (!$fu || $horas <= 0) {
        die("Debes indicar fecha y número de horas.");
    }

    $fecha_inicio = $fu . " 00:00:00";
    $fecha_fin    = $fu . " 23:59:59";

} else {
    die("Tipo de reserva no válido.");
}

/* ============================================
    3️ Validar que el cuidador ofrece ese servicio
============================================ */
$sql = "
    SELECT precio 
    FROM cuidador_servicio 
    WHERE cuidador_id = :c AND servicio_id = :s
";
$stmt = $conn->prepare($sql);
$stmt->execute([':c' => $id_cuidador, ':s' => $id_servicio]);
$precio = $stmt->fetchColumn();

if (!$precio) {
    die("El cuidador no ofrece este servicio.");
}

/* ============================================
    4️ Comprobar que NO se solapan reservas
============================================ */
$sql = "
    SELECT COUNT(*) 
    FROM reservas
    WHERE id_cuidador = :c
    AND estado_reserva = 'confirmada'
    AND (
        (fecha_inicio <= :fi AND fecha_fin >= :fi) OR
        (fecha_inicio <= :ff AND fecha_fin >= :ff) OR
        (fecha_inicio >= :fi AND fecha_fin <= :ff)
    )
";
$stmt = $conn->prepare($sql);
$stmt->execute([
    ':c' => $id_cuidador,
    ':fi' => $fecha_inicio,
    ':ff' => $fecha_fin
]);

if ($stmt->fetchColumn() > 0) {
    die("El cuidador NO está disponible en ese intervalo.");
}

/* ============================================
    5️ Calcular precio final
============================================ */
$total = $precio;

if ($tipo === "dias") {
    $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / 86400 + 1;
    $total = $precio * $dias;
}

if ($tipo === "horas") {
    $total = $precio * $horas;
}

/* ============================================
    6️ Insertar reserva
============================================ */
$sql = "
INSERT INTO reservas (
    id_usuario, id_cuidador, id_mascota, id_servicio,
    tipo_reserva, fecha_reserva, fecha_inicio, fecha_fin, horas,
    notas, precio_final
)
VALUES (:u, :c, :m, :s, :t, NOW(), :fi, :ff, :h, :n, :p)
";

$stmt = $conn->prepare($sql);
$stmt->execute([
    ':u' => $id_usuario,
    ':c' => $id_cuidador,
    ':m' => $id_mascota,
    ':s' => $id_servicio,
    ':t' => $tipo,
    ':fi' => $fecha_inicio,
    ':ff' => $fecha_fin,
    ':h' => $horas,
    ':n' => $notas,
    ':p' => $total
]);

header("Location: /KaiPets/pags/perfil.php?reserva=ok");
exit;

?>
