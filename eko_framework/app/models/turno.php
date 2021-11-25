<?php

class Turno extends Model{
    var $useTable = 'turnos';
	var $detalleTable="turnos_detalles";
    var $name='Turno';
    var $primaryKey = 'id_turno';
    var $specific = true;
    var $camposAfiltrar = array('concepto');
		
    function readAll($params) {
        
		$limit = (empty($params['limit'])) ? 20 : $params['limit'];
		$start = (empty($params['start'])) ?  0 : $params['start'];
		$filtro = (empty($params['filtro'])) ?  '': $params['filtro']; 
		$filtroStatus = (empty($params['filtroStatus'])) ?  'A': $params['filtroStatus'];			
		$fechaInicio=(empty($params['fechaInicio'])) ?  '': $params['fechaInicio'];
		$fechaFin=(empty($params['fechaFin'])) ?  '': $params['fechaFin'];
		$fechaInicio.=" 00:00:00";
		$fechaFin.=" 23:59:59";
		$IDEmpresa = $params['IDEmpresa'];
		$IDSucursal = $params['IDSucursal'];
		
		$filtroSql = $this->filtroToSQL( $filtro ); 
		 
		if (strlen($filtroSql) > 0) {
            $filtroSql.=" AND t.id_empresa = $IDEmpresa AND t.id_sucursal = $IDSucursal ";
        } else {
           $filtroSql = "WHERE t.id_empresa = $IDEmpresa AND t.id_sucursal = $IDSucursal ";
        }
		
		if ($filtroStatus=='A')
			$filtroSql.=" AND t.status='A' ";
		if ($filtroStatus=='I')
            $filtroSql.=" AND t.status='I' ";
		
		$filtroSql.=    "AND (fechainicio BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable t
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT t.id_turno,t.concepto,
        			DATE_FORMAT(t.fechainicio,'%d/%m/%Y') as fechainicio,
					DATE_FORMAT(t.fechafin,'%d/%m/%Y') as fechafin,t.status,total_turno as total        		
        		 FROM $this->useTable t        			
				  $filtroSql ORDER BY t.fechainicio limit $start,$limit ;";

	    $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }
	
	 function getById($id){
    	 $query="SELECT t.id_turno,t.concepto,DATE_FORMAT(t.fechainicio,'%d/%m/%Y %H:%i:%S') as fechainicio,t.status
				FROM $this->useTable t
				WHERE t.id_turno=$id";       
        $turno=$this->query($query);
        
        if (sizeof($turno)==0){
        	throw new Exception("Error: No se encontró un turno con esos parámetros");
        }
			
		$query="SELECT d.id_turno_detalle,d.id_formapago,p.nombre_formapago,d.id_denominacion,u.denominacion,d.cantidad,d.total
				FROM $this->detalleTable d
				inner join cat_formaspagos p on p.id_formapago = d.id_formapago
				inner join cat_denominaciones u on u.id_denominacion = d.id_denominacion
				WHERE d.id_turno=$id ORDER BY d.id_turno_detalle";
		
		
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptos=$detalles;
		
		$datos=array();
		$datos['Turno']=$turno[0];   
		$datos['Detalles']=$detalles;
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){	
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fechainicio FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_turno'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		
        return $arrResult[0];		
	}
	
