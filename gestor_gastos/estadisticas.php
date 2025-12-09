<?php
// Controlador de estadísticas

require "includes/conexion.php";

$grupo_id = intval($_GET['grupo_id'] ?? 0);
if ($grupo_id <= 0) {
    header("Location: grupos.php");
    exit;
}

/*   Gasto por usuario  */
$stmt = $conexion->prepare("
    SELECT u.nombre, SUM(g.cantidad) AS total 
    FROM usuarios u
    JOIN gastos g ON g.usuario_id = u.id
    WHERE g.grupo_id = ?
    GROUP BY u.id
    ORDER BY u.nombre
");
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$res_usuarios = $stmt->get_result();

$usrLabels = [];
$usrData   = [];

while ($row = $res_usuarios->fetch_assoc()) {
    $usrLabels[] = $row['nombre'];
    $usrData[]   = (float)$row['total'];
}
$stmt->close();

/*   Gasto por categoría */
$stmt = $conexion->prepare("
    SELECT
        CASE
            WHEN categoria IS NULL OR categoria = '' THEN 'Sin categoría'
            ELSE categoria
        END AS categoria,
        SUM(cantidad) AS total
    FROM gastos
    WHERE grupo_id = ?
    GROUP BY categoria
    ORDER BY categoria
");
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$res_cat = $stmt->get_result();

$catLabels = [];
$catData   = [];

while ($row = $res_cat->fetch_assoc()) {
    $catLabels[] = $row['categoria'];
    $catData[]   = (float)$row['total'];
}
$stmt->close();

/*     Evolución mensual   */
$stmt = $conexion->prepare("
    SELECT DATE_FORMAT(fecha, '%Y-%m') AS mes, SUM(cantidad) AS total
    FROM gastos
    WHERE grupo_id = ?
    GROUP BY mes
    ORDER BY mes
");
$stmt->bind_param("i", $grupo_id);
$stmt->execute();
$res_mes = $stmt->get_result();

$mesLabels = [];
$mesData   = [];

while ($row = $res_mes->fetch_assoc()) {
    $mesLabels[] = $row['mes'];
    $mesData[]   = (float)$row['total'];
}
$stmt->close();

/*  Ver si hay datos   */
$hayDatosEstadisticas = !empty($usrData) || !empty($catData) || !empty($mesData);

/* Titulo*/
$tituloPagina = "Estadísticas del grupo";

include "includes/header.php";
include "vistas/estadisticas_vista.php";
include "includes/footer.php";
