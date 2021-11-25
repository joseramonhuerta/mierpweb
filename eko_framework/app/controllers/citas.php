<?php
require ('eko_framework/app/models/cita.php');       //MODELO

class Citas extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

    function obtenercitas(){ //<----------------PARA EL GRID
		$params = $_POST;
			if ($params['IDEmpresa']=='' || !isset($params['IDEmpresa'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar Remisiones.");
		}
        
		$citaModel=new CitaModel();
		$response = $citaModel->readAll($params);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function obtenercita(){
		$citaModel=new CitaModel();

		$id=$_POST['idCit'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
	
		
		if ($id_empresa==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear citas';
			return $response;
		}
		
		if ($id_sucursal==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Sucursal para crear citas';
			return $response;
		}
		
		IF($id==0){
			$data=array();
			$data['Cita']=$citaModel->getInitialInfo($id_empresa,$id_sucursal);
					
		}else{
			$data=$citaModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function save(){
		$params = $_POST;
		
		$citaModel=new CitaModel();
		
		$resp = $citaModel->guardar($params);		
		
		return $resp;
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
	
	function eliminar(){
		$citaModel=new CitaModel();
		$titulo=$citaModel->name;
		
		if ( empty($_POST['id_cita']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la Cita que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_cita'];	
	
		$citaModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La cita no fue eliminada";
		}else{
			$success=true;
			$mensaje="Cita eliminada de la base de datos";
		}	
		$data=array('id_cita'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Citas',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}

	function obtenerclientes(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_fiscal'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A'";
			
			$query = "SELECT COUNT(id_cliente) AS totalrows FROM cat_clientes $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 50 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_cliente,nombre_fiscal FROM cat_clientes $filtro ";
			$query.= " ORDER BY id_cliente LIMIT $start, $limit ";
			$res = mysqlQuery($query);
			if (!$res)  throw new Exception(mysql_error()." ".$query);
				
			$response = ResulsetToExt::resToArray($res);
			$response['totalRows'] = $total_rows;
		} catch (Exception $e) {
			$response['totalRows'] = $total_rows;
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		echo json_encode($response);
		
	}	

	function obteneragentes(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_agente'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A'";
			
			$query = "SELECT COUNT(id_agente) AS totalrows FROM cat_agentes $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 50 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_agente,nombre_agente FROM cat_agentes $filtro ";
			$query.= " ORDER BY nombre_agente LIMIT $start, $limit ";
			$res = mysqlQuery($query);
			if (!$res)  throw new Exception(mysql_error()." ".$query);
				
			$response = ResulsetToExt::resToArray($res);
			$response['totalRows'] = $total_rows;
		} catch (Exception $e) {
			$response['totalRows'] = $total_rows;
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		echo json_encode($response);
		
	}	
	
	function obtenerhorarios(){
		try {
			
			$filtro="";
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal = $_POST['id_sucursal'];
			$id_agente = $_POST['id_agente'];
			$fecha = $_POST['fecha'];
			$id_cita = $_POST['id_cita'];
			
			$filtro.= " WHERE h.status = 'A'";
			$filtro.= " AND h.id_horario NOT IN (
			SELECT id_horario FROM citas WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND fecha = '$fecha'  AND id_agente = $id_agente	AND (($id_cita > 0 AND id_cita != $id_cita)  OR ($id_cita = 0))		
			)
			
			";
			
			$query = "SELECT COUNT(h.id_horario) AS totalrows FROM cat_horarios h 
					 $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 50 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT h.id_horario, concat(DATE_FORMAT(h.hora_inicio,'%H:%i:%S'),'-',DATE_FORMAT(h.hora_fin,'%H:%i:%S')) AS descripcion_horario FROM cat_horarios h ";
			$query.= "$filtro ORDER BY h.hora_inicio,h.hora_fin LIMIT $start, $limit ";
			$res = mysqlQuery($query);
			if (!$res)  throw new Exception(mysql_error()." ".$query);
				
			//throw new Exception($query);	
				
			$response = ResulsetToExt::resToArray($res);
			$response['totalRows'] = $total_rows;
		} catch (Exception $e) {
			$response['totalRows'] = $total_rows;
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		echo json_encode($response);
	}

}
?>
