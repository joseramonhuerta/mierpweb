<?php
class ClienteModel extends Model{
    var $useTable = 'cat_clientes';
    var $name='Cliente';
    var $primaryKey = 'id_cliente';
    var $specific = true;
    var $camposAfiltrar = array('nombre_fiscal','nombre_contacto','email_contacto','telefono_contacto','celular_contacto','rfc_cliente');
	 
    function readAll($start, $limit, $filtro, $filtroStatus) {        
        if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);
        } else {
            $filtroSql = '';
        }
		
		 if (strlen($filtroSql) > 0) {
            if ($filtroStatus=='A')
            $filtroSql.=" AND c.status='A' ";
            if ($filtroStatus=='I')
            $filtroSql.=" AND c.status='I' ";
        }else {
            if ($filtroStatus=='A')
            $filtroSql.="WHERE c.status='A' ";
            if ($filtroStatus=='I')
            $filtroSql.="WHERE c.status='I' ";
        }      

        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable c
        $filtroSql";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT c.id_cliente,c.nombre_fiscal,c.nombre_comercial,c.estilista,c.nombre_contacto,c.email_contacto,c.telefono_contacto,c.celular_contacto,c.tipo_cliente,c.rfc_cliente,c.status,cc.nombre_categoria FROM $this->useTable c
                  LEFT JOIN cat_clientes_categorias cc ON cc.id_cliente_categoria = c.id_cliente_categoria  
                $filtroSql ORDER BY nombre_fiscal limit $start,$limit ;";

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
		 
		switch($datos['tipo_cliente']){
				case 'F':
				$patron="/^[a-zA-Z0-9&]{4}(\d{6})([A-Za-z0-9]{3})+$/";	
				$ayuda='Revise el formato del RFC  para personas fisicas: <br/> <br/>';
				$ayuda.="4 caracteres alfanuméricos + 6 digitos (fecha) + homoclave";
				$ayuda.='<br/>Ejemplo: <label style="font-weight:bold;">FISI010101HOM</label>';
				break;
			case 'M':
				$patron="/^[a-zA-Z0-9&]{3}(\d{6})([A-Za-z0-9]{3})+$/";	
				$ayuda='Revise el formato del RFC  para personas morales: <br/> <br/>';
				$ayuda.="3 caracteres alfanuméricos + 6 digitos (fecha) + homoclave";
				$ayuda.='<br/>Ejemplo: <label style="font-weight:bold;">MOR010101HOM</label>';
				break;
			default:
				$patron="/^[a-zA-Z0-9&]{3,4}(\d{6})([A-Za-z0-9]{3})+$/";	
				$ayuda="La cadena debe estar compuesta de 3 ó 4 caracteres alfanuméricos + 6 digitos (fecha) + homoclave";						
		}		
        
          
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
        
		if (!preg_match($patron, $datos['rfc_cliente'])) {
			throw new Exception($ayuda.$datos['rfc_cliente']);
		}	
        $query.=",rfc_cliente='".$this->EscComillas(strtoupper($datos['rfc_cliente']))."'";
        $query.=",nombre_fiscal='".$this->EscComillas($datos['nombre_fiscal'])."'";
		$query.=",nombre_comercial='".$this->EscComillas($datos['nombre_comercial'])."'";
        $query.=",calle='".$this->EscComillas($datos['calle'])."'";
        $query.=",numext='".$this->EscComillas($datos['numext'])."'";
        $query.=",numint='".$this->EscComillas($datos['numint'])."'";
        $query.=",colonia='".$this->EscComillas($datos['colonia'])."'";
        $query.=",localidad='".$this->EscComillas($datos['localidad'])."'";
        $query.=",tipo_cliente='".$this->EscComillas($datos['tipo_cliente'])."'";
		$query.=",estilista='".$this->EscComillas($datos['estilista'])."'";
		$query.=",foraneo='".$this->EscComillas($datos['foraneo'])."'";
     
        $query.=",id_ciu='".$datos['id_ciu']."'";
		$query.=",id_est='".$datos['id_est']."'";
        $query.=",id_pai='".$datos['id_pai']."'";
        $query.=",cp='".$datos['cp']."'";

        $query.=",calle_contacto='".$this->EscComillas($datos['calle_contacto'])."'";
        $query.=",numext_contacto='".$this->EscComillas($datos['numext_contacto'])."'";
        $query.=",numint_contacto='".$this->EscComillas($datos['numint_contacto'])."'";
        $query.=",colonia_contacto='".$this->EscComillas($datos['colonia_contacto'])."'";
        $query.=",localidad_contacto='".$this->EscComillas($datos['localidad_contacto'])."'";
        $query.=",cp_contacto='".$datos['cp_contacto']."'";
        // $query.=",id_cliente=".$datos['id_cliente']."";
        
      
        $query.=",nombre_contacto='".$this->EscComillas($datos['nombre_contacto'])."'";
        $query.=",email_contacto='".$this->EscComillas($datos['email_contacto'])."'";
        $query.=",telefono_contacto='".$this->EscComillas($datos['telefono_contacto'])."'";
        $query.=",celular_contacto='".$this->EscComillas($datos['celular_contacto'])."'";
		
		$query.=",status='".$this->EscComillas($datos['status'])."'";

        if (is_numeric($datos['id_listaprecio'])){	
			$query.=",id_listaprecio='".$datos['id_listaprecio']."'";
		}else{
            $query.=",id_listaprecio=NULL";
        }

        if (is_numeric($datos['id_cliente_categoria'])){	
			$query.=",id_cliente_categoria='".$datos['id_cliente_categoria']."'";
		}
		
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
            $data = $this->getcliente($id);
            return $data['Cliente'];
        }catch(Exception $e){            
            return false;
        }                    

    }
	
	public function getcliente($IDValue){	
		$query = "SELECT id_cliente,nombre_fiscal,nombre_comercial,estilista,foraneo,nombre_contacto,email_contacto,telefono_contacto,celular_contacto,status,tipo_cliente,rfc_cliente,id_ciu,id_est,id_pai,calle,numext,numint,localidad,colonia,cp,calle_contacto,numext_contacto,numint_contacto,localidad_contacto,colonia_contacto,cp_contacto,id_listaprecio,id_cliente_categoria FROM $this->useTable
        WHERE id_cliente = $IDValue ;";

		$arrCliente= $this->select($query);
		return array('Cliente'=>$arrCliente[0]);		
	}
	
	public function delete($id){
        return parent::delete($id);
    }
}
?>
