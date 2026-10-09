<?php
require_once __DIR__ . '/../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . url('login.php'));
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$redirect = trim($_POST['redirect'] ?? '');

$_SESSION['old_login'] = ['email' => $email];

if ($email === '' || $password === '') {
    setFlash('error', 'Completá el email y la contraseña.');
    header('Location: ' . url('login.php'));
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlash('error', 'Ingresá un email válido.');
    header('Location: ' . url('login.php'));
    exit;
}

$stmt = $pdo->prepare('SELECT id, nombre, email, password FROM usuarios WHERE email = ?');
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if (!$usuario || !password_verify($password, $usuario['password'])) {
    setFlash('error', 'Email o contraseña incorrectos.');
    header('Location: ' . url('login.php'));
    exit;
}

session_regenerate_id(true);
$_SESSION['usuario'] = [
    'id' => (int)$usuario['id'],
    'nombre' => $usuario['nombre'],
    'email' => $usuario['email'],
];
unset($_SESSION['old_login']);

if ($redirect !== '' && str_starts_with($redirect, BASE_URL . '/')) {
    header('Location: ' . $redirect);
    exit;
}

header('Location: ' . url('index.php'));
exit;
