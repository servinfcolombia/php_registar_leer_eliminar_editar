<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración de Usuarios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        h1 {
            color: #333;
            text-align: center;
            margin-bottom: 30px;
        }
        
        .form-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        
        input[type="text"]:focus,
        input[type="password"]:focus {
            border-color: #4CAF50;
            outline: none;
        }
        
        .button-group {
            margin-top: 20px;
        }
        
        button, .button {
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            margin-right: 10px;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
        }
        
        .btn-primary:hover {
            background-color: #45a049;
        }
        
        .btn-secondary {
            background-color: #f44336;
            color: white;
        }
        
        .btn-secondary:hover {
            background-color: #d32f2f;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #555;
        }
        
        tr:hover {
            background-color: #f9f9f9;
        }
        
        .actions {
            white-space: nowrap;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-edit {
            background-color: #2196F3;
            color: white;
        }
        
        .btn-edit:hover {
            background-color: #0b7dda;
        }
        
        .btn-delete {
            background-color: #f44336;
            color: white;
        }
        
        .btn-delete:hover {
            background-color: #d32f2f;
        }
        
        .note {
            font-size: 12px;
            color: #777;
            margin-top: 5px;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            display: none;
        }
        
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            display: block;
        }
        
        .error {
            background-color: #f2dede;
            color: #a94442;
            display: block;
        }
    </style>
</head>
<body>
    <h1>Administración de Usuarios</h1>
    
    <!-- Mostrar mensajes de sesión -->
    <?php
    if (isset($_SESSION['message'])) {
        $messageType = $_SESSION['message_type'] ?? 'success';
        echo '<div class="message '.$messageType.'">'.$_SESSION['message'].'</div>';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
    }
    ?>
    
    <div class="form-container">
        <form id="userForm" action="crud_users.php" method="post">
            <input type="hidden" id="userId" name="userId">
            <input type="hidden" id="action" name="action" value="create">
            
            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" id="password" name="password" required>
                <div class="note">(Dejar en blanco para no cambiar al actualizar)</div>
            </div>
            
            <div class="button-group">
                <button type="submit" class="btn-primary">Guardar</button>
                <button type="button" id="cancelBtn" class="btn-secondary">Cancelar</button>
            </div>
        </form>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Fecha Creación</th>
                <th>Última Actualización</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php
            
                require_once 'db_connection.php';

                // Para obtener usuarios:
                function getUsers($conn) {
                    $stmt = $conn->query("SELECT id, username, created_at, updated_at FROM login_user ORDER BY id");
                    return $stmt->fetchAll(PDO::FETCH_ASSOC);
                }

                $users = getUsers($conn); // Pasar la conexión como parámetro
            
            if (empty($users)) {
                echo '<tr><td colspan="5" style="text-align: center;">No hay usuarios registrados</td></tr>';
            } else {
                foreach ($users as $user) {
                    echo '<tr>
                        <td>'.$user['id'].'</td>
                        <td>'.$user['username'].'</td>
                        <td>'.$user['created_at'].'</td>
                        <td>'.$user['updated_at'].'</td>
                        <td class="actions">
                            <button class="btn-edit btn-sm" data-id="'.$user['id'].'">Editar</button>
                            <button class="btn-delete btn-sm" data-id="'.$user['id'].'">Eliminar</button>
                        </td>
                    </tr>';
                }
            }
            ?>
        </tbody>
    </table>

    <script>
        // Manejar el botón de cancelar
        document.getElementById('cancelBtn').addEventListener('click', function() {
            resetForm();
        });

        // Manejar botones de editar
        document.querySelectorAll('.btn-edit').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                fetchUserData(userId);
            });
        });

        // Manejar botones de eliminar
        document.querySelectorAll('.btn-delete').forEach(button => {
            button.addEventListener('click', function() {
                const userId = this.getAttribute('data-id');
                if (confirm('¿Estás seguro de que deseas eliminar este usuario?')) {
                    deleteUser(userId);
                }
            });
        });

        // Función para obtener datos de un usuario
        function fetchUserData(userId) {
            fetch('crud_users.php?userId=' + userId)
                .then(response => response.json())
                .then(data => {
                    if (data) {
                        document.getElementById('userId').value = data.id;
                        document.getElementById('username').value = data.username;
                        document.getElementById('password').required = false;
                        document.getElementById('action').value = 'update';
                        document.querySelector('.note').style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Función para eliminar usuario
        function deleteUser(userId) {
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('userId', userId);

            fetch('crud_users.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        // Función para resetear el formulario
        function resetForm() {
            document.getElementById('userForm').reset();
            document.getElementById('userId').value = '';
            document.getElementById('action').value = 'create';
            document.getElementById('password').required = true;
            document.querySelector('.note').style.display = 'none';
        }
    </script>
</body>
</html>