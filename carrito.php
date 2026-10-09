<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/consultas.php';

$carrito = $_SESSION['carrito'] ?? [];
$productos = [];
$total = 0;

if (!empty($carrito)) {
    $ids = array_map('intval', array_keys($carrito));
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ({$placeholders}) ORDER BY id DESC");
    $stmt->execute($ids);
    $productos = $stmt->fetchAll();
}

$titulo = 'Mi carrito';
$activo = 'carrito';
require __DIR__ . '/includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container text-center">
        <div class="display-5 mb-2">🛒</div>
        <h1 class="fw-bold mb-0">Mi carrito</h1>
    </div>
</section>

<section class="container py-5">
    <?php if ($exito = getFlash('exito')): ?><div class="alert alert-success">✅ <?= e($exito) ?></div><?php endif; ?>
    <?php if ($error = getFlash('error')): ?><div class="alert alert-danger">⚠️ <?= e($error) ?></div><?php endif; ?>

    <?php if (empty($productos)): ?>
        <div class="card shadow-sm border-0 text-center p-5">
            <div class="display-3 mb-3">🛒</div>
            <h3>Tu carrito está vacío</h3>
            <p class="text-muted">Agregá un auto o un repuesto para empezar.</p>
            <div class="d-flex justify-content-center flex-wrap gap-2">
                <a class="btn btn-primary" href="<?= url('autos.php') ?>">🚘 Ver autos</a>
                <a class="btn btn-outline-primary" href="<?= url('repuestos.php') ?>">🔧 Ver repuestos</a>
            </div>
        </div>
    <?php else: ?>
        <form method="POST" action="<?= url('actualizar_carrito.php') ?>">
            <div class="table-responsive card shadow-sm border-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Producto</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio</th>
                            <th class="text-end">Subtotal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto):
                            $cantidad = max(1, (int)($carrito[$producto['id']] ?? 1));
                            $cantidad = min($cantidad, (int)$producto['stock']);
                            $subtotal = $cantidad * (float)$producto['precio'];
                            $total += $subtotal;
                        ?>
                            <tr>
                                <td>
                                    <a class="text-decoration-none fw-semibold" href="<?= url('detalle.php?id=' . (int)$producto['id']) ?>">
                                        <?= $producto['tipo'] === 'auto' ? '🚗' : '🔧' ?> <?= e($producto['nombre']) ?>
                                    </a>
                                    <?php if ($producto['tipo'] === 'auto'): ?>
                                        <div class="small text-muted"><?= number_format((int)$producto['kilometros'], 0, ',', '.') ?> km</div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <input class="form-control mx-auto text-center" type="number" name="cantidad[<?= (int)$producto['id'] ?>]" value="<?= $cantidad ?>" min="0" max="<?= (int)$producto['stock'] ?>" style="width: 90px;">
                                </td>
                                <td class="text-end"><?= money($producto['precio']) ?></td>
                                <td class="text-end fw-bold"><?= money($subtotal) ?></td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-outline-danger" href="<?= url('quitar_carrito.php?id=' . (int)$producto['id']) ?>">Quitar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mt-4">
                <div class="d-flex gap-2 flex-wrap">
                    <button class="btn btn-outline-dark" type="submit">Actualizar carrito</button>
                    <a class="btn btn-outline-danger" href="<?= url('vaciar_carrito.php') ?>">Vaciar carrito</a>
                </div>
                <div class="text-md-end">
                    <div class="small text-muted">Total</div>
                    <div class="price-tag fs-2 mb-2"><?= money($total) ?></div>
                    <?php if (estaLogueado()): ?>
                        <a class="btn btn-success btn-lg" href="<?= url('finalizar_compra.php') ?>">✅ Finalizar compra</a>
                    <?php else: ?>
                        <a class="btn btn-primary btn-lg" href="<?= url('login.php?redirect=' . rawurlencode(url('carrito.php'))) ?>">🔐 Iniciar sesión para comprar</a>
                    <?php endif; ?>
                </div>
            </div>
        </form>
    <?php endif; ?>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
