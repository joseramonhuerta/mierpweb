<?php
function getJsFiles($modoProduccion=true){
    $arrayFiles=Array();
		
        $arrayFiles[]="js/ext-3.4.0/adapter/ext/ext-base.js";  
		$arrayFiles[]="js/ext-3.4.0/ext-all-debug.js";
	
      
        $arrayFiles[]="js/ext-3.4.0/src/locale/ext-lang-es.js";
        $arrayFiles[]="js/app.js";
        $arrayFiles[]="js/ext-ux/mensajes.js";
		$arrayFiles[]="js/ext-ux/fixed_opera_vtype_bug.js";
		/*
		$arrayFiles[]="js/login/RecordarForm.ui.js";
        $arrayFiles[]="js/login/RecordarForm.js";
        $arrayFiles[]="js/login/LoginForm.ui.js";
        $arrayFiles[]="js/login/LoginForm.js";
        $arrayFiles[]="js/login/loginStoreCorporativos.js";
        $arrayFiles[]="js/login/loginStoreEmpresas.js";
        $arrayFiles[]="js/login/corpForm.ui.js";
        $arrayFiles[]="js/login/corpForm.js";
        $arrayFiles[]="js/login/loginPanel.ui.js";        
        $arrayFiles[]="js/login/loginPanel.js";
		$arrayFiles[]="js/login/main.ui.js";
		$arrayFiles[]="js/login/main.js";
        */
		
		$arrayFiles[]="js/login/main.ui.js";
		$arrayFiles[]="js/login/main.js";
		$arrayFiles[]="js/login/loginStoreCorporativos.js";
        $arrayFiles[]="js/login/loginStoreEmpresas.js";
		$arrayFiles[]="js/login/loginStoreSucursales.js";
		$arrayFiles[]="js/login/comportamiento_login.js";
		$arrayFiles[]="js/login/comportamiento_corporativo.js";
		
	 if ($modoProduccion){
        for($i=0 ; $i<sizeof($arrayFiles) ;$i++){
            $arrayFiles[$i]='../'.$arrayFiles[$i]; //POR LA RUTA DEL SCRIPT DEL COMPRESOR
        }
    }
	
	return $arrayFiles;
}
	
?>