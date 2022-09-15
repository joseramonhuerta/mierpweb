<?php
/**
 * @class Model
 * Clase base para los modelos
* $debug:		Usar como true para desarrollo	(muestra todo el detalle del error junto con el query)
* 				usuar como false para modo de produccion (Solo regresar? el nombre del Modelo que lanz? el error y el tipo de consulta que trataba de realizar )
*/
class Model {
    public $id=0, $name='Model';
    private $debug=true; 	
    var $select="*";
    var $primaryKey='id';
	var $singleton=false;	//TRUE: PARA USAR EL MISMO LINK DE CONEXION,  FALSE: UNA CONEXION NUEVA EN CADA CONSULTA
    function jsDateToMysql($jsDate){
        $date = "04/30/1973";
        $arrDate=explode('/', $jsDate);   

        list($dia, $mes, $year) = explode("/", $jsDate);

        @list($year,$time) = explode(" ", $year);//si la fecha no trae la hora puede marcar un NOTICE:, no nos interesa el notice
        $convertida="$year-$mes-$dia";

        if ($time!=''){
            list($hora, $minuto, $segundo) = explode(':', $time);
            $convertida.=" $hora:$minuto:$segundo";
        }        
        return $convertida;
    }
	function startTransaction(){
		$conexion=dbConexion::singleton();
		$conexion->startTransaction();
	}

    /*      
	*	     
	*/
    public function EscComillas($texto){
		//return 	$texto;
		// return addslashes($texto);
    	return str_replace ( "'" ,"\'" ,$texto);
    }
	
    public function execute($query,$dbName=null){

		//if ($this->singleton){			
			$conexion=dbConexion::singleton($dbName);
			$link=$conexion->link;
		/*}else{
			$conexion = dbConexion::singleton($dbName,true);
			$link=$conexion->link;
		}*/
		
		$res  = mysql_query($query,$link);        
        if (!$res) {
			if ($this->debug){	
				//throw new Exception('Debug: '. $this->name. "->".mysql_error() ." dbName: ".$dbName." : ".$query);
				generaLog('query_'.$this->name,mysql_error().":".$query);
				throw new Exception('Debug: '. $this->name. "->".mysql_error()." $query");
			}else{
				generaLog('query_'.$this->name,mysql_error().":".$query);				
				throw new Exception($this->name.": Error al realizar la consulta, consulte con el administrador del sistema");
			}
        }
    }
    
