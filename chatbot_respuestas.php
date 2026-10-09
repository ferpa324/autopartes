<?php

declare(strict_types=1);

function normalizarTexto(string $texto): string
{
    $texto = mb_strtolower(trim($texto), 'UTF-8');
    return strtr($texto, [
        'á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ü'=>'u','ñ'=>'n'
    ]);
}

function contieneAlguno(string $texto, array $palabras): bool
{
    foreach ($palabras as $palabra) {
        if (str_contains($texto, $palabra)) {
            return true;
        }
    }
    return false;
}

function enlacesRapidos(): array
{
    return [
        ['texto' => '🚘 Ver autos', 'url' => url('autos.php')],
        ['texto' => '🔧 Ver repuestos', 'url' => url('repuestos.php')],
    ];
}

function buscarProductosPorTexto(PDO $pdo, string $texto): array
{
    $limpios = preg_replace('/[^a-z0-9 ]+/u', ' ', $texto) ?? '';
    $tokens = preg_split('/\s+/', trim($limpios), -1, PREG_SPLIT_NO_EMPTY) ?: [];

    $ignorar = [
        'hola','buenas','buen','dia','dias','tarde','tardes','noche','noches','que','como','cual','cuales',
        'cuanto','cuesta','sale','precio','precios','tenes','tienen','tiene','hay','quiero','busco','necesito',
        'dame','mostrame','mostrar','autos','auto','vehiculo','vehiculos','repuesto','repuestos','accesorio',
        'accesorios','pieza','piezas','stock','disponible','disponibles','carrito','comprar','compra','para',
        'una','uno','unos','unas','los','las','del','con','por','favor','informacion','info','gracias','este','esta',
        'ese','esa','tenes','tienes','me','mi','un','el','la'
    ];

    $tokens = array_values(array_unique(array_filter($tokens, static function ($token) use ($ignorar): bool {
        return strlen($token) >= 3 && !in_array($token, $ignorar, true);
    })));

    if (!$tokens) {
        return [];
    }

    $tokens = array_slice($tokens, 0, 6);
    $condiciones = [];
    $params = [];

    foreach ($tokens as $token) {
        $condiciones[] = '(nombre LIKE ? OR descripcion LIKE ? OR marca LIKE ? OR modelo LIKE ?)';
        $like = '%' . $token . '%';
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
        $params[] = $like;
    }

    $sql = 'SELECT id, nombre, descripcion, precio, stock, tipo, marca, modelo, kilometros
            FROM productos
            WHERE ' . implode(' OR ', $condiciones) . '
            ORDER BY CASE WHEN stock > 0 THEN 0 ELSE 1 END, id DESC
            LIMIT 4';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

function respuestaProductos(array $productos): array
{
    $lineas = [];
    $enlaces = [];

    foreach ($productos as $producto) {
        $extra = $producto['tipo'] === 'auto'
            ? number_format((int)$producto['kilometros'], 0, ',', '.') . ' km'
            : 'stock: ' . (int)$producto['stock'];

        $estado = ((int)$producto['stock'] > 0) ? $extra : 'sin stock';
        
        $compatibilidad = '';
        if ($producto['tipo'] === 'repuesto' && !empty($producto['marca']) && $producto['marca'] !== 'Multimarca') {
            $compatibilidad = ' — compatible con ' . $producto['marca'] . ' ' . $producto['modelo'];
        }
        $lineas[] = '• ' . $producto['nombre'] . $compatibilidad . ' — ' . money($producto['precio']) . ' (' . $estado . ')';
        $enlaces[] = [
            'texto' => 'Ver ' . $producto['nombre'],
            'url' => url('detalle.php?id=' . (int)$producto['id'])
        ];
    }

    return [
        'texto' => "Encontré estas opciones:\n" . implode("\n", $lineas),
        'enlaces' => $enlaces
    ];
}

function generarRespuesta(PDO $pdo, string $mensaje): array
{
    $t = normalizarTexto($mensaje);

    if ($t === '') {
        return ['texto' => 'Escribime algo y te ayudo 😊', 'enlaces' => []];
    }

    if (contieneAlguno($t, ['hola', 'buenas', 'buen dia', 'buenos dias', 'buenas tardes', 'buenas noches'])) {
        return [
            'texto' => '¡Hola! 👋 Soy el asistente de AutoMarket. Puedo ayudarte a buscar autos o repuestos, consultar precios y explicarte cómo comprar.',
            'enlaces' => enlacesRapidos()
        ];
    }

    if (contieneAlguno($t, ['gracias', 'genial', 'perfecto'])) {
        return ['texto' => '¡De nada! 😊', 'enlaces' => []];
    }

    if (contieneAlguno($t, ['chau', 'adios', 'hasta luego'])) {
        return ['texto' => '¡Chau! 👋 Cuando vuelvas, acá voy a estar.', 'enlaces' => []];
    }

    if (contieneAlguno($t, ['como compro', 'como comprar', 'comprar', 'carrito', 'finalizar compra', 'pagar', 'pago'])) {
        return [
            'texto' => 'Para comprar: 1) elegí un producto, 2) abrí su detalle, 3) elegí la cantidad y agregalo al carrito, 4) iniciá sesión y 5) finalizá la compra.',
            'enlaces' => [['texto' => '🛒 Ir al carrito', 'url' => url('carrito.php')]]
        ];
    }

    if (contieneAlguno($t, ['registrarme', 'registrar', 'crear cuenta', 'registro', 'login', 'iniciar sesion', 'ingresar', 'cuenta'])) {
        if (estaLogueado()) {
            return ['texto' => 'Ya tenés la sesión iniciada como ' . usuarioActual()['nombre'] . ' 👍', 'enlaces' => []];
        }
        return [
            'texto' => 'Podés crear una cuenta desde Registrarse. Para finalizar una compra necesitás iniciar sesión.',
            'enlaces' => [
                ['texto' => '🔐 Ingresar', 'url' => url('login.php')],
                ['texto' => '📝 Registrarse', 'url' => url('registro.php')]
            ]
        ];
    }

    // Primero buscamos productos concretos para que preguntas como
    // "precio del Corolla" o "tenés batería" devuelvan el producto real.
    $productos = buscarProductosPorTexto($pdo, $t);
    if ($productos) {
        return respuestaProductos($productos);
    }

    if (contieneAlguno($t, ['autos', 'auto', 'vehiculo', 'vehiculos', 'coche', 'camioneta'])) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM productos WHERE tipo='auto' AND stock > 0");
        $n = (int)$stmt->fetchColumn();
        return [
            'texto' => "Tenemos {$n} vehículo(s) disponible(s). Podés ver modelo, precio y kilómetros.",
            'enlaces' => [['texto' => '🚘 Ver autos', 'url' => url('autos.php')]]
        ];
    }

    if (contieneAlguno($t, ['repuesto', 'repuestos', 'accesorio', 'accesorios', 'pieza', 'piezas'])) {
        $stmt = $pdo->query("SELECT COUNT(*) FROM productos WHERE tipo='repuesto' AND stock > 0");
        $n = (int)$stmt->fetchColumn();
        return [
            'texto' => "Tenemos {$n} repuesto(s) disponible(s). Decime qué pieza buscás y reviso el catálogo.",
            'enlaces' => [['texto' => '🔧 Ver repuestos', 'url' => url('repuestos.php')]]
        ];
    }

    return [
        'texto' => 'No encontré una respuesta para eso. Probá con “precio del Corolla”, “tenés batería?”, “¿qué autos tienen?” o “¿cómo compro?”.',
        'enlaces' => enlacesRapidos()
    ];
}
