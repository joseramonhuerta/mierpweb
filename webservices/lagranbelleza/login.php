<?php 
require_once('db_conexion.php');
$conexion = new Conexion();
$json=array();

if(isset($_GET["usuario"]) && isset($_GET["clave"])){

    $conexion->conectar();

	$usuario=$_GET['usuario'];
	$clave=$_GET['clave'];
	
    


	$consulta="SELECT id_cliente, nombre_fiscal as nombre_cliente
                FROM cat_clientes				
				WHERE ((email_contacto = '{$usuario}' AND AES_DECRYPT(pass,'asdf') = '{$clave}') or (celular_contacto = '{$usuario}' AND AES_DECRYPT(pass,'asdf') = '{$clave}')) AND status = 'A' LIMIT 1";
	//throw new Exception($consulta);
	$resultado=mysqli_query($conexion->getConexion(),$consulta);

	if($consulta){
       
		if($reg=mysqli_fetch_array($resultado, MYSQLI_ASSOC)){
            $json['success'] = true;
			$json['datos'][]=$reg;
		}else{
            $json['success'] = false;
            $json['datos']=[];
        }

		$conexion->closeConexion();
		header('Content-Type: application/json; charset=utf8');
		echo json_encode($json);
	}
	else{

        $json['success'] = false;
        $json['datos']=[];
		
		$conexion->closeConexion();
		echo json_encode($json);
	}
}else{
        $json['success'] = false;
        $json['datos']=[];
		$conexion->closeConexion();
		echo json_encode($json);
}



 ?>