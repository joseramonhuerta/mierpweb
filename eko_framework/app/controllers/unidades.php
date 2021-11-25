<?php

require ('eko_framework/app/models/unidades.php'); 

class Unidades extends ApplicationController {
	
	function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
   
	 function obtenerunidades(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $unidadModel=new UnidadesModel();
            $response = $unidadModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_unidadmedida'];
   
		
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
		
		
        $query="UPDATE cat_unidadesdemedida SET status='$nuevoStatus' WHERE id_unidadmedida=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_unidadmedida'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Unidades de Medida',
					'mensaje'=>"Error al actualizar el estado de la Unidad de Medida:".mysql_error()
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
					'titulo'=>'Unidades de Medida',
					'mensaje'=>"La unidad de medida ha sido $estado"
				);
			
			$response['data'] = $data;
        }
		
        return $response;
	}
	
	function obtenerunidad(){
		$unidadModel = new UnidadesModel();
						
		$id=$_POST['idUni'];
		$datos = $unidadModel->getById($id);
		$response['success'] = true;
		$response['data']['UnidadMedida'] = $datos['UnidadMedida'];
			
		return $response;			
	}
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$UnidadMedida=array(
		'id_unidadmedida'=>$_POST['id_unidadmedida'],
		'codigo_unidad'=>$_POST['codigo_unidad'],
		'descripcion_unidad'=>$_POST['descripcion_unidad'],
		'status'=>$_POST['status']			
		);		
		
		$unidadModel=new UnidadesModel();
		
		$unidadGuardado=$unidadModel->guardar($UnidadMedida);
		if (!$unidadGuardado)throw new Exception("Error al guardar los datos de la Unidad de Medida");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Unidades de Medida','mensaje'=> 'La información de la Unidad de Medida ha sido guardada satisfactoriamente') ;            
		$response['data']['UnidadMedida']= $unidadGuardado; 
			
		return $response;
    }

	
	function eliminar(){
		 $unidadModel=new UnidadesModel();
		$titulo=$unidadModel->name;
		
		if ( empty($_POST['id_unidadmedida']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la Unidad de Medida que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_unidadmedida'];	
	
		$unidadModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La Unidad de Medida no fue eliminada";
		}else{
			$success=false;
			$mensaje="Unidad de Medida eliminada de la base de datos";
		}	
		$data=array('id_unidadmedida'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Unidades de Medida',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	/******************/
		
	
	
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