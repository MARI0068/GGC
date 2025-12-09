<?php include("includes/header.php"); ?>
<div class="contenedor">
  <h2>Liquidación sugerida</h2>
  <p>Total: <?= number_format($total,2,",",".") ?> € · Cuota: <?= number_format($cuota,2,",",".") ?> €</p>

  <?php if (empty($transferencias)): ?>
    <div class="alerta info">No hay deudas pendientes. Todo equilibrado.</div>
  <?php else: ?>
    <table class="tabla">
      <thead><tr><th>Quien paga</th><th>A quién</th><th>Importe</th></tr></thead>
      <tbody>
        <?php foreach($transferencias as $t): ?>
          <tr>
            <td><?= htmlspecialchars($t["de"]) ?></td>
            <td><?= htmlspecialchars($t["a"]) ?></td>
            <td><strong><?= number_format($t["monto"],2,",",".") ?> €</strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>

  <div style="margin-top:16px">
    <a class="boton boton-invitar" href="export_csv.php?grupo_id=<?= intval($_GET['grupo_id']) ?>&tipo=liquidacion">Exportar Excel</a>
    <a class="boton boton-secundario" href="balances.php?grupo_id=<?= intval($_GET['grupo_id']) ?>">Volver al balance</a>
  </div>
</div>
<?php include("includes/footer.php"); ?>

