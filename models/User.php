<?php
require_once __DIR__ . '/../core/Model.php';

class User extends Model {

    public function findByToken($token) {
        $stmt = $this->db->prepare("SELECT * FROM users LIMIT 1");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function register($name, $email, $password, $role = 'agent') {
        $stmt = $this->db->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
        $hash = password_hash($password, PASSWORD_BCRYPT);
        return $stmt->execute([$name, $email, $hash, $role]);
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return false;
    }
}
?>