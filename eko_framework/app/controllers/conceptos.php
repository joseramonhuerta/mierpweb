<?php
require ('eko_framework/app/models/concepto.php');       //MODELO

class Conceptos extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerconceptos(){ //<----------------PARA EL GRID

			 //$params= $_POST;
			/*if ($params['IDEmp']=='' || !isset($params['IDEmp'])){
				throw new Exception("Es necesario logearse en una Empresa para buscar sus Facturas correspondientes");
			}
	*/
            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		    
			


			
            $conceptoModel=new ConceptoModel();
            $response = $conceptoModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$Concepto=array(
		'id_concepto'=>$_POST['id_concepto'],
		'descripcion'=>$_POST['descripcion'],
		'tipo'=>$_POST['tipo'],
		'status'=>$_POST['status']			
		);		
		
		$conceptoModel=new ConceptoModel();
		
		$conceptoGuardado=$conceptoModel->guardar($Concepto);
		if (!$conceptoGuardado)throw new Exception("Error al guardar los datos del concepto");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Conceptos','mensaje'=> 'La información del concepto ha sido guardada satisfactoriamente') ;            
		$response['data']['Concepto']= $conceptoGuardado; 
			
		return $response;
    }

	function obtenerconcepto(){
		$conceptoModel = new ConceptoModel();
						
		$id=$_POST['idCon'];
		$datos = $conceptoModel->getById($id);
		$response['success'] = true;
		$response['data']['Concepto'] = $datos['Concepto'];
			
		return $response;			
	}

	function eliminar(){
		 $conceptoModel=new ConceptoModel();
		$titulo=$conceptoModel->name;
		
		if ( empty($_POST['id_concepto']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Concepto que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_concepto'];	
	
		$conceptoModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El concepto no fue eliminado";
		}else{
			$success=false;
			$mensaje="Concepto eliminado de la base de datos";
		}	
		$data=array('id_concepto'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Conceptos',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_concepto'];
   
		
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
		
		
        $query="UPDATE cat_conceptos SET status='$nuevoStatus' WHERE id_concepto=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_concepto'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Conceptos',
					'mensaje'=>"Error al actualizar el estado del concepto:".mysql_error()
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
					'titulo'=>'Conceptos',
					'mensaje'=>"El concepto ha sido $estado"
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
	
	function obtenertiposgastos(){
		
	}

}
?>
