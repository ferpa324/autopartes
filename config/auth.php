<?php

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function estaLogueado(): bool
{
    return isset($_SESSION['usuario']) && is_array($_SESSION['usuario']);
}

function usuarioActual(): ?array
{
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin(): void
{
    if (!estaLogueado()) {
        setFlash('error', 'Iniciá sesión para continuar.');
        header('Location: ' . url('login.php'));
        exit;
    }
}

function setFlash(string $clave, string $valor): void
{
    $_SESSION['flash'][$clave] = $valor;
}

function getFlash(string $clave, ?string $porDefecto = null): ?string
{
    $valor = $_SESSION['flash'][$clave] ?? $porDefecto;
    unset($_SESSION['flash'][$clave]);
    return $valor;
}

function esAdmin(): bool
{
    return estaLogueado() && (($_SESSION['usuario']['rol'] ?? '') === 'admin');
}

/**
 * Exige que el usuario sea admin. Verifica el rol contra la base de datos
 * en cada llamada (no solo contra la sesión), así que si se le quita el
 * rol a alguien pierde el acceso de inmediato.
 */
function exigirAdmin(PDO $pdo): void
{
    exigirLogin();

    $stmt = $pdo->prepare('SELECT rol FROM usuarios WHERE id = ?');
    $stmt->execute([(int)$_SESSION['usuario']['id']]);
    $rol = (string)$stmt->fetchColumn();

    $_SESSION['usuario']['rol'] = $rol;

    if ($rol !== 'admin') {
        setFlash('error', 'No tenés permisos para entrar al panel de administración.');
        header('Location: ' . url('index.php'));
        exit;
    }
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrfCampo(): string
{
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8') . '">';
}

function verificarCsrf(): void
{
    $enviado = (string)($_POST['csrf'] ?? '');
    if (!hash_equals(csrfToken(), $enviado)) {
        setFlash('error', 'El formulario expiró o no es válido. Probá de nuevo.');
        header('Location: ' . url('admin/index.php'));
        exit;
    }
}
