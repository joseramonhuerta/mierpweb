<?php
require ('eko_framework/app/models/turno.php');       //MODELO
// require_once "eko_framework/app/models/reporte_turno_ticket_pdf.php";
require_once "eko_framework/app/models/reporte_turno_ticket.php";

class Turnos extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

    function obtenerturnos(){ //<----------------PARA EL GRID
		$params = $_POST;
			if ($params['IDEmpresa']=='' || !isset($params['IDEmpresa'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar los turnos.");
		}
        
		$turnoModel=new Turno();
		$response = $turnoModel->readAll($params);
      
        return $response; 
    }
	
	function obtenerturno(){
		$turnoModel=new Turno();

		$id=$_POST['idTur'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
		
		
		if ($id_empresa==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear turnos';
			return $response;
		}
		
		if ($id_sucursal==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Sucursal para crear turnos';
			return $response;
		}
		
		IF($id==0){
			$data=array();
			$data['Turno']=$turnoModel->getInitialInfo($id_empresa,$id_sucursal);
					
		}else{
			$data=$turnoModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function save(){
		$params = $_POST;
		
		$turnoModel=new Turno();
		
		$resp = $turnoModel->guardar($params);
		
		if (!$resp)throw new Exception("Error al guardar el turno");
		
		$response=array();
        $response['success']=true;
        $response['data']=$resp;
		
        return $response;
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
	
	function obtenerdenominaciones(){
		try {
			//$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			//$filtro=$this->filtroToSQL($filtro_query,array('nombre_linea'));
			// throw new Exception($filtro);		
			// $filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			
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
		$turnoModel=new Turno();
		$titulo=$turnoModel->name;
		
		if ( empty($_POST['id_turno']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Turno que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_turno'];	
	
		$turnoModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Turno no fue eliminado";
		}else{
			$success=false;
			$mensaje="Turno eliminado de la base de datos";
		}	
		$data=array('id_turno'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Turnos',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	function generarreporteturno(){
		$params = $_POST;
		$reporte=new ReporteTurnoTicket();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repTur']['rand']=$numero_aleatorio ;
		$_SESSION['repTur']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
	}
	
	function getpdfturno(){		
		if (!isset($_SESSION['repTur'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repTur']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repTur']['pdf'];
		
		$reporte=new ReporteTurnoTicket();
		$reporte->getPDF($pdfName);
	}
	
}
?>