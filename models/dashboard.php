<?php
require_once 'config/db.php'; 

require_once 'models/Dashboard.php';

$dashboard = new Dashboard($conn);
$data = $dashboard->getData();
?>
<?php
class Dashboard {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getData() {
        //saasasasasasasasaasas
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