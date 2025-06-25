<?php
//if (session_status() === PHP_SESSION_NONE) {
 //   session_start();
//}
//session_start();
// Configuración de la base de datos

$host = "localhost";
$dbuser = "root";
$dbpass = "";
$dbname = "usuario_php";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $dbuser, $dbpass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

?>