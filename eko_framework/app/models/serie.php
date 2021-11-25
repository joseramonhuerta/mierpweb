<?php
class SerieModel extends Model{
    var $useTable = 'cat_series';
    var $name='Serie';
    var $primaryKey = 'id_serie';
    var $specific = true;
    var $camposAfiltrar = array('nombre_serie');
		
	function readAll($start, $limit, $filtro, $filtroStatus, $id_empresa,$id_sucursal ) {
        
        if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);
        } else {
            $filtroSql = '';
        }
		
		 if (strlen($filtroSql) > 0) {
				if ($filtroStatus=='A')
					$filtroSql.=" AND status='A' AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal ";
				else if ($filtroStatus=='I')
					$filtroSql.=" AND status='I' AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal ";
				else 
					$filtroSql.=" AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal ";
            }else {
				if ($filtroStatus=='A')
					$filtroSql.="WHERE status='A' AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal ";
				else if ($filtroStatus=='I')
					$filtroSql.="WHERE status='I' AND id_empresa=$id_empresa AND id_sucursal=$id_sucursal ";
				else
				$filtroSql.="WHERE id_empresa=$id_empresa AND id_sucursal=$id_sucursal ";
            }

        
      

        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable
        $filtroSql";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT id_serie,nombre_serie,folioinicio,foliofin,foliosig,
				case tipo_serie when 0 then 'FACTURA'
				when 1 then 'NOTAS DE CREDITO'
				when 2 then 'COMPRAS'
				when 3 then 'VENTAS'
				when 4 then 'INVENTARIO FISICO'
				when 5 then 'NOMINA'
				when 6 then 'REMISIONES'
				when 7 then 'ENTRADAS INVENTARIO'
				when 8 then 'SALIDAS INVENTARIO'
				when 9 then 'ABONOS'
                when 10 then 'MOVIMIENTOS BANCOS'	
				when 11 then 'GASTOS'	
				  end AS tipo_serie,status FROM $this->useTable
				  $filtroSql ORDER BY nombre_serie limit $start,$limit ;";
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
    
	public function guardar($datos){
        //----------------------
    	// if(empty($datos['nombre_fiscal'])){
        	// throw new Exception("Debe especificar un nombre para el cliente");	
        // }
        //---------------------
    	$registroNuevo=false;
		 $IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 
		 // throw new Exception($datos['tipo_cliente']);
		 
			
        
          
        if (@$datos[$this->primaryKey]){//UPDATE
            $query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$datos[$this->primaryKey];
        }else{  //INSERT
            $query="INSERT INTO $this->useTable SET ";
            $query.="usercreador=$IDUsu";    //LOG
            $query.=",fechacreador=now()";
          
            $registroNuevo=true;
			$where='';
        }
		
        $query.=",id_empresa='".$datos['id_empresa']."'";
		$query.=",id_sucursal='".$datos['id_sucursal']."'";
        $query.=",nombre_serie='".$this->EscComillas(strtoupper($datos['nombre_serie']))."'";
        $query.=",folioinicio='".$this->EscComillas($datos['folioinicio'])."'";
		$query.=",foliofin='".$this->EscComillas($datos['foliofin'])."'";
		
		$query.=",foliosig='".$this->EscComillas($datos['folioinicio'])."'";
	
        $query.=",tipo_serie='".$this->EscComillas($datos['tipo_serie'])."'";
        $query.=",status='".$this->EscComillas($datos['status'])."'";
		
        /*LOG dEL MOVIMIENTO*/

        $query=$query.$where;
		// throw new Exception($query);
		
        try{
            if ($registroNuevo) {
                $id = $this->insert($query);
            } else {
                $this->update($query);
                $id = $datos[$this->primaryKey];
            }
            $this->id = $id;
            $data = $this->getById($id);
            return $data['Serie'];
        }catch(Exception $e){            
            return false;
        }                    

    }
	
	public function delete($id){
        return parent::delete($id);
    }
	
	function getById($id){
    	$query="SELECT id_serie,nombre_serie,folioinicio,foliofin,tipo_serie,status
		FROM cat_series
		WHERE id_serie=$id ";
    	$arrResult=$this->query($query);
    	
    	return array('Serie'=>$arrResult[0]);
    }

}
?>
