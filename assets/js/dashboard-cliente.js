// Dashboard Cliente - JavaScript Sencillo y Responsivo

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar todas las funcionalidades
    initDashboard();
});

function initDashboard() {
    // Efectos suaves de entrada
    animateElements();
    
    // Hacer tablas interactivas
    enhanceTable();
    
    // Configurar formularios
    initForms();
    
    // Responsive móvil
    initMobile();
    
    // Agregar interactividad a botones
    enhanceButtons();
    
    // Inicializar header
    initHeader();
}

// Animaciones suaves de entrada
function animateElements() {
    const elements = document.querySelectorAll('.stat-card, .card');
    
    elements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(10px)';
        
        setTimeout(() => {
            element.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, index * 50);
    });
    
    // Animar números de estadísticas
    animateNumbers();
}

// Animar contadores de estadísticas
function animateNumbers() {
    const numbers = document.querySelectorAll('.stat-number');
    
    numbers.forEach(numberElement => {
        const finalValue = parseInt(numberElement.textContent) || 0;
        let currentValue = 0;
        const increment = Math.ceil(finalValue / 30);
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            numberElement.textContent = currentValue;
        }, 30);
    });
}

// Hacer tablas más interactivas
function enhanceTable() {
    const rows = document.querySelectorAll('.table tbody tr');
    
    rows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = 'var(--gray-50)';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
        
        row.addEventListener('click', function() {
            // Remover selección anterior
            rows.forEach(r => r.classList.remove('table-selected'));
            // Agregar nueva selección
            this.classList.add('table-selected');
        });
    });
}

// Configurar formularios con validación
function initForms() {
    const configForm = document.querySelector('#configForm');
    if (configForm) {
        configForm.addEventListener('submit', function(e) {
            if (!validateConfigForm()) {
                e.preventDefault();
            }
        });
    }
    
    // Validación en tiempo real para campos de contraseña
    const passwordFields = document.querySelectorAll('input[type="password"]');
    passwordFields.forEach(field => {
        field.addEventListener('input', validatePasswordField);
    });
}

// Validar formulario de configuración
function validateConfigForm() {
    const password = document.getElementById('nueva_clave');
    const confirmPassword = document.getElementById('confirmar_clave');
    
    if (password && confirmPassword) {
        if (password.value !== confirmPassword.value) {
            showNotification('Las contraseñas no coinciden', 'error');
            return false;
        }
        
        if (password.value.length > 0 && password.value.length < 6) {
            showNotification('La contraseña debe tener al menos 6 caracteres', 'error');
            return false;
        }
    }
    
    showNotification('Guardando cambios...', 'info');
    return true;
}

// Validar campo de contraseña en tiempo real
function validatePasswordField(e) {
    const field = e.target;
    const value = field.value;
    
    // Remover mensajes anteriores
    const existingMessage = field.parentNode.querySelector('.field-message');
    if (existingMessage) {
        existingMessage.remove();
    }
    
    if (value.length > 0 && value.length < 6) {
        showFieldMessage(field, 'Mínimo 6 caracteres', 'warning');
    }
}

// Mostrar mensaje en campo específico
function showFieldMessage(field, message, type) {
    const messageDiv = document.createElement('div');
    messageDiv.className = `field-message text-${type}`;
    messageDiv.style.fontSize = '0.8rem';
    messageDiv.style.marginTop = '0.25rem';
    messageDiv.textContent = message;
    
    field.parentNode.appendChild(messageDiv);
}

// Funcionalidades móviles
function initMobile() {
    // Detectar dispositivo móvil
    if (window.innerWidth <= 768) {
        document.body.classList.add('mobile-device');
        optimizeForMobile();
    }
    
    // Responsive al cambiar tamaño
    window.addEventListener('resize', function() {
        if (window.innerWidth <= 768) {
            document.body.classList.add('mobile-device');
            optimizeForMobile();
        } else {
            document.body.classList.remove('mobile-device');
        }
    });
}

// Optimizar para móviles
function optimizeForMobile() {
    // Hacer scroll horizontal en tablas
    const tables = document.querySelectorAll('.table-container');
    tables.forEach(table => {
        table.style.overflowX = 'auto';
        table.style.webkitOverflowScrolling = 'touch';
    });
    
    // Simplificar navegación
    const quickActions = document.querySelector('.quick-actions');
    if (quickActions) {
        quickActions.style.gridTemplateColumns = '1fr';
    }
}

// Mejorar botones con efectos
function enhanceButtons() {
    const buttons = document.querySelectorAll('.btn');
    
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Efecto de click suave
            this.style.transform = 'scale(0.98)';
            setTimeout(() => {
                this.style.transform = '';
            }, 100);
        });
    });
}

// Inicializar header responsivo
function initHeader() {
    const navbar = document.querySelector('.navbar');
    const userInfo = document.querySelector('.user-info');
    
    if (navbar && userInfo) {
        // Ajustar header en pantallas pequeñas
        function adjustHeader() {
            if (window.innerWidth <= 640) {
                navbar.style.flexDirection = 'column';
                navbar.style.textAlign = 'center';
                userInfo.style.alignItems = 'center';
            } else {
                navbar.style.flexDirection = 'row';
                navbar.style.textAlign = 'left';
                userInfo.style.alignItems = 'flex-end';
            }
        }
        
        adjustHeader();
        window.addEventListener('resize', adjustHeader);
    }
}

// Sistema de notificaciones sencillo
function showNotification(message, type = 'info') {
    // Remover notificación anterior
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    const notification = document.createElement('div');
    notification.className = `notification alert alert-${type}`;
    notification.style.position = 'fixed';
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '1000';
    notification.style.minWidth = '300px';
    notification.style.boxShadow = '0 4px 6px rgba(0, 0, 0, 0.1)';
    notification.textContent = message;
    
    document.body.appendChild(notification);
    
    // Auto-remover después de 4 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    }, 4000);
}

// Función para refrescar dashboard (opcional)
function refreshDashboard() {
    showNotification('Actualizando datos...', 'info');
    
    // Simular carga
    setTimeout(() => {
        location.reload();
    }, 1000);
}

// Utilidades adicionales
function formatCurrency(amount) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP'
    }).format(amount);
}

function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    });
}
