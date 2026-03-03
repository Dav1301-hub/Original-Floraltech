// Inicializar tooltips
document.addEventListener('DOMContentLoaded', function() {
    // Tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Gráfico de métodos de pago
    if (document.getElementById('metodosPagoChart')) {
        var ctx = document.getElementById('metodosPagoChart').getContext('2d');
        var data = JSON.parse(document.getElementById('metodosPagoChart').dataset.chartData);
        
        var chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: [
                        '#4e73df',
                        '#1cc88a',
                        '#36b9cc',
                        '#f6c23e',
                        '#e74a3b'
                    ],
                    hoverBackgroundColor: [
                        '#2e59d9',
                        '#17a673',
                        '#2c9faf',
                        '#dda20a',
                        '#be2617'
                    ],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: false,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.raw || 0;
                                return label + ': $' + value.toFixed(2);
                            }
                        }
                    },
                },
                cutout: '80%',
            },
        });
    }
    
    // Manejar el formulario de filtros
    document.querySelectorAll('.filter-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Aquí iría la lógica para aplicar los filtros
            console.log('Filtros aplicados');
        });
    });
});