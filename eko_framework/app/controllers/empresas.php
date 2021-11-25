<?php
require ('eko_framework/app/models/empresa.php');       //MODELO
require ('eko_framework/app/models/regimen_fiscal.php');       //MODELO
require ('eko_framework/lib/rmkdir_r.php');       //CREAR DIRECTORIOS
require ('eko_framework/lib/upload.php');      
class Empresas extends ApplicationController {
    var $components=array(
		'ACL'=>array(
			'allowedActions'=>array('findForCombo'))
	);
    function getEmpresas(){ //<----------------PARA EL GRID        
        try {            
            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro'];

            if ($_POST['filtrarActivos']=='true'){
                $filtrarActivos=true;                
            }else{
                $filtrarActivos=false;
            	if (!$_SESSION['Auth']['User']['AdminUsu'] && !$_SESSION['Auth']['User']['super']){
                	$response=array(
                		'success'=>false,
                		'data'=>array(),
                		'msg'=>array(
                			'titulo'=>'Buscador de Empresas',
                			'mensaje'=>"Solo el administrador puede ver las empresas suspendidas"
                		)
                	);
                	return $response;
                }
            }
            
            $filtro=$_POST['filtro'] ;
            $empresaModel=new Empresa();
            $response = $empresaModel->getEmpresasParaGrid($start,$limit,$filtro,$filtrarActivos);
        } catch (Exception $e) {
            $response['succes'] = false;
            $response['msg'] =array('titulo'=>'Empresas','mensaje'=>$e->getMessage()) ;
        }

        return $response;
    }

    public function findForCombo(){
		$ini_id = $_POST['inicial'];
		if ($_POST['inicial']=='' || !isset($_POST['inicial'])){
				throw new Exception("Es necesario logearse en una Empresa");
		}
		$query  = " SELECT IDEmp, ComEmp,CFDiEmp, RFCEmp, IF(StatusEmp = 'A',1,0) AS StatusActivo, IF(IDEmp = $ini_id, '0', '1') AS orden FROM cat_empresas ";
		if (!$_SESSION['Auth']['User']['AdminUsu'] && !$_SESSION['Auth']['User']['super']){
			// Si no es ADMIN ni SUPER, muestra las empresas permitidas y que esten activas
			$query .= " LEFT JOIN cat_usuarios_privilegios ON(KEYUsuPriv = '".$_SESSION['Auth']['User']['IDUsu']."' AND KEYID = IDEmp) ";
			$query .= " WHERE StatusEmp = 'A' AND Origen = 'EMP' ";
		}
		$model=new Model(); 
		$arrRes=$model->query($query);
		$response=array(
			'success'=>true,
			'data'=>$arrRes		
		);
		return $response;
		//$query .= " ORDER BY orden, StatusActivo DESC, ComEmp  ;";
		//echo mysqlGridPaginado($query);
	}

    public function findEmpSucs(){
        if ($_SESSION['Auth']['User']['super']==true){
                $tipoUser =2;
        }else{
            $tipoUser = $_SESSION['Auth']['User']['AdminUsu'];
        }
        $userId = $_SESSION['Auth']['User']['IDUsu'];
        $empresaModel=new Empresa();
        
        switch ($tipoUser) {
            case 2: //Super User
                $empresas = $empresaModel->obtenerTodasLasEmpresas();
                break;
            case 1: //Admin
                $empresas = $empresaModel->obtenerTodasLasEmpresas();
                break;
            default:    //User
                $empresas = $empresaModel->obtenerEmpresasConPermiso($userId);
                break;
        }
        $response['success']=true;
        $response['data']=$empresas;
        return $response;
    }
	
    function get(){
        try{
            $idParam = $_POST['IDEmp'];

            $empresaModel=new Empresa();
            
            $resArr = $empresaModel->getById($idParam);
            $empresa =$resArr['Empresa'];
            //$empresa = $this->Empresa->getEmpresa($idParam);
            $response['success'] = true;
            $response['data'] = $empresa;
        }catch(exception $e){
            $response['success']=false;
            $response['msg']=$e->getMessage();
        }
        return $response;
    }
	
