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
exit;
