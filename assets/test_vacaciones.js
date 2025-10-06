// Test directo para el formulario de vacaciones
console.log('Script de prueba vacaciones cargado');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded');
    
    // Verificar que el formulario existe
    const form = document.getElementById('formNuevaVacacion');
    console.log('Formulario encontrado:', form);
    
    if (form) {
        // Evento directo en el formulario
        form.onsubmit = function(e) {
            e.preventDefault();
            console.log('Formulario enviado - método directo');
            
            // Obtener datos del formulario
            const formData = new FormData(form);
            formData.append('action', 'create');
            
            // Log de datos
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            // Enviar datos
            fetch('assets/ajax/ajax_vacacion.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Response data:', data);
                    if (data.success) {
                        alert('Vacación guardada exitosamente');
                        location.reload();
                    } else {
                        alert('Error: ' + (data.error || 'Error desconocido'));
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    alert('Error en la respuesta del servidor: ' + text);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error de conexión: ' + error.message);
            });
            
            return false;
        };
        
        // También evento click en el botón
        const submitBtn = form.querySelector('button[type="submit"]');
        if (submitBtn) {
            console.log('Botón submit encontrado:', submitBtn);
            submitBtn.onclick = function(e) {
                console.log('Botón submit clickeado');
                form.onsubmit(e);
            };
        }
    }
});