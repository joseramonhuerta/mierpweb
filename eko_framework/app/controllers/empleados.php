<?php
require ('eko_framework/app/models/empleado.php');       //MODELO

class Empleados extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerempleados(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $empleadoModel=new EmpleadoModel();
            $response = $empleadoModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$Empleado=array(
		'id_empleado'=>$_POST['id_empleado'],
		'codigo_empleado'=>$_POST['codigo_empleado'],
		'nombre_empleado'=>$_POST['nombre_empleado'],
		'celular'=>$_POST['celular'],
		'status'=>$_POST['status']			
		);		
		
		$empleadoModel=new EmpleadoModel();
		
		$empleadoGuardado=$empleadoModel->guardar($Empleado);
		if (!$empleadoGuardado)throw new Exception("Error al guardar los datos del Empleado");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Empleados','mensaje'=> 'La información del empleado han sido guardada satisfactoriamente') ;            
		$response['data']['Empleado']= $empleadoGuardado; 
			
		return $response;
    }

	function obtenerempleado(){
		$empleadoModel = new EmpleadoModel();
						
		$id=$_POST['idEmp'];
		$datos = $empleadoModel->getById($id);
		$response['success'] = true;
		$response['data']['Empleado'] = $datos['Empleado'];
			
		return $response;			
	}

	function eliminar(){
		 $empleadoModel=new EmpleadoModel();
		$titulo=$empleadoModel->name;
		
		if ( empty($_POST['id_empleado']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al empleado que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_empleado'];	
	
		$empleadoModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Empleado no fue eliminada";
		}else{
			$success=false;
			$mensaje="Empleado eliminado de la base de datos";
		}	
		$data=array('id_empleado'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Empleados',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_empleado'];
   
		
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
		
		
        $query="UPDATE cat_empleados SET status='$nuevoStatus' WHERE id_empleado=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_empleado'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Empleados',
					'mensaje'=>"Error al actualizar el estado del Empleado:".mysql_error()
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
					'titulo'=>'Empleados',
					'mensaje'=>"El Empleado ha sido $estado"
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
