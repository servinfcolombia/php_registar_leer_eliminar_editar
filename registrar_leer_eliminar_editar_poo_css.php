<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD Usuarios</title>
    <link rel="stylesheet" href="_estilo_0.css">
</head>
<body>


<?php
class UsuarioCRUD {
    private $conn;

    public function __construct() {
        $host = "localhost";
        $dbuser = "root";
        $dbpass = "";
        $dbname = "usuario_php";
        $this->conn = new mysqli($host, $dbuser, $dbpass, $dbname);
        if ($this->conn->connect_error) {
            die("Conexión fallida: " . $this->conn->connect_error);
        }
    }

    public function obtenerUsuarios() {
        $sql = "SELECT id, username, password, created_at, updated_at FROM login_user";
        return $this->conn->query($sql);
    }

    public function obtenerUsuarioPorId($id) {
        $sql = "SELECT * FROM login_user WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $res = $stmt->get_result();
        $usuario = $res->fetch_assoc();
        $stmt->close();
        return $usuario;
    }

    public function usuarioExiste($username, $excludeId = null) {
        if ($excludeId) {
            $sql = "SELECT id FROM login_user WHERE username = ? AND id != ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $username, $excludeId);
        } else {
            $sql = "SELECT id FROM login_user WHERE username = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("s", $username);
        }
        $stmt->execute();
        $stmt->store_result();
        $existe = $stmt->num_rows > 0;
        $stmt->close();
        return $existe;
    }

    public function registrarUsuario($username, $password) {
        if ($this->usuarioExiste($username)) {
            return "El nombre de usuario ya existe. Por favor elige otro.";
        }
        $sql = "INSERT INTO login_user (username, password) VALUES (?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $username, $password);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? "Usuario registrado correctamente." : "Error al registrar usuario: " . $this->conn->error;
    }

    public function actualizarUsuario($id, $username, $password) {
        if ($this->usuarioExiste($username, $id)) {
            return "El nombre de usuario ya existe en otro registro. Por favor elige otro.";
        }
        if (!empty($password)) {
            $sql = "UPDATE login_user SET username=?, password=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("ssi", $username, $password, $id);
        } else {
            $sql = "UPDATE login_user SET username=? WHERE id=?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("si", $username, $id);
        }
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? "Usuario actualizado correctamente." : "Error al actualizar usuario: " . $this->conn->error;
    }

    public function eliminarUsuario($id) {
        $sql = "DELETE FROM login_user WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $ok = $stmt->execute();
        $stmt->close();
        return $ok ? "Usuario eliminado correctamente." : "Error al eliminar usuario: " . $this->conn->error;
    }
}

// --- Lógica de control ---
$crud = new UsuarioCRUD();
$mensaje = "";
$editar_usuario = null;

// Procesar acciones
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['registrar']) && isset($_POST['user']) && isset($_POST['password'])) {
        $mensaje = $crud->registrarUsuario($_POST['user'], $_POST['password']);
    }
    if (isset($_POST['actualizar']) && isset($_POST['id']) && isset($_POST['user'])) {
        $mensaje = $crud->actualizarUsuario($_POST['id'], $_POST['user'], $_POST['password']);
    }
    if (isset($_POST['eliminar_id'])) {
        $mensaje = $crud->eliminarUsuario($_POST['eliminar_id']);
    }
    if (isset($_POST['editar_id'])) {
        $editar_usuario = $crud->obtenerUsuarioPorId($_POST['editar_id']);
    }
}

$result = $crud->obtenerUsuarios();
?>

<!-- Formulario de registro/edición -->
<h2><?php echo $editar_usuario ? "Editar Usuario" : "Formulario de Registro"; ?></h2>
<?php if ($mensaje): ?>
    <div style="color:<?php echo strpos($mensaje, 'correctamente') !== false ? 'green' : 'red'; ?>;"><?php echo $mensaje; ?></div>
<?php endif; ?>
<form action="registrar_leer_eliminar_editar_poo.php" method="post">
    <?php if ($editar_usuario): ?>
        <input type="hidden" name="id" value="<?php echo $editar_usuario['id']; ?>">
    <?php endif; ?>
    <label for="user">Usuario</label><br>
    <input type="text" name="user" placeholder="Usuario" value="<?php echo $editar_usuario ? htmlspecialchars($editar_usuario['username']) : ''; ?>"><br><br>
    <label for="password">Contraseña</label><br>
    <input type="password" name="password" placeholder="Contraseña" value=""><br><br>
    <input type="submit" name="<?php echo $editar_usuario ? 'actualizar' : 'registrar'; ?>" value="<?php echo $editar_usuario ? 'Actualizar' : 'Registrar'; ?>">
    <?php if ($editar_usuario): ?>
        <button type="button" onclick="window.location.href='registrar_leer_eliminar_editar_poo.php'">Cancelar</button>
    <?php endif; ?>
</form>

<!-- Tabla de usuarios -->
<link rel="stylesheet" href="estilo.css">
<table border="1" cellpadding="5" cellspacing="0" style="margin-top:30px; width:100%;">
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
                        <form method="post" style="display:inline;" onsubmit="return confirm('Eliminar Este usuario?');">
                            <input type="hidden" name="eliminar_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Eliminar">
                        </form>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="editar_id" value="<?php echo $row['id']; ?>">
                            <input type="submit" value="Editar">
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6" style="text-align:center;">No hay usuarios registrados</td></tr>
        <?php endif; ?>
    </tbody>
</table>


</body>
</html>