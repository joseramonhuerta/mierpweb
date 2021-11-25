<?php
class CertificadoModel extends Model{
    var $useTable = 'cat_certificados';
    var $name='Certificado';
    var $primaryKey = 'id_certificado';
    var $specific = true;
    var $camposAfiltrar = array('rfc_certificado','razonsocial_certificado');
    var $impuestos;
    var $cerFileTempFullPath='';
    var $keyFileTempFullPath='';

    function jsDateToMysql($jsDate){
        $date = "04/30/1973";
        list($dia, $mes, $año) = split('[/]', $jsDate);
        $convertida="$año-$mes-$dia";
        return $convertida;                
    }
	
    private function getBySerie($serie,$idCert=0){
        $filtro='';
        if ($idCert!=0){
            $filtro=" AND IDCer!=$idCert";
        }
        $query="SELECT 1 FROM cat_certificados WHERE numero_certificado='$serie' $filtro";
        $arrayResult=$this->query($query);        
        return $arrayResult;       
    }
    
    function getCertificados($start, $limit, $filtro,$empresaId,$filtroStatus) {
		
        $filtroSql = $this->filtroToSQL($filtro);

      	
		if (strlen($filtroSql) > 0) {
				if ($filtroStatus=='A')
                $filtroSql.=" AND status='A' AND id_empresa=$empresaId";
				if ($filtroStatus=='I')
                $filtroSql.=" AND status='I' AND id_empresa=$empresaId";
            }else {
               if ($filtroStatus=='A')
                $filtroSql.="WHERE status='A' AND id_empresa=$empresaId";
				if ($filtroStatus=='I')
                $filtroSql.="WHERE status='I' AND id_empresa=$empresaId";
            }


        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable
        $filtroSql;";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT id_certificado,rfc_certificado,numero_certificado,
                    DATE_FORMAT(fecha_solicitud, '%d/%m/%Y') as fecha_solicitud,
                    DATE_FORMAT(fecha_vencimiento, '%d/%m/%Y') as fecha_vencimiento,razonsocial_certificado,status FROM $this->useTable
                $filtroSql limit $start,$limit;";
       /*----------------------------------------------------------------*/
        // throw new Exception($query);
		$res = mysqlQuery($query);

        
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);
	
        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;

