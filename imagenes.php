<?php

/**
 * Valida y guarda una imagen subida desde el panel admin.
 * Devuelve la ruta relativa (ej: img/autos/fiat-cronos-1a2b3c4d.jpg).
 * Lanza RuntimeException con un mensaje listo para mostrar si algo falla.
 */
function guardarImagenSubida(array $archivo, string $tipo, string $nombre): string
{
    $codigo = (int)($archivo['error'] ?? UPLOAD_ERR_NO_FILE);
    if ($codigo !== UPLOAD_ERR_OK) {
        if ($codigo === UPLOAD_ERR_INI_SIZE || $codigo === UPLOAD_ERR_FORM_SIZE) {
            throw new RuntimeException('La imagen es demasiado grande (máximo 5 MB).');
        }
        throw new RuntimeException('No se pudo subir la imagen (código ' . $codigo . ').');
    }

    if ((int)$archivo['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('La imagen es demasiado grande (máximo 5 MB).');
    }

    $info = @getimagesize($archivo['tmp_name']);
    $permitidos = [
        IMAGETYPE_JPEG => 'jpg',
        IMAGETYPE_PNG  => 'png',
        IMAGETYPE_WEBP => 'webp',
    ];
    if ($info === false || !isset($permitidos[$info[2]])) {
        throw new RuntimeException('El archivo debe ser una imagen JPG, PNG o WEBP.');
    }

    $carpeta = $tipo === 'auto' ? 'img/autos' : 'img/repuestos';
    $directorio = dirname(__DIR__) . '/' . $carpeta;
    if (!is_dir($directorio) && !mkdir($directorio, 0775, true)) {
        throw new RuntimeException('No se pudo crear la carpeta de imágenes.');
    }

    $ascii = @iconv('UTF-8', 'ASCII//TRANSLIT', $nombre);
    if ($ascii === false || $ascii === '') {
        $ascii = $nombre;
    }
    $slug = strtolower(trim((string)preg_replace('/[^a-zA-Z0-9]+/', '-', $ascii), '-'));
    $slug = substr($slug, 0, 60);
    if ($slug === '') {
        $slug = 'producto';
    }

    // El sufijo aleatorio de 8 hex distingue los archivos subidos desde el
    // panel de las fotos originales del proyecto (que nunca se borran solas).
    $nombreArchivo = $slug . '-' . bin2hex(random_bytes(4)) . '.' . $permitidos[$info[2]];

    if (!move_uploaded_file($archivo['tmp_name'], $directorio . '/' . $nombreArchivo)) {
        throw new RuntimeException('No se pudo guardar la imagen en el servidor.');
    }

    return $carpeta . '/' . $nombreArchivo;
}

/**
 * Borra del disco una imagen subida desde el panel, siempre que ningún
 * otro producto la use. Las fotos originales del proyecto no se tocan.
 */
function borrarImagenSiHuerfana(PDO $pdo, ?string $ruta): void
{
    $ruta = trim((string)$ruta);
    if (!preg_match('#^img/(autos|repuestos)/[a-z0-9-]+-[0-9a-f]{8}\.(jpg|png|webp)$#', $ruta)) {
        return;
    }

    $stmt = $pdo->prepare('SELECT COUNT(*) FROM productos WHERE imagen = ?');
    $stmt->execute([$ruta]);
    if ((int)$stmt->fetchColumn() > 0) {
        return;
    }

    $archivo = dirname(__DIR__) . '/' . $ruta;
    if (is_file($archivo)) {
        @unlink($archivo);
    }
}
