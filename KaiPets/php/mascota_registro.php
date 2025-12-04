<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../php/conn_db.php';

$usuario = $_SESSION['usuario'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Añadir mascota</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
</head>

<body class="bg-light">

<?php include "../componentes/header.php"; ?>

<div class="container mt-5">
    <div class="card shadow p-4" style="max-width: 600px; margin:auto; border-radius:20px;">
    
        <h2 class="text-center mb-4">Añadir mascota</h2>

        <form action="/KaiPets/php/mascota_guardar.php" method="POST">

            <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>">

            <div class="mb-3">
                <label class="form-label">Nombre de la mascota</label>
                <input type="text" name="nombre" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Especie</label>
                <select name="especie" class="form-control" required>
                    <option value="">Selecciona...</option>
                    <option value="Perro">Perro</option>
                    <option value="Gato">Gato</option>
                    <option value="Ave">Ave</option>
                    <option value="Conejo">Conejo</option>
                    <option value="Reptil">Reptil</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Raza</label>
                <input type="text" name="raza" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">Edad (años)</label>
                <input type="number" name="edad" min="0" max="50" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Peso (kg)</label>
                <input type="number" step="0.01" min="0" name="peso" class="form-control" required>
            </div>

            <button class="btn btn-primary w-100" style="border-radius:25px;">Guardar mascota</button>

        </form>
    </div>
</div>

<?php include "../componentes/footer.php"; ?>

</body>
</html>
