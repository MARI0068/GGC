<?php
session_start();
include("includes/conexion.php");

// Verificar login
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Comprobar parámetros
if (!isset($_GET["grupo_id"]) || !isset($_GET["usuario_id"])) {
    header("Location: grupos.php");
    exit;
}

$grupo_id = intval($_GET["grupo_id"]);
$miembro_id = intval($_GET["usuario_id"]);

// Verificar que el usuario actual es propietario del grupo
$sql = "SELECT rol FROM usuarios_grupos WHERE grupo_id = ? AND usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$rol_actual = $result->fetch_assoc();

if (!$rol_actual || $rol_actual["rol"] != "propietario") {
    $_SESSION["mensaje"] = "No tienes permisos para asignar propietarios.";
    header("Location: miembros.php?grupo_id=$grupo_id");
    exit;
}

// Asignar propietario al usuario
$sql = "UPDATE usuarios_grupos SET rol = 'propietario' WHERE grupo_id = ? AND usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $miembro_id);

if ($stmt->execute()) {
    $_SESSION["mensaje"] = "Usuario ascendido a propietario correctamente.";
} else {
    $_SESSION["mensaje"] = "Error al asignar el rol de propietario.";
}

header("Location: miembros.php?grupo_id=$grupo_id");
exit;
