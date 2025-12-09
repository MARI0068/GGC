<?php
$BASE = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
?>
<!DOCTYPE html>
<html lang="es">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">


<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestor de Gastos</title>
     <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

    <header class="site-header">
        <div class="header-inner">
            <!-- LOGO -->
            <a href="<?= $BASE ?>/index.php" class="brand">
                <img src="<?= $BASE ?>/imagenes/menu/logoGGC.gif" alt="GGC" class="brand-logo">

            </a>

            <!-- euros -->
            <div class="euros">
         
                <img src="imagenes/menu/euro.gif" class="euro-l" alt="">
                <img src="imagenes/menu/euro.gif" class="euro-xl" alt="">
                <img src="imagenes/menu/euro.gif" class="euro-xxl" alt="">
                <img src="imagenes/menu/euro.gif" class="euro-g" alt="">
                <img src="imagenes/menu/euro.gif" class="euro-giro" alt="">
            </div>


            <!-- boton menu movil -->
            <button class="nav-toggle" aria-label="Abrir menú" aria-expanded="false">☰</button>

            <!-- navegación -->
            <nav class="nav" id="nav">
                <a href="<?= $BASE ?>/index.php">Inicio</a>
                <a href="<?= $BASE ?>/login.php">Login</a>
                <a href="<?= $BASE ?>/registro.php">Registro</a>
                <a href="<?= $BASE ?>/grupos.php">Grupos</a>
                <a href="<?= $BASE ?>/salir.php">Salir</a>
            </nav>
        </div>
    </header>

    <script>
        const btn = document.querySelector('.nav-toggle');
        const nav = document.getElementById('nav');
        btn.addEventListener('click', () => {
            nav.classList.toggle('open');
            btn.setAttribute('aria-expanded', nav.classList.contains('open') ? 'true' : 'false');
        });
    </script>