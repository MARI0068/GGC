<?php include("includes/header.php"); ?>

<div class="contenedor">

<h2>Registro de usuario</h2>
<form id="registerForm" method="POST" action="">
    <label>Nombre:</label><br>
    <input type="text" id="nombre" name="nombre"><br><br>

    <label>Email:</label><br>
    <input type="email" id="email" name="email" autocomplete="off"><br><br>

    <label>Contraseña:</label><br>
    <input type="password" id="password" name="password" autocomplete="off"><br><br>

    <label>Confirmar contraseña:</label><br>
    <input type="password" id="confirmPassword" name="confirmPassword"><br><br>

    <input type="submit" value="Registrarse">
</form>

<p class="mensaje-global"><?php echo $mensaje; ?></p>
<p><a href="login.php" class="boton boton-gris">Ir a Login</a></p>
</div>


<?php include("includes/footer.php"); ?>
<script src="js/validaciones.js" defer></script>
