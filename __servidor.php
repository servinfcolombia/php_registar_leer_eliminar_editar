<?php
// Verifica si se ha enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene el valor del campo "username"
    $username = $_POST["username"];
    $password = $_POST["password"];
    // Muestra un mensaje con el usuario recibido
    echo "<h1>Datos recibidos:</h1>";
    echo "<p>Usuario: " . htmlspecialchars($username) . "</p>";
    echo "<p>Contraseña: " . htmlspecialchars($password) . "</p>";

    // Si no se envió el formulario, muestra un mensaje de error
} else {
    // Si no se envió el formulario, muestra un mensaje de error
    echo "<h1>Error: No se recibieron datos.</h1>";
}
?>