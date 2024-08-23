<?php
require ('eko_framework/app/models/stock.php');

class CotizacionModel extends Model{
    var $useTable = 'cotizaciones';
	var $detalleTable="cotizaciones_detalles";
    var $name='Cotizacion';
    var $primaryKey = 'id_cotizacion';
	var $primaryKeyDetalle = 'id_cotizacion_detalle';
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

        $query = "SELECT r.id_cotizacion,r.concepto,concat(serie,' - ',folio) as serie_folio,
        			DATE_FORMAT(r.fecha,'%d/%m/%Y') as fecha,
					c.nombre_fiscal as nombre_cliente,
					r.total,r.status
        		 FROM $this->useTable r
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
					
			$Cotizacion = $params['Cotizacion'];
					
			$Conceptos=json_decode( stripslashes($params['Conceptos']), true);
			
			$numConceptos=sizeof($Conceptos);
			// Throw new Exception($numConceptos);
			if($numConceptos == 0)
				Throw new Exception("No se recibieron detalles.");
				
			
			$id_empresa = $Cotizacion['id_empresa'];		
			$id_sucursal = $Cotizacion['id_sucursal'];								 
			$id_cotizacion = $Cotizacion['id_cotizacion'];
			$id_serie = $Cotizacion['id_serie'];
			$nombre_serie = $Cotizacion['nombre_serie'];
			$folio = $Cotizacion['folio'];
			$fecha = $Cotizacion['fecha'];
			$hora = $Cotizacion['hora'];
						
			$concepto = $Cotizacion['concepto'];
			$id_cliente = $Cotizacion['id_cliente'];
						
			$importe = $Cotizacion['importe'];
			$descuento = $Cotizacion['descuento'];
			$subtotal = $Cotizacion['subtotal'];
			$comision = $Cotizacion['comision'];
			$impuestos = $Cotizacion['impuestos'];
			$total = $Cotizacion['total'];
			$status = $Cotizacion['status'];
		
			$datetime="$fecha $hora";
			
			$fecha=date('Y-m-d H:i:s',strtotime($datetime));
			
			if($id_cotizacion > 0){
				$query="UPDATE $this->useTable SET ";
				
				$query.="usermodif=$IDUsu";    //LOG
				$query.=",fechamodif=now()";
				$where=" WHERE $this->primaryKey = ".$id_cotizacion;
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
			
			if (is_numeric($id_cliente)){	
				$query.=",id_cliente='".$id_cliente."'";
			}
			
			if (is_numeric($folio)){	
				$query.=",folio='".$folio."'";
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
				$id=$id_cotizacion;
			}
			
			
			$this->guardarDetalles($id,$Conceptos,$registroNuevo);
			
			$data=$this->getById($id);   
			
			if ($registroNuevo){
				$this->gastarFolio( $id_serie, $folio);
			}
			$response['success']    = true;
			$response['msg']       = array('titulo'=>"Cotizaciones",'mensaje'=>"Cotizacion guardada correctamente");
			$response['data']    = $data;
		}catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
		return $response;
                     

    }
	
	public function delete($id){	
			
		$sql="SELECT r.id_cotizacion FROM cotizaciones r
				WHERE id_cotizacion = $id";
				
		$Cotizacion=$this->select($sql);
		
		$id_cotizacion = $Cotizacion[0]['id_cotizacion'];
		
		
		$sql="SELECT rd.id_producto,rd.cantidad FROM cotizaciones_detalles rd
				INNER JOIN cotizaciones r on r.id_cotizacion = rd.id_cotizacion
				WHERE rd.id_cotizacion = $id";
				
		$detalles=$this->select($sql);
		
		if (sizeof($detalles)==0){
			throw new Exception("Error: No se encontraron detalles");
		}		
			

		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_cotizacion = $id ";
		$this->queryDelete($sqlDelete);			
		
        return parent::delete($id);
    }

	 function getById($id){
    	 $query="SELECT r.id_cotizacion,DATE_FORMAT(r.fecha,'%d/%m/%Y %H:%i:%S') as fecha,r.folio,
				r.id_serie,r.serie,r.concepto,r.importe,r.descuento,r.subtotal,r.impuestos,r.total,r.id_cliente,c.nombre_fiscal as nombre_cliente,ifnull(c.foraneo,0) as foraneo
				FROM $this->useTable r
				left join cat_clientes c on c.id_cliente = r.id_cliente
				WHERE r.id_cotizacion=$id";       
        $cotizacion=$this->query($query);
        
        if (sizeof($cotizacion)==0){
        	throw new Exception("Error: No se encontró una cotizacion con esos parámetros");
        }
			
		$query="SELECT d.id_cotizacion_detalle,d.id_producto,p.descripcion,u.codigo_unidad as unidad_medida,d.cantidad,d.costo,d.importe,d.descuento,d.subtotal,d.impuestos,d.total
				FROM $this->detalleTable d
				inner join cat_productos p on p.id_producto = d.id_producto
				inner join cat_unidadesdemedida u on u.id_unidadmedida = p.id_unidadmedida
				WHERE d.id_cotizacion=$id ORDER BY d.id_cotizacion_detalle";
		
		
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptos=$detalles;
		
		$datos=array();
		$datos['Cotizacion']=$cotizacion[0];   
		$datos['Detalles']=$detalles;
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal){
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_cotizacion'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
			
		
        return $arrResult[0];		
	}
	
	private function guardarDetalles($id,$conceptos,$registroNuevo){		
		
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
				$queryInsert="INSERT INTO $this->detalleTable SET id_cotizacion=$id,id_producto='$id_producto',cantidad='$cantidad', costo='$costo',
            	importe='$importe',descuento='$descuento',subtotal='$subtotal',impuestos='$impuestos',total='$total';";
				// throw new Exception($queryInsert);	
				$IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 
				// throw new Exception($queryInsert);					
								            
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
