<?php if (empty($productos)): ?>
    <div class="alert alert-light border text-center py-4">
        <div class="fs-1 mb-2">🔎</div>
        <h5>No encontramos productos</h5>
        <p class="mb-0 text-muted">Probá con otra búsqueda.</p>
    </div>
<?php else: ?>
    <div class="row g-4">
        <?php foreach ($productos as $producto): ?>
            <div class="col-12 col-md-6 col-xl-4">
                <article class="card product-card h-100 shadow-sm">
                <div class="producto-imagen">
    <?php $imagenSrc = imagenProducto($producto); ?>
    <?php if ($imagenSrc): ?>
        <img
            src="<?= e($imagenSrc) ?>"
            alt="<?= e($producto['nombre']) ?>"
            class="img-fluid w-100"
            loading="lazy"
        >
    <?php else: ?>
        <span aria-hidden="true"><?= $producto['tipo'] === 'auto' ? '🚗' : '🔧' ?></span>
    <?php endif; ?>
    <span class="producto-tipo"><?= $producto['tipo'] === 'auto' ? 'AUTO' : 'REPUESTO' ?></span>
</div>

                    <div class="card-body d-flex flex-column p-4">
                        <div class="d-flex justify-content-between align-items-start gap-2 mb-2">
                            <h5 class="card-title fw-bold mb-0"><?= e($producto['nombre']) ?></h5>
                            <?php if ($producto['stock'] > 0): ?>
                                <span class="badge text-bg-success">Disponible</span>
                            <?php else: ?>
                                <span class="badge text-bg-secondary">Agotado</span>
                            <?php endif; ?>
                        </div>

                        <p class="card-text text-muted flex-grow-1"><?= e($producto['descripcion']) ?></p>

                        <?php if ($producto['tipo'] === 'auto'): ?>
                            <div class="spec-row mb-2">
                                <span>🏷️ Marca</span>
                                <strong><?= e($producto['marca']) ?></strong>
                            </div>
                            <div class="spec-row mb-2">
                                <span>🚘 Modelo</span>
                                <strong><?= e($producto['modelo']) ?></strong>
                            </div>
                            <div class="spec-row mb-3">
                                <span>📍 Kilómetros</span>
                                <strong><?= number_format((int)$producto['kilometros'], 0, ',', '.') ?> km</strong>
                            </div>
                        <?php else: ?>
                            <div class="spec-row mb-2">
                                <span>🚗 Compatible con</span>
                                <strong><?= e($producto['marca']) ?> <?= e($producto['modelo']) ?></strong>
                            </div>
                            <div class="spec-row mb-3">
                                <span>📦 Stock</span>
                                <strong><?= (int)$producto['stock'] ?> unidad(es)</strong>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-between align-items-center gap-3 mt-auto">
                            <span class="price-tag"><?= money($producto['precio']) ?></span>
                            <a class="btn btn-primary" href="<?= url('detalle.php?id=' . (int)$producto['id']) ?>">Ver detalle</a>
                        </div>
                    </div>
                </article>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
