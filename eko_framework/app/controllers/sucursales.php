<?php
require ('eko_framework/app/models/sucursal.php');       //MODELO
require ('eko_framework/app/models/ciudad.php');       //MODELO
require ('eko_framework/app/models/pais.php');       //MODELO
class Sucursales extends ApplicationController {
    
    function readAll(){ //<----------------PARA EL GRID
        try {            
             $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro'];

            
           if ($_POST['filtrarActivos']=='true'){
                $filtrarActivos=true;
            }else{
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
                $filtrarActivos=false;
            }
            $sucursalModel=new SucursalModel();
            $response = $sucursalModel->readAll($start,$limit,$filtro,$filtrarActivos);
        } catch (Exception $e) {
            $response['succes'] = false;
            $response['msg'] = $e->getMessage();
        }

        return $response;
        
    }

    public function setStatus(){
        $modelObject = new SucursalModel();
        $id=$_POST['IDValue'];
        $status=$_POST['status'];
        try{
            $modelObject->setStatus($id,$status);
            if ($status=="A"){
                $estado="Activada";
            }else{
                $estado="Suspendida";
            }
			
            $response['success'] = true;
            $response['msg'] =array('titulo'=>'Sucursales','mensaje'=> "La Sucursal ha sido ha sido $estado");
        }catch(Exception $e){
            $response['success'] = false;
            $response['msg'] = "Error al tratar de cambiar el estado, consulte con el administrado del sistema";
        }
		
        return $response;
    }

    function read(){
        $modelObject = new SucursalModel();
      //  $unidadesModel=new UnidadesModel();
        if (isset($_POST['idValue'])) {

            $id = $_POST['idValue'];
            $datos = $modelObject->getById($id);
              
            $response['success'] = true;
            $response['data'] = $datos;        

            $ciudadId = $datos['Sucursal']['MunSuc'];
            $paisId = $datos['Sucursal']['PaisSuc'];
            $estadoId = $datos['Sucursal']['EstSuc'];
            $ciudadModel = new CiudadModel();
            
            if (is_numeric($ciudadId)) {
                $ciudad = $ciudadModel->getCiudadEstadoYpais($ciudadId, $estadoId, $paisId);
                
            } else {
                $paisModel = new PaisModel();
                $pais = $paisModel->getById($paisId);
                $nomPais = $pais['Pais']['nom_pai'];
                $city = array(
                    'id_ciu' => '0',
                    'nom_ciu' => $ciudadId,
                    'nom_est' => $estadoId,
                    'id_est' => $estadoId,
                    'id_pai' => $paisId,
                    'nom_pai' => $nomPais,
                );
                $ciudad = array();
                $ciudad[0] = $city;
            }
            $response['data']['Ciudad'] = $ciudad[0];

            //====================================================
            $query="select IDImp,DescImp,ActivoImp,IDTaR,KEYTasaTaR,DescTasa,ActivoTasa,ImpTasa FROM cat_impuestos i
            LEFT JOIN cat_tasas_relaciones r ON r.KEYImpTaR=i.IDImp AND OrigenTaR='S' AND KEYOrigenTar=$id
            LEFT JOIN cat_tasas t ON t.IDTasa=r.KEYTasaTaR;";
            $resImpuestos=mysqlQuery($query);
            if (!$resImpuestos)throw new Exception(mysql_error());
            $impuestos=array();
            while ($obj = @mysql_fetch_object($resImpuestos)) {
                    $impuestos[] = $obj;
                }

             $response['data']['Impuestos'] = $impuestos;

             
             $empresaId=$datos['Sucursal']['KEYEmpSuc'];             

             $certificados=$modelObject->getCertificados($empresaId);
             $response['data']['Certificados'] = $certificados;

        } else {
            $response['success'] = false;			
            $response['msg'] =array('titulo'=>'Sucursales','mensaje'=>"El servicio está indisponible") ;
        }
        return $response;
    }
	
    function save(){
             $modelObject = new SucursalModel();
            $datos = array();
            $response = array();
            $params = $_POST[$modelObject->name];
            $impuestos=json_decode(stripslashes($_POST['impuestos']),true);
            $certificados=json_decode(stripslashes($_POST['certificados']),true);
            try {
                $modelObject->setImpuestos($impuestos);
                $modelObject->setCertificados($certificados);
                $registroAsArray = $modelObject->guardar($params);

                $impuestos=$modelObject->getTasas();
                
                if (!$registroAsArray)throw new Exception("Error al guardar la Sucursal");
                $response['success'] = true;
                $response['msg'] =array('titulo'=>'Sucursales','mensaje'=>'Sucursal Guardada Satisfactoriamente') ;
                $response['data'] = $registroAsArray[$modelObject->name];
				$empresaId=$registroAsArray[$modelObject->name]['KEYEmpSuc'];
				$certificados=$modelObject->getCertificados($empresaId);
				$response['data']['Certificados'] = $certificados;


                $response['impuestos'] = $impuestos;
            } catch (Exception $e) {
                $response['success'] = false;
                $response['msg'] = $e->getMessage();
            }
            return $response;

    }

    function remove(){

        $modelObject = new SucursalModel();
        $idValue = $_POST[$modelObject->primaryKey];
        $response = array();
        try {
            $idBorrado = $modelObject->delete($idValue);
            $response['success'] = true;
            $response['msg'] = array('titulo'=>'Sucursales','mensaje'=>'Sucursal eliminada');
            $response['data'] = array($modelObject->primaryKey => $idBorrado);
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msg'] = 'no se eliminó la sucursal con Id=' . $idValue . $e->getMessage();
        }
        return $response;
    }
}
?>
