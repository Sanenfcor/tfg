<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// --- Conexión a la base de datos ---
$host = '100.72.131.53';
$dbname = 'kaipets';
$username = 'sanenfcor';
$password = '0147';

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>
