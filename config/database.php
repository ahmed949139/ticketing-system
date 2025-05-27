<?php
class Database {
    private $host = "localhost";
    private $db_name = "ticketing_system_db";
    private $username = "root";
    private $password = "";
    public $conn;

    public function connect() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host={$this->host};dbname={$this->db_name}", $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            die(json_encode(['error' => 'DB Connection failed: ' . $e->getMessage()]));
        }
        return $this->conn;
    }
}
?>