    public function setStatus(){
        $idValue=$_POST['IDEmp'];
        $status=$_POST['StatusEmp'];
        $query="UPDATE cat_empresas SET StatusEmp='$status' WHERE IDEmp=$idValue";
        $result=mysqlQuery($query);
        $response=array();
        if (!$result){
            $response['success']=false;
            $response['msg']="Error al actualizar el estado de la Empresa:".mysql_error();
        }else{
            $response['success'] = true;
            $estado='';
            if ($status=="I"){
                $estado="Desactivada";
            }else{
                $estado="Activada";
            }
            $response['msg'] =array('titulo'=>'Empresas','mensaje'=>"La Empresa ha sido $estado") ;
        }
        return $response;
    }
	
    function getEmpresaYCiudad(){
        try {
            $idParam = $_POST['IDEmp'];
            $empresaModel = new Empresa();
            $empresa = $empresaModel->getById($idParam);    //<--Obtuve los datos de la tabla empresa 
			
            //====================================================
            $ciudad= $empresa['Empresa']['MunEmp'];
            $estado = $empresa['Empresa']['EstEmp'];
            $pais = $empresa['Empresa']['PaisEmp'];
            $query = "CALL spCatCiudadesConsultar($ciudad, $estado, $pais, '', 0, 0);";
            //$resCiudad = mysqlQuery($query);                      //<--Obtuve los datos de la tabla ciudad
			$resCiudad=$empresaModel->query($query);
			//echo print_r($resCiudad);
            if (empty($resCiudad)) throw new Exception(mysql_error());
            //====================================================
            $query="SELECT IDImp,DescImp,ActivoImp,IDTaR,KEYTasaTaR,DescTasa,ActivoTasa,ImpTasa FROM cat_impuestos 
            LEFT JOIN cat_tasas_relaciones ON KEYImpTaR = IDImp AND OrigenTaR = 'E' AND KEYOrigenTar= $idParam 
            LEFT JOIN cat_tasas ON IDTasa = KEYTasaTaR;";
           // $resImpuestos=mysqlQuery($query);
			$impuestos=$empresaModel->query($query);
			//echo print_r($impuestos);
            if (!$impuestos)throw new Exception(mysql_error());

			
            $certificados=$empresaModel->getCertificados($idParam);
			
			$queryRegimens="SELECT Regimen_EmpReg Regimen,ID_EmpReg 
			FROM cat_empresas_regimen 
			WHERE KEY_Emp_EmpReg=$idParam
			ORDER BY Regimen_EmpReg ASC";
			
			$regimens_fiscales=$empresaModel->select($queryRegimens);
            
            $response['data'] ['Ciudad'] = $resCiudad[0];
            $response['success'] = true;
            $response['data']['Empresa'] = $empresa['Empresa'];
            $response['data']['Impuestos'] = $impuestos;
			$response['data']['Regimens'] = $regimens_fiscales;
            $response['data']['Certificados'] = $certificados;
        } catch (exception $e) {
            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }
        echo json_encode($response);
    }
	
    function getimpuestos(){
         try {
            $empresaModel = new Empresa();            
            //====================================================
            $query = "SELECT IDImp,DescImp,ActivoImp,NULL AS IDTaR,NULL AS KEYTasaTaR FROM cat_impuestos;";
            $resImpuestos = mysqlQuery($query);
            if (!$resImpuestos)throw new Exception(mysql_error());
            $impuestos = array();
            while ($obj = @mysql_fetch_object($resImpuestos)) {
                $impuestos[] = $obj;
            }
            //====================================================                                    
            $response['success'] = true;            
            $response['data']['Impuestos'] = $impuestos;
        } catch (exception $e) {
            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }
        echo json_encode($response);
    }
	
