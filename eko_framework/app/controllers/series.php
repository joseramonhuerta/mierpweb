<?php
require ('eko_framework/app/models/serie.php');       //MODELO

class Series extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerseries(){ //<----------------PARA EL GRID

			 //$params= $_POST;
			/*if ($params['IDEmp']=='' || !isset($params['IDEmp'])){
				throw new Exception("Es necesario logearse en una Empresa para buscar sus Facturas correspondientes");
			}
	*/
            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		    $id_empresa = (empty($_POST['id_empresa'])) ?  0 : $_POST['id_empresa'];
			$id_sucursal = (empty($_POST['id_sucursal'])) ?  0 : $_POST['id_sucursal'];
			


			
            $serieModel=new SerieModel();
            $response = $serieModel->readAll($start,$limit,$filtro,$filtroStatus, $id_empresa,$id_sucursal );
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$Serie=array(
		'id_empresa'=>$_POST['id_empresa'],
		'id_sucursal'=>$_POST['id_sucursal'],
		'id_serie'=>$_POST['id_serie'],
		'nombre_serie'=>$_POST['nombre_serie'],
		'folioinicio'=>$_POST['folioinicio'],
		'foliofin'=>$_POST['foliofin'],
		'tipo_serie'=>$_POST['tipo_serie'],
		'status'=>$_POST['status']			
		);		
		
		$serieModel=new SerieModel();
		
		$serieGuardado=$serieModel->guardar($Serie);
		if (!$serieGuardado)throw new Exception("Error al guardar los datos de la Serie");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Series','mensaje'=> 'La información de la Serie ha sido guardada satisfactoriamente') ;            
		$response['data']['Serie']= $serieGuardado; 
			
		return $response;
    }

	function obtenerserie(){
		$serieModel = new SerieModel();
						
		$id=$_POST['idSer'];
		$datos = $serieModel->getById($id);
		$response['success'] = true;
		$response['data']['Serie'] = $datos['Serie'];
			
		return $response;			
	}

	function eliminar(){
		 $serieModel=new SerieModel();
		$titulo=$serieModel->name;
		
		if ( empty($_POST['id_serie']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Producto que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_serie'];	
	
		$serieModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La Serie no fue eliminada";
		}else{
			$success=false;
			$mensaje="Serie eliminada de la base de datos";
		}	
		$data=array('id_serie'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Series',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_serie'];
   
		
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
		
		
        $query="UPDATE cat_series SET status='$nuevoStatus' WHERE id_serie=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_serie'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Series',
					'mensaje'=>"Error al actualizar el estado de la Serie:".mysql_error()
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
					'titulo'=>'Series',
					'mensaje'=>"La Serie ha sido $estado"
				);
			
			$response['data'] = $data;
        }
		
        return $response;
	}
	
	function obtenerunidadesdemedida(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro = (isset($_POST['query'])) ? $this->filtroToSQL($_POST['query']) : '';
			// $filtro = $this->filtroToSQL($_POST['query'],);
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion_unidad'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_unidadmedida) AS totalrows FROM cat_unidadesdemedida $filtro ";
			
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_unidadmedida,codigo_unidad, descripcion_unidad, status FROM cat_unidadesdemedida $filtro ";
			$query.= " ORDER BY id_unidadmedida LIMIT $start, $limit ";
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

	function obtenerlineas(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro = (isset($_POST['query'])) ? $this->filtroToSQL($_POST['query']) : '';
			// $filtro = $this->filtroToSQL($_POST['query'],);
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A' ";
			
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_linea) AS totalrows FROM cat_lineas $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_linea,descripcion_linea, status FROM cat_lineas $filtro ";
			$query.= " ORDER BY id_linea LIMIT $start, $limit ";
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
