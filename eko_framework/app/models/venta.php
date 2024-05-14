<?php
require ('eko_framework/app/models/stock.php');
require ('eko_framework/app/models/turno.php');
class VentaModel extends Model{
    var $useTable = 'ventas';
	var $detalleTable="ventas_detalles";
	var $formaspagosTable="ventas_formaspagos";
    var $name='Venta';
    var $primaryKey = 'id_venta';
    var $specific = true;
    var $camposAfiltrar = array('concepto_venta');
		
    function readAll($params) {
        
		$limit = (empty($params['limit'])) ? 20 : $params['limit'];
		$start = (empty($params['start'])) ?  0 : $params['start'];
		$filtro = (empty($params['filtro'])) ?  '': $params['filtro']; 
		$filtroStatus = (empty($params['filtroStatus'])) ?  'A': $params['filtroStatus'];			
		$fechaInicio=(empty($params['fechaInicio'])) ?  '': $params['fechaInicio'];
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
		
		$filtroSql.=    "AND (fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable m
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT m.id_venta,m.concepto_movimiento,concat(serie_movimiento,' - ',folio_movimiento) as serie_folio,
        			DATE_FORMAT(m.fecha_movimiento,'%d/%m/%Y') as fecha_movimiento,
        		ao.nombre_almacen almacen_origen,ad.nombre_almacen almacen_destino,m.total
        		 FROM $this->useTable m
        			left join cat_almacenes a on a.id_almacen = m.id_almacen        			
				  $filtroSql ORDER BY m.fecha_venta limit $start,$limit ;";

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
		// throw new Exception('No existe turno');
    	
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$Venta = $params['Venta'];
		
		
		$Conceptos=json_decode( stripslashes($params['Conceptos']), true);
		$FormasPagos=json_decode( stripslashes($params['FormasPagos']), true);
		
			
		
		
		$id_empresa = $Venta['id_empresa'];		
		$id_sucursal = $Venta['id_sucursal'];
		$id_almacen = $Venta['id_almacen'];
		$turnoModel = new Turno();
		$turno = $turnoModel->getTurno($id_empresa,$id_sucursal);
		
		if(!$turno){
			throw new Exception('No existe turno abierto');
		}
		
		$id_turno = $turno['id_turno'];	
		
		
				 
		//$id_movimiento_almacen = $Movimiento['id_movimiento_almacen'];
		$id_serie = $Venta['id_serie'];
		$serie_venta = $Venta['serie_venta'];
		$folio_venta = $Venta['folio_venta'];
		$fecha = $Venta['fecha'];
		$hora = $Venta['hora'];
		$id_cliente = $Venta['id_cliente'];
		$concepto_venta = $Venta['concepto_venta'];
		$id_agente = $Venta['id_agente'];
			
		$importe = $Venta['importe'];
		$descuento = $Venta['descuento'];
		$subtotal = $Venta['subtotal'];
		$impuestos = $Venta['impuestos'];
		$total = $Venta['total'];
		$importe_pagado = $Venta['importe_pagado'];
		$cambio = $Venta['cambio'];
	
		$datetime="$fecha $hora";
		
		$fecha_venta=date('Y-m-d H:i:s',strtotime($datetime));
		
		$query_venta = "SELECT COUNT(id_venta) numVentas FROM ventas WHERE id_empresa = $id_empresa AND id_sucursal = $id_sucursal AND serie_venta = '$serie_venta' AND folio_venta = $folio_venta";
		
		$arrVentas=$this->select($query_venta);
		if ( intval($arrVentas[0]['numVentas'])>0 ){
			$querySerie = " SELECT s.foliosig FROM cat_parametros_ventas p";
			$querySerie.= " INNER JOIN cat_series s on s.id_serie = p.id_serie";
			$querySerie.= " WHERE p.id_empresa = $id_empresa AND p.id_sucursal = $id_sucursal AND p.status = 'A';";
			$res = $this->query($querySerie);
			
			$folio_venta = $res[0]['foliosig'];	
	
		}
			// throw new Exception('La venta ya se encuentra registrada con esa serie y folio');	
		
        $query="INSERT INTO $this->useTable SET ";
        $query.="usercreador=$IDUsu";    //LOG
        $query.=",fechacreador=now()";
         
		
        
        
		if (is_numeric($id_empresa)){	
			$query.=",id_empresa='".$id_empresa."'";
		}
		
		if (is_numeric($id_turno)){	
			$query.=",id_turno='".$id_turno."'";
		}
		
		
		if (is_numeric($id_sucursal)){	
			$query.=",id_sucursal='".$id_sucursal."'";
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
		
		if (is_numeric($id_serie)){	
			$query.=",id_serie='".$id_serie."'";
		}
		
		if (is_numeric($folio_venta)){	
			$query.=",folio_venta='".$folio_venta."'";
		}
		
		$query.=",serie_venta='".$this->EscComillas($serie_venta)."'";
		$query.=",fecha_venta='".$fecha_venta."'";
		$query.=",concepto_venta='".$this->EscComillas($concepto_venta)."'";
		
		$query.=",importe='".$this->EscComillas($importe)."'";
		$query.=",descuento='".$this->EscComillas($descuento)."'";
		$query.=",subtotal='".$this->EscComillas($subtotal)."'";
		$query.=",impuestos='".$this->EscComillas($impuestos)."'";
		$query.=",total='".$this->EscComillas($total)."'";
		$query.=",pago='".$this->EscComillas($importe_pagado)."'";
		$query.=",cambio='".$this->EscComillas($cambio)."'";
		$query.=",status='A'";
	

        $query=$query;
		 
		 
		try{
            $id= $this->insert($query);		
			$this->guardarDetalles($id,$id_almacen,$Conceptos);
			$this->guardarFormasPagos($id,$FormasPagos);		
			$this->gastarFolio( $id_serie, $folio_venta);
            // $this->id = $id;
            $data = $this->getById($id);
            return $data['Venta'];
        }catch(Exception $e){            
            return false;
        } 
		        
		
		
		// return array(
			// 'id_venta'=>$id
						
		// );	
		
                     

    }
	
	public function delete($id){
		$stock = new Stock();
		$sql="SELECT v.id_almacen,vd.id_producto,vd.cantidad FROM ventas_detalles vd
					INNER JOIN ventas v on v.id_venta = vd.id_venta
					WHERE vd.id_venta = $id";
					
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

		//$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_venta = $id ";
		//$this->queryDelete($sqlDelete);			
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];	
		$sqlUpdate="UPDATE ventas set status='I', usermodif = $IDUsu, fechamodif=now() WHERE id_venta=$id";					
		$this->update($sqlUpdate);		
        return $id;
    }

