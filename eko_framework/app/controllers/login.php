<?php
require ('eko_framework/app/models/login.php');       //MODELO
class Login extends ApplicationController {
    //var $uses=array('Login');
    var $components = array('Auth'=>array(
        'allowedActions'=>array('setUsr','setPass','crearAdmin','getEmps','seleccionarcorporativo','seleccionarempresa','seleccionarsucursal','enter','identificarse','recordarContra')
    ));
     var $loginModel;
	 var $model='LoginModel';
	 /**/
	 function validarEmail($temp_email) {
		if (filter_var($temp_email, FILTER_VALIDATE_EMAIL)) {
			return true;
		}else{
			return false;
		}
		
	}

	

	/* Cuando el usuario no es identificado, se registra el intento*/ 
	function registrarIntento(){
		$usuario=$_SESSION['identificado']['emaUsu'];
		
		$ip=$_SERVER['REMOTE_ADDR'];

		$date = new DateTime();
		$fecha= $date->format('Y-m-d');
		$hora= $date->format('H:i:s');
		//Busca el registro 
		$sql="SELECT intentos,ip,fecha,user,hora_inicio,hora_fin FROM loginattempts WHERE ip='$ip' AND fecha='$fecha';";
		$model=$this->getModelObject();
		$arrRegistro=$model->select($sql);
		if ( empty($arrRegistro) ){
			$sqlInsert="INSERT INTO loginattempts SET intentos=1, ip='$ip', fecha='$fecha', user='$usuario', hora_inicio='$hora', hora_fin='$hora';";
			$model->insert($sqlInsert);
		}else{
			$intentos=intval($arrRegistro[0]['intentos'])+1;
			$sqlInsert="UPDATE loginattempts SET intentos=$intentos, user='$usuario', hora_fin='$hora' WHERE ip='$ip' AND fecha='$fecha';";
			$model->insert($sqlInsert);
		}		
	}
	
	/*	Antes de identificar al usuario, se llama esta funcion para ver si la ip no está bloqueada	*/	
	function confirmIPAddress(){
		$ip=$_SERVER['REMOTE_ADDR'];
		$model=$this->getModelObject();
		//buscar que la ip no esté bloqueda
		$date = new DateTime();
		$fecha= $date->format('Y-m-d');
		$sqlBuscar="SELECT intentos,ip,fecha,user,hora_inicio,hora_fin FROM loginattempts WHERE ip='$ip' AND fecha='$fecha';";
		
		$arrIntentos=$model->select($sqlBuscar,MASTER);
		if ( empty($arrIntentos) ){
			return true;
		}else{
			$intentos=intval($arrIntentos[0]['intentos']);
			if ( $intentos>= INTENTOS_PERMITIDOS){
				$intentos++;	
				$hora= $date->format('H:i:s');
				$usuario=$_POST['username'];
				$sqlInsert="UPDATE loginattempts SET intentos=$intentos, user='$usuario', hora_fin='$hora' WHERE ip='$ip' AND fecha='$fecha';";				
				$model->insert($sqlInsert);
				throw new Exception("Su ip ha sido bloqueada debido a multiples reintentos fallidos");
			}
		}		
	}
	 /**/
	function recordarContra(){
		//Obtener las contraseñas del usuario en cada corporativo		
		$data=array(
			'nomUser'=>'Usuario',			
		);
		require ('templates/contras_recuperadas.php');       
		require ('eko_framework/includes/phpmailer/mailer.php');  
		
		//	$_POST['username']='zesar@test.com';
		if ( !isset($_POST['username']) ){
			throw new Exception("usuario desconocido");
		}
		
		$email=$_POST['username'];
		
		$sqlContras="SELECT AES_DECRYPT(uc.pass,'asdf') contra,c.nombre_corporativo corpName,DBCor
		FROM cat_usuarios_corporativos uc
		LEFT JOIN cat_corporativos c ON c.id_corporativo=uc.id_corporativo
		WHERE uc='$email';";
		
		$model=new Model();
		$arrContras=$model->select($sqlContras,MASTER);
		if ( !empty($arrContras) ){
			$DBCor=	$arrContras[0]['DBCor'];
			if (!empty($DBCor)){
				$sqlUserName="SELECT NomUsu FROM cat_usuarios WHERE UserUsu='$email'";
				$arrUserName=$model->select($sqlUserName,$DBCor);
				if ( !empty($arrUserName) ){
					$data['nomUser']=$arrUserName[0]['NomUsu'];
				}
				$data['contras']=$arrContras;
				$contenido=imprimeTemplate_contras_recuperadas($data);
				//echo $contenido;exit;
				enviarCorreo('Recuperacion de contraseñas',$contenido, $To=array($email), $cc = null, $bcc = null, $html = true, $attachment = null,$enviadoPor='sistema@pontuel.mx',$FromName='pontuel');
				//enviar contraseñas al correo especificado
			}			
		}
		
		
		return array(
			'success'=>true
		);
	}
	
