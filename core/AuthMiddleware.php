<?php
class AuthMiddleware {
    public static function checkAuth() {
        $user = self::getUserFromToken();
        if (!$user) {
            http_response_code(401);
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        return $user;
    }

    public static function checkAdmin() {
        $user = self::getUserFromToken();
        if (!$user || $user['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden: Admins only']);
            exit;
        }
        return $user;
    }

    private static function getUserFromToken() {
        $headers = getallheaders();
        $token = $headers['Authorization'] ?? null;
        if (!$token) return null;

        $tokens = json_decode(file_get_contents(__DIR__ . '/../storage/tokens.json'), true);
        foreach ($tokens as $storedToken) {
            if ($storedToken === $token) {
                $userModel = new User();
                return $userModel->findByToken($token);
            }
        }
        return null;
    }
}
?>