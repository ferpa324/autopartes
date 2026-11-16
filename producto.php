<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/consultas.php';
require_once __DIR__ . '/../includes/imagenes.php';

exigirAdmin($pdo);

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$producto = null;

if ($id > 0) {
    $producto = obtenerProducto($pdo, $id, false);
    if (!$producto) {
        setFlash('error', 'La publicación que querés editar no existe.');
        header('Location: ' . url('admin/index.php'));
        exit;
    }
}

$editando = $producto !== null;
$errores = [];

$datos = [
    'tipo'        => $editando ? $producto['tipo'] : 'auto',
    'nombre'      => $editando ? $producto['nombre'] : '',
    'marca'       => $editando ? $producto['marca'] : '',
    'modelo'      => $editando ? $producto['modelo'] : '',
    'descripcion' => $editando ? (string)$producto['descripcion'] : '',
    'precio'      => $editando ? (string)(float)$producto['precio'] : '',
    'stock'       => $editando ? (string)(int)$producto['stock'] : '1',
    'kilometros'  => $editando ? (string)(int)$producto['kilometros'] : '0',
    'activo'      => $editando ? (int)$producto['activo'] : 1,
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarCsrf();

    // El tipo no se puede cambiar una vez creada la publicación.
    $tipo = $editando ? $producto['tipo'] : (($_POST['tipo'] ?? '') === 'repuesto' ? 'repuesto' : 'auto');

    $datos = [
        'tipo'        => $tipo,
        'nombre'      => trim((string)($_POST['nombre'] ?? '')),
        'marca'       => trim((string)($_POST['marca'] ?? '')),
        'modelo'      => trim((string)($_POST['modelo'] ?? '')),
        'descripcion' => trim((string)($_POST['descripcion'] ?? '')),
        'precio'      => trim((string)($_POST['precio'] ?? '')),
        'stock'       => trim((string)($_POST['stock'] ?? '')),
        'kilometros'  => trim((string)($_POST['kilometros'] ?? '0')),
        'activo'      => isset($_POST['activo']) ? 1 : 0,
    ];

    $nombreLen = mb_strlen($datos['nombre'], 'UTF-8');
    if ($nombreLen < 3 || $nombreLen > 150) {
        $errores[] = 'El nombre debe tener entre 3 y 150 caracteres.';
    }
    if ($datos['marca'] === '' || mb_strlen($datos['marca'], 'UTF-8') > 80) {
        $errores[] = 'La marca es obligatoria (máximo 80 caracteres).';
    }
    if ($datos['modelo'] === '' || mb_strlen($datos['modelo'], 'UTF-8') > 120) {
        $errores[] = 'El modelo es obligatorio (máximo 120 caracteres).';
    }
    if (mb_strlen($datos['descripcion'], 'UTF-8') > 2000) {
        $errores[] = 'La descripción no puede superar los 2000 caracteres.';
    }

    $precioTxt = str_replace(',', '.', $datos['precio']);
    if (!is_numeric($precioTxt) || (float)$precioTxt <= 0 || (float)$precioTxt >= 9999999999) {
        $errores[] = 'Ingresá un precio válido mayor a 0.';
        $precio = 0.0;
    } else {
        $precio = round((float)$precioTxt, 2);
    }

    $stock = filter_var($datos['stock'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 1000000]]);
    if ($stock === false) {
        $errores[] = 'El stock debe ser un número entero igual o mayor a 0.';
        $stock = 0;
    }

    $km = 0;
    if ($tipo === 'auto') {
        $km = filter_var($datos['kilometros'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 2000000]]);
        if ($km === false) {
            $errores[] = 'Los kilómetros deben ser un número entero igual o mayor a 0.';
            $km = 0;
        }
    }

    $hayArchivo = isset($_FILES['imagen']) && (int)$_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE;
    $imagenActual = $editando ? trim((string)$producto['imagen']) : '';
    $tieneImagenValida = $editando && imagenProducto($producto) !== null;

    if ($tipo === 'auto' && !$hayArchivo && !$tieneImagenValida) {
        $errores[] = 'Los autos deben tener una imagen. Subí una foto JPG, PNG o WEBP.';
    }

    $nuevaRuta = null;
    if (!$errores && $hayArchivo) {
        try {
            $nuevaRuta = guardarImagenSubida($_FILES['imagen'], $tipo, $datos['nombre']);
        } catch (RuntimeException $ex) {
            $errores[] = $ex->getMessage();
        }
    }

    if (!$errores) {
        try {
            if ($editando) {
                $imagenFinal = $nuevaRuta ?? ($imagenActual !== '' ? $imagenActual : null);
                $stmt = $pdo->prepare(
                    'UPDATE productos
                        SET nombre = ?, descripcion = ?, precio = ?, stock = ?, marca = ?, modelo = ?,
                            kilometros = ?, imagen = ?, activo = ?
                      WHERE id = ?'
                );
                $stmt->execute([
                    $datos['nombre'], $datos['descripcion'], number_format($precio, 2, '.', ''), $stock,
                    $datos['marca'], $datos['modelo'], $km, $imagenFinal, $datos['activo'], $id,
                ]);

                if ($nuevaRuta !== null) {
                    borrarImagenSiHuerfana($pdo, $imagenActual);
                }
                setFlash('exito', 'Publicación «' . $datos['nombre'] . '» actualizada.');
            } else {
                $stmt = $pdo->prepare(
                    'INSERT INTO productos (nombre, descripcion, precio, stock, tipo, marca, modelo, kilometros, imagen, activo)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                $stmt->execute([
                    $datos['nombre'], $datos['descripcion'], number_format($precio, 2, '.', ''), $stock,
                    $tipo, $datos['marca'], $datos['modelo'], $km, $nuevaRuta, $datos['activo'],
                ]);
                setFlash('exito', 'Publicación «' . $datos['nombre'] . '» creada correctamente.');
            }

            header('Location: ' . url('admin/index.php'));
            exit;
        } catch (Throwable $ex) {
            // Si falló la base de datos, no dejamos una imagen suelta en disco.
            if ($nuevaRuta !== null) {
                borrarImagenSiHuerfana($pdo, $nuevaRuta);
            }
            error_log('AutoMarket admin: ' . $ex->getMessage());
            $errores[] = 'No se pudo guardar en la base de datos. Intentá de nuevo.';
        }
    }
}