   function  beforeAction() {
    //   echo "MODELO";
       $this->loginModel=new LoginModel();
        parent::beforeAction();
    }

    function identificarse(){
		if ( !$this->validarEmail( $_POST['username'] ) ){
			throw new Exception("escriba un email como nombre de usuario");
		}
		//$this->confirmIPAddress();
         $_POST['email']=$_POST['username'];
        $response=$this->setusr();
        if (!$response['success']){
            return $response;
        }
        $_POST['pass']=$_POST['pass'];
        return $this->setPass();
    }
    
    function enter(){       
        $response=array();
        if ($_SESSION['identificado']['entrar']==true){
            $_SESSION['Auth']['User'] = $_SESSION['identificado'];
            $_SESSION['Auth']['User']['UserUsu'] = $_SESSION['Auth']['User']['emaUsu'];
            // $_SESSION['Auth']['User']['UserUsu'] = $_SESSION['Auth']['User']['emaUsu'];
			if ($_SESSION['Auth']['User']['AdminUsu'] == 2) {
                $_SESSION['Auth']['User']['super'] = true;
            } else {
                $_SESSION['Auth']['User']['super'] = false;
            }
            
            unset ($_SESSION['identificado']);
            $response['success']=true;
        }else{
            $response['success']=false;
            $response['msg']="Debe Identificarse para entrar";
        }                
        
        return $response;
    }

   
    public function setusr() {

        if (isset($_POST['email'])) { //verifico que recibí la variable
            $email = $_POST['email'];
            $_SESSION['identificado']['emaUsu'] = $email;
            $response['msg'] = "usuario recibido";
            $response['success'] = true;
            $response['siguiente'] = 1;
        } else {
            $response['msg'] = 'No se recibió el usuario.';
            $response['success'] = false;
        }
        return $response;
    }

    public function setPass() {
        $identificado = $this->identificar($_SESSION['identificado']['emaUsu'], $_POST['pass']);
      //  echo $identificado; 
     //   echo print_r($identificado);
        if ($identificado==true) {
        	
            $_SESSION['identificado']['pass']=$_POST['pass'];
             $response = $this->analizarSeleccionAutomaticaDeCorporativo();
        } else {
            $response['success'] = false;
            $response['msg'] = 'Usuario o password incorrecto.';
			//$this->registrarIntento();
        }     
       return $response;
    }
   
    private function identificar($user, $pass) {
	
        $identificado=$this->loginModel->identificar($user, $pass);

        if ($identificado) {
            
            $_SESSION['identificado']['AdminUsu'] = $identificado['esadmin'];
            $_SESSION['identificado']['IDUsr'] = $identificado['id_usuario'];
			$_SESSION['identificado']['NomUsu'] = $identificado['nombre_usuario'];	
            $_SESSION['identificado']['identificado'] = true;
			// throw new exception($identificado['nombre_usuario']);
            
            return true;
        } else {
        	
            unset($_SESSION['identificado']);
            $_SESSION['identificado']['emaUsu'] = $user;
        }
        return false;
    }
  
    function seleccionarcorporativo() {
        try{
            $response=$this->seleccionaCorporativo($_POST['IDCor']);
            
            $response=$this->analizarSeleccionAutomaticaDeEmpresa();
        }catch(Exception $e){
            $response['success']=false;
            $response['msg']=$e->getMessage();
        }        
       return $response;
    }

