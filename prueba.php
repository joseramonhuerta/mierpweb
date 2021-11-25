<?php
    
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


	
        
        <link rel="stylesheet" type="text/css" media="all" href="css/login.css"/>

        <!-- CSS Ext 3.1.0 -->
	<link rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-blue.css" />
	
	 <?php
        
		
		 echo '<script type="text/javascript" src="js/ext-3.4.0/adapter/ext/ext-base.js"></script>';
		 
		echo '<script type="text/javascript" src="js/ext-3.4.0/ext-all-debug.js"></script>';
	
      
        echo '<script type="text/javascript" src="js/ext-3.4.0/src/locale/ext-lang-es.js"></script>';
		
        echo '<script type="text/javascript" src="js/app.js"></script>';
		
        echo '<script type="text/javascript" src="js/ext-ux/mensajes.js"></script>';
		
		echo '<script type="text/javascript" src="js/ext-ux/fixed_opera_vtype_bug.js"></script>';
       
            echo '<script type="text/javascript" src="js/pruebas/formPrueba.ui.js"></script>';
			 echo '<script type="text/javascript" src="js/pruebas/formPrueba.js"></script>';
			 
			  //echo '<script type="text/javascript" src="js/pruebas/WindowFechasObras/WindowFechasObras.ui.js"></script>';
			 //echo '<script type="text/javascript" src="js/pruebas/WindowFechasObras/WindowFechasObras.js"></script>';
       
        ?>
        
        

<script type="text/javascript">
    Ext.onReady(function(){
        App = new Ext.App({});  
		
       mew.loginPanel=new formPrueba({renderTo:'form'});

		
    }

    );

</script>		
</head>

<body>
 <div>
        <div id="form">
           
        </div>
    </div>
</body>
</html>
