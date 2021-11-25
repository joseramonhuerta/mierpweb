<?php
require ('eko_framework/app/models/chequera.php');       //MODELO

class Chequeras extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerchequeras(){ //<----------------PARA EL GRID

			 //$params= $_POST;
			/*if ($params['IDEmp']=='' || !isset($params['IDEmp'])){
				throw new Exception("Es necesario logearse en una Empresa para buscar sus Facturas correspondientes");
			}
	*/
            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		    
			


			
            $chequeraModel=new ChequeraModel();
            $response = $chequeraModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$Chequera=array(
		'id_chequera'=>$_POST['id_chequera'],
		'descripcion'=>$_POST['descripcion'],
		'status'=>$_POST['status']			
		);		
		
		$chequeraModel=new ChequeraModel();
		
		$chequeraGuardado=$chequeraModel->guardar($Chequera);
		if (!$chequeraGuardado)throw new Exception("Error al guardar los datos de la chequera");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Chequeras','mensaje'=> 'La información de la chequera ha sido guardada satisfactoriamente') ;            
		$response['data']['Chequera']= $chequeraGuardado; 
			
		return $response;
    }

	function obtenerchequera(){
		$chequeraModel = new ChequeraModel();
						
		$id=$_POST['idChe'];
		$datos = $chequeraModel->getById($id);
		$response['success'] = true;
		$response['data']['Chequera'] = $datos['Chequera'];
			
		return $response;			
	}

	function eliminar(){
		 $chequeraModel=new ChequeraModel();
		$titulo=$chequeraModel->name;
		
		if ( empty($_POST['id_chequera']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la chequera que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_chequera'];	
	
		$chequeraModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La chequera no fue eliminada";
		}else{
			$success=false;
			$mensaje="Chequera eliminada de la base de datos";
		}	
		$data=array('id_chequera'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Chequeras',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_chequera'];
   
		
		$statusOld=$_POST['status'];			
		if ($statusOld=='A'){
			$nuevoStatus="I";
		}else if ($statusOld=='I'){
			$nuevoStatus="A";
		}else{
			return array(
				'success'=>false,
				'msg'=>array(
					'titulo'=>'Error en la peticion de cambio de status',
					'mensaje'=>"El estado (<span style='font-weight:bold;'>$statusOld</span>) es desconocido por el sistema."
				)				
			);
		}
		
		
        $query="UPDATE cat_chequeras SET status='$nuevoStatus' WHERE id_chequera=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_chequera'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Chequeras',
					'mensaje'=>"Error al actualizar el estado de la chequera:".mysql_error()
				);
        }else{
            $response['success'] = true;
            $estado='';
            if ($nuevoStatus=="I"){
                $estado="Desactivada";
            }else{
                $estado="Activada";
            }
            $response['msg'] = array(
					'titulo'=>'Chequeras',
					'mensaje'=>"La chequera ha sido $estado"
				);
			
			$response['data'] = $data;
        }
		
        return $response;
	}
	
	function filtroToSQL($filtro,$camposAfiltrar=array()) {
     	 $where = '';
     	 
        if (!empty($filtro)) {
			$filtroArray = explode(" ", $filtro);
	        $condiciones = "";
	        $condicion = "";

	        foreach ($camposAfiltrar as $campo) {
	
	            foreach ($filtroArray as $text) {
	                if (strlen($text) > 0){
						$condicion.="$campo LIKE '%$text%' AND ";	 									
					}
	            }
	
	            if (strlen($condicion) > 0) {
	                $condicion = substr($condicion, 0, strlen($condicion) - 4); //<----LE BORRO LA ULTIMA PARTE "AND ";
	                $condicion = "(" . $condicion . ") OR ";
	                $condiciones.=$condicion;
	                $condicion = "";
	            }
	        }
	       
	        if (strlen($condiciones) > 0) {
	            $condiciones = substr($condiciones, 0, strlen($condiciones) - 3); //<----LE BORRO LA ULTIMA PARTE "or ";
	            $where = "WHERE ($condiciones)";
	        }
        }
        return $where;
    }	
	
}
?>
