<?php
require ('eko_framework/app/models/checada.php');       //MODELO
require_once "eko_framework/app/models/reporte_checadas.php";
class Checadas extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 

   	function guardar(){
		$params = $_POST;
		
		$Model=new ChecadaModel();
		$resp = $Model->guardar($params);
		
		if (!$resp)throw new Exception("Error al guardar la checada");
		
		$response=array();
        $response['success']=true;
		$response['msg'] = array('titulo'=>'Checadas','mensaje'=> 'La checada se ha guardado') ;            
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
	
	function obtenerempleados(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_empleado'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A' " : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_empleado) AS totalrows FROM cat_empleados $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_empleado,nombre_empleado FROM cat_empleados $filtro ";
			$query.= " ORDER BY nombre_empleado LIMIT $start, $limit ";
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
	
	function generarreportechecadas(){
		$params = $_POST;
		$reporte=new ReporteChecadas();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repChec']['rand']=$numero_aleatorio ;
		$_SESSION['repChec']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfchecadas(){		
		if (!isset($_SESSION['repChec'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repChec']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repChec']['pdf'];
		
		$reporte=new ReporteChecadas();
		$reporte->getPDF($pdfName);
	}
	
}
?>