$imagenVista = $editando ? imagenProducto($producto) : null;

$titulo = $editando ? 'Editar publicación' : 'Nueva publicación';
$activo = 'admin';
require __DIR__ . '/../includes/header.php';
?>

<section class="page-title text-white py-5">
    <div class="container">
        <a href="<?= url('admin/index.php') ?>" class="btn btn-outline-light btn-sm mb-3">← Volver al panel</a>
        <h1 class="fw-bold mb-0"><?= $editando ? '✏️ Editar publicación' : '➕ Nueva publicación' ?></h1>
    </div>
</section>

<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card form-card">
                <div class="card-body p-4 p-lg-5">

                    <?php if ($errores): ?>
                        <div class="alert alert-danger">
                            <strong>Revisá estos datos:</strong>
                            <ul class="mb-0 mt-1">
                                <?php foreach ($errores as $mensaje): ?><li><?= e($mensaje) ?></li><?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" novalidate>
                        <?= csrfCampo() ?>
                        <?php if ($editando): ?><input type="hidden" name="id" value="<?= (int)$producto['id'] ?>"><?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="tipo">Tipo</label>
                                <?php if ($editando): ?>
                                    <input type="text" class="form-control" value="<?= $datos['tipo'] === 'auto' ? 'Auto' : 'Repuesto' ?>" disabled>
                                <?php else: ?>
                                    <select name="tipo" id="tipo" class="form-select">
                                        <option value="auto" <?= $datos['tipo'] === 'auto' ? 'selected' : '' ?>>🚘 Auto</option>
                                        <option value="repuesto" <?= $datos['tipo'] === 'repuesto' ? 'selected' : '' ?>>🔧 Repuesto</option>
                                    </select>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label fw-semibold" for="nombre">Nombre de la publicación</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" maxlength="150" value="<?= e($datos['nombre']) ?>" placeholder="Ej.: Toyota Corolla XEI 2022" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="marca">Marca</label>
                                <input type="text" name="marca" id="marca" class="form-control" maxlength="80" value="<?= e($datos['marca']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold" for="modelo">Modelo</label>
                                <input type="text" name="modelo" id="modelo" class="form-control" maxlength="120" value="<?= e($datos['modelo']) ?>" required>
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="descripcion">Descripción</label>
                                <textarea name="descripcion" id="descripcion" class="form-control" rows="3" maxlength="2000"><?= e($datos['descripcion']) ?></textarea>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="precio">Precio ($)</label>
                                <input type="number" name="precio" id="precio" class="form-control" min="0.01" step="0.01" value="<?= e($datos['precio']) ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold" for="stock">Stock</label>
                                <input type="number" name="stock" id="stock" class="form-control" min="0" step="1" value="<?= e($datos['stock']) ?>" required>
                            </div>
                            <div class="col-md-4" id="campo-km">
                                <label class="form-label fw-semibold" for="kilometros">Kilómetros</label>
                                <input type="number" name="kilometros" id="kilometros" class="form-control" min="0" step="1" value="<?= e($datos['kilometros']) ?>">
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold" for="imagen">
                                    Imagen <span class="text-muted fw-normal" id="imagen-ayuda"><?= $datos['tipo'] === 'auto' ? '(obligatoria para autos)' : '(opcional)' ?></span>
                                </label>
                                <div class="row g-3 align-items-center">
                                    <div class="col-sm-4">
                                        <div class="producto-imagen detalle-imagen rounded-3" style="min-height: 140px;">
                                            <img id="vista-previa" class="producto-foto" alt="Vista previa" src="<?= $imagenVista ? e($imagenVista) : '' ?>" style="<?= $imagenVista ? '' : 'display:none;' ?>">
                                            <span id="vista-vacia" style="font-size:3rem; <?= $imagenVista ? 'display:none;' : '' ?>">🖼️</span>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <input type="file" name="imagen" id="imagen" class="form-control" accept="image/jpeg,image/png,image/webp">
                                        <div class="form-text">
                                            JPG, PNG o WEBP, hasta 5 MB.
                                            <?= $editando ? 'Si no subís una nueva, se mantiene la actual.' : '' ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch" name="activo" id="activo" value="1" <?= (int)$datos['activo'] === 1 ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="activo">Publicada (visible en la tienda)</label>
                                </div>
                                <div class="form-text">Si la desmarcás queda como borrador: solo la ves vos como admin.</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg px-4"><?= $editando ? '💾 Guardar cambios' : '🚀 Publicar' ?></button>
                            <a href="<?= url('admin/index.php') ?>" class="btn btn-outline-secondary btn-lg">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
    var tipo = document.getElementById('tipo');
    var campoKm = document.getElementById('campo-km');
    var ayuda = document.getElementById('imagen-ayuda');
    var tipoFijo = <?= json_encode($datos['tipo']) ?>;

    function actualizarTipo() {
        var actual = tipo ? tipo.value : tipoFijo;
        campoKm.style.display = actual === 'auto' ? '' : 'none';
        ayuda.textContent = actual === 'auto' ? '(obligatoria para autos)' : '(opcional)';
    }
    if (tipo) { tipo.addEventListener('change', actualizarTipo); }
    actualizarTipo();

    var input = document.getElementById('imagen');
    var vista = document.getElementById('vista-previa');
    var vacia = document.getElementById('vista-vacia');
    input.addEventListener('change', function () {
        var archivo = input.files && input.files[0];
        if (!archivo) { return; }
        vista.src = URL.createObjectURL(archivo);
        vista.style.display = '';
        vacia.style.display = 'none';
    });
})();
</script>

<?php require __DIR__ . '/../includes/footer.php'; ?>
