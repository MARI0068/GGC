<?php include("includes/header.php"); ?>

<div class="contenedor">
 


<h2>Iniciar sesión</h2>
<form id="loginForm" method="POST" action="">
    <label>Email:</label><br>
    <input type="email" id="email" name="email" autocomplete="off"><br><br>

    <label>Contraseña:</label><br>
    <input type="password" id="password" name="password" autocomplete="off"><br><br>

    <input type="submit" value="Entrar">
</form>


  <P class="volver"></p>
    <p class="mensaje-global"><?php echo $mensaje; ?></p>
 
    <p><a href="registro.php" class="boton boton-gris">Registrate</a></p>
  </div>



<?php include("includes/footer.php"); ?>
