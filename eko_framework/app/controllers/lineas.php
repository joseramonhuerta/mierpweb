<?php
require ('eko_framework/app/models/linea.php');       //MODELO

class Lineas extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerlineas(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $lineaModel=new LineaModel();
            $response = $lineaModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$Linea=array(
		'id_linea'=>$_POST['id_linea'],
		'nombre_linea'=>$_POST['nombre_linea'],
		'status'=>$_POST['status']			
		);		
		
		$lineaModel=new LineaModel();
		
		$lineaGuardado=$lineaModel->guardar($Linea);
		if (!$lineaGuardado)throw new Exception("Error al guardar los datos del Linea");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Lineas','mensaje'=> 'La información del Linea han sido guardada satisfactoriamente') ;            
		$response['data']['Linea']= $lineaGuardado; 
			
		return $response;
    }

	function obtenerlinea(){
		$lineaModel = new LineaModel();
						
		$id=$_POST['idLin'];
		$datos = $lineaModel->getById($id);
		$response['success'] = true;
		$response['data']['Linea'] = $datos['Linea'];
			
		return $response;			
	}

	function eliminar(){
		 $lineaModel=new LineaModel();
		$titulo=$lineaModel->name;
		
		if ( empty($_POST['id_linea']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la Linea que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_linea'];	
	
		$lineaModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Linea no fue eliminada";
		}else{
			$success=false;
			$mensaje="Linea eliminado de la base de datos";
		}	
		$data=array('id_linea'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Lineas',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_linea'];
   
		
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
		
		
        $query="UPDATE cat_lineas SET status='$nuevoStatus' WHERE id_linea=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_linea'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Lineas',
					'mensaje'=>"Error al actualizar el estado del Linea:".mysql_error()
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
					'titulo'=>'Lineas',
					'mensaje'=>"El Linea ha sido $estado"
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
