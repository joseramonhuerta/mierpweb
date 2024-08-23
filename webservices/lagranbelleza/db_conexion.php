<?php
require_once('config.php');
class Conexion{
	var $conexion="";	
	
	public function getConexion(){
		
		return $this->conexion;
	}
	
	
	public function conectar(){
		$this->conexion=mysqli_connect(DB_HOST,DB_USER,DB_PASS,DB_NAME) or die('No se pudo conectar a la base de datos');
		mysqli_set_charset($this->conexion, "utf8");
	}
	
	public function closeConexion(){
		mysqli_close($this->conexion);
	}	
	
}

?>