<?php
require ('eko_framework/app/models/stock.php');

class InventarioModel extends Model{
    var $useTable = 'inventarios';
	var $detalleTable="inventarios_detalles";
	var $useTableM = 'movimientos_almacen';
	var $detalleTableM="movimientos_almacen_detalles";
    var $name='Inventario';
    var $primaryKey = 'id_inventario';
    var $specific = true;
    var $camposAfiltrar = array('concepto_inventario');
		
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
            $filtroSql.=" AND i.id_empresa = $IDEmpresa AND i.id_sucursal = $IDSucursal ";
        } else {
           $filtroSql = "WHERE i.id_empresa = $IDEmpresa AND i.id_sucursal = $IDSucursal ";
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
			$filtroSql.=" AND i.status='A' ";
		if ($filtroStatus=='I')
            $filtroSql.=" AND i.status='I' ";
		
		$filtroSql.=    "AND (fecha_inventario BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable i
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT i.id_inventario,i.concepto_inventario,concat(serie_inventario,' - ',folio_inventario) as serie_folio,
        			DATE_FORMAT(i.fecha_inventario,'%d/%m/%Y') as fecha_inventario,
        		a.nombre_almacen, i.status,i.aplicado
        		 FROM $this->useTable i
        			left join cat_almacenes a on a.id_almacen = i.id_almacen       			
				  $filtroSql ORDER BY i.fecha_inventario limit $start,$limit ;";

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
		 		
		$Inventario = $params['Inventario'];
				
		$Conceptos=json_decode( stripslashes($params['Conceptos']), true);
		
		$numConceptos=sizeof($Conceptos);
		// Throw new Exception($numConceptos);
		if($numConceptos == 0)
			Throw new Exception("No se recibieron detalles.");
			
		
		$id_empresa = $Inventario['id_empresa'];		
		$id_sucursal = $Inventario['id_sucursal'];
		$id_almacen = $Inventario['id_almacen'];				 
		$id_inventario = $Inventario['id_inventario'];
		$id_serie = $Inventario['id_serie'];
		$nombre_serie = $Inventario['nombre_serie'];
		$folio_inventario = $Inventario['folio_inventario'];
		$fecha = $Inventario['fecha'];
		$hora = $Inventario['hora'];
		$concepto_inventario = $Inventario['concepto_inventario'];
		$status = $Inventario['status'];
	
		$datetime="$fecha $hora";
		
		$fecha_inventario=date('Y-m-d H:i:s',strtotime($datetime));
		
        if($id_inventario > 0){
			$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_inventario;
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
		
		if (is_numeric($id_almacen)){	
			$query.=",id_almacen='".$id_almacen."'";
		}
		
		if (is_numeric($id_serie)){	
			$query.=",id_serie='".$id_serie."'";
		}
		
		if (is_numeric($folio_inventario)){	
			$query.=",folio_inventario='".$folio_inventario."'";
		}
		
		$query.=",serie_inventario='".$this->EscComillas($nombre_serie)."'";
		$query.=",fecha_inventario='".$fecha_inventario."'";
		$query.=",concepto_inventario='".$this->EscComillas($concepto_inventario)."'";
		
		$query.=",status='".$this->EscComillas($status)."'";	

        $query=$query.$where;
		 
		if ($registroNuevo){            
			$id= $this->insert($query); 			
			
		}else{		
			$result=$this->update($query);               
			$id=$id_inventario;
		}
		
		$this->guardarDetalles($id,$id_almacen,$Conceptos,$registroNuevo);
		
        $data=$this->getById($id);   
        
		if ($registroNuevo){
			$this->gastarFolio( $id_serie, $folio_inventario );
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
				}else if($tipo_movimiento == 2){
					$stock->entrada($id_alm_ori,$id_producto,$cantidad);
				}
			
			}	

		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_movimiento = $id ";
		$this->queryDelete($sqlDelete);			
		
        return parent::delete($id);
    }
	
	public function aplicar($id){
		$params=array();
		$query="SELECT id_empresa,id_sucursal,id_almacen, fecha_inventario,
				DATE_FORMAT(now(),'%d/%m/%Y') as fecha_inventario,
				DATE_FORMAT(now(),'%H:%i:%S') as hora_inventario,				
				concepto_inventario, aplicado				
				FROM $this->useTable i
				WHERE i.id_inventario=$id";       
        $inventario=$this->query($query);
		
		if (sizeof($inventario)==0){
        	throw new Exception("Error: No se encontró un inventario con esos parámetros");
        }
		
		$id_empresa = $inventario[0]['id_empresa'];
		$id_sucursal = $inventario[0]['id_sucursal'];
		
		$params['Movimiento']['id_movimiento'] = 0;
		$params['Movimiento']['id_empresa'] = $inventario[0]['id_empresa'];
		$params['Movimiento']['id_sucursal'] = $inventario[0]['id_sucursal'];
		$params['Movimiento']['id_inventario'] = $id;
		$params['Movimiento']['fecha'] = $inventario[0]['fecha_inventario'];
		$params['Movimiento']['hora'] = $inventario[0]['hora_inventario'];
		
		$params['Movimiento']['concepto_movimiento'] = $inventario[0]['concepto_inventario'];
	
		
		$params['Movimiento']['importe'] = 0;
		$params['Movimiento']['descuento'] = 0;
		$params['Movimiento']['subtotal'] = 0;
		$params['Movimiento']['impuestos'] = 0;
		$params['Movimiento']['total'] = 0;
		$params['Movimiento']['status'] = 'A';
		
		$query="SELECT id_producto,diferencia as cantidad, 0 as costo,0 as importe,0 as descuento,0 as subtotal,0 as impuestos,0 as total
				FROM $this->detalleTable
				WHERE id_inventario=$id and diferencia > 0
				ORDER BY id_inventario_detalle";
		
		
		$detallesEntrada=$this->query($query);	//<--Lee los detalles de la tabla temporal		
		
		$detallesEntrada=$detallesEntrada;
		
		if (sizeof($detallesEntrada) > 0){
			// throw new Exception("Ramon2");
			$params['Movimiento']['id_almacen_origen'] = 0;
			$params['Movimiento']['id_almacen_destino'] = $inventario[0]['id_almacen'];
			
			$query="SELECT id_serie_eaju 
					FROM cat_parametros_ventas 
					WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A'
					Limit 1";       
			$serie_config=$this->query($query);
			
			if (sizeof($serie_config)==0){
				throw new Exception("Error: No se encontró una serie de ajuste de entrada en la configuracion de la sucursal");
			}else{
				
				$id_serie_eaju = $serie_config[0]['id_serie_eaju'];
				$query="SELECT id_serie,nombre_serie,foliosig 
					FROM cat_series 
					WHERE id_serie = $id_serie_eaju";       
				$serie=$this->query($query);
				
				if (sizeof($serie)==0){
					throw new Exception("Error: No se encontró una serie");
				}
				
			}			
			
			$id_serie = $serie[0]['id_serie'];
			$nombre_serie = $serie[0]['nombre_serie'];
			$folio_movimiento = $serie[0]['foliosig'];
			
			$params['Movimiento']['id_serie'] = $id_serie;
			$params['Movimiento']['nombre_serie'] = $nombre_serie;
			$params['Movimiento']['folio_movimiento'] = $folio_movimiento;		
			
			$params['Movimiento']['id_tipomovimiento'] = 2;
			$params['Movimiento']['tipo_movimiento'] = 1;	
			
			$params['Conceptos'] = $detallesEntrada;				
			
			$this->guardarMovimiento($params);				
        	
        }
		
		$query="SELECT id_producto,(diferencia * -1) as cantidad, 0 as costo,0 as importe,0 as descuento,0 as subtotal,0 as impuestos,0 as total
				FROM $this->detalleTable
				WHERE id_inventario=$id and diferencia < 0
				ORDER BY id_inventario_detalle";
		
		
		$detallesSalida=$this->query($query);	//<--Lee los detalles de la tabla temporal		
		
		if (sizeof($detallesSalida) > 0){
			// throw new Exception("Ramon2");
			$params['Movimiento']['id_almacen_origen'] = $inventario[0]['id_almacen'];
			$params['Movimiento']['id_almacen_destino'] = 0;
			/*
			$query="SELECT id_serie,nombre_serie,foliosig 
					FROM cat_series 
					WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and tipo_serie = 8 and status = 'A'
					order by nombre_serie Limit 1";       
			$serie=$this->query($query);
			
			if (sizeof($serie)==0){
				throw new Exception("Error: No se encontró una serie");
			}
			*/
			
			$query="SELECT id_serie_saju 
					FROM cat_parametros_ventas 
					WHERE id_empresa = $id_empresa and id_sucursal = $id_sucursal and status = 'A'
					Limit 1";       
			$serie_config=$this->query($query);
			
			if (sizeof($serie_config)==0){
				throw new Exception("Error: No se encontró una serie de ajuste de salida en la configuracion de la sucursal");
			}else{
				
				$id_serie_saju = $serie_config[0]['id_serie_saju'];
				$query="SELECT id_serie,nombre_serie,foliosig 
					FROM cat_series 
					WHERE id_serie = $id_serie_saju";       
				$serie=$this->query($query);
				
				if (sizeof($serie)==0){
					throw new Exception("Error: No se encontró una serie");
				}
				
			}
			
			$id_serie = $serie[0]['id_serie'];
			$nombre_serie = $serie[0]['nombre_serie'];
			$folio_movimiento = $serie[0]['foliosig'];
			
			$params['Movimiento']['id_serie'] = $id_serie;
			$params['Movimiento']['nombre_serie'] = $nombre_serie;
			$params['Movimiento']['folio_movimiento'] = $folio_movimiento;		
			
			$params['Movimiento']['id_tipomovimiento'] = 3;
			$params['Movimiento']['tipo_movimiento'] = 2;	
			
			$params['Conceptos'] = $detallesSalida;	
			
			
			// $resp = $movimientoModel->guardar($params);
			
			$this->guardarMovimiento($params);	
			
			
			
        	
        }
	
		$sql="UPDATE inventarios set aplicado=1,fecha_aplica = now() WHERE id_inventario=$id";		
		
		
		$this->update($sql);
		
		$data=$this->getById($id);   
        
		return $data;
			
    }

	function getById($id){
    	 $query="SELECT i.id_inventario,i.id_almacen,a.nombre_almacen, DATE_FORMAT(i.fecha_inventario,'%d/%m/%Y %H:%i:%S') as fecha_inventario,i.folio_inventario,i.id_serie,i.serie_inventario,i.concepto_inventario,i.aplicado				
				FROM $this->useTable i
				left join cat_almacenes a on a.id_almacen = i.id_almacen				
				WHERE i.id_inventario=$id";       
        $inventario=$this->query($query);
        
        if (sizeof($inventario)==0){
        	throw new Exception("Error: No se encontró un inventario con esos parámetros");
        }
			
		$query="SELECT d.id_inventario_detalle,d.id_producto,p.descripcion,d.conteo,d.stock,d.diferencia
				FROM $this->detalleTable d
				inner join cat_productos p on p.id_producto = d.id_producto
				WHERE d.id_inventario=$id ORDER BY d.id_inventario_detalle";
		
		
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptos=$detalles;
		
		$datos=array();
		$datos['Inventario']=$inventario[0];   
		$datos['Detalles']=$detalles;
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');		
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha_inventario FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_inventario'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		$arrResult[0]['id_almacen'] = $id_almacen;		
		
        return $arrResult[0];		
	}
	
	private function guardarDetalles($id,$id_almacen,$conceptos,$registroNuevo){
				
		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_inventario = $id ";
		$this->queryDelete($sqlDelete);
		
		foreach ($conceptos  as $concepto) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$id_producto = $concepto['id_producto'];
			$conteo = $concepto['conteo'];
			$stock = $concepto['stock'];
			$diferencia = $concepto['diferencia'];
			
			try{
				$queryInsert="INSERT INTO $this->detalleTable SET id_inventario=$id,id_producto='$id_producto',conteo='$conteo', stock='$stock',
            	diferencia='$diferencia';";
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
	
	public function guardarMovimiento($params){
				
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$Movimiento = $params['Movimiento'];
				
		$Conceptos=$params['Conceptos'];
		
		$numConceptos=sizeof($Conceptos);
		
		if($numConceptos == 0)
			Throw new Exception("No se recibieron detalles.");
				
		$id_empresa = $Movimiento['id_empresa'];		
		$id_sucursal = $Movimiento['id_sucursal'];
		$id_inventario = $Movimiento['id_inventario'];		 
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
		
		
		$importe = $Movimiento['importe'];
		$descuento = $Movimiento['descuento'];
		$subtotal = $Movimiento['subtotal'];
		$impuestos = $Movimiento['impuestos'];
		$total = $Movimiento['total'];
		$status = $Movimiento['status'];
	
		$datetime="$fecha $hora";
		
		$fecha_movimiento=date('Y-m-d H:i:s',strtotime($datetime));
		
        $query="INSERT INTO $this->useTableM SET ";
        $query.="usercreador=$IDUsu";    //LOG
        $query.=",fechacreador=now()";
          
        $registroNuevo=true;
		$where='';
                
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
		
		if (is_numeric($id_inventario)){	
			$query.=",id_inventario='".$id_inventario."'";
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
		$query.=",fecha_movimiento=now()";
		$query.=",concepto_movimiento='".$this->EscComillas($concepto_movimiento)."'";
		
		$query.=",importe='".$this->EscComillas($importe)."'";
		$query.=",descuento='".$this->EscComillas($descuento)."'";
		$query.=",subtotal='".$this->EscComillas($subtotal)."'";
		$query.=",impuestos='".$this->EscComillas($impuestos)."'";
		$query.=",total='".$this->EscComillas($total)."'";
		$query.=",status='".$this->EscComillas($status)."'";
	

        $query=$query.$where;
		 
		$id= $this->insert($query); 			
			
		
		
		
		$this->guardarDetallesMovimiento($id,$id_almacen_origen,$id_almacen_destino,$Conceptos,$tipo_movimiento,$registroNuevo);
		
        
        
		if ($registroNuevo){
			$this->gastarFolio( $id_serie, $folio_movimiento );
		}
		// throw new Exception($data['id_movimiento']);
		
		return $data;
                     

    }	
	
	private function guardarDetallesMovimiento($id,$id_almacen_origen,$id_almacen_destino,$Conceptos,$tipo_movimiento,$registroNuevo){
		$stock = new Stock();
		//$this->detalleTableM = '';
		foreach ($Conceptos  as $concepto) {
			// throw new Exception('ramon4');
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
				
				$queryInsert="INSERT INTO $this->detalleTableM SET id_movimiento=$id,id_producto='$id_producto',cantidad='$cantidad', costo='$costo',
            	importe='$importe',descuento='$descuento',subtotal='$subtotal',impuestos='$impuestos',total='$total';";
					
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
		
}
?>
