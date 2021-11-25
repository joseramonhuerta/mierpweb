<?php
/*========================================================================================
	DEFINIR PARÁMETROS POR DEFAULT PARA EL CASO DE NO EXISTIR DATOS EN LA BASE DE DATOS
*/

define("LIMITE_EN_PAGINACION",	  50);		//	Numero de registros por página para los grids
/* 	Revisar:	la ruta a los css de los temas deberia estar en una tabla o en un archivo de configuracion			*/
define("TEMA",					 'B');		//	A:AZUL, B:GRIS...
											//	Localidad por Default			
define("PAIS_ID",				 146);		//  Pais de la tabla cat_paises
define("PAIS_NOMBRE",			 'MÉXICO');	
define("CIUDAD_ID",				 1883);		//	Ciudad de la tabla cat_ciudades
define("CIUDAD_NOMBRE",			 'MAZATLÁN');	
define("ESTADO_ID",				 25);		//	Estado de la tabla cat_estados
define("ESTADO_NOMBRE",			 'SINALOA');
define("INTENTOS_PERMITIDOS",    3);
//------------------------------------------------------------------------------------------
define("FORMATO_DE_TEXTO",		 '2');	
/*	 valores disponibles para el formato de texto:
								
		1=TEXTO EN MAYÚSCULAS
								
		2=texto en minúsculas
								
		3=El Texto Es Capitalizado		
*/
/*=========================================================================
			DEFINE ZONA HORARIA
*/			
define("CUSTOM_TIMEZONE", 'America/Mazatlan');
date_default_timezone_set (CUSTOM_TIMEZONE);

/*=========================================================================
	DEFINE EL MENSAJE A MOSTRAR EN CASO DE QUE UN COMANDO MySQL FALLE

	0= Sin Degub: 	CAMBIA LOS MENSAJES DE MySQL POR MENSAJES PARA USUARIOS
	1= Con Debug:	MUESTRA LOS MENSAJES DE ERROR DE MySQL Y LA CONSULTA SQL
*/
define("SQL_DEBUG", '0');	


$localhost=false;


define("DB_HOST", "localhost");
define("DB_USER", "mifactura");
define("DB_PASS", "fac9845o");
define("DB_MASTER", "cfd_master");
define("RUTA_CONECTOR", "http://upcconnector.pontuel.mx");
//define("TIMBRADO", "http://mifactura.upctechnologies.com/getCFDI_mifactura.php");

define("DB_LICENCIAS", "db_mifactura");
define("MASTER", "cfd_master");

/*==================================================================================================================
	REVISAR LO SIGUIENTE:
Deberia omitir declarar la constante DB_NAME y buscar el nombre de la base de datos en $_SESSION['dbcorp'].
Creo que esto hará mas sencilla la logica al momento de logearse en un corporativo (al momento de switchear de DB)
*/
if(!isset($_SESSION['dbcorp'])){
	define("DB_NAME", "cfd_master");
}else{
	define("DB_NAME", $_SESSION['dbcorp']);
}
?>