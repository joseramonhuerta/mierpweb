<?php
class HorarioModel extends Model{
    var $useTable = 'cat_horarios';
    var $name='Horario';
    var $primaryKey = 'id_horario';
    var $specific = true;
    var $camposAfiltrar = array('hora_inicio','hora_fin');
		
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

        $query = "SELECT id_horario, concat(DATE_FORMAT(hora_inicio,'%H:%i:%S'),'-',DATE_FORMAT(hora_fin,'%H:%i:%S')) as descripcion_horario,status FROM $this->useTable
				  $filtroSql ORDER BY descripcion_horario limit $start,$limit ;";
		// throw new Exception($query);
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }
    
	public function guardar($datos){
		
		$Horario = $datos['Horario'];					
			
		$id_horario = $Horario['id_horario'];
		$fecha = $Horario['fecha'];
		$hora_inicio = $Horario['hora_inicio'];
		$hora_fin = $Horario['hora_fin'];
		$datetimeI = "$fecha $hora_inicio";
		$datetimeF = "$fecha $hora_fin";	
		$fechaI =date('Y-m-d H:i:s',strtotime($datetimeI));
		$fechaF =date('Y-m-d H:i:s',strtotime($datetimeF));
		$status = $Horario['status'];	
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];  

		//throw new Exception($fechaI);
		 
		if ($id_horario > 0){//UPDATE
            $query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_horario;
        }else{  //INSERT
            $query="INSERT INTO $this->useTable SET ";
            $query.="usercreador=$IDUsu";    //LOG
            $query.=",fechacreador=now()";
          
            $registroNuevo=true;
			$where='';
        }		
		
		$query.=",hora_inicio='".$fechaI."'";
		$query.=",hora_fin='".$fechaF."'";
		$query.=",status='".$this->EscComillas($status)."'";
		
        $query=$query.$where;
		
		try{
            if ($registroNuevo) {
                $id = $this->insert($query);
            } else {
                $this->update($query);
                $id = $id_horario;
            }
            $this->id = $id;
            $data = $this->getById($id);
            $response['success']    = true;
			$response['msg']       = array('titulo'=>"Horarios",'mensaje'=>"Horario guardado correctamente");
			$response['data']    = $data;			
        }catch(Exception $e){            
            $response['success']    = false;
			$response['msg']       = $e->getMessage();
        }  

		return $response;	

    }
	
	public function delete($id){
        return parent::delete($id);
    }
	
	function getById($id){
    	$query="SELECT id_horario,DATE_FORMAT(hora_inicio,'%d/%m/%Y %H:%i:%S') as hora_inicio,DATE_FORMAT(hora_fin,'%d/%m/%Y %H:%i:%S') as hora_fin,
		concat(DATE_FORMAT(hora_inicio,'%d/%m/%Y %H:%i:%S'),'-',DATE_FORMAT(hora_fin,'%d/%m/%Y %H:%i:%S')) as descripcion_horario,status
		FROM cat_horarios
		WHERE id_horario=$id ";
    	$arrResult=$this->query($query);    	
    	
		$datos=array();
		$datos['Horario']=$arrResult[0];   
		
		return $datos;
    }
	
	public function getInitialInfo($id_empresa,$id_sucursal){
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');		
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha FROM cat_empresas WHERE id_empresa=$id_empresa";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_horario'] = 0;
			
		
        return $arrResult[0];		
	}
}
?>
