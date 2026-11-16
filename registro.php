<?php
require_once __DIR__ . '/config/config.php';

if (estaLogueado()) {
    header('Location: ' . url('index.php'));
    exit;
}

$error = getFlash('error');
$exito = getFlash('exito');
$old = $_SESSION['old_registro'] ?? [];
unset($_SESSION['old_registro']);

$titulo = 'Crear cuenta';
require __DIR__ . '/includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container text-center">
        <div class="display-6 mb-2">📝</div>
        <h1 class="fw-bold mb-0">Crear cuenta</h1>
    </div>
</section>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-7 col-lg-5">
            <div class="card form-card">
                <div class="card-body p-4 p-lg-5">
                    <h2 class="fw-bold mb-1">Registrate</h2>
                    <p class="text-muted mb-4">Creá tu cuenta para poder comprar.</p>

                    <?php if ($exito): ?><div class="alert alert-success">✅ <?= e($exito) ?></div><?php endif; ?>
                    <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= e($error) ?></div><?php endif; ?>

                    <form method="POST" action="<?= url('backend/registro_backend.php') ?>">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nombre</label>
                            <input type="text" name="nombre" class="form-control form-control-lg" value="<?= e($old['nombre'] ?? '') ?>" minlength="3" maxlength="100" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control form-control-lg" value="<?= e($old['email'] ?? '') ?>" maxlength="150" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Contraseña</label>
                            <input type="password" name="password" class="form-control form-control-lg" minlength="6" required>
                            <div class="form-text">Mínimo 6 caracteres.</div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Repetir contraseña</label>
                            <input type="password" name="password2" class="form-control form-control-lg" minlength="6" required>
                        </div>
                        <button class="btn btn-primary btn-lg w-100" type="submit">Crear cuenta</button>
                    </form>
                    <hr class="my-4">
                    <p class="text-center mb-0">¿Ya tenés cuenta? <a href="<?= url('login.php') ?>">Ingresá</a></p>
                </div>
            </div>
        </div>
    </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
