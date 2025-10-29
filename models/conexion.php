<?php
class conexion{
	protected $db;
	public function __construct() {
		try {
			include(__DIR__ . "/data.php");
			
			// Verificar que las variables estén definidas
			if (!isset($host) || !isset($db) || !isset($user)) {
				throw new Exception("Variables de configuración de base de datos no están definidas");
			}
			
			$this->db = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
			
		} catch (PDOException $e) {
			$error_msg = "Error de conexión a la base de datos: " . $e->getMessage();
			error_log($error_msg);
			
			// Mostrar mensaje específico según el tipo de error
			if (strpos($e->getMessage(), 'Connection refused') !== false) {
				throw new Exception("Error: No se puede conectar al servidor MySQL. Asegúrate de que XAMPP esté ejecutándose.");
			} elseif (strpos($e->getMessage(), 'Unknown database') !== false) {
				throw new Exception("Error: La base de datos 'flores' no existe. Por favor, crea la base de datos primero.");
			} elseif (strpos($e->getMessage(), 'Access denied') !== false) {
				throw new Exception("Error: Acceso denegado. Verifica el usuario y contraseña de la base de datos.");
			} else {
				throw new Exception("Error de conexión a la base de datos: " . $e->getMessage());
			}
		} catch (Exception $e) {
			error_log("Error de configuración: " . $e->getMessage());
			throw new Exception("Error de configuración: " . $e->getMessage());
		}
	}
	
	public function get_conexion(){
		return $this->db;
	}
}