<?php
class EmpleadoModel extends Model{
    var $useTable = 'cat_empleados';
    var $name='Empleado';
    var $primaryKey = 'id_empleado';
    var $specific = true;
    var $camposAfiltrar = array('nombre_empleado', 'codigo_empleado');
		
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

        $query = "SELECT id_empleado, codigo_empleado,nombre_empleado, celular,status FROM $this->useTable
				  $filtroSql ORDER BY nombre_empleado limit $start,$limit ;";
		// throw new Exception($query);
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }
    
	public function guardar($datos){
       
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 
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
       
		$query.=",codigo_empleado='".$this->EscComillas(strtoupper($datos['codigo_empleado']))."'";
        $query.=",nombre_empleado='".$this->EscComillas(strtoupper($datos['nombre_empleado']))."'";
		$query.=",celular='".$this->EscComillas(strtoupper($datos['celular']))."'";
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
            return $data['Empleado'];
        }catch(Exception $e){            
            return false;
        }                    

    }
	
	public function delete($id){
        return parent::delete($id);
    }
	
	function getById($id){
    	$query="SELECT id_empleado,codigo_empleado,nombre_empleado,celular,status
		FROM cat_empleados
		WHERE id_empleado=$id ";
    	$arrResult=$this->query($query);
    	
    	return array('Empleado'=>$arrResult[0]);
    }

}
?>
