<?php
require_once 'classes/Database.php';
require_once 'classes/Auth.php';
require_once 'classes/User.php';

session_start();

$db = new Database('localhost', 'root', '', 'usuario_php');
$auth = new Auth($db);
$userManager = new User($db);

// Verificar autenticación
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit;
}

$currentUser = $auth->getCurrentUser();

// Procesar operaciones CRUD
$message = null;
$editar_usuario = null;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['editar_id'])) {
        $editar_usuario = $userManager->getById($_POST['editar_id']);
    } 
    elseif (isset($_POST['registrar']) && isset($_POST['user']) && isset($_POST['password'])) {
        $message = $userManager->create($_POST['user'], $_POST['password']);
    } 
    elseif (isset($_POST['actualizar']) && isset($_POST['id']) && isset($_POST['user'])) {
        $password = !empty($_POST['password']) ? $_POST['password'] : null;
        $message = $userManager->update($_POST['id'], $_POST['user'], $password);
    } 
    elseif (isset($_POST['eliminar_id'])) {
        $message = $userManager->delete($_POST['eliminar_id']);
    }
}

$result = $userManager->getAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Usuarios</title>
    <link rel="stylesheet" href="estilo_nuevo.css">
</head>
<body>
    <div class="main-container">
        <!-- Formulario de registro/edición -->
        <div class="form-container">
            <h2 class="form-title"><?php echo $editar_usuario ? "Editar Usuario" : "Formulario de Registro"; ?></h2>
            
            <div class="user-info">
                <?php
                echo "User Id: " . $currentUser['id'];
                $nombreSession = session_name();
                $idSession = session_id();
                echo " | Session Name: $nombreSession | Session Id: $idSession |";
                ?>
                Bienvenido, <?php echo htmlspecialchars($currentUser['username']); ?> | 
                <a href="login.php?logout">Cerrar sesión</a>
            </div>
            
            <?php if ($message): ?>
                <div class="alert <?php echo strpos($message, 'correctamente') !== false ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo $message; ?>
                </div>
            <?php endif; ?>
            
            <form method="post">
                <?php if ($editar_usuario): ?>
                    <input type="hidden" name="id" value="<?php echo $editar_usuario['id']; ?>">
                <?php endif; ?>
                
                <div class="form-group">
                    <label for="user">Usuario</label>
                    <input type="text" name="user" placeholder="Usuario" 
                           value="<?php echo $editar_usuario ? htmlspecialchars($editar_usuario['username']) : ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password" name="password" placeholder="Contraseña" 
                           <?php echo !$editar_usuario ? 'required' : ''; ?>>
                    <?php if ($editar_usuario): ?>
                        <small>(Dejar vacío para mantener la contraseña actual)</small>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" name="<?php echo $editar_usuario ? 'actualizar' : 'registrar'; ?>" 
                            class="btn btn-primary">
                        <?php echo $editar_usuario ? 'Actualizar' : 'Registrar'; ?>
                    </button>
                    
                    <?php if ($editar_usuario): ?>
                        <a href="registrar_leer_eliminar_editar_css_sesion.php" class="btn cancel-btn">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>
        
        <!-- Tabla de usuarios -->
        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Contraseña</th>
                        <th>Creado</th>
                        <th>Actualizado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><?php echo htmlspecialchars($row['username']); ?></td>
                                <td><?php echo htmlspecialchars($row['password']); ?></td>
                                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                <td><?php echo htmlspecialchars($row['updated_at']); ?></td>
                                <td>
                                    <div class="action-btns">
                                        <form method="post" onsubmit="return confirm('¿Eliminar este usuario?');">
                                            <input type="hidden" name="eliminar_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="action-btn btn-delete">Eliminar</button>
                                        </form>
                                        <form method="post">
                                            <input type="hidden" name="editar_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" class="action-btn btn-edit">Editar</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">No hay usuarios registrados</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>