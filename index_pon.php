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
        <link rel="stylesheet" type="text/css" href="js/ext-3.4.0/resources/css/xtheme-tp.css" />	
        <!-- SISTEMA -->
        <!--<script type="text/javascript" src="js/login_min_1.1.0.js.gz"></script>-->
        <script type="text/javascript" src="appjs.php"></script>
        




<title>Login: Pontuel</title>
<script type="text/javascript">
    Ext.onReady(function(){
        App = new Ext.App({});

        miFacturaWeb.loginPanel=new miFacturaWeb.mainUi({renderTo:'form'});
    }

    );

</script>

</head>

<body style="background-color: #666;font-size: 14px;">
    <div class="centro">
        <div id="form">

        </div>
    </div>
</body>
</html>
