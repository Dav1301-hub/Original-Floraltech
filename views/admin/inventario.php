<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title></title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<script src="https://code.jquery.com/jquery-3.7.1.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/js/"></script><script src="bootstrap.bundle.min.js"></script>
	<script src="https://cdn.datatables.net/2.3.2/js/dataTables.js"></script>
	<script src="https://cdn.datatables.net/2.3.2/js/dataTables.bootstrap5.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.3/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.3.2/css/dataTables.bootstrap5.css">
	<link rel="stylesheet" href="/assets/dashboard-admin.css">
	
	
</head>
<body>
<main class="container py-4">
    <h2 class="mb-4 fw-bold">Gestión de Inventario</h2>
    <!-- Tarjetas métricas -->
    <div class="row mb-4 g-3">
        <div class="col-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-box h2 text-primary"></i>
                    <h6 class="fw-bold mt-2">Total Productos</h6>
                    <div class="h4">150</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-exclamation-triangle h2 text-warning"></i>
                    <h6 class="fw-bold mt-2">Stock Bajo</h6>
                    <div class="h4">8</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-x-circle h2 text-danger"></i>
                    <h6 class="fw-bold mt-2">Sin Stock</h6>
                    <div class="h4">2</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="bi bi-currency-dollar h2 text-success"></i>
                    <h6 class="fw-bold mt-2">Valor Total</h6>
                    <div class="h4">$12,760,000.00</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros de Inventario -->
    <fieldset class="border rounded p-3 mb-4 bg-light">
        <legend class="float-none w-auto px-2 mb-2 fw-bold"><i class="bi bi-funnel"></i> Filtros de Inventario</legend>
        <form class="row g-2 flex-wrap align-items-end" method="get" action="">
            <div class="col-12 col-md-3">
                <label for="categoria" class="form-label mb-1">Categoría</label>
                <select id="categoria" name="categoria" class="form-select">
                    <option value="">Todas las categorías</option>
                    <!-- ...categorías dinámicas... -->
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="estado_stock" class="form-label mb-1">Estado de Stock</label>
                <select id="estado_stock" name="estado_stock" class="form-select">
                    <option value="">Todos los estados</option>
                    <option value="bajo">Bajo</option>
                    <option value="sin_stock">Sin Stock</option>
                    <option value="normal">Normal</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label for="buscar" class="form-label mb-1">Buscar</label>
                <input type="text" id="buscar" name="buscar" class="form-control" placeholder="Nombre de la flor...">
            </div>
            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                <a href="?limpiar=1" class="d-block mt-2 text-secondary">Limpiar Filtros</a>
            </div>
        </form>
    </fieldset>

    <!-- Tabla de inventario -->
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Stock</th>
                    <th>Valor Unitario</th>
                    <th>Código</th>
                    <th>Proveedor</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- ...filas dinámicas... -->
                <tr>
                    <td>Rosa</td>
                    <td>10</td>
                    <td>Bajo</td>
                    <td>$1,000</td>
                    <td>F001</td>
                    <td>Florería ABC</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        <button type="button" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <tr>
                    <td>Lirio</td>
                    <td>0</td>
                    <td>Sin Stock</td>
                    <td>$2,000</td>
                    <td>F002</td>
                    <td>Florería XYZ</td>
                    <td>
                        
                        <button type="button" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                        <button type="button" class="btn btn-warning btn-sm"><i class="bi bi-pencil"></i></button>
                    </td>
                </tr>
                <!-- Si no hay productos -->
                <!-- <tr><td colspan="7" class="text-center text-warning"><i class="bi bi-search" style="font-size:2rem;"></i><h5>No se encontraron productos</h5><p>No hay productos que coincidan con los filtros aplicados</p></td></tr> -->
            </tbody>
        </table>
    </div>

    <!-- Botones de acción -->
    <div class="barra mb-3 d-flex justify-content-center flex-wrap gap-2">
        <button onclick="abrirproducto()">Nuevo producto</button>
        <button onclick="abrirproveedor()">Proveedores</button>
        <button>Reportes inventario</button>
        <button>Parámetros inventario</button>
        <button onclick="window.location.href='http://localhost/ProyectoFloralTechhh/index.php?ctrl=FlorController&action=index'">Gestión de flores</button>
    </div>
</main>

<!-- dialogs -->
<div class="modal" id="modal-nuevo-producto" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo producto</h5>
                <button type="button" class="btn-close" onclick="cerrarproducto()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="">Codigo:</label> <br>
                <input type="text"><br>
                <label for="">Nombre:</label> <br>
                <input type="text"><br>
                <label for="">Cantidad:</label><br>
                <input type="text"><br>
                <label for="">Nivel:</label><br>
                <select>
                    <option value="bajo">Bajo</option>
                    <option value="medio">Medio</option>
                    <option value="alto">Alto</option>
                </select> <br>
                <label for="">Valor unitario:</label><br>
                <input type="text"><br>
                <label for="">Proveedor:</label><br>
                <select>
                    
                </select> <br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarproducto()">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
            </div>
        </div>
        </div>
        <div class="modal-backdrop-custom" id="modal-backdrop"></div>


        <div class="modal" id="modal-proveedores" style="display:none;">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nuevo Proveedor</h5>
                <button type="button" class="btn-close" onclick="cerrarproveedor()" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <label for="">Nombre:</label> <br>
                <input type="text"><br>
                <label for="">Categoria:</label> <br>
                <select>
                    <option value="bajo">Bajo</option>
                    <option value="medio">Medio</option>
                    <option value="alto">Alto</option>
                </select> <br>
                <label for="">Telefono:</label><br>
                <input type="text"><br>
                <label for="">Dirección:</label><br>
                <input type="text"><br>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="cerrarproveedor()">Cerrar</button>
                <button type="button" class="btn btn-primary">Guardar</button>
            </div>
            </div>
        </div>
        </div>
        <div class="modal-backdrop-custom" id="modal-backdrop"></div>
        </section>
    </main>

    
</body>
<script src="../../assets/inventario.js"></script>
</html>