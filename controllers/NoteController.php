<?php
require_once __DIR__ . '/../models/Note.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../core/Request.php';

class NoteController {
    public function create() {
        $user = AuthMiddleware::checkAuth();
        $data = Request::getBody();

        if (empty($data['ticket_id']) || empty($data['note'])) {
            http_response_code(400);
            echo json_encode(['error' => 'ticket_id and note are required']);
            return;
        }

        $noteModel = new Note();
        if ($noteModel->addNote($data['ticket_id'], $user['id'], $data['note'])) {
            echo json_encode(['message' => 'Note added']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add note']);
        }
    }
}
?>