<?php
    require('init.php');

    // Get Request
    $request = new Request(array('restful' => false));

    //echo "<P>request: " . $request->to_string();-

    // Get Controller
    require('app/controllers/' . $request->controller . '.php');
    $controller_name = ucfirst($request->controller);
    $controller = new $controller_name;

    // Dispatch request
    $controller->dispatch($request);
	
	
function mostrarVariables(){
	if (!defined('DS')) {
		define('DS', DIRECTORY_SEPARATOR);
	}
	/**
	 * These defines should only be edited if you have cake installed in
	 * a directory layout other than the way it is distributed.
	 * When using custom settings be sure to use the DS and do not add a trailing DS.
	 */


	if (!defined('APP_DIR')) {
		define('APP_DIR', basename(dirname(dirname(__FILE__))));
	}
	echo APP_DIR."<br/>";
/**
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 */
	if (!defined('CAKE_CORE_INCLUDE_PATH')) {
		define('CAKE_CORE_INCLUDE_PATH', ROOT);
	}
	echo CAKE_CORE_INCLUDE_PATH."<br/>";
/**
 * Editing below this line should NOT be necessary.
 * Change at your own risk.
 *
 */
	if (!defined('WEBROOT_DIR')) {
		define('WEBROOT_DIR', basename(dirname(__FILE__)));
	}
	echo WEBROOT_DIR."<br/>";
	if (!defined('WWW_ROOT')) {
		define('WWW_ROOT', dirname(__FILE__) . DS);
	}
	echo "WEBDIR: ".WWW_ROOT."<br/>";
	
	if (!defined('CORE_PATH')) {
		if (function_exists('ini_set') && ini_set('include_path', CAKE_CORE_INCLUDE_PATH . PATH_SEPARATOR . ROOT . DS . APP_DIR . DS . PATH_SEPARATOR . ini_get('include_path'))) {
			define('APP_PATH', null);
			define('CORE_PATH', null);
		} else {
			define('APP_PATH', ROOT . DS . APP_DIR . DS);
			define('CORE_PATH', CAKE_CORE_INCLUDE_PATH . DS);
		}
	}
	echo APP_PATH."<br/>";
	echo CORE_PATH."<br/>";
	
	if (isset($_GET['url']) && $_GET['url'] === 'favicon.ico') {
		return;
	} else {
	//	$Dispatcher = new Dispatcher();
	//	$Dispatcher->dispatch();
	}
//echo print_r($_SERVER);

echo "<br/>";
echo "<br/>";


echo $_SERVER['REQUEST_URI']."<br/>";
echo $_SERVER['PHP_SELF']."<br/>";
echo "<br/>";
echo "<br/>";
echo print_r($_SERVER);

}	
	
?>

