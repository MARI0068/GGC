<?php include "includes/header.php"; ?>
<div class="contenedor">
  <h2>Invitar usuarios al grupo</h2>

  <?php if (!empty($mensaje)): ?>
    <p class="<?= $ok ? 'ok' : 'error' ?>"><?= htmlspecialchars($mensaje) ?></p>
  <?php endif; ?>

  <form method="POST" action="invitar.php?grupo_id=<?= (int)$grupo_id ?>">
    <label for="email">Correo del usuario a invitar:</label>
    <input id="email" type="email" name="email" required class="form-control" autocomplete="email">
    <br>
    <input type="submit" value="Invitar" class="btn btn-success">
  </form>

  <div class="volver" style="margin-top:16px">
    <a href="grupos.php" class="btn btn-dark">Volver a Grupos</a>
  </div>
</div>
<?php include "includes/footer.php"; ?>
