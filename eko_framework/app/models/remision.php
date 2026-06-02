<?php
require ('eko_framework/app/models/stock.php');

class RemisionModel extends Model{
    var $useTable = 'remisiones';
	var $detalleTable="remisiones_detalles";
    var $name='Remision';
    var $primaryKey = 'id_remision';
	var $primaryKeyDetalle = 'id_remision_detalle';
    var $specific = true;
    var $camposAfiltrar = array('concepto');
		
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
		
		/*
        if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);
        } else {
            $filtroSql = '';
        }
		
		 if (strlen($filtroSql) > 0) {
				if ($filtroStatus=='A')
                $filtroSql.=" AND m.status='A' ";
				if ($filtroStatus=='I')
                $filtroSql.=" AND m.status='I' ";
            }else {
               if ($filtroStatus=='A')
                $filtroSql.="WHERE m.status='A' ";
				if ($filtroStatus=='I')
                $filtroSql.="WHERE m.status='I' ";
            }
		*/
		
		if ($filtroStatus=='A')
			$filtroSql.=" AND r.status='A' ";
		if ($filtroStatus=='I')
            $filtroSql.=" AND r.status='I' ";
		
		$filtroSql.=    "AND (fecha BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable r
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT r.id_remision,r.concepto,concat(serie,' - ',folio) as serie_folio,
        			DATE_FORMAT(r.fecha,'%d/%m/%Y') as fecha,
					a.nombre_agente,c.nombre_fiscal as nombre_cliente,
					case r.condicion_pago when 1 then 'CONTADO' when 2 then 'CREDITO' else '' end as condicion_pago,
        		r.total,r.status,r.aplicado
        		 FROM $this->useTable r
        			left join cat_agentes a on a.id_agente = r.id_agente
					left join cat_clientes c on c.id_cliente = r.id_cliente
				  $filtroSql ORDER BY r.fecha limit $start,$limit ;";

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
					
			$Remision = $params['Remision'];
					
			$Conceptos=json_decode( stripslashes($params['Conceptos']), true);
			
			$numConceptos=sizeof($Conceptos);
			// Throw new Exception($numConceptos);
			if($numConceptos == 0)
				Throw new Exception("No se recibieron detalles.");
				
			
			$id_empresa = $Remision['id_empresa'];		
			$id_sucursal = $Remision['id_sucursal'];
			$id_almacen = $Remision['id_almacen'];					 
			$id_remision = $Remision['id_remision'];			

			$id_serie = $Remision['id_serie'];
			$nombre_serie = $Remision['nombre_serie'];
			$folio = $Remision['folio'];
			$fecha = $Remision['fecha'];
			$hora = $Remision['hora'];
			$condicion_pago = $Remision['condicion_pago'];
			
			$concepto = $Remision['concepto'];
			$id_cliente = $Remision['id_cliente'];
			$id_agente = $Remision['id_agente'];
			
			$importe = $Remision['importe'];
			$descuento = $Remision['descuento'];
			$subtotal = $Remision['subtotal'];
			$comision = $Remision['comision'];
			$impuestos = $Remision['impuestos'];
			$total = $Remision['total'];
			$status = $Remision['status'];			
		
			$datetime="$fecha $hora";
			
			$fecha=date('Y-m-d H:i:s',strtotime($datetime));
			
