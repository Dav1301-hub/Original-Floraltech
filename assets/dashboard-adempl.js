<script>
    let empleados = [
        {id:101, nombre:"María", apellido:"González", documento:"12345678", cargo:"Florista Senior", ingreso:"2020-03-15", contrato:"Indefinido", estado:"Activo"},
        {id:102, nombre:"Juan", apellido:"Pérez", documento:"87654321", cargo:"Diseñador floral", ingreso:"2021-08-22", contrato:"Fijo", estado:"Activo"},
        {id:103, nombre:"Ana", apellido:"Rodríguez", documento:"11223344", cargo:"Vendedora", ingreso:"2022-01-05", contrato:"Temporal", estado:"Activo"},
        {id:104, nombre:"Carlos", apellido:"López", documento:"44332211", cargo:"Entregas", ingreso:"2021-11-10", contrato:"Obra", estado:"Inactivo"}
    ];

    // Renderizar empleados en la tabla
    function renderEmpleados() {
        const tbody = document.querySelector("#empleados tbody");
        tbody.innerHTML = "";
        empleados.forEach(emp => {
            let tr = document.createElement("tr");
            tr.innerHTML = `
                <td>${emp.id}</td>
                <td>${emp.nombre} ${emp.apellido}</td>
                <td>${emp.documento}</td>
                <td>${emp.cargo}</td>
                <td>${emp.ingreso}</td>
                <td><span class="badge bg-success">${emp.contrato}</span></td>
                <td><span class="badge ${emp.estado==="Activo"?"bg-success":"bg-danger"}">${emp.estado}</span></td>
                <td class="actions-column">
                    <button class="btn btn-sm btn-outline-primary" onclick="editarEmpleado(${emp.id})"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-danger" onclick="eliminarEmpleado(${emp.id})"><i class="fas fa-trash"></i></button>
                    <button class="btn btn-sm btn-outline-info" onclick="verEmpleado(${emp.id})"><i class="fas fa-eye"></i></button>
                </td>
            `;
            tbody.appendChild(tr);
        });
    }

    // Guardar empleado en la BD
function guardarEmpleado() {
    let data = {
        nombre: document.getElementById("nombre").value,
        apellido: document.getElementById("apellido").value,
        documento: document.getElementById("documento").value,
        cargo: document.getElementById("cargo").value,
        ingreso: document.getElementById("fecha_ingreso").value,
        contrato: document.getElementById("tipo_contrato").value,
        estado: document.getElementById("estado").value
    };

    fetch("guardarEmpleado.php", {
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        renderEmpleados(); // refresca
    });
} 
    // Eliminar empleado
    function eliminarEmpleado(id) {
        if(confirm("¿Seguro que quieres eliminar este empleado?")) {
            empleados = empleados.filter(emp => emp.id !== id);
            renderEmpleados();
        }
    }

    // Ver empleado
    function verEmpleado(id) {
        let emp = empleados.find(e=>e.id===id);
        alert(`Empleado: ${emp.nombre} ${emp.apellido}\nDocumento: ${emp.documento}\nCargo: ${emp.cargo}\nEstado: ${emp.estado}`);
    }

    // Editar empleado
    function editarEmpleado(id) {
        let emp = empleados.find(e=>e.id===id);
        document.getElementById("edit_id").value = emp.id;
        document.getElementById("edit_nombre").value = emp.nombre;
        document.getElementById("edit_apellido").value = emp.apellido;
        document.getElementById("edit_documento").value = emp.documento;
        document.getElementById("edit_cargo").value = emp.cargo;
        document.getElementById("edit_fecha_ingreso").value = emp.ingreso;
        document.getElementById("edit_tipo_contrato").value = emp.contrato.toLowerCase();
        document.getElementById("edit_estado").value = emp.estado.toLowerCase();
        new bootstrap.Modal(document.getElementById("editarEmpleadoModal")).show();
    }

    // Actualizar empleado
    function actualizarEmpleado() {
        let id = parseInt(document.getElementById("edit_id").value);
        let emp = empleados.find(e=>e.id===id);
        emp.nombre = document.getElementById("edit_nombre").value;
        emp.apellido = document.getElementById("edit_apellido").value;
        emp.documento = document.getElementById("edit_documento").value;
        emp.cargo = document.getElementById("edit_cargo").value;
        emp.ingreso = document.getElementById("edit_fecha_ingreso").value;
        emp.contrato = document.getElementById("edit_tipo_contrato").value;
        emp.estado = document.getElementById("edit_estado").value;
        renderEmpleados();
        bootstrap.Modal.getInstance(document.getElementById("editarEmpleadoModal")).hide();
    }

    // Inicializar
    document.addEventListener('DOMContentLoaded', function() {
        renderEmpleados();
        document.querySelector("#nuevoEmpleadoModal .btn-primary").addEventListener("click", guardarEmpleado);
        document.querySelector("#editarEmpleadoModal .btn-primary").addEventListener("click", actualizarEmpleado);
    });
</script>