   public function GETEMPS() {
        $arr = array();
        try {       
            $identificado=$_SESSION['identificado']['identificado'];          
            if (!$identificado)throw new Exception('Debe especificar su nombre de usuario y contraseña');
            $email=$_SESSION['identificado']['emaUsu'];
           
            $idUser = $this->loginModel->getUserId($email,$_SESSION['dbcorp']);
            $empresas = $this->getEmpresas();
            if (sizeof($empresas)==0)throw new Exception('No tiene empresas asignadas, consulte con el administrador');
            
            $response['data'] = $empresas;
            $response['success'] = true;
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }
       return $response;
    }

    public function seleccionarempresa() {
        try {
			// throw new Exception('1');
            $response=$this->setEmpresa($_POST['IDEmp']);            
			// throw new Exception('1');
			$response=$this->analizarSeleccionAutomaticaDeSucursal($_POST['IDEmp']); //crear funcion
			// throw new Exception('2');
		} catch (Exception $e) {
            $response['msg'] = $e->getMessage();
            $response['success'] = false;
        }
       return $response;
        
    }
	
	 public function seleccionarsucursal() {
       try {
             $response=$this->setSucursal($_POST['IDSuc']);           
        } catch (Exception $e) {
            $response['msg'] = $e->getMessage();
            $response['success'] = false;
        }
       return $response;
        
    }
    //==========================================================================
    //      FUNCIONES PRIVADAS
    //==========================================================================
    private function getCorporativos() {
        $tipoUser = $_SESSION['identificado']['AdminUsu'];
        $userId = $_SESSION['identificado']['IDUsr'];
        $pass=$_SESSION['identificado']['pass'];
      
        switch ($tipoUser) {
            case 2: //Super User
                $corps = $this->loginModel->obtenerCorporativosParaSuper($userId,$pass);
                break;
            default:
                $corps = $this->loginModel->obtenerCorporativosRelacionadosConElUsuario($userId,$pass);                            
        }     
        
        if (sizeof($corps)==0){
            throw new Exception('No tiene corporativos asignados');
        }
        //echo $pass;
        $corps = $this->filtrarConContraseña($corps,$pass);
        
         if (sizeof($corps)==0){
            throw new Exception('Usuario o password incorrecto.');
        }        
        return $corps;
    }
    private function filtrarConContraseña($corps,$pass){
        $filtrados=array();
        foreach($corps as $corp){           
            if ($corp['pass']==$pass){
                $filtrados[]=$corp;
            }            
        }
        return $filtrados;
    }
    
      private function analizarSeleccionAutomaticaDeCorporativo() {
        $corps=$this->getCorporativos($_POST['pass']);
       
        $ncs = sizeof($corps);
        
        if ($ncs == 1) {
            $response = $this->seleccionarPrimerCorporativo($corps);
           // $response=$this->analizarSeleccionAutomaticaDeEmpresa();
			$response['data']['corporativos']=$corps;
			
        } else if ($ncs > 1) {
            $response['success'] = true;
            $response['siguiente'] = 2;   //MOSTRAR CORPORATIVOS
           
            $corporativos=array();
            foreach($corps as $corp){   //Le quito la base de datos antes de mandarla al cliente HTTP
                $corpCombo=array();
             
                $corpCombo['id_corporativo']=$corp['id_corporativo'];
                $corpCombo['nombre_corporativo']=$corp['nombre_corporativo'];
                $corporativos[]=$corpCombo;
            }
            $response['data']['corporativos']=$corporativos;
        } else if ($ncs == 0) {
            $response['success'] = false;
            $response['msg'] = 'no tiene corporativos asignados, consulte con el administrador del sistema';            
        }
		
        return $response;
    }
    /*
    private function filtrarCorporativosPorStatusDelUsuario($corps,$userId){
        $filtrados=array();
        foreach($corps as $corp){
            $dbCorp=$corp['DBCor'];
            $usuarioActivo=$this->loginModel->getStatusDelUsuarioEnElCorporativo($dbCorp,$userId);
            if ($usuarioActivo){
                $filtrados[]=$corp;
            }
        }
        return $filtrados;
        
    }*/

