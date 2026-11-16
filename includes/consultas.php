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
    $sql = 'SELECT * FROM productos WHERE activo = 1';
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
    $stmt = $pdo->prepare('SELECT DISTINCT marca FROM productos WHERE tipo = ? AND activo = 1 ORDER BY marca ASC');
    $stmt->execute([$tipo]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function obtenerModelosPorMarca(PDO $pdo, string $tipo, string $marca): array
{
    $stmt = $pdo->prepare('SELECT DISTINCT modelo FROM productos WHERE tipo = ? AND marca = ? AND activo = 1 ORDER BY modelo ASC');
    $stmt->execute([$tipo, $marca]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function productosDestacados(PDO $pdo, string $tipo, int $cantidad = 3): array
{
    $cantidad = max(1, min($cantidad, 12));
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE tipo = ? AND stock > 0 AND activo = 1 ORDER BY id DESC LIMIT {$cantidad}");
    $stmt->execute([$tipo]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * $soloActivos = false permite ver publicaciones ocultas (uso del admin).
 */
function obtenerProducto(PDO $pdo, int $id, bool $soloActivos = true): ?array
{
    $sql = 'SELECT * FROM productos WHERE id = ?';
    if ($soloActivos) {
        $sql .= ' AND activo = 1';
    }
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);
    return $producto ?: null;
}

/* ------------------------- Panel de administración ------------------------- */

/**
 * Lista TODAS las publicaciones (activas y ocultas) con filtros opcionales.
 * $estado: '' (todas) | 'publicado' | 'oculto' | 'sin_stock'
 */
function listarProductosAdmin(PDO $pdo, string $buscar = '', string $tipo = '', string $estado = ''): array
{
    $sql = 'SELECT * FROM productos WHERE 1=1';
    $params = [];

    if (in_array($tipo, ['auto', 'repuesto'], true)) {
        $sql .= ' AND tipo = ?';
        $params[] = $tipo;
    }

    if ($estado === 'publicado') {
        $sql .= ' AND activo = 1';
    } elseif ($estado === 'oculto') {
        $sql .= ' AND activo = 0';
    } elseif ($estado === 'sin_stock') {
        $sql .= ' AND stock <= 0';
    }

    if ($buscar !== '') {
        $sql .= ' AND (nombre LIKE ? OR marca LIKE ? OR modelo LIKE ?)';
        $valor = '%' . $buscar . '%';
        $params[] = $valor;
        $params[] = $valor;
        $params[] = $valor;
    }

    $sql .= ' ORDER BY id DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function estadisticasAdmin(PDO $pdo): array
{
    $fila = $pdo->query(
        "SELECT COUNT(*) AS total,
                SUM(tipo = 'auto') AS autos,
                SUM(tipo = 'repuesto') AS repuestos,
                SUM(stock <= 0) AS sin_stock,
                SUM(activo = 0) AS ocultos
         FROM productos"
    )->fetch(PDO::FETCH_ASSOC) ?: [];

    return [
        'total'     => (int)($fila['total'] ?? 0),
        'autos'     => (int)($fila['autos'] ?? 0),
        'repuestos' => (int)($fila['repuestos'] ?? 0),
        'sin_stock' => (int)($fila['sin_stock'] ?? 0),
        'ocultos'   => (int)($fila['ocultos'] ?? 0),
    ];
}
