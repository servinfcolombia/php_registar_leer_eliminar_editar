<?php
class User {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT id, username, password, created_at, updated_at FROM login_user");
        return $stmt->get_result();
    }

    public function getById($id) {
        $stmt = $this->db->query(
            "SELECT * FROM login_user WHERE id = ?",
            [$id]
        );
        return $stmt->get_result()->fetch_assoc();
    }

    public function create($username, $password) {
        if ($this->usernameExists($username)) {
            return "El nombre de usuario ya existe. Por favor elige otro.";
        }

        $stmt = $this->db->query(
            "INSERT INTO login_user (username, password) VALUES (?, ?)",
            [$username, $password]
        );
        
        return $stmt ? "Usuario registrado correctamente." : "Error al registrar usuario";
    }

    public function update($id, $username, $password = null) {
        if ($this->usernameExists($username, $id)) {
            return "El nombre de usuario ya existe en otro registro. Por favor elige otro.";
        }

        if ($password) {
            $stmt = $this->db->query(
                "UPDATE login_user SET username = ?, password = ? WHERE id = ?",
                [$username, $password, $id]
            );
        } else {
            $stmt = $this->db->query(
                "UPDATE login_user SET username = ? WHERE id = ?",
                [$username, $id]
            );
        }
        
        return $stmt ? "Usuario actualizado correctamente." : "Error al actualizar usuario";
    }

    public function delete($id) {
        $stmt = $this->db->query(
            "DELETE FROM login_user WHERE id = ?",
            [$id]
        );
        
        return $stmt ? "Usuario eliminado correctamente." : "Error al eliminar usuario";
    }

    private function usernameExists($username, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->db->query(
                "SELECT id FROM login_user WHERE username = ? AND id != ?",
                [$username, $excludeId]
            );
        } else {
            $stmt = $this->db->query(
                "SELECT id FROM login_user WHERE username = ?",
                [$username]
            );
        }
        
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}
?>