    public function query($query,$dbName=null){		
		//return "false";
		//if ($this->singleton){
			$conexion = dbConexion::singleton($dbName);
			$link=$conexion->link;
		/*}else{			  
			$conexion = dbConexion::singleton($dbName,true);
			$link=$conexion->link;
		}*/
		
		$res  = mysql_query($query,$link);
		        
        if (!$res) {
			if ($this->debug){	
				//throw new Exception('Debug: '. $this->name. "->".mysql_error() ." dbName: ".$dbName." : ".$query);
				generaLog('query_'.$this->name,mysql_error().":".$query);
				throw new Exception('Debug: '. $this->name. "->".mysql_error()." $query");
			}else{
				generaLog('query_'.$this->name,mysql_error().":".$query);				
				throw new Exception($this->name.": Error al realizar la consulta, consulte con el administrador del sistema");
			}
        }
        $result=array();
        // if (mysql_num_rows($res) >= 1) {
            while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
                $result[] = $row;
            }
        // }
        return $result;        
    }
    
    public function select($query,$dbName=null){
    	return $this->query($query,$dbName);
    }

    /*  
	*	RECIBE UN QUERY "INSERT" , LO EJECUTA Y REGRESA EL ID DEL REGISTRO INSERTADO   
	*/
    public function insert($query,$dbName=null){
        //if ($this->singleton){
			$conexion = dbConexion::singleton($dbName);
			$link=$conexion->link;
		/*}else{
			$conexion = dbConexion::singleton($dbName,true);
			$link=$conexion->link;
		}*/
        $res = mysql_query($query,$link);
        if (!$res) { 
        	switch(mysql_errno()){
        		case 1062:
					generaLog('insert_'.$this->name,mysql_error().":".$query);
        			throw new Exception("El registro no puede duplicarse");
        		break;
        		default:
        		
        	}     
			if ($this->debug){
				//throw new Exception('Debug: '. $this->name. "->".mysql_error() );
				generaLog('insert_'.$this->name,mysql_error().":".$query);
				throw new Exception('Debug: '. $this->name. "->".mysql_error() . $query);
			}else{
				generaLog('insert_'.$this->name,mysql_error().":".$query);
				throw new Exception($this->name." ".": Error al intentar crear el registro, consulte con el administrador del sistema");
			}                  
        }
        $id=mysql_insert_id();        
        return $id;
    }

	/*
	*	EJECUTA UN QUERY "UPDATE"	REGRESA TRUE AUNQUE DEBERIA REGRESAR  mysql_affected_rows()); 
	*/
    public function update($query, $dbName=null) {
        //if ($this->singleton){
			$conexion = dbConexion::singleton($dbName);
			$link=$conexion->link;
		/*}else{
			$conexion = dbConexion::singleton($dbName,true);
			$link=$conexion->link;
		}*/
        $res = mysql_query($query,$link);
        if (!$res) {      
        	switch(mysql_errno()){
        		case 1062:
        			generaLog('update_'.$this->name,mysql_error().":".$query);
        			throw new Exception("El registro no puede duplicarse");
        		break;
        		default:        		
        	}  
			if ($this->debug){
				throw new Exception('Debug: '. $this->name. "->".mysql_error() . $query);
				generaLog('update_'.$this->name,mysql_error().":".$query);
			//	throw new Exception('Debug: '. $this->name. "->".mysql_error() );
			}else{
				generaLog('update_'.$this->name,mysql_error().":".$query);
				throw new Exception($this->name.": Error al intentar actualizar el registro, consulte con el administrador del sistema");
			}                  
        }		           
        
        return true;
		//return   mysql_affected_rows();
    }
	
	
	function delete($Id){
		$dbName='';
		//if ($this->singleton){
			$conexion = dbConexion::singleton($dbName);
			$link=$conexion->link;
		/*}else{
			$conexion = dbConexion::singleton($dbName,true);
			$link=$conexion->link;
		}*/
        $query="DELETE FROM $this->useTable WHERE $this->primaryKey=$Id";
        $result = mysql_query($query,$link);
		if (!$result) {      
			if ($this->debug){
				generaLog('DELETE_'.$this->name,mysql_error().":".$query);	
				throw new Exception('Debug: '. $this->name. "->".mysql_error() . $query);
			}else{
				generaLog('DELETE_'.$this->name,mysql_error().":".$query);
				throw new Exception($this->name.": Error al intentar eliminar el registro, consulte con el administrador del sistema");
			}                  
        }        
        return $Id;
    }
    /*
	*	EJECUTA UN QUERY "DELETE"	REGRESA TRUE AUNQUE DEBERIA REGRESAR  mysql_affected_rows()); 
	*/
     public function queryDelete($query, $dbName=null) {
       // if ($this->singleton){
			$conexion = dbConexion::singleton($dbName);
			$link=$conexion->link;
		/*}else{
			$conexion = dbConexion::singleton($dbName,true);
			$link=$conexion->link;
		}*/
        $res = mysql_query($query,$link);
        if (!$res) {      
			if ($this->debug){
				throw new Exception('Debug: '. $this->name. "->".mysql_error() . $query);
				generaLog('queryDelete_'.$this->name,mysql_error().":".$query);
			//	throw new Exception('Debug: '. $this->name. "->".mysql_error());
			}else{
				generaLog('queryDelete_'.$this->name,mysql_error().":".$query);
				throw new Exception($this->name.": Error al intentar eliminar el registro, consulte con el administrador del sistema");
			}                  
        }
		//return   mysql_affected_rows();
        return true;
    }
	
	/*
	*	ESTA FUNCION SE USA PARA FILTRAR EL TEXTO ESCRITO EN LA CAJA DE TEXTO UBICADA EN EL TOOLBAR DE LOS BUSCADORES
	*	ESE TEXTO ES PASADO EN LA VARIABLE $filtro
	*	Para su funcionamiento, es necesario que se defina en el modelo un arreglo llamado $camposAfiltrar,
	*	entonces el resultado de la busqueda estara definida por algun campo de ese arreglo que coincida (LIKE '%$texto%') CON EL TEXTO DE LA VARIABLE $filtro (NI YO ENTIENDO ESTA LINEA)
	*/
     function filtroToSQL($filtro,$filtros=array(),$usarAlias=false,$where='') {
     	 //$where = '';
     	 
        if (!empty($filtro)) {
			$filtroArray = explode(" ", $filtro);
	        $condiciones = "";
	        $condicion = "";
			$tableAlias=$this->name;
	        foreach ($this->camposAfiltrar as $campo) {
	
	            foreach ($filtroArray as $text) {
	                if (strlen($text) > 0){
						if ($usarAlias==true){
							$condicion.="$tableAlias.$campo LIKE '%$text%' AND ";
						}else{
							$condicion.="$campo LIKE '%$text%' AND ";
						}
						
					}
	            }
	
	            if (strlen($condicion) > 0) {
	                $condicion = substr($condicion, 0, strlen($condicion) - 4); //<----LE BORRO LA ULTIMA PARTE "AND ";
	                $condicion = "(" . $condicion . ") OR ";
	                $condiciones.=$condicion;
	                $condicion = "";
	            }
	        }
	       
	        if (strlen($condiciones) > 0) {
	            $condiciones = substr($condiciones, 0, strlen($condiciones) - 3); //<----LE BORRO LA ULTIMA PARTE "or ";
	            $where = "WHERE ($condiciones)";
	        }
        }
        
        //---------------------------------------------------------
        $condiciones="";
        for($i=0; $i<sizeof($filtros); $i++){   
        	if ( sizeof($filtros[$i])==1 && isset($filtros[$i]['filtro'])  ){
        		
        		$condiciones.=$filtros[$i]['filtro']." AND ";
        		
        	}else{
        		$campo=$filtros[$i]['campo'];
				$condicion=$filtros[$i]['condicion'];
				$valor=$filtros[$i]['valor'];			
	        	$condiciones.="$campo $condicion '$valor' AND ";	
        	}    	
			
        }
       
     	if (strlen($condiciones) > 0) {
             $condiciones=substr($condiciones, 0, strlen($condiciones) - 4); //<----LE BORRO LA ULTIMA PARTE "AND ";
             if (empty($where)){
            	$where = "WHERE $condiciones"; 	
             }else{
             	$where.= "AND $condiciones";
             }
            
        }
        //---------------------------------------------------------
        return $where;
    }

	/*
	*		 		PLANTILLA PARA BUSQUEDA PAGINADA   
	*/
     function readAll($start=0, $limit=0, $filtro='',$params=array(),$usarAlias=false) {	    
		
		$filtros= isset($params['filtros'])? $params['filtros'] : array();
		$filtroSql = $this->filtroToSQL($filtro,$filtros,$usarAlias);
        
        $tableAlias=$this->name;
       	//-------------------------------------------------------------------------------------------------------
        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable as $tableAlias $filtroSql";
        $resultado= $this->query($query);
        $totalRows = $resultado[0]['totalrows'];
		//-------------------------------------------------------------------------------------------------------
        if (isset($params['select'])){
        	$selectParams=$params['select'];
        }else if (isset($this->select)){
        	$selectParams=$this->select;
        }else{
        	$selectParams=array(0=>'*');
        }
		
		
		if ( is_string($selectParams) ){
			$select=$selectParams;
		}else if (is_array($selectParams)){
			$select=$this->constructSelect($selectParams, $tableAlias);
        }else{
        	$select='*';
        }
        //------------------------------------------------------------------
        if (isset($params['hasOne'])){
        	$hasOne=$params['hasOne'];
        }else if (isset($this->hasOne)){
        	$hasOne=$this->hasOne;
        } else{
        	$hasOne=array();
        }
     	
        $leftJoin='';
        for($i=0; $i<sizeof($hasOne); $i++){
        	if ( isset($hasOne[$i]['tabla']) && isset($hasOne[$i]['alias']) && isset($hasOne[$i]['pk']) && isset($hasOne[$i]['pk']) ){
        		//echo print_r($hasOne[$i]);		
        		$tabla=$this->hasOne[$i]['tabla'];
        		$alias=$this->hasOne[$i]['alias'];
        		$fk=$this->hasOne[$i]['fk'];
        		$pk=$this->hasOne[$i]['pk'];
        		$leftJoin.=" LEFT JOIN $tabla as $alias ON $alias.$pk=$tableAlias.$fk ";
        	}
        	
	        if (isset($hasOne[$i]['select'])){
	        	$selectParams=$hasOne[$i]['select'];
	        	$tableAliasLeft=$hasOne[$i]['alias'];
	        	$select.=",".$this->constructSelect($selectParams, $tableAliasLeft); 	        	
	        }
        }     	
        //-------------------------------------------------------------------------------------------------------
        $orderBy=$this->gerOrderBy();
        $query = "SELECT $select 
        		FROM $this->useTable as $tableAlias
        		$leftJoin
        		$filtroSql
				$orderBy
				limit $start,$limit";
				//echo $query."<br/>";	
        $resArr=$this->query($query);
		//throw new Exception($query);
		/*if (sizeof($resArr)==0){
			$response['success']=false;
			$response['msg']=array('titulo'=>$this->name,'mensaje'=>'No se encontraron resultados con los par�metros especificados');
        	$response['data']=array();
        	$response['totalRows'] = 0;	
		}else{
			$response['success']=true;
        	$response['data']=$resArr;
        	$response['totalRows'] = $totalRows;
		}*/
        $response['success']=true;
        	$response['data']=$resArr;
        	$response['totalRows'] = $totalRows;

        return $response;
    }
   
   function gerOrderBy(){
		if (isset($this->orderBy)){
			$order='';
			foreach($this->orderBy as $orderEl){
				foreach($orderEl as $column=>$orden){
					$order.="$column $orden,";
				}
			}
			if (strlen($order)>0){
				$order=substr($order,0,-1);
				$order='ORDER BY '.$order;
			}
		}else{
			$order='';
		}
		return $order;
   }
   /*
   *	Plantillas
   */
   
   /*
   *				PLANTILLA PARA OBTENER UN REGISTRO	
   */
    public function constructSelect($selectParams, $tableAlias){
    	$select='';        	
        for ($i=0; $i<sizeof($selectParams); $i++){
        	$regSelect=$selectParams[$i];
        	if (is_array($regSelect) && sizeof($regSelect)==1){
        		$campo=key($regSelect);
        		$alias=$regSelect[$campo];
        		if (isset($regSelect[$campo])){
        			$select.="$tableAlias.$campo as $alias,";	
        		}else{
        			$select.="$tableAlias.$campo,";
        		}        				
        	}else if (is_array($regSelect) && sizeof($regSelect)==2){
        		$campo=$regSelect[1];        		
        		$select.="$campo,";        		        				
        	}else if( is_string($regSelect) ){        		
        		$select.="$tableAlias.$regSelect,";
        	}else{        		
        		
        		throw new Exception("constructSelect error"); 
        	}        		
        }    
        $select=substr($select, 0,strlen($select)-1);
        
        return $select;   
    }
    function getById($IDValue,$params=array()){	
    	//-------------------------------------------------------------------------------------------------------
        if (isset($params['select'])){
        	$selectParams=$params['select'];
        }else if (isset($this->select)){
        	$selectParams=$this->select;
        }else{
        	$selectParams='*';
        }
		$tableAlias=$this->name;
		
		if ( is_string($selectParams) ){
			$select=$selectParams;
		}else if (is_array($selectParams)){
			$select=$this->constructSelect($selectParams, $tableAlias);
        }else{
        	$select='*';
        }
        //-------------------------------------------------------------------------------------------------------
         //------------------------------------------------------------------
        if (isset($params['hasOne'])){
        	$hasOne=$params['hasOne'];
        }else if (isset($this->hasOne)){
        	$hasOne=$this->hasOne;
        } else{
        	$hasOne=array();
        }
     	
        $leftJoin='';
        for($i=0; $i<sizeof($hasOne); $i++){
        	if ( isset($hasOne[$i]['tabla']) && isset($hasOne[$i]['alias']) && isset($hasOne[$i]['pk']) && isset($hasOne[$i]['pk']) ){
        		//echo print_r($hasOne[$i]);		
        		$tabla=$this->hasOne[$i]['tabla'];
        		$alias=$this->hasOne[$i]['alias'];
        		$fk=$this->hasOne[$i]['fk'];
        		$pk=$this->hasOne[$i]['pk'];
        		$leftJoin.=" LEFT JOIN $tabla as $alias ON $alias.$pk=$tableAlias.$fk ";
        	}
        	
	        if (isset($hasOne[$i]['select'])){
	        	$selectParams=$hasOne[$i]['select'];
	        	$tableAliasLeft=$hasOne[$i]['alias'];
	        	$select.=",".$this->constructSelect($selectParams, $tableAliasLeft); 	        	
	        }
        }     	
        //-------------------------------------------------------------------------------------------------------
            $query="SELECT $select 
            FROM $this->useTable as $tableAlias
            $leftJoin
            WHERE $tableAlias.$this->primaryKey=$IDValue";
					
            $arrRes= $this->select($query);
            if (empty($arrRes)){
            	throw new Exception("El elemento buscado no existe en la base de datos");
            }
            $datos=array();
			
            $datos[$this->name]=$arrRes[0];            
            return $datos;
    }
    
    
    
	/*	
	*	PLANTILLA GENERICA PARA GUARDAR	
	*/
    function save($params,$log=true){
         $registroNuevo = false;
         
         if (!is_array($params) || sizeof($params)==0  ){
             throw new Exception("No se recibieron los datos a guardar");
         }
         /*  Que pasa con $IDUsu cuando el usuario no est? logeado o cuando sea Super User??   */
        $IDUsu = $_SESSION['Auth']['User']['IDUsu'];
        $where='';
        if (!empty($params[$this->primaryKey])) {//UPDATE
            $query = "UPDATE $this->useTable SET ";
            if ($log){
            	$query.="ModUsuario=$IDUsu";    //LOG
            	$query.=",ModFecha=now(),";	
            }
            
            $where = " WHERE $this->primaryKey = " . $params[$this->primaryKey];
        } else {  //INSERT
            $query = "INSERT INTO $this->useTable SET ";
        	if ($log){
            	$query.="AddUsuario=$IDUsu";    //LOG
            	$query.=",AddFecha=now(),";
            }

            $registroNuevo = true;
        }

        foreach($params as $key=>$value){
				
				if (is_null($value)){
					$query.="$key=NULL,";
				}else{
					$query.="$key='$value',";	
				}

        }
        $query=substr($query, 0,strlen($query)-1);  

        $query = $query . $where;

        $result = $this->insert($query);

        if ($registroNuevo) {
            $id = mysql_insert_id();
        } else {
		//	echo $query;
            $id = $params[$this->primaryKey];
        }
        $this->id = $id;
        $data = $this->getById($id);
        
        $this->registroNuevo=$registroNuevo;
        
        return $data;                  
    }
	
    public function __construct($params=null) {
		if (defined('SQL_DEBUG')){	//ESTA CONSTANTE ESTA DEFINIDA EN EL ARCHIVO config.php, alli mismo se define lo relacionado con la base de datos			
			if (SQL_DEBUG=='0'){
				$this->debug=false;
			}else{
				$this->debug=true;
			}
		}    
    }

}
?>