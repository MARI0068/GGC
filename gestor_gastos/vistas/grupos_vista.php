<?php include("includes/header.php"); ?>

<div class="contenedor">
  <h2>Mis grupos</h2>

  <?php if (!empty($mensaje)): ?>
    <p style="color:#2e7d32;font-weight:600;"><?= htmlspecialchars($mensaje) ?></p>
  <?php endif; ?>

  
  <button type="button" id="btnAbrirModal" class="boton boton-azul">➕ Añadir grupo</button>

  <div id="modalGrupo" class="modal" aria-hidden="true">
    <div class="modal-contenido" role="dialog" aria-modal="true" aria-labelledby="titulo-modal-grupo">
      <button type="button" class="cerrar" id="cerrarModal" aria-label="Cerrar">×</button>
      <h3 id="titulo-modal-grupo">Crear nuevo grupo</h3>
      <form method="POST" action="grupos.php">
        <label>Nombre del grupo:</label>
        <input type="text" name="nombre_grupo" required>
        <label>Descripción:</label>
        <input type="text" name="descripcion" required>
        <input type="hidden" name="nuevo_grupo" value="1">
        <input type="submit" value="Guardar grupo" class="boton boton-verde">
      </form>
    </div>
  </div>

  <h3>Unirse a un grupo</h3>
  <form method="POST" action="grupos.php" class="form-barra">
    <label>Selecciona un grupo:</label>
    <select name="grupo_id" required>
      <?php if ($grupos && $grupos->num_rows): ?>
        <?php while ($g = $grupos->fetch_assoc()): ?>
          <option value="<?= (int)$g['id'] ?>">
            <?= htmlspecialchars($g['nombre']) ?> — <?= htmlspecialchars($g['descripcion']) ?>
          </option>
        <?php endwhile; ?>
      <?php else: ?>
        <option value="">No hay grupos disponibles</option>
      <?php endif; ?>
    </select>
    <button type="submit" class="boton boton-azul">Unirme</button>
  </form>

  <h3>
    <p class="mensaje-global">Listado de grupos a los que pertenezco</p>
  </h3>

  <?php if ($resultado && $resultado->num_rows): ?>
    <ul class="lista-grupos">
      <?php while ($fila = $resultado->fetch_assoc()): ?>
        <?php $rol = $fila['rol'] ?? 'miembro';
        $es_prop = ($rol === 'propietario'); ?>
        <li class="grupo-item">
          <div class="grupo-info">
            <strong><?= htmlspecialchars($fila['nombre']) ?></strong>
            <?php if ($es_prop): ?>
              <span class="badge bg-warning text-dark">👑 Propietario</span>
            <?php else: ?>
              <span class="badge bg-secondary">Miembro</span>
            <?php endif; ?>
            <div style="color:#555;margin-top:4px;"><?= htmlspecialchars($fila['descripcion']) ?></div>
          </div>

          <div class="grupo-botones <?= $es_prop ? 'owner' : '' ?>">
            <a href="gastos.php?grupo_id=<?= (int)$fila['id'] ?>" class="boton boton-gastos">💰 Gastos</a>
            <a href="balances.php?grupo_id=<?= (int)$fila['id'] ?>" class="boton boton-balance">⚖️ Balance</a>
            <a href="estadisticas.php?grupo_id=<?= (int)$fila['id'] ?>" class="boton boton-invitar">📊 Estadísticas</a>

            <?php if ($es_prop): ?>
              <a href="miembros.php?grupo_id=<?= (int)$fila['id'] ?>" class="boton boton-miembros">👥 Miembros</a>
              <a href="invitar.php?grupo_id=<?= (int)$fila['id'] ?>" class="boton boton-invitar">✉ Invitar</a>
              <a href="eliminar_grupo.php?grupo_id=<?= (int)$fila['id'] ?>"
                class="boton boton-eliminar"
                onclick="return confirmarEliminacionGrupo('<?= htmlspecialchars($fila['nombre'], ENT_QUOTES) ?>');">
                ❌ Eliminar
              </a>

            <?php endif; ?>
          </div>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="mensaje-global">No perteneces a ningún grupo todavía.</p>

  <?php endif; ?>

  <div class="volver" style="margin-top:12px">

    <a href="index.php" class="boton boton-gris">Volver al inicio</a>
  </div>
</div>

<script>

  const modal = document.getElementById('modalGrupo');
  const openBtn = document.getElementById('btnAbrirModal');
  const closeBtn = document.getElementById('cerrarModal');

  function abrirModal(e) {
    if (e) e.preventDefault();
    modal.classList.add('show');
    modal.style.display = 'block';
    modal.setAttribute('aria-hidden', 'false');
  }

  function cerrarModal(e) {
    if (e) e.preventDefault();
    modal.classList.remove('show');
    modal.style.display = 'none';
    modal.setAttribute('aria-hidden', 'true');
  }

  openBtn?.addEventListener('click', abrirModal);
  closeBtn?.addEventListener('click', cerrarModal);
  modal?.addEventListener('click', (ev) => {
    if (ev.target === modal) cerrarModal();
  });

 
  function confirmarEliminacionGrupo(nombre) {
    return confirm(
      'Vas a eliminar el grupo "' + nombre + '".\n\n' +
      'Se borrarán DEFINITIVAMENTE todos los gastos, balances y miembros asociados a este grupo.\n' +
      'Esta acción NO se puede deshacer.\n\n' +
      '¿Quieres continuar?'
    );
  }


</script>

<?php include("includes/footer.php"); ?>