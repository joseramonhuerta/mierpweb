<?php
class ListaPrecioModel extends Model{
    var $useTable = 'cat_listaprecios';
    var $detalleTable="cat_listaprecios_detalles";
    var $name='ListaPrecio';
    var $primaryKey = 'id_listaprecio';
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

        $query = "SELECT id_listaprecio,descripcion,status FROM $this->useTable
				  $filtroSql ORDER BY descripcion limit $start,$limit ;";
		// throw new Exception($query);
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }

    public function guardar($params){
				
    	$registroNuevo=false;
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 		
		$ListaPrecio = $params['ListaPrecio'];
				
		$Conceptos=json_decode( stripslashes($params['Conceptos']), true);
		
		$numConceptos=sizeof($Conceptos);
		// Throw new Exception($numConceptos);
		if($numConceptos == 0)
			Throw new Exception("No se recibieron detalles.");
			
		$id_listaprecio = $ListaPrecio['id_listaprecio'];
		$descripcion = $ListaPrecio['descripcion'];		
		$status = $ListaPrecio['status'];		
		
        if($id_listaprecio > 0){
			$query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$id_listaprecio;
        }else{  //INSERT
            $query="INSERT INTO $this->useTable SET ";
            $query.="usercreador=$IDUsu";    //LOG
            $query.=",fechacreador=now()";
          
            $registroNuevo=true;
			$where='';
        }        
	
		$query.=",descripcion='".$this->EscComillas($descripcion)."'";		
		$query.=",status='".$this->EscComillas($status)."'";	

        $query=$query.$where;
		 
		if ($registroNuevo){            
			$id= $this->insert($query); 			
			
		}else{		
			$result=$this->update($query);               
			$id=$id_listaprecio;
		}		
		
		$this->guardarDetalles($id,$Conceptos,$registroNuevo);
		
        $data=$this->getById($id);   
        
		return $data;
                     

    }

    private function guardarDetalles($id,$conceptos,$registroNuevo){	
		if(!$registroNuevo){
			
			$sql="SELECT ld.id_listaprecio_detalle, ld.id_listaprecio, ld.id_producto, ld.precio, ld.valor_puntos 
                    FROM cat_listaprecios_detalles ld
					INNER JOIN cat_listaprecios l on l.id_listaprecio = ld.id_listaprecio
					WHERE ld.id_listaprecio = $id";
					
			$detalles=$this->select($sql);
			
			if (sizeof($detalles)==0){
				throw new Exception("Error: No se encontraron detalles");
			}
			
		}
		
		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_listaprecio = $id ";
		$this->queryDelete($sqlDelete);
		
		foreach ($conceptos  as $concepto) {
			$id_producto = $concepto['id_producto'];
			$precio = $concepto['precio'];
			$valor_puntos = $concepto['valor_puntos'];
			
			try{
				$queryInsert="INSERT INTO $this->detalleTable SET id_listaprecio=$id,id_producto='$id_producto',precio='$precio', valor_puntos='$valor_puntos';";
			
				$IDDetalle= $this->insert($queryInsert); 				          
			}catch(Exception $e){            
				return false;
			}  
			
			
		}
	}

    function getById($id){
        $query="SELECT id_listaprecio, descripcion
               FROM $this->useTable              
               WHERE id_listaprecio=$id";       
       $listaprecio=$this->query($query);
       
       if (sizeof($listaprecio)==0){
           throw new Exception("Error: No se encontró una lista de precio con esos parámetros");
       }
           
       $query="SELECT lp.id_listaprecio_detalle, lp.id_listaprecio, lp.id_producto, lp.precio, p.descripcion,lp.valor_puntos
               FROM $this->detalleTable lp
               inner join cat_productos p on p.id_producto = lp.id_producto               
               WHERE lp.id_listaprecio=$id ORDER BY lp.id_listaprecio_detalle";
       
       
       $detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

       $this->conceptos=$detalles;
       
       $datos=array();
       $datos['ListaPrecio']=$listaprecio[0];   
       $datos['Detalles']=$detalles;
       return $datos;
      
    }

    public function getInitialInfo(){	
		$date = new DateTime();
		$fecha_hora= $date->format('Y-m-d H:i:s');	
		$query="SELECT DATE_FORMAT('$fecha_hora','%d/%m/%Y %H:%i:%S') as fecha_corte";		
		$arrResult=$this->query($query);
		
		$arrResult[0]['id_listaprecio'] = 0;
			
        return $arrResult[0];		
	}
    
    public function delete($id){
        //buscar si la lista de precios se encuentra ligada a un cliente, si  si no eliminar, si no eliminar
        $sql="SELECT id_cliente FROM cat_clientes					
					WHERE id_listaprecio = $id";
					
        $clienteslistas=$this->select($sql);
        
        if (sizeof($clienteslistas)>0){
            throw new Exception("Error: No se puede eliminar la lista porque esta en uso");
        }


		$sqlDelete="DELETE FROM  $this->detalleTable WHERE id_listaprecio = $id ";
		$this->queryDelete($sqlDelete);			
		
        return parent::delete($id);
    }
    
    public function actualizarPrecios($id){
        $query = "CALL spActualizaListaPrecios($id);";
		$this->query($query);
        $affected=mysql_affected_rows();		

        return $affected;
    }

}
?>