    private function seleccionarPrimerCorporativo($corp){
    	
        	$response=$this->seleccionaCorporativo($corp[0]['id_corporativo']);
    	
        return $response;
    }
     private function analizarSeleccionAutomaticaDeEmpresa() {
         $empresas=$this->getEmpresas();

        $numEmps = sizeof($empresas);
        if ($numEmps > 0) {
            $response['success'] = true;
            $response['siguiente'] = 3;   //MOSTRAR EMPRESAS
            $response['data']=$empresas;
           // $response['data']=$empresas;
        } else if ($numEmps == 1) {
            $response=$this->seleccionarPrimerEmpresa($empresas);            
        } else if ($numEmps == 0) {
            if ($_SESSION['identificado']['AdminUsu'] == 1 | $_SESSION['identificado']['AdminUsu'] ==2) {
               return $this->entrar();
            } else {
                $response['success']=false;
                $response['msg']='No  tiene empresas asignadas, consulte al administrador del sistema';
            }
        }
        return $response;
    }
	
	private function analizarSeleccionAutomaticaDeSucursal($IDEmp) {
        $sucursales=$this->getSucursales($IDEmp);
		
        $numSucs = sizeof($sucursales);
		if ($numSucs > 0) {
            $response['success'] = true;
            $response['siguiente'] = 3;   //MOSTRAR EMPRESAS
            $response['data']=$sucursales;
        } else if ($numSucs == 1) {
            $response=$this->seleccionarPrimerSucursal($sucursales);            
        } else if ($numSucs == 0) {
            if ($_SESSION['identificado']['AdminUsu'] == 1 | $_SESSION['identificado']['AdminUsu'] ==2) {
               return $this->entrar();
            } else {
                $response['success']=false;
                $response['msg']='No  tiene sucursales asignadas, consulte al administrador del sistema';
            }
        }
        return $response;
    }
	
    private function getEmpresas() {
        $tipoUser = $_SESSION['identificado']['AdminUsu'];
        $userEmail=$_SESSION['identificado']['IDUsr'];        
        $db = $_SESSION['dbcorp'];
         try{
			$userId = $this->loginModel->getUserId($userEmail,$db);
         }catch(MyException $e){         	         	
         	throw $e;
         }catch(Exception $e){         	
         	$corpName=$_SESSION['identificado']['NomCor'];
         	throw new Exception("No pudo seleccionarse el corporativo.: $corpName");
         }
        $_SESSION['identificado']['IDUsu'] = $userId;
		switch ($tipoUser) {
            case 2: //Super User
				$empresas = $this->loginModel->obtenerTodasLasEmpresas($db);
                break;
            case 1: //Admin
                $empresas = $this->loginModel->obtenerTodasLasEmpresas($db);
                break;
            default:    //User
                $empresas = $this->loginModel->obtenerEmpresasConPermiso($db, $userId);
                break;
        }
        
        return $empresas;
    }
	
	 private function getSucursales($IDEmp) {
        $tipoUser = $_SESSION['identificado']['AdminUsu'];
        $userEmail=$_SESSION['identificado']['IDUsr'];        
        $db = $_SESSION['dbcorp'];
         try{
			$userId = $this->loginModel->getUserId($userEmail,$db);
         }catch(MyException $e){         	         	
         	throw $e;
         }catch(Exception $e){         	
         	$corpName=$_SESSION['identificado']['NomCor'];
         	throw new Exception("No pudo seleccionarse el corporativo..: $corpName");
         }
		 
        $_SESSION['identificado']['IDUsu'] = $userId;
		switch ($tipoUser) {
            case 2: //Super User
				$sucursales = $this->loginModel->obtenerTodasLasSucursales($db,$IDEmp);
                break;
            case 1: //Admin
                $sucursales = $this->loginModel->obtenerTodasLasSucursales($db,$IDEmp);
                break;
            default:    //User
                $sucursales = $this->loginModel->obtenerSucursalesConPermiso($db, $userId,$IDEmp);
                break;
        }
        
        return $sucursales;
    }

     private function seleccionarPrimerEmpresa($empresa) {          
         //$concat=$empresa[0]['Origen'].'-'.$empresa[0]['IDEmpresa'];
          return $this->setEmpresa($empresa[0]['id_empresa']);
    }
	
	 private function seleccionarPrimerSucursal($sucursal) {          
         
          return $this->setSucursal($sucursal[0]['id_sucursal']);
    }

