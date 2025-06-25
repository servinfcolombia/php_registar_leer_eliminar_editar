<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "usuario_php";

// Validar datos de entrada
if (!isset($_POST['tipo_documento']) || !isset($_POST['numero_documento'])) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
    exit;
}

$tipoDocumento = $_POST['tipo_documento'];
$numeroDocumento = trim($_POST['numero_documento']);

if (empty($tipoDocumento) || empty($numeroDocumento)) {
    echo json_encode(['success' => false, 'message' => 'Todos los campos son obligatorios']);
    exit;
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Verificar si el tipo de documento existe
    $stmt = $conn->prepare("SELECT nombre FROM tipo_documento WHERE id = ?");
    $stmt->execute([$tipoDocumento]);
    $tipo = $stmt->fetch();
    
    if (!$tipo) {
        echo json_encode(['success' => false, 'message' => 'Tipo de documento no válido']);
        exit;
    }
    
    // Insertar la consulta
    $stmt = $conn->prepare("INSERT INTO datos_usuario (tipo_documento_id, numero_documento) VALUES (?, ?)");
    $stmt->execute([$tipoDocumento, $numeroDocumento]);
    
    // Obtener la fecha de la consulta
    $fechaConsulta = $conn->query("SELECT NOW() as fecha")->fetch()['fecha'];
    
    echo json_encode([
        'success' => true,
        'tipo_documento' => $tipo['nombre'],
        'numero_documento' => $numeroDocumento,
        'fecha_consulta' => $fechaConsulta
    ]);
} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
}
?>