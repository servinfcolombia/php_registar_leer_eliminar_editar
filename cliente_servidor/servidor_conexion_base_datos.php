<?php
// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene los datos sin sanitización
    $username = $_POST["username"];
    $password = $_POST["password"];
    
    // Configuración de la base de datos (con credenciales hardcodeadas)
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $database = "loguin";
    
    // Conexión a MySQL (vulnerable)
    $conn = new mysqli($servername, $db_username, $db_password, $database);
        // Verificar conexión
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
        // Consulta SQL vulnerable a inyección (CONCATENACIÓN DIRECTA)
    $sql = "INSERT INTO login (username, password) VALUES ('$username', '$password')";
        // Ejecutar consulta vulnerable
    if ($conn->query($sql) === TRUE) {
        // Mostrar datos recibidos sin protección XSS
        echo "<h1>Datos recibidos y almacenados:</h1>";
        echo "<p>Usuario: " . $username . "</p>";
        echo "<p>Contraseña: " . $password . "</p>";
        echo "<p style='color: green;'>Registro exitoso!</p>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
        // Cerrar conexión
    $conn->close();
    
} else {
    // Si no se envió el formulario
    echo "<h1>Error: No se recibieron datos.</h1>";
}
?>