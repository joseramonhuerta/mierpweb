<?php
require_once "eko_framework/app/models/movimiento_caja.php";
require_once "eko_framework/app/models/reporte_movimiento_caja_ticket.php";
class MovimientosCaja extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

    function obtenermovimientoscaja(){ //<----------------PARA EL GRID
		$params = $_POST;
			if ($params['IDEmpresa']=='' || !isset($params['IDEmpresa'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar los movimientos de caja.");
		}
        // throw new Exception("ramon");
		$movimientoCajaModel=new MovimientoCaja();
		$response = $movimientoCajaModel->readAll($params);
      
        return $response; 
    }
	
	function obtenermovimientocaja(){
		$movimientoCajaModel=new MovimientoCaja();

		$id=$_POST['idMov'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
		
		
		if ($id_empresa==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear movimientos de caja';
			return $response;
		}
		
		if ($id_sucursal==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Sucursal para crear movimientos de caja';
			return $response;
		}
		
		IF($id==0){
			$data=array();
			$data['MovimientoCaja']=$movimientoCajaModel->getInitialInfo($id_empresa,$id_sucursal);
					
		}else{
			$data=$movimientoCajaModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function save(){
		$params = $_POST;
		
		$movimientoCajaModel=new MovimientoCaja();
		
		$resp = $movimientoCajaModel->guardar($params);
		
		if (!$resp)throw new Exception("Error al guardar el movimiento de caja");
		
		$response=array();
        $response['success']=true;
        $response['data']=$resp;
		
        return $response;
	}
	
	function obtenerformaspagos(){
		try {
		
			$filtro="";
		
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
	
	function obtenerdenominaciones(){
		try {
			
			$filtro="";
		
			
			$query = "SELECT COUNT(id_denominacion) AS totalrows FROM cat_denominaciones ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_denominacion,denominacion FROM cat_denominaciones ";
			$query.= " ORDER BY denominacion LIMIT $start, $limit ";
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

	function eliminar(){
		$movimientoCajaModel=new MovimientoCaja();
		$titulo=$movimientoCajaModel->name;
		
		if ( empty($_POST['id_movimiento_caja']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Movimiento de Caja que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_movimiento_caja'];	
	
		$movimientoCajaModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Movimiento de Caja no fue eliminado";
		}else{
			$success=false;
			$mensaje="Movimiento de Caja eliminado de la base de datos";
		}	
		$data=array('id_movimiento_caja'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Movimientos de Caja',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	function generarreportemovimientoscaja(){
		$params = $_POST;
		$reporte=new ReporteMovimientoCajaTicket();
		
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
	
	
	function getpdfmovimientocaja(){		
		if (!isset($_SESSION['repMov'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repMov']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repMov']['pdf'];
		
		$reporte=new ReporteMovimientoCajaTicket();
		$reporte->getPDF($pdfName);
	}
	
}
?>