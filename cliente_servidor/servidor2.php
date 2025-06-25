<?php // Esta es una prueba de PHP
// This is a single-line comment
# This is also a single-line comment
/* This is a
multi-line comment */
//$casa = true;

$nombre = "Felipe Alejandro";
$edad = 44;
$cinco_años = 5;
echo "Este es mi nombre $nombre";
echo "<br>";

echo "Tengo $edad años"."<br>";

$cinco_años=$edad+5;
echo "En cinco años tendre $cinco_años años"."<br>";

$el_hijo=$cinco_años-$edad;
echo "Mi hijo tiene $el_hijo años"."<br>";

$la_mitad_edad=$edad/2;
echo "La mitad de mi edad es $la_mitad_edad"."<br>";

// Operadores de comparación
if ($la_mitad_edad < $el_hijo) {
    echo "La mitad de mi edad es mayor que la edad de mi hijo"."<br>";

} else {
    echo "La mitad de mi edad no es mayor que la edad de mi hijo"."<br>";
} 

echo "<br>";
echo "<form action='servidor.php' method='post'>";
echo "<input type='text' name='CASO' placeholder='INGRESE Nombre'>"."<br>";
echo "<input type='text' name='edad' placeholder='Edad'>"."<br>";
echo "<input type='text' name='valor_switch' placeholder='valor_switch'>"."<br>";
echo "<br>";
echo "<input type='submit' value='Enviar'>";
echo "</form>";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtiene el valor del campo "username"
    $nombre = $_POST["CASO"];
    $edad = $_POST["edad"];
    $valor_switch = $_POST["valor_switch"];
    
    // Muestra un mensaje con el usuario recibido
    echo "<h1>Datos recibidos:</h1>";
    echo "<p>Nombre: " . htmlspecialchars($nombre) . "</p>";
    echo "<p>Edad: " . htmlspecialchars($edad) . "</p>";
    echo "<p>Valor_switch: " . htmlspecialchars($valor_switch) . "</p>";
    // Muestra un mensaje con el usuario recibido

    $la_mitad_edad = $valor_switch;

    switch ($la_mitad_edad) {
    case 0:
        echo "ESTA ES LA 1° OPCION DE LA ESTRUCTURA DE CONTROL SWITCH"."<br>";
        break;
    case 1:
        echo "ESTA ES LA 2° OPCION DE LA ESTRUCTURA DE CONTROL SWITCH"."<br>";
        break;
    case 2:
        echo "ESTA ES LA 3° OPCION DE LA ESTRUCTURA DE CONTROL SWITCH"."<br>";
        break;
    default:
        echo "EN CASO DE NO SER NINGUNDA, ESTA ES LA OPCION POR DEFECTO DE LA ESTRUCTURA DE CONTROL SWITCH"."<br>.<br>";
        break;
}

} else {
    // Si no se envió el formulario, muestra un mensaje de error
    echo "<h1>Error: No se recibieron datos.</h1>";
}



?>

