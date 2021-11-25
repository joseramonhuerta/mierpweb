<?php
	include 'Conexion.php';
       
    //$username = $_POST["username"];//"prueba@hotmail.com";//
    //$password = $_POST["password"];//"prueba"; //

    $consulta = "SELECT id_cliente, rfc_cliente, nombre_fiscal as nombre_cliente FROM cat_clientes";
	$resultado = $conexion->query($consulta);
   
   while($fila = $resultado->fetch_array()){
		$cliente[] = array_map('utf8_encode', $fila);	
   }
    
	$response = array();
    $response["success"] = true;  
    $response["cliente"] = $cliente;
	 
    echo json_encode($response);
	$resultado->close();
?>