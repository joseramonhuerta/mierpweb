<?php
require_once 'conexion.php';
header('Content-Type: application/json');
$json=array();

$usuario=$_GET['usuario'];
$clave=$_GET['clave'];
$conexion = Conexion::getInstance();

if ($stmt = $mysqli->prepare("SELECT id_cliente, nombre_fiscal FROM cat_clientes WHERE celular_contacto = '?' AND pass = '?'")) {

    // Vincular el parámetro (i = integer, s = string, d = double, b = blob)
    $stmt->bind_param("s", $usuario);
	$stmt->bind_param("s", $clave);

    // Ejecutar la declaración
    $stmt->execute();

    // Vincular variables a los resultados
    $stmt->bind_result($id_cliente, $nombre_cliente);

   
    // Recorrer los resultados
    while ($stmt->fetch()) {
			$results["id_cliente"]= $id_cliente;
			$results["nombre_cliente"]=$nombre_cliente;			
			$json['datos'][]=$results;
    }

    // Cerrar la declaración
    $stmt->close();
} else {
    echo "Error al preparar la consulta: " . $mysqli->error;
}



echo json_encode($json);


exit;
?>