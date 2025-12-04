<?php
require_once "conn_db.php";

if (!isset($_GET['ciudad_id'])) {
    echo json_encode([]);
    exit;
}

$ciudad_id = intval($_GET['ciudad_id']);
// Se ontieenn todos los barrios dependiedno de la ciudad
$stmt = $conn->prepare("SELECT id, nombre FROM barrios WHERE id_ciudad = :cid ORDER BY nombre ASC");
$stmt->execute([':cid' => $ciudad_id]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
exit;
