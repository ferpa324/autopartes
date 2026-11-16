<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/imagenes.php';

exigirAdmin($pdo);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('admin/index.php'));
    exit;
}

verificarCsrf();

// Vuelve a la misma lista (con sus filtros) si la URL es del panel.
$volver = (string)($_POST['volver'] ?? '');
if (!str_starts_with($volver, BASE_URL . '/admin/') || preg_match('/[\r\n]/', $volver)) {
    $volver = url('admin/index.php');
}

$accion = (string)($_POST['accion'] ?? '');
$id = (int)($_POST['id'] ?? 0);

$stmt = $pdo->prepare('SELECT * FROM productos WHERE id = ?');
$stmt->execute([$id]);
$producto = $stmt->fetch();

if (!$producto) {
    setFlash('error', 'La publicación no existe.');
    header('Location: ' . $volver);
    exit;
}

switch ($accion) {
    case 'restock':
        $cantidad = filter_var($_POST['cantidad'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 100000]]);
        if ($cantidad === false) {
            setFlash('error', 'Ingresá una cantidad válida (entre 1 y 100000).');
            break;
        }
        $stmt = $pdo->prepare('UPDATE productos SET stock = stock + ? WHERE id = ?');
        $stmt->execute([$cantidad, $id]);
        $nuevo = (int)$producto['stock'] + $cantidad;
        setFlash('exito', "Stock repuesto: «{$producto['nombre']}» ahora tiene {$nuevo} unidad(es).");
        break;

    case 'toggle':
        $nuevoEstado = (int)$producto['activo'] === 1 ? 0 : 1;
        $stmt = $pdo->prepare('UPDATE productos SET activo = ? WHERE id = ?');
        $stmt->execute([$nuevoEstado, $id]);
        setFlash('exito', $nuevoEstado === 1
            ? "«{$producto['nombre']}» fue publicada."
            : "«{$producto['nombre']}» fue ocultada de la tienda.");
        break;

    case 'eliminar':
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM pedido_detalles WHERE producto_id = ?');
        $stmt->execute([$id]);
        if ((int)$stmt->fetchColumn() > 0) {
            setFlash('error', 'No se puede eliminar «' . $producto['nombre'] . '» porque ya figura en pedidos. Ocultala en su lugar.');
            break;
        }
        $stmt = $pdo->prepare('DELETE FROM productos WHERE id = ?');
        $stmt->execute([$id]);
        borrarImagenSiHuerfana($pdo, $producto['imagen']);
        setFlash('exito', "«{$producto['nombre']}» fue eliminada.");
        break;

    default:
        setFlash('error', 'Acción no reconocida.');
}

header('Location: ' . $volver);
exit;
