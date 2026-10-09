<?php

declare(strict_types=1);

function buscarProductos(
    PDO $pdo,
    ?string $tipo,
    string $buscar,
    string $orden,
    string $marca = '',
    string $modelo = ''
): array
{
    $sql = 'SELECT * FROM productos WHERE 1=1';
    $params = [];

    if (in_array($tipo, ['auto', 'repuesto'], true)) {
        $sql .= ' AND tipo = ?';
        $params[] = $tipo;
    }

    if ($marca !== '') {
        $sql .= ' AND marca = ?';
        $params[] = $marca;
    }

    if ($modelo !== '') {
        $sql .= ' AND modelo LIKE ?';
        $params[] = '%' . $modelo . '%';
    }

    if ($buscar !== '') {
        $sql .= ' AND (nombre LIKE ? OR descripcion LIKE ? OR marca LIKE ? OR modelo LIKE ?)';
        $valor = '%' . $buscar . '%';
        $params[] = $valor;
        $params[] = $valor;
        $params[] = $valor;
        $params[] = $valor;
    }

    switch ($orden) {
        case 'precio_asc':
            $sql .= ' ORDER BY precio ASC, id DESC';
            break;
        case 'precio_desc':
            $sql .= ' ORDER BY precio DESC, id DESC';
            break;
        case 'nombre':
            $sql .= ' ORDER BY nombre ASC';
            break;
        case 'kilometros_asc':
            $sql .= " ORDER BY CASE WHEN tipo='auto' THEN kilometros ELSE 2147483647 END ASC, id DESC";
            break;
        default:
            $sql .= ' ORDER BY id DESC';
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerMarcas(PDO $pdo, string $tipo): array
{
    $stmt = $pdo->prepare('SELECT DISTINCT marca FROM productos WHERE tipo = ? ORDER BY marca ASC');
    $stmt->execute([$tipo]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function obtenerModelosPorMarca(PDO $pdo, string $tipo, string $marca): array
{
    $stmt = $pdo->prepare('SELECT DISTINCT modelo FROM productos WHERE tipo = ? AND marca = ? ORDER BY modelo ASC');
    $stmt->execute([$tipo, $marca]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function productosDestacados(PDO $pdo, string $tipo, int $cantidad = 3): array
{
    $cantidad = max(1, min($cantidad, 12));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE tipo = ? AND stock > 0 ORDER BY id DESC LIMIT {$cantidad}");
    $stmt->execute([$tipo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function obtenerProducto(PDO $pdo, int $id): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM productos WHERE id = ?');
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    return $producto ?: null;
}
