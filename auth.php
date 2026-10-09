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
