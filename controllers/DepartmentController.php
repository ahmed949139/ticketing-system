<?php
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../core/Request.php';
require_once __DIR__ . '/../core/AuthMiddleware.php';

class DepartmentController {
    public function index() {
        $dept = new Department();
        echo json_encode($dept->all());
    }

    public function create() {
        AuthMiddleware::checkAdmin();
        $data = Request::getBody();
        $dept = new Department();
        if ($dept->create($data['name'])) {
            echo json_encode(['message' => 'Department created']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Creation failed']);
        }
    }

    public function update($id) {
        AuthMiddleware::checkAdmin();
        $data = Request::getBody();
        $dept = new Department();
        if ($dept->update($id, $data['name'])) {
            echo json_encode(['message' => 'Department updated']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Update failed']);
        }
    }

    public function delete($id) {
        AuthMiddleware::checkAdmin();
        $dept = new Department();
        if ($dept->delete($id)) {
            echo json_encode(['message' => 'Department deleted']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Delete failed']);
        }
    }
}
?>