<?php
echo "Hay que descomentar, pero puede ser inutil este codigo ya";
/* require_once __DIR__ . "/../php/conn_db.php";

$inicio = 10;   // empieza en usuario 5
$fin    = 14;  // cuantos quieres generar (puedes aumentar)

for ($i = $inicio; $i <= $fin; $i++) {

    $nombre     = "Cuidador";
    $apellido   = $i;
    $email      = "cuidador{$i}@gmail.com";
    $password   = password_hash("123456789", PASSWORD_DEFAULT);
    $telefono   = "600000$i";    // teléfono de prueba
    $direccion  = "Andalucia";
    $terminos   = 1;
    $admin      = 0;
    $cuidador   = 0;
    $ciudad_id  = 2; // según tu ejemplo

    $sql = "INSERT INTO usuarios 
        (nombre, apellido, email, password, telefono, direccion, terminos, admin, cuidador, ciudad_id)
        VALUES
        (:n, :a, :e, :p, :t, :d, :ter, :adm, :cuid, :cid)";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ":n" => $nombre,
        ":a" => $apellido,
        ":e" => $email,
        ":p" => $password,
        ":t" => $telefono,
        ":d" => $direccion,
        ":ter" => $terminos,
        ":adm" => $admin,
        ":cuid" => $cuidador,
        ":cid" => $ciudad_id
    ]);

    echo "✔ Usuario $i generado<br>";
}

echo "<hr>Completado."; */
