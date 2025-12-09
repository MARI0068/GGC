<?php
session_start();
include("includes/conexion.php");

// Modo diagnóstico útil
mysqli_report(MYSQLI_REPORT_OFF);


function must_prep($cx, $sql, $tag){
    $stmt = $cx->prepare($sql);
    if(!$stmt){
        die("Error SQL [$tag]: ".$cx->error);
    }
    return $stmt;
}

// Requiere sesión
if (!isset($_SESSION["usuario_id"])) { header("Location: login.php"); exit; }
$usuario_id = (int)$_SESSION["usuario_id"];

$mensaje = "";

/* ========= Alta de grupo ========= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["nuevo_grupo"])) {
    $nombre = trim($_POST["nombre_grupo"] ?? "");
    $nombre = preg_replace('/\s+/', ' ', $nombre); // normaliza espacios
    $descripcion = trim($_POST["descripcion"] ?? "");

    if ($nombre === "" || $descripcion === "") {
        $_SESSION["mensaje"] = "Nombre y descripción son obligatorios.";
        header("Location: grupos.php"); exit;
    }

    // ¿Existe ya?
    $stmt = must_prep($conexion, "SELECT id FROM grupos WHERE nombre=? LIMIT 1", "exist-grupo");
    $stmt->bind_param("s", $nombre);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        $_SESSION["mensaje"] = "Ya existe un grupo con ese nombre.";
        header("Location: grupos.php"); exit;
    }

    // Detecta si la columna 'creado_en' existe para construir el INSERT correcto
    $tiene_creado_en = false;
    if ($rsCols = $conexion->query("SHOW COLUMNS FROM grupos LIKE 'creado_en'")) {
        $tiene_creado_en = ($rsCols->num_rows > 0);
        $rsCols->free();
    }

    if ($tiene_creado_en) {
        // INSERT con 'creado_en'
        $stmt = must_prep(
            $conexion,
            "INSERT INTO grupos (nombre, descripcion, creado_en) VALUES (?, ?, NOW())",
            "ins-grupo"
        );
        $stmt->bind_param("ss", $nombre, $descripcion);
    } else {
        // INSERT sin 'creado_en'
        $stmt = must_prep(
            $conexion,
            "INSERT INTO grupos (nombre, descripcion) VALUES (?, ?)",
            "ins-grupo"
        );
        $stmt->bind_param("ss", $nombre, $descripcion);
    }

    if ($stmt->execute()) {
        $nuevo_id = (int)$conexion->insert_id;

        // Creador propietario
        $stmt2 = must_prep(
            $conexion,
            "INSERT INTO usuarios_grupos (usuario_id, grupo_id, rol) VALUES (?, ?, 'propietario')",
            "ins-ug-prop"
        );
        $stmt2->bind_param("ii", $usuario_id, $nuevo_id);
        $stmt2->execute();

        $_SESSION["mensaje"] = "Grupo creado. Eres propietario.";
    } else {
        $_SESSION["mensaje"] = "Error al crear el grupo: ".$conexion->error;
    }
    header("Location: grupos.php"); exit;
}

/* ========= Unirse a un grupo existente ========= */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["grupo_id"]) && !isset($_POST["nuevo_grupo"])) {
    $grupo_id = (int)$_POST["grupo_id"];

    // ¿Ya pertenece?
    $stmt = must_prep($conexion, "SELECT 1 FROM usuarios_grupos WHERE usuario_id=? AND grupo_id=? LIMIT 1", "exist-ug");
    $stmt->bind_param("ii", $usuario_id, $grupo_id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows === 0) {
        $stmt2 = must_prep($conexion, "INSERT INTO usuarios_grupos (usuario_id, grupo_id, rol) VALUES (?, ?, 'miembro')", "ins-ug");
        $stmt2->bind_param("ii", $usuario_id, $grupo_id);
        $stmt2->execute();
        $_SESSION["mensaje"] = "Te has unido al grupo.";
    } else {
        $_SESSION["mensaje"] = "Ya perteneces a ese grupo.";
    }
    header("Location: grupos.php"); exit;
}

/* ========= Datos para la vista ========= */

/* Unirse a un grupo: anti-join con LEFT JOIN para evitar problemas con subconsultas */
$sqlUnirse = "
SELECT g.id, g.nombre, g.descripcion
FROM grupos g
LEFT JOIN usuarios_grupos ug
  ON ug.grupo_id = g.id AND ug.usuario_id = ?
WHERE ug.grupo_id IS NULL
ORDER BY g.nombre
";
$stmt = must_prep($conexion, $sqlUnirse, "list-unirse");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$grupos = $stmt->get_result();

/* ========= Mis grupos ========= */
$sqlMis = "
SELECT
  g.id,
  g.nombre,
  g.descripcion,
  COALESCE(ug.rol,'miembro') AS rol
FROM grupos g
JOIN usuarios_grupos ug
  ON ug.grupo_id = g.id
WHERE ug.usuario_id = ?
ORDER BY g.nombre
";
$stmt2 = must_prep($conexion, $sqlMis, "list-mios");
$stmt2->bind_param("i", $usuario_id);
$stmt2->execute();
$resultado = $stmt2->get_result();

/* ========= Vista ========= */
include("vistas/grupos_vista.php");
