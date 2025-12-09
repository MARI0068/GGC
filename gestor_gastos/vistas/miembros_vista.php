<?php include("includes/header.php"); ?>

<div class="contenedor">
  <h2>Miembros del grupo</h2>

  <?php if (!empty($mensaje)) : ?>
    <p style="color:green; font-weight:bold;"><?= htmlspecialchars($mensaje) ?></p>
  <?php endif; ?>

  <table class="tabla">
    <thead>
      <tr>
        <th>Nombre</th>
        <th>Email</th>
        <th>Rol</th>
        <th>Acciones</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($miembros && $miembros->num_rows > 0): ?>
        <?php while ($m = $miembros->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($m["nombre"]) ?></td>
            <td><?= htmlspecialchars($m["email"]) ?></td>
            <td><?= $m["rol"] === "propietario" ? "👑 Propietario" : "Miembro" ?></td>
            <td>
              <?php if ($rol_actual === "propietario" && intval($m["id"]) !== intval($usuario_id)): ?>
                <a href="expulsar.php?grupo_id=<?= $grupo_id ?>&usuario_id=<?= $m['id'] ?>"
                   class="boton boton-eliminar"
                   onclick="return confirm('¿Seguro que quieres expulsar a este usuario?');">Expulsar</a>

                <?php if ($m["rol"] === "propietario"): ?>
                  <a href="quitar_propietario.php?grupo_id=<?= $grupo_id ?>&usuario_id=<?= $m['id'] ?>"
                     class="boton boton-propietario"
                     onclick="return confirm('¿Seguro que quieres quitar el rol de propietario a este usuario?');">
                     Quitar propietario
                  </a>
                <?php else: ?>
                  <a href="hacer_propietario.php?grupo_id=<?= $grupo_id ?>&usuario_id=<?= $m['id'] ?>"
                     class="boton boton-propietario"
                     onclick="return confirm('¿Seguro que quieres hacer propietario a este usuario?');">
                     Hacer propietario
                  </a>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="4">No hay miembros en este grupo.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>

  <a href="grupos.php" class="boton boton-secundario">Volver a Grupos</a>
</div>

<?php include("includes/footer.php"); ?>
