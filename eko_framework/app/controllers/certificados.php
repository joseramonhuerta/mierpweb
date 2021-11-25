<?php
require ('eko_framework/app/models/certificado.php');       //MODELO
require ('eko_framework/app/models/empresa.php');       //MODELO
require ('eko_framework/lib/rmkdir_r.php');       //CREAR DIRECTORIOS

class Certificados extends ApplicationController {
    
    function obtenercertificados(){ //<----------------PARA EL GRID
        try {            
             $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro'];
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];		
			$empresaId=$_POST['id_empresa'];
            
			if ($_POST['id_empresa']=='' || !isset($_POST['id_empresa'])){
				throw new Exception("Es necesario logearse en una Empresa para ver sus certificados");
			}    
            $modelObject=new CertificadoModel();
            $response = $modelObject->getCertificados($start,$limit,$filtro,$empresaId,$filtroStatus);
        } catch (Exception $e) {
            $response['succes'] = false;
            $response['msg'] = $e->getMessage();
        }
        return $response;        
    }

    function obtenercertificado(){
        $modelObject = new CertificadoModel();
        if (isset($_POST['idCer'])) {

            $id = $_POST['idCer'];
            $datos = $modelObject->getById($id);
              
            $response['success'] = true;
            $response['data'] = $datos;
        } else {
            $response['success'] = false;
            $response['msg'] = "El servicio está indisponible";
            
        }
        return $response;
    }


    private function moveUploadedCert($ruta_temp){
        if (!empty($_FILES['archivo_certificado']['name'])) {
            $CertfileInfo = $_FILES['archivo_certificado'];
            $tempPathFileCer = "$ruta_temp" . $CertfileInfo['name'];
            $cerTempName = $CertfileInfo['tmp_name'];
            
            if (!move_uploaded_file($cerTempName, $tempPathFileCer)) {                
                 throw new Exception('Error al subir el certificado:'.$CertfileInfo['name']);
            }
            return  $ruta_temp.$CertfileInfo['name'];
        } else {
            throw new Exception('No se recibió el certificado');
        }
    }

    private function moveUploadedKey($ruta_temp){
        if (!empty($_FILES['archivo_llave']['name'])) {
            $KeyfileInfo = $_FILES['archivo_llave'];
            $tempPathFileKey = "$ruta_temp" . $KeyfileInfo['name'];
            $keyTempName = $KeyfileInfo['tmp_name'];

            if (!move_uploaded_file($keyTempName, $tempPathFileKey)) {
                throw new Exception('"Error al subir la llave:'.$KeyfileInfo['name']);                                
            }
            return $ruta_temp.$KeyfileInfo['name'];
        } else {
            throw new Exception('No se recibió la llave');
        }
    }

   
    function guardar(){

        $modelObject = new CertificadoModel();
       
        $datos = array();
        $response = array();
        $params['pass_certificado'] = $_POST['pass_certificado'];
		$params['id_empresa'] =  $_SESSION['Auth']['User']['id_empresa'];

        // $accion=$_POST['pass_certificado'];
		
        if (!empty($_FILES['archivo_certificado']['name']) || !empty($_FILES['archivo_certificado']['key'])) {
			// throw new Exception($params['id_empresa']);

            $KEYEmpCer=$params['id_empresa'];
            $corpId = $_SESSION['Auth']['User']['IDCor'];
            $ruta_temp = "tmp/certificados/$corpId/$KEYEmpCer/";
            !@rmkdir_r($ruta_temp, 0775, true);     //<--------------- crear el directorio donde se guardaran los archivos
			
            $cerFileTempFullPath=$this->moveUploadedCert($ruta_temp);   //<-------Muevo archivo subido a una carpeta temporal
            $keyFileTempFullPath=$this->moveUploadedKey($ruta_temp);    //<-------Muevo archivo subido a una carpeta temporal
			
            
            $detalles=$modelObject->leerCertificados($cerFileTempFullPath,$keyFileTempFullPath,$params['id_empresa'],$params['pass_certificado']);
            $params['pem_llave']=$detalles['KeyPemData'];
            $params['pem_certificado']=$detalles['CerPemData'];

            $params['numero_certificado']=$detalles['serie'];
            $params['rfc_certificado']=$detalles['rfc'];
            $params['fecha_solicitud']=$detalles['validFrom'];
            $params['fecha_vencimiento']=$detalles['validTo'];

            $params['archivo_certificado']=$cerFileTempFullPath;
            $params['archivo_llave']=$keyFileTempFullPath;
             
            $params['razonsocial_certificado']=$detalles['SubjectNameCer'];
           
        }else{
            $params['pass_certificado']='';
        }

		// throw new Exception("nada");
        $registroAsArray = $modelObject->guardar($params);

        $response['success'] = true;
        $response['msg'] = array(
			'titulo'=>'Certificados',
			'mensaje'=>'Información almacenada satisfactoriamente');
        $response['data'] = $registroAsArray[$modelObject->name];

        return $response;
    }

    function remove(){
        $modelObject = new CertificadoModel();
        $idValue = $_POST[$modelObject->primaryKey];
        $response = array();
        try {
            $idBorrado = $modelObject->delete($idValue);
            $response['success'] = true;
            $response['msg'] = array('titulo'=>'Certificados','mensaje'=>'Certificado eliminado'); 
            $response['data'] = array($modelObject->primaryKey => $idBorrado);
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msg'] = 'no se eliminó el certificado con Id=' . $idValue . $e->getMessage();
        }
        return $response;
    }
}
?>
