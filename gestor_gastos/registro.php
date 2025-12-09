<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);




include("includes/conexion.php");

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre   = trim($_POST["nombre"]);
    $email    = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if ($nombre == "" || $email == "" || $password == "") {
        $mensaje = "Todos los campos son obligatorios.";
    } else {
        $sql = "SELECT id FROM usuarios WHERE email = ?";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
        } else {
            $sql = "INSERT INTO usuarios (nombre, email, password) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("sss", $nombre, $email, $password);
            if ($stmt->execute()) {
                $mensaje = "Usuario registrado correctamente.";
            } else {
                $mensaje = "Error al registrar: " . $conexion->error;
            }
        }
    }
}

include("vistas/registro_vista.php");
