<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/consultas.php';

$autosDestacados = productosDestacados($pdo, 'auto', 3);
$titulo = 'Inicio';
$activo = 'inicio';
require __DIR__ . '/includes/header.php';
?>

<section class="hero-automarket py-5 py-lg-6">
    <div class="container position-relative py-lg-5">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <span class="badge text-bg-warning mb-3">E-COMMERCE AUTOMOTOR</span>
                <h1 class="display-3 fw-bold">Encontrá tu próximo vehículo</h1>
                <p class="lead mb-4">Autos y repuestos en un solo lugar, con búsqueda, carrito y cuenta de usuario.</p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="<?= url('autos.php') ?>" class="btn btn-light btn-lg px-4">🚘 Ver autos</a>
                    <a href="<?= url('repuestos.php') ?>" class="btn btn-outline-light btn-lg px-4">🔧 Ver repuestos</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <span class="text-primary fw-semibold">CATÁLOGO</span>
            <h2 class="fw-bold mb-0">Elegí una categoría</h2>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card category-card h-100 shadow-sm">
                <div class="card-body p-4 p-lg-5 text-center">
                    <div class="icon-big mb-3">🚘</div>
                    <h3 class="fw-bold">Autos</h3>
                    <p class="text-muted">Consultá modelos, precios y cantidad de kilómetros.</p>
                    <a href="<?= url('autos.php') ?>" class="btn btn-primary px-4">Ver autos</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card category-card h-100 shadow-sm">
                <div class="card-body p-4 p-lg-5 text-center">
                    <div class="icon-big mb-3">🔧</div>
                    <h3 class="fw-bold">Repuestos</h3>
                    <p class="text-muted">Buscá piezas y accesorios y revisá el stock disponible.</p>
                    <a href="<?= url('repuestos.php') ?>" class="btn btn-primary px-4">Ver repuestos</a>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container pb-5">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end gap-2 mb-4">
        <div>
            <span class="text-primary fw-semibold">DESTACADOS</span>
            <h2 class="fw-bold mb-0">Autos disponibles</h2>
        </div>
        <a href="<?= url('autos.php') ?>" class="btn btn-outline-primary">Ver catálogo completo</a>
    </div>

    <?php $productos = $autosDestacados; require __DIR__ . '/includes/lista_productos.php'; ?>
</section>

<section class="bg-white border-top border-bottom py-5">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-4">
                <div class="info-card p-3 h-100">
                    <div class="fs-1 mb-2">🔎</div>
                    <h5 class="fw-bold">Buscá fácil</h5>
                    <p class="text-muted mb-0">Filtrá por nombre y ordená por precio o kilómetros.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card p-3 h-100">
                    <div class="fs-1 mb-2">🛒</div>
                    <h5 class="fw-bold">Armá tu carrito</h5>
                    <p class="text-muted mb-0">Agregá productos y revisá cantidades antes de comprar.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-card p-3 h-100">
                    <div class="fs-1 mb-2">💬</div>
                    <h5 class="fw-bold">Asistente</h5>
                    <p class="text-muted mb-0">Usá el chat para encontrar productos y resolver dudas.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
