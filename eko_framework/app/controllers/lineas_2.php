<?php
class Lineas extends ApplicationController {
	
	private $id_uni, $desc_uni, $abrev_uni,$user_uni;
	protected $camposAfiltrar = array('desc');
	/*var $components=array(
		'ACL'=>array(
			'allowedActions'=>array('getUnidadesMedida'))
	);*/
    public function getUnidadesMedida(){
        try {
        	$filtro = (isset($_POST['filtro'])) ? $this->filtroToSQL($_POST['filtro']) : ''; 
			//$filtro = $this->filtroToSQL($_POST['filtro']);
			if (isset($_POST['vertodos'])){
				if ($_POST['vertodos'] == 0){
					$filtro.= ($filtro) ? " AND ActivoUni = 1 " : " WHERE ActivoUni = 1 ";
				}
			}
            $query = "SELECT COUNT(IDUni) AS totalrows FROM cat_unidad_medida $filtro ";
            $res = mysqlQuery($query);
            if (!$res)
                throw new Exception(mysql_error()." ".$query);
			
            $resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
            $total_rows = $resultado['totalrows'];
			
            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
			
            $query = " SELECT IDUni, DescUni, AbrevUni, ActivoUni FROM cat_unidad_medida $filtro ";
			$query.= " ORDER BY IDUni LIMIT $start, $limit ";
            $res = mysqlQuery($query);
            if (!$res)  throw new Exception(mysql_error()." ".$query);
			
            $response = ResulsetToExt::resToArray($res);
            $response['totalRows'] = $total_rows;
        } catch (Exception $e) {
            $response['totalRows'] = $total_rows;
            $response['succes']    = false;
            $response['msg']       = $e->getMessage();
        }
        echo json_encode($response);
    }
	
	public function nuevo(){
		$datos 				= json_decode(stripslashes($_POST['data']));
		$this->id_uni   	= 0;
		$this->desc_uni 	= addslashes($datos->DescUni);
		$this->abrev_uni 	= addslashes($datos->AbrevUni);
		$this->user_uni 	= $_SESSION['Auth']['User']['IDUsu'];
		$this->guardar();
	}
	
	public function actualizar(){
		$datos = json_decode(stripslashes($_POST['data']));
		$this->id_uni   	= $datos->IDUni;
		$this->desc_uni 	= addslashes($datos->DescUni);
		$this->abrev_uni 	= addslashes($datos->AbrevUni);
		$this->user_uni 	= $_SESSION['Auth']['User']['IDUsu'];
		
		$this->guardar();
	}
	
	public function eliminar(){
        $response = array();
		$datos = json_decode(stripslashes($_POST['data']));
		
		try {
			$res = mysqlQuery("CALL unidadesMedidaEliminar(".$datos->IDUni.");");
			if (!$res)  throw new Exception(mysql_error());
			$row = mysql_fetch_object($res);
			$response['success'] = true;			
			$response['msg']     =array('titulo'=>'Unidades de Medida','mensaje'=>'El registro ha sido '.$row->accion) ;
        } catch(Exception $e) {
			$response['success'] = false;
			$response['msg']     = 'No pudo eliminarse el registro. '.$e->getMessage();
        }
        echo json_encode($response);
	}
	
	public function activar(){
		$response = array();
		$query = "UPDATE cat_unidad_medida SET ActivoUni = 1 WHERE IDUni = ".$_POST['campoId']." LIMIT 1";
		try {
			$res = mysqlQuery($query);
			if(!$res)  throw new Exception(mysql_error());
			$response['success'] = true;
			$response['msg']     = array('titulo'=>'Unidades de Medida','mensaje'=>'El registro ha sido activado.');
		} catch(Exception $e) {
			$response['success'] = false;
			$response['msg']     = 'No se pudo activar el registro.';
		}
		echo json_encode($response);
	}
	
	private function guardar(){ // Para Agregar o Actualizar
		$response = array();
		
		$query = "CALL unidadesMedidaGuardar(".$this->id_uni.", '".$this->desc_uni."','".$this->abrev_uni."', '".$this->user_uni."');";
		
        try {
			$res = mysqlQuery($query);
			if (!$res)throw new Exception(mysql_error());
			$row = mysql_fetch_array($res);
			
			$response['data']    = $row;  // Regresa el registro completo
            $response['success'] = true;
            $response['msg']     = array('titulo'=>'Unidades de Medida','mensaje'=>"Información Almacenada Satisfactoriamente");
        } catch(Exception $e) {
            $response['data']    = array();
            $response['success'] = false;
            $response['msg']     = $e->getMessage();
        }
        echo json_encode($response);
    }
	
}