<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/consultas.php';

exigirAdmin($pdo);

$buscar = trim((string)($_GET['q'] ?? ''));
$tipo = (string)($_GET['tipo'] ?? '');
$estado = (string)($_GET['estado'] ?? '');

$productos = listarProductosAdmin($pdo, $buscar, $tipo, $estado);
$stats = estadisticasAdmin($pdo);

$exito = getFlash('exito');
$error = getFlash('error');
$volver = $_SERVER['REQUEST_URI'] ?? url('admin/index.php');

$titulo = 'Panel de administración';
$activo = 'admin';
require __DIR__ . '/../includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <h1 class="fw-bold mb-1">⚙️ Panel de administración</h1>
            <p class="lead mb-0">Publicá, modificá y reponé stock de tus productos.</p>
        </div>
        <a href="<?= url('admin/producto.php') ?>" class="btn btn-warning btn-lg">➕ Nueva publicación</a>
    </div>
</section>

<section class="container py-4">

    <?php if ($exito): ?><div class="alert alert-success">✅ <?= e($exito) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger">⚠️ <?= e($error) ?></div><?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-6 col-lg"><div class="card admin-stat shadow-sm"><div class="card-body"><div class="small text-muted">Publicaciones</div><div class="fs-3 fw-bold"><?= $stats['total'] ?></div></div></div></div>
        <div class="col-6 col-lg"><div class="card admin-stat shadow-sm"><div class="card-body"><div class="small text-muted">🚘 Autos</div><div class="fs-3 fw-bold"><?= $stats['autos'] ?></div></div></div></div>
        <div class="col-6 col-lg"><div class="card admin-stat shadow-sm"><div class="card-body"><div class="small text-muted">🔧 Repuestos</div><div class="fs-3 fw-bold"><?= $stats['repuestos'] ?></div></div></div></div>
        <div class="col-6 col-lg"><div class="card admin-stat shadow-sm"><div class="card-body"><div class="small text-muted">Sin stock</div><div class="fs-3 fw-bold text-danger"><?= $stats['sin_stock'] ?></div></div></div></div>
        <div class="col-12 col-lg"><div class="card admin-stat shadow-sm"><div class="card-body"><div class="small text-muted">Ocultas</div><div class="fs-3 fw-bold text-secondary"><?= $stats['ocultos'] ?></div></div></div></div>
    </div>

    <form method="GET" class="filter-box p-3 mb-4">
        <div class="row g-2 align-items-end">
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold small mb-1" for="q">Buscar</label>
                <input type="search" id="q" name="q" class="form-control" placeholder="Nombre, marca o modelo" value="<?= e($buscar) ?>">
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small mb-1" for="tipo">Tipo</label>
                <select id="tipo" name="tipo" class="form-select">
                    <option value="">Todos</option>
                    <option value="auto" <?= $tipo === 'auto' ? 'selected' : '' ?>>Autos</option>
                    <option value="repuesto" <?= $tipo === 'repuesto' ? 'selected' : '' ?>>Repuestos</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label fw-semibold small mb-1" for="estado">Estado</label>
                <select id="estado" name="estado" class="form-select">
                    <option value="">Todos</option>
                    <option value="publicado" <?= $estado === 'publicado' ? 'selected' : '' ?>>Publicados</option>
                    <option value="oculto" <?= $estado === 'oculto' ? 'selected' : '' ?>>Ocultos</option>
                    <option value="sin_stock" <?= $estado === 'sin_stock' ? 'selected' : '' ?>>Sin stock</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-grid">
                <button class="btn btn-primary" type="submit">Filtrar</button>
            </div>
        </div>
        <?php if ($buscar !== '' || $tipo !== '' || $estado !== ''): ?>
            <div class="mt-2"><a href="<?= url('admin/index.php') ?>" class="btn btn-outline-secondary btn-sm">Limpiar filtros</a></div>
        <?php endif; ?>
    </form>

    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th style="width:84px">Foto</th>
                        <th>Publicación</th>
                        <th class="text-end">Precio</th>
                        <th class="text-center">Stock</th>
                        <th>Reponer stock</th>
                        <th class="text-end">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php if (empty($productos)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-5">No hay publicaciones con esos filtros.</td></tr>
                <?php endif; ?>

                <?php foreach ($productos as $p):
                    $stock = (int)$p['stock'];
                    $claseStock = $stock <= 0 ? 'text-bg-danger' : ($stock <= 3 ? 'text-bg-warning' : 'text-bg-success');
                    $imagen = imagenProducto($p);
                    $oculto = (int)$p['activo'] === 0;
                ?>
                    <tr class="<?= $oculto ? 'table-secondary' : '' ?>">
                        <td>
                            <?php if ($imagen): ?>
                                <img src="<?= e($imagen) ?>" alt="" class="admin-thumb">
                            <?php else: ?>
                                <div class="admin-thumb admin-thumb-vacia"><?= $p['tipo'] === 'auto' ? '🚗' : '🔧' ?></div>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a class="fw-semibold text-decoration-none" href="<?= url('detalle.php?id=' . (int)$p['id']) ?>"><?= e($p['nombre']) ?></a>
                            <div class="small text-muted">
                                <?= $p['tipo'] === 'auto' ? 'Auto' : 'Repuesto' ?> · <?= e($p['marca']) ?> <?= e($p['modelo']) ?>
                                <?php if ($p['tipo'] === 'auto'): ?> · <?= number_format((int)$p['kilometros'], 0, ',', '.') ?> km<?php endif; ?>
                            </div>
                            <?php if ($oculto): ?><span class="badge text-bg-secondary">Oculta</span><?php endif; ?>
                        </td>
                        <td class="text-end fw-semibold"><?= money($p['precio']) ?></td>
                        <td class="text-center"><span class="badge <?= $claseStock ?> fs-6"><?= $stock ?></span></td>
                        <td>
                            <form method="POST" action="<?= url('admin/accion.php') ?>" class="d-flex gap-1">
                                <?= csrfCampo() ?>
                                <input type="hidden" name="accion" value="restock">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="volver" value="<?= e($volver) ?>">
                                <input type="number" name="cantidad" value="1" min="1" max="100000" class="form-control form-control-sm" style="width:80px" aria-label="Cantidad a reponer" required>
                                <button type="submit" class="btn btn-sm btn-success text-nowrap">+ Reponer</button>
                            </form>
                        </td>
                        <td class="text-end text-nowrap">
                            <a class="btn btn-sm btn-outline-primary" href="<?= url('admin/producto.php?id=' . (int)$p['id']) ?>">✏️ Editar</a>

                            <form method="POST" action="<?= url('admin/accion.php') ?>" class="d-inline">
                                <?= csrfCampo() ?>
                                <input type="hidden" name="accion" value="toggle">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="volver" value="<?= e($volver) ?>">
                                <button type="submit" class="btn btn-sm <?= $oculto ? 'btn-outline-success' : 'btn-outline-secondary' ?>">
                                    <?= $oculto ? '👁️ Publicar' : '🙈 Ocultar' ?>
                                </button>
                            </form>

                            <form method="POST" action="<?= url('admin/accion.php') ?>" class="d-inline"
                                  onsubmit="return confirm('¿Eliminar definitivamente «<?= e(addslashes($p['nombre'])) ?>»? Esta acción no se puede deshacer.');">
                                <?= csrfCampo() ?>
                                <input type="hidden" name="accion" value="eliminar">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                                <input type="hidden" name="volver" value="<?= e($volver) ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <p class="small text-muted mt-3 mb-0">
        Si una publicación ya figura en pedidos no se puede eliminar: ocultala y deja de aparecer en la tienda.
    </p>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
