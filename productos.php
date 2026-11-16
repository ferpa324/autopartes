<?php
require_once __DIR__ . '/config/config.php';
$tipo = $_GET['tipo'] ?? 'auto';
header('Location: ' . url($tipo === 'repuesto' ? 'repuestos.php' : 'autos.php'));
exit;
