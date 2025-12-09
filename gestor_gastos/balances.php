<?php
session_start();
include("includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION["usuario_id"];

// Verificar grupo_id recibido
if (!isset($_GET["grupo_id"])) {
    die("Grupo no especificado.");
}
$grupo_id = intval($_GET["grupo_id"]);

// Verificar que el usuario pertenece al grupo
$sql = "SELECT * FROM usuarios_grupos WHERE grupo_id = ? AND usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $grupo_id, $usuario_id);
$stmt->execute();
if ($stmt->get_result()->num_rows == 0) {
    die("No tienes acceso a este grupo.");
}

// Obtener miembros del grupo
$sql = "SELECT u.id, u.nombre 
        FROM usuarios u
        INNER JOIN usuarios_grupos ug ON u.id = ug.usuario_id
        WHERE ug.grupo_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$miembros = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Calcular total de gastos del grupo
$sql = "SELECT SUM(cantidad) as total FROM gastos WHERE grupo_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$result = $stmt->get_result()->fetch_assoc();
$total = $result && $result["total"] ? floatval($result["total"]) : 0.0;

// Calcular lo que debería pagar cada miembro
$cuota = count($miembros) > 0 ? $total / count($miembros) : 0;

// Calcular lo que pagó cada miembro
$sql = "SELECT usuario_id, SUM(cantidad) as pagado 
        FROM gastos WHERE grupo_id = ? GROUP BY usuario_id";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$pagos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Array asociativo [usuario_id => cantidad pagada]
$pagado_por_usuario = [];
foreach ($pagos as $p) {
    $pagado_por_usuario[$p["usuario_id"]] = floatval($p["pagado"]);
}

// Preparar balance
$balances = [];
foreach ($miembros as $m) {
    $id = $m["id"];
    $nombre = $m["nombre"];
    $pagado = isset($pagado_por_usuario[$id]) ? $pagado_por_usuario[$id] : 0.0;
    $saldo = $pagado - $cuota;
    $balances[] = [
        "nombre" => $nombre,
        "pagado" => $pagado,
        "saldo" => $saldo
    ];
}

// Mostrar vista
include("vistas/balances_vista.php");
