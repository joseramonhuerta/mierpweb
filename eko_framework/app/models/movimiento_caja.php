<?php
require ('eko_framework/app/models/turno.php');
class MovimientoCaja extends Model{
    var $useTable = 'movimientos_caja';
	 var $name='MovimientoCaja';
    var $primaryKey = 'id_movimiento_caja';
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
            $filtroSql.=" AND m.id_empresa = $IDEmpresa AND m.id_sucursal = $IDSucursal ";
        } else {
           $filtroSql = "WHERE m.id_empresa = $IDEmpresa AND m.id_sucursal = $IDSucursal ";
        }
		
		if ($filtroStatus=='A')
			$filtroSql.=" AND m.status='A' ";
		if ($filtroStatus=='I')
            $filtroSql.=" AND m.status='I' ";
		
		$filtroSql.=    "AND (fecha BETWEEN '$fechaInicio' AND '$fechaFin' )";

		$query = "select count($this->primaryKey) as totalrows  FROM $this->useTable m
        $filtroSql";
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT m.id_movimiento_caja,m.concepto,
        			DATE_FORMAT(m.fecha,'%d/%m/%Y') as fecha,
					m.status,total,case tipo when 1 then 'DEPOSITO' when 2 then 'RETIRO' when 3 then 'APARTADO' when 4 then 'DEUDORES DIVERSOS' else '' end tipo        		
        		 FROM $this->useTable m        			
				  $filtroSql ORDER BY m.fecha limit $start,$limit ;";

	    $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }
	
	 function getById($id){
    	 $query="SELECT m.id_movimiento_caja,m.concepto,DATE_FORMAT(m.fecha,'%d/%m/%Y %H:%i:%S') as fecha,
				m.total,m.tipo,m.status
				FROM $this->useTable m
				WHERE m.id_movimiento_caja=$id";       
        $movimientoCaja=$this->query($query);
        
        if (sizeof($movimientoCaja)==0){
        	throw new Exception("Error: No se encontró un movimiento de caja con esos parámetros");
        }
			
		$datos=array();
		$datos['MovimientoCaja']=$movimientoCaja[0];   
		return $datos;
	   
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal,$id_almacen){	
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_movimiento_caja'] = 0;
		$arrResult[0]['id_empresa'] = $id_empresa;
		$arrResult[0]['id_sucursal'] = $id_sucursal;
		
        return $arrResult[0];		
	}
	
	public function guardar($params){
		
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$MovimientoCaja = $params['MovimientoCaja'];
				
		
		
		//$numConceptos=sizeof($Detalles);
		// Throw new Exception($numConceptos);
		// if($numConceptos == 0)
			// Throw new Exception("No se recibieron detalles.");
			
		
		$id_empresa = $MovimientoCaja['id_empresa'];		
		$id_sucursal = $MovimientoCaja['id_sucursal'];
		
		
		
		$id_movimiento_caja = $MovimientoCaja['id_movimiento_caja'];
		$fecha = $MovimientoCaja['fecha'];
		$hora = $MovimientoCaja['hora'];
		$concepto = $MovimientoCaja['concepto'];
		$total = $MovimientoCaja['total'];
		$status = $MovimientoCaja['status'];
		$id_tipo = $MovimientoCaja['tipo'];
	
		$datetime="$fecha $hora";
		
		$fecha_mov=date('Y-m-d H:i:s',strtotime($datetime));
		
        if($id_movimiento_caja > 0){
			$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_movimiento_caja;
        }else{  //INSERT
			$turnoModel = new Turno();		
			$turno = $turnoModel->getTurno($id_empresa,$id_sucursal);
		
			if(!$turno){
				throw new Exception('No existe un turno abierto, verifique.');
			}
			$id_turno = $turno['id_turno'];	
			
			
			
			$sql="SELECT max(consecutivo) + 1 as consecutivo FROM $this->useTable
                WHERE id_empresa='$id_empresa' AND id_sucursal='$id_sucursal'";
			$consecutivo = 1;
			
			$MovCaja=$this->query($sql);
			if($MovCaja)
				$consecutivo = $MovCaja[0]['consecutivo'];
			
			
			
			
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
		
		if (is_numeric($id_tipo)){	
			$query.=",tipo='".$id_tipo."'";
		}
		
		$query.=",fecha='".$fecha_mov."'";
		$query.=",concepto='".$this->EscComillas($concepto)."'";
		$query.=",total='".$this->EscComillas($total)."'";
		$query.=",status='".$this->EscComillas($status)."'";
	

        $query=$query.$where;
		// Throw new Exception($query); 
		if ($registroNuevo){
			if (is_numeric($consecutivo)){	
			$query.=",consecutivo='".$consecutivo."'";
			}	
			
			if (is_numeric($id_turno)){	
			$query.=",id_turno='".$id_turno."'";
			}
			
			$id= $this->insert($query); 			
			
		}else{		
			$result=$this->update($query);               
			$id=$id_movimiento_caja;
		}
		
		$data=$this->getById($id);   
        
		return $data;
                     

    }
	
	public function delete($id){
		return parent::delete($id);
    }	
}
?>