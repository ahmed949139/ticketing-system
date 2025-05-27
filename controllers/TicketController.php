<?php
require_once __DIR__ . '/../models/Ticket.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';
require_once __DIR__ . '/../core/RateLimiter.php';

class TicketController {
    public function index() {
        AuthMiddleware::checkAuth();
        $ticket = new Ticket();
        echo json_encode($ticket->all());
    }

    public function createWithFile() {

        $user = AuthMiddleware::checkAuth();
        
        $limiter = new RateLimiter($user['id'], 'submit_ticket');
        if ($limiter->tooManyRequests(5, 3600)) {  // 5 tickets per hour
            http_response_code(429);
            echo json_encode(['error' => 'Rate limit exceeded. Try again later.']);
            return;
        }
        
        $limiter->logAction();
    
        $title = $_POST['title'] ?? '';
        $desc = $_POST['description'] ?? '';
        $dept_id = $_POST['department_id'] ?? '';
    
        if (!$title || !$desc || !$dept_id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }
    
        $uploadDir = __DIR__ . '/../uploads/';
        $filePath = null;
    
        if (!empty($_FILES['attachment']['tmp_name'])) {
            $filename = basename($_FILES['attachment']['name']);
            $target = $uploadDir . uniqid() . '_' . $filename;
    
            if (move_uploaded_file($_FILES['attachment']['tmp_name'], $target)) {
                $filePath = 'uploads/' . basename($target);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'File upload failed']);
                return;
            }
        }
    
        $ticket = new Ticket();
        if ($ticket->createWithFile($title, $desc, $user['id'], $dept_id, $filePath)) {
            echo json_encode(['message' => 'Ticket submitted']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Ticket creation failed']);
        }
    }

    public function assignToSelf() {

        $user = AuthMiddleware::checkAuth();
    
        if ($user['role'] !== 'agent') {
            http_response_code(403);
            echo json_encode(['error' => 'Only agents can assign tickets']);
            return;
        }
    
        $ticketId = $_GET['id'] ?? null;
        if (!$ticketId) {
            http_response_code(400);
            echo json_encode(['error' => 'Ticket ID required']);
            return;
        }
    
        $ticket = new Ticket();
        if ($ticket->assignAgent($ticketId, $user['id'])) {
            echo json_encode(['message' => 'Ticket assigned to you']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Assignment failed']);
        }
    }

    public function updateStatus() {
        
        $user = AuthMiddleware::checkAuth();
    
        $ticketId = $_GET['id'] ?? null;
        $data = Request::getBody();
        $status = $data['status'] ?? null;
    
        if (!$ticketId || !$status) {
            http_response_code(400);
            echo json_encode(['error' => 'Ticket ID and status are required']);
            return;
        }
    
        $ticket = new Ticket();
        if ($ticket->updateStatus($ticketId, $status)) {
            echo json_encode(['message' => 'Ticket status updated']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid status or update failed']);
        }
    }
}
?>