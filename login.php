<?php
require_once __DIR__ . '/config/config.php';

if (estaLogueado()) {
    header('Location: ' . url('index.php'));
    exit;
}

$error = getFlash('error');
$exito = getFlash('exito');
$old = $_SESSION['old_login'] ?? [];
unset($_SESSION['old_login']);
$redirect = trim($_GET['redirect'] ?? '');

$titulo = 'Ingresar';
require __DIR__ . '/includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container text-center">
        <div class="display-6 mb-2">🔐</div>
        <h1 class="fw-bold mb-0">Iniciar sesión</h1>
    </div>
</section>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5">
            <div class="card form-card">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="fw-bold mb-1">Bienvenido</h2>
                    <p class="text-muted mb-4">Ingresá para finalizar tus compras.</p>

                    <?php if ($exito): ?><div class="alert alert-success">✅ <?= e($exito) ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= e($error) ?></div><?php endif; ?>

                    <form method="POST" action="<?= url('backend/login_backend.php') ?>">
                        <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" value="<?= e($old['email'] ?? '') ?>" required autofocus>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <input type="password" name="password" class="form-control form-control-lg" required>
                        </div>
                        <button class="btn btn-primary btn-lg w-100" type="submit">Ingresar</button>
                    </form>
                    <hr class="my-4">
                    <p class="text-center mb-0">¿No tenés cuenta? <a href="<?= url('registro.php') ?>">Registrate</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
