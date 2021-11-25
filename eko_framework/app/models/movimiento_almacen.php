<?php
require ('eko_framework/app/models/stock.php');

class MovimientoAlmacenModel extends Model{
    var $useTable = 'movimientos_almacen';
	var $detalleTable="movimientos_almacen_detalles";
    var $name='MovimientoAlmacen';
    var $primaryKey = 'id_movimiento';
    var $specific = true;
    var $camposAfiltrar = array('concepto_movimiento');
		
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
            $filtroSql.=" AND m.id_empresa = $IDEmpresa AND m.id_sucursal = $IDSucursal ";
        } else {
           $filtroSql = "WHERE m.id_empresa = $IDEmpresa AND m.id_sucursal = $IDSucursal ";
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
			$filtroSql.=" AND m.status='A' ";
		if ($filtroStatus=='I')
            $filtroSql.=" AND m.status='I' ";
		
		$filtroSql.=    "AND (fecha_movimiento BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable m
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT m.id_movimiento,t.nombre_movimiento tipo_movimiento,m.concepto_movimiento,concat(serie_movimiento,' - ',folio_movimiento) as serie_folio,
        			DATE_FORMAT(m.fecha_movimiento,'%d/%m/%Y') as fecha_movimiento,
        		ao.nombre_almacen almacen_origen,ad.nombre_almacen almacen_destino,m.total,m.status
        		 FROM $this->useTable m
        			left join cat_almacenes ao on ao.id_almacen = m.id_almacen_origen
        			left join cat_almacenes ad on ad.id_almacen = m.id_almacen_destino
        			inner join cat_tiposmovimientos t on t.id_tipomovimiento = m.id_tipomovimiento
				  $filtroSql ORDER BY m.fecha_movimiento limit $start,$limit ;";

		//throw new Exception($query);		  
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		/*
		 
		 * */
       /* $response["metaData"]=array(
	        "totalProperty"=> "totalRows",
	        "root"=> "data",
	        "id"=> "IDCli", 
        	"fields"=>array(    			
        		array("name"=>"IDCli","type"=>"int"),
	            array("name"=>'NomCli',"type"=>'string'),
	            array("name"=>'StatusCli'),
	            array("name"=>'RazSocCliDet',"type"=>'string'),
	            array("name"=>'NomConCorCli',"type"=>'string'),
	            array("name"=>'EmaConCorCli',"type"=>'string',"formatear"=>false),
	            array("name"=>'TipoCliDet'),
	            array("name"=>'RFCCliDet',"type"=>'string'),  
	            array("name"=>'TelConCorCli',"type"=>'tel'),
	            array("name"=>'CelConCorCli',"type"=>'tel'),
	            array("name"=>'NomConCorCli',"type"=>'string')
        	),       
	        "sortInfo"=> array(
	            "field"=>"NomCli",
	            "direction"=>"$sort"
	        )
	    );*/
        return $response;
    }
    
	public function guardar($params){
				
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$Movimiento = $params['Movimiento'];
				
		$Conceptos=json_decode( stripslashes($params['Conceptos']), true);
		
		$numConceptos=sizeof($Conceptos);
		// Throw new Exception($numConceptos);
		if($numConceptos == 0)
			Throw new Exception("No se recibieron detalles.");
			
		
		$id_empresa = $Movimiento['id_empresa'];		
		$id_sucursal = $Movimiento['id_sucursal'];
				 
		$id_movimiento_almacen = $Movimiento['id_movimiento_almacen'];
		$id_serie = $Movimiento['id_serie'];
		$nombre_serie = $Movimiento['nombre_serie'];
		$folio_movimiento = $Movimiento['folio_movimiento'];
		$fecha = $Movimiento['fecha'];
		$hora = $Movimiento['hora'];
		$id_tipomovimiento = $Movimiento['id_tipomovimiento'];
		$tipo_movimiento = $Movimiento['tipo_movimiento'];
		$concepto_movimiento = $Movimiento['concepto_movimiento'];
		$id_almacen_origen = $Movimiento['id_almacen_origen'];
		$id_almacen_destino = $Movimiento['id_almacen_destino'];
		$id_agente = $Movimiento['id_agente'];
		
		$importe = $Movimiento['importe'];
		$descuento = $Movimiento['descuento'];
		$subtotal = $Movimiento['subtotal'];
		$impuestos = $Movimiento['impuestos'];
		$total = $Movimiento['total'];
		$status = $Movimiento['status'];
	
		$datetime="$fecha $hora";
		
		$fecha_movimiento=date('Y-m-d H:i:s',strtotime($datetime));
		
		if($tipo_movimiento == 3){
			throw new Exception("No es posible generar tipos de movimiento de traspaso.");
		}
		
        if($id_movimiento_almacen > 0){
			$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_movimiento_almacen;
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
		
		if (is_numeric($id_almacen_origen)){	
			$query.=",id_almacen_origen='".$id_almacen_origen."'";
		}
		
		if (is_numeric($id_almacen_destino)){	
			$query.=",id_almacen_destino='".$id_almacen_destino."'";
		}
		
		if (is_numeric($id_agente)){	
			$query.=",id_agente='".$id_agente."'";
		}
		
		if (is_numeric($id_tipomovimiento)){	
			$query.=",id_tipomovimiento='".$id_tipomovimiento."'";
		}
		
		if (is_numeric($folio_movimiento)){	
			$query.=",folio_movimiento='".$folio_movimiento."'";
		}
		
		$query.=",serie_movimiento='".$this->EscComillas($nombre_serie)."'";
		$query.=",fecha_movimiento='".$fecha_movimiento."'";
		$query.=",concepto_movimiento='".$this->EscComillas($concepto_movimiento)."'";
		
		$query.=",importe='".$this->EscComillas($importe)."'";
		$query.=",descuento='".$this->EscComillas($descuento)."'";
		$query.=",subtotal='".$this->EscComillas($subtotal)."'";
		$query.=",impuestos='".$this->EscComillas($impuestos)."'";
		$query.=",total='".$this->EscComillas($total)."'";
		$query.=",status='".$this->EscComillas($status)."'";
	

        $query=$query.$where;
		 
		if ($registroNuevo){            
			$id= $this->insert($query); 			
			
		}else{		
			$result=$this->update($query);               
			$id=$id_movimiento_almacen;
		}
		
		
		$this->guardarDetalles($id,$id_almacen_origen,$id_almacen_destino,$Conceptos,$tipo_movimiento,$registroNuevo);
		
        $data=$this->getById($id);   
        
		if ($registroNuevo){
			$this->gastarFolio( $id_serie, $folio_movimiento );
		}
		// throw new Exception($data['id_movimiento']);
		
		return $data;
                     

    }
	
	public function delete($id){
		$stock = new Stock();
		$sql="SELECT m.id_almacen_origen,m.id_almacen_destino,md.id_producto,md.cantidad,t.tipo_movimiento FROM movimientos_almacen_detalles md
					INNER JOIN movimientos_almacen m on m.id_movimiento = md.id_movimiento
					INNER JOIN cat_tiposmovimientos t on t.id_tipomovimiento = m.id_tipomovimiento
					WHERE md.id_movimiento = $id";
					
			$detalles=$this->select($sql);
			
			if (sizeof($detalles)==0){
				throw new Exception("Error: No se encontraron detalles");
			}
			
			foreach($detalles as $detalle){
				$id_alm_ori=$detalle['id_almacen_origen'];
				$id_alm_des=$detalle['id_almacen_destino'];
				$id_producto=$detalle['id_producto'];
				$cantidad=$detalle['cantidad'];
				$tipo_movimiento=$detalle['tipo_movimiento'];
					
				if($tipo_movimiento == 1){
					$stock->salida($id_alm_des,$id_producto,$cantidad);			
				}else if($tipo_movimiento == 2 || $tipo_movimiento == 4){
					$stock->entrada($id_alm_ori,$id_producto,$cantidad);
				}
			
			}	

		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_movimiento = $id ";
		$this->queryDelete($sqlDelete);			
		
        return parent::delete($id);
    }

	 function getById($id){
    	 $query="SELECT m.id_movimiento,m.id_almacen_origen,ao.nombre_almacen as nombre_almacen_origen,m.id_almacen_destino,ad.nombre_almacen as nombre_almacen_destino, DATE_FORMAT(m.fecha_movimiento,'%d/%m/%Y %H:%i:%S') as fecha_movimiento,m.folio_movimiento,
				m.id_tipomovimiento,m.id_serie,m.serie_movimiento,m.concepto_movimiento,t.nombre_movimiento,t.tipo_movimiento,
				m.importe,m.descuento,m.subtotal,m.impuestos,m.total,m.id_agente,ag.nombre_agente
				FROM $this->useTable m
				inner join cat_tiposmovimientos t on t.id_tipomovimiento = m.id_tipomovimiento
				left join cat_almacenes ao on ao.id_almacen = m.id_almacen_origen
				left join cat_almacenes ad on ad.id_almacen = m.id_almacen_destino
				left join cat_agentes ag on ag.id_agente = m.id_agente
				WHERE m.id_movimiento=$id";       
        $movimiento=$this->query($query);
        
        if (sizeof($movimiento)==0){
        	throw new Exception("Error: No se encontró un movimiento con esos parámetros");
        }
			
		$query="SELECT d.id_movimiento_detalle,d.id_producto,p.descripcion,u.codigo_unidad as unidad_medida,d.cantidad,d.costo,d.importe,d.descuento,d.subtotal,d.impuestos,d.total
				FROM $this->detalleTable d
				inner join cat_productos p on p.id_producto = d.id_producto
				inner join cat_unidadesdemedida u on u.id_unidadmedida = p.id_unidadmedida
				WHERE d.id_movimiento=$id ORDER BY d.id_movimiento_detalle";
		
		
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptos=$detalles;
		
		$datos=array();
		$datos['Movimiento']=$movimiento[0];   
		$datos['Detalles']=$detalles;
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){	
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha_movimiento FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_movimiento'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		$arrResult[0]['id_almacen'] = $id_almacen;		
		
        return $arrResult[0];		
	}
	
	private function guardarDetalles($id,$id_almacen_origen,$id_almacen_destino,$conceptos,$tipo_movimiento,$registroNuevo){
		$stock = new Stock();
		
		if(!$registroNuevo){
			
			$sql="SELECT m.id_almacen_origen,m.id_almacen_destino,md.id_producto,md.cantidad FROM movimientos_almacen_detalles md
					INNER JOIN movimientos_almacen m on m.id_movimiento = md.id_movimiento
					WHERE md.id_movimiento = $id";
					
			$detalles=$this->select($sql);
			
			if (sizeof($detalles)==0){
				throw new Exception("Error: No se encontraron detalles");
			}
			
			foreach($detalles as $detalle){
				$id_alm_ori=$detalle['id_almacen_origen'];
				$id_alm_des=$detalle['id_almacen_destino'];
				$id_producto=$detalle['id_producto'];
				$cantidad=$detalle['cantidad'];
					
				if($tipo_movimiento == 1){
					$stock->salida($id_alm_des,$id_producto,$cantidad);			
				}else if($tipo_movimiento == 2 || $tipo_movimiento == 4){
					$stock->entrada($id_alm_ori,$id_producto,$cantidad);
				}
			
			}	
			
		}
		
		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_movimiento = $id ";
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
				$queryInsert="INSERT INTO $this->detalleTable SET id_movimiento=$id,id_producto='$id_producto',cantidad='$cantidad', costo='$costo',
            	importe='$importe',descuento='$descuento',subtotal='$subtotal',impuestos='$impuestos',total='$total';";
				// throw new Exception($queryInsert);	
				$IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 
				// throw new Exception($queryInsert);	
				if($tipo_movimiento == 1){
					$stock->entrada($id_almacen_destino,$id_producto,$cantidad);			
				}else if($tipo_movimiento == 2 || $tipo_movimiento == 4){
					$stock->salida($id_almacen_origen,$id_producto,$cantidad);
				}            
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
		
	private function guardarDetallesRemisiones(){
		//Se creo esta funcion porque las remisiones no estaban afectando el stock, al guardar un movimiento se ejecuta la funcion seguido de un throw exception.
		$stock = new Stock();
		
					
			$sql="SELECT m.id_almacen_origen,md.id_producto,md.cantidad
					FROM movimientos_almacen_detalles md 
					INNER JOIN movimientos_almacen m ON m.id_movimiento = md.id_movimiento
					INNER JOIN cat_tiposmovimientos t ON t.id_tipomovimiento = m.id_tipomovimiento
					WHERE t.tipo_movimiento = 4
					ORDER BY  m.id_movimiento,md.id_producto";
					
			$detalles=$this->select($sql);
			
			if (sizeof($detalles)==0){
				throw new Exception("Error: No se encontraron detalles");
			}
			
			foreach($detalles as $detalle){
				$id_alm_ori=$detalle['id_almacen_origen'];
				$id_producto=$detalle['id_producto'];
				$cantidad=$detalle['cantidad'];
					
				$stock->salida($id_alm_ori,$id_producto,$cantidad);
				  
			
			}	
		
	}
		
}
?>
