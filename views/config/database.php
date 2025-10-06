<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'flores';
    private $username = 'root';
    private $password = '';
    private $conn;

    public function getConnection() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};dbname={$this->db_name}", 
                $this->username, 
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->exec("SET NAMES 'utf8'");
        } catch(PDOException $exception) {
            // Lanzar excepción en lugar de solo mostrar error
            throw new Exception("Error de conexión a la base de datos: " . $exception->getMessage());
        }

        return $this->conn;
    }

    // Método para verificar credenciales
    public function verifyCredentials($username, $password) {
        try {
            $query = "SELECT clave FROM usu WHERE username = :username AND activo = 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                $storedHash = $row['clave'];
                
                // Verificar el hash (SHA-256)
                $inputHash = hash('sha256', $password);
                
                return hash_equals($storedHash, $inputHash);
            }
            return false;
        } catch(PDOException $exception) {
            error_log("Error verifying credentials: " . $exception->getMessage());
            return false;
        }
    }
}
?>