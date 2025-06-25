<?php
// Configuración de la base de datos
$host = "localhost";
$user = "root";
$password = "";
$dbname = "adso25"; // Cambia esto por el nombre real de tu base

// Crear conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener el parámetro enviado por AJAX
$consulta = isset($_GET['consulta']) ? $conn->real_escape_string($_GET['consulta']) : "";

// Realizar consulta a la base de datos
$sql = "SELECT * FROM paciente WHERE nombres LIKE '%$consulta%'";
$result = $conn->query($sql);

// Mostrar resultados
if ($result && $result->num_rows > 0) {
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>" . htmlspecialchars($row['nombres']) . "</li>";
    }
    echo "</ul>";
} else {
    echo "No se encontraron resultados.";
}

$conn->close();
?>