<?php include("includes/header.php"); ?>
<div class="contenedor">
  <h2>Balance del grupo</h2>

  <div class="form-barra">
    <div><strong>Total grupo:</strong> <?= number_format($total,2,",",".") ?> €</div>
    <div><strong>Cuota por persona:</strong> <?= number_format($cuota,2,",",".") ?> €</div>
    <div style="margin-left:auto;">
      <a class="boton boton-balance" href="liquidar.php?grupo_id=<?= intval($_GET['grupo_id']) ?>">Sugerir liquidación</a>
      <a class="boton boton-invitar" href="export_csv.php?grupo_id=<?= intval($_GET['grupo_id']) ?>">Exportar Excel</a>
      <a class="boton boton-secundario" href="grupos.php">Volver</a>
    </div>
  </div>

  <table class="tabla">
    <thead>
      <tr>
        <th>Miembro</th>
        <th>Pagado</th>
        <th>Saldo</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($balances as $b): 
        $saldo = floatval($b["saldo"]);
        $clase = $saldo >= 0 ? "positivo" : "negativo";
      ?>
      <tr>
        <td><?= htmlspecialchars($b["nombre"]) ?></td>
        <td><?= number_format($b["pagado"],2,",",".") ?> €</td>
        <td class="<?= $clase ?>"><?= number_format($saldo,2,",",".") ?> €</td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div style="margin-top:20px">
  <h3>Saldos por miembro</h3>
  <canvas id="chartSaldos"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const saldosLabels = <?= json_encode(array_map(fn($b)=>$b['nombre'],$balances), JSON_UNESCAPED_UNICODE) ?>;
const saldosData   = <?= json_encode(array_map(fn($b)=>round((float)$b['saldo'],2), $balances)) ?>;

new Chart(document.getElementById('chartSaldos'), {
  type: 'bar',
  data: { labels: saldosLabels, datasets: [{ label:'€ saldo', data: saldosData, borderWidth:2 }] },
  options: { responsive:true, scales:{ y:{ beginAtZero:true } }, plugins:{ legend:{ display:false } } }
});
</script>
</div>
<?php include("includes/footer.php"); ?>
