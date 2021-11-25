<?php
require ('eko_framework/app/models/agente.php');       //MODELO

class Agentes extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obteneragentes(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $agenteModel=new AgenteModel();
            $response = $agenteModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$Agente=array(
		'id_agente'=>$_POST['id_agente'],
		'nombre_agente'=>$_POST['nombre_agente'],
		'status'=>$_POST['status']			
		);		
		
		$agenteModel=new AgenteModel();
		
		$agenteGuardado=$agenteModel->guardar($Agente);
		if (!$agenteGuardado)throw new Exception("Error al guardar los datos del Agente");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Agentes','mensaje'=> 'La información del Agente han sido guardada satisfactoriamente') ;            
		$response['data']['Agente']= $agenteGuardado; 
			
		return $response;
    }

	function obteneragente(){
		$agenteModel = new AgenteModel();
						
		$id=$_POST['idAge'];
		$datos = $agenteModel->getById($id);
		$response['success'] = true;
		$response['data']['Agente'] = $datos['Agente'];
			
		return $response;			
	}

	function eliminar(){
		 $agenteModel=new AgenteModel();
		$titulo=$agenteModel->name;
		
		if ( empty($_POST['id_agente']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Producto que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_agente'];	
	
		$agenteModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Agente no fue eliminada";
		}else{
			$success=false;
			$mensaje="Agente eliminado de la base de datos";
		}	
		$data=array('id_agente'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Agentes',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_agente'];
   
		
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
		
		
        $query="UPDATE cat_agentes SET status='$nuevoStatus' WHERE id_agente=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_agente'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Agentes',
					'mensaje'=>"Error al actualizar el estado del Agente:".mysql_error()
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
					'titulo'=>'Agentes',
					'mensaje'=>"El Agente ha sido $estado"
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
