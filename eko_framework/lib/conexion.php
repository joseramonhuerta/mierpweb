<?php
class dbConexion {
	public $link,$dbase;
	private static $instance;
	private $transaction=false;
	
	public function switchDB($dbname){
		mysql_close(self::$instance->link);
		self::$instance = new dbConexion($basedatos,true);       
    }
		
	 public static function singleton($basedatos=false){
        if (!isset(self::$instance)) {            
			//SI LA CONEXION NO EXISTE
            self::$instance = new dbConexion($basedatos,false);
        }else if (isset(self::$instance) && self::$instance->transaction==false){		
			mysql_close(self::$instance->link);
			self::$instance = new dbConexion($basedatos,true);
		}
		
        return self::$instance;
    }
	
	public function startTransaction(){
		$this->transaction=true;
		mysql_query('SET AUTOCOMMIT=0');		
		mysql_query('START TRANSACTION');		 
	}
	
	public function dbConexion($basedatos=false) {
		$this->transaction=false;
		$this->dbase = ($basedatos) ? $basedatos : DB_NAME; // a cual bd se conecta
		
		//$this->link  = @mysql_connect(DB_HOST, DB_USER, DB_PASS, false, 131074|8192);
		$this->link  = mysql_connect(DB_HOST, DB_USER, DB_PASS, false, 8192);
		if (!$this->link) {			
			throw new Exception("Error de Conexión: El sistema no pudo conectarse con el servidor de Bases de datos");
			//$response['success']=false;
			//$response['msg']='ERROR: El sistema no pudo conectarse con el servidor de Bases de datos';
			//$response['msg']=mysql_error();
			//echo json_encode($response);
			//exit;
		}
		if (!mysql_select_db($this->dbase, $this->link)) {
			
			throw new Exception("Error de Conexión: No pudo seleccionarse la base de datos:".mysql_error());			
		}					
		mysql_query("SET NAMES utf8");
	
	}

   	public function __destruct() {
           // mysql_close( $this->link );
   	}
        
	public function useMaster(){
		if (!mysql_select_db(DB_MASTER,$this->link)) {
		   throw new Exception(mysql_error());
	   }
	}
}
?>