    private function seleccionaCorporativo($corpId) {
        
   

        $userType=$_SESSION['identificado']['AdminUsu'];
        
        if ($userType!=2){
            $userId=$_SESSION['identificado']['emaUsu'];
            // throw new Exception($corpId.' '.$userId);	   
            $respArray=$this->loginModel->getCorporativoYrol($corpId,$userId);
        
             $corp=$respArray;
			 
            if ($corp['status']!='A'){
                throw new Exception('El corporativo está suspendido');
            }
            $db_name=$respArray['bd_corporativo'];
            // throw new Exception($corp['status']);	
            // throw new Exception($_SESSION['identificado']['IDUsr']);
            $status=$this->loginModel->getStatusDelUsuario($db_name,$_SESSION['identificado']['IDUsr']);
			
            if ($status!='A'){
                throw new Exception('El usuario está suspendido para este corporativo');
            }
            $_SESSION['identificado']['AdminUsu']=$respArray['AdminUsr'];
        }else{        	
            $respArray=$this->loginModel->getCorporativo($corpId);

            $corp=$respArray;
        }
         
        $_SESSION['dbcorp'] = $corp['bd_corporativo'];
        $_SESSION['NomCor'] = $corp['nombre_corporativo'];
        $_SESSION['IDCor'] = $corpId;
        $_SESSION['identificado']['IDCor'] = $corpId;
        $_SESSION['identificado']['NomCor']=$corp['nombre_corporativo'];
        $response['success'] = true;
        $response['siguiente'] = 3;
		
		/*BORRO LAS REFERENCIAS POR SI EL USUARIO YA HA SELECCIONADO UNA EMPRESA EN OTRO CORPORATIVO*/
		unset($_SESSION['identificado']['nombre']);
        unset($_SESSION['identificado']['IDOrigen']); 
        unset($_SESSION['identificado']['RFCEmp']);
        unset($_SESSION['identificado']['IDEmp']);
        unset($_SESSION['identificado']['ComEmp']);
        unset($_SESSION['identificado']['NomSuc']);
        unset($_SESSION['identificado']['IDSuc']);
        unset($_SESSION['identificado']['Origen']);
		
        return $response;
    }
    
    public function entrarSinEmpresa(){
        $response['success'] = true;
        $response['siguiente'] = 4;
        $response['msg'] = "entrar";
        return $this->entrar();       
    }
  
