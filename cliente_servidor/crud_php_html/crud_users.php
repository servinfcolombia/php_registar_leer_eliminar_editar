<?php
require_once 'db_connection.php';

// Elimina el session_start() y la configuración de conexión duplicada
// Mantén el resto del código igual

// Manejar solicitudes GET para obtener datos de un usuario
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['userId'])) {
    $user = getUserById($_GET['userId']);
    if ($user) {
        header('Content-Type: application/json');
        echo json_encode($user);
    } else {
        http_response_code(404);
    }
    exit;
}

// ... (el resto de tu código actual) ...

// Función para obtener todos los usuarios

function getUsers() {
    global $conn;
    $stmt = $conn->query("SELECT id, username, created_at, updated_at FROM login_user ORDER BY id");
    if ($stmt instanceof PDOStatement) {
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        return [];
    }
}

// Función para obtener un usuario por ID
function getUserById($id) {
    global $conn;
    try {
        $stmt = $conn->prepare("SELECT id, username FROM login_user WHERE id = ?");
        $stmt->execute([$id]);
        $user = $stmt->fetch();
        
        if (!$user) {
            $_SESSION['message'] = 'Usuario no encontrado';
            $_SESSION['message_type'] = 'error';
            return null;
        }
        
        return $user;
    } catch(PDOException $e) {
        error_log("Error en getUserById: " . $e->getMessage());
        $_SESSION['message'] = 'Error al obtener usuario';
        $_SESSION['message_type'] = 'error';
        return null;
    }
}
// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create':
            createUser();
            break;
        case 'update':
            updateUser();
            break;
        case 'delete':
            deleteUser();
            break;
        default:
            $_SESSION['message'] = 'Acción no válida';
            $_SESSION['message_type'] = 'error';
            break;
    }
    
    header("Location: index.php");
    exit;
}

// Función para crear usuario
function createUser() {
    global $conn;
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $_SESSION['message'] = 'Usuario y contraseña son obligatorios';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        $_SESSION['message'] = 'El usuario ya existe';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Hash de la contraseña
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO login_user (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);
    
    $_SESSION['message'] = 'Usuario creado correctamente';
    $_SESSION['message_type'] = 'success';
}

// Función para actualizar usuario
function updateUser() {
    global $conn;
    
    $userId = $_POST['userId'] ?? 0;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($userId) || empty($username)) {
        $_SESSION['message'] = 'Datos incompletos';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE id = ?");
    $stmt->execute([$userId]);
    
    if (!$stmt->fetch()) {
        $_SESSION['message'] = 'Usuario no encontrado';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Verificar si el nuevo nombre de usuario ya existe (excluyendo el actual)
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE username = ? AND id != ?");
    $stmt->execute([$username, $userId]);
    
    if ($stmt->fetch()) {
        $_SESSION['message'] = 'El nombre de usuario ya está en uso';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Actualizar usuario
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE login_user SET username = ?, password = ? WHERE id = ?");
        $stmt->execute([$username, $hashedPassword, $userId]);
    } else {
        $stmt = $conn->prepare("UPDATE login_user SET username = ? WHERE id = ?");
        $stmt->execute([$username, $userId]);
    }
    
    $_SESSION['message'] = 'Usuario actualizado correctamente';
    $_SESSION['message_type'] = 'success';
}

// Función para eliminar usuario
function deleteUser() {
    global $conn;
    
    $userId = $_POST['userId'] ?? 0;
    
    if (empty($userId)) {
        $_SESSION['message'] = 'ID de usuario no válido';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE id = ?");
    $stmt->execute([$userId]);
    
    if (!$stmt->fetch()) {
        $_SESSION['message'] = 'Usuario no encontrado';
        $_SESSION['message_type'] = 'error';
        return;
    }
    
    // Eliminar usuario
    $stmt = $conn->prepare("DELETE FROM login_user WHERE id = ?");
    $stmt->execute([$userId]);
    
    $_SESSION['message'] = 'Usuario eliminado correctamente';
    $_SESSION['message_type'] = 'success';
}
?>