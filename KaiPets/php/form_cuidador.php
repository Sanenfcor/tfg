<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once "../php/conn_db.php";

$usuario = $_SESSION['usuario'];

// Obtener ciudades
$ciudades = $conn->query("SELECT * FROM ciudades ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // =====================================================
    // VALIDACIÓN DE ARCHIVOS
    // =====================================================

    // Carpetas donde se guardarán imágenes
    $dir_dni     = "../uploads/dni/";
    $dir_perfil  = "../uploads/perfiles/";

    // Crear carpetas si no existen
    if (!is_dir($dir_dni)) mkdir($dir_dni, 0755, true);
    if (!is_dir($dir_perfil)) mkdir($dir_perfil, 0755, true);

    // --- Validar DNI ---
    if (!isset($_FILES['dni_foto']) || $_FILES['dni_foto']['error'] !== 0) {
        die("Error: Debes subir una foto del DNI.");
    }

    // --- Validar Foto personal ---
    if (!isset($_FILES['foto_perfil']) || $_FILES['foto_perfil']['error'] !== 0) {
        die("Error: Debes subir una foto personal.");
    }

    // Validar extensiones
    $extensiones = ['jpg','jpeg','png','webp'];

    function validar_extension($file) {
        global $extensiones;
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        return in_array($ext, $extensiones);
    }

    if (!validar_extension($_FILES['dni_foto'])) {
        die("Formato de DNI no válido. Solo JPG, PNG o WEBP.");
    }

    if (!validar_extension($_FILES['foto_perfil'])) {
        die("Formato de foto personal no válido. Solo JPG, PNG o WEBP.");
    }

    // --- Guardar archivos ---
    $dni_nombre = uniqid("dni_") . "_" . basename($_FILES['dni_foto']['name']);
    $foto_nombre = uniqid("foto_") . "_" . basename($_FILES['foto_perfil']['name']);

    $dni_ruta = $dir_dni . $dni_nombre;
    $foto_ruta = $dir_perfil . $foto_nombre;

    move_uploaded_file($_FILES['dni_foto']['tmp_name'], $dni_ruta);
    move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $foto_ruta);

    // Convertir rutas a formato accesible desde la web
    $dni_url = "/KaiPets/uploads/dni/" . $dni_nombre;
    $foto_url = "/KaiPets/uploads/perfiles/" . $foto_nombre;

    // =====================================================
    // INSERTAR CUIDADOR
    // =====================================================

    $stmt = $conn->prepare("
        INSERT INTO cuidadores 
        (usuario_id, ciudad_id, barrio, descripcion, experiencia, dni_foto, foto_perfil)
        VALUES (:uid, :cid, :barrio, :descripcion, :exp, :dni, :foto)
    ");

    $stmt->execute([
        ':uid' => $usuario['id'],
        ':cid' => $_POST['ciudad_id'],
        ':barrio' => $_POST['barrio'],
        ':descripcion' => $_POST['descripcion'],
        ':exp' => $_POST['experiencia'],
        ':dni' => $dni_url,
        ':foto' => $foto_url
    ]);

    header("Location: /KaiPets/pags/perfil.php?msg=cuidador_ok");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Convertirse en Cuidador</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
</head>
<body>

<?php include "../componentes/header.php"; ?>

<div class="container py-5">
    <h2 class="mb-4">Convertirse en Cuidador</h2>

    <form action="form_cuidador.php" method="POST" enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Ciudad</label>
            <select name="ciudad_id" class="form-select" required>
                <option value="">Seleccione una ciudad...</option>
                <?php foreach ($ciudades as $c): ?>
                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Barrio</label>
            <input type="text" name="barrio" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Descripción</label>
            <textarea name="descripcion" class="form-control" rows="3" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Experiencia</label>
            <input type="text" name="experiencia" class="form-control">
        </div>

        <!-- FOTO DNI -->
        <div class="mb-3">
            <label class="form-label">Foto del DNI (obligatoria)</label>
            <input type="file" name="dni_foto" class="form-control" accept="image/*" required>
        </div>

        <!-- FOTO PERSONAL -->
        <div class="mb-3">
            <label class="form-label">Foto personal (obligatoria)</label>
            <input type="file" name="foto_perfil" class="form-control" accept="image/*" required>
        </div>

        <button class="btn btn-success">Convertirme en Cuidador</button>
    </form>
</div>

<?php include "../componentes/footer.php"; ?>
</body>
</html>
