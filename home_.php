<?php session_start(); ?>
<?php 
				if (!isset($_SESSION['authObject'])){					
						header( 'Location: index.php' ) ;
						return;
				}				
			?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

	


<title>Documento de Prueba</title>

<style type="text/css">
<!--
body {
	background-image: url(images/bg.jpg);
	margin-left: 0px;
	margin-top: 0px;
	margin-right: 0px;
	margin-bottom: 0px;
}
.EMP{
	/*color:red;*/
}
.SUC{
	padding:0px 25px;
}
.ajaxLoading{
	background-image:url("images/loading.gif");
	background-repeat:no-repeat;
	background-position:right top;
}
.Login_Centrar { margin:0 auto 0 auto; width:580px; }

#Login_Banda { height:50px; background-image:url(images/bgTransparencia.png); border-bottom:dashed #FFFFFF 1px; border-top:dashed #FFFFFF 1px}

#Login_Banda_Captura { width:300px; height:50px; background-image:url(images/bgTransparenciaNivel75.png); float:left }
#Login_Banda_Icono { width:80px; height:50px; background-image:url(images/bgTransparenciaNivel75.png); float:left; text-align:center }
#Login_Banda_Caption { width:200px; height:50px; float:left; font-size:18px; text-align:center; padding-top:15px }
.Login_Ligas { padding:5px 14px; float:left; }

body,td,th {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 12px;
	
}
a.activo{
	text-decoration:underline;
}
a.inactivo{
	text-decoration:none;
}
a:link {
	text-decoration: underline;
	color:#FFFFFF;
}
a:visited {
	/*text-decoration: underline;*/
	color:#FFFFFF;
}
a:hover {
	text-decoration: none;
	color:#FFFFFF;
}
a:active {
	/*text-decoration: underline;*/
	color:#FFFFFF;
}
.login_links{
	
}
-->
</style>
<!--[if lte IE 6]>
<style type="text/css">

#Login_Banda {

	height:50px;
    background-image: none;
    filter: progid: DXImageTransform.Microsoft.AlphaImageLoader( src="images/bgTransparencia.png", sizingMethod="scale");
	
	
}
#Login_Banda_Icono{
	height:0px;
}
#Login_Banda_Caption{
	height:0px;
}

</style>
<script defer type="text/javascript" src="js/pngfix.js"></script>

<![endif]-->

</head>

<body>
	<div class="Login_Centrar" style="text-align:center"><img src="images/logo.png"/></div>
    <div id="Login_Banda">
    	<div class="Login_Centrar">
			
            <div id="Login_Banda_Caption" style="color: #FFF;">
            	HOME</strong>
            </div>
			<?php $authObject=$_SESSION['authObject'];
			?>
    		<div id="Login_Banda_Captura" style="position:absolute;left:50%;">
			Corporativo:<?php echo  $authObject['NomCor'];?> 	<br/>		
			<?php echo  $authObject['Origen'].' : '.$authObject['nombre']; ?> <br/>
			User:<?php echo $authObject['emaUsr']; ?> 
			</div>
        </div>
	</div>
	<div class="login_links" style="position:absolute;left:50%;">
    	<div class="Login_Ligas"><a id="lnkAtras" href="logout.php">Logout</a></div>        
	</div>
</body>
</html>
