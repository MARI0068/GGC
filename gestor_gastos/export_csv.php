<?php
session_start();
include("includes/conexion.php");

if (!isset($_SESSION["usuario_id"])) { header("Location: login.php"); exit; }
if (!isset($_GET["grupo_id"])) { die("Grupo no especificado."); }

$usuario_id = $_SESSION["usuario_id"];
$grupo_id   = intval($_GET["grupo_id"]);
$tipo       = isset($_GET["tipo"]) ? $_GET["tipo"] : "balances";

/* Verificar acceso */
$sql="SELECT 1 FROM usuarios_grupos WHERE grupo_id=? AND usuario_id=?";
$stmt=$conexion->prepare($sql); $stmt->bind_param("ii",$grupo_id,$usuario_id);
$stmt->execute(); if($stmt->get_result()->num_rows==0){ die("Sin acceso."); }

header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename={$tipo}_grupo_$grupo_id.csv");
$out = fopen("php://output","w");
fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); 

if ($tipo === "liquidacion") {
 
  // Miembros
  $sql="SELECT u.id,u.nombre FROM usuarios u INNER JOIN usuarios_grupos ug ON u.id=ug.usuario_id WHERE ug.grupo_id=?";
  $st=$conexion->prepare($sql); $st->bind_param("i",$grupo_id); $st->execute();
  $mi=$st->get_result()->fetch_all(MYSQLI_ASSOC);

  // Total y cuota
  $st=$conexion->prepare("SELECT SUM(cantidad) total FROM gastos WHERE grupo_id=?");
  $st->bind_param("i",$grupo_id); $st->execute(); $row=$st->get_result()->fetch_assoc();
  $total = $row && $row["total"] ? floatval($row["total"]) : 0.0;
  $cuota = count($mi)>0 ? $total / count($mi) : 0;

  // Pagos
  $st=$conexion->prepare("SELECT usuario_id, SUM(cantidad) pagado FROM gastos WHERE grupo_id=? GROUP BY usuario_id");
  $st->bind_param("i",$grupo_id); $st->execute(); $pg=$st->get_result()->fetch_all(MYSQLI_ASSOC);
  $pagado_por=[]; foreach($pg as $p){ $pagado_por[$p["usuario_id"]]=floatval($p["pagado"]); }
  $nombres=[]; $saldos=[];
  foreach($mi as $m){ $nombres[$m["id"]]=$m["nombre"]; $saldos[$m["id"]]=round(($pagado_por[$m["id"]]??0)-$cuota,2); }
  $de=[]; $ac=[];
  foreach($saldos as $id=>$s){ if($s<-0.004)$de[]=["id"=>$id,"resta"=>round(-$s,2)]; if($s>0.004)$ac[]=["id"=>$id,"resta"=>round($s,2)]; }
  usort($de,fn($a,$b)=>$b["resta"]<=>$a["resta"]); usort($ac,fn($a,$b)=>$b["resta"]<=>$a["resta"]);
  fputcsv($out, ["Quien paga","A quién","Importe (€)"], ";");
  $i=0;$j=0;
  while($i<count($de)&&$j<count($ac)){
    $paga=min($de[$i]["resta"],$ac[$j]["resta"]);
    fputcsv($out, [$nombres[$de[$i]["id"]], $nombres[$ac[$j]["id"]], number_format($paga,2,",","")], ";");
    $de[$i]["resta"]=round($de[$i]["resta"]-$paga,2); if($de[$i]["resta"]<=0.004)$i++;
    $ac[$j]["resta"]=round($ac[$j]["resta"]-$paga,2); if($ac[$j]["resta"]<=0.004)$j++;
  }
  exit;
}

/* CSV por defecto: balances */
fputcsv($out, ["Nombre","Pagado (€)","Saldo (€)"], ";");

/* Recalcular balances igual que en balances.php para exportar */
$sql="SELECT u.id,u.nombre FROM usuarios u INNER JOIN usuarios_grupos ug ON u.id=ug.usuario_id WHERE ug.grupo_id=?";
$st=$conexion->prepare($sql); $st->bind_param("i",$grupo_id); $st->execute();
$mi=$st->get_result()->fetch_all(MYSQLI_ASSOC);

$st=$conexion->prepare("SELECT SUM(cantidad) total FROM gastos WHERE grupo_id=?");
$st->bind_param("i",$grupo_id); $st->execute(); $row=$st->get_result()->fetch_assoc();
$total = $row && $row["total"] ? floatval($row["total"]) : 0.0;
$cuota = count($mi)>0 ? $total / count($mi) : 0;

$st=$conexion->prepare("SELECT usuario_id, SUM(cantidad) pagado FROM gastos WHERE grupo_id=? GROUP BY usuario_id");
$st->bind_param("i",$grupo_id); $st->execute(); $pg=$st->get_result()->fetch_all(MYSQLI_ASSOC);
$pagado_por=[]; foreach($pg as $p){ $pagado_por[$p["usuario_id"]]=floatval($p["pagado"]); }

foreach($mi as $m){
  $pagado = $pagado_por[$m["id"]] ?? 0.0;
  $saldo  = $pagado - $cuota;
  fputcsv($out, [$m["nombre"], number_format($pagado,2,",",""), number_format($saldo,2,",","")], ";");
}