			if($id_remision > 0){

				$sql="SELECT r.status FROM remisiones r
					WHERE id_remision = $id_remision";
					
				$RemisionStatus=$this->select($sql);		
				$statusRemision = $RemisionStatus[0]['status'];
				if($statusRemision == 'I'){				
					throw new Exception("Error: La remision se encuentra inactiva");				
				}

				$query="UPDATE $this->useTable SET ";
				
				$query.="usermodif=$IDUsu";    //LOG
				$query.=",fechamodif=now()";
				$where=" WHERE $this->primaryKey = ".$id_remision;
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
			
			if (is_numeric($id_agente)){	
				$query.=",id_agente='".$id_agente."'";
			}
			
			if (is_numeric($folio)){	
				$query.=",folio='".$folio."'";
			}
			
			if (is_numeric($condicion_pago)){	
				$query.=",condicion_pago='".$condicion_pago."'";
			}
			
			$query.=",serie='".$this->EscComillas($nombre_serie)."'";
			$query.=",fecha='".$fecha."'";
			$query.=",concepto='".$this->EscComillas($concepto)."'";
			
			$query.=",importe='".$this->EscComillas($importe)."'";
			$query.=",descuento='".$this->EscComillas($descuento)."'";
			$query.=",subtotal='".$this->EscComillas($subtotal)."'";
			$query.=",comision='".$this->EscComillas($comision)."'";
			$query.=",impuestos='".$this->EscComillas($impuestos)."'";
			$query.=",total='".$this->EscComillas($total)."'";
			$query.=",status='".$this->EscComillas($status)."'";
		

			$query=$query.$where;
			 
			if ($registroNuevo){            
				$id= $this->insert($query); 			
				
			}else{		
				$result=$this->update($query);               
				$id=$id_remision;
			}
			
			
			$this->guardarDetalles($id,$id_almacen,0,$Conceptos,4,$registroNuevo);
			
			$data=$this->getById($id);   
			
			if ($registroNuevo){
				$this->gastarFolio( $id_serie, $folio);
			}
			$response['success']    = true;
			$response['msg']       = array('titulo'=>"Remisiones",'mensaje'=>"Remision guardada correctamente");
			$response['data']    = $data;
		}catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
		return $response;
                     

    }
	
	public function delete($id){
		$stock = new Stock();
		
			
			$sql="SELECT r.id_remision,r.aplicado,r.condicion_pago,r.status FROM remisiones r
					WHERE id_remision = $id";
					
			$Remision=$this->select($sql);
			
			$id_remision = $Remision[0]['id_remision'];
			$aplicada = $Remision[0]['aplicado'];
			$condicion_pago = $Remision[0]['condicion_pago'];
			$status = $Remision[0]['status'];

			if($status == 'I'){				
				throw new Exception("Error: La remision se encuentra inactiva");				
			}
			
			if($aplicada == 1){				
				throw new Exception("Error: La remision se encuentra aplicada");
				
			}
			
			$sql="SELECT r.id_almacen,rd.id_producto,rd.cantidad FROM remisiones_detalles rd
					INNER JOIN remisiones r on r.id_remision = rd.id_remision
					WHERE rd.id_remision = $id";
					
			$detalles=$this->select($sql);
			
			if (sizeof($detalles)==0){
				throw new Exception("Error: No se encontraron detalles");
			}
			
			foreach($detalles as $detalle){
				$id_alm_ori=$detalle['id_almacen'];
				$id_producto=$detalle['id_producto'];
				$cantidad=$detalle['cantidad'];
				
				$stock->entrada($id_alm_ori,$id_producto,$cantidad);
				
			
			}	

		//$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_remision = $id ";
		//$this->queryDelete($sqlDelete);		
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];	
		$sqlUpdate="UPDATE remisiones set status='I', usermodif = $IDUsu, fechamodif=now() WHERE id_remision=$id";					
		$this->update($sqlUpdate);		
        return $id;
    }

	 function getById($id){
    	 $query="SELECT r.id_remision,DATE_FORMAT(r.fecha,'%d/%m/%Y %H:%i:%S') as fecha,r.folio,
				r.condicion_pago,r.id_serie,r.serie,r.concepto,r.importe,r.descuento,r.subtotal,r.impuestos,r.total,r.id_agente,ag.nombre_agente,r.id_cliente,c.nombre_fiscal as nombre_cliente,ifnull(r.aplicado,0) as aplicado,ifnull(c.foraneo,0) as foraneo, r.status
				FROM $this->useTable r
				left join cat_agentes ag on ag.id_agente = r.id_agente
				left join cat_clientes c on c.id_cliente = r.id_cliente
				WHERE r.id_remision=$id";       
        $remision=$this->query($query);
        
        if (sizeof($remision)==0){
        	throw new Exception("Error: No se encontró una remision con esos parámetros");
        }
			
		$query="SELECT d.id_remision_detalle,d.id_producto,p.descripcion,u.codigo_unidad as unidad_medida,d.cantidad,d.costo,d.importe,d.descuento,d.subtotal,d.impuestos,d.total
				FROM $this->detalleTable d
				inner join cat_productos p on p.id_producto = d.id_producto
				inner join cat_unidadesdemedida u on u.id_unidadmedida = p.id_unidadmedida
				WHERE d.id_remision=$id ORDER BY d.id_remision_detalle";
		
		
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptos=$detalles;
		
		$datos=array();
		$datos['Remision']=$remision[0];   
		$datos['Detalles']=$detalles;
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_remision'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		$arrResult[0]['id_almacen'] = $id_almacen;		
		
        return $arrResult[0];		
	}
	
	private function guardarDetalles($id,$id_almacen_origen,$id_almacen_destino,$conceptos,$tipo_movimiento,$registroNuevo){
		$stock = new Stock();
		
		if(!$registroNuevo){
			
			$sql="SELECT r.id_almacen,rd.id_producto,rd.cantidad FROM remisiones_detalles rd
					INNER JOIN remisiones r on r.id_remision = rd.id_remision
					WHERE rd.id_remision = $id";
					
			$detalles=$this->select($sql);
			
			if (sizeof($detalles)==0){
				throw new Exception("Error: No se encontraron detalles");
			}
			
			foreach($detalles as $detalle){
				$id_alm_ori=$detalle['id_almacen'];
				$id_producto=$detalle['id_producto'];
				$cantidad=$detalle['cantidad'];
					
				$stock->entrada($id_alm_ori,$id_producto,$cantidad);
				
			
			}	
			
		}
		
		$sqlDelete="DELETE FROM  $this->detalleTable WHERE $this->primaryKey = $id ";
		$this->queryDelete($sqlDelete);
		
		foreach ($conceptos  as $concepto) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$id_producto = $concepto['id_producto'];
			$cantidad = $concepto['cantidad'];
			$costo = $concepto['costo'];
			$importe = $concepto['importe'];
			$descuento = $concepto['descuento'];
			$subtotal = $concepto['subtotal'];
			$impuestos = $concepto['impuestos'];
			$total = $concepto['total'];
			try{
				$queryInsert="INSERT INTO $this->detalleTable SET id_remision=$id,id_producto='$id_producto',cantidad='$cantidad', costo='$costo',
            	importe='$importe',descuento='$descuento',subtotal='$subtotal',impuestos='$impuestos',total='$total';";
				// throw new Exception($queryInsert);	
				$IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 
				// throw new Exception($queryInsert);	
				
				$stock->salida($id_almacen_origen,$id_producto,$cantidad);
				            
			}catch(Exception $e){            
				return false;
			}  
			
			
		}
	}
	
	private function gastarFolio($id_serie,$folio){
		$sigFol=floatval($folio)+1;
		$sql="UPDATE cat_series set foliosig=$sigFol WHERE id_serie=$id_serie";		
		$this->update($sql);
	}

	public function aplicar($id){
		$response=array();
		
		$sql="SELECT r.status FROM remisiones r
					WHERE id_remision = $id";
					
		$Remision=$this->select($sql);		
		$status = $Remision[0]['status'];
		if($status == 'I'){				
			throw new Exception("Error: La remision se encuentra inactiva");				
		}

		try{			
			$IDUsu=$_SESSION['Auth']['User']['IDUsu'];    
			
			$sql="UPDATE remisiones set aplicado=1,fecha_aplica = now() WHERE id_remision=$id";		
			
			$this->update($sql);
			
			$data=$this->getById($id); 
			$condicion_pago = $data['Remision']['condicion_pago'];
			$id_cliente = $data['Remision']['id_cliente'];
			$total = $data['Remision']['total'];			
			
			if($condicion_pago == 2){
				$query="INSERT INTO cxc SET ";
				$query.="usercreador=$IDUsu";    //LOG
				$query.=",fechacreador=now()";           
			
				if (is_numeric($id))
					$query.=",id_remision='".$id."'";
				
				if (is_numeric($id_cliente))
					$query.=",id_cliente='".$id_cliente."'";
				
				$query.=",total='".$total."'";	
				$query.=",saldo='".$total."'";	
				$id_cxc = $this->insert($query); 
			}
			$response['success']    = true;
			$response['msg']       = array('titulo'=>"Remisiones",'mensaje'=>"Remision aplicada correctamente");
			$response['data']    = $data;
		}catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
        return $response;
			
    }
	
	public function desaplicar($id){
		$response=array();	
		$sql="SELECT r.status FROM remisiones r
					WHERE id_remision = $id";
					
		$Remision=$this->select($sql);		
		$status = $Remision[0]['status'];
		if($status == 'I'){				
			throw new Exception("Error: La remision se encuentra inactiva");				
		}
	
		try{
			
			$data=$this->getById($id);			
			$condicion_pago = $data['Remision']['condicion_pago'];
						
			if($condicion_pago == 2){
				
				$sqlAbonos="SELECT COUNT(id_cxc_abono) numabonos FROM cxc c
								INNER JOIN cxc_abonos a ON a.id_cxc = c.id_cxc
								WHERE c.id_remision = $id";
				$arrAbonos=$this->select($sqlAbonos);
				
				if ( intval($arrAbonos[0]['numabonos'])>0 ){
						Throw new Exception("No es posible desaplicar la remision, existen abonos aplicados.");
				}
				
				$sqlDelete="DELETE FROM  cxc WHERE id_remision = $id ";
				$this->queryDelete($sqlDelete);					
			}
			
			$sql="UPDATE remisiones set aplicado=0, fecha_aplica = null WHERE id_remision=$id";					
			$this->update($sql);			
			$data=$this->getById($id);
				
			$response['success']    = true;
			$response['msg']       = array('titulo'=>"Remisiones",'mensaje'=>"Remision desaplicada correctamente");
			$response['data']    = $data;
		}catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
        return $response;
			
    }
	
	public function auditoria($id){
		$query="SELECT r.id_remision,r.usercreador,r.fechacreador,r.usermodif,r.fechamodif				
				FROM $this->useTable r				
				WHERE r.id_remision=$id";       
        $remision=$this->query($query);
        
        if (sizeof($remision)==0){
        	throw new Exception("Error: No se encontró una remision con esos parámetros");
        }		
		
		$response=array();
		$response['Auditoria']=$remision[0];  		
		return $response;
		
	}
}
?>
