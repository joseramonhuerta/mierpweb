<?php
	require ('eko_framework/app/models/turno.php');
class Corte extends Model{
    var $useTable = 'cortes';
	var $liquidacionesTable="cortes_liquidaciones";
	var $retencionesTable="cortes_retenciones";
    var $name='Corte';
    var $primaryKey = 'id_corte';
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
		
		$filtroSql.=    "AND (fecha_corte BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable t
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT t.id_corte,t.concepto,
        			DATE_FORMAT(t.fecha_corte,'%d/%m/%Y') as fecha_corte,total_corte as total,
					t.status        		
        		 FROM $this->useTable t        			
				  $filtroSql ORDER BY t.fecha_corte limit $start,$limit ;";
		 // throw new Exception($query);
	    $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }
	
	 function getById($id){
    	 $query="SELECT t.id_corte,t.concepto,DATE_FORMAT(t.fecha_corte,'%d/%m/%Y %H:%i:%S') as fecha_corte,t.status
				FROM $this->useTable t
				WHERE t.id_corte=$id";       
        $corte=$this->query($query);
        
        if (sizeof($corte)==0){
        	throw new Exception("Error: No se encontró un corte con esos parámetros");
        }
			
		$query="SELECT d.id_corte_liquidacion,d.id_formapago,p.nombre_formapago,d.id_denominacion,u.denominacion,d.cantidad,d.total
				FROM $this->liquidacionesTable d
				inner join cat_formaspagos p on p.id_formapago = d.id_formapago
				inner join cat_denominaciones u on u.id_denominacion = d.id_denominacion
				WHERE d.id_corte=$id ORDER BY d.id_corte_liquidacion";
		
		
		$detallesLiquidacion=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptosliquidacion=$detallesLiquidacion;
		
		$query="SELECT d.id_corte_retencion,d.id_formapago,p.nombre_formapago,d.id_denominacion,u.denominacion,d.cantidad,d.total
				FROM $this->retencionesTable d
				inner join cat_formaspagos p on p.id_formapago = d.id_formapago
				inner join cat_denominaciones u on u.id_denominacion = d.id_denominacion
				WHERE d.id_corte=$id ORDER BY d.id_corte_retencion";
		
		
		$detallesRetencion=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptosretencion=$detallesRetencion;
		
		$datos=array();
		$datos['Corte']=$corte[0];   
		$datos['DetallesLiquidacion']=$detallesLiquidacion;
		$datos['DetallesRetencion']=$detallesRetencion;
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){	
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha_corte FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_corte'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		
        return $arrResult[0];		
	}
	
	public function guardar($params){
		
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$Corte = $params['Corte'];
				
		$DetallesLiquidacion=json_decode( stripslashes($params['DetallesLiquidacion']), true);
		$DetallesRetencion=json_decode( stripslashes($params['DetallesRetencion']), true);
				
		
		$id_empresa = $Corte['id_empresa'];		
		$id_sucursal = $Corte['id_sucursal'];
		
		
	
				 
		$id_corte = $Corte['id_corte'];
		
		$fecha = $Corte['fecha'];
		$hora = $Corte['hora'];
		$concepto = $Corte['concepto'];
		$total_liquidado = $Corte['total_liquidado'];
		$total_retenido = $Corte['total_retenido'];
		$total_corte = $Corte['total_corte'];
		$status = $Corte['status'];
		$diferencia_corte = 0;
		$datetime="$fecha $hora";
		// throw new Exception($status);
		$fecha_corte=date('Y-m-d H:i:s',strtotime($datetime));
		
        if($id_corte > 0){
			$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_corte;
        }else{  //INSERT
		
			$sql="SELECT IFNULL(max(consecutivo),0) + 1 as consecutivo FROM $this->useTable
                WHERE id_empresa='$id_empresa' AND id_sucursal='$id_sucursal' and status = 'A'";
			$consecutivo = 1;
			$corte=$this->query($sql);
			if($corte)
				$consecutivo = $corte[0]['consecutivo'];
			
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
		
		
		
		$query.=",fecha_corte='".$fecha_corte."'";
		$query.=",concepto='".$this->EscComillas($concepto)."'";
		$query.=",total_liquidado='".$this->EscComillas($total_liquidado)."'";
		$query.=",total_retenido='".$this->EscComillas($total_retenido)."'";
		$query.=",total_corte='".$this->EscComillas($total_corte)."'";
		
		$query.=",status='".$this->EscComillas($status)."'";
		
		if($id_corte > 0){
			$turno = $this->getTurnoCorte($id_corte);
			
			if(!$turno){
				throw new Exception('No existe un turno para el corte');
			}
			
			$id_turno = $turno['id_turno'];	
			$total_turno = $turno['total_turno'];	
						
			if (is_numeric($id_turno)){	
				$query.=",id_turno='".$id_turno."'";
			}
			
			$queryVentas = "select IFNULL(SUM(total),0) as totalventas  FROM ventas where status = 'A' and id_turno = $id_turno";
			$res = mysqlQuery($queryVentas);
			$resultado = mysql_fetch_array($res, MYSQL_ASSOC);
			$totalVentas = $resultado['totalventas'];
			
			$queryDepositos = "select IFNULL(SUM(total),0) as totaldepositos  FROM movimientos_caja where id_turno = $id_turno and Tipo = 1";
			$res = mysqlQuery($queryDepositos);
			$resultado = mysql_fetch_array($res, MYSQL_ASSOC);
			$totalDepositos = $resultado['totaldepositos'];
			
			$queryRetiros = "select IFNULL(SUM(total),0) as totalretiros FROM movimientos_caja where id_turno = $id_turno and Tipo = 2";
			$res = mysqlQuery($queryRetiros);
			$resultado = mysql_fetch_array($res, MYSQL_ASSOC);
			$totalRetiros = $resultado['totalretiros'];
			
			$diferencia_corte = ($totalRetiros + $total_liquidado + $total_retenido) - ($total_turno + $totalVentas + $totalDepositos);		
			
			$query.=",total_ventas='".$this->EscComillas($totalVentas)."'";
			$query.=",total_depositos='".$this->EscComillas($totalDepositos)."'";
			$query.=",total_retiros='".$this->EscComillas($totalRetiros)."'";
			$query.=",diferencia_corte='".$this->EscComillas($diferencia_corte)."'";
			
			
		}else{
			$turnoModel = new Turno();
			$turno = $turnoModel->getTurno($id_empresa,$id_sucursal);
			
			if(!$turno){
				throw new Exception('No existe turno abierto');
			}
			
			$id_turno = $turno['id_turno'];	
			$total_turno = $turno['total_turno'];
			
			if (is_numeric($id_turno)){	
				$query.=",id_turno='".$id_turno."'";
			}
			$query.=",total_turno='".$this->EscComillas($total_turno)."'";
			
			$queryVentas = "select IFNULL(SUM(total),0) as totalventas  FROM ventas where status = 'A' and id_turno = $id_turno";
			$res = mysqlQuery($queryVentas);
			$resultado = mysql_fetch_array($res, MYSQL_ASSOC);
			$totalVentas = $resultado['totalventas'];
			
			$queryDepositos = "select IFNULL(SUM(total),0) as totaldepositos  FROM movimientos_caja where id_turno = $id_turno and Tipo = 1";
			$res = mysqlQuery($queryDepositos);
			$resultado = mysql_fetch_array($res, MYSQL_ASSOC);
			$totalDepositos = $resultado['totaldepositos'];
			
			$queryRetiros = "select IFNULL(SUM(total),0) as totalretiros FROM movimientos_caja where id_turno = $id_turno and Tipo = 2";
			$res = mysqlQuery($queryRetiros);
			$resultado = mysql_fetch_array($res, MYSQL_ASSOC);
			$totalRetiros = $resultado['totalretiros'];
			
			$diferencia_corte = ($totalRetiros + $total_liquidado + $total_retenido) - ($total_turno + $totalVentas + $totalDepositos);		
			
			$query.=",total_ventas='".$this->EscComillas($totalVentas)."'";
			$query.=",total_depositos='".$this->EscComillas($totalDepositos)."'";
			$query.=",total_retiros='".$this->EscComillas($totalRetiros)."'";
			$query.=",diferencia_corte='".$this->EscComillas($diferencia_corte)."'";
						
		}
		
		
		

        $query=$query.$where;
		// throw new Exception($query);
		// Throw new Exception($query); 
		if ($registroNuevo){ 

			if (is_numeric($consecutivo)){	
			$query.=",consecutivo='".$consecutivo."'";
			}
				

			
			$id= $this->insert($query); 			
			
			$this->CerrarTurno($id_turno,$id,$IDUsu,$fecha_corte);
		}else{		
			$result=$this->update($query);               
			$id=$id_corte;
		}
		/*$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";$
            $where=" WHERE $this->primaryKey = ".$id_turno;*/
		$this->guardarDetallesLiquidacion($id,$DetallesLiquidacion,$registroNuevo);
		$this->guardarDetallesRetencion($id,$DetallesRetencion,$registroNuevo);
		
        $data=$this->getById($id);   
        
		return $data;
                     

    }
	
	private function guardarDetallesLiquidacion($id,$conceptos,$registroNuevo){
			
		$sqlDelete="DELETE FROM  $this->liquidacionesTable WHERE id_corte = $id ";
		$this->queryDelete($sqlDelete);
		
		foreach ($conceptos  as $concepto) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$id_formapago = $concepto['id_formapago'];
			$id_denominacion = $concepto['id_denominacion'];
			$cantidad = $concepto['cantidad'];
			$total = $concepto['total'];
			try{
				$queryInsert="INSERT INTO $this->liquidacionesTable SET id_corte=$id,id_formapago='$id_formapago',id_denominacion='$id_denominacion',
				cantidad='$cantidad', total='$total';";
				// throw new Exception($queryInsert);	
				$IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 
				// throw new Exception($queryInsert);	
				            
			}catch(Exception $e){            
				return false;
			}  
			
			
		}
	}
	
	private function guardarDetallesRetencion($id,$conceptos,$registroNuevo){
			
		$sqlDelete="DELETE FROM  $this->retencionesTable WHERE id_corte = $id ";
		$this->queryDelete($sqlDelete);
		
		foreach ($conceptos  as $concepto) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$id_formapago = $concepto['id_formapago'];
			$id_denominacion = $concepto['id_denominacion'];
			$cantidad = $concepto['cantidad'];
			$total = $concepto['total'];
			try{
				$queryInsert="INSERT INTO $this->retencionesTable SET id_corte=$id,id_formapago='$id_formapago',id_denominacion='$id_denominacion',
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
		
		$query="SELECT id_turno,concepto as turno FROM $this->useTable
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

	public function delete($id){
		$query="update turnos Set id_corte = NULL,fechafin=NULL,usercierre = NULL,fechacierre=NULL
                WHERE id_corte = $id";	
		$this->query($query);
	   
		$sqlDelete="DELETE FROM  $this->liquidacionesTable WHERE id_corte = $id ";
		$this->queryDelete($sqlDelete);	
		
		$sqlDelete="DELETE FROM  $this->retencionesTable WHERE id_corte = $id ";
		$this->queryDelete($sqlDelete);	
		
		return parent::delete($id);
    }	

	public function getTurnoCorte($id_corte){
		$query="SELECT id_turno,total_turno FROM $this->useTable
                WHERE id_corte = $id_corte";
	
       $arrayResult=$this->query($query);
       if (sizeof($arrayResult)>0){
        	return $arrayResult[0];	
       }else{
        	return array();
       }
		
		
		
	}	
	
	public function CerrarTurno($id_turno,$id_corte,$IDUsu,$fecha_corte){
		$query="update turnos Set id_corte = $id_corte,fechafin='$fecha_corte',usercierre = $IDUsu,fechacierre='$fecha_corte'
                WHERE id_turno = $id_turno";
	
       $this->query($query);
   	}
}
?>