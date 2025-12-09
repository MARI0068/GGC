<?php
$host = "localhost";     // Servidor
$user = "root";          // Usuario de MySQL 
$pass = "";              // Contraseña 
$db   = "gestor_gastos"; // Nombre de tu base de datos

// Crear conexión
$conexion = new mysqli($host, $user, $pass, $db);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Forzar codificación UTF-8
$conexion->set_charset("utf8mb4");
?>
