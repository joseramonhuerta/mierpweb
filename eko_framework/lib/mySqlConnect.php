<?php

function mysqljson($query){
	global $host;
	global $user;
	global $pass;
	global $dbname;
	$dbd = @mysql_connect($host, $user, $pass, TRUE, 131074);
	if (!$dbd)
	{
		//file_put_contents('debug.txt', 'Error al Conectar con servidor mysql:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		return '{"success": false, "msg": "Error al Conectar con servidor mysql: '.utf8_encode(mysql_errno().': '.mysql_error()).'..."}';
		exit ;
	}
	if (!@mysql_select_db($dbname, $dbd))
	{
		//file_put_contents('debug.txt', 'ERROR: al seleccionar bd:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		return '{"success": false, "msg": "ERROR al seleccionar bd: '.utf8_encode(mysql_errno().': '.mysql_error()).'..."}';
		exit ;
	}
	
	@mysql_query("SET NAMES 'UTF8';", $dbd);
	$res = @mysql_query($query, $dbd);
	if (!$res)
	{
		//file_put_contents('debug.txt', 'Error al ejecutar query: '.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		return '{"success": false, "msg": "Error al ejecutar query: '.utf8_encode(mysql_errno().': '.mysql_error()).'..."}';
		exit ;
	}
	$rec = @mysql_fetch_assoc($res);
		if($rec){
			return $rec['mensaje'];
		}
		else
		{return '{"success": true}';}
	//file_put_contents('debug.txt', $rec['mensaje']);
	mysql_close($dbd);
}



function fileupload($query){
		
		global $host;
		global $user;
		global $pass;
		global $dbname;
		
		//$query = stripslashes($_REQUEST["query"]);
		$dbd = @mysql_connect($host, $user, $pass, TRUE, 131074);
		if (!$dbd)
		{
			//file_put_contents('debug.txt', 'Error al Conectar con servidor mysql:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
			echo '{"success": false, "msg": "Error al Conectar con servidor mysql: '.utf8_encode(mysql_errno().': '.mysql_error()).'..."}';
			exit ;
		}
		if (!@mysql_select_db($dbname, $dbd))
		{
			//file_put_contents('debug.txt', 'ERROR: al seleccionar bd:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
			echo '{"success": false, "msg": "ERROR al seleccionar bd: '.utf8_encode(mysql_errno().': '.mysql_error()).'..."}';
			exit ;
		}
		@mysql_query("SET NAMES 'utf8';", $dbd);
		$res = @mysql_query($query, $dbd);
		if (!$res)
		{
			//file_put_contents('debug.txt', 'Error al ejecutar query: '.utf8_encode(mysql_errno().": ".mysql_error()).'...');
			echo '{"success": false, "msg": "Error al ejecutar query: '.utf8_encode(mysql_errno().': '.mysql_error()).'..."}';
			exit ;
		}
		$rows = mysql_num_rows($res);
		if ($rows){
			$reg = mysql_fetch_array($res);	
			if($reg['success']){
			  //validacion de extracion de nombres para la repeticion
			  $nombres = $reg['nombre_archivos'];
			  $archivos = explode(",",$nombres);
			  $nombre_upload = $_REQUEST["nombre_campos"];
			  $campos = explode(",",$nombre_upload);
			  $n = count($campos);
			  //Extraer el numero de repeticiones
			  $i = 0;
			  while($i <= $n)
			  {
				//validacion si el nombre biene vacio    $reg['archivo_llave']
				if($_FILES[$campos[$i]]['name'] != "")
					{
					$nombre_archivo = '../'.$_REQUEST["ruta_archivo"].$archivos[$i];
					$temporal = $_FILES[$campos[$i]]['tmp_name'];
					if (!@move_uploaded_file($temporal, $nombre_archivo)){
						echo '{"success":false, "msg":"Error al subir el archivo '.$campos[$i].'..."}';
						exit ;
					}
				}
				$i = $i+1;
			   } //fin del while;
			}
			else{
			die($reg['mensaje']);
			}
		} 
		echo '{"success":true}';
		mysql_close($dbd);
		
}

