<?php
require ('eko_framework/app/models/lista_precio.php');       //MODELO

class ListaPrecios extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerlistaprecios(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $listaprecioModel=new ListaPrecioModel();
            $response = $listaprecioModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }

	function obtenerproducto(){
		try {
			$producto = $_POST['Descripcion'];
			$id_producto = $_POST['ID'];
			
			$query = "SELECT COUNT(id_producto) AS totalrows FROM cat_productos WHERE (descripcion = '$producto' OR codigo = '$producto' OR codigo_barras = '$producto') OR id_producto = $id_producto";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
			
			if ($total_rows > 0){
				$query = " SELECT p.id_producto, p.descripcion, p.codigo_barras, p.codigo, u.codigo_unidad, p.precio_venta, p.valor_puntos FROM cat_productos p";
				$query.= " INNER JOIN cat_unidadesdemedida u on u.id_unidadmedida = p.id_unidadmedida";
				$query.= " WHERE (p.descripcion = '$producto' OR p.codigo = '$producto' OR p.codigo_barras = '$producto') OR p.id_producto = $id_producto";
				$query.= " ORDER BY p.descripcion;";
				$res = mysqlQuery($query);
				if (!$res)  throw new Exception(mysql_error()." ".$query);
					
				$response = ResulsetToExt::resToArray($res);
			}else{
				$response['success']    = false;
			}
				
		} catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
		echo json_encode($response);
		
	}		
	
	function save(){
		$params = $_POST;
		
		$listaprecioModel=new ListaPrecioModel();
		
		$resp = $listaprecioModel->guardar($params);
		
		$response=array();
        $response['success']=true;
        $response['msg'] = array('titulo'=>'Lista Precios','mensaje'=> 'La información de la Lista de Precios ha sido guardada satisfactoriamente') ;            
        $response['data']=$resp;
		
        return $response;
	}

	function obtenerlista(){
		$listaprecioModel=new  ListaPrecioModel();

		$id=$_POST['idLis'];
				
		if($id==0){
			$data=array();
			$data['ListaPrecio']=$listaprecioModel->getInitialInfo();
					
		}else{
			$data=$listaprecioModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}

	function eliminar(){
		$listaprecioModel=new ListaPrecioModel();
		$titulo=$listaprecioModel->name;
		
		if ( empty($_POST['id_listaprecio']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la lista de precios que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_listaprecio'];	
	
		$listaprecioModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La lista de precios no fue eliminado";
		}else{
			$success=true;
			$mensaje="Lista de precios eliminado de la base de datos";
		}	
		$data=array('id_listaprecio'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Lista Precio',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}
	/*
	function obtenerlinea(){
		$lineaModel = new LineaModel();
		$sucursalModel=new Sucursal();

		$id=$_POST['idLin'];
		$id_suc=$_POST['idSuc'];
		$datos = $lineaModel->getById($id, $id_suc);

		//$sucursales=$sucursalModel->readAll(0, 100000, '', false);

		$response['success'] = true;
		$response['data']['Linea'] = $datos['Linea'];
		//$response['data']['Sucursales']=$sucursales['data'];	
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
	
	function obtenersucursales(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$id_sucursal= ( empty($_POST['id_sucursal']) )? '' : $_POST['id_sucursal']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_sucursal'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND su.status = 'A' AND su.id_sucursal != $id_sucursal" : " WHERE su.status = 'A' AND su.id_sucursal != $id_sucursal";
			
			$IDUsu = $_SESSION['Auth']['User']['IDUsu'];
			$query = "SELECT COUNT(su.id_sucursal) AS totalrows FROM cat_sucursales su $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
			
			$query = " SELECT su.id_sucursal,su.nombre_sucursal,su.id_empresa,e.nombre_fiscal as nombre_empresa FROM cat_sucursales su ";
			$query.= " inner join cat_empresas e on e.id_empresa = su.id_empresa $filtro ";
			$query.= " ORDER BY su.nombre_sucursal LIMIT $start, $limit ";
			
			// throw new Exception($query);	
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
	*/
}
?>