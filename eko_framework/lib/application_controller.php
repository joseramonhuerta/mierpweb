<?php
/**
 * @class ApplicationController
 */
class MyException extends Exception { 
	var $mensaje='';
	var $titulo='';
	var $icon='';	//Iconos disponibles: WARNING|ERROR|INFO|QUESTION
	function MyException($mensaje='Exception Msg',$titulo='Exception',$icon='ERROR'){
		$this->mensaje=$mensaje;
		$this->message=$mensaje;
		$this->titulo=$titulo;
		$this->icon=$icon;	
	}
}
 
class ApplicationController {
    public $request, $id, $params;
    public $requiereAuth=true;
    /**
     * dispatch
     * Dispatch request to appropriate controller-action by convention according to the HTTP method.
     */
	//-------------------------------------------------
	 function getModelObject(){	 
		if(empty($this->modelObject)){		

			$this->modelObject=new $this->model;			
		}
		return $this->modelObject;
	 }
	function getParams(){
		$model=$this->getModelObject();
		$campos=$model->select;
		$params=array();
		
		foreach ($campos as $campo){
			//busco los campos en la variable post
			if ( is_string($campo) ){
				if ( isset($_POST[$campo]) ) $params[$campo]=$_POST[$campo];
			}else if ( is_array($campo) && sizeof($campo)==1 ){//esperamos un alias
				$nomCampo=key($campo);
        		$alias=$campo[$nomCampo];
				if (isset($_POST[$alias]) ){
					$params[$nomCampo]=$_POST[$alias];				
				}
			}
		}
		return $params;		
	}
	
	function check_email_address($email) 
	{
		/*if (!preg_match('/^[^0-9][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[@][a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,4}$/',$email)){
			return false;
		}else{
			return true;
		}*/
		//throw new Exception($email);
		// Primero, checamos que solo haya un símbolo @, y que los largos sean correctos
	
	  if (!@ereg("^[^@]{1,64}@[^@]{1,255}$", $email)) 
		{
			// correo inválido por número incorrecto de caracteres en una parte, o número incorrecto de símbolos @		
	    return false;
	  }
		
	  // se divide en partes para hacerlo más sencillo
	  $email_array = explode("@", $email);
	  $local_array = explode(".", $email_array[0]);
	  for ($i = 0; $i < sizeof($local_array); $i++) 
		{
		
		     
	    if (!@ereg("^(([A-Za-z0-9!#$%&'*+/=?^_`{|}~-][A-Za-z0-9!#$%&'*+/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$", $local_array[$i])) 
			{
		
	      return false;
	    }
	  } 
	  // se revisa si el dominio es una IP. Si no, debe ser un nombre de dominio válido
		if (!@ereg("/^\[?[0-9\.]+\]?$/", $email_array[1])) 
		{ 
	     $domain_array = explode(".", $email_array[1]);
	     if (sizeof($domain_array) < 2) 
			 {
			 	
	        return false; // No son suficientes partes o secciones para se un dominio
	     }
	     for ($i = 0; $i < sizeof($domain_array); $i++) 
			 {
	        if (!@ereg("^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$", $domain_array[$i])) 
					{
	           return false;
	        }
	     }
	  }
	  return true;
	}
	
	function validarCorreos($emailsStr){
		$emailsStr=strtolower($emailsStr);
		$emailsArray=explode(',',$emailsStr);
		$numCorreos=sizeof($emailsArray);
		$validos=array();
		//throw new Exception($numCorreos);
		for($i=0;$i<$numCorreos;$i++){
			$email=trim($emailsArray[$i]);
			if ($email!=''){
				if(!$this->check_email_address($email)){
					throw new Exception($email);
					/*$response=array(
						'success'=>false,
						'msg'=>array(
							'titulo'=>'Correo Invalido',
							'mensaje'=>"El email $email es Inválido"
						)
					);
					return $response;
					*/
				}
				$validos[]=$email;	
			}						
		}
		return $validos;
	}
	
