<?php

declare(strict_types=1);

$titulo = $titulo ?? 'AutoMarket';
$activo = $activo ?? '';
$carritoCantidad = 0;
if (!empty($_SESSION['carrito']) && is_array($_SESSION['carrito'])) {
    $carritoCantidad = array_sum(array_map('intval', $_SESSION['carrito']));
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AutoMarket: venta de autos y repuestos.">
    <title><?= e($titulo) ?> | AutoMarket</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= url('css/style.css') ?>">
</head>
<body class="d-flex flex-column min-vh-100">

<nav class="navbar navbar-expand-lg navbar-dark navbar-automarket sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center gap-2" href="<?= url('index.php') ?>">
            <span class="brand-icon">🚗</span> AutoMarket
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal" aria-controls="menuPrincipal" aria-expanded="false" aria-label="Abrir menú">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                <li class="nav-item"><a class="nav-link <?= $activo === 'inicio' ? 'active' : '' ?>" href="<?= url('index.php') ?>">Inicio</a></li>
                <li class="nav-item"><a class="nav-link <?= $activo === 'autos' ? 'active' : '' ?>" href="<?= url('autos.php') ?>">🚘 Autos</a></li>
                <li class="nav-item"><a class="nav-link <?= $activo === 'repuestos' ? 'active' : '' ?>" href="<?= url('repuestos.php') ?>">🔧 Repuestos</a></li>
                <li class="nav-item"><a class="nav-link <?= $activo === 'carrito' ? 'active' : '' ?>" href="<?= url('carrito.php') ?>">🛒 Carrito <span class="cart-badge"><?= $carritoCantidad ?></span></a></li>

                <?php if (estaLogueado()): ?>
                    <li class="nav-item"><span class="nav-link user-chip">Hola, <?= e(usuarioActual()['nombre']) ?></span></li>
                    <li class="nav-item"><a class="nav-link" href="<?= url('logout.php') ?>">Salir</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= url('login.php') ?>">Ingresar</a></li>
                    <li class="nav-item"><a class="btn btn-warning btn-sm px-3 ms-lg-2" href="<?= url('registro.php') ?>">Registrarse</a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<main class="flex-grow-1">
