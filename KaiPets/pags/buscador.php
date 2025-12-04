<?php
session_start();
require_once __DIR__ . '/../php/conn_db.php';

// ===========================
// Obtener ciudades
// ===========================
$ciudades_raw = $conn->query("SELECT * FROM ciudades ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

$ciudades = [];
foreach ($ciudades_raw as $c) {
    $ciudades[$c['id']] = $c;
}

// ===========================
// Procesar búsqueda
// ===========================
$texto = $_GET['q'] ?? '';
$ciudad_id = $_GET['ciudad_id'] ?? '';
$servicio_id = $_GET['servicio_id'] ?? '';
$precio_max = $_GET['precio_max'] ?? '';

// Servicios
$servicios = $conn->query("SELECT * FROM servicios ORDER BY nombre")->fetchAll(PDO::FETCH_ASSOC);

// Construir consulta dinámica
$sql = "
    SELECT c.id AS cuidador_id, c.descripcion, c.experiencia, c.barrio, c.foto_perfil,
           u.nombre, u.apellido, u.telefono,
           (SELECT AVG(puntuacion) FROM opiniones o WHERE o.cuidador_id = c.id) AS media
    FROM cuidadores c
    JOIN usuarios u ON u.id = c.usuario_id
    WHERE 1=1
";

$params = [];

if ($texto !== '') {
    $sql .= " AND (u.nombre LIKE :q OR u.apellido LIKE :q OR c.descripcion LIKE :q OR c.barrio LIKE :q)";
    $params[':q'] = "%$texto%";
}

if ($ciudad_id !== '') {
    $sql .= " AND c.ciudad_id = :cid";
    $params[':cid'] = $ciudad_id;
}

if ($servicio_id !== '') {
    $sql .= " AND c.id IN (
        SELECT cuidador_id FROM cuidador_servicio WHERE servicio_id = :sid
    )";
    $params[':sid'] = $servicio_id;
}

if ($precio_max !== '') {
    $sql .= " AND c.id IN (
        SELECT cuidador_id FROM cuidador_servicio WHERE precio <= :pmax
    )";
    $params[':pmax'] = $precio_max;
}

$sql .= " ORDER BY u.nombre ASC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscador de Cuidadores</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/KaiPets/src/css/style.css">
    <link rel="stylesheet" href="/KaiPets/src/css/buscador.css">
</head>
<body>

<?php include __DIR__ . '/../componentes/header.php'; ?>

<div class="container py-4">

    <h2 class="mb-4">Buscar cuidadores</h2>

    <!-- =========================
         FORMULARIO DE BÚSQUEDA
         ========================= -->
    <form class="buscador-card mb-4" method="GET" action="buscador.php">

        <div class="row g-3">

            <div class="col-md-4">
                <label class="form-label">Nombre / Barrio / Descripción</label>
                <input type="text" name="q" value="<?= htmlspecialchars($texto) ?>" class="form-control" placeholder="Ej: Juan, centro, paseos…">
            </div>

            <div class="col-md-3">
                <label class="form-label">Ciudad</label>
                <select name="ciudad_id" class="form-select">
                    <option value="">Todas</option>
                    <?php foreach ($ciudades as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= ($ciudad_id == $c['id']) ? "selected" : "" ?>>
                            <?= htmlspecialchars($c['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">Servicio</label>
                <select name="servicio_id" class="form-select">
                    <option value="">Todos</option>
                    <?php foreach ($servicios as $s): ?>
                        <option value="<?= $s['id_servicio'] ?>" <?= ($servicio_id == $s['id_servicio']) ? "selected" : "" ?>>
                            <?= htmlspecialchars($s['nombre']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">Precio máximo (€)</label>
                <input type="number" name="precio_max" value="<?= htmlspecialchars($precio_max) ?>" class="form-control" min="1">
            </div>

        </div>

        <button class="btn btn-kai-search mt-3">Buscar</button>
    </form>

    <!-- =========================
         RESULTADOS
         ========================= -->
    <h3 class="resultados-title">Resultados</h3>

    <?php if (!$resultados): ?>
        <p>No se encontraron cuidadores con esos criterios.</p>

    <?php else: ?>
        <div class="row">
            <?php foreach ($resultados as $c): ?>
                <div class="col-md-4 mb-4">
                    <div class="busqueda-card h-100">

                        <img src="<?= htmlspecialchars($c['foto_perfil'] ?: '/KaiPets/uploads/perfiles/pred_usu.jpg') ?>"
                            class="busqueda-img">

                        <div class="busqueda-body p-3">
                            <h5 class="card-title">
                                <?= htmlspecialchars($c['nombre'] . " " . $c['apellido']) ?>
                            </h5>

                            <p class="card-text">
                                <strong>Barrio:</strong> <?= htmlspecialchars($c['barrio']) ?><br>
                                <strong>Experiencia:</strong> <?= htmlspecialchars($c['experiencia']) ?><br>
                                <strong>Valoración:</strong> <?= $c['media'] ? round($c['media'], 1)." ⭐" : "Sin valoraciones" ?>
                            </p>

                            <a href="/KaiPets/pags/ver_cuidador.php?id=<?= $c['cuidador_id'] ?>" class="btn btn-ver-perfil w-100">
                                Ver perfil
                            </a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<?php include __DIR__ . '/../componentes/footer.php'; ?>

</body>
</html>