        return $response;
        /*----------------------------------------------------------------*/
    }
    
    public function guardar($datos){        
        $registroNuevo=false;
		$where='';
        if ($datos[$this->primaryKey]){//UPDATE
            $serie = (isset($datos['numero_certificado'])) ? $datos['numero_certificado'] : '';
            if ($serie!=''){
                $arr = $this->getBySerie($serie,$datos[$this->primaryKey]);
                if (sizeof($arr) > 0) {
                    throw new Exception('El certificado ya existe');
                }
            }                        
            $query="UPDATE $this->useTable SET ";
            $where=" WHERE $this->primaryKey = ".$datos[$this->primaryKey];
            $this->id=$datos[$this->primaryKey];
            $datos[$this->primaryKey]='';
        }else{  //INSERT
            /*  REVISO QUE EL CERTIFICADO NO EXISTA   */
            $serie=$datos['numero_certificado'];
            $arr=$this->getBySerie($serie);            
            if (sizeof($arr)>0){
                throw new Exception('El certificado ya existe');
            }
            //------------------------------------------------
            $query="INSERT INTO $this->useTable SET ";
            $registroNuevo=true;
            
        }
        /*----------------------------------------------------------------*/
        foreach($datos as $key=>$value){
            if ($value!=''){
            	if ($key=='pass_certificado'){
            		$query.="pass_certificado=AES_ENCRYPT('$value','asdf'),";
            	}else{
            		$query.="$key='$value',";	
            	}
                
            }
        }
        $query=substr($query, 0,strlen($query)-1);     
         
        /*----------------------------------------------------------------*/

        $query=$query.$where;
      
            if ($registroNuevo){
                
               $result= $this->insert($query);
                $id=mysql_insert_id();
            }else{
                $result=$this->update($query);
                $id=$this->id;
            }

            if ($this->cerFileTempFullPath!=''){
                $this->respaldarCertificados($this->cerFileTempFullPath, $this->keyFileTempFullPath, $datos['id_empresa'], $datos['rfc_certificado']);
            }
            $this->id=$id;
            $data=$this->getById($id);
            
            return $data;
        
    }
    function getById($IDValue){
            $query="SELECT id_certificado,concat(rfc_certificado,'.cer') as archivo_certificado,razonsocial_certificado,rfc_certificado,AES_DECRYPT(pass_certificado,'asdf') pass_certificado,concat(rfc_certificado,'.key') as llave_certificado,numero_certificado,
                   fecha_solicitud, fecha_vencimiento,status,
                    FROM $this->useTable c
                    WHERE $this->primaryKey=$IDValue";
            $result=mysqlQuery($query);
            $datos=array();
            $datos[$this->name]=mysql_fetch_array($result,MYSQL_ASSOC);
            return $datos;
    }

    public function delete($id){
   
        return parent::delete($id);
    }
	
	public function validarRFCCertificado($id_emp, $id_suc){
		$msg = '';
		if ($id_suc){
			$cad = "SELECT RFCEmp, RFCCer, IF(FecExpCer<NOW(),1,0) AS expirado, IF(RFCEmp=RFCCer,1,0) AS coincide  FROM cat_sucursales 
			LEFT JOIN cat_empresas ON(KEYEmpSuc = IDEmp)  LEFT JOIN cat_certificados_sucursales ON(KEYSucCerSuc = IDSuc) 
			LEFT JOIN cat_certificados ON(KEYCerCerSuc = IDCer)  WHERE IDSuc = $id_suc AND DefaultCerSuc = 1;";
		} else {
			$cad = "SELECT RFCEmp, RFCCer, IF(FecExpCer<NOW(),1,0) AS expirado, IF(RFCEmp=RFCCer,1,0) AS coincide  FROM cat_empresas 
			LEFT JOIN cat_certificados ON(KEYEmpCer = IDEmp)  WHERE IDEmp = $id_emp AND DefaultCer = 1;";
		}
		$res = $this->query($cad);
		if (count($res) <= 0){
			$msg = "No se ha especificado un certificado para la empresa o el certificado está suspendido.";
		} else {
			if ($res[0]['expirado'] == 1){
				$msg = "El certificado ha expirado.";
			}
			if ($res[0]['coincide'] == 0){
				$certRFC=$res[0]['RFCCer'];
				$RFCEmp=$res[0]['RFCEmp'];
				$msg = "El RFC del certificado ($certRFC) no coincide con el RFC de la empresa ($RFCEmp).";
			}
		}
		return $msg;
	}
	
    /*      DE AKI EN ADELANTE ES EL CODIGO PARA LEER CERTIFICADOS		*/
     public function leerCertificados($cerFileTempFullPath, $keyFileTempFullPath, $KEYEmpCer, $pass) {
     $this->cerFileTempFullPath=$cerFileTempFullPath;   //Usare estas variables para repaldar los archivos despues de guardar
     $this->keyFileTempFullPath=$keyFileTempFullPath;
        //Crear archivos pem
        $keyPemTempFile = $this->crearKeyPem($keyFileTempFullPath, $pass);
        $cerPemTempFile = $this->crearCerPem($cerFileTempFullPath);


        $detalleAsArray = $this->getDetalleDelCertificado($cerPemTempFile); //<-----------ahora obtengo los detalles del cer.pem
		
        $match = $this->compararRFCDelCertificadoContraEmpresa($KEYEmpCer, $detalleAsArray['rfc']);
		
        $match = $this->compararLlavePublicaYPrivada($cerPemTempFile, $keyPemTempFile);

        $detalleAsArray['KeyPemData'] = file_get_contents($keyPemTempFile);
        $detalleAsArray['CerPemData'] = file_get_contents($cerPemTempFile);
        $detalleAsArray['CerFileCer']=$keyPemTempFile;
        $detalleAsArray['KeyFileCer']=$cerPemTempFile;
        /* BORRAR ARCHIVOS PEM */
        unlink($keyPemTempFile);
        unlink($cerPemTempFile);
        /* MOVER ARCHIVOS cer y key a la carpeta que corresponde */
        return $detalleAsArray;
    }

    public function compararLlavePublicaYPrivada($cerPemTempFile, $keyPemTempFile) {
        //comparar hash MD5 de los módulos
        if (file_exists("$cerPemTempFile.mod")) {
            unlink("$cerPemTempFile.mod");
        }
        if (file_exists("$keyPemTempFile.mod")) {
            unlink("$keyPemTempFile.mod");
        }
        $comando = "openssl x509 -noout -modulus -in $cerPemTempFile | openssl md5 >$cerPemTempFile.mod";
        $modCert = shell_exec($comando);
        if (!file_exists("$cerPemTempFile.mod")) {
            throw new Exception("Error tratar de comparar el certificado");
        }
        $modCert = file_get_contents("$cerPemTempFile.mod");
        if ($modCert == '') {
            throw new Exception("Error leyendo el modulo del certificado");
        }

        $comando = "openssl rsa -noout -modulus -in $keyPemTempFile | openssl md5 >$keyPemTempFile.mod";
        $modKey = shell_exec($comando);
        if (!file_exists("$keyPemTempFile.mod")) {
            throw new Exception("Error tratar de comparar la llave privada");
        }
        $modKey = file_get_contents("$keyPemTempFile.mod");
        if ($modKey == '') {
            throw new Exception("Error leyendo el modulo de la llave privada");
        }
        if ($modKey != $modCert) {
            throw new Exception('No coincide la llave pública y privada');
        }
        return true;
    }

    private function compararRFCDelCertificadoContraEmpresa($KEYEmpCer, $rfc) {
		
        $empresaModel = new Empresa();
		// throw new Exception("aqui");
        $rfcEmp = $empresaModel->getRFC($KEYEmpCer);
		// throw new Exception("aqui2");
        if (strtoupper($rfc)!=strtoupper($rfcEmp)) {
            throw new Exception("No coincide el RFC del certificado (".strtoupper($rfc).") con el de la empresa seleccionada(".strtoupper($rfcEmp).") ");
        } else {
            return true;
        }
    }
    
    private function respaldarCertificados($tempCerFile,$tempKeyFile,$empresaId,$rfcCer){
        
        
        $corpId = $_SESSION['Auth']['User']['IDCor'];
        if ($corpId =='')throw new Exception('Error al respaldar los certificados, perdido el identificador del corporativo');
        if ($empresaId =='')throw new Exception('Error al respaldar los certificados, perdido el identificador de la empresa');
        $ruta = "certificados/$corpId/$empresaId/";
        !@rmkdir_r($ruta, 0775, true);

        copy($tempCerFile, $ruta . $rfcCer . '.cer');
        unlink($tempCerFile);

        copy($tempKeyFile, $ruta . $rfcCer . '.key');
        unlink($tempKeyFile);
    }
    
    private function getDetalleDelCertificado($archivoCerPem) {
        $certData = file_get_contents($archivoCerPem);

        $key=@openssl_x509_read($certData);
         if (!$key){
             throw new Exception('Error leyendo el pem');
         }

        $arr = openssl_x509_parse($key);

        $detalle = array();
        
        /*OBTENER EL NUMERO DE SERIE DEL CERTIFICADO, SI LO OBTENGO CON LAFUNCION DE PHP COMO QUE ESTA CODIFICADO ASI QUE TENGO QUE USAR EL SHELL
         * SI ENCUENTRAS UNA SOLUCION ENVIAMELA A runtim3.error@gmail.com, gracias.
         */
        $cmd="openssl x509 -in $archivoCerPem -serial -noout";
        $serialResp=shell_exec($cmd);
        
        if ($serialResp==''){
            throw new Exception('No pude obtener el numero de serie del certificado');
        }else{
           list($x,$serial)=explode("=", $serialResp);
           $serial=trim($serial);
           $tamaño=strlen($serial);           
           $serie='';
           if ($tamaño!=40){
               throw new Exception("La longitud del numero de serie del certificado no es la esperada:".$tamaño);
           }
           for($i=1;$i<$tamaño;$i+=2){
                $serie.=substr($serial, $i,1);
           }                                   
        }
        
        //list($otraCosa, $serie) = split('/', $arr['subject']['serialNumber']);
        list($rfc, $otraCosa) = explode('/', $arr['subject']['x500UniqueIdentifier']);
        $detalle['SubjectNameCer'] = $arr['subject']['name'];
        $detalle['serie'] = $serie;

        $detalle['rfc'] = trim($rfc);
		//-------------------------------------------------------
		$userTimezone = new DateTimeZone(CUSTOM_TIMEZONE);
		$gmtTimezone = new DateTimeZone('GMT');
		$myDateTime = new DateTime('', $gmtTimezone);
		$offset = $userTimezone->getOffset($myDateTime);
		//---------------------------------------------------
		$validFrom=$this->ASN1_UTCTime_ToMysqlDate($arr['validFrom']);		
		$validFrom=strtotime($validFrom);		
		$validFrom=$validFrom+$offset;
		$detalle['validFrom']=date("Y-m-d H:i:s",$validFrom);		
		//---------------------------------------------------
		$validTo=$this->ASN1_UTCTime_ToMysqlDate($arr['validTo']);		
		$validTo=strtotime($validTo);		
		$validTo=$validTo+$offset;
		$detalle['validTo']=date("Y-m-d H:i:s",$validTo);		
		//---------------------------------------------------
       // $detalle['validFrom'] = $this->ASN1_UTCTime_ToMysqlDate($arr['validFrom']);
      //  $detalle['validTo'] = $this->ASN1_UTCTime_ToMysqlDate($arr['validTo']);
        return $detalle;
    }

    private function crearCerPem($archivoCer) {
        $comando = 'openssl x509 -inform DER -outform PEM -in "' . $archivoCer . '" -pubkey -out "' . $archivoCer . '.pem"';
        $output = shell_exec($comando);
        if (!file_exists($archivoCer . ".pem")) {
            throw new Exception("No pudo crearse el archivo $archivoCer.pem");
        }
        return "$archivoCer.pem";
    }

    private function crearKeyPem($archivoKey, $pass) {

        $comando = 'openssl pkcs8 -inform DER -in ' . $archivoKey . ' -passin pass:"' . $pass . '" -out ' . $archivoKey . '.pem';
        $output = shell_exec($comando);
        echo $output;
        if (!file_exists($archivoKey . ".pem")) {
            throw new Exception("No pudo crearse el archivo $archivoKey.pem");
        }
        $contenido = file_get_contents($archivoKey . ".pem");
        if ($contenido == '') {
            throw new Exception("contraseña incorrecta");
        }
        return "$archivoKey.pem";
    }

   private  function ASN1_UTCTime_ToMysqlDate($fecha) {
        //The format of the date is YYMMDDHHMMSSZ		
        $año = substr($fecha, 0, 2);
        $mes = substr($fecha, 2, 2);
        $dia = substr($fecha, 4, 2);
        $hora = substr($fecha, 6, 2);
        $minutos = substr($fecha, 8, 2);
        $segundos = substr($fecha, 10, 2);
		$date="20$año-$mes-$dia $hora:$minutos:$segundos";		
        return $date;
    }
    
}
?>
