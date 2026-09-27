<?php
require_once __DIR__ . '/../includes/auth-admin.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Kosheru Admin</title>
    <link rel="stylesheet" href="../css/admin.css">
    <link rel="stylesheet" href="../css/dashboard.css">
    <link rel="stylesheet" href="../css/responsive-admin.css">
    <link rel="icon" href="img/logos/Isotipo transparente.png" type="image/png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&family=Outfit:wght@600;800;900&display=swap" rel="stylesheet">
    <!-- Chart.js para los gráficos del dashboard -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
</head>
<body class="admin-body">

    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <div class="admin-main-wrapper">
        <?php include __DIR__ . '/../includes/navbar.php'; ?>

        <main class="admin-content">
            <h1 class="admin-page-title">Dashboard General</h1>

            <!-- Filtro por Rango de Fechas -->
            <div class="dashboard-filter-bar">
                <div class="form-group" style="margin: 0; min-width: 180px;">
                    <label for="filterDesde" style="font-size: 0.8rem;">Desde</label>
                    <input type="date" id="filterDesde" style="padding: 0.5rem; font-size: 0.85rem;">
                </div>
                <div class="form-group" style="margin: 0; min-width: 180px;">
                    <label for="filterHasta" style="font-size: 0.8rem;">Hasta</label>
                    <input type="date" id="filterHasta" style="padding: 0.5rem; font-size: 0.85rem;">
                </div>
                <button type="button" class="btn-primary" id="btnFilterDashboard" style="padding: 0.6rem 1.4rem; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <img src="../../img/icons/search-big.svg" alt="Filtrar" class="inline-icon" aria-hidden="true"> Filtrar
                </button>
                <button type="button" class="btn-secondary" id="btnClearDashboard" style="padding: 0.6rem 1.4rem; font-size: 0.85rem; color: var(--color-cafe-oscuro); border-color: #ccc; display: inline-flex; align-items: center; gap: 0.35rem;">
                    <img src="../../img/icons/clock.svg" alt="Limpiar" class="inline-icon" aria-hidden="true"> Limpiar
                </button>
            </div>

            <!-- Grilla de Tarjetas de Métricas -->
            <div class="dashboard-grid">
                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/user.svg" alt="Usuarios" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Usuarios Registrados</div>
                        <div class="metric-value" id="mTotalUsers">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/basket.svg" alt="Productos" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Productos Disponibles</div>
                        <div class="metric-value" id="mProdAvail">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/cart.svg" alt="Agotados" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Productos Agotados</div>
                        <div class="metric-value" style="color: var(--color-error);" id="mProdOut">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/cog.svg" alt="Poco stock" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Poco Stock (<=5)</div>
                        <div class="metric-value" style="color: var(--color-alerta);" id="mProdLow">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/cart.svg" alt="Compras del Día" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Compras del Día</div>
                        <div class="metric-value" id="mOrdersToday">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/dollar-circle.svg" alt="Ventas del Día" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Ventas del Día</div>
                        <div class="metric-value" style="color: var(--color-exito);" id="mSalesToday">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/chart-sine.svg" alt="Ventas del Mes" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Ventas del Mes</div>
                        <div class="metric-value" style="color: var(--color-rojo-kosheru);" id="mSalesMonth">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/dollar-circle.svg" alt="Ingresos Totales" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Ingresos Totales</div>
                        <div class="metric-value" style="color: var(--color-rojo-oscuro);" id="mTotalRevenue">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/credit-card-alt.svg" alt="Facturas Generadas" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Facturas Generadas</div>
                        <div class="metric-value" id="mTotalInvoices">-</div>
                    </div>
                </div>

                <div class="metric-card">
                    <div class="metric-icon-wrap"><img src="../../img/icons/envelope-alt.svg" alt="Sugerencias Pendientes" class="inline-icon" aria-hidden="true"></div>
                    <div class="metric-data">
                        <div class="metric-label">Sugerencias Pendientes</div>
                        <div class="metric-value" id="mPendingSugg">-</div>
                    </div>
                </div>
            </div>

            <!-- Gráficos y Tendencias -->
            <h2 class="charts-section-title">Gráficos y Tendencias</h2>
            <div class="charts-grid">
                <div class="chart-card">
                    <div class="chart-card-title">Ventas de los últimos 14 días</div>
                    <div class="chart-card-canvas-wrap">
                        <canvas id="chartSalesTrend"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-card-title">Top 5 productos más vendidos</div>
                    <div class="chart-card-canvas-wrap">
                        <canvas id="chartTopProducts"></canvas>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="chart-card-title">Ventas por categoría</div>
                    <div class="chart-card-canvas-wrap">
                        <canvas id="chartSalesByCategory"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="../../js/app.js"></script>
    <script src="../js/admin.js"></script>
    <script>
        // Instancias de Chart.js guardadas para poder destruirlas y
        // volver a dibujarlas cuando se recarguen las métricas (evita que
        // se apilen gráficos fantasma encima uno del otro).
        let chartSalesTrendInstance = null;
        let chartTopProductsInstance = null;
        let chartSalesByCategoryInstance = null;

        // Paleta de Kosheru para los gráficos (coherente con el resto del panel)
        const CHART_COLORS = {
            rojo: '#c0392b',
            naranja: '#e67e22',
            dorado: '#d4a017',
            oliva: '#6b7a3c',
            cafe: '#4a2c17',
            grid: 'rgba(74, 44, 23, 0.08)',
            palette: ['#c0392b', '#e67e22', '#d4a017', '#6b7a3c', '#922b21', '#a8790f']
        };

        function renderSalesTrendChart(salesTrend) {
            const ctx = document.getElementById('chartSalesTrend');
            if (!ctx) return;

            const labels = salesTrend.map(d => {
                const parts = d.date.split('-'); // YYYY-MM-DD
                return `${parts[2]}/${parts[1]}`;
            });
            const data = salesTrend.map(d => d.total);

            if (chartSalesTrendInstance) chartSalesTrendInstance.destroy();

            chartSalesTrendInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Ventas (COP)',
                        data: data,
                        borderColor: CHART_COLORS.rojo,
                        backgroundColor: 'rgba(192, 57, 43, 0.12)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.35,
                        pointRadius: 3,
                        pointBackgroundColor: CHART_COLORS.rojo
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: CHART_COLORS.grid },
                            ticks: {
                                callback: (value) => '$' + new Intl.NumberFormat('es-CO', { notation: 'compact' }).format(value)
                            }
                        },
                        x: { grid: { display: false } }
                    }
                }
            });
        }

        function renderTopProductsChart(topProducts) {
            const ctx = document.getElementById('chartTopProducts');
            const wrap = ctx ? ctx.closest('.chart-card') : null;
            if (!ctx) return;

            if (!topProducts || topProducts.length === 0) {
                ctx.closest('.chart-card-canvas-wrap').innerHTML = '<div class="chart-card-empty">Aún no hay ventas registradas para mostrar este gráfico.</div>';
                return;
            }

            const labels = topProducts.map(p => p.name.length > 22 ? p.name.slice(0, 20) + '…' : p.name);
            const data = topProducts.map(p => p.qty);

            if (chartTopProductsInstance) chartTopProductsInstance.destroy();

            chartTopProductsInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Unidades vendidas',
                        data: data,
                        backgroundColor: CHART_COLORS.naranja,
                        borderRadius: 6,
                        maxBarThickness: 40
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { beginAtZero: true, grid: { color: CHART_COLORS.grid } },
                        y: { grid: { display: false } }
                    }
                }
            });
        }

        function renderSalesByCategoryChart(salesByCategory) {
            const ctx = document.getElementById('chartSalesByCategory');
            if (!ctx) return;

            if (!salesByCategory || salesByCategory.length === 0) {
                ctx.closest('.chart-card-canvas-wrap').innerHTML = '<div class="chart-card-empty">Aún no hay ventas registradas para mostrar este gráfico.</div>';
                return;
            }

            const labels = salesByCategory.map(c => c.category);
            const data = salesByCategory.map(c => c.total);

            if (chartSalesByCategoryInstance) chartSalesByCategoryInstance.destroy();

            chartSalesByCategoryInstance = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: CHART_COLORS.palette,
                        borderColor: '#ffffff',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } }
                    }
                }
            });
        }

        async function loadMetrics(desde = '', hasta = '') {
            try {
                let url = '../api/dashboard.php?';
                if (desde) url += `desde=${encodeURIComponent(desde)}&`;
                if (hasta) url += `hasta=${encodeURIComponent(hasta)}&`;

                const res = await fetch(url);
                const data = await res.json();

                if (data.success) {
                    const m = data.metrics;
                    document.getElementById('mTotalUsers').textContent = m.total_users;
                    document.getElementById('mProdAvail').textContent = m.total_available;
                    document.getElementById('mProdOut').textContent = m.total_out_of_stock;
                    document.getElementById('mProdLow').textContent = m.total_low_stock;
                    document.getElementById('mOrdersToday').textContent = m.orders_today;
                    document.getElementById('mSalesToday').textContent = m.sales_today_formatted;
                    document.getElementById('mSalesMonth').textContent = m.sales_month_formatted;
                    document.getElementById('mTotalRevenue').textContent = m.total_revenue_formatted;
                    document.getElementById('mTotalInvoices').textContent = m.total_invoices;
                    document.getElementById('mPendingSugg').textContent = m.pending_suggestions;

                    if (data.charts) {
                        renderSalesTrendChart(data.charts.sales_trend || []);
                        renderTopProductsChart(data.charts.top_products || []);
                        renderSalesByCategoryChart(data.charts.sales_by_category || []);
                    }
                }
            } catch (e) {
                console.error('Error cargando métricas:', e);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            loadMetrics();

            document.getElementById('btnFilterDashboard').addEventListener('click', () => {
                const desde = document.getElementById('filterDesde').value;
                const hasta = document.getElementById('filterHasta').value;
                loadMetrics(desde, hasta);
            });

            document.getElementById('btnClearDashboard').addEventListener('click', () => {
                document.getElementById('filterDesde').value = '';
                document.getElementById('filterHasta').value = '';
                loadMetrics();
            });
        });
    </script>
</body>
</html>
