<?php
require_once __DIR__ . '/config/config.php';
exigirLogin();

$carrito = $_SESSION['carrito'] ?? [];
if (empty($carrito)) {
    setFlash('error', 'El carrito está vacío.');
    header('Location: ' . url('carrito.php'));
    exit;
}

$ids = array_map('intval', array_keys($carrito));
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$pdo->beginTransaction();
try {
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id IN ({$placeholders}) FOR UPDATE");
    $stmt->execute($ids);
    $productos = $stmt->fetchAll();

    if (count($productos) !== count($ids)) {
        throw new RuntimeException('Uno de los productos ya no existe.');
    }

    $total = 0.0;
    foreach ($productos as $producto) {
        $id = (int)$producto['id'];
        $cantidad = (int)($carrito[$id] ?? 0);
        if ($cantidad < 1) {
            throw new RuntimeException('Cantidad inválida para ' . $producto['nombre']);
        }
        if ($cantidad > (int)$producto['stock']) {
            throw new RuntimeException('No hay stock suficiente de ' . $producto['nombre']);
        }
        $total += $cantidad * (float)$producto['precio'];
    }

    $stmt = $pdo->prepare('INSERT INTO pedidos (usuario_id, total, estado) VALUES (?, ?, ?)');
    $stmt->execute([(int)$_SESSION['usuario']['id'], $total, 'confirmado']);
    $pedidoId = (int)$pdo->lastInsertId();

    foreach ($productos as $producto) {
        $id = (int)$producto['id'];
        $cantidad = (int)$carrito[$id];

        $stmt = $pdo->prepare('INSERT INTO pedido_detalles (pedido_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)');
        $stmt->execute([$pedidoId, $id, $cantidad, $producto['precio']]);

        $stmt = $pdo->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');
        $stmt->execute([$cantidad, $id]);
    }

    $pdo->commit();
    $_SESSION['carrito'] = [];
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    setFlash('error', 'No se pudo finalizar la compra: ' . $e->getMessage());
    header('Location: ' . url('carrito.php'));
    exit;
}

$titulo = 'Compra realizada';
require __DIR__ . '/includes/header.php';
?>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card form-card text-center">
                <div class="card-body p-5">
                    <div class="display-2 mb-3">✅</div>
                    <h1 class="fw-bold">¡Compra realizada!</h1>
                    <p class="text-muted">Tu pedido fue registrado correctamente.</p>
                    <div class="alert alert-light border my-4">
                        Pedido <strong>#<?= $pedidoId ?></strong><br>
                        Total <strong class="text-primary"><?= money($total) ?></strong>
                    </div>
                    <a class="btn btn-primary btn-lg" href="<?= url('autos.php') ?>">Seguir comprando</a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
