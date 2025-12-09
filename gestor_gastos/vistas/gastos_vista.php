<?php include("includes/header.php"); ?>

<div class="contenedor">
  <h2>Gastos del grupo</h2>

  <?php if (!empty($mensaje)) : ?>
    <p style="color:green; font-weight:bold;"><?= htmlspecialchars($mensaje) ?></p>
  <?php endif; ?>

  <!-- Alta de gasto -->
  <form method="POST" action="gastos.php?grupo_id=<?= $grupo_id ?>" class="form-barra">
    <input type="text" name="concepto" placeholder="Concepto" required>
    <input type="text" name="categoria" placeholder="Categoría">
    <input type="number" step="0.01" name="cantidad" placeholder="Cantidad (€)" required>
    <input type="date" name="fecha" required>
    <button type="submit" class="boton boton-gastos">Añadir gasto</button>
    <a href="balances.php?grupo_id=<?= $grupo_id ?>" class="boton boton-balance">Ver balance</a>
  </form>

  <!-- Filtros -->
  <form method="GET" action="gastos.php" class="form-barra filtros-gastos">
    <input type="hidden" name="grupo_id" value="<?= $grupo_id ?>">

    <div class="campo-filtro">
      <label for="f_inicio">Desde</label>
      <input type="date" id="f_inicio" name="f_inicio"
             value="<?= htmlspecialchars($_GET['f_inicio'] ?? '') ?>">
    </div>

    <div class="campo-filtro">
      <label for="f_fin">Hasta</label>
      <input type="date" id="f_fin" name="f_fin"
             value="<?= htmlspecialchars($_GET['f_fin'] ?? '') ?>">
    </div>

    <div class="campo-filtro">
      <label for="categoria">Categoría</label>
      <select id="categoria" name="categoria">
        <option value="">Todas</option>
        <?php while ($c = $rs_cats->fetch_assoc()): $v = $c['categoria']; ?>
          <option value="<?= htmlspecialchars($v) ?>"
                  <?= ($v === ($_GET['categoria'] ?? '') ? 'selected' : '') ?>>
            <?= htmlspecialchars($v) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="campo-filtro">
      <label for="miembro">Miembro</label>
      <select id="miembro" name="miembro">
        <option value="0">Todos</option>
        <?php while ($m = $rs_mi->fetch_assoc()): ?>
          <option value="<?= $m['id'] ?>"
                  <?= ((int)($_GET['miembro'] ?? 0) === (int)$m['id'] ? 'selected' : '') ?>>
            <?= htmlspecialchars($m['nombre']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <button type="submit" class="boton boton-balance">Filtrar</button>
    <a href="gastos.php?grupo_id=<?= $grupo_id ?>" class="boton boton-secundario">Limpiar</a>
  </form>


  <!-- Listado -->
  <div class="tabla-responsive">
    <table class="tabla">
      <thead>
        <tr>
          <th>Fecha</th>
          <th>Concepto</th>
          <th>Categoría</th>
          <th>Cantidad</th>
          <th>Pagado por</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($gastos && $gastos->num_rows > 0): ?>
          <?php while ($fila = $gastos->fetch_assoc()): ?>
            <?php
            $cat = $fila['categoria'] ?: 'Sin categoría';
            $cat_class = 'cat-' . preg_replace('/[^a-z0-9]+/i', '-', strtolower($cat));
            ?>
            <tr>
              <td><?= htmlspecialchars($fila['fecha']); ?></td>
              <td><?= htmlspecialchars($fila['concepto']); ?></td>
              <td><span class="chip <?= $cat_class ?>"><?= htmlspecialchars($cat) ?></span></td>
              <td><?= number_format((float)$fila['cantidad'], 2, ',', '.') ?> €</td>
              <td><?= htmlspecialchars($fila['nombre']); ?></td>
              <td>
                <a href="gastos.php?grupo_id=<?= $grupo_id ?>&eliminar_id=<?= $fila['id'] ?>"
                  class="boton boton-eliminar"
                  onclick="return confirm('¿Seguro que quieres eliminar este gasto?');">
                  Eliminar
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        <?php else: ?>
          <tr>
            <td colspan="6">No hay gastos con los filtros actuales.</td>
          </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <a href="grupos.php" class="boton boton-secundario">Volver a grupos</a>
</div>

<?php include("includes/footer.php"); ?>