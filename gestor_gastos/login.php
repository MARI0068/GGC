<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




session_start();
include("includes/conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($email == "" || $password == "") {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        $sql = "SELECT id, nombre, password FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows == 1) {
            $fila = $resultado->fetch_assoc();
            if ($fila["password"] === $password) {
                $_SESSION["usuario_id"] = $fila["id"];
                $_SESSION["usuario_nombre"] = $fila["nombre"];
                header("Location: index.php");
                exit;
            } else {
                $mensaje = "Contraseña incorrecta.";
            }
        } else {
            $mensaje = "No existe una cuenta con ese correo.";
        }
    }
}

include("vistas/login_vista.php");
