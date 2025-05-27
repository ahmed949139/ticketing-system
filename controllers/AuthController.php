<?php
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../core/Request.php';

class AuthController {
    public function register() {
        $data = Request::getBody();

        if (empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $user = new User();
        if ($user->register($data['name'], $data['email'], $data['password'], $data['role'])) {
            echo json_encode(['message' => 'User registered']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Registration failed']);
        }
    }

    public function login() {
        $data = Request::getBody();
        
        if (empty($data['email']) || empty($data['password'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $user = new User();
        $authUser = $user->login($data['email'], $data['password']);
        if ($authUser) {
            $token = bin2hex(random_bytes(16));
            $tokens = json_decode(file_get_contents(__DIR__ . '/../storage/tokens.json'), true);
            $tokens[] = $token;
            file_put_contents(__DIR__ . '/../storage/tokens.json', json_encode($tokens));
            echo json_encode(['token' => $token]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }

    public function logout() {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
    
        if (!$token) {
            echo json_encode(['error' => 'Authorization token missing']);
            return;
        }
    
        $filePath = __DIR__ . '/../storage/tokens.json';
        $tokens = json_decode(file_get_contents($filePath), true);
    
        if (!in_array($token, $tokens)) {
            echo json_encode(['error' => 'Invalid token']);
            return;
        }
    
        $tokens = array_values(array_filter($tokens, fn($t) => $t !== $token));
        file_put_contents($filePath, json_encode($tokens));
    
        echo json_encode(['message' => 'Logged out']);
    }
}
?>