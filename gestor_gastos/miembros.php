<?php
session_start();
include("includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) { header("Location: login.php"); exit; }

$usuario_id = $_SESSION["usuario_id"];
$grupo_id   = isset($_GET["grupo_id"]) ? intval($_GET["grupo_id"]) : 0;
if ($grupo_id <= 0) { header("Location: grupos.php"); exit; }

$mensaje = "";

/* Verificar pertenencia y rol actual */
$sql = "SELECT rol FROM usuarios_grupos WHERE grupo_id=? AND usuario_id=? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $usuario_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { die("No tienes acceso a este grupo."); }
$rol_actual = $row["rol"];

/* Obtener miembros del grupo */
$sql = "SELECT u.id, u.nombre, u.email, ug.rol
        FROM usuarios u
        INNER JOIN usuarios_grupos ug ON u.id = ug.usuario_id
        WHERE ug.grupo_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$miembros = $stmt->get_result();   // <-- ahora siempre existe 

// Cargar vista
include("vistas/miembros_vista.php");
