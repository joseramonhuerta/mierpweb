<?php
require ('eko_framework/app/models/producto.php');       //MODELO
require ('eko_framework/app/models/unidades.php');
require ('eko_framework/app/models/linea.php');
require_once "eko_framework/app/models/reporte_existencia.php";
require_once "eko_framework/app/models/reporte_existencia_productos_excel.php";
class Productos extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerproductos(){ //<----------------PARA EL GRID

            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		      
            $productoModel=new ProductoModel();
            $response = $productoModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
        
        // $cliente=$_POST['Cliente'];
		//--------------------
		//	Validar Email
		//-----------
		
			
			$Producto=array(
			'id_producto'=>$_POST['id_producto'],
			'codigo'=>$_POST['codigo'],
			'codigo_barras'=>$_POST['codigo_barras'],
			'descripcion'=>$_POST['descripcion'],
			'detalles'=>$_POST['detalles'],
			'tipo_producto'=>$_POST['tipo_producto'],
			'id_unidadmedida'=>$_POST['id_unidadmedida'],
			'id_linea'=>$_POST['id_linea'],
			'precio_venta'=>$_POST['precio_venta'],
			'precio_estilista'=>$_POST['precio_estilista'],
			'precio_compra'=>$_POST['precio_compra'],
			'iva'=>$_POST['iva'],
			'ret_iva'=>$_POST['ret_iva'],
			'ret_isr'=>$_POST['ret_isr'],
			'status'=>$_POST['status'],
			'stock_min'=>$_POST['stock_min'],
			'stock_max'=>$_POST['stock_max']		
			);		
			
			$productoModel=new ProductoModel();
			
			$productoModel->validarProductoDuplicado($Producto);
			
			$productoGuardado=$productoModel->guardar($Producto);
            if (!$productoGuardado)throw new Exception("Error al guardar los datos del Producto");
            
            $response['success'] = true;
            $response['msg'] = array('titulo'=>'Productos','mensaje'=> 'La información del Producto ha sido guardada satisfactoriamente') ;            
            $response['data']['Producto']= $productoGuardado; 
			
			$unidadesModel=new UnidadesModel();
			$lineasModel=new LineaModel();
				 
			$unidades=$unidadesModel->readAll(0, 200, '');  //ESPERO NO TENGAMOS MAS DE 200 UNIDADES DE MEDIDA
			$lineas=$lineasModel->readAll(0, 200, ''); 
			
			
			$response['data']['Unidades']=$unidades['data'];
			$response['data']['Lineas']=$lineas['data'];
			
         

        return $response;
    }

	function obtenerproducto(){
			$productoModel = new ProductoModel();
			$unidadesModel=new UnidadesModel();
			$lineasModel=new LineaModel();
			
		// if (isset($_POST[$productoModel->primaryKey])) {
			$id=$_POST['idPro'];
			

			$datos = $productoModel->getById($id);
				
			$unidades=$unidadesModel->readAll(0, 200, '');  //ESPERO NO TENGAMOS MAS DE 200 UNIDADES DE MEDIDA
			$lineas=$lineasModel->readAll(0, 1000000, ''); 
			
			$response['success'] = true;
            $response['data']['Producto'] = $datos['Producto'];
			$response['data']['Unidades']=$unidades['data'];
			$response['data']['Lineas']=$lineas['data'];
			
		// } else {
			// $response['success'] = false;
			// $response['msg'] = "El servicio está indisponible";
		// }
		return $response;
			
	}

	function eliminar(){
		 $productoModel=new ProductoModel();
		$titulo=$productoModel->name;
		
		if ( empty($_POST['id_producto']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Producto que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_producto'];	
	
		$productoModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Producto no fue eliminado";
		}else{
			$success=false;
			$mensaje="Producto eliminado de la base de datos";
		}	
		$data=array('id_producto'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Productos',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_producto'];
   
		
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
		
		
        $query="UPDATE cat_productos SET status='$nuevoStatus' WHERE id_producto=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_producto'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Productos',
					'mensaje'=>"Error al actualizar el estado del Producto:".mysql_error()
				);
        }else{
            $response['success'] = true;
            $estado='';
            if ($nuevoStatus=="I"){
                $estado="Desactivado";
            }else{
                $estado="Activado";
            }
            $response['msg'] = array(
					'titulo'=>'Productos',
					'mensaje'=>"El Producto ha sido $estado"
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
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
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
				
			$query = " SELECT id_linea,nombre_linea, status FROM cat_lineas $filtro ";
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
	
	
	function generarreporteexistencia(){
		$params = $_POST;
		$reporte=new ReporteExistencia();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repExis']['rand']=$numero_aleatorio ;
		$_SESSION['repExis']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfexistencia(){		
		if (!isset($_SESSION['repExis'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repExis']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repExis']['pdf'];
		
		$reporte=new ReporteExistencia();
		$reporte->getPDF($pdfName);
	}
	
	function generarreporteexistenciaexcel(){
		$params = $_GET;
		$reporte=new ReporteExistenciaProductosExcel();
		
		$pdf=$reporte->generarReporteExcel($params);
	}
	
	function obtenerproductoscombo(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$id_linea = $_POST['id_linea'];
			//$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion'));
			
			$filtro.= ($filtro) ? " AND status = 'A' AND tipo_producto ='P' " : " WHERE status = 'A' AND tipo_producto ='P'";
			
			if($id_linea > 0)
				$filtro.= " AND id_linea = $id_linea";
			
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
	
	function obteneralmacenescombo(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_almacen'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND a.status = 'A' " : " WHERE a.status = 'A' ";
			
			$query = "SELECT COUNT(a.id_sucursal) AS totalrows FROM cat_almacenes a $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
			
			$query = " SELECT a.id_almacen,a.nombre_almacen,su.nombre_sucursal,e.nombre_fiscal as nombre_empresa FROM cat_almacenes a ";	
			$query.= " inner join cat_sucursales su on su.id_sucursal = a.id_sucursal ";
			$query.= " inner join cat_empresas e on e.id_empresa = a.id_empresa $filtro ";
			$query.= " ORDER BY a.nombre_almacen LIMIT $start, $limit ";
			
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
	
	function obtenerproductosmm(){
		
		$id_almacen=$_POST['ID_Almacen'];
		$id_linea=$_POST['ID_Linea'];
		$id_producto=$_POST['ID_Producto'];
	
		if ($id_almacen==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en un Almacen';
			return $response;
		}
	
		$productoModel=new ProductoModel();
		$data=$productoModel->getProductosMM($id_almacen, $id_linea, $id_producto);
		
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function guardarmaximosminimos(){
		$params = $_POST;
		
		$productoModel=new ProductoModel();
			
		$productosGuardado=$productoModel->guardarMaximoMinimos($params);
			
		
		return $productosGuardado;
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
}
?>
