<?php

require_once 'classes/Database.php';
require_once 'classes/Auth.php';

$db = new Database('localhost', 'root', '', 'usuario_php');
$auth = new Auth($db);

// Procesar logout
if (isset($_GET['logout'])) {
    $auth->logout();
    header("Location: login.php");
    exit;
}

// Procesar login
$error = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $result = $auth->login($_POST['username'], $_POST['password']);
    
    if ($result === true) {
        header("Location: registrar_leer_eliminar_editar_css_sesion_poo.php");
        exit;
    } else {
        $error = $result;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="estilo_login.css">
</head>
<body>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form action="login_poo.php" method="post">
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <input type="submit" name="login" value="Iniciar Sesión">
        </form>
    </div>
</body>
</html>