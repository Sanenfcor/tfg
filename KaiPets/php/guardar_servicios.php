<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../php/conn_db.php";

// Solo cuidadores
if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario']['id'];

$stmt = $conn->prepare("SELECT id FROM cuidadores WHERE usuario_id = ?");
$stmt->execute([$usuario_id]);
$cuidador = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cuidador) {
    die("Solo cuidadores.");
}

$cuidador_id = $cuidador['id'];

// Eliminar servicios actuales
$conn->prepare("DELETE FROM cuidador_servicio WHERE cuidador_id = ?")->execute([$cuidador_id]);

// Insertar nuevos
if (!empty($_POST['servicios'])) {
    foreach ($_POST['servicios'] as $id_servicio => $datos) {
        if (!isset($datos['activar'])) continue;

        $precio = floatval($datos['precio'] ?? 0);

        $insert = $conn->prepare("
            INSERT INTO cuidador_servicio (cuidador_id, servicio_id, precio)
            VALUES (?, ?, ?)
        ");
        $insert->execute([$cuidador_id, $id_servicio, $precio]);
    }
}

header("Location: ../pags/perfil.php?msg=servicios_actualizados");
exit;
