<?php

class ChecadaModel extends Model{
    var $useTable = 'checadas';
	var $name='Checada';
    var $primaryKey = 'id_checada';
	var $specific = true;
    var $camposAfiltrar = array('');
		
	public function guardar($params){
		// throw new Exception('No existe turno');
    	
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$Checada = $params['Checada'];
		
		
					
		
		
		$codigo_empleado = $Checada['codigo_empleado'];		
		
		
		$query = "SELECT COUNT(id_empleado) numEmpleados FROM cat_empleados WHERE codigo_empleado = '$codigo_empleado' AND status = 'A';";
		
		$arrEmpleado=$this->select($query);
		if ( intval($arrEmpleado[0]['numEmpleados'])>0 ){
			$query = " SELECT id_empleado FROM cat_empleados";
			$query.= " WHERE codigo_empleado = '$codigo_empleado' AND status = 'A';";
			$res = $this->query($query);
			
			$id_empleado = $res[0]['id_empleado'];	
	
		}else{
			throw new Exception('No existe el empleado');
		}
			
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');		
		
        $query="INSERT INTO $this->useTable SET ";
        $query.="usercreador=$IDUsu";    //LOG
        $query.=",fechacreador=now()";
		
		if (is_numeric($id_empleado)){	
			$query.=",id_empleado='".$id_empleado."'";
		}		
		 
		$query.=",fecha_hora='".$fecha_hora."'";
		
		$query.=",status='A'";
	
	
        $query=$query;
		 
		
		try{
            $id= $this->insert($query);		
			
            $data = $this->getById($id);
            return $data['Checada'];
        }catch(Exception $e){            
            return false;
        }  

    }
	
	function getById($id){
    	 $query="SELECT v.id_empleado, concat('Bienvenido: ',a.nombre_empleado) as mensaje, DATE_FORMAT(fecha_hora,'%d/%m/%Y %H:%i:%S') as fecha_hora
				FROM $this->useTable v
				inner join cat_empleados a on a.id_empleado = v.id_empleado				
				WHERE v.id_checada=$id";       
        $checada=$this->query($query);
        
        if (sizeof($checada)==0){
        	throw new Exception("Error: No se encontró una checada con esos parametros");
        }		
		
		$datos=array();
		$datos['Checada']=$checada[0];   
		
		return $datos;
	   
    }
	
	
}
?>
