<?php
// Conexión a la base de datos

function conectarBD() {
    $host = "localhost";
    $dbuser = "root";
    $dbpass = "";
    $dbname = "usuario_php";
    $conn = new mysqli($host, $dbuser, $dbpass, $dbname);

    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }
    return $conn;
}


//consultar base de datos
function consultarBD() {
    $conn = conectarBD();
    // Consulta para obtener los usuarios registrados
    $sql = "SELECT id, username AS username, password, created_at, updated_at FROM login_user";
    $result = $conn->query($sql);
    // Prepara la consulta
    $stmt = $conn->prepare($sql);
    // retorna el resultado de la consulta
    if (!$result) {
        die("Error en la consulta: " . $conn->error);
    }
    return $result;
}
$result = consultarBD();

?>

<?php
//Sirve para cargar los datos de un usuario específico 
//cuando presionas el botón "Editar" en la tabla.
$editar_usuario = null;
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['editar_id'])) {
    $conn = conectarBD();
    $id = intval($_POST['editar_id']);
    $sql = "SELECT * FROM login_user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $editar_usuario = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>
<!-- Formulario de registro de usuarios -->

<h2><?php echo $editar_usuario ? "Editar Usuario" : "Formulario de Registro"; ?></h2>
<form action="registrar_leer_eliminar_editar.php" method="post">
    <?php if ($editar_usuario): ?>
        <input type="hidden" name="id" value="<?php echo $editar_usuario['id']; ?>">
    <?php endif; ?>
    <label for="user">Usuario</label><br>
    <input type="text" name="user" placeholder="Usuario" value="<?php echo $editar_usuario ? htmlspecialchars($editar_usuario['username']) : ''; ?>"><br><br>
    <label for="password">Contraseña</label><br>
    <input type="password" name="password" placeholder="Contraseña" value=""><br><br>
    <input type="submit" name="<?php echo $editar_usuario ? 'actualizar' : 'registrar'; ?>" value="<?php echo $editar_usuario ? 'Actualizar' : 'Registrar'; ?>">
    <?php if ($editar_usuario): ?>
        <a href="registrar_leer_eliminar_editar.php">Cancelar</a>
    <?php endif; ?>
</form>

<?php

$conn = conectarBD();
// Guardar en base de datos.

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar']) && isset($_POST['user']) && isset($_POST['password'])) {
    $user = $_POST["user"];
    $password = $_POST["password"];

    // Verificar si el usuario ya existe
    $sql_check = "SELECT id FROM login_user WHERE username = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $user);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<h3 style='color:red;'>El nombre de usuario ya existe. Por favor elige otro.</h3>";
    } else {
        $sql = "INSERT INTO login_user (username, password) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $user, $password);
        if ($stmt->execute()) {
            echo "<h3 style='color:green;'>Usuario registrado correctamente.</h3>";
        } else {
            echo "Error al registrar usuario: " . $conn->error;
        }
        $stmt->close();
    }
    $stmt_check->close();
    $result = consultarBD();
}

// Actualizar usuario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['actualizar']) && isset($_POST['id'])) {
    $conn = conectarBD();
    $id = intval($_POST['id']);
    $user = $_POST["user"];
    $password = $_POST["password"];

    // Verificar si el usuario ya existe en otro registro
    $sql_check = "SELECT id FROM login_user WHERE username = ? AND id != ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("si", $user, $id);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        echo "<h3 style='color:red;'>El nombre de usuario ya existe en otro registro. Por favor elige otro.</h3>";
    } else {
        if (!empty($password)) {
            $sql = "UPDATE login_user SET username=?, password=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $user, $password, $id);
        } else {
            $sql = "UPDATE login_user SET username=? WHERE id=?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("si", $user, $id);
        }
        if ($stmt->execute()) {
            echo "<h3 style='color:green;'>Usuario actualizado correctamente.</h3>";
        } else {
            echo "Error al actualizar usuario: " . $conn->error;
        }
        $stmt->close();
    }
    $stmt_check->close();
    $result = consultarBD();
}

// Procesar eliminación si se envió
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_id'])) {
    $conn = conectarBD();
    $id = intval($_POST['eliminar_id']);
    $sql = "DELETE FROM login_user WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo "<h3 style='color:red;'>Usuario eliminado correctamente.</h3>";
    } else {
        echo "<h3 style='color:red;'>Error al eliminar usuario: " . $conn->error . "</h3>";
    }
    $stmt->close();
}
?>

<!-- Tabla donde se presenta la consulta a la bae datos -->
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
            <!-- Verifica que la variable $result tenga un valor válido -->
            <!-- y que la consulta devolvió al menos una fila           -->
        <?php if ($result && $result->num_rows > 0): ?>
            <!-- Recorre todos los registros obtenidos                  -->    
            <!-- de la base de datos con la consulta SQL.               -->                 
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
            <tr><td colspan="5" style="text-align:center;">No hay usuarios registrados</td></tr>
        <?php endif; ?>
    </tbody>
</table>
