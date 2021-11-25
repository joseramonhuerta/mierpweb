<?php


class MovimientoBancoModel extends Model{
    var $useTable = 'movimientos_bancos';
	//var $detalleTable="inventarios_detalles";
	//var $useTableM = 'movimientos_almacen';
	//var $detalleTableM="movimientos_almacen_detalles";
    var $name='MovimientoBanco';
    var $primaryKey = 'id_movimiento_banco';
    var $specific = true;
    var $camposAfiltrar = array('observaciones');
		
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
		$Origen = $params['origen'];
		
		$filtroSql = $this->filtroToSQL( $filtro ); 
		 
		if (strlen($filtroSql) > 0) {
            $filtroSql.=" AND i.id_empresa = $IDEmpresa AND i.id_sucursal = $IDSucursal AND origen = $Origen ";
        } else {
           $filtroSql = "WHERE i.id_empresa = $IDEmpresa AND i.id_sucursal = $IDSucursal AND origen = $Origen ";
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
		
		$filtroSql.=    "AND (fecha BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable i
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT i.id_movimiento_banco,a.descripcion as concepto,i.observaciones,concat(serie,' - ',folio) as serie_folio,
        			DATE_FORMAT(i.fecha,'%d/%m/%Y') as fecha,i.importe,
        		 i.status,i.tipo_movimiento,case i.tipo_origen when 1 then 'EFECTIVO' when 2 then 'BANCOS' else '' end as tipo_origen, IFNULL(ch.descripcion,'') as chequera 
        		 FROM $this->useTable i
        			inner join cat_conceptos a on a.id_concepto = i.id_concepto
					left join cat_chequeras ch on ch.id_chequera = i.id_chequera   					
				  $filtroSql ORDER BY i.fecha limit $start,$limit ;";

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
		 		
		$MovimientoBanco = $params['MovimientoBanco'];
		$origen = $MovimientoBanco['origen'];		
		$id_empresa = $MovimientoBanco['id_empresa'];		
		$id_sucursal = $MovimientoBanco['id_sucursal'];
		$id_movimiento_banco = $MovimientoBanco['id_movimiento_banco'];
		$id_serie = $MovimientoBanco['id_serie'];
		$nombre_serie = $MovimientoBanco['nombre_serie'];
		$folio = $MovimientoBanco['folio'];
		$observaciones = $MovimientoBanco['observaciones'];
		$fecha = $MovimientoBanco['fecha'];
		$hora = $MovimientoBanco['hora'];
		$tipo_movimiento = $MovimientoBanco['tipo_movimiento'];
		$id_concepto = $MovimientoBanco['id_concepto'];
		$tipo_origen = $MovimientoBanco['tipo_origen'];
		$importe = $MovimientoBanco['importe'];
		$status = $MovimientoBanco['status'];
		
		if($tipo_origen == 2)
			$id_chequera = $MovimientoBanco['id_chequera'];
		
		if ($tipo_origen == 2 && !$id_chequera){
				throw new Exception("Error: Debe seleccionar el origen");
			}
		
		$datetime="$fecha $hora";
		
		$fecha=date('Y-m-d H:i:s',strtotime($datetime));
		
        if($id_movimiento_banco > 0){
			$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_movimiento_banco;
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
		
		if (is_numeric($folio)){	
			$query.=",folio='".$folio."'";
		}
		
		if (is_numeric($id_concepto)){	
			$query.=",id_concepto='".$id_concepto."'";
		}
		
		if (is_numeric($id_chequera)){	
			$query.=",id_chequera='".$id_chequera."'";
		}else{
			$query.=",id_chequera=NULL";
		}
		
		if (is_numeric($tipo_movimiento)){	
			$query.=",tipo_movimiento='".$tipo_movimiento."'";
		}
		
		if (is_numeric($tipo_origen)){	
			$query.=",tipo_origen='".$tipo_origen."'";
		}
		
		$query.=",origen='".$origen."'";	

		$query.=",serie='".$this->EscComillas($nombre_serie)."'";
		$query.=",fecha='".$fecha."'";
		$query.=",observaciones='".$this->EscComillas($observaciones)."'";
		$query.=",importe='".$this->EscComillas($importe)."'";		
		$query.=",status='".$this->EscComillas($status)."'";	

        $query=$query.$where;
		 
		if ($registroNuevo){            
			$id= $this->insert($query); 			
			
		}else{		
			$result=$this->update($query);               
			$id=$id_movimiento_banco;
		}
		
		$data=$this->getById($id);   
        
		if ($registroNuevo){
			$this->gastarFolio( $id_serie, $folio );
		}
		// throw new Exception($data['id_movimiento']);
		
		return $data;
                     

    }
	
	public function delete($id){
		return parent::delete($id);
    }
	
	function getById($id){
	$query="SELECT i.id_movimiento_banco,DATE_FORMAT(i.fecha,'%d/%m/%Y %H:%i:%S') as fecha,i.folio,i.observaciones,i.id_serie,i.serie,i.id_concepto,i.id_chequera,i.tipo_movimiento,i.tipo_origen,i.importe,a.descripcion as concepto,b.descripcion	as chequera,
	case i.tipo_movimiento	when 1 then 'INGRESO' when 2 then 'EGRESO' end nombre_movimiento
				FROM $this->useTable i
				inner join cat_conceptos a on a.id_concepto = i.id_concepto		
				left join cat_chequeras b on b.id_chequera = i.id_chequera			
				WHERE i.id_movimiento_banco=$id";       
        $movimientobanco=$this->query($query);
        
        if (sizeof($movimientobanco)==0){
        	throw new Exception("Error: No se encontró un movimiento de banco con esos parámetros");
        }
			
		$datos=array();
		$datos['MovimientoBanco']=$movimientobanco[0];   
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal){	
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_movimiento_banco'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
				
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
	
}
?>
