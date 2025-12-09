<?php
session_start();
require_once "includes/conexion.php";

/* Mostrar errores mientras estás en pruebas */
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

/* 1. Comprobar que el usuario ha iniciado sesión */
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = (int) $_SESSION["usuario_id"];

/* 2. Comprobar que recibimos el id de grupo */
if (!isset($_GET["grupo_id"])) {
    $_SESSION["mensaje"] = "❌ Grupo no especificado.";
    header("Location: grupos.php");
    exit;
}

$grupo_id = (int) $_GET["grupo_id"];

if ($grupo_id <= 0) {
    $_SESSION["mensaje"] = "❌ Grupo no válido.";
    header("Location: grupos.php");
    exit;
}

/* 3. (Opcional) Comprobar que el usuario pertenece al grupo
   Ajusta la consulta a tu esquema. Ejemplo si tienes tabla usuarios_grupos: */

$consulta = $conexion->prepare("
    SELECT ug.grupo_id
    FROM usuarios_grupos ug
    WHERE ug.grupo_id = ? AND ug.usuario_id = ?
");
if ($consulta) {
    $consulta->bind_param("ii", $grupo_id, $usuario_id);
    $consulta->execute();
    $consulta->store_result();

    if ($consulta->num_rows === 0) {
        $consulta->close();
        $_SESSION["mensaje"] = "❌ No tienes permiso para eliminar este grupo.";
        header("Location: grupos.php");
        exit;
    }
    $consulta->close();
}

/* 4. Eliminar datos relacionados dentro de una transacción */

$conexion->begin_transaction();

try {
    // 4.1 Borrar los gastos del grupo
    $stmt = $conexion->prepare("DELETE FROM gastos WHERE grupo_id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar borrado de gastos: " . $conexion->error);
    }
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    // 4.2 Borrar relaciones usuarios_grupos del grupo
    $stmt = $conexion->prepare("DELETE FROM usuarios_grupos WHERE grupo_id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar borrado de usuarios_grupos: " . $conexion->error);
    }
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    // 4.3 Borrar el propio grupo
    $stmt = $conexion->prepare("DELETE FROM grupos WHERE id = ?");
    if (!$stmt) {
        throw new Exception("Error al preparar borrado de grupo: " . $conexion->error);
    }
    $stmt->bind_param("i", $grupo_id);
    $stmt->execute();
    $stmt->close();

    // Todo OK
    $conexion->commit();

    $_SESSION["mensaje"] = "✅ Grupo eliminado correctamente.";
    header("Location: grupos.php");
    exit;

} catch (Exception $e) {
    $conexion->rollback();
    // Guarda el mensaje en sesión para no dejar la pantalla en blanco
    $_SESSION["mensaje"] = "❌ Error al eliminar el grupo: " . $e->getMessage();
    header("Location: grupos.php");
    exit;
}
