<?php

define("DB_LICENCIAS", "db_mifactura");
define("UBICACION_DEL_PROYECTO", 'internet');		//EN FACTURACION, PARA DEFINIR LAS RUTAS A LOS ARCHIVOS
//define("UBICACION_DEL_PROYECTO", 'web-on');	
//define("UBICACION_DEL_PROYECTO", 'micompu');

define("UBICACION_DE_MySQL", 'internet');	
//define("UBICACION_DE_MySQL", 'web-on');	
//define("UBICACION_DE_MySQL", 'micompu');	

//-----------------------------------------------
//	  SQL_DEBUG : Configura del modelo, el comportamiento ante los errores;
//			
//		Valor	:	Descripcion
//			  1 :	SE MUESTRAN MENSAJES DE ERROR DE MySQL
//			  2 :	EL MODELO ENCAPSULA LOS MENSAJES DE MySQL 
define("SQL_DEBUG", '1');	//Con Debug:	 

$localhost=false;

switch(UBICACION_DE_MySQL){
	case 'internet':
		define("DB_HOST", "localhost");
		define("DB_USER", "mifactura");
		define("DB_PASS", "fac9845o");
		define("DB_MASTER", "cfd_master");
		define("RUTA_CONECTOR", "http://upcconnector.pontuel.mx");
	break;
	case 'micompu':
		define("DB_HOST", "localhost");
		define("DB_USER", "root");
		define("DB_PASS", "");
		define("DB_MASTER", "cfd_master");
	break;
	case 'web-on':
		define("DB_HOST", "192.168.2.17");
		define("DB_USER", "mifactura");
		define("DB_PASS", "mifactura");
		define("DB_MASTER", "cfd_master");
	break;
	default;
}
define("MASTER", "cfd_master");


if(!isset($_SESSION['dbcorp'])){
	define("DB_NAME", "cfd_master");
} else {
	define("DB_NAME", $_SESSION['dbcorp']);
}
?>