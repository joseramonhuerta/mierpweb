<?php

class AbonoModel extends Model{
    var $useTable = 'cxc_abonos';
	var $name='Abono';
    var $primaryKey = 'id_cxc_abono';
	var $specific = true;
    var $camposAfiltrar = array('a.concepto','nombre_fiscal','observacion');
		
    function readAll($params) {
        
		$limit = (empty($params['limit'])) ? 20 : $params['limit'];
		$start = (empty($params['start'])) ?  0 : $params['start'];
		$filtro = (empty($params['filtro'])) ?  '': $params['filtro']; 
		$filtroStatus = (empty($params['filtroStatus'])) ?  'A': $params['filtroStatus'];			
		$fechaInicio=(empty($params['fechaInicio'])) ?  '': $params['fechaInicio'];
		$fechaInicio.=" 00:00:00";
		$fechaFin=(empty($params['fechaFin'])) ?  '': $params['fechaFin'];
		$fechaFin.=" 23:59:59";
		$IDEmpresa = $params['IDEmpresa'];
		$IDSucursal = $params['IDSucursal'];
		
		$filtroSql = $this->filtroToSQL( $filtro ); 
		 
		if (strlen($filtroSql) > 0) {
            $filtroSql.=" AND r.id_empresa = $IDEmpresa AND r.id_sucursal = $IDSucursal ";
        } else {
           $filtroSql = "WHERE r.id_empresa = $IDEmpresa AND r.id_sucursal = $IDSucursal ";
        }
		
		if ($filtroStatus=='A')
			$filtroSql.=" AND a.status='A' ";
		if ($filtroStatus=='I')
            $filtroSql.=" AND a.status='I' ";
		
		$filtroSql.=    "AND (a.fecha BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable a
					inner join cxc c on c.id_cxc = a.id_cxc
					inner join remisiones r on c.id_remision = r.id_remision
					inner join cat_clientes ct on ct.id_cliente = c.id_cliente
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT a.id_cxc_abono,a.concepto,concat(a.serie,' - ',a.folio) as serie_folio,
        			DATE_FORMAT(a.fecha,'%d/%m/%Y') as fecha,
					ct.nombre_fiscal,
					a.importe as total,a.status,a.observacion
        		 FROM $this->useTable a
        			inner join cxc c on c.id_cxc = a.id_cxc
					inner join remisiones r on c.id_remision = r.id_remision
					inner join cat_clientes ct on ct.id_cliente = c.id_cliente
				  $filtroSql ORDER BY a.fecha limit $start,$limit ;";

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
			// Throw new Exception("Ramon");		
			$registroNuevo=false;
			$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
					
			$Abono = $params['Abono'];
					
			// $id_empresa = $Abono['id_empresa'];		
			// $id_sucursal = $Abono['id_sucursal'];
			// $id_almacen = $Abono['id_almacen'];
					 
			$id_cxc_abono = $Abono['id_cxc_abono'];
			$id_cxc = $Abono['id_cxc'];
			$id_serie = $Abono['id_serie'];
			$nombre_serie = $Abono['nombre_serie'];
			$folio = $Abono['folio'];
			$observacion = $Abono['observacion'];
			$fecha = $Abono['fecha'];
			$hora = $Abono['hora'];
					
			$sql="SELECT c.abonos, c.saldo,r.serie,r.folio FROM cxc c
					inner join remisiones r on r.id_remision = c.id_remision
					WHERE c.id_cxc = $id_cxc";
			$cxc=$this->select($sql);					
						
			$abonos = $cxc[0]['abonos'];
			$saldo = $cxc[0]['saldo'];
			$nombre_serie_cxc = $cxc[0]['serie'];
			$folio_cxc = $cxc[0]['folio'];	
			
			$concepto = 'ABONO CXC '.$nombre_serie_cxc.' - '.$folio_cxc;
			// $id_cliente = $Abono['id_cliente'];
			// $id_remision = $Abono['id_remision'];
			
			$importe = $Abono['importe'];
			$status = $Abono['status'];	
		
			$datetime="$fecha $hora";
			
			$fecha=date('Y-m-d H:i:s',strtotime($datetime));
			
			if($id_cxc_abono > 0){
				$query="UPDATE $this->useTable SET ";
				
				$query.="usermodif=$IDUsu";    //LOG
				$query.=",fechamodif=now()";
				$where=" WHERE $this->primaryKey = ".$id_cxc_abono;
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
			
			if (is_numeric($id_serie)){	
				$query.=",id_serie='".$id_serie."'";
			}
			
			if (is_numeric($id_almacen)){	
				$query.=",id_almacen='".$id_almacen."'";
			}
			
			if (is_numeric($id_cliente)){	
				$query.=",id_cliente='".$id_cliente."'";
			}
			
			if (is_numeric($id_cxc)){	
				$query.=",id_cxc='".$id_cxc."'";
			}
			
			if (is_numeric($folio)){	
				$query.=",folio='".$folio."'";
			}
			
			$query.=",serie='".$this->EscComillas($nombre_serie)."'";
			$query.=",fecha='".$fecha."'";
			$query.=",concepto='".$this->EscComillas($concepto)."'";
			$query.=",observacion='".$this->EscComillas($observacion)."'";
			
			$query.=",importe='".$this->EscComillas($importe)."'";
			
			$query.=",status='".$this->EscComillas($status)."'";
		

			$query=$query.$where;
			 
			if ($registroNuevo){
				
				if($importe > $saldo)
				throw new Exception("El importe del abono no puede ser mayor al saldo");
			
				$id= $this->insert($query); 
				$this->registrarPago($id_cxc, $id_cxc_abono, $importe, $registroNuevo);		
				
			}else{
				
				$sql="SELECT a.importe FROM cxc_abonos a
						WHERE a.id_cxc_abono = $id_cxc_abono";
						
				$abono=$this->select($sql);			
							
				$impote_ant = $abono[0]['importe'];
			
				if($importe > ($saldo + $impote_ant))
				throw new Exception("El importe del abono no puede ser mayor al saldo");
			
				$this->registrarPago($id_cxc, $id_cxc_abono, $importe, $registroNuevo);
				$result=$this->update($query);               
				$id=$id_cxc_abono;
			}		
			
			$data=$this->getById($id);   
			
			if ($registroNuevo){
				$this->gastarFolio( $id_serie, $folio);
			}
		// throw new Exception($data['id_movimiento']);
			$response['success']    = true;
			$response['msg']       = array('titulo'=>"Abonos",'mensaje'=>"Abono guardado correctamente");
			$response['data']    = $data;
		}catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
		return $response;
                     

    }
	
