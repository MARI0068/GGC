<?php include __DIR__ . '/../includes/header.php'; ?>

<div class="contenedor" style="max-width:920px;margin-top:28px;">
  <div class="salir-panel">
    <div class="salir-icon">🔒</div>
    <h2>Has salido de tu cuenta</h2>
    <p class="salir-msg">
      Tu sesión se ha cerrado correctamente. Si compartes este equipo, es una buena práctica
      cerrar el navegador o entrar con “modo invitado” para mayor privacidad.
    </p>

    <div class="salir-actions">
      <a href="<?= $BASE ?>/login.php" class="boton boton-azul">Iniciar sesión de nuevo</a>
      <a href="<?= $BASE ?>/index.php" class="boton boton-gris">Volver al inicio</a>
     
    </div>

    <div class="salir-footnote">
      ¿Necesitas ayuda? Contacta con el administrador del sistema.
    </div>
  </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
