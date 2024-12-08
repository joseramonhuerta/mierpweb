<?php
require ('eko_framework/app/models/inventario.php');       //MODELO
require ('eko_framework/app/models/serie.php');
require_once "eko_framework/app/models/reporte_inventario.php";

class Inventarios extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

    function obtenerinventarios(){ //<----------------PARA EL GRID
		$params = $_POST;
			if ($params['IDEmpresa']=='' || !isset($params['IDEmpresa'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar los inventarios.");
		}
        
		$inventarioModel=new InventarioModel();
		$response = $inventarioModel->readAll($params);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function obtenerseries(){
		try {
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal = $_POST['id_sucursal'];
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie = 4" : " WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie = 4";
			
			$query = "SELECT COUNT(id_serie) AS totalrows FROM cat_series $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_serie,nombre_serie, foliosig FROM cat_series $filtro ";
			$query.= " ORDER BY id_serie LIMIT $start, $limit ";
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
	
	function obtenertiposmovimientos(){
		try {
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_tipomovimiento) AS totalrows FROM cat_tiposmovimientos $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_tipomovimiento,nombre_movimiento,tipo_movimiento FROM cat_tiposmovimientos $filtro ";
			$query.= " ORDER BY id_tipomovimiento LIMIT $start, $limit ";
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
	
	function obteneralmacenes(){
		try {
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal = $_POST['id_sucursal'];

			
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			$filtro.= " AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal";
			
			$query = "SELECT COUNT(id_almacen) AS totalrows FROM cat_almacenes $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_almacen,nombre_almacen FROM cat_almacenes $filtro ";
			$query.= " ORDER BY id_almacen LIMIT $start, $limit ";
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
	
	function obtenerproductos(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			//$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			throw new Exception($filtro_query);		
			$filtro.= ($filtro) ? " AND status = 'A' AND tipo_producto ='P' " : " WHERE status = 'A' AND tipo_producto ='P'";
			
			$query = "SELECT COUNT(id_producto) AS totalrows FROM cat_productos $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_producto,descripcion,codigo_barras,codigo FROM cat_productos $filtro ";
			$query.= " ORDER BY id_producto LIMIT $start, $limit ";
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
	
	function obtenerproductosbusqueda(){		
		try {
			$filtro_query= ( empty($_POST['filtro']) )? '' : $_POST['filtro']; 
			// throw new Exception($filtro_query);	
			//$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' AND tipo_producto ='P' " : " WHERE status = 'A' AND tipo_producto ='P'";
			
			$query = "SELECT COUNT(id_producto) AS totalrows FROM cat_productos $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$id_almacen = $_SESSION['Auth']['User']['id_almacen'];
				
			$query = " SELECT p.id_producto,p.descripcion,p.codigo_barras,p.codigo,IFNULL(s.stock,0) as stock";
			$query.= " FROM cat_productos p";
			$query.= " left join cat_productos_stocks s on s.id_producto = p.id_producto and s.id_almacen = $id_almacen ";
			$query.= " $filtro ORDER BY p.descripcion";
			
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

	function obetenerdetalles($id){
		$detalleModel =  new MovimientoAlmacenDetalleModel();

		$params=array(
			'filtros'=>array(
				array('filtro'=>"id_movimiento=$id")
			)
		);
		$detalles=$detalleModel->readAll(0, 1000, '',$params,true);

		$detalles = $detalles['data'];

		return $detalles;


	}
	
	function obtenerproducto(){
		try {
			$producto = $_POST['Descripcion'];
			$id_producto = $_POST['ID'];
			$id_almacen = $_POST['IDAlmacen'];
			$query = "SELECT COUNT(id_producto) AS totalrows FROM cat_productos WHERE (descripcion = '$producto' OR codigo = '$producto' OR codigo_barras = '$producto') OR id_producto = $id_producto";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
			
			if ($total_rows > 0){
				$query = " SELECT p.id_producto, p.descripcion, p.codigo_barras, p.codigo, u.codigo_unidad, p.precio_venta as precio_compra,IFNULL(s.stock,0) as stock FROM cat_productos p";
				$query.= " INNER JOIN cat_unidadesdemedida u on u.id_unidadmedida = p.id_unidadmedida";
				$query.= " left join cat_productos_stocks s on s.id_producto = p.id_producto and s.id_almacen = $id_almacen ";
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
	
	function obtenerinventario(){
		$inventarioModel=new InventarioModel();

		$id=$_POST['idInv'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
		$id_almacen=$_POST['id_almacen'];
		
		if ($id_empresa==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear Inventarios Fisicos';
			return $response;
		}
		
		if ($id_sucursal==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Sucursal para crear movimientos de almacen';
			return $response;
		}
		
		if ($id_almacen==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en un Almacen para crear movimientos de almacen';
			return $response;
		}
		
		IF($id==0){
			$data=array();
			$data['Inventario']=$inventarioModel->getInitialInfo($id_empresa,$id_sucursal,$id_almacen);
					
		}else{
			$data=$inventarioModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function save(){
		$params = $_POST;
		
		$inventarioModel=new InventarioModel();
		
		$resp = $inventarioModel->guardar($params);
		
		// throw new Exception($resp['Movimiento']['id_movimiento']);
		//$data=array();
		//$data['Movimiento']['id_movimiento_almacen']= 1;
		$response=array();
        $response['success']=true;
        $response['data']=$resp;
		
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
	
	function eliminar(){
		$movimientoModel=new MovimientoAlmacenModel();
		$titulo=$movimientoModel->name;
		
		if ( empty($_POST['id_movimiento']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Movimiento que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_movimiento'];	
	
		$movimientoModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Movimiento no fue eliminado";
		}else{
			$success=true;
			$mensaje="Movimiento eliminado de la base de datos";
		}	
		$data=array('id_movimiento'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Movimientos Almacen',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}
	
	function aplicar(){
		$inventarioModel=new InventarioModel();
		$titulo=$inventarioModel->name;
		
		if ( empty($_POST['id_inventario']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de aplicar",'mensaje'=>"Debe proporcionar la referencia al Inventario Físico que desea aplicar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_inventario'];	
	
		$resp = $inventarioModel->aplicar($id);
		
		$response=array();
        $response['success']=true;
        $response['data']=$resp;
		
        return $response;
		
	}
	
	function generarreporteinventario(){
		$params = $_POST;
		$reporte=new ReporteInventario();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repInv']['rand']=$numero_aleatorio ;
		$_SESSION['repInv']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfinventario(){		
		if (!isset($_SESSION['repInv'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repInv']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repInv']['pdf'];
		
		$reporte=new ReporteInventario();
		$reporte->getPDF($pdfName);
	}

}
?>
