<?php
function calcularTotales($cantidad, $costoU, $descuento, $impuestos,$esPorcentaje=false){
	
	
}

function buscarZipEnConector($ruta, $RFCEmisor, $serie, $folio, $fecha){				
	require_once('eko_framework/includes/nusoap_lib/nusoap.php');
	// Create the client instance
	//$client = new nusoap_client('http://mfw/ws_get_zipAlamo.php?wsdl',true);								  
	$client = new nusoap_client(RUTA_CONECTOR.'/ws_get_zipAlamo.php?wsdl',true);								  
	// Call the SOAP method
	$result = $client->call( 'buscarArchivo', array(
		'ruta' 		=> $ruta,
		'RFCEmisor' => $RFCEmisor,
		'serie' => $serie,
		'folio' => $folio,
		'fecha' => $fecha) 
	);
	// return the result
	if ( empty ($result) ){
		return false;
	}else{
		return $result;
	}	
}

function truncate($string, $len, $hard=false) {        
     if(!$len || $len>strlen($string))
          return $string;
        
     $string = substr($string,0,$len);

     return $hard?$string:(substr($string,0,strrpos($string,' ')).' ...');
}

function create_zip($files = array(),$destination = '',$overwrite = false) {
	//if the zip file already exists and overwrite is false, return false
	if(file_exists($destination)&& !$overwrite) { return false; }
	//vars
	$valid_files = array();
	//if files were passed in...
	if(is_array($files)) {
		//cycle through each file
		foreach($files as $file) {
			//make sure the file exists
			if(file_exists($file[0])) {
				$valid_files[] = $file;
			}
		}
	}
	//if we have good files...
	if(count($valid_files)) {
		//create the archive
		$zip = new ZipArchive();
		if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
			return false;
		}
		//add the files
		foreach($valid_files as $file) {
			$zip->addFile($file[0],$file[1]);
		}
		
		//close the zip -- done!
		$zip->close();
		
		//check to make sure the file exists
		return file_exists($destination);
	}
	else
	{
		return false;
	}
}

function getFechaActual($format='%d/%m/%Y %H:%i:%s'){
	$format=str_replace('%','',$format);
	$format=str_replace('S','s',$format);
	$customTimeZone = new DateTimeZone(CUSTOM_TIMEZONE);		
	$dt = new DateTime('', $customTimeZone);
	return  $dt->format($format);	
	/*
	$sql="SELECT DATE_FORMAT(now(),'$format') as FechaFac;";
	try{
		$model=new Model();
		
		$arrFecha=$model->select($sql);
		if(empty($arrFecha))throw new Excepttion("El resultado de la consulta no arrojó resultados");
		$fecha=$arrFecha[0]['FechaFac'];
		return $fecha; 
	}catch(Exception $e){
		generaLog("FECHA_ACTUAL",$e->getMessage());
		throw new Exception("Error al cosultar la fecha actual");	
	}*/
	
}

function formatearTexto($texto,$decode=false){
	$formato=$_SESSION['Auth']['UserConfig']['forUsu'];

	switch($formato){
		case 1: //MAYUSCULAS
			if ($decode){
				$formateado=mb_strtoupper($texto,'UTF8');
			}else{
				$formateado=strtoupper($texto);
			}
			
		break;
		case 2:// minusculas
			if ($decode){
				$formateado=mb_strtolower($texto,'UTF8');
			}else{
				$formateado=strtolower($texto);
			}
			
		break;
		case 3: //Capitalizado
			if ($decode){
				$formateado=ucwords(mb_strtolower($texto,'UTF8'));
			}else{			
				$formateado=ucwords(strtolower($texto));
			}
		break;
	}
	return $formateado;	
}

