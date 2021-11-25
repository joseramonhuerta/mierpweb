<html>
<body>
<?php
set_time_limit ( 6000);
if (ob_get_level() == 0) {
    ob_start();
}

echo str_pad('',4096);

echo "<h1>Building Site Tool</h1>\n";

ob_flush();
flush();

if (isset($_GET['configfile'])){
    
        require_once $_GET['configfile'];
    
    
}else{
    echo "<h3>Escriba la ruta del archivo de configuracion  (?configfile=admin_build_conf.php)</h3>";
    exit;
}


/*
require_once 'cssmin.php';


@mkdir($outputJs, 0775, true);
@mkdir($outputCss, 0775, true);

chmod($outputJs, 0775);
chmod($outputCss, 0775);

$outputPath=$outputCss;
echo "<p>Creando Archivo".$outputPath.$outputName.".css</p>\n";
ob_flush();
flush();

file_put_contents($outputPath.$outputName.'.css',"");

foreach($arrayFiles["css"] as $cssFile){
	echo "<p>Cargando $cssFile</p>\n";
	ob_flush();
	flush();
	
	$css = file_get_contents($cssFile);
	
	echo "<p>Minimificando $cssFile</p>\n";
	ob_flush();
	flush();
	
	$css_min = cssmin($css);
	
	echo "<p>Incluyendo css minimificado en ".$outputPath.$outputName.".css</p>\n";
	ob_flush();
	flush();
	
	file_put_contents($outputPath.$outputName.".css",$css_min."\n", FILE_APPEND);

}

echo "<p>Comprimiendo ".$outputPath.$outputName.".css</p>\n";
ob_flush();
flush();

$css_min = file_get_contents($outputPath.$outputName.".css");
$css_gz = gzencode($css_min,6);
file_put_contents($outputPath.$outputName.".css.gz",$css_gz);
*/
#-------------------------------------------------------------------------------------#

require_once 'jsmin.php';
$outputPath=$outputJs;

echo "<p>Creando Archivo ".$outputPath.$outputName.".js</p>\n";
ob_flush();
flush();

file_put_contents($outputPath.$outputName.".js","");

foreach($arrayFiles["js"] as $jsFile){
	echo "<p>Cargando $jsFile</p>\n";
	ob_flush();
	flush();
	
	$js = file_get_contents($jsFile);
	
	echo "<p>Minimificando $jsFile</p>\n";
	ob_flush();
	flush();
	
	$js_min = JSMin::minify($js);
	
	echo "<p>Incluyendo js minimificado en ".$outputPath.$outputName.".js</p>\n";
	ob_flush();
	flush();
	
	file_put_contents($outputPath.$outputName.'.js',$js_min."\n", FILE_APPEND);
}


echo "<p>Comprimiendo ".$outputPath.$outputName.".js</p>\n";
ob_flush();
flush();

$js_min = file_get_contents($outputPath.$outputName.".js");
$js_gz = gzencode($js_min,6);
file_put_contents($outputPath.$outputName.'.js.gz',$js_gz);

echo "<h4>Proceso terminado</h4>\n";

ob_end_flush();

?>
</body>
</html>