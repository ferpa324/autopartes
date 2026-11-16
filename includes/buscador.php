<form class="filter-box p-3 p-md-4 mb-4" method="GET">
    <div class="row g-3 align-items-end">
        <div class="col-12 col-md-6">
            <label class="form-label fw-semibold">Buscar</label>
            <div class="input-group">
                <span class="input-group-text">🔎</span>
                <input type="search" name="q" class="form-control" placeholder="Marca, modelo, repuesto..." value="<?= e($buscar) ?>">
            </div>
        </div>
        <div class="col-12 col-md-4">
            <label class="form-label fw-semibold">Ordenar por</label>
            <select name="orden" class="form-select">
                <option value="recientes" <?= $orden === 'recientes' ? 'selected' : '' ?>>Más recientes</option>
                <option value="precio_asc" <?= $orden === 'precio_asc' ? 'selected' : '' ?>>Menor precio</option>
                <option value="precio_desc" <?= $orden === 'precio_desc' ? 'selected' : '' ?>>Mayor precio</option>
                <option value="nombre" <?= $orden === 'nombre' ? 'selected' : '' ?>>Nombre (A-Z)</option>
                <?php if (($tipo ?? '') === 'auto'): ?>
                    <option value="kilometros_asc" <?= $orden === 'kilometros_asc' ? 'selected' : '' ?>>Menos kilómetros</option>
                <?php endif; ?>
            </select>
        </div>
        <div class="col-12 col-md-2 d-grid">
            <button class="btn btn-primary btn-lg" type="submit">Buscar</button>
        </div>
    </div>
</form>
