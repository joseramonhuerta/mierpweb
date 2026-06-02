<?php

class Conexion {
    
    private static $instance = null;   
    private $host = 'localhost';   
    private $user = 'root';
    private $pass = '';     
    private $db = 'erp_corporativopruebas'; 
	
    private function __construct() {
       
    }
    
    public static function getInstance() {
        if (self::$instance == null) {
            
           
            $config = new self(); 
            
           
            $conn = new mysqli($config->host, $config->user, $config->pass, $config->db);

           
            if ($conn->connect_error) {
               
                die("Error de conexión a la base de datos: " . $conn->connect_error);
            }
            
            
            if (!$conn->set_charset("utf8mb4")) {
                 
            }

            self::$instance = $conn;
        }
        return self::$instance;
    }
    
   
    private function __clone() {}
    
   
    public function __wakeup() {}
}

?>