    function guardar(){
        $empresa=array();
        $response=array();
        
        $empresa=$_POST['Empresa'];
        $empresa['TipoEmp']=$_POST['TipoEmp'];
        $empresa['CFDiEmp']= (isset($_POST['CFDiEmp'])) ? $_POST['CFDiEmp'] : '';
        
        $empresaModel=new Empresa();

        $impuestos=json_decode(stripslashes($_POST['impuestos']),true);			
        $empresaModel->setImpuestos($impuestos);

        $certificados=json_decode(stripslashes($_POST['certificados']),true);
        $empresaModel->setCertificados($certificados);

		$regimensEnviados=json_decode(stripslashes($_POST['regimens']),true);
		$numRegimens=sizeof($regimensEnviados);
		$regimens=array();
		for($i=0; $i<$numRegimens; $i++){			
			$regimen=$regimensEnviados[$i];
			if (empty($regimen['Regimen'])){
				if ( empty($regimen['ID_EmpReg'] )){
					continue;
				}else{
					throw new Exception("Es Obligatorio establecer un nombre para cada régimen fiscal de la empresa");
				}
			}else{
				$regimens[]=$regimen;
			}
		}
        $empresaModel->regimens=$regimens;
		
		$regimensEliminados=json_decode(stripslashes($_POST['regimensEliminados']),true);
        $empresaModel->regimensEliminados=$regimensEliminados;
		
        $empresaGuardada=$empresaModel->guardar($empresa);

        $impuestos=$empresaModel->getTasas();
        $certificados=$empresaModel->getCertificados($empresaModel->id);
            
		$queryRegimens="SELECT Regimen_EmpReg Regimen,ID_EmpReg 
			FROM cat_empresas_regimen 
			WHERE KEY_Emp_EmpReg=$empresaModel->id
			ORDER BY Regimen_EmpReg ASC";
		$regimens_fiscales=$empresaModel->select($queryRegimens);
			
        $response['success'] = true;
        $response['msg'] =array('titulo'=>'Empresas','mensaje'=>'La información se ha guardado satisfactoriamente');            
        $response['data'] = $empresaGuardada['Empresa'];
        $response['impuestos'] = $impuestos;            
		$response['Regimens'] = $regimens_fiscales;            
        $response['Certificados'] = $certificados;
        return $response;
    }
    
    function actualizar(){
        $this->render('/ajax/default');
    }
	
    function delete() {
        $idValue = $_POST['IDEmp'];
        $response = array();
        
        $empresaModel = new Empresa();
        $idBorrado = $empresaModel->delete($idValue);
        $response['success'] = true;
        $response['msg'] = array('titulo'=>'Empresas','mensaje'=>'Empresa eliminada');
        $response['data'] = array($empresaModel->primaryKey => $idBorrado);
        
        return $response;
    }
	
    function validaRFC(){
        $rfc = $_POST['rfc'];
        $emp = $_POST['id_emp'];

        if (empty($emp)) {
            $res = mysqlQuery("SELECT 1 FROM cat_empresas WHERE RFCEmp = '$rfc' ");
        } else {
            $res = mysqlQuery("SELECT 1 FROM cat_empresas WHERE RFCEmp = '$rfc' AND IDEmp != $emp ");
        }
        return mysql_num_rows($res);
    }
    function cambiarModo(){
    	//-------------------------------------------------------------------------------------------------------
    	//			Para esta funcion necesitamos que se nos envie el ID de la empresa, vamos a validarlo
    	//-------------------------------------------------------------------------------------------------------
    	if(!isset($_POST['IDEmp'])){	//validar que la variabla haya sido enviada
    		$response=array(
	    		'success'=>false,	    		
	    		'msg'=>array(
	    			'titulo'=>'Error cambiando el modo de facturación',
	    			'mensaje'=>'Información incompleta para procesar la petición'
	    		)
	    	);
	    	return $response;    		
    	}    	    	   
    	if(!is_numeric($_POST['IDEmp'])){	//validar que sea un identificador válido	(al parecer esto es trabajo del modelo)
    		$response=array(
	    		'success'=>false,	    		
	    		'msg'=>array(
	    			'titulo'=>'Error cambiando el modo de facturación',
	    			'mensaje'=>'la información recibida por el servidor es inválida'
	    		)
	    	);
    		return $response;
    	}
    	//-------------------------------------------------------------------------------------------------------
    	$empresaModel=new Empresa();
    	//-------------------------------------------------------------------------------------------------------
    	// 			cambiarModo devuelve 0 para CFD,1 para CFDI, un error cachable en otro caso
    	//------------------------------------------------------------------------------------------------------- 
    	$IDEmp=$_POST['IDEmp']; 
    	$nuevoModo=$empresaModel->cambiarModoDeFacturacion($IDEmp);
    	$modo=($nuevoModo)? 'CFDI': 'CFD';
    	
    	$response=array(
    		'success'=>true,
    		'data'=>array('nuevoModo'=>$nuevoModo),
    		'msg'=>array(
    			'titulo'=>'Empresa',
    			'mensaje'=>'Modo cambiado a '.$modo. "<br/>(Todavia no se implementan las restricciones)"
    		)
    	);
    	
    	return $response;
    	
    	
    }
	