	public function dispatch($request) { 
		$this->request = $request;
		$this->id = $request->id;
		$this->params = $request->params;
		
		if ($request->isRestful()) {
			return $this->dispatchRestful();
		}
		if ($request->action) {
			try{
				$this->revisarPermiso();
				$this->beforeAction($request);
			
				$respuesta=$this->{$request->action}();
				
				if (isset($respuesta)){
					$this->comprimirRespuesta($respuesta);
				}
			}catch(MyException $e){
                $response=array();
				$response['success']=false;				
				$response['msg']=array(
					'titulo'=>$e->titulo,
					'mensaje'=>$e->mensaje,
					'icon'=>$e->icon
				);				
				$response['data']=array();
				echo trim( json_encode($response));
			}catch (Exception $e) {
				$response=array();
				$response['success']=false;
				$response['msg']=$e->getMessage();
				$response['data']=array();
				echo trim( json_encode($response));
			}
		}
	}
    private function comprimirRespuesta($respuesta){
        $respuesta=json_encode($respuesta);
        $HTTP_ACCEPT_ENCODING = $_SERVER["HTTP_ACCEPT_ENCODING"];

        if (headers_sent ())
            $encoding = false;
        else if (strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false)
            $encoding = 'x-gzip';
        else if (strpos($HTTP_ACCEPT_ENCODING, 'gzip') !== false)
            $encoding = 'gzip';
        else
            $encoding = false;

        $encoding = false;
        if ($encoding) {
            $respuesta = gzencode($respuesta, 6);
            header('Content-Encoding: ' . $encoding);
        }

        header("content-type: text/html");
        echo $respuesta;
    }
    public function revisarPermiso(){
    	//Con el nombre del controlador y el id Del usuario
    	//throw new Exception(json_encode($this->request));
    }
    public function beforeAction() {    	
        $request=$this->request;
        try{    //REVISA SI LA ACCION NECESITA QUE EL USUARIO HAYA SIDO IDENTIFICADO
            if (!$this->requiereAuth){
            	return; //happy path
            }
			
            //-------------------------------------------------------
    	    //		 Talvez esta acción no necesita identificacion
	        //-------------------------------------------------------			
           	if (isset($this->components['Auth'])) {
            	$auth = $this->components['Auth'];
                if (isset($auth ['allowedActions'])){	
                $actions = $auth ['allowedActions'];
                foreach ($actions as $action) {
                	if (strtoupper($request->action)==strtoupper($action))                                    
                    	return true;	//ok, continua con la peticion
                    }
            	}                  
            }    
			//-------------------------------------------------------
			//		La acción si necesita identificación del usuario			
			//-------------------------------------------------------
			if (!isset($_SESSION['Auth']['User']['IDUsu'])){//Si el usuario no ha sido identificado
				echo header('HTTP/1.1 403 Forbidden');
				exit;				
			}
				
            //---------------------------------------------------------
            //				REVISAR PERMISO AL CONTROLADOR
            //---------------------------------------------------------
			
			
			//-------------------------------------------------------
    	    //		 Talvez esta acción no necesita autorizacion
	        //-------------------------------------------------------			
           	if (isset($this->components['ACL'])) {
            	$acl = $this->components['ACL'];
                if (isset($acl['allowedActions'])){	
                $actions = $acl ['allowedActions'];
                foreach ($actions as $action) {
                	if (strtoupper($request->action)==strtoupper($action))                                    
                    	return true;	//ok, continua con la peticion
                    }
            	}                  
            }    
			
        	$model=new Model();
        	
        	if ($_SESSION['Auth']['User']['super']==1 || $_SESSION['Auth']['User']['AdminUsu']==1){
        		return true; //el super tiene derecho a todo
        	}
        	// $IDUsr=$_SESSION['Auth']['User']['IDUsu'];
            // $query="SELECT KEYUsuPriv,KEYID,Origen FROM cat_modulos 
			// LEFT JOIN cat_usuarios_privilegios ON KEYUsuPriv=$IDUsr AND KEYID=IDMod AND Origen='MOD'
			// WHERE controller='".$this->request->controller."'";
			// echo $query;
            // $arrRes=$model->query($query);

            // if (sizeof($arrRes)>0){
            	// if (isset($arrRes[0]['KEYID'])){
            		return true;	
            	// }else{
            		// throw new Exception('No tiene los privilegios para realizar esta acción');
            	// }            	            	            	
            // }else{
            	// throw new Exception('No tiene los privilegios para realizar esta acción');
            // }
        }catch(Exception $e){
         	if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])){
         		$response=array(
         			'success'=>false,
         			'msg'=>array(
         				'titulo'=>'Información de acceso',
         				'mensaje'=>$e->getMessage()
	         		)
	         	);
         		echo json_encode($response);
         		exit;
         	}else{
         		header('Content-Type: text/html; charset=iso-8859-1');
         		
         		?>
         		<html>
         		<head>
         		</head>
         		<body>
         			<h1>Informaci&oacute;n de acceso</h1>
         			<h3>No tiene permiso para entrar a esta p&aacute;gina</h3>
         		</body>
         		
         		</html> 
         		<?php          		         		
         		exit;
         		//Aqui deberia llamar la vista
         		//$view=$this->getView('/accesso_view.php');
         		//$view->setMensaje("No tiene permiso para entrar a esta página");
         		//$view->show();
         	}
            
        }    
			
    }
    
    public function afterAction(){
        
    }
 	
    protected function filtroToSQL($filtro) {  // filtro para busqueda
        $filtroArray = explode(" ", $filtro);
        $condiciones = "";
        $condicion   = "";
        foreach ($this->camposAfiltrar as $campo) {

            foreach ($filtroArray as $text) {
                if (strlen($text) > 0
                    )$condicion.="$campo LIKE '%$text%' AND ";
            }

            if (strlen($condicion) > 0) {
                $condicion = substr($condicion, 0, strlen($condicion) - 4); //<----LE BORRO LA ULTIMA PARTE "AND ";
                $condicion = "(" . $condicion . ") OR ";
                $condiciones.=$condicion;
                $condicion = "";
            }
        }
        $where = '';
        if (strlen($condiciones) > 0) {
            $condiciones = substr($condiciones, 0, strlen($condiciones) - 3); //<----LE BORRO LA ULTIMA PARTE "or ";
            $where = "WHERE ($condiciones)";
        }
        return $where;
    }
    
}
?>