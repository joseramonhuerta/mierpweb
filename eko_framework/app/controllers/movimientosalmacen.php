<?php
require ('eko_framework/app/models/movimiento_almacen.php');       //MODELO
require ('eko_framework/app/models/serie.php');
require_once "eko_framework/app/models/reporte_movimiento_almacen.php";
require_once "eko_framework/app/models/reporte_movimiento_almacen_carta.php";
//require ('eko_framework/app/models/linea.php');
class MovimientosAlmacen extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

    function obtenermovimientos(){ //<----------------PARA EL GRID
		$params = $_POST;
			if ($params['IDEmpresa']=='' || !isset($params['IDEmpresa'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar los movimientos de almacén.");
		}
        
		$movimientoModel=new MovimientoAlmacenModel();
		$response = $movimientoModel->readAll($params);
      
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
			$filtro.= ($filtro) ? " AND id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie in (2,7,8)" : " WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie in (2,7,8)";
			
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
			
			$query = "SELECT COUNT(id_producto) AS totalrows FROM cat_productos WHERE (descripcion = '$producto' OR codigo = '$producto' OR codigo_barras = '$producto') OR id_producto = $id_producto";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
			
			if ($total_rows > 0){
				$query = " SELECT p.id_producto, p.descripcion, p.codigo_barras, p.codigo, u.codigo_unidad, p.precio_venta as precio_compra FROM cat_productos p";
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
	
	function obtenermovimiento(){
		$movimientoModel=new MovimientoAlmacenModel();

		$id=$_POST['idMov'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
		$id_almacen=$_POST['id_almacen'];
		
		if ($id_empresa==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear movimientos de almacen';
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
			$data['Movimiento']=$movimientoModel->getInitialInfo($id_empresa,$id_sucursal,$id_almacen);
					
		}else{
			$data=$movimientoModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function save(){
		$params = $_POST;
		
		$movimientoModel=new MovimientoAlmacenModel();
		
		$resp = $movimientoModel->guardar($params);
		
		// throw new Exception($resp['Movimiento']['id_movimiento']);
		//$data=array();
		//$data['Movimiento']['id_movimiento_almacen']= 1;
		$response=array();
        $response['success']=true;
        $response['msg'] = array('titulo'=>'Movimientos Almacen','mensaje'=> 'La información del Movimiento ha sido guardada satisfactoriamente') ;            
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

	function generarreportemovimiento(){
		$params = $_POST;
		$reporte=new ReporteMovimientoAlmacen();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repMov']['rand']=$numero_aleatorio ;
		$_SESSION['repMov']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfmovimiento(){		
		if (!isset($_SESSION['repMov'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repMov']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repMov']['pdf'];
		
		$reporte=new ReporteMovimientoAlmacen();
		$reporte->getPDF($pdfName);
	}
	
	function generarreportemovimientocarta(){
		$params = $_POST;
		$reporte=new ReporteMovimientoAlmacenCarta();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repMov']['rand']=$numero_aleatorio ;
		$_SESSION['repMov']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfmovimientocarta(){		
		if (!isset($_SESSION['repMov'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repMov']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repMov']['pdf'];
		
		$reporte=new ReporteMovimientoAlmacenCarta();
		$reporte->getPDF($pdfName);
	}

	
	function obtenermovimientosbusqueda(){		
		try {
			$fechainicio= ( empty($_POST['fechainicio']) )? '' : $_POST['fechainicio']; 
			$fechafin= ( empty($_POST['fechafin']) )? '' : $_POST['fechafin'];
			$fechafin.=" 23:59:59";
			$id_empresa = $_POST['id_empresa'];
						
			
			$filtro = " WHERE m.fecha_movimiento between '$fechainicio' AND '$fechafin' AND t.tipo_movimiento in (2,3)";
			
			$query = "SELECT COUNT(m.id_movimiento) AS totalrows FROM movimientos_almacen m 
					left join cat_tiposmovimientos t on t.id_tipomovimiento = m.id_tipomovimiento
					$filtro ";
			// throw new Exception($query);
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			// $id_almacen = $_SESSION['Auth']['User']['id_almacen'];
				
			$query = " SELECT m.id_movimiento,DATE_FORMAT(m.fecha_movimiento,'%d/%m/%Y') as fecha_movimiento,CONCAT(m.serie_movimiento,' - ',m.folio_movimiento) as serie_folio,
			s.nombre_sucursal,ifnull(ao.nombre_almacen,'') as nombre_almacen_origen,m.total as total";
			$query.= " FROM movimientos_almacen m";
			$query.= " left join cat_sucursales s on s.id_sucursal = m.id_sucursal";
			$query.= " left join cat_almacenes ao on ao.id_almacen = m.id_almacen_origen";
			$query.= " left join cat_tiposmovimientos t on t.id_tipomovimiento = m.id_tipomovimiento";
			$query.= " $filtro ORDER BY m.fecha_movimiento";
			
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

	function obtenerproductosmovimiento(){
		$movimientoModel=new MovimientoAlmacenModel();

		$id=$_POST['idMov'];		
		
		if($id > 0){
			$data=$movimientoModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function obteneragentes(){
		try {
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
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
			$query.= " ORDER BY id_agente LIMIT $start, $limit ";
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
}
?>
