<?php
require ('eko_framework/app/models/horario.php');       //MODELO

class Horarios extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerhorarios(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $horarioModel=new HorarioModel();
            $response = $horarioModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $params = $_POST;
		
		$horarioModel=new HorarioModel();
		
		$resp = $horarioModel->guardar($params);		
		
		return $resp;
    }

	function obtenerhorario(){
		$horarioModel = new HorarioModel();
						
		$id=$_POST['idHor'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
		$id_almacen=$_POST['id_almacen'];
		
		if ($id_empresa==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear horarios';
			return $response;
		}
		
		if ($id_sucursal==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Sucursal para crear horarios';
			return $response;
		}
		IF($id==0){
			$data=array();
			$data['Horario']=$horarioModel->getInitialInfo($id_empresa,$id_sucursal);
					
		}else{
			$data = $horarioModel->getById($id);
		}
		
		
		$response['success'] = true;
		$response['data']= $data;
			
		return $response;			
	}

	function eliminar(){
		 $horarioModel=new HorarioModel();
		$titulo=$horarioModel->name;
		
		if ( empty($_POST['id_horario']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al horario que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_horario'];	
	
		$horarioModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Horario no fue eliminada";
		}else{
			$success=false;
			$mensaje="Horario eliminado de la base de datos";
		}	
		$data=array('id_horario'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Horarios',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_horario'];
   
		
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
		
		
        $query="UPDATE cat_horarios SET status='$nuevoStatus' WHERE id_horario=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_horario'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Horarios',
					'mensaje'=>"Error al actualizar el estado del Horario:".mysql_error()
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
					'titulo'=>'Horarios',
					'mensaje'=>"El Horario ha sido $estado"
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