	public function guardar($params){
		
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$Turno = $params['Turno'];
				
		$Detalles=json_decode( stripslashes($params['Detalles']), true);
		
		//$numConceptos=sizeof($Detalles);
		// Throw new Exception($numConceptos);
		// if($numConceptos == 0)
			// Throw new Exception("No se recibieron detalles.");
			
		
		$id_empresa = $Turno['id_empresa'];		
		$id_sucursal = $Turno['id_sucursal'];
		
		
		
		$id_turno = $Turno['id_turno'];
		$fecha = $Turno['fecha'];
		$hora = $Turno['hora'];
		$concepto = $Turno['concepto'];
		$total = $Turno['total'];
		$status = $Turno['status'];
	
		$datetime="$fecha $hora";
		
		$fecha_turno=date('Y-m-d H:i:s',strtotime($datetime));
		
        if($id_turno > 0){
			$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_turno;
        }else{  //INSERT
			$turno = $this->getTurno($id_empresa,$id_sucursal);
		
			if($turno){
				throw new Exception('Existe un turno abierto, favor de realizar el corte.');
			}
		
			$sql="SELECT IFNULL(max(consecutivo),0) + 1 as consecutivo FROM $this->useTable
                WHERE id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' and status = 'A'";
			$consecutivo = 1;
			$turno=$this->query($sql);
			if($turno)
				$consecutivo = $turno[0]['consecutivo'];
			
			
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
		
		$query.=",fechainicio='".$fecha_turno."'";
		$query.=",concepto='".$this->EscComillas($concepto)."'";
		$query.=",total_turno='".$this->EscComillas($total)."'";
		$query.=",status='".$this->EscComillas($status)."'";
	

        $query=$query.$where;
		// Throw new Exception($query); 
		if ($registroNuevo){
			if (is_numeric($consecutivo)){	
			$query.=",consecutivo='".$consecutivo."'";
			}	
			
			$id= $this->insert($query); 			
			
		}else{		
			$result=$this->update($query);               
			$id=$id_turno;
		}
		
		$this->guardarDetalles($id,$Detalles,$registroNuevo);
		
        $data=$this->getById($id);   
        
		return $data;
                     

    }
	
	private function guardarDetalles($id,$conceptos,$registroNuevo){
			
		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_turno = $id ";
		$this->queryDelete($sqlDelete);
		
		foreach ($conceptos  as $concepto) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$id_formapago = $concepto['id_formapago'];
			$id_denominacion = $concepto['id_denominacion'];
			$cantidad = $concepto['cantidad'];
			$total = $concepto['total'];
			try{
				$queryInsert="INSERT INTO $this->detalleTable SET id_turno=$id,id_formapago='$id_formapago',id_denominacion='$id_denominacion',
				cantidad='$cantidad', total='$total';";
				// throw new Exception($queryInsert);	
				$IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 
				// throw new Exception($queryInsert);	
				            
			}catch(Exception $e){            
				return false;
			}  
			
			
		}
	}
	
	public function getTurno($id_empresa,$id_sucursal){
		$date = new DateTime();
		$fecha= $date->format('Y-m-d');		
		$fechaInicio=$fecha;
		$fechaFin=$fecha;
		$fechaInicio.=" 00:00:00"; 
		$fechaFin.=" 23:59:59";
		
		$query="SELECT id_turno,concepto as turno,total_turno FROM $this->useTable
                WHERE id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' and fechafin IS NULL and status = 'A'";
	
       $arrayResult=$this->query($query);
       // Throw new Exception($query);
        if (sizeof($arrayResult)>0){
        	return $arrayResult[0];	
        }else{
        	return array();
        }
		
		// $datos=array();
		// $datos['Turno']=$arrayResult[0];   
		
		// return $datos;
		
		
		
		// public function getEmpresa($IDemp){
        // $query="SELECT id_empresa,nombre_fiscal ";
        // $query.=" FROM cat_empresas";
        // $query.=" WHERE id_empresa=$IDemp;";

        
        
    // }
		
	}
	
	private function cerrarTurno($id_turno,$fecha){
		$date = new DateTime();
		$fecha= $date->format('Y-m-d');		
		$fechaInicio=$fecha;
		$fechaFin=$fecha;
		$fechaInicio.=" 00:00:00"; 
		$fechaFin.=" 23:59:59";
		
		$query="SELECT id_turno,concepto as turno,total_turno FROM $this->useTable
                WHERE id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' and fechafin IS NULL and status = 'A'";
	
       $arrayResult=$this->query($query);
       // Throw new Exception($query);
        if (sizeof($arrayResult)>0){
        	return $arrayResult[0];	
        }else{
        	return array();
        }
	
	}

	public function delete($id){
		
		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_turno = $id ";
		$this->queryDelete($sqlDelete);	
		
		return parent::delete($id);
    }	
}
?>