	public function delete($id){
		
		$sql="SELECT a.id_cxc, a.importe FROM $this->useTable a
					WHERE a.id_cxc_abono = $id";
					
		$abono=$this->select($sql);			
					
		$id_cxc = $abono[0]['id_cxc'];
		$impote = $abono[0]['importe'];	

		$sql="UPDATE cxc set abonos=abonos - $impote, saldo = saldo + $impote WHERE id_cxc=$id_cxc";		
			$this->update($sql);	
					
			
		$sqlDelete="DELETE FROM  $this->useTable WHERE id_cxc_abono = $id ";
		$this->queryDelete($sqlDelete);			
		
        return parent::delete($id);
    }

	 function getById($id){
    	 $query="SELECT a.id_cxc_abono,a.id_cxc,a.concepto,c.id_remision,DATE_FORMAT(a.fecha,'%d/%m/%Y %H:%i:%S') as fecha,a.folio,
				a.id_serie,a.serie,a.importe,c.id_cliente,cl.nombre_fiscal,a.status,
				concat(r.serie,' - ',r.folio,' (',r.concepto,')') as descripcion,c.total,c.abonos,c.saldo,
				a.observacion
				FROM $this->useTable a
				inner join cxc c on c.id_cxc = a.id_cxc
				inner join remisiones r on r.id_remision = c.id_remision
				inner join cat_clientes cl on cl.id_cliente = c.id_cliente
				WHERE a.id_cxc_abono=$id";       
        $abono=$this->query($query);
        
        if (sizeof($abono)==0){
        	throw new Exception("Error: No se encontró un abono con esos parámetros");
        }
			
		$datos=array();
		$datos['Abono']=$abono[0];   
		
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_cxc_abono'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		$arrResult[0]['id_almacen'] = $id_almacen;		
		
        return $arrResult[0];		
	}
	
	private function gastarFolio($id_serie,$folio){
		$sigFol=floatval($folio)+1;
		$sql="UPDATE cat_series set foliosig=$sigFol WHERE id_serie=$id_serie";		
		$this->update($sql);
	}

	private function registrarPago($id_cxc, $id_cxc_abono, $importe, $registroNuevo){
		if($registroNuevo){			
			$sql="UPDATE cxc set abonos=abonos + $importe, saldo = saldo - $importe WHERE id_cxc=$id_cxc";		
			$this->update($sql);					
		}else{
			
			$sql="SELECT a.id_cxc, a.importe FROM cxc_abonos a
					WHERE a.id_cxc_abono = $id_cxc_abono";
					
			$abono=$this->select($sql);
			
			$id_cxc_pago = $abono[0]['id_cxc'];
			$impote_abono = $abono[0]['importe'];
			
			$sql="UPDATE cxc set abonos=abonos - $impote_abono, saldo = saldo + $impote_abono WHERE id_cxc=$id_cxc_pago";		
			$this->update($sql);
			
			$sql="UPDATE cxc set abonos=abonos + $importe, saldo = saldo - $importe WHERE id_cxc=$id_cxc";		
			$this->update($sql);			
		}		
	}
}
?>
