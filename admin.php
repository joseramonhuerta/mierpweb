<?php
    require('eko_framework/lib/init.php');
    require('eko_framework/app/controllers/sistema.php');
    if (!isset($_SESSION['Auth'])){
        header('location: index.php');
        return;
    }
    $sis=new Sistema();
    //$res=$sis->actualizarTemaEnLaSesion();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html >
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>     
        
	<!-- CSS Ext 3.4.0 -->
	<link rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/ext-all.css" />	

		<?php

        $tema='A';        
		
        switch($tema){
            case 'A': //Azul
                echo '<link id="tema" rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-blue.css" />';
                break;
            case 'B': //Gris
                echo '<link id="tema" rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-gray.css" />';
                break;
            case 'C':    //Access
                echo '<link id="tema" rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-access.css" />';
                break;
            case 'D':
                echo '<link id="tema" rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-blueen.css" />';
                break;
            case 'E':
                echo '<link id="tema" rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-tp.css" />';
                break;
            case 'F':
                echo '<link id="tema" rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-gray-extend.css" />';
                break;
			 case 'G':
                echo '<link id="tema" rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-newgentheme.css" />';
                break;	
            default:  
        }

        ?>
        
        <!--link href="http://192.168.2.17/feng_community/public/assets/themes/default/stylesheets/website.css" rel="Stylesheet" type="text/css" /-->
        
	<link rel="stylesheet" type="text/css" href="css/searchfield.css" />
	<link rel="stylesheet" type="text/css" href="css/RowEditor.css" />
	<link rel="stylesheet" type="text/css" href="css/styles.css" />
    <link rel="stylesheet" type="text/css" href="css/fileuploadfield.css" />
    <link rel="stylesheet" type="text/css" href="css/data-view.css" />
	<link rel="stylesheet" type="text/css" href="css/app.css" />

	
	 <?php
        /*
         * Incluyo los javascript de esta forma por la necesidad de comprimir los js para el proyecto en produccion
         *
         */
        require('administracion_js_files.php');
        $rutas=getJsFiles(false);
        for( $i=0; $i<sizeof($rutas); $i++ ){
            echo '<script type="text/javascript" src="'.$rutas[$i].'"></script>';
        }
        ?>
        
        <script type="text/javascript" src="js/fix_timeout.js"></script> 
</head>

<body>
<div id="customhandler">
		<center>
		<div><!--
			<div>
				
				<span style="font-size:18px;color:#3f3f3f;">La descarga debe comenzar en unos segundo. Si no de <a id="customhandler_descarga" href="#"> clic aquí</a> para empezar</span>
				</br>
				<span style="font-size:18px;color:#3f3f3f;">Si ya instaló el programa <a id="customhandler_close" href="#"> clic aquí </a> para continuar</span>
			</div>
			<hr class="linea_horizontal" />
			-->
		</div>
		</center>
	</div>
</body>
</html>
