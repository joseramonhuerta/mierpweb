<?php
require ('eko_framework/app/models/abono.php');       //MODELO
require ('eko_framework/app/models/serie.php');
require_once "eko_framework/app/models/reporte_abono_ticket.php";
require_once "eko_framework/app/models/reporte_abonos_clientes.php";
//require ('eko_framework/app/models/linea.php');
class Abonos extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

    function obtenerabonos(){ //<----------------PARA EL GRID
		$params = $_POST;
			if ($params['IDEmpresa']=='' || !isset($params['IDEmpresa'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar Remisiones.");
		}
        
		$abonoModel=new AbonoModel();
		$response = $abonoModel->readAll($params);
      
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
			$filtro.= ($filtro) ? " AND id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie in (9)" : " WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A' AND tipo_serie in (9)";
			
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
			// throw new Exception($query);
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
	
	function obtenerabono(){
		$abonoModel=new AbonoModel();

		$id=$_POST['idAbo'];
		$id_empresa=$_POST['id_empresa'];
		$id_sucursal=$_POST['id_sucursal'];
		$id_almacen=$_POST['id_almacen'];
		
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
		
		if ($id_almacen==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en un Almacen para crear abonos';
			return $response;
		}
		
		IF($id==0){
			$data=array();
			$data['Abono']=$abonoModel->getInitialInfo($id_empresa,$id_sucursal,$id_almacen);
					
		}else{
			$data=$abonoModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}
	
	function save(){
		$params = $_POST;
		
		$abonoModel=new AbonoModel();
		
		$resp = $abonoModel->guardar($params);		
		
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
		$abonoModel=new AbonoModel();
		$titulo=$abonoModel->name;
		
		if ( empty($_POST['id_cxc_abono']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Abono que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_cxc_abono'];	
	
		$abonoModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Abono no fue eliminado";
		}else{
			$success=true;
			$mensaje="Abono eliminado de la base de datos";
		}	
		$data=array('id_cxc_abono'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Abonos',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}

	function generarreporteabono(){
		$params = $_POST;
		$reporte=new ReporteAbonoTicket();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repAbo']['rand']=$numero_aleatorio ;
		$_SESSION['repAbo']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
	}
	
	function getpdfabono(){		
		if (!isset($_SESSION['repAbo'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repAbo']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repAbo']['pdf'];
		
		$reporte=new ReporteAbonoTicket();
		$reporte->getPDF($pdfName);
	}

	function obtenerremisiones(){
		try {
			
			$filtro="";
			$id_empresa = $_POST['id_empresa'];
			$id_sucursal = $_POST['id_sucursal'];
			$id_cliente = $_POST['id_cliente'];
			$filtro.= ($filtro) ? " AND r.status = 'A'" : " WHERE r.status = 'A'";
			$filtro.= " AND r.id_empresa = $id_empresa AND r.id_sucursal = $id_sucursal AND r.id_cliente = $id_cliente and c.saldo > 0
						AND r.condicion_pago = 2 AND r.aplicado = 1";
			
			$query = "SELECT COUNT(r.id_remision) AS totalrows FROM remisiones r 
						INNER JOIN cxc c on c.id_remision = r.id_remision
						$filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 50 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT r.id_remision, concat(r.serie,' - ',r.folio,' (',r.concepto,')') as descripcion, c.total, c.abonos, c.saldo,c.id_cxc FROM remisiones r ";
			$query.= " INNER JOIN cxc c on c.id_remision = r.id_remision";
			$query.= "$filtro ORDER BY r.serie,r.folio LIMIT $start, $limit ";
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

	function generarreporteabonos(){
		$params = $_POST;
		$reporte=new ReporteAbonosClientes();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repAbo']['rand']=$numero_aleatorio ;
		$_SESSION['repAbo']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;		
	}
	
	function getpdfreporteabonos(){		
		if (!isset($_SESSION['repAbo'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repAbo']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repAbo']['pdf'];
		
		$reporte=new ReporteAbonosClientes();
		$reporte->getPDF($pdfName);
	}
	
}
?>
