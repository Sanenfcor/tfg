<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "conn_db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['usuario']) || $_SESSION['usuario']['admin'] != 1) {
    header('Location: login.php');
    exit;
}

// Si no se pasa un id válido, redirigir
if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<p style='color:red;'>❌ ID de usuario no válido.</p>");
}

$id_usuario = intval($_GET['id']);

// 🔹 Cargar los datos del usuario
$stmtUsuario = $conn->prepare("SELECT nombre, apellido, email FROM usuarios WHERE id = :id");
$stmtUsuario->execute([':id' => $id_usuario]);
$usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("<p style='color:red;'>❌ Usuario no encontrado.</p>");
}

// 🔹 Obtener pedidos con detalle de productos
$stmt = $conn->prepare("
    SELECT 
        p.id AS pedido_id,
        p.fecha_pedido,
        p.total,
        p.estado_pedido,
        pd.cantidad,
        pd.precio_unitario,
        pr.nombre AS producto_nombre
    FROM pedidos p
    JOIN pedido_detalles pd ON pd.pedido_id = p.id
    JOIN productos pr ON pd.id_producto = pr.id
    WHERE p.id_usuario = :id
    ORDER BY p.fecha_pedido DESC
");
$stmt->execute([':id' => $id_usuario]);
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

include __DIR__ . '/../includes/header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kai Pets</title>
    <link rel="stylesheet" href="/KaiPets/src/css/perfil.css">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h2 class="mb-3">
            Historial de pedidos de <?= htmlspecialchars($usuario['nombre'] . ' ' . $usuario['apellido']) ?>
        </h2>
        <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>
        <hr>

        <?php if (empty($pedidos)): ?>
            <div class="alert alert-info">Este usuario no tiene pedidos registrados.</div>
        <?php else: ?>
            <?php
            $pedidoActual = 0;
            foreach ($pedidos as $detalle):
                // Calculamos diferencia de días
                $fechaPedido = new DateTime($detalle['fecha']);
                $hoy = new DateTime();
                $diff = $fechaPedido->diff($hoy)->days;

                // Si tiene menos de 2 días → sin entregar
                if ($diff < 2) {
                    $estadoTexto = "Sin entregar";
                    $estadoClase = "text-warning";
                } else {
                    $estadoTexto = "Entregado";
                    $estadoClase = "text-success";
                }
                // Nuevo pedido → crear una nueva tarjeta
                if ($pedidoActual !== $detalle['pedido_id']):
                    // Si ya hay un pedido abierto, cerrarlo
                    if ($pedidoActual !== 0) {
                        echo "
                                    </tbody>
                                </table>
                            </div>
                            <div class='card-footer text-end'>
                                <strong>Total:</strong> " . number_format($totalActual, 2) . " €
                            </div>
                        </div>";
                    }

                    $pedidoActual = $detalle['pedido_id'];
                    $totalActual = $detalle['total'];
                    ?>

                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-dark text-white d-flex justify-content-between">
                            <span><strong>Pedido #<?= $detalle['pedido_id'] ?></strong> — Fecha: <?= $detalle['fecha'] ?></span>
                            <span class="<?= $estadoClase ?>">
                                <?= $estadoTexto ?>
                            </span>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-striped mb-0">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Cantidad</th>
                                        <th>Precio unitario (€)</th>
                                    </tr>
                                </thead>
                                <tbody>
                <?php endif; ?>

                <tr>
                    <td><?= htmlspecialchars($detalle['producto_nombre']) ?></td>
                    <td><?= $detalle['cantidad'] ?></td>
                    <td><?= number_format($detalle['precio'], 2) ?></td>
                </tr>

            <?php endforeach; ?>

            <!-- 🔚 Cierre del último pedido -->
            </tbody>
            </table>
            </div>
            <div class="card-footer text-end">
                <strong>Total:</strong> <?= number_format($totalActual, 2) ?> €
            </div>
            </div>
        <?php endif; ?>

        <a href="./perfil.php" class="btn btn-secondary mt-3">← Volver al perfil</a>
    </div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
</body>
</html>