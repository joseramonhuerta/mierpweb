<?php
require ('eko_framework/app/models/movimiento_banco.php');       //MODELO
require ('eko_framework/app/models/serie.php');
require_once "eko_framework/app/models/reporte_movimientos_bancos.php";
require_once "eko_framework/app/models/reporte_movimiento_banco_ticket.php";

class MovimientosBanco extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

    function obtenermovimientosbancos(){ //<----------------PARA EL GRID
		$params = $_POST;
			if ($params['IDEmpresa']=='' || !isset($params['IDEmpresa'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar los inventarios.");
		}
        
		$movimientoBancoModel=new MovimientoBancoModel();
		$response = $movimientoBancoModel->readAll($params);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function obtenermovimientobanco(){
		$movimientobancoModel=new MovimientoBancoModel();

		$id=$_POST['idMovBanco'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
				
		if ($id_empresa==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear abonos';
			return $response;
		}
		
		if ($id_sucursal==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Sucursal para crear abonos';
			return $response;
		}
		
		IF($id==0){
			$data=array();
			$data['MovimientoBanco']=$movimientobancoModel->getInitialInfo($id_empresa,$id_sucursal);
					
		}else{
			$data=$movimientobancoModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function obtenerseries(){
		try {
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal = $_POST['id_sucursal'];
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie = 4" : " WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie = 10";
			
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
	
	function obtenerseriesgastos(){
		try {
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal = $_POST['id_sucursal'];
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie = 4" : " WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie = 11";
			
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
	
	function obtenerconceptos(){
		try {
			
			$filtro="";
			$id_tipo = $_POST['id_tipo'];
			$filtro.= ($filtro) ? " AND r.status = 'A'" : " WHERE r.status = 'A'";
			$filtro.= " AND r.tipo = $id_tipo";
			
			$query = "SELECT COUNT(r.id_concepto) AS totalrows FROM cat_conceptos r 
						$filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 50 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT r.id_concepto, r.descripcion FROM cat_conceptos r ";
			$query.= "$filtro ORDER BY r.descripcion LIMIT $start, $limit ";
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
	
	function obtenerchequeras(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A'";
			
			$query = "SELECT COUNT(id_chequera) AS totalrows FROM cat_chequeras $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 50 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_chequera,descripcion FROM cat_chequeras $filtro ";
			$query.= " ORDER BY descripcion LIMIT $start, $limit ";
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
	
	function obtenerconceptosreporte(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND c.status = 'A' " : " WHERE c.status = 'A' ";
			
			$query = "SELECT COUNT(c.id_concepto) AS totalrows FROM cat_conceptos c $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
			
			$query = " SELECT c.id_concepto,c.descripcion,case c.tipo when 1 then 'INGRESO' when 2 then 'EGRESO' end as tipo FROM cat_conceptos c ";	
			$query.= "$filtro ORDER BY c.descripcion LIMIT $start, $limit ";
			
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
	
	
	function save(){
		$params = $_POST;
		
		$movimientobancoModel=new MovimientoBancoModel();
		
		$resp = $movimientobancoModel->guardar($params);
		
		$response=array();
        $response['success']=true;
        $response['msg'] = array('titulo'=>'Movimientos Bancos','mensaje'=> 'La información del Movimiento Banco ha sido guardada satisfactoriamente') ;            
            
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
		$movimientoModel=new MovimientoBancoModel();
		$titulo=$movimientoModel->name;
		
		if ( empty($_POST['id_movimiento_banco']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Movimiento Banco que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_movimiento_banco'];	
	
		$movimientoModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Movimiento de banco no fue eliminado";
		}else{
			$success=true;
			$mensaje="Movimiento de banco eliminado de la base de datos";
		}	
		$data=array('id_movimiento_banco'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Movimientos Bancos',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}
	
	function generarreportemovimientobanco(){
		$params = $_POST;
		$reporte=new ReporteMovimientoBancoTicket();
		
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
	
	function getpdfmovimientobanco(){		
		if (!isset($_SESSION['repMov'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repMov']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repMov']['pdf'];
		
		$reporte=new ReporteMovimientoBancoTicket();
		$reporte->getPDF($pdfName);
	}
	
	function generarreportemovimientosbancos(){
		$params = $_GET;
		$reporte=new ReporteMovimientosBancos();
		
		$pdf=$reporte->generarReporteExcel($params);
	}
	
	public function EscComillas($texto){
		//return 	$texto;
		// return addslashes($texto);
    	return str_replace ( "'" ,"\'" ,$texto);
    }
}
?>
