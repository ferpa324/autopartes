<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/consultas.php';

$buscar = trim($_GET['q'] ?? '');
$orden = $_GET['orden'] ?? 'recientes';
$marca = trim($_GET['marca'] ?? '');
$modelo = trim($_GET['modelo'] ?? '');
$tipo = 'auto';

$productos = buscarProductos($pdo, $tipo, $buscar, $orden, $marca, $modelo);

$titulo = 'Autos';
$activo = 'autos';
require __DIR__ . '/includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container text-center">
        <div class="display-5 mb-2">🚘</div>
        <h1 class="fw-bold mb-1">Autos disponibles</h1>
        <p class="lead mb-0">
            <?= count($productos) ?> vehículo(s) encontrados
        </p>
    </div>
</section>

<section class="container py-5">

    <div class="filter-box p-3 p-md-4 mb-4">
        <div class="d-flex flex-column flex-lg-row justify-content-between gap-2 mb-3">
            <div>
                <span class="text-primary fw-semibold">API DE VEHÍCULOS</span>
                <h2 class="h4 fw-bold mb-1">Buscá por marca y modelo</h2>
                <p class="text-muted small mb-0">
                    Las marcas y modelos se cargan desde la API vPIC de NHTSA.
                </p>
            </div>
            <span id="api-status" class="badge text-bg-secondary align-self-start">Cargando marcas...</span>
        </div>

        <form class="row g-3 align-items-end" method="GET" id="filtroAutos">

            <div class="col-12 col-lg-3">
                <label for="marca" class="form-label fw-semibold">Marca</label>
                <select name="marca" id="marca" class="form-select" disabled>
                    <option value="">Cargando marcas...</option>
                </select>
            </div>

            <div class="col-12 col-lg-3">
                <label for="modelo" class="form-label fw-semibold">Modelo</label>
                <select name="modelo" id="modelo" class="form-select" disabled>
                    <option value="">Seleccioná una marca primero</option>
                </select>
            </div>

            <div class="col-12 col-lg-3">
                <label for="q" class="form-label fw-semibold">Búsqueda adicional</label>
                <input
                    type="search"
                    name="q"
                    id="q"
                    class="form-control"
                    placeholder="Ej.: XEI, Turbo..."
                    value="<?= e($buscar) ?>"
                >
            </div>

            <div class="col-12 col-lg-2">
                <label for="orden" class="form-label fw-semibold">Ordenar</label>
                <select name="orden" id="orden" class="form-select">
                    <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
                    <option value="precio_asc" <?= $orden === 'precio_asc' ? 'selected' : '' ?>>Menor precio</option>
                    <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Mayor precio</option>
                    <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Nombre (A-Z)</option>
                    <option value="kilometros_asc" <?= $orden === 'kilometros_asc' ? 'selected' : '' ?>>Menos kilómetros</option>
                </select>
            </div>

            <div class="col-12 col-lg-1 d-grid">
                <button class="btn btn-primary" type="submit">Buscar</button>
            </div>
        </form>

        <div class="d-flex flex-wrap gap-2 mt-3">
            <?php if ($marca !== '' || $modelo !== '' || $buscar !== '' || $orden !== 'recientes'): ?>
                <a href="<?= url('autos.php') ?>" class="btn btn-outline-secondary btn-sm">
                    Limpiar filtros
                </a>
            <?php endif; ?>
            <span class="small text-muted align-self-center" id="modelo-ayuda"></span>
        </div>
    </div>

    <div class="alert alert-light border d-flex gap-2 align-items-start mb-4">
        <span>ℹ️</span>
        <div class="small">
            La API se usa para completar los selectores de marca y modelo. El catálogo publicado,
            sus precios y sus kilómetros siguen saliendo de la base de datos de AutoMarket.
        </div>
    </div>

    <?php require __DIR__ . '/includes/lista_productos.php'; ?>
</section>

<script src="<?= url('js/vehiculos_api.js') ?>"></script>
<script>
    window.AUTOMARKET_API_FILTER = <?= json_encode([
        'marca' => $marca,
        'modelo' => $modelo,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>

<?php require __DIR__ . '/includes/footer.php'; ?>
