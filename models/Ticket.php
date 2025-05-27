<?php
require_once __DIR__ . '/../core/Model.php';

class Ticket extends Model {
    public function createWithFile($title, $desc, $user_id, $dept_id, $filePath = null) {
        $stmt = $this->db->prepare("INSERT INTO tickets (title, description, user_id, department_id, file_path) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $desc, $user_id, $dept_id, $filePath]);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM tickets");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function assignAgent($ticketId, $agentId) {
        $stmt = $this->db->prepare("UPDATE tickets SET agent_id = ? WHERE id = ?");
        return $stmt->execute([$agentId, $ticketId]);
    }

    public function updateStatus($ticketId, $status) {
        $validStatuses = ['open', 'in_progress', 'closed'];
        if (!in_array($status, $validStatuses)) {
            return false;
        }
    
        $stmt = $this->db->prepare("UPDATE tickets SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $ticketId]);
    }
}
?>