<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay usuario logueado, redirige
if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

require_once __DIR__ . "/../php/conn_db.php";

$usuario = $_SESSION['usuario'];

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kai Pets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
    <link rel="stylesheet" href="/KaiPets/src/css/perfil.css">
</head>

<body>

    <?php include __DIR__ . '/../componentes/header.php'; ?>

    <main class="main-content">
    
        <section class="perfil-historial">
            <h2 class="Titutlo_Pedidos">Historial de pedidos</h2>

            <?php
            try {
                $stmt = $conn->prepare("SELECT id, total, fecha_pedido FROM pedidos WHERE id_usuario = :id ORDER BY fecha_pedido DESC");
                $stmt->execute([':id' => $usuario['id']]);
                $pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($pedidos) {
                    echo "<ol>";
                    foreach ($pedidos as $p) {
                        echo "<li>Pedido #{$p['id']} — Total: {$p['total']} € — Fecha: {$p['fecha_pedido']}</li>";
                    }
                    echo "</ol>";
                } else {
                    echo "<p>No has realizado ningún pedido.</p>";
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error en historial: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </section>

        <section class="perfil-servicios">
            <h2 class="titulo_servicios">Servicios que ofrezco</h2>

            <?php
            try {
                $stmt = $conn->prepare("SELECT * FROM servicios_cuidador WHERE usuario_id = :id");
                $stmt->execute([':id' => $usuario['id']]);
                $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($servicios) {
                    echo "<ul>";
                    foreach ($servicios as $s) {
                        echo "<li><strong>{$s['titulo']}</strong> — {$s['precio']} €<br> {$s['descripcion']}</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No tienes servicios publicados.</p>";
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error cargando servicios: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </section>
    
        <section class="perfil-cuidados">
            <h2 class="Titulo_Cuidados">Mascotas que he cuidado</h2>

            <?php
            try {
                $sql = "SELECT h.*, m.nombre AS mascota_nombre, u.nombre AS duenio
                FROM historial_cuidados h
                JOIN mascotas m ON m.id = h.mascota_id
                JOIN usuarios u ON u.id = m.usuario_id
                WHERE h.cuidador_id = :id
                ORDER BY h.fecha_inicio DESC";

                $stmt = $conn->prepare($sql);
                $stmt->execute([':id' => $usuario['id']]);
                $cuidados = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($cuidados) {
                    echo "<ul>";
                    foreach ($cuidados as $c) {
                        echo "<li>
                        Mascota: <strong>{$c['mascota_nombre']}</strong> (dueño: {$c['duenio']})<br>
                        Desde: {$c['fecha_inicio']} — Hasta: {$c['fecha_fin']}<br>
                        Notas: {$c['notas']}
                    </li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No has cuidado ninguna mascota todavía.</p>";
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error cargando historial de cuidados: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </section>
        
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js">
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('addProductModal');
            if (!modal) return;

            const modalContent = modal.querySelector('.modal-content');

            modal.addEventListener('show.bs.modal', () => {
                fetch('anadir_producto.php?is_modal=true')
                    .then(response => response.ok ? response.text() : Promise.reject())
                    .then(html => modalContent.innerHTML = html)
                    .catch(() => modalContent.innerHTML = "<div class='alert alert-danger p-4'>Error al cargar el formulario.</div>");
            });

            modal.addEventListener('hidden.bs.modal', () => {
                modalContent.innerHTML = "";
            });
        });
    </script>

    <?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>

</html>