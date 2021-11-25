<?php
class ConceptoModel extends Model{
    var $useTable = 'cat_conceptos';
    var $name='Concepto';
    var $primaryKey = 'id_concepto';
    var $specific = true;
    var $camposAfiltrar = array('descripcion');
		
	function readAll($start, $limit, $filtro, $filtroStatus) {
        
        if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);
        } else {
            $filtroSql = '';
        }
		
		 if (strlen($filtroSql) > 0) {
				if ($filtroStatus=='A')
					$filtroSql.=" AND status='A' ";
				else if ($filtroStatus=='I')
					$filtroSql.=" AND status='I' ";
				
            }else {
				if ($filtroStatus=='A')
					$filtroSql.="WHERE status='A' ";
				else if ($filtroStatus=='I')
					$filtroSql.="WHERE status='I' ";
				
            }

        
      

        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable
        $filtroSql";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT id_concepto,descripcion,
				case tipo when 1 then 'INGRESOS'
				when 2 then 'EGRESOS'
				end AS tipo,status FROM $this->useTable
				  $filtroSql ORDER BY descripcion limit $start,$limit ;";
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
		
        $query.=",descripcion='".$this->EscComillas(strtoupper($datos['descripcion']))."'";
        $query.=",tipo='".$this->EscComillas($datos['tipo'])."'";
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
            return $data['Concepto'];
        }catch(Exception $e){            
            return false;
        }                    

    }
	
	public function delete($id){
        return parent::delete($id);
    }
	
	function getById($id){
    	$query="SELECT id_concepto,descripcion,tipo,status
		FROM cat_conceptos
		WHERE id_concepto=$id ";
    	$arrResult=$this->query($query);
    	
    	return array('Concepto'=>$arrResult[0]);
    }

}
?>
