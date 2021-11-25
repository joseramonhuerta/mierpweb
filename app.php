<?php
	ini_set("display_errors", 0);
	ignore_user_abort(true);
	set_time_limit(0);
    require('eko_framework/lib/init.php');
    // Get Request
    $request = new Request(array('restful' => false));
	try{	
		require('eko_framework/app/controllers/' . $request->controller . '.php');
		$controller_name = ucfirst($request->controller);
		$controller = new $controller_name;
		// Dispatch request
		$controller->dispatch($request);
	}catch(Exception $e){
		echo $e->getMessage();
	}
?>