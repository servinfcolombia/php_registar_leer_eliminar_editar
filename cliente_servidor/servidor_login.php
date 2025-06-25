<?php

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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene el valor del campo "username" y "password"
    $user = $_POST["user"];
    $password = $_POST["password"];

    // Llama a la función de conexión
    $conn = conectarBD();

    // Inserta los datos en la tabla login_user
    $sql = "INSERT INTO login_user (username, password) VALUES (?, ?)";

    // Prepara la consulta
    $stmt = $conn->prepare($sql);

    //Evita inyecciones SQL
    $stmt->bind_param("ss", $user, $password);

    // Verifica si la consulta se ejecutó correctamente
    if ($stmt->execute()) {
        echo "<h1>Datos recibidos con éxito</h1>";
        echo "<p>Usuario: $user</p>";
        echo "<p>Contraseña: $password</p>";
        echo "<p>Usuario registrado correctamente en la base de datos.</p>";
    } else {
        echo "Error al registrar usuario: " . $conn->error;
    }

    // Cierra la consulta
    $stmt->close();

    // Cierra la conexión
    $conn->close();
}
?>

