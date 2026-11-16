<?php

declare(strict_types=1);

// COMPLETAR con los datos de InfinityFree (panel > MySQL Databases)
$host = 'sqlXXX.infinityfree.com';
$db   = 'if0_XXXXXXXX_automarket';
$user = 'if0_XXXXXXXX';
$pass = 'TU_CONTRASEÑA_DE_VPANEL';

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$db};charset=utf8mb4",
        $user,
        $pass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    http_response_code(500);
    exit('No se pudo conectar con MariaDB/MySQL. Revisá los datos de config/database.php.');
}