	 function getById($id){
    	 $query="SELECT v.id_venta,a.id_almacen, DATE_FORMAT(v.fecha_venta,'%d/%m/%Y %H:%i:%S') as fecha_venta,v.folio_venta,
				v.id_serie,v.serie_venta,v.id_cliente,c.nombre_fiscal as nombre_cliente,CONCAT(v.serie_venta,' - ',v.folio_venta) as SerieFolio,
				v.importe,v.descuento,v.subtotal,v.impuestos,v.total,v.pago,v.cambio, v.id_agente, ag.nombre_agente
				FROM $this->useTable v
				inner join cat_almacenes a on a.id_almacen = v.id_almacen
				inner join cat_clientes c on c.id_cliente = v.id_cliente
				left join cat_agentes ag on ag.id_agente = v.id_agente
				WHERE v.id_venta=$id";       
        $venta=$this->query($query);
        
        if (sizeof($venta)==0){
        	throw new Exception("Error: No se encontró una venta con esos parámetros");
        }
			
		$query="SELECT d.id_venta_detalle,d.id_producto,p.descripcion,u.codigo_unidad as unidad_medida,d.cantidad,d.precio,d.importe,d.descuento,d.subtotal,d.impuestos,d.total
				FROM $this->detalleTable d
				inner join cat_productos p on p.id_producto = d.id_producto
				inner join cat_unidadesdemedida u on u.id_unidadmedida = p.id_unidadmedida
				WHERE d.id_venta=$id ORDER BY d.id_venta_detalle";
		
		
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptos=$detalles;
		
		$query="SELECT vf.id_formapago,f.nombre_formapago,vf.importe,f.tipo_formapago 
				FROM $this->formaspagosTable vf 
				inner join cat_formaspagos f on f.id_formapago = vf.id_formapago
				WHERE vf.id_venta=$id ORDER BY vf.id_formapago";
		
		
		$formaspagos=$this->query($query);	//<--Lee los detalles de la tabla temporal		
		
		
		$datos=array();
		$datos['Venta']=$venta[0];   
		$datos['Detalles']=$detalles;
		$datos['FormasPagos']=$formaspagos;
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
	
	private function guardarDetalles($id,$id_almacen,$conceptos){
		$stock = new Stock();
		
		foreach ($conceptos  as $concepto) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$id_producto = $concepto['id_producto'];
			$cantidad = $concepto['cantidad'];
			$precio = $concepto['precio'];
			$importe = $concepto['importe'];
			$descuento = $concepto['descuento'];
			$subtotal = $concepto['subtotal'];
			$impuestos = $concepto['impuestos'];
			$total = $concepto['total'];
			
			$queryInsert="INSERT INTO $this->detalleTable SET id_venta=$id,id_producto='$id_producto',cantidad='$cantidad', precio='$precio',
            	importe='$importe',descuento='$descuento',subtotal='$subtotal',impuestos='$impuestos',total='$total';";
			// throw new Exception($queryInsert);	
            $IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 
			// throw new Exception($queryInsert);	
			
			
			$query="SELECT tipo_producto FROM cat_productos WHERE id_producto=$id_producto";       
			$producto=$this->query($query);
			
			if($producto[0]['tipo_producto'] == 'P')
				$stock->salida($id_almacen,$id_producto,$cantidad);
			
			
		}
	}
	
	private function guardarFormasPagos($id,$formaspagos){
		$stock = new Stock();
		
		foreach ($formaspagos  as $formapago) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$id_formapago = $formapago['id_formapago'];
			$importe = $formapago['importe'];
			
			
			$queryInsert="INSERT INTO $this->formaspagosTable SET id_venta=$id,id_formapago='$id_formapago',importe='$importe';";
			// throw new Exception($queryInsert);	
            $IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 
									
		}
	}
	
	private function gastarFolio($id_serie,$folio){
		$sigFol=floatval($folio)+1;
		$sql="UPDATE cat_series set foliosig=$sigFol WHERE id_serie=$id_serie";		
		$this->update($sql);
	}
		
}
?>
