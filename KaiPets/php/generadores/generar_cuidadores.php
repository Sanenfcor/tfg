<?php
echo "Hay que descomentar, pero puede ser inutil este codigo ya";
/* ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../php/conn_db.php';

// Imágenes por defecto, luego ya se pueden añadir otrss, pero habría que modificar los usuarios 1 x 1
$default_dni = "/KaiPets/uploads/predeterminado/pred_dni.jpg";
$default_perfil = "/KaiPets/uploads/predeterminado/pred_usu.png";

// Seleccionar todos los que se llaman cuidador 
$sql = "
SELECT u.id, u.ciudad_id
FROM usuarios u
LEFT JOIN cuidadores c ON c.usuario_id = u.id
WHERE u.email LIKE 'cuidador%' 
  AND c.usuario_id IS NULL;
";

$stmt = $conn->query($sql);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!$usuarios) {
    echo "No hay cuidadores pendientes por crear.";
    exit;
}

$insert = $conn->prepare("
    INSERT INTO cuidadores
    (usuario_id, ciudad_id, barrio, descripcion, experiencia, dni_foto, foto_perfil)
    VALUES (:uid, :cid, :barrio, :descripcion, :experiencia, :dni, :perfil)
");

foreach ($usuarios as $u) {

    $insert->execute([
        ":uid" => $u["id"],
        ":cid" => $u["ciudad_id"],
        ":barrio" => "Barrio " . $u["id"],   // Puedes cambiarlo por uno real si quieres
        ":descripcion" => "Cuidador disponible para servicios.",
        ":experiencia" => "Sin experiencia registrada.",
        ":dni" => $default_dni,
        ":perfil" => $default_perfil,
    ]);

    echo "Cuidador creado para usuario ID: {$u['id']}<br>";
}

echo "<br><br>Proceso finalizado."; */
