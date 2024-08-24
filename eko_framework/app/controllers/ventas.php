<?php
require ('eko_framework/app/models/venta.php');       //MODELO
require_once "eko_framework/app/models/turno.php";
require_once "eko_framework/app/models/reporte_venta.php";
require_once "eko_framework/app/models/reporte_venta_ticket.php";
require_once "eko_framework/app/models/reporte_ventas.php";
require_once "eko_framework/app/models/reporte_ventas_productos.php";
require_once "eko_framework/app/models/reporte_ventas_productos_excel.php";
require_once "eko_framework/app/models/reporte_pedido_sugerido.php";
require_once "eko_framework/app/models/reporte_ventas_productos_costos.php";
require_once "eko_framework/app/models/reporte_ventas_productos_global.php";
require_once "eko_framework/app/models/reporte_flujo_efectivo_excel.php";
require_once "eko_framework/app/models/reporte_saldos_lineas.php";

//require ('eko_framework/app/models/linea.php');
class Ventas extends ApplicationController {
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
        
		$ventaModel=new VentaModel();
		$response = $ventaModel->readAll($params);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function obtenerseries(){
		try {
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' AND tipo_serie = 4" : " WHERE status = 'A' AND tipo_serie = 4";
			
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
	
	function obtenerformaspagos(){
		try {
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_formapago) AS totalrows FROM cat_formaspagos $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_formapago,nombre_formapago,tipo_formapago FROM cat_formaspagos $filtro ";
			$query.= " ORDER BY id_formapago LIMIT $start, $limit ";
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
	
	function obtenerclientes(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_fiscal'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_cliente) AS totalrows FROM cat_clientes $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_cliente,nombre_fiscal as nombre_cliente,estilista FROM cat_clientes $filtro ";
			$query.= " ORDER BY nombre_cliente LIMIT $start, $limit ";
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
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_agente'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_agente) AS totalrows FROM cat_agentes $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
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
	
	function obtenerlineas(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_linea) AS totalrows FROM cat_lineas $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_linea,nombre_linea FROM cat_lineas $filtro ";
			$query.= " ORDER BY nombre_linea LIMIT $start, $limit ";
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
	
	function obtenersucursales(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_sucursal'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND su.status = 'A' " : " WHERE su.status = 'A' ";
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
			$query.= " INNER JOIN cat_usuarios_privilegios p ON p.id_usuario=$IDUsu AND p.id_privilegio=su.id_sucursal AND p.tipo_privilegio=2";		
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
			$filtro_query= ( empty($_POST['filtro']) )? '' : $_POST['filtro']; 
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
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_producto,descripcion,codigo_barras,codigo FROM cat_productos $filtro ";
			$query.= " ORDER BY id_producto LIMIT $start, $limit ";
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
	
	function obtenerproductosbusqueda(){		
		try {
			$filtro_query= ( empty($_POST['filtro']) )? '' : $_POST['filtro']; 
			// throw new Exception($filtro_query);	
			//$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A'";
			
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
			
			//throw new Exception($query);		
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
	
	function obtenerventasbusqueda(){		
		try {
			$fechainicio= ( empty($_POST['fechainicio']) )? '' : $_POST['fechainicio']; 
			$fechafin= ( empty($_POST['fechafin']) )? '' : $_POST['fechafin'];
			$fechafin.=" 23:59:59";
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal= $_POST['id_sucursal'];
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
			$folio = (empty($_POST['folio'])) ?  '': $_POST['folio']; 
			
			//$filtro = (empty($params['filtro'])) ?  '': $params['filtro']; 
	
			//$filtro = $this->filtroToSQL( $folio ); 
			// throw new Exception($folio);
			if (strlen($folio) > 0) {
				$filtro = " WHERE v.status = 'A' and fecha_venta between '$fechainicio' AND '$fechafin' AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal and CONCAT(serie_venta,' - ',folio_venta) like '%$folio%'";
			} else {
			   $filtro = " WHERE v.status = 'A' and fecha_venta between '$fechainicio' AND '$fechafin' AND id_empresa = $id_empresa AND id_sucursal = $id_sucursal";
			}
			
			
			$query = "SELECT COUNT(id_venta) AS totalrows FROM ventas v $filtro ";
			// throw new Exception($query);
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			// $id_almacen = $_SESSION['Auth']['User']['id_almacen'];
				
			$query = " SELECT v.id_venta,DATE_FORMAT(v.fecha_venta,'%d/%m/%Y') as fecha_venta,CONCAT(v.serie_venta,' - ',v.folio_venta) as serie_folio,c.nombre_fiscal as nombre_cliente,v.total as total_venta";
			$query.= " FROM ventas v";
			$query.= " left join cat_clientes c on c.id_cliente = v.id_cliente";
			$query.= " $filtro ORDER BY v.fecha_venta limit $start,$limit";
			
			// throw new Exception($query);		
			$res = mysqlQuery($query);
			if (!$res)  throw new Exception(mysql_error()." ".$query);
				
				$response = ResulsetToExt::resToArray($res);
				$response['totalRows'] = $total_rows;
				if (isset($response['totalRows'])){			
					$response['total']	=	$response['totalRows'];		
				}
			} catch (Exception $e) {
				$response['totalRows'] = $total_rows;
				if (isset($response['totalRows'])){			
					$response['total']	=	$response['totalRows'];		
				}	
				
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
			$producto = $this->EscComillas($_POST['Descripcion']);
			$id_producto = $_POST['ID'];
			$id_cliente = $_POST['ID_Cliente'];
			
			$query = "SELECT COUNT(id_producto) AS totalrows FROM cat_productos WHERE (descripcion = '$producto' OR codigo = '$producto' OR codigo_barras = '$producto') OR id_producto = $id_producto";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
			
			if ($total_rows > 0){
				$query = " SELECT p.id_producto, p.descripcion, p.codigo_barras, p.codigo, u.codigo_unidad, getPrecioProducto(p.id_producto, $id_cliente) AS precio_venta,p.precio_estilista FROM cat_productos p";
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
	
	function obtenerconfiguracionpos(){
		try {
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal = $_POST['id_sucursal'];
			
			$query = "SELECT COUNT(id_parametro_venta) AS totalrows FROM cat_parametros_ventas WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND status = 'A'";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
			
			$date = new DateTime();
			$fecha_hora= $date->format('Y-m-d H:i:s');
			
			if ($total_rows > 0){
				$query = " SELECT p.id_parametro_venta, p.id_serie, s.nombre_serie, s.foliosig, p.id_cliente,c.nombre_fiscal as nombre_cliente, p.agrega_concepto_auto,CONCAT(s.nombre_serie,'-',LPAD(s.foliosig,10,'0')) as seriefolio,DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha_venta,impresion_ticket,
				ifnull(p.mostrar_agente,0) as mostrar_agente
				FROM cat_parametros_ventas p";
				$query.= " INNER JOIN cat_series s on s.id_serie = p.id_serie";
				$query.= " INNER JOIN cat_clientes c on c.id_cliente = p.id_cliente";
				$query.= " WHERE p.id_empresa = $id_empresa AND p.id_sucursal = $id_sucursal AND p.status = 'A';";
				// throw new Exception($query);
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
	
	function obtenerventa(){
		$ventaModel=new VentaModel();

		$id=$_POST['idVen'];
				
		$data=$ventaModel->getById($id);
		
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function save(){
		$params = $_POST;
		
		$ventaModel=new VentaModel();
		$resp = $ventaModel->guardar($params);
		
		if (!$resp)throw new Exception("Error al guardar la venta");
		
		$response=array();
        $response['success']=true;
		$response['msg'] = array('titulo'=>'Ventas','mensaje'=> 'La venta se ha guardado') ;            
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
		$ventaModel=new VentaModel();
		$titulo=$ventaModel->name;
		
		if ( empty($_POST['IDVen']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la Venta que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['IDVen'];	
	
		$ventaModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La Venta no fue eliminada";
		}else{
			$success=true;
			$mensaje="Venta eliminada";
		}	
		$data=array('IDVen'=>$id);
		
		return array(
			'success'=>$success,
			'msg'=>array(
					'titulo'=>'Ventas',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}

	function generarreporteventa(){
		$params = $_POST;
		$reporte=new ReporteVentaTicket();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repVen']['rand']=$numero_aleatorio ;
		$_SESSION['repVen']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfventa(){		
		if (!isset($_SESSION['repVen'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repVen']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repVen']['pdf'];
		
		$reporte=new ReporteVentaTicket();
		$reporte->getPDF($pdfName);
	}
	
	function generarreporteventas(){
		$params = $_POST;
		$reporte=new ReporteVentas();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repVtas']['rand']=$numero_aleatorio ;
		$_SESSION['repVtas']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfventas(){		
		if (!isset($_SESSION['repVtas'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repVtas']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repVtas']['pdf'];
		
		$reporte=new ReporteVentas();
		$reporte->getPDF($pdfName);
	}
	
	function generarreporteventasproductos(){
		$params = $_POST;
		$reporte=new ReporteVentasProductos();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repVtasPro']['rand']=$numero_aleatorio ;
		$_SESSION['repVtasPro']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfventasproductos(){		
		if (!isset($_SESSION['repVtasPro'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repVtasPro']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repVtasPro']['pdf'];
		
		$reporte=new ReporteVentasProductos();
		$reporte->getPDF($pdfName);
	}
	
	function generarreporteventasproductoscostos(){
		$params = $_POST;
		$reporte=new ReporteVentasProductosCostos();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repVtasPro']['rand']=$numero_aleatorio ;
		$_SESSION['repVtasPro']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfventasproductoscostos(){		
		if (!isset($_SESSION['repVtasPro'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repVtasPro']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repVtasPro']['pdf'];
		
		$reporte=new ReporteVentasProductosCostos();
		$reporte->getPDF($pdfName);
	}
	
	
	function generarreporteventasproductosexcel(){
		$params = $_GET;
		$reporte=new ReporteVentasProductosExcel();
		
		$pdf=$reporte->generarReporteExcel($params);
	}
	
	function generarreporteventasproductosglobalexcel(){
		$params = $_GET;
		$reporte=new ReporteVentasProductosGlobal();
		
		$pdf=$reporte->generarReporteExcel($params);
	}
	
	function generarreporteventasproductosglobalexcelagrupado(){
		$params = $_GET;
		$reporte=new ReporteVentasProductosGlobal();
		
		$pdf=$reporte->generarReporteExcelAgrupado($params);
	}
	
	function generarreporteventasproductosglobalpdf(){
		$params = $_POST;
		$params['IDSucOrigen'] = $_SESSION['Auth']['User']['id_sucursal'];
		$reporte=new ReporteVentasProductosGlobal();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repVtaProGlo']['rand']=$numero_aleatorio ;
		$_SESSION['repVtaProGlo']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;		
	}
	
	function getpdfventasproductosglobal(){		
		if (!isset($_SESSION['repVtaProGlo'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repVtaProGlo']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repVtaProGlo']['pdf'];
		
		$reporte=new ReporteVentasProductosGlobal();
		$reporte->getPDF($pdfName);
	}
	
	function generarreportepedidosugerido(){
		$params = $_GET;
		$params['IDSucOrigen'] = $_SESSION['Auth']['User']['id_sucursal'];
		$reporte=new ReportePedidoSugerido();
		
		$pdf=$reporte->generarReporteExcel($params);
	}
	
	function generarreportepedidosugeridopdf(){
		$params = $_POST;
		$params['IDSucOrigen'] = $_SESSION['Auth']['User']['id_sucursal'];
		$reporte=new ReportePedidoSugerido();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repPedSug']['rand']=$numero_aleatorio ;
		$_SESSION['repPedSug']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfpedidosugerido(){		
		if (!isset($_SESSION['repPedSug'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repPedSug']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repPedSug']['pdf'];
		
		$reporte=new ReportePedidoSugerido();
		$reporte->getPDF($pdfName);
	}
	
	public function EscComillas($texto){
		//return 	$texto;
		// return addslashes($texto);
    	return str_replace ( "'" ,"\'" ,$texto);
	}
	
	function obtenersucursalesempresa(){
		try {
			$idEmpresa = ( empty($_POST['id_empresa']) )? 0 : $_POST['id_empresa'];
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_sucursal'));
			// throw new Exception($filtro);
			$filtro.= ($filtro) ? " AND su.status = 'A' AND su.id_empresa = $idEmpresa" : " WHERE su.status = 'A' AND su.id_empresa = $idEmpresa";
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
			$query.= " INNER JOIN cat_usuarios_privilegios p ON p.id_usuario=$IDUsu AND p.id_privilegio=su.id_sucursal AND p.tipo_privilegio=2";		
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

	function generarreporteflujoefectivo(){
		$params = $_GET;
		$reporte=new ReporteFlujoEfectivoExcel();
		
		$pdf=$reporte->generarReporteExcel($params);
	}

	function generarreportesaldoslineaspdf(){
		$params = $_POST;
		
		$reporte=new ReporteSaldosLineas();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repSalLin']['rand']=$numero_aleatorio ;
		$_SESSION['repSalLin']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfsaldoslineas(){		
		if (!isset($_SESSION['repSalLin'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repSalLin']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repSalLin']['pdf'];
		
		$reporte=new ReporteSaldosLineas();
		$reporte->getPDF($pdfName);
	}
}
?>
