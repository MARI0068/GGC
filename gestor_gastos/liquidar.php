<?php
session_start();
include("includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) { header("Location: login.php"); exit; }
if (!isset($_GET["grupo_id"])) { die("Grupo no especificado."); }

$usuario_id = $_SESSION["usuario_id"];
$grupo_id   = intval($_GET["grupo_id"]);

/* Verificar pertenencia */
$sql = "SELECT 1 FROM usuarios_grupos WHERE grupo_id=? AND usuario_id=?";
$stmt=$conexion->prepare($sql); $stmt->bind_param("ii",$grupo_id,$usuario_id);
$stmt->execute(); if($stmt->get_result()->num_rows==0){ die("Sin acceso."); }

/* Miembros */
$sql = "SELECT u.id, u.nombre 
        FROM usuarios u INNER JOIN usuarios_grupos ug ON u.id=ug.usuario_id
        WHERE ug.grupo_id=?";
$stmt=$conexion->prepare($sql); $stmt->bind_param("i",$grupo_id);
$stmt->execute(); $miembros=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* Total grupo */
$sql = "SELECT SUM(cantidad) total FROM gastos WHERE grupo_id=?";
$stmt=$conexion->prepare($sql); $stmt->bind_param("i",$grupo_id);
$stmt->execute(); $row=$stmt->get_result()->fetch_assoc();
$total = $row && $row["total"] ? floatval($row["total"]) : 0.0;
$cuota = count($miembros)>0 ? $total / count($miembros) : 0;

/* Pagos por usuario */
$sql="SELECT usuario_id, SUM(cantidad) pagado FROM gastos WHERE grupo_id=? GROUP BY usuario_id";
$stmt=$conexion->prepare($sql); $stmt->bind_param("i",$grupo_id);
$stmt->execute(); $pagos=$stmt->get_result()->fetch_all(MYSQLI_ASSOC);

$pagado_por = [];
foreach($pagos as $p){ $pagado_por[$p["usuario_id"]] = floatval($p["pagado"]); }

/* Saldos */
$saldos = [];
$nombres = [];
foreach($miembros as $m){
  $id = $m["id"];
  $nombres[$id] = $m["nombre"];
  $pagado = isset($pagado_por[$id]) ? $pagado_por[$id] : 0.0;
  $saldos[$id] = round($pagado - $cuota, 2); // positivo cobra, negativo paga
}

/* Algoritmo de liquidación mínima (greedy) */
$deudores = [];  // saldo < 0
$acreedores = []; // saldo > 0
foreach($saldos as $id=>$s){
  if ($s < -0.004) $deudores[] = ["id"=>$id, "resta"=>round(-$s,2)];
  if ($s >  0.004) $acreedores[] = ["id"=>$id, "resta"=>round( $s,2)];
}
usort($deudores,   fn($a,$b)=> $b["resta"] <=> $a["resta"]);
usort($acreedores, fn($a,$b)=> $b["resta"] <=> $a["resta"]);

$transferencias = [];
$i=0; $j=0;
while ($i < count($deudores) && $j < count($acreedores)){
  $paga = min($deudores[$i]["resta"], $acreedores[$j]["resta"]);
  $transferencias[] = [
    "de"   => $nombres[$deudores[$i]["id"]],
    "a"    => $nombres[$acreedores[$j]["id"]],
    "monto"=> round($paga,2)
  ];
  $deudores[$i]["resta"]   = round($deudores[$i]["resta"] - $paga, 2);
  $acreedores[$j]["resta"] = round($acreedores[$j]["resta"] - $paga, 2);
  if ($deudores[$i]["resta"] <= 0.004) $i++;
  if ($acreedores[$j]["resta"] <= 0.004) $j++;
}

/* Pasar a vista */
$total = $total;
$cuota = $cuota;
include("vistas/liquidar_vista.php");