function eliminarfileupload($query){
		
		global $host;
		global $user;
		global $pass;
		global $dbname;

		$extensiones = array("gif","png","bmp","jpg","jpeg");
		$f = $_REQUEST["f"];
		$ftmp = explode(".",$f);
		$fExt = strtolower($ftmp[count($ftmp)-1]);
		if(!in_array($fExt,$extensiones)){
			return '{"success": false, "msg": "<b>ERROR!</b> no es posible eliminar el archivo con la extensi�n '.$fExt.'"}';
		}
		
		if(file_exists("../$f")){	
		   $fp = unlink("../$f");
		}
		return mysqljson($query);
}

function mysqlResult($query){
	global $host;
	global $user;
	global $pass;
	global $dbname;
	//$dbd = @mysql_connect($host, $user, $pass, TRUE, 131074);	
	//echo $host.$user.$pass.$dbname;
	$dbd = @mysql_connect($host, $user, $pass, TRUE, 196610);
	if (!$dbd)
	{
		//file_put_contents('debug.txt', 'Error al Conectar con servidor mysql:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		//echo 'Error al Conectar con servidor mysql:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return false;
		exit ;
	}
	if (!@mysql_select_db($dbname, $dbd))
	{
		//file_put_contents('debug.txt', 'ERROR: al seleccionar bd:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		//echo 'ERROR: al seleccionar bd:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return false;
		exit ;
	}
	@mysql_query("SET NAMES 'utf8';", $dbd);
	$res = mysql_query($query, $dbd);
	mysql_close($dbd);
	return $res;	
}

function mysqlTree($query){
	global $host;
	global $user;
	global $pass;
	global $dbname;
	$dbd = @mysql_connect($host, $user, $pass, TRUE, 131074);
	if (!$dbd)
	{
		echo 'Error al Conectar con servidor mysql:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return '[]';
		exit ;
	}
	if (!@mysql_select_db($dbname, $dbd))
	{
		echo 'ERROR: al seleccionar bd:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return '[]';
		exit ;
	}
	@mysql_query("SET NAMES 'utf8';", $dbd);
	$res = mysql_query($query, $dbd);
	if (!$res)
	{
		echo 'Error al ejecutar query:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return '[]';
		exit ;
	}
	
	$rows = mysql_num_rows($res);

	if ($rows == 0)
	{
	    return '[]';
	} else {
	    $arr = array ();
	    while ($obj = mysql_fetch_object($res))
	    {
	        $arr[] = $obj;
	    }
		$json = json_encode($arr);
		$json = str_replace('"true"','true',$json);
		$json = str_replace('"false"','false',$json);
	    return $json;
	}
	mysql_close($dbd);
}

function mysqlGridPaginado($query){
	global $host;
	global $user;
	global $pass;
	global $dbname;
	$dbd = @mysql_connect($host, $user, $pass, TRUE, 131074);
	if (!$dbd)
	{
		//file_put_contents('debug.txt', 'Error al Conectar con servidor mysql:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		echo 'debug.txt', 'Error al Conectar con servidor mysql:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return '{"totalRows":"0","data":[]}';
		exit ;
	}
	if (!@mysql_select_db($dbname, $dbd))
	{
		//file_put_contents('debug.txt', 'ERROR: al seleccionar bd:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		echo 'debug.txt', 'ERROR: al seleccionar bd:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return '{"totalRows":"0","data":[]}';
		exit ;
	}
	@mysql_query("SET NAMES 'utf8';", $dbd);
	$res = mysql_query($query, $dbd);
	if (!$res)
	{
		//file_put_contents('debug.txt', 'Error al ejecutar query:'.utf8_encode(mysql_errno().": ".mysql_error()).'...');
		echo 'debug.txt', 'Error al ejecutar query:'.utf8_encode(mysql_errno().": ".mysql_error()).'...';
		return '{"totalRows":"0","data":[]}';
		exit ;
	}
	$rows = @mysql_num_rows($res);
	if ($rows == 0)
	{
		return '{"totalRows":"0","data":[]}';
	} else {
		$arr = array ();
		while ($obj = @mysql_fetch_object($res))
		{
			if(!$cantrows){
				$cantrows = $obj->totalrows;
			}
			
			$arr[] = $obj;
		}

		return '{"totalRows":"'.$cantrows.'","data":'.json_encode($arr).'}';
		//file_put_contents('debug.txt', '{"totalRows":"'.$rows.'","data":'.json_encode($arr).'}');
	}
	mysql_close($dbd);
}

?>