<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/consultas.php';

$buscar = trim($_GET['q'] ?? '');
$orden = $_GET['orden'] ?? 'recientes';
$marca = trim($_GET['marca'] ?? '');
$modelo = trim($_GET['modelo'] ?? '');
$tipo = 'repuesto';

$marcas = obtenerMarcas($pdo, $tipo);
$modelos = $marca !== '' ? obtenerModelosPorMarca($pdo, $tipo, $marca) : [];
$productos = buscarProductos($pdo, $tipo, $buscar, $orden, $marca, $modelo);

$titulo = 'Repuestos';
$activo = 'repuestos';
require __DIR__ . '/includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container text-center">
        <div class="display-5 mb-2">🔧</div>
        <h1 class="fw-bold mb-1">Repuestos por marca y modelo</h1>
        <p class="lead mb-0"><?= count($productos) ?> repuesto(s) encontrados</p>
    </div>
</section>

<section class="container py-5">

    <div class="filter-box p-3 p-md-4 mb-4">
        <div class="mb-3">
            <span class="text-primary fw-semibold">CATÁLOGO DE REPUESTOS</span>
            <h2 class="h4 fw-bold mb-1">Elegí la marca y el modelo de tu vehículo</h2>
            <p class="text-muted small mb-0">
                Los repuestos específicos están asociados a una marca y un modelo para que encuentres la pieza correcta.
            </p>
        </div>

        <form class="row g-3 align-items-end" method="GET">
            <div class="col-12 col-md-3">
                <label for="marca" class="form-label fw-semibold">Marca</label>
                <select name="marca" id="marca" class="form-select" onchange="this.form.submit()">
                    <option value="">Todas las marcas</option>
                    <?php foreach ($marcas as $nombreMarca): ?>
                        <option value="<?= e($nombreMarca) ?>" <?= $marca === $nombreMarca ? 'selected' : '' ?>>
                            <?= e($nombreMarca) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-3">
                <label for="modelo" class="form-label fw-semibold">Modelo</label>
                <select name="modelo" id="modelo" class="form-select" <?= $marca === '' ? 'disabled' : '' ?> onchange="this.form.submit()">
                    <option value="">Todos los modelos</option>
                    <?php foreach ($modelos as $nombreModelo): ?>
                        <option value="<?= e($nombreModelo) ?>" <?= $modelo === $nombreModelo ? 'selected' : '' ?>>
                            <?= e($nombreModelo) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-12 col-md-4">
                <label for="q" class="form-label fw-semibold">Buscar repuesto</label>
                <input type="search" name="q" id="q" class="form-control" placeholder="Ej.: cremallera, pastillas..." value="<?= e($buscar) ?>">
            </div>

            <div class="col-12 col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>

        <?php if ($marca !== '' || $modelo !== '' || $buscar !== '' || $orden !== 'recientes'): ?>
            <div class="mt-3">
                <a href="<?= url('repuestos.php') ?>" class="btn btn-outline-secondary btn-sm">Limpiar filtros</a>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($marca !== '' || $modelo !== ''): ?>
        <div class="alert alert-primary border-0 shadow-sm">
            <strong>Filtro activo:</strong>
            <?= e($marca !== '' ? $marca : '') ?>
            <?php if ($modelo !== ''): ?>
                <?= e($modelo) ?>
            <?php endif; ?>
            — se muestran únicamente los repuestos compatibles con esa selección.
        </div>
    <?php endif; ?>

    <?php require __DIR__ . '/includes/lista_productos.php'; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
