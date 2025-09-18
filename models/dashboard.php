<?php
require_once 'config/db.php'; // Incluye la conexión

require_once 'models/Dashboard.php';

$dashboard = new Dashboard($conn); // Le pasas la conexión
$data = $dashboard->getData();
?>
<?php
// models/Dashboard.php
class Dashboard {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getData() {
        // Obtener datos para el dashboard
        // Ejemplo: Obtener estadísticas de ventas
        $sql = "SELECT * FROM sales";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        $result = $stmt->get_result();

        $data = [];
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
        return $data;
    }
}
?>