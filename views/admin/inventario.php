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
	<link rel="stylesheet" href="../../assets/inventario.css">
	<link rel="stylesheet" type="text/css" href="assets/inventario.css">
	
	
</head>
<body>
<main  class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Gestión Inventario</h1>
    </div>


<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Productos
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">150</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total proveedores
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">$15,000</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Pedidos Pendientes
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">25</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Stock Bajo
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">8</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="barra">
    <button onclick="abrirproducto()">Nuevo producto</button>
    <button onclick="abrirproveedor()">Preveedores</button>
    <button>Reportes inventario</button>
    <button>Parametros inventario</button>
</div>
<div class="tabla-inventario">
    <div class="table-container" id="table-container">
        <table id="tabla-inventario" class="table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Nivel de Producto</th>
                    <th>Valor unitario</th>
                    <th>Codigo de Producto</th>
                    <th>Proveedor</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="tabla-body">
                
                <tr>
                            <td>aaaaaaaaa</td>
                            <td>aaaaaaaaa</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>
                                <button type="button" class="btn btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <button type="button" class="btn btn-warning">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr>
                        <tr>
                            <td>bbbbbbbbbb</td>
                            <td>aaaaaaaa</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>cococo</td>
                            <td>
                                <button type="button" class="btn btn-danger">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                                <button type="button" class="btn btn-warning">
                                    <i class="fa-solid fa-pen"></i>
                                </button>
                            </td>
                        </tr><!-- Aquí se generarán dinámicamente las filas -->
            </tbody>
        </table>
    </div>
</div>

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