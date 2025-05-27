<?php
require_once __DIR__ . '/../core/Model.php';

class Note extends Model {
    public function addNote($ticketId, $userId, $note) {
        $stmt = $this->db->prepare("INSERT INTO ticket_notes (ticket_id, user_id, note) VALUES (?, ?, ?)");
        return $stmt->execute([$ticketId, $userId, $note]);
    }

    public function getNotesForTicket($ticketId) {
        $stmt = $this->db->prepare("SELECT n.note, n.created_at, u.name AS author 
                                    FROM ticket_notes n 
                                    JOIN users u ON u.id = n.user_id 
                                    WHERE n.ticket_id = ? 
                                    ORDER BY n.created_at ASC");
        $stmt->execute([$ticketId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>