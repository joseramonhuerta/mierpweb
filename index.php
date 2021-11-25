<?php session_start();
if (isset($_SESSION['Auth']['User']['IDUsu'])) {
    echo header('Location: admin.php');
    exit;
}else{
    unset($_SESSION['identificado']);
}
?>
<?php unset($_SESSION['dbcorp']); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


	
        
        <link rel="stylesheet" type="text/css" media="all" href="css/login.css"/>

        <!-- CSS Ext 3.1.0 -->
	<link rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/ext-all.css" />
        <link rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-blue.css" />
        <?php
       
        require('login_js_files.php');
        $rutas = getJsFiles(false);
        for ($i = 0; $i < sizeof($rutas); $i++) {
            echo '<script type="text/javascript" src="' . $rutas[$i] . '"></script>';
        }
		
        ?> 
	    

<title>Inicio de Sesion: miErpWeb</title>
<script type="text/javascript">
    Ext.onReady(function(){
        App = new Ext.App({});  
		
       mew.loginPanel=new mew.mainLogin({renderTo:'form'});

		
    }

    );

</script>

</head>

<body style="font-size: 14px;">
    <div class="centro">
        <div id="form">
           
        </div>
    </div>
	 
</body>
</html>
