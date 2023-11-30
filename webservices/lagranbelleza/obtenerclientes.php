<?php 

require_once('db_conexion.php');
$conexion = new Conexion();

$json=array();
	
$conexion->conectar();				

$consulta="SELECT id_cliente, nombre_fiscal, email_contacto, telefono_contacto
	FROM cat_clientes";
$resultado=mysqli_query($conexion->getConexion(),$consulta);

if($consulta){			
	while ($row = mysqli_fetch_array($resultado, MYSQLI_ASSOC)) {
		$json[] = $row;
	}

	$data = array();
	$data['success'] = true;
	$data['data'] = $json;
	
	header('Content-Type: application/json; charset=utf8');
	echo json_encode($data);

	$conexion->closeConexion();
}else{
	$results["id_cliente"]='';
	$results["nombre_cliente"]='';
	$results["celular"]='';
	$results["direccion"]='';
	$json[]=$results;
	$conexion->closeConexion();
	echo json_encode($json);
}


 ?>