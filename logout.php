<?php 
session_start(); 
if ( isset($_SESSION['Auth']['Cliente']) ){
	$location="Location: clientes.php";
}else{
	$location="Location: index.php";
}

session_destroy();
header( $location ) ;
return;
 ?>