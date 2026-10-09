</main>

<footer class="footer-automarket text-white mt-auto py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-6">
                <h5 class="fw-bold">🚗 AutoMarket</h5>
                <p class="mb-0 text-white-50">Venta de autos y repuestos. Proyecto educativo con PHP, Bootstrap y MariaDB.</p>
            </div>
            <div class="col-md-3">
                <h6>Accesos</h6>
                <a href="<?= url('autos.php') ?>">Autos</a>
                <a href="<?= url('repuestos.php') ?>">Repuestos</a>
                <a href="<?= url('carrito.php') ?>">Carrito</a>
            </div>
            <div class="col-md-3">
                <h6>Cuenta</h6>
                <?php if (estaLogueado()): ?>
                    <a href="<?= url('logout.php') ?>">Cerrar sesión</a>
                <?php else: ?>
                    <a href="<?= url('login.php') ?>">Ingresar</a>
                    <a href="<?= url('registro.php') ?>">Registrarse</a>
                <?php endif; ?>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2 text-white-50 small">
            <span>© 2026 AutoMarket</span>
            <span>E-commerce educativo</span>
        </div>
    </div>
</footer>

<?php require __DIR__ . '/chatbot.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
