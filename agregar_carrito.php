<?php
require_once __DIR__ . '/config/config.php';

$id = (int)($_POST['id'] ?? $_GET['id'] ?? 0);
$cantidad = max(1, (int)($_POST['cantidad'] ?? $_GET['cantidad'] ?? 1));

if ($id > 0) {
    $stmt = $pdo->prepare('SELECT id, stock, tipo FROM productos WHERE id = ?');
    $stmt->execute([$id]);
    $producto = $stmt->fetch();

    if ($producto && (int)$producto['stock'] > 0) {
        if (!isset($_SESSION['carrito']) || !is_array($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $actual = (int)($_SESSION['carrito'][$id] ?? 0);
        $maximo = (int)$producto['stock'];
        $_SESSION['carrito'][$id] = min($actual + $cantidad, $maximo);
        setFlash('exito', 'Producto agregado al carrito.');
    } else {
        setFlash('error', 'El producto no tiene stock disponible.');
    }
}

header('Location: ' . url('carrito.php'));
exit;
