<?php
/**
 * Instalador Automático para FloralTech
 * Este script configura automáticamente la base de datos y los datos iniciales
 */

// Configuración de la base de datos
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'flores';

$errors = [];
$success = [];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalador FloralTech</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3><i class="fas fa-leaf"></i> Instalador FloralTech</h3>
                    </div>
                    <div class="card-body">
                        <?php
                        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                            try {
                                // Crear conexión inicial sin especificar base de datos
                                $pdo = new PDO("mysql:host=$host", $username, $password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Crear base de datos si no existe
                                $pdo->exec("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                                $success[] = "Base de datos '$database' creada exitosamente.";
                                
                                // Conectar a la base de datos específica
                                $pdo = new PDO("mysql:host=$host;dbname=$database", $username, $password);
                                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                                
                                // Leer y ejecutar script de estructura
                                $structureSQL = file_get_contents(__DIR__ . '/DB/Flores.sql');
                                if ($structureSQL) {
                                    // Dividir el SQL en statements individuales
                                    $statements = explode(';', $structureSQL);
                                    
                                    foreach ($statements as $statement) {
                                        $statement = trim($statement);
                                        if (!empty($statement)) {
                                            $pdo->exec($statement);
                                        }
                                    }
                                    $success[] = "Estructura de tablas creada exitosamente.";
                                } else {
                                    $errors[] = "No se pudo leer el archivo de estructura SQL.";
                                }
                                
                                // Leer y ejecutar script de datos de prueba
                                $dataSQL = file_get_contents(__DIR__ . '/DB/datos_prueba.sql');
                                if ($dataSQL) {
                                    // Dividir el SQL en statements individuales
                                    $statements = explode(';', $dataSQL);
                                    
                                    foreach ($statements as $statement) {
                                        $statement = trim($statement);
                                        if (!empty($statement) && !strpos($statement, '--') === 0) {
                                            try {
                                                $pdo->exec($statement);
                                            } catch (PDOException $e) {
                                                // Ignorar errores de datos duplicados
                                                if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                                                    throw $e;
                                                }
                                            }
                                        }
                                    }
                                    $success[] = "Datos de prueba insertados exitosamente.";
                                } else {
                                    $errors[] = "No se pudo leer el archivo de datos de prueba.";
                                }
                                
                                $success[] = "¡Instalación completada exitosamente!";
                                
                            } catch (PDOException $e) {
                                $errors[] = "Error de base de datos: " . $e->getMessage();
                            } catch (Exception $e) {
                                $errors[] = "Error: " . $e->getMessage();
                            }
                        }
                        ?>
                        
                        <?php if (!empty($errors)): ?>
                            <div class="alert alert-danger">
                                <h5><i class="fas fa-exclamation-triangle"></i> Errores encontrados:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= htmlspecialchars($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success">
                                <h5><i class="fas fa-check-circle"></i> Instalación exitosa:</h5>
                                <ul class="mb-0">
                                    <?php foreach ($success as $msg): ?>
                                        <li><?= htmlspecialchars($msg) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                            
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Información importante:</h5>
                                <p><strong>Usuarios de prueba creados:</strong></p>
                                <ul>
                                    <li><strong>Administrador:</strong> admin / password123</li>
                                    <li><strong>Empleado:</strong> empleado1 / password123</li>
                                    <li><strong>Cliente:</strong> cliente1 / password123</li>
                                </ul>
                                <p class="mb-0">
                                    <a href="index.php" class="btn btn-primary">
                                        <i class="fas fa-sign-in-alt"></i> Ir al Sistema
                                    </a>
                                </p>
                            </div>
                        <?php endif; ?>
                        
                        <?php if (empty($success)): ?>
                            <div class="alert alert-info">
                                <h5><i class="fas fa-info-circle"></i> Bienvenido al Instalador de FloralTech</h5>
                                <p>Este instalador configurará automáticamente la base de datos y los datos iniciales del sistema.</p>
                                <p><strong>Configuración actual:</strong></p>
                                <ul>
                                    <li><strong>Servidor:</strong> <?= htmlspecialchars($host) ?></li>
                                    <li><strong>Usuario:</strong> <?= htmlspecialchars($username) ?></li>
                                    <li><strong>Base de datos:</strong> <?= htmlspecialchars($database) ?></li>
                                </ul>
                                <p class="text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    <strong>Nota:</strong> Si la base de datos ya existe, se recreará con datos nuevos.
                                </p>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5><i class="fas fa-check"></i> Verificaciones</h5>
                                    <ul class="list-unstyled">
                                        <li>
                                            <?php if (extension_loaded('pdo')): ?>
                                                <i class="fas fa-check text-success"></i> PDO habilitado
                                            <?php else: ?>
                                                <i class="fas fa-times text-danger"></i> PDO no encontrado
                                            <?php endif; ?>
                                        </li>
                                        <li>
                                            <?php if (extension_loaded('pdo_mysql')): ?>
                                                <i class="fas fa-check text-success"></i> PDO MySQL habilitado
                                            <?php else: ?>
                                                <i class="fas fa-times text-danger"></i> PDO MySQL no encontrado
                                            <?php endif; ?>
                                        </li>
                                        <li>
                                            <?php if (file_exists(__DIR__ . '/DB/Flores.sql')): ?>
                                                <i class="fas fa-check text-success"></i> Archivo de estructura encontrado
                                            <?php else: ?>
                                                <i class="fas fa-times text-danger"></i> Archivo de estructura no encontrado
                                            <?php endif; ?>
                                        </li>
                                        <li>
                                            <?php if (file_exists(__DIR__ . '/DB/datos_prueba.sql')): ?>
                                                <i class="fas fa-check text-success"></i> Archivo de datos encontrado
                                            <?php else: ?>
                                                <i class="fas fa-times text-danger"></i> Archivo de datos no encontrado
                                            <?php endif; ?>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h5><i class="fas fa-cogs"></i> Configuración</h5>
                                    <p>Si necesitas cambiar la configuración de la base de datos, edita el archivo <code>models/data.php</code></p>
                                </div>
                            </div>
                            
                            <div class="text-center mt-4">
                                <form method="POST">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="fas fa-download"></i> Instalar FloralTech
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer text-muted text-center">
                        <small>FloralTech v1.0 - Sistema de Gestión para Floristería</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
