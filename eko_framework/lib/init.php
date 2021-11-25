<?php
	
	if (!isset($_SESSION))session_start();
	ini_set('session.cache_expire',1);
	/*
    require(dirname(__FILE__).'/lib/config.php');	
    require(dirname(__FILE__).'/lib/conexion.php');
    require(dirname(__FILE__).'/lib/funciones.php');
    require(dirname(__FILE__).'/lib/ResulsetToExt.php');

    require(dirname(__FILE__).'/lib/application_controller.php');
    require(dirname(__FILE__).'/lib/model.php');
     
    require(dirname(__FILE__).'/lib/request.php');
	*/
	require('eko_framework/config.php');	
    require('eko_framework/lib/conexion.php');
    require('eko_framework/lib/funciones.php');
    require('eko_framework/lib/ResulsetToExt.php');

    require('eko_framework/lib/application_controller.php');
    require('eko_framework/lib/model.php');
     
    require('eko_framework/lib/request.php');
?>