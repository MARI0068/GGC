<?php
session_start();
include("includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

if (isset($_GET["grupo_id"]) && isset($_GET["usuario_id"])) {
    $grupo_id = intval($_GET["grupo_id"]);
    $expulsado_id = intval($_GET["usuario_id"]);

    // Verificar que el usuario actual es propietario del grupo
    $sql = "SELECT rol FROM usuarios_grupos WHERE grupo_id = ? AND usuario_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $grupo_id, $usuario_id);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $fila = $res->fetch_assoc();
        if ($fila["rol"] === "propietario") {

            // Eliminar al usuario del grupo
            $sql = "DELETE FROM usuarios_grupos WHERE grupo_id = ? AND usuario_id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ii", $grupo_id, $expulsado_id);
            $stmt->execute();
        }
    }
}

header("Location: miembros.php?grupo_id=" . $grupo_id);
exit;
?>
