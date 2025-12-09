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
    $_SESSION["mensaje"] = "No tienes permisos para quitar propietarios.";
    header("Location: miembros.php?grupo_id=$grupo_id");
    exit;
}

// Verificar cuántos propietarios hay en el grupo
$sql = "SELECT COUNT(*) AS total FROM usuarios_grupos WHERE grupo_id = ? AND rol = 'propietario'";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if ($data["total"] <= 1) {
    // No se puede quitar si es el único propietario
    $_SESSION["mensaje"] = "El grupo debe tener al menos un propietario.";
    header("Location: miembros.php?grupo_id=$grupo_id");
    exit;
}

// Quitar rol de propietario. Pasa a "miembro"
$sql = "UPDATE usuarios_grupos SET rol = 'miembro' WHERE grupo_id = ? AND usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $miembro_id);

if ($stmt->execute()) {
    $_SESSION["mensaje"] = "Se ha quitado el rol de propietario correctamente.";
} else {
    $_SESSION["mensaje"] = "Error al quitar el rol de propietario.";
}

header("Location: miembros.php?grupo_id=$grupo_id");
exit;
