<div class="contenedor">
    <h2>Estadísticas del grupo</h2>

    <?php if (!$hayDatosEstadisticas): ?>

        <div class="tarjeta-estadistica tarjeta-sin-datos">
            <p>Este grupo aún no tiene ningún gasto registrado.</p>
            <p>Añade al menos un gasto para poder generar las gráficas.</p>
        </div>

    <?php else: ?>

        <div class="contenedor-estadisticas">
            <!-- Gasto por categoría -->
            <div class="tarjeta-estadistica">
                <h3>Gasto por categoría</h3>
                <canvas id="chartCategorias"></canvas>
            </div>

            <!-- Gasto por usuario -->
            <div class="tarjeta-estadistica">
                <h3>Gasto por usuario</h3>
                <canvas id="chartUsuarios"></canvas>
            </div>

            <!-- Evolución mensual -->
            <div class="tarjeta-estadistica">
                <h3>Evolución mensual</h3>
                <canvas id="chartMeses"></canvas>
            </div>
        </div>

        
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

        <script>
            // Datos desde PHP
            const catLabels = <?= json_encode($catLabels) ?>;
            const catData   = <?= json_encode($catData) ?>;

            const usrLabels = <?= json_encode($usrLabels) ?>;
            const usrData   = <?= json_encode($usrData) ?>;

            const mesLabels = <?= json_encode($mesLabels) ?>;
            const mesData   = <?= json_encode($mesData) ?>;

           
            const colores = [
                '#4e79a7', '#f28e2b', '#e15759', '#76b7b2',
                '#59a14f', '#edc948', '#b07aa1', '#ff9da7',
                '#9c755f', '#bab0ab'
            ];

            // Gasto por categoría
            const ctxCat = document.getElementById('chartCategorias').getContext('2d');
            new Chart(ctxCat, {
                type: 'pie',
                data: {
                    labels: catLabels,
                    datasets: [{
                        data: catData,
                        backgroundColor: colores,
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true        
                }
            });

            // Gasto por usuario
            const ctxUsr = document.getElementById('chartUsuarios').getContext('2d');
            new Chart(ctxUsr, {
                type: 'bar',
                data: {
                    labels: usrLabels,
                    datasets: [{
                        label: 'Gasto por usuario',
                        data: usrData,
                        backgroundColor: colores,
                        borderColor: '#333',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });

            // Evolución mensual
            const ctxMes = document.getElementById('chartMeses').getContext('2d');
            new Chart(ctxMes, {
                type: 'line',
                data: {
                    labels: mesLabels,
                    datasets: [{
                        label: 'Gasto mensual',
                        data: mesData,
                        borderColor: '#4e79a7',
                        backgroundColor: 'rgba(78, 121, 167, 0.2)',
                        borderWidth: 3,
                        tension: 0.3,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        </script>

    <?php endif; ?>

    <a href="balances.php?grupo_id=<?= $grupo_id ?>" class="boton boton-volver">Volver</a>
</div>
