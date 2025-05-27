<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

$basePath = '/ticketing-system/index.php';
$uri = str_replace($basePath, '', $uri);

require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/TicketController.php';
require_once __DIR__ . '/controllers/DepartmentController.php';
require_once __DIR__ . '/controllers/NoteController.php';

$routes = [
    'GET' => [
        '/tickets' => [new TicketController(), 'index'],
        '/departments' => [new DepartmentController(), 'index'],
    ],
    'POST' => [
        '/register' => [new AuthController(), 'register'],
        '/login' => [new AuthController(), 'login'],
        '/logout' => [new AuthController(), 'logout'],
        '/tickets' => [new TicketController(), 'createWithFile'],
        '/tickets/assign' => function () {
            (new TicketController())->assignToSelf();
        },
        '/departments' => [new DepartmentController(), 'create'],
        '/notes/create' => [new NoteController(), 'create']
    ],
    'PUT' => [
        '/departments/update' => function () {
            $id = $_GET['id'] ?? null;
            (new DepartmentController())->update($id);
        },
        '/tickets/status' => function () {
            (new TicketController())->updateStatus();
        }
    ],
    'DELETE' => [
        '/departments/delete' => function () {
            $id = $_GET['id'] ?? null;
            (new DepartmentController())->delete($id);
        }
    ]
];

if (isset($routes[$method][$uri])) {
    call_user_func($routes[$method][$uri]);
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Route not found']);
}
