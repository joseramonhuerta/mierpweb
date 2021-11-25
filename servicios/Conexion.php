<?php
   $host = "localhost";
   $user = "root";
   $pass = "ramon"; 
   $db = "erp_hucr860220hl3";
   
   $conexion = new mysqli($host,$user,$pass,$db);
   if($conexion->connect_errno){
	   echo "no se pudo hacer la conexion con la base de datos.";
   }

?>