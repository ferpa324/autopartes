<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('registro.php'));
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password2 = $_POST['password2'] ?? '';

$_SESSION['old_registro'] = ['nombre' => $nombre, 'email' => $email];

function errorRegistro(string $mensaje): never
{
    setFlash('error', $mensaje);
    header('Location: ' . url('registro.php'));
    exit;
}

if ($nombre === '' || $email === '' || $password === '' || $password2 === '') {
    errorRegistro('Todos los campos son obligatorios.');
}
if (mb_strlen($nombre, 'UTF-8') < 3) {
    errorRegistro('El nombre debe tener al menos 3 caracteres.');
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    errorRegistro('Ingresá un email válido.');
}
if (strlen($password) < 6) {
    errorRegistro('La contraseña debe tener al menos 6 caracteres.');
}
if ($password !== $password2) {
    errorRegistro('Las contraseñas no coinciden.');
}

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
if ($stmt->fetch()) {
    errorRegistro('Ese email ya está registrado.');
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)');
$stmt->execute([$nombre, $email, $hash]);

unset($_SESSION['old_registro']);
setFlash('exito', 'Cuenta creada correctamente. Ya podés iniciar sesión.');
header('Location: ' . url('login.php'));
exit;
