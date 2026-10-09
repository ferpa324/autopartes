<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/consultas.php';

$id = (int)($_GET['id'] ?? 0);
$producto = $id > 0 ? obtenerProducto($pdo, $id) : null;

if (!$producto) {
    setFlash('error', 'El producto que buscás no existe.');
    header('Location: ' . url('index.php'));
    exit;
}

$titulo = $producto['nombre'];
require __DIR__ . '/includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container">
        <a href="javascript:history.back()" class="btn btn-outline-light btn-sm mb-3">← Volver</a>
        <h1 class="fw-bold mb-0"><?= e($producto['nombre']) ?></h1>
    </div>
</section>

<section class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-5">
            <div class="producto-imagen rounded-4 shadow-sm h-100" style="min-height: 320px;">
                <?php if ($producto['tipo'] === 'auto' && !empty($producto['imagen_url'])): ?>
                    <img class="producto-foto" src="<?= e($producto['imagen_url']) ?>" alt="<?= e($producto['nombre']) ?>" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                    <span class="producto-fallback" aria-hidden="true" style="display:none; font-size:8rem;">🚗</span>
                <?php else: ?>
                    <span style="font-size: 8rem;"><?= $producto['tipo'] === 'auto' ? '🚗' : '🔧' ?></span>
                <?php endif; ?>
                <span class="producto-tipo"> <?= $producto['tipo'] === 'auto' ? 'AUTO' : 'REPUESTO' ?> </span>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card form-card h-100">
                <div class="card-body p-4 p-lg-5">
                    <span class="badge text-bg-primary mb-3"><?= $producto['tipo'] === 'auto' ? 'Vehículo' : 'Repuesto' ?></span>
                    <h2 class="fw-bold mb-3"><?= e($producto['nombre']) ?></h2>

                    <?php if ($producto['tipo'] === 'auto'): ?>
                        <div class="spec-row mb-3"><span>🏷️ Marca</span><strong><?= e($producto['marca']) ?></strong></div>
                        <div class="spec-row mb-3"><span>🚘 Modelo</span><strong><?= e($producto['modelo']) ?></strong></div>
                    <?php else: ?>
                        <div class="spec-row mb-3"><span>🚗 Compatible con</span><strong><?= e($producto['marca']) ?> <?= e($producto['modelo']) ?></strong></div>
                    <?php endif; ?>
                    <p class="text-muted fs-5"><?= e($producto['descripcion']) ?></p>

                    <?php if ($producto['tipo'] === 'auto'): ?>
                        <div class="spec-row my-3"><span>📍 Kilómetros</span><strong><?= number_format((int)$producto['kilometros'], 0, ',', '.') ?> km</strong></div>
                    <?php else: ?>
                        <div class="spec-row my-3"><span>📦 Stock</span><strong><?= (int)$producto['stock'] ?> unidad(es)</strong></div>
                    <?php endif; ?>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 my-4">
                        <div>
                            <div class="small text-muted">Precio</div>
                            <div class="price-tag fs-2"><?= money($producto['precio']) ?></div>
                        </div>
                        <?php if ((int)$producto['stock'] > 0): ?>
                            <form method="POST" action="<?= url('agregar_carrito.php') ?>" class="d-flex gap-2 align-items-end">
                                <input type="hidden" name="id" value="<?= (int)$producto['id'] ?>">
                                <div>
                                    <label class="form-label small mb-1">Cantidad</label>
                                    <input class="form-control" type="number" name="cantidad" value="1" min="1" max="<?= (int)$producto['stock'] ?>" style="width: 90px;">
                                </div>
                                <button class="btn btn-success btn-lg" type="submit">🛒 Agregar</button>
                            </form>
                        <?php else: ?>
                            <button class="btn btn-secondary btn-lg" disabled>Sin stock</button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
