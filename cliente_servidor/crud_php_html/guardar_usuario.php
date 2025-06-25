<?php
// Configuración de la base de datos
$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "usuario_php";

// Conectar a la base de datos
$conn = new mysqli($host, $dbuser, $dbpass, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Procesar el formulario cuando se envía
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos del formulario
    $username = $conn->real_escape_string(trim($_POST['username']));
    $password = trim($_POST['password']);
    
    // Validar que los campos no estén vacíos
    if (empty($username) || empty($password)) {
        $mensaje = "Todos los campos son obligatorios";
        $tipo_mensaje = "error";
    } else {
        // Verificar si el usuario ya existe
        $check_sql = "SELECT id FROM login_user WHERE username = '$username'";
        $result = $conn->query($check_sql);
        
        if ($result->num_rows > 0) {
            $mensaje = "El nombre de usuario ya está registrado";
            $tipo_mensaje = "error";
        } else {
            // Hash de la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Insertar nuevo usuario
            $insert_sql = "INSERT INTO login_user (username, password) VALUES ('$username', '$hashed_password')";
            
            if ($conn->query($insert_sql)) {
                $mensaje = "Usuario registrado exitosamente";
                $tipo_mensaje = "success";
            } else {
                $mensaje = "Error al registrar el usuario: " . $conn->error;
                $tipo_mensaje = "error";
            }
        }
    }
    
    // Cerrar conexión
    $conn->close();
    
    // Redireccionar con mensaje
    header("Location: index.html?mensaje=" . urlencode($mensaje) . "&tipo=" . urlencode($tipo_mensaje));
    exit();
}
?>