function formatearCantidad($cantidad){
	if (empty($cantidad)){
		$cantidad=0;
	}
	$decimales=$_SESSION['Auth']['Parametros']['dec_can_par'];	
	return number_format($cantidad, $decimales, '.', ',');
}
function formatearMoneda($cantidad){
	if (empty($cantidad)){
		$cantidad=0;
	}
	$decimales=$_SESSION['Auth']['Parametros']['dec_mon_par'];	
	return number_format($cantidad, $decimales, '.', ',');
}
//ZESAR: FUNCION PARA ESCRIBIR UN LOG DEL ARCHIVO QUE SE ESTA PROCESANDO
function generaLog($proceso,$error){		
	//Se genera un archivo de log para cada RFC, por dia y por proceso.
	$RFCEmp= (isset($_SESSION['Auth']['User']['RFCEmp']) )? $_SESSION['Auth']['User']['RFCEmp'] : 'RFCX';
	$emaUsu= (isset( $_SESSION['Auth']['User']['emaUsu']) )? $_SESSION['Auth']['User']['emaUsu'] : 'emaX';
	//----------------------------------------------------------------------
	$dia=date('d',time());	$mes=date('m',time());	$año=date('y',time());
	$archivo=$RFCEmp."_".$año.$mes.$dia;
	$log = "logs/".$archivo."_".strtoupper($proceso).".txt";		//un log por RFC, proceso y dia	
	fopen($log, 'a');	//Se abre para seguir escribiendo
	//-------------------------------------------------------------------------
	file_put_contents("$log","\r\n ".date("d/m/y H:i:s")." -> ".'User:'.$emaUsu.". ".$error." \r\n", FILE_APPEND | LOCK_EX);
}

function mysqlQuery($query, $basedatos = false) {
	$link = new dbConexion($basedatos);
	$res  = mysql_query($query);
        //if (!$res) throw new Exception("Error en la consulta: $consulta --->".mysql_error());
	return $res;
}

function mysqlGridPaginado($query, $basedatos = false){
	$link = new dbConexion($basedatos);
	$res  = mysql_query($query);
	throw new Exception($query);
	if (!$res) throw new Exception("BDD error: ".mysql_error());
	
	$rows = mysql_num_rows($res);
	if ($rows == 0) {
		$response=array();
		$response['success']   = true;
		$response['totalRows'] = 0;
		$response['data']      = array();		
	} else {
		$arr = array ();
		$cantrows = 0;
		while ($obj = mysql_fetch_object($res)) {
			if(!$cantrows) {
				$cantrows = $obj->totalrows;
			}
			$arr[] = $obj;
		}
		$response=array();
		$response['success']   = true;
		$response['totalRows'] = $cantrows;
		$response['data']      = $arr;
		
	}
	return json_encode($response);
}

function jsonform($res) {
	$response=array();
	if (!$res) {
		$response['success']=false;
		$response['msg']="Error al ejecutar query";
		$response['data']=array();            
	} else {
		$rows = @mysql_num_rows($res);
		if ($rows == 0) {
			$response['success']=false;
			$response['data']=array();            
			$response['msg']="No se encontro el registro en la base de datos";                
		} else {
			$response['success']=true;                
			$arr = array();
			$arr = @mysql_fetch_object($res);
			if ($arr) {
				$response['data']=$arr;                    
			} else {
				$response['data']=array();
				$response['msg']=$arr['mensaje'];
				$response['success']=false;
			}
		}
	}
	return json_encode($response);
}

function nombreDelMes($numMes){
	switch($numMes) :
		case 1 : $mes = 'ENERO';  break;
		case 2 : $mes = 'FEBRERO';  break;
		case 3 : $mes = 'MARZO';  break;
		case 4 : $mes = 'ABRIL';  break;
		case 5 : $mes = 'MAYO';  break;
		case 6 : $mes = 'JUNIO';  break;
		case 7 : $mes = 'JULIO';  break;
		case 8 : $mes = 'AGOSTO';  break;
		case 9 : $mes = 'SEPTIEMBRE';  break;
		case 10 : $mes = 'OCTUBRE';  break;
		case 11 : $mes = 'NOVIEMBRE';  break;
		case 12 : $mes = 'DICIEMBRE';  break;
		default:throw new Exception("Mes desconocido:".$numMes);
	endswitch;
	return $mes;
}

function setFechaSQL($fecha){
	$fs = explode("/",$fecha); // fecha separada
	switch($fs[1]) :
		case 'Ene' : $mes = '01';  break;
		case 'Feb' : $mes = '02';  break;
		case 'Mar' : $mes = '03';  break;
		case 'Abr' : $mes = '04';  break;
		case 'May' : $mes = '05';  break;
		case 'Jun' : $mes = '06';  break;
		case 'Jul' : $mes = '07';  break;
		case 'Ago' : $mes = '08';  break;
		case 'Sep' : $mes = '09';  break;
		case 'Oct' : $mes = '10';  break;
		case 'Nov' : $mes = '11';  break;
		case 'Dic' : $mes = '12';  break;
	endswitch;
	$fecha = $fs[2]."-".$mes."-".$fs[0];
	return $fecha;

}















?>