<?php
require_once __DIR__ . '/../core/Model.php';

class Department extends Model {
    public function create($name) {
        $stmt = $this->db->prepare("INSERT INTO departments (name) VALUES (?)");
        return $stmt->execute([$name]);
    }

    public function update($id, $name) {
        $stmt = $this->db->prepare("UPDATE departments SET name = ? WHERE id = ?");
        return $stmt->execute([$name, $id]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM departments WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function all() {
        $stmt = $this->db->query("SELECT * FROM departments");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>