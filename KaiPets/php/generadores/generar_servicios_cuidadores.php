<?php
echo "Hay que descomentar, pero puede ser inutil este codigo ya";
/* ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../php/conn_db.php';

// Precio base por defecto
$precio_base = 10.00;

// Obtener todos los cuidadores
$cuidadores = $conn->query("SELECT id FROM cuidadores")->fetchAll(PDO::FETCH_COLUMN);

// Obtener todos los servicios
$servicios = $conn->query("SELECT id_servicio FROM servicios")->fetchAll(PDO::FETCH_COLUMN);

if (!$cuidadores || !$servicios) {
    exit("No hay cuidadores o servicios disponibles.");
}

$check = $conn->prepare("
    SELECT id FROM cuidador_servicio
    WHERE cuidador_id = :cid AND servicio_id = :sid
");

$insert = $conn->prepare("
    INSERT INTO cuidador_servicio (cuidador_id, servicio_id, precio)
    VALUES (:cid, :sid, :precio)
");

foreach ($cuidadores as $cid) {

    foreach ($servicios as $sid) {

        // ¿Ya existe este registro? Evitar duplicados
        $check->execute([
            ":cid" => $cid,
            ":sid" => $sid
        ]);

        if ($check->fetch()) {
            echo "CUIDADOR $cid ya tiene el servicio $sid — OK<br>";
            continue;
        }

        // Insertar con precio por defecto
        $insert->execute([
            ":cid" => $cid,
            ":sid" => $sid,
            ":precio" => $precio_base
        ]);

        echo "Añadido servicio $sid al cuidador $cid<br>";
    }

    echo "<hr>";
}

echo "<br><b>Proceso completado.</b>";
 */