<?php
	
	if (!isset($_SESSION))session_start();
	ini_set('session.cache_expire',1);

	require('../../../config.php');	
    require('../../../../eko_framework/lib/conexion.php');
    require('../../../../eko_framework/lib/funciones.php');
    require('../../../../eko_framework/lib/ResulsetToExt.php');

    require('../../../../eko_framework/lib/application_controller.php');
    require('../../../../eko_framework/lib/model.php');
     
    //require('eko_framework/lib/request.php');
?>