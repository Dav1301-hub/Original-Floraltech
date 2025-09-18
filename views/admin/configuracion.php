<?php
// Vista de Configuración Administrativa para Floristería
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración - FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h1 class="mb-4">Configuración del Negocio</h1>
    <form class="card p-4 mx-auto" style="max-width: 500px;">
        <div class="mb-3">
            <label class="form-label">Nombre de la floristería</label>
            <input type="text" class="form-control" value="FloralTech Boutique">
        </div>
        <div class="mb-3">
            <label class="form-label">Dirección</label>
            <input type="text" class="form-control" value="Calle Falsa 123, Ciudad">
        </div>
        <div class="mb-3">
            <label class="form-label">Horarios de apertura</label>
            <input type="text" class="form-control" value="Lunes a Sábado 9:00-19:00">
        </div>
        <div class="mb-3">
            <label class="form-label">Teléfono</label>
            <input type="text" class="form-control" value="555-1234">
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="contacto@floraltech.com">
        </div>
        <button class="btn btn-primary w-100">Guardar cambios</button>
    </form>
</div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
