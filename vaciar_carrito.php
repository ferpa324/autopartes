<?php
require_once __DIR__ . '/config/config.php';
$_SESSION['carrito'] = [];
header('Location: ' . url('carrito.php'));
exit;
