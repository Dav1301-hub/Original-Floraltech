<?php
class conexion{
	protected $db;
	public function __construct() {
		try {
			include(__DIR__ . "/data.php");
			$this->db = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
			$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
		} catch (PDOException $e) {
			error_log("Error de conexión a la base de datos: " . $e->getMessage());
			die("Error de conexión a la base de datos");
		}
	}
	public function get_conexion(){
		return $this->db;
	}
}
?>