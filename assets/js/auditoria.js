document.addEventListener('DOMContentLoaded', () => {
    const dataScript = document.getElementById('auditoria-data');
    if (!dataScript) return;
    let payload = {};
    try {
        payload = JSON.parse(dataScript.textContent || '{}');
    } catch (e) {
        console.error('No se pudo parsear auditoria-data', e);
        return;
    }

    // Gráfico de acciones
    const acciones = payload.acciones || [];
    if (acciones.length) {
        const ctxAcciones = document.getElementById('accionesChart');
        if (ctxAcciones) {
            new Chart(ctxAcciones, {
                type: 'bar',
                data: {
                    labels: acciones.map(item => item.tipo),
                    datasets: [{
                        label: 'Cantidad de acciones',
                        data: acciones.map(item => Number(item.cantidad)),
                        backgroundColor: ['#6a5af9','#4ade80','#f87171','#facc15','#60a5fa'],
                        borderRadius: 6
                    }]
                },
                options: { responsive: true, scales: { y: { beginAtZero: true } } }
            });
        }
    }

    // Gráfico de actividad
    const actividad = payload.actividad || {};
    const ctxActividad = document.getElementById('actividadChart');
    if (ctxActividad && Array.isArray(actividad.labels) && Array.isArray(actividad.data)) {
        new Chart(ctxActividad, {
            type: 'line',
            data: {
                labels: actividad.labels,
                datasets: [{
                    label: 'Eventos registrados',
                    data: actividad.data,
                    fill: true,
                    backgroundColor: 'rgba(106,90,249,0.12)',
                    borderColor: '#6a5af9',
                    tension: 0.3,
                    pointRadius: 4
                }]
            },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    }
});
