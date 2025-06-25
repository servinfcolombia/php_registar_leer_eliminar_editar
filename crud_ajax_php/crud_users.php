<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "usuario_php";

// Conexión a la base de datos
try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
    exit;
}

// Determinar la acción a realizar
$action = $_GET['action'] ?? $_POST['action'] ?? '';

// Procesar la acción
switch ($action) {
    case 'create':
        createUser($conn);
        break;
    case 'read':
        readUsers($conn);
        break;
    case 'update':
        updateUser($conn);
        break;
    case 'delete':
        deleteUser($conn);
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}

// Función para crear usuario
function createUser($conn) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Usuario y contraseña son obligatorios']);
        return;
    }
    
    // Verificar si el usuario ya existe
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El usuario ya existe']);
        return;
    }
    
    // Hash de la contraseña
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
    
    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO login_user (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashedPassword]);
    
    echo json_encode(['success' => true, 'message' => 'Usuario creado correctamente']);
}

// Función para leer usuarios
function readUsers($conn) {
    $stmt = $conn->query("SELECT id, username, created_at, updated_at FROM login_user ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($users);
}

// Función para actualizar usuario
function updateUser($conn) {
    $userId = $_POST['userId'] ?? 0;
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($userId) || empty($username)) {
        echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
        return;
    }
    
    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE id = ?");
    $stmt->execute([$userId]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        return;
    }
    
    // Verificar si el nuevo nombre de usuario ya existe (excluyendo el actual)
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE username = ? AND id != ?");
    $stmt->execute([$username, $userId]);
    
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'El nombre de usuario ya está en uso']);
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
    
    echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
}

// Función para eliminar usuario
function deleteUser($conn) {
    $userId = $_POST['userId'] ?? 0;
    
    if (empty($userId)) {
        echo json_encode(['success' => false, 'message' => 'ID de usuario no válido']);
        return;
    }
    
    // Verificar si el usuario existe
    $stmt = $conn->prepare("SELECT id FROM login_user WHERE id = ?");
    $stmt->execute([$userId]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
        return;
    }
    
    // Eliminar usuario
    $stmt = $conn->prepare("DELETE FROM login_user WHERE id = ?");
    $stmt->execute([$userId]);
    
    echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
}
?>