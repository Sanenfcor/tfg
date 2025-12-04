<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Si no hay usuario logueado, redirige
if (empty($_SESSION['usuario'])) {
    header('Location: ../php/login.php');
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
        <section class="perfil-info">
            <h2 class="Titulo_Usu">Información de usuario</h2>
            <div class="Info_Usu">
                <p><strong>Nombre de usuario:</strong> <?= htmlspecialchars($usuario['nombre']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($usuario['email']) ?></p>

                <p><strong>Registrado el:</strong>
                    <?php
                    try {
                        $stmt = $conn->prepare("SELECT registro FROM usuarios WHERE id = :id");
                        $stmt->execute([':id' => $usuario['id']]);
                        $data = $stmt->fetch(PDO::FETCH_ASSOC);

                        echo $data ? htmlspecialchars($data['registro']) : "No disponible";
                    } catch (PDOException $e) {
                        echo "<span style='color:red;'>Error: " . htmlspecialchars($e->getMessage()) . "</span>";
                    }
                    ?>
                </p>
            </div>

            <p class="Boton_Sesion">
                <a href="/KaiPets/php/logout.php" style="text-decoration:none;color:black;">
                    Cerrar sesión
                </a>
            </p>
        </section>

        <section class="perfil-historial">
            <h2 class="Titutlo_Pedidos">Historial de cuidados / reservas</h2>
            <?php
            $stmt = $conn->prepare("
                SELECT r.*, s.nombre AS servicio
                FROM reservas r
                JOIN servicios s ON s.id_servicio = r.id_servicio
                WHERE r.id_usuario = :id
                ORDER BY r.fecha_inicio DESC
            ");
            $stmt->execute([':id' => $usuario['id']]);
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$reservas) {
                echo "<p>No tienes reservas todavía.</p>";
            } else {
                echo "<ul class='lista-reservas'>";
                foreach ($reservas as $r) {
                    echo "<li>
                        <strong>{$r['servicio']}</strong><br>
                        Desde: {$r['fecha_inicio']}<br>
                        Hasta: {$r['fecha_fin']}<br>
                        <button class='btn btn-sm btn-info mt-2'
                                style='border-radius:20px;'
                                onclick='verDetallesReserva(" . $r['id_reserva'] . ")'>
                            Ver detalles
                        </button>
                    </li>";
                }
                echo "</ul>";
            }
            ?>
        </section>

        <section class="perfil-mascotas">
            <h2 class="Titulo_Mascotas">Mis mascotas</h2>
            <a href="/KaiPets/php/mascota_registro.php" class="btn btn-success mb-3" style="border-radius:25px;">
                Añadir mascota
            </a>

            <?php
            try {
                $stmt = $conn->prepare("SELECT * FROM mascotas WHERE usuario_id = :id");
                $stmt->execute([':id' => $usuario['id']]);
                $mascotas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($mascotas) {
                    echo "<ul>";
                    foreach ($mascotas as $m) {
                        echo "<li><strong>{$m['nombre']}</strong> — {$m['especie']} ({$m['raza']}) — {$m['edad']} años — {$m['peso']} kg</li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p>No tienes mascotas registradas.</p>";
                }
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error al cargar mascotas: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </section>

        <?php if ($usuario['cuidador'] == 1): ?>
            <section class="perfil-servicios">
            <h2 class="titulo_servicios">Servicios que ofrezco</h2>

            <a href="/KaiPets/pags/gestionar_servicios.php" class="btn btn-primary mb-3" 
            style="border-radius: 25px; padding: 10px 20px; font-weight: 600;">
                Gestionar servicios
            </a>


            <?php
            try {
                $stmt = $conn->prepare("
                    SELECT cs.precio, s.nombre, s.descripcion
                    FROM cuidador_servicio cs
                    JOIN servicios s ON s.id_servicio = cs.servicio_id
                    WHERE cs.cuidador_id = :id
                ");
                $stmt->execute([':id' => $usuario['id']]);
                $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($servicios) {
                    echo "<ul>";
                    foreach ($servicios as $s) {
                        echo "<li>
                            <strong>" . htmlspecialchars($s['nombre']) . "</strong> — 
                            " . number_format($s['precio'], 2) . " €
                            <br>
                            " . htmlspecialchars($s['descripcion']) . "
                        </li>";
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
    
        <section class="perfil-reservas-cuidador">
            <h2 class="Titulo_Reservas_Cuidador">Reservas recibidas</h2>

            <?php
            try {
                $sql = "
                    SELECT r.*, 
                        m.nombre AS mascota_nombre,
                        u.nombre AS cliente_nombre,
                        s.nombre AS servicio_nombre
                    FROM reservas r
                    JOIN usuarios u ON u.id = r.id_usuario
                    LEFT JOIN mascotas m ON m.id = r.id_mascota
                    JOIN servicios s ON s.id_servicio = r.id_servicio
                    WHERE r.id_cuidador = :id
                    ORDER BY r.fecha_inicio DESC
                ";

                $stmt = $conn->prepare($sql);
                $stmt->execute([':id' => $usuario['id']]);
                $reservas_cuidador = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($reservas_cuidador) {
                    echo "<ul class='lista-reservas-cuidador'>";

                    foreach ($reservas_cuidador as $r) {

                        $mascota = $r['mascota_nombre'] ?? "Sin asignar";

                        echo "<li class='card mb-3 p-3'>
                                <strong>Servicio:</strong> {$r['servicio_nombre']}<br>
                                <strong>Cliente:</strong> {$r['cliente_nombre']}<br>
                                <strong>Mascota:</strong> {$mascota}<br>
                                <strong>Desde:</strong> {$r['fecha_inicio']}<br>
                                <strong>Hasta:</strong> {$r['fecha_fin']}<br>
                                <strong>Estado:</strong> <span class='badge bg-secondary'>{$r['estado_reserva']}</span><br>
                                <hr>

                                <div class='d-flex gap-2'>
                                    <button class='btn btn-sm btn-info mt-2'
                                            style='border-radius:20px;'
                                            onclick='verDetallesReserva(" . $r['id_reserva'] . ")'>
                                        Ver detalles
                                    </button>";

                        if ($r['estado_reserva'] == 'pendiente') {

                            echo "
                            <a href='/KaiPets/php/reserva_estado.php?id={$r['id_reserva']}&accion=confirmar'
                            class='btn btn-sm btn-success' style='border-radius:20px;'>
                                Aceptar
                            </a>

                            <a href='/KaiPets/php/reserva_estado.php?id={$r['id_reserva']}&accion=cancelar'
                            class='btn btn-sm btn-danger' style='border-radius:20px;'>
                                Rechazar
                            </a>";
                        }

                        echo "</div>
                            </li>";
                    }

                    echo "</ul>";

                } else {
                    echo "<p>No tienes reservas como cuidador.</p>";
                }

            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error al cargar reservas: " . htmlspecialchars($e->getMessage()) . "</p>";
            }
            ?>
        </section>

        <section class="timeline-cuidador mt-5">
            <h2 class="Titulo_Timeline">Línea de tiempo de cuidados</h2>

            <?php
            $sql = "
                SELECT fecha_inicio, fecha_fin, 'reserva' AS tipo, estado_reserva AS estado,
                    s.nombre AS servicio, m.nombre AS mascota
                FROM reservas r
                JOIN servicios s ON s.id_servicio = r.id_servicio
                LEFT JOIN mascotas m ON m.id = r.id_mascota
                WHERE r.id_cuidador = :id

                UNION ALL

                SELECT fecha_inicio, fecha_fin, 'historial' AS tipo, 'finalizado' AS estado,
                    'Cuidado finalizado' AS servicio, m.nombre AS mascota
                FROM historial_cuidados h
                LEFT JOIN mascotas m ON m.id = h.mascota_id
                WHERE h.cuidador_id = :id

                ORDER BY fecha_inicio DESC
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $usuario['id']]);
            $timelogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if ($timelogs) {
                echo "<ul class='timeline-list'>";
                foreach ($timelogs as $t) {
                    echo "<li class='card p-3 mb-3'>
                        <strong>{$t['servicio']}</strong><br>
                        Mascota: {$t['mascota']}<br>
                        Desde: {$t['fecha_inicio']}<br>
                        Hasta: {$t['fecha_fin']}<br>
                        Estado: <span class='badge bg-primary'>{$t['estado']}</span>
                    </li>";
                }
                echo "</ul>";
            } else {
                echo "<p>No hay eventos aún.</p>";
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

        <section class="perfil-estadisticas mt-5">
            <h2 class="Titulo_Estadisticas">Estadísticas</h2>

            <?php
            $sql = "
                SELECT 
                    COUNT(*) AS total_reservas,
                    SUM(CASE WHEN estado_reserva='confirmada' THEN 1 ELSE 0 END) AS aceptadas,
                    SUM(CASE WHEN estado_reserva='cancelada' THEN 1 ELSE 0 END) AS rechazadas,
                    SUM(precio_final) AS ingresos
                FROM reservas
                WHERE id_cuidador = :id
            ";

            $stmt = $conn->prepare($sql);
            $stmt->execute([':id' => $usuario['id']]);
            $stats = $stmt->fetch(PDO::FETCH_ASSOC);

            echo "<ul class='lista-estadisticas'>
                    <li><strong>Total de reservas recibidas:</strong> {$stats['total_reservas']}</li>
                    <li><strong>Aceptadas:</strong> {$stats['aceptadas']}</li>
                    <li><strong>Rechazadas:</strong> {$stats['rechazadas']}</li>
                    <li><strong>Ingresos totales:</strong> " . number_format($stats['ingresos'], 2) . " €</li>
                </ul>";
            ?>
        </section>
        <?php endif; ?>

        <?php if ($usuario['admin'] == 1): ?>
            <section class="perfil-admin">
                <h2 class="titulo-usuarios">Lista de usuarios registrados</h2>

                <?php
                try {
                    $stmt = $conn->prepare("SELECT id, nombre, email, admin, registro, cuidador FROM usuarios");
                    $stmt->execute();
                    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    if ($usuarios) {
                        echo "<table class='lista-usuarios-admin'>";
                        echo "<thead><tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th>Cuidador</th>
                        <th>Fecha</th>
                        <th colspan='2'>Acciones</th>
                      </tr></thead><tbody>";

                        foreach ($usuarios as $u) {
                            echo "<tr>
                            <td>{$u['id']}</td>
                            <td>" . htmlspecialchars($u['nombre']) . "</td>
                            <td>" . htmlspecialchars($u['email']) . "</td>
                            <td>" . ($u['admin'] ? 'TRUE' : 'FALSE') . "</td>
                            <td>" . ($u['cuidador'] ? 'TRUE' : 'FALSE') . "</td>
                            <td>{$u['registro']}</td>
                            <td><a class='enlace-accion' href='/KaiPets/php/modificar_usuarios.php?id={$u['id']}'>Editar</a></td>
                            <td><a class='enlace-accion' href='/KaiPets/php/ver_historial.php?id={$u['id']}'>Historial</a></td>
                          </tr>";
                        }

                        echo "</tbody></table>";
                    }
                } catch (PDOException $e) {
                    echo "<p class='error-message'>Error al obtener usuarios: " . htmlspecialchars($e->getMessage()) . "</p>";
                }
                ?>
            </section>
        <?php endif; ?>
    </main>
    
    <div class="modal fade" id="modalDetallesReserva" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content" id="modal-detalles-content">
                <div class="modal-header">
                    <h5 class="modal-title">Detalles de la reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Cargando detalles...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
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

    <script>
        function verDetallesReserva(id) {
            fetch('/KaiPets/php/reserva_detalle_modal.php?id=' + id)
                .then(r => r.text())
                .then(html => {
                    document.getElementById('modal-detalles-content').innerHTML = html;

                    new bootstrap.Modal(
                        document.getElementById('modalDetallesReserva')
                    ).show();
                });
        }
    </script>

    <?php include __DIR__ . '/../componentes/footer.php'; ?>
</body>

</html>