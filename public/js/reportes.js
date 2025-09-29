document.getElementById('periodoSelect').addEventListener('change', function() {
    const rangoFechas = document.getElementById('rangoFechas');
    rangoFechas.style.display = this.value === 'personalizado' ? 'inline-flex' : 'none';
});

// Datos para los gráficos (debe estar disponible en la vista como variables globales)
const datosEtapas = window.datosEtapas || [];
const datosOrigenes = window.datosOrigenes || [];
const datosTendencia = window.datosTendencia || [];

// Gráfico de Leads por Etapa (Doughnut)
const ctxEtapas = document.getElementById('chartEtapas').getContext('2d');
new Chart(ctxEtapas, {
    type: 'doughnut',
    data: {
        labels: datosEtapas.map(d => d.etapa),
        datasets: [{
            data: datosEtapas.map(d => d.total),
            backgroundColor: [
                '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b', '#858796'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Gráfico de Leads por Origen (Bar)
const ctxOrigenes = document.getElementById('chartOrigenes').getContext('2d');
new Chart(ctxOrigenes, {
    type: 'bar',
    data: {
        labels: datosOrigenes.map(d => d.origen),
        datasets: [{
            label: 'Leads',
            data: datosOrigenes.map(d => d.total),
            backgroundColor: '#4e73df'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } },
        plugins: { legend: { display: false } }
    }
});

// Gráfico de Tendencia (Line)
const ctxTendencia = document.getElementById('chartTendencia').getContext('2d');
new Chart(ctxTendencia, {
    type: 'line',
    data: {
        labels: datosTendencia.map(d => d.fecha),
        datasets: [
            {
                label: 'Leads',
                data: datosTendencia.map(d => d.leads),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4
            },
            {
                label: 'Conversiones',
                data: datosTendencia.map(d => d.conversiones),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: { y: { beginAtZero: true } }
    }
});

function imprimirReporte() {
    window.print();
}

function exportarExcel() {
    window.location.href = window.EXPORTAR_EXCEL_URL;
}