	function getCedulas(){
		require ('eko_framework/app/models/cedula_model.php');       //MODELO
		
		$model=new CedulaModel();
		
		$limit  = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
        $start  = (empty($_POST['start'])) ?  0 : $_POST['start'];
        $filtro = (empty($_POST['filtro']))?  '': $_POST['filtro'];
        $IDEmp =  (empty($_POST['IDEmp'])) ?  0: $_POST['IDEmp'];
				
		$params['filtros']=array(
			0=>array(
				'campo'=>"$model->name.KEY_EmpresaCedula",
				'condicion'=>'=',
				'valor'=>$IDEmp
			)
		);			
		
		return $model->readAll($start,$limit, $filtro,$params,true);
	}
	
	function guardarCedula(){
		require ('eko_framework/app/models/cedula_model.php');       //MODELO
		
		$params=array(
			'idCedula'			=>$_POST['id'],
			'KEY_EmpresaCedula' =>$_POST['KEY_EmpresaCedula'],
			'descripcionCedula' =>$_POST['observacion']						
		);
		#=======================================================================================
		# 							.Guardar la Imagen.
		#=======================================================================================
		if ( !empty($_FILES['imagen']['name']) ){
			$fupload = new Upload();
			//Guardarlas primero en tmp, despues moverlas a la carpeta destino
			
			$rfc = $_SESSION['Auth']['User']['RFCEmp'] ;			
			$corpId='cor_'.$_SESSION['Auth']['User']['IDCor'];
			
			rmkdir_r("cedulas/$corpId/$rfc/", 0775, true);     //<--------------- crear el directorio donde se guardaran los archivos
			$ruta = "cedulas/$corpId/$rfc/";
			$fupload->setPath($ruta);        
			$fupload->setFile("imagen");
			$fupload->allowed[]='image/pdf';
			$fupload->isimage=false;
			
			$nombre=$_FILES['imagen']['name'];
			if ( file_exists($ruta.$nombre) ){
				throw new Exception("El archivo con nombre $nombre  ya existe, intente cambiar el nombre");
			}
			$fupload->save();

			
			if ($fupload->isupload===false){
				throw new Exception($fupload->message);
			}
			$params['rutaImagen']=$ruta.$_FILES['imagen']['name'];
			$params['fileName']  = $_FILES['imagen']['name'];			
		}else{
			if ( empty($_POST['id']) ){
				throw new Exception("Es necesario que suba el archivo con la cédula");
			}
		}

		#=======================================================================================
		$model=new CedulaModel();				
		$data=$model->save($params);
		
		//if ( empty($data['Cedula']['ModFecha']))
		return array(
			'success'=>true,
			'data'=>$data['Cedula'],
			'msg'=>array('titulo'=>'Cedulas fiscales','mensaje'=>'Cédula almacenada de manera correcta')
		);		
	}
	
	function verCedula(){		
		$cedulaId=intval( $_GET['cedula'] );
		require ('eko_framework/app/models/cedula_model.php'); 
		$model=new CedulaModel();	
		
		$arrData=$model->getById( $cedulaId );
		$sesionEmp=$_SESSION['Auth']['User']['IDEmp'];
				
		$imagen=$arrData['Cedula']['rutaImagen'];
		$nombre=$arrData['Cedula']['nombre'];
		
		header("Content-disposition: attachment; filename=$nombre");
		header("Content-type: application/octet-stream");
		readfile($imagen);		
	}
	
	function borrarCedula(){
		require ('eko_framework/app/models/cedula_model.php'); 
		$cedulaId=intval( $_POST['idCedula'] );
		
		$model=new CedulaModel();
		
		$arrData=$model->getById( $cedulaId );		
		$rutaImagen=$arrData['Cedula']['rutaImagen'];
		$idBorrado = $model->delete($cedulaId);
		unlink($rutaImagen);	
				
        $response['success'] = true;
        $response['msg']     = array('titulo'=>'Empresas','mensaje'=>'Cédula Borrada');
        $response['data']    = array();
        
        return $response;
	}
}
?>
