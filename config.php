<?php

declare(strict_types=1);

// Nombre de la carpeta dentro de htdocs.
define('BASE_URL', '/autopartes');

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
