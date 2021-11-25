<?php

class CitaModel extends Model{
    var $useTable = 'citas';
	var $name='Cita';
    var $primaryKey = 'id_cita';
	var $specific = true;
    var $camposAfiltrar = array('observaciones','nombre_agente','nombre_fiscal');
		
    function readAll($params) {
        
		$limit = (empty($params['limit'])) ? 20 : $params['limit'];
		$start = (empty($params['start'])) ?  0 : $params['start'];
		$filtro = (empty($params['filtro'])) ?  '': $params['filtro']; 
		$filtroStatus = (empty($params['filtroStatus'])) ?  'A': $params['filtroStatus'];			
		$fechaInicio=(empty($params['fechaInicio'])) ?  '': $params['fechaInicio'];
		//$fechaInicio.=" 00:00:00";
		$fechaFin=(empty($params['fechaFin'])) ?  '': $params['fechaFin'];
		//$fechaFin.=" 23:59:59";
		$IDEmpresa = $params['IDEmpresa'];
		$IDSucursal = $params['IDSucursal'];
		
		$filtroSql = $this->filtroToSQL( $filtro ); 
		 
		if (strlen($filtroSql) > 0) {
            $filtroSql.=" AND a.id_empresa = $IDEmpresa AND a.id_sucursal = $IDSucursal ";
        } else {
           $filtroSql = "WHERE a.id_empresa = $IDEmpresa AND a.id_sucursal = $IDSucursal ";
        }
		
		if ($filtroStatus=='A')
			$filtroSql.=" AND a.status='A' ";
		if ($filtroStatus=='I')
            $filtroSql.=" AND a.status='I' ";
		
		$filtroSql.=    "AND (a.fecha BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable a
					inner join cat_clientes ct on ct.id_cliente = a.id_cliente
					inner join cat_agentes ag on ag.id_agente = a.id_agente
					inner join cat_horarios h on h.id_horario = a.id_horario
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT a.id_cita,
        			DATE_FORMAT(a.fecha,'%d/%m/%Y') as fecha,
					concat(DATE_FORMAT(h.hora_inicio,'%H:%i:%S'),'-',DATE_FORMAT(h.hora_fin,'%H:%i:%S')) as horario,
					ct.nombre_fiscal,
					ag.nombre_agente,a.observaciones
        		 FROM $this->useTable a
        			inner join cat_clientes ct on ct.id_cliente = a.id_cliente
					inner join cat_agentes ag on ag.id_agente = a.id_agente
					inner join cat_horarios h on h.id_horario = a.id_horario
				  $filtroSql ORDER BY a.fecha,h.hora_inicio,h.hora_fin limit $start,$limit ;";

		// throw new Exception($query);		  
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }
    
	public function guardar($params){
		try{
					
			$registroNuevo=false;
			$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
					
			$Cita = $params['Cita'];
					
			$id_empresa = $Cita['id_empresa'];		
			$id_sucursal = $Cita['id_sucursal'];			
					 
			$id_cita = $Cita['id_cita'];
			$fecha = $Cita['fecha'];
			$id_cliente = $Cita['id_cliente'];
			$id_agente = $Cita['id_agente'];
			$id_horario = $Cita['id_horario'];			
			$observaciones = $Cita['observaciones'];					
			$status = $Cita['status'];
			
			if($id_cita > 0){
				$query="UPDATE $this->useTable SET ";
				
				$query.="usermodif=$IDUsu";    //LOG
				$query.=",fechamodif=now()";
				$where=" WHERE $this->primaryKey = ".$id_cita;
			}else{  //INSERT
				$query="INSERT INTO $this->useTable SET ";
				$query.="usercreador=$IDUsu";    //LOG
				$query.=",fechacreador=now()";
			  
				$registroNuevo=true;
				$where='';
			}
			
			if (is_numeric($id_empresa)){	
				$query.=",id_empresa='".$id_empresa."'";
			}
			
			if (is_numeric($id_sucursal)){	
				$query.=",id_sucursal='".$id_sucursal."'";
			}
			
			if (is_numeric($id_cliente)){	
				$query.=",id_cliente='".$id_cliente."'";
			}
			
			if (is_numeric($id_agente)){	
				$query.=",id_agente='".$id_agente."'";
			}
			
			if (is_numeric($id_horario)){	
				$query.=",id_horario='".$id_horario."'";
			}
			
			$query.=",fecha='".$fecha."'";
			
			$query.=",observaciones='".$this->EscComillas($observaciones)."'";		
			
			$query.=",status='".$this->EscComillas($status)."'";
			
			$query=$query.$where;
			 
			if ($registroNuevo){				
				$id= $this->insert($query); 					
			}else{				
				$result=$this->update($query);               
				$id=$id_cita;
			}		
			
			$data=$this->getById($id);   			
			
			$response['success']    = true;
			$response['msg']       = array('titulo'=>"Citas",'mensaje'=>"Cita guardada correctamente");
			$response['data']    = $data;
		}catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
		return $response;
                     

    }
	
	public function delete($id){			
		$sqlDelete="DELETE FROM  $this->useTable WHERE id_cita = $id ";
		$this->queryDelete($sqlDelete);			
		
        return parent::delete($id);
    }

	 function getById($id){
    	 $query="SELECT a.id_cita,DATE_FORMAT(a.fecha,'%d/%m/%Y %H:%i:%S') as fecha,
				cl.id_cliente,cl.nombre_fiscal,a.status,a.id_horario,a.id_agente,ag.nombre_agente,
				concat(DATE_FORMAT(h.hora_inicio,'%H:%i:%S'),'-',DATE_FORMAT(h.hora_fin,'%H:%i:%S')) as descripcion_horario,
				a.observaciones
				FROM $this->useTable a
				inner join cat_clientes cl on cl.id_cliente = a.id_cliente
				inner join cat_agentes ag on ag.id_agente = a.id_agente
				inner join cat_horarios h on h.id_horario = a.id_horario
				WHERE a.id_cita=$id";       
        $cita=$this->query($query);
        
        if (sizeof($cita)==0){
        	throw new Exception("Error: No se encontró una cita con esos parámetros");
        }
			
		$datos=array();
		$datos['Cita']=$cita[0];   
		
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){	
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_cita'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		$arrResult[0]['id_almacen'] = $id_almacen;		
		
        return $arrResult[0];		
	}
	
		
}
?>
