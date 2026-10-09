<?php
require_once __DIR__ . '/config/config.php';

$carrito = $_SESSION['carrito'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];

if (!is_array($cantidades)) {
    header('Location: ' . url('carrito.php'));
    exit;
}

foreach ($cantidades as $id => $cantidad) {
    $id = (int)$id;
    $cantidad = (int)$cantidad;

    if ($id <= 0) {
        continue;
    }

    $stmt = $pdo->prepare('SELECT stock FROM productos WHERE id = ?');
    $stmt->execute([$id]);
    $producto = $stmt->fetch();

    if (!$producto) {
        unset($carrito[$id]);
        continue;
    }

    if ($cantidad <= 0) {
        unset($carrito[$id]);
        continue;
    }

    $carrito[$id] = min($cantidad, (int)$producto['stock']);
}

$_SESSION['carrito'] = $carrito;
setFlash('exito', 'Carrito actualizado.');
header('Location: ' . url('carrito.php'));
exit;
