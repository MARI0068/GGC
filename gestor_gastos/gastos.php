<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


session_start();
include("includes/conexion.php");

// Requiere sesión
if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];
$grupo_id   = isset($_GET["grupo_id"]) ? intval($_GET["grupo_id"]) : 0;
if ($grupo_id <= 0) { die("Grupo no válido."); }

$mensaje = "";

// Verificar pertenencia y rol
$sql = "SELECT rol FROM usuarios_grupos WHERE grupo_id=? AND usuario_id=? LIMIT 1";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $usuario_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if (!$row) { die("No tienes acceso a este grupo."); }
$rol_actual = $row["rol"];

// Alta de gasto
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["concepto"])) {
    $concepto = trim($_POST["concepto"]);
    $categoria= trim($_POST["categoria"] ?? "");
    $cantidad = filter_var($_POST["cantidad"] ?? "", FILTER_VALIDATE_FLOAT);
    $fecha    = trim($_POST["fecha"] ?? "");

    if ($concepto === "" || !$cantidad || $cantidad <= 0 || $fecha === "") {
        $mensaje = "Datos de gasto inválidos.";
    } else {
        $sql = "INSERT INTO gastos (grupo_id, usuario_id, concepto, categoria, cantidad, fecha)
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param("iissds", $grupo_id, $usuario_id, $concepto, $categoria, $cantidad, $fecha);
        $stmt->execute();
        $mensaje = "Gasto añadido.";
    }
}

// Eliminar gasto con permisos: pagador o propietario
if (isset($_GET["eliminar_id"])) {
    $gasto_id = intval($_GET["eliminar_id"]);

    // ¿Quién pagó?
    $sql = "SELECT usuario_id FROM gastos WHERE id=? AND grupo_id=? LIMIT 1";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $gasto_id, $grupo_id);
    $stmt->execute();
    $r = $stmt->get_result()->fetch_assoc();
    if ($r) {
        $pagador_id = intval($r["usuario_id"]);
        if ($rol_actual === "propietario" || $pagador_id === $usuario_id) {
            $sql = "DELETE FROM gastos WHERE id=? AND grupo_id=?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("ii", $gasto_id, $grupo_id);
            $stmt->execute();
            $mensaje = "Gasto eliminado.";
        } else {
            $mensaje = "Sin permiso para eliminar este gasto.";
        }
    } else {
        $mensaje = "Gasto no encontrado.";
    }
}

// -------- Filtros --------
$f_inicio = $_GET['f_inicio'] ?? '';
$f_fin    = $_GET['f_fin']    ?? '';
$categ    = trim($_GET['categoria'] ?? '');
$miembro  = intval($_GET['miembro'] ?? 0);

$cond   = ["g.grupo_id = ?"];
$params = [$grupo_id];
$types  = "i";

if ($f_inicio !== '') { $cond[] = "g.fecha >= ?";    $params[] = $f_inicio; $types .= "s"; }
if ($f_fin    !== '') { $cond[] = "g.fecha <= ?";    $params[] = $f_fin;    $types .= "s"; }
if ($categ    !== '') { $cond[] = "g.categoria = ?"; $params[] = $categ;    $types .= "s"; }
if ($miembro  > 0)    { $cond[] = "g.usuario_id = ?";$params[] = $miembro;  $types .= "i"; }

// Listado con filtros
$sql = "SELECT g.id, g.concepto, g.categoria, g.cantidad, g.fecha, u.nombre
        FROM gastos g JOIN usuarios u ON u.id=g.usuario_id
        WHERE " . implode(" AND ", $cond) . "
        ORDER BY g.fecha DESC, g.id DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$gastos = $stmt->get_result();

// Total filtrado
$sqlT = "SELECT SUM(g.cantidad) total FROM gastos g WHERE " . implode(" AND ", $cond);
$stmtT = $conexion->prepare($sqlT);
$stmtT->bind_param($types, ...$params);
$stmtT->execute();
$total_filtrado = (float)($stmtT->get_result()->fetch_assoc()['total'] ?? 0);

// Datos para selects
$stc = $conexion->prepare("SELECT DISTINCT categoria FROM gastos WHERE grupo_id=? AND categoria<>'' ORDER BY categoria");
$stc->bind_param("i", $grupo_id);
$stc->execute();
$rs_cats = $stc->get_result();

$stm = $conexion->prepare("SELECT u.id,u.nombre FROM usuarios u
                           JOIN usuarios_grupos ug ON u.id=ug.usuario_id
                           WHERE ug.grupo_id=? ORDER BY u.nombre");
$stm->bind_param("i", $grupo_id);
$stm->execute();
$rs_mi = $stm->get_result();

// Vista
include("vistas/gastos_vista.php");
