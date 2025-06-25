<?php
class Auth {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($username, $password) {
        $username = trim($username);
        $password = trim($password);
        
        if (empty($username) || empty($password)) {
            return "Por favor ingrese usuario y contraseña";
        }

        $stmt = $this->db->query(
            "SELECT id, username, password FROM login_user WHERE username = ?", 
            [$username]
        );
        
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['loggedin'] = true;
                return true;
            } else {
                return "Contraseña incorrecta";
            }
        } else {
            return "Usuario no encontrado";
        }
    }

    public function logout() {
        session_destroy();
        session_unset();
    }

    public function isLoggedIn() {
        return isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true;
    }

    public function getCurrentUser() {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null
        ];
    }
}
?>