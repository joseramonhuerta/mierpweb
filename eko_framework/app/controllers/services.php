<?php

//require ('eko_framework/app/models/linea.php');
class Services extends ApplicationController {
    
	public function initialize(){
		parent::initialize();
		$this->loadComponent('RequestHandler');
	}
	
    public function index(){ //<----------------PARA EL GRID
		$response['totalRows'] = 0;
		$response['success']    = true;
		$response['msg']       = "mensaje1";
		
		echo "1111";
    }
	
	
}
?>
