<?php
class LineaModel extends Model{
    var $useTable = 'cat_lineas';
    var $name='Linea';
    var $primaryKey = 'id_linea';
    var $specific = true;
    var $camposAfiltrar = array('nombre_linea');


    function readAll($start, $limit, $filtro, $filtroStatus) {
        
        if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);
        } else {
            $filtroSql = '';
        }
		
		 if (strlen($filtroSql) > 0) {
				if ($filtroStatus=='A')
					$filtroSql.=" AND status='A' ";
				if ($filtroStatus=='I')
                $filtroSql.=" AND status='I' ";				
            }else {
				if ($filtroStatus=='A')
					$filtroSql.="WHERE status='A' ";
				if ($filtroStatus=='I')
                $filtroSql.="WHERE status='I' ";
				
            }

        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable
        $filtroSql";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT id_linea,nombre_linea,status FROM $this->useTable
				  $filtroSql ORDER BY nombre_linea limit $start,$limit ;";
		// throw new Exception($query);
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
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
		
       
        $query.=",nombre_linea='".$this->EscComillas(strtoupper($datos['nombre_linea']))."'";
        $query.=",status='".$this->EscComillas($datos['status'])."'";
		
        $query=$query.$where;
		
		try{
            if ($registroNuevo) {
                $id = $this->insert($query);
            } else {
                $this->update($query);
                $id = $datos[$this->primaryKey];
            }
            $this->id = $id;
            $data = $this->getById($id);
            return $data['Linea'];
        }catch(Exception $e){            
            return false;
        }                    

    }
	
	public function delete($id){
        return parent::delete($id);
    }
	
	function getById($id){
    	$query="SELECT id_linea,nombre_linea,status
		FROM cat_lineas
		WHERE id_linea=$id ";
    	$arrResult=$this->query($query);
    	
    	return array('Linea'=>$arrResult[0]);
    }


}
?>