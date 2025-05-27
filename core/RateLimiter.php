<?php
require_once __DIR__ . '/../config/database.php';

class RateLimiter {
    private $db;
    private $userId;
    private $action;

    public function __construct($userId, $action) {
        $database = new Database();
        $this->db = $database->connect();
        $this->userId = $userId;
        $this->action = $action;
    }

    public function tooManyRequests($limit, $seconds) {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM rate_limits
            WHERE user_id = ? AND action = ? AND timestamp > (NOW() - INTERVAL ? SECOND)
        ");
        $stmt->execute([$this->userId, $this->action, $seconds]);
        return $stmt->fetchColumn() >= $limit;
    }

    public function logAction() {
        $stmt = $this->db->prepare("INSERT INTO rate_limits (user_id, action) VALUES (?, ?)");
        $stmt->execute([$this->userId, $this->action]);
    }
}
?>