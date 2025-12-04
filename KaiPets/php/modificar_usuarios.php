<?php
require_once "conn_db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 🔒 Solo los administradores pueden acceder
if (empty($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    header('Location: login.php');
    exit;
}

$errores = [];
$usuario_a_editar = null;

// 🔹 Si viene por GET → cargar los datos del usuario
if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $usuario_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$usuario_a_editar) {
            die("<p style='color:red;'>❌ Usuario no encontrado.</p>");
        }
    } catch (PDOException $e) {
        die("<p style='color:red;'>Error al cargar el usuario: " . htmlspecialchars($e->getMessage()) . "</p>");
    }
}

// 🔹 Si viene por POST → guardar los cambios
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $telefono = trim($_POST['telefono'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $admin = isset($_POST['admin']) ? intval($_POST['admin']) : 0;

    if (empty($nombre)) $errores[] = "El nombre no puede estar vacío.";
    if (empty($apellido)) $errores[] = "El apellido no puede estar vacío.";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errores[] = "El email no es válido.";
    if (empty($telefono)) $errores[] = "El teléfono no puede estar vacío.";
    if (empty($direccion)) $errores[] = "La dirección no puede estar vacía.";

    if (empty($errores)) {
        try {
            // Lógica de actualización de usuario (con o sin contraseña)
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE usuarios 
                        SET nombre = :nombre, apellido = :apellido, email = :email, 
                            password = :password, telefono = :telefono, 
                            direccion = :direccion, admin = :admin
                        WHERE id = :id";
                $params = [
                    ':nombre' => $nombre, ':apellido' => $apellido, ':email' => $email, 
                    ':password' => $hashedPassword, ':telefono' => $telefono, 
                    ':direccion' => $direccion, ':admin' => $admin, ':id' => $id
                ];
            } else {
                $sql = "UPDATE usuarios 
                        SET nombre = :nombre, apellido = :apellido, email = :email, 
                            telefono = :telefono, direccion = :direccion, admin = :admin
                        WHERE id = :id";
                $params = [
                    ':nombre' => $nombre, ':apellido' => $apellido, ':email' => $email, 
                    ':telefono' => $telefono, ':direccion' => $direccion, ':admin' => $admin, ':id' => $id
                ];
            }

            $stmt = $conn->prepare($sql);
            $stmt->execute($params);

            // Si el admin edita su propio perfil, actualiza la sesión
            if ($_SESSION['usuario']['id'] == $id) {
                // Lógica de actualización de sesión
                $_SESSION['usuario'] = array_merge($_SESSION['usuario'], 
                    compact('nombre', 'apellido', 'email', 'telefono', 'direccion', 'admin'));
            }

            $_SESSION['mensaje_exito'] = "✅ Usuario actualizado correctamente.";
            header("Location: ../pags/perfil.php");
            exit;
        } catch (PDOException $e) {
            $errores[] = "Error al guardar los cambios: " . htmlspecialchars($e->getMessage());
        }
    } 
    
    // Cargar los datos actuales para mantener el formulario relleno en caso de errores en POST
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $usuario_a_editar = $stmt->fetch(PDO::FETCH_ASSOC);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kai Pets</title>
    <link rel="stylesheet" href="../src/css/perfil.css">
</head>
<body>

    <?php include __DIR__ . '/../includes/header.php'; ?>

    <main class="main-content container">

        <?php if (!empty($errores)): ?>
            <div class="error-container">
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?= htmlspecialchars($error) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($usuario_a_editar): ?>
            <form action="modificar_usuarios.php" method="post" class="form-card">
                <h2 class="form-title">Editar usuario #<?= htmlspecialchars($usuario_a_editar['id'] ?? '') ?></h2>
                <input type="hidden" name="id" value="<?= htmlspecialchars($usuario_a_editar['id']) ?>">

                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? $usuario_a_editar['nombre']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" value="<?= htmlspecialchars($_POST['apellido'] ?? $usuario_a_editar['apellido']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? $usuario_a_editar['email']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="password">Nueva contraseña:</label>
                    <div class="input-password-group">
                        <input 
                            type="password" 
                            name="password" 
                            id="password"
                            placeholder="Dejar vacío para no cambiar"
                        >
                        <button 
                            type="button" 
                            id="togglePassword"
                            class="btn-toggle-password"
                        >
                            👁️
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?= htmlspecialchars($_POST['telefono'] ?? $usuario_a_editar['telefono']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($_POST['direccion'] ?? $usuario_a_editar['direccion']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="admin">Administrador:</label>
                    <select id="admin" name="admin" class="select-field">
                        <option value="0" <?= ($usuario_a_editar['admin'] ?? 0) == 0 ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= ($usuario_a_editar['admin'] ?? 0) == 1 ? 'selected' : '' ?>>Sí</option>
                    </select>
                </div>

                <div class="form-info">
                    <strong>Fecha de registro:</strong> <?= htmlspecialchars($usuario_a_editar['registro']) ?>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="../pags/perfil.php" class="btn btn-secondary">Volver</a>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <?php include __DIR__ . '/../includes/footer.php'; ?>
    <script src="../js/ver_contrasenia.js"></script>

</body>
</html>