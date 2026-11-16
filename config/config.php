<?php

declare(strict_types=1);

// Sitio subido a la raíz de htdocs => ''. Si lo subís a una subcarpeta, poné '/nombre_carpeta'.
define('BASE_URL', '');

date_default_timezone_set('America/Argentina/Buenos_Aires');

require_once __DIR__ . '/database.php';
require_once __DIR__ . '/auth.php';

function url(string $ruta = ''): string
{
    $base = rtrim(BASE_URL, '/');
    return $base . '/' . ltrim($ruta, '/');
}

function e(mixed $valor): string
{
    return htmlspecialchars((string)$valor, ENT_QUOTES, 'UTF-8');
}

function money(float|int|string $valor): string
{
    return '$' . number_format((float)$valor, 0, ',', '.');
}

/**
 * Devuelve la URL de la imagen del producto, o null si no tiene imagen
 * o si el archivo no existe en disco (así nunca se ve una imagen rota).
 */
function imagenProducto(array $producto): ?string
{
    $ruta = trim((string)($producto['imagen'] ?? ''));
    if ($ruta === '') {
        return null;
    }

    $ruta = ltrim(str_replace('\\', '/', $ruta), '/');
    if (str_contains($ruta, '..') || !is_file(dirname(__DIR__) . '/' . $ruta)) {
        return null;
    }

    return url($ruta);
}