    private function getMatriz($idSuc){
        $query = "SELECT IDEmp,ComEmp,ManejaInvEmp FROM cat_sucursales s
                    LEFT JOIN cat_empresas e ON e.IDEmp = s.KEYEmpSuc WHERE s.IDSuc = $idSuc";
        $db = $_SESSION['dbcorp'];
        $res = mysqlQuery($query, $db);
        if (!$res)
            throw new Exception('Error al obtener la empresa relacionada con esta sucursal, Consulte con el administrador del sistema');
        if (mysql_num_rows($res) < 1)
            throw new Exception('Error en las relaciones de la empresa y esta sucursal');
        while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
            $arr = $row;
        }
        return $arr;
    }
   private function setEmpresa($IDEmp) {

        // $params = explode('-', $concat);
        // $key = $params[1];
        // $tipo = $params[0];

        $usuario = $_SESSION['identificado']['IDUsu'];
        $tipoUsuario=$_SESSION['identificado']['AdminUsu'];
        $suc=array();
		$db=$_SESSION['dbcorp'];
        if ($tipoUsuario == 2 | $tipoUsuario == 1) {   //SUPER o ADMIN
            $arr=array();
                         
             $empresa=$this->loginModel->getEmpresa($IDEmp,$db);
             $emp['nombre_fiscal']=$empresa['nombre_fiscal'];				 
             $emp['id_empresa']=$IDEmp;
			 $emp['maneja_inventario']=$empresa['maneja_inventario'];	
             $sucursales=$this->getSucursales($IDEmp);
			           
        } else {
			
			 $empresa=$this->loginModel->getEmpresa($IDEmp,$db);
             $emp['nombre_fiscal']=$empresa['nombre_fiscal'];				 
             $emp['id_empresa']=$IDEmp;
			 $emp['maneja_inventario']=$empresa['maneja_inventario'];	
			 // throw new Exception($IDEmp);
             $sucursales=$this->getSucursales($IDEmp);
			 //throw new Exception('11');
			/*
            $query="SELECT keyid as respuesta,
		IF(Origen='EMP',(SELECT ComEmp FROM cat_empresas WHERE IDEmp=KEYID),(select NomSuc FROM cat_sucursales WHERE IDSuc=KEYID) ) as Nombre,
		IF(Origen='EMP',(SELECT RFCEmp FROM cat_empresas WHERE IDEmp=KEYID),(SELECT RFCEmp FROM cat_sucursales s LEFT JOIN cat_empresas e ON e.IDEmp = s.KEYEmpSuc WHERE IDSuc=KEYID) ) as RFCEmp
		FROM cat_usuarios_privilegios
		WHERE KEYUsuPriv=$usuario AND KEYID=$key AND Origen='$tipo';";
            
            $db = $_SESSION['dbcorp'];
            $res = mysqlQuery($query, $db);
            $arr = '';
            if (!$res)
                //throw new Exception('error en la consulta' . "$query");
                throw new Exception('error en la consulta');
            if (mysql_num_rows($res) < 1)
                throw new Exception('No tiene permisos para entrar a esa empresa o sucursal');
            while ($row = mysql_fetch_array($res, MYSQL_ASSOC)) {
                $arr = $row;
            }
         
            if ($tipo=='SUC'){
                $suc['NomSuc']=$arr['Nombre'];
                $suc['IDSuc']=$key;
                $emp=$this->getMatriz($key);
            }else{
                $emp['ComEmp']=$arr['Nombre'];
                $emp['IDEmp']=$key;
                $suc['NomSuc']='';
                $suc['IDSuc']=0;
				$sql="SELECT *, ManejaInvEmp FROM cat_empresas WHERE IDEmp=$key";
				$arrEmp=$this->loginModel->select($sql);
				//print_r($arrEmp);exit;
				$emp['ManejaInvEmp']=$arrEmp[0]['ManejaInvEmp'];
            }*/
        }

        $_SESSION['identificado']['nombre_fiscal'] 	  = $arr['nombre_fiscal']; //NOMBRE DE LA EMPRESA O SUCURSAL
        $_SESSION['identificado']['id_empresa'] 		  = $emp['id_empresa'];
		$_SESSION['identificado']['maneja_inventario'] = $emp['maneja_inventario'];		
   
  
        //return $this->entrar();
    }
	
	private function setSucursal($IDSuc) {

        // $params = explode('-', $concat);
        // $key = $params[1];
        // $tipo = $params[0];

        $usuario = $_SESSION['identificado']['IDUsu'];
        $tipoUsuario=$_SESSION['identificado']['AdminUsu'];
        $suc=array();
		$db=$_SESSION['dbcorp'];
        if ($tipoUsuario == 2 | $tipoUsuario == 1) {   //SUPER o ADMIN
            $arr=array();
                         
             $sucursal=$this->loginModel->getSucursal($IDSuc,$db);
             $suc['nombre_sucursal']=$sucursal['nombre_sucursal'];				 
             $suc['id_sucursal']=$IDSuc;
			 /*
			 $almacen=$this->loginModel->getAlmacenDefault($IDSuc,$db);
             $alm['nombre_almacen']=$almacen['nombre_almacen'];				 
             $alm['id_almacen']=$almacen['id_almacen'];
            */
			           
        } else {
			 $sucursal=$this->loginModel->getSucursal($IDSuc,$db);
             $suc['nombre_sucursal']=$sucursal['nombre_sucursal'];				 
             $suc['id_sucursal']=$IDSuc;
			 /*
			 $almacen=$this->loginModel->getAlmacenDefault($IDSuc,$db);
             $alm['nombre_almacen']=$almacen['nombre_almacen'];				 
             $alm['id_almacen']=$almacen['id_almacen'];*/
			
        }

        $_SESSION['identificado']['nombre_sucursal'] = $suc['nombre_sucursal'];
        $_SESSION['identificado']['id_sucursal'] = $suc['id_sucursal'];
		$_SESSION['identificado']['id_almacen'] = $alm['id_almacen'];
		$_SESSION['identificado']['nombre_almacen'] = $alm['nombre_almacen'];
 

        return $this->entrar();
    }

    private function entrar(){
        $_SESSION['identificado']['entrar']=true;
        

         $response['siguiente'] = 5;
         $response['success'] = true;
               
        return $response;
                 
    }
    

}

