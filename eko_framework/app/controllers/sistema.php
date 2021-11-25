<?php
require 'eko_framework/app/models/user_corp.php';
require 'eko_framework/app/models/parametros.php';
require 'eko_framework/app/models/empresa.php';
require 'eko_framework/app/models/sucursal.php';
require 'eko_framework/app/models/ciudad.php';
require 'eko_framework/app/models/turno.php';
require 'eko_framework/app/models/remision.php';
require_once 'eko_framework/lib/model.php';
// require 'eko_framework/app/models/certificado.php';
class Sistema extends ApplicationController{
    var $uses=array('Parametros','UserCorp');
	var $model="Model";
    var $components = array(
		'ACL'=>array(
        'allowedActions'=>array(
        	'actualizarTemaEnLaSesion'
    		,'getParametros'
    		,'seleccionarEmpresa'
    		,'obtenerempresas'
    		,'getInfoCertificado'
    		,'getInfoFolios'
			,'getInfoFoliosNuevo'
    		,'extraerTipoDeCambioDelDia'
    		,'getMonedas',
    		'obteneralmacenes',
			'obtenersucursales',
    		'seleccionarAlmacen',
			'seleccionarSucursal',
			'verificasesion',
			'getAuditoria'
    	)
		)
	);
    /*var $components = array('Auth'=>array(
        'redirectActions'=>array('index')
       ));*/
	/*Consulta un stored procedura para obtener el tipo de cambio*/
	private function consultarPermisos($user, $parent, $mostrarTodos){
		$model=$this->getModelObject();
		$consulta="CALL get_menus_del_usuario($user, $parent, $mostrarTodos);";
		$arrMenus=$model->select($consulta);
		return $arrMenus;
	}
	public function prohibidosJs(){
		/*===========================================================
			Primero se obtienen todos los permisos configurables	
		===========================================================*/
		$arrTodos=$this->getArrayPermisos(0,0,true);
		
		 /*===========================================================
			Luego se obtienen todos los permisos asignados al usuario
		===========================================================*/	
		$usuario=$_SESSION['Auth']['User'] ['IDUsu'];		
		
		if ($_SESSION['Auth']['User']['AdminUsu']==1 || $_SESSION['Auth']['User']['AdminUsu']==2){
			$arrPermisos=$this->getArrayPermisos($usuario, 0, true);
		}else{
			$arrPermisos=$this->getArrayPermisos($usuario, 0, 0);
		}
		
		/*===========================================================
			Luego se comparan los permisos, los permisos generales que no estàn configurados para el usuario, estàn prohibidos
		===========================================================*/	
		$prohibidos=array();
		foreach($arrTodos as $modulo){
			if ( !in_array($modulo,$arrPermisos) ){
				$prohibidos[]=$modulo;
			}
		}
		return $prohibidos; 
	}
	public function getArrayPermisos($user,$parent,$mostrarTodos){
		if ( !empty($parent) ){
			//$_POST['node']=$parent;
			$menus=array();
		}else{
		//	$_POST['node']=0;			
		}
		$menus= $this->consultarPermisos($user,$parent,$mostrarTodos);
		
		$hijos=array();
		for($i=0; $i<sizeof($menus); $i++){			
			if ( !$menus[$i]['leaf'] ) {				
				$hijos[] = $this->getArrayPermisos($user, $menus[$i]['id'], $mostrarTodos);
			}			
		}
		
		$permisos=array();
		foreach($menus as $menu){
			if ( !empty($menu['newTab']) ){
				$permisos[]=$menu['newTab'];
			}
		}

		foreach($hijos as $hijo){			
			foreach($hijo as $nodo){
				$permisos[]=$nodo;		
			}		
		}

		return $permisos;
	}
	public function getMonedas(){
		$monedas=array();
		
		$monedas[0]=array('idMoneda'=>1,'descripcion'=>'Pesos','bandera'	=>"images/banderas/m/Mexico.png");
		$monedas[1]=array('idMoneda'=>2,'descripcion'=>'Dolares','bandera'	=>'images/banderas/e/Estados Unidos.png');
		return array(
			'success'=>true,
			'monedas'=>$monedas
		);
	}
	public function obtenerAlmacenes(){
		$IDUsu = $_SESSION['Auth']['User']['IDUsu'];
		$IDEmp=$_SESSION['Auth']['User']['id_empresa'];   
		$IDSuc=$_SESSION['Auth']['User']['id_sucursal'];
		$admin= $_SESSION['Auth']['User']['AdminUsu'];
		if ($admin==0){
			$sql="SELECT a.id_almacen id_almacen,a.nombre_almacen nombre_almacen
			FROM cat_almacenes a
			INNER JOIN cat_usuarios_privilegios p ON p.id_usuario=$IDUsu AND p.id_privilegio=id_almacen AND p.tipo_privilegio=3
			INNER JOIN cat_empresas e ON e.id_empresa=a.id_empresa
			INNER JOIN cat_sucursales s ON s.id_sucursal=a.id_sucursal
			WHERE a.id_empresa='$IDEmp' AND a.id_sucursal='$IDSuc' AND a.status='A'";			
		}else{
			$sql="SELECT id_almacen,nombre_almacen 
			FROM cat_almacenes
			WHERE id_empresa='$IDEmp' AND id_sucursal='$IDSuc' AND status='A'";
		}
		// throw new Exception($sql);
		$model=new Model();
		$almacenes=$model->select($sql);
		//$arrAlmacenes[]=array(/*"id_almacen"=>0, "nombre_almacen"=>"TODOS LOS ALMACENES"*/);
		foreach($almacenes as $almacen)
			$arrAlmacenes[] = $almacen;

		return array(
			'success'=>true,
			'data'=>$arrAlmacenes			
		);
	}
	
	public function seleccionarAlmacenDefault(){
		$IDUsu = $_SESSION['Auth']['User']['IDUsu'];
		$IDEmp=$_SESSION['Auth']['User']['id_empresa'];   
		$IDSuc=$_SESSION['Auth']['User']['id_sucursal'];
		$admin= $_SESSION['Auth']['User']['AdminUsu'];
		if ($admin==0){
			$sql="SELECT a.id_almacen id_almacen,a.nombre_almacen nombre_almacen
			FROM cat_almacenes a
			INNER JOIN cat_usuarios_privilegios p ON p.id_usuario=$IDUsu AND p.id_privilegio=id_almacen AND p.tipo_privilegio=3
			INNER JOIN cat_empresas e ON e.id_empresa=a.id_empresa
			INNER JOIN cat_sucursales s ON s.id_sucursal=a.id_sucursal
			WHERE a.id_empresa='$IDEmp' AND a.id_sucursal='$IDSuc' AND a.status='A' AND a.esdefault = 1 limit 1";			
		}else{
			$sql="SELECT id_almacen,nombre_almacen 
			FROM cat_almacenes
			WHERE id_empresa='$IDEmp' AND id_sucursal='$IDSuc'  AND esdefault = 1 AND status='A' limit 1";
		}
		// throw new Exception($sql);
		$model=new Model();
		$almacenes=$model->select($sql);
		//$arrAlmacenes[]=array(/*"id_almacen"=>0, "nombre_almacen"=>"TODOS LOS ALMACENES"*/);
		foreach($almacenes as $almacen)
			$arrAlmacen[0] = $almacen;

		return $arrAlmacen;
	}
	
	public function extraerTipoDeCambioDelDia(){
		$moneda=$_POST['moneda'];
		$fecha=$_POST['fecha'];
		$model=new Model();
				
		$arrCambio=$model->query("CALL extraerTipoDeCambioDelDia('$moneda','$fecha');");
		if(sizeof($arrCambio)==0){
			throw new Exception("No pudo obtenerse el tipo de cambio para la moneda:'$moneda' en la fecha:'$fecha'");
		}
		return array(
			'success'=>true,
			'data'=>$arrCambio[0]			
		);
	}
    public function actualizarTemaEnLaSesion(){

        $Parametros = new Parametros();
        $parametros = $Parametros->getActivo();
        $_SESSION['Auth']['Parametros'] = $parametros['Parametros'];


        if ($_SESSION['Auth']['User']['super'] == true) {
            $userConfig = array();
            
            $userConfig['temUsu'] = $parametros['Parametros']['tem_par'];
            $userConfig['forUsu'] = $parametros['Parametros']['tex_par'];
            $userConfig['super'] = true;
            $_SESSION['Auth']['UserConfig'] = $userConfig;
        } else {
            $query = "SELECT temUsu,forUsu FROM cat_usuarios WHERE IDUsu=" . $_SESSION['Auth']['User']['IDUsu'];
            $result = mysqlQuery($query);            
            $userConfig = mysql_fetch_array($result, MYSQL_ASSOC);
            $_SESSION['Auth']['UserConfig'] = $userConfig;
            $userConfig['super'] = true;
        }
        $parametros['User']['super'] = $_SESSION['Auth']['User']['super'];

    }
    
    /*Devuelve:
     *  la configuracion del usuario,
     * la configuracion del corporativo,
     * los datos del usuario logeado
     * nombre del corporativo
     * nombre e id de la empresa logeada
     * nombre e id dela sucursal logeada en caso de que aplico
     */
    public function getParametros() {
        //----------------------PARAMETROS DEL CORPORATIVO------------------------------//        
        $paramObject=new Parametros();
	
        $response=array();
        $query = "SELECT * FROM cat_parametros WHERE status='A'";
        $result = mysqlQuery($query);
        if (!$result) {
            $response['success'] = false;
            $response['msg'] = "Error al obtener los parametros: " . mysql_error();            
            return $response;
        }
        if (mysql_num_rows($result)==0){
			//SI NO EXISTEN DATOS, SE ESTABLECEN VALORES POR DEFAULT, ESTOS VALORES DEBEN ESTAR CONFIGURADOS 
			//EN EL ARCHIVO config.php			
			//$parametros['Parametros']['tem_par']	=TEMA;
			$parametros['Parametros']['tipo_texto']	=FORMATO_DE_TEXTO;
            $parametros['Parametros']['ciudad_default']=CIUDAD_ID;
			$parametros['Parametros']['pais_default']=PAIS_ID;
			$parametros['Parametros']['estado_default']=ESTADO_ID;
			$parametros['Parametros']['registros_pagina']=LIMITE_EN_PAGINACION;
        }else{
            $parametros['Parametros'] = mysql_fetch_array($result, MYSQL_ASSOC);
        }
		
        //----------------------USUARIO LOGEADO ---------------------------------//
        $parametros['User']['IDUsu'] = $_SESSION['Auth']['User']['IDUsu'];
        $parametros['User']['AdminUsu'] = $_SESSION['Auth']['User']['AdminUsu'];
        $parametros['User']['superUser'] = $_SESSION['Auth']['User']['super'];
        //-----------------------CORPORATIVO EMPRESA y SUCURSAL-----------------------------------------//

        $corporativo['NomCor']=$_SESSION['NomCor'];
        $empresa=array();
        $sucursal=array();
		$almacen=array();
		$turno=array();
		
        if (isset($_SESSION['Auth']['User']['id_empresa'])){
            $IDEmp=$_SESSION['Auth']['User']['id_empresa'];
			$IDSuc=$_SESSION['Auth']['User']['id_sucursal'];  			
            $empArr=$paramObject->getEmpresa($IDEmp);  
			if (sizeof($empArr)>0){ 
				$empresa=$empArr;  
						
				$sucursal=array();
				$sucursal['id_sucursal']=$_SESSION['Auth']['User']['id_sucursal'];
				$sucursal['nombre_sucursal']=$_SESSION['Auth']['User']['nombre_sucursal'];
				if($empArr['logotipo_sucursal'] == 1){
					$sucArr=$paramObject->getSucursal($_SESSION['Auth']['User']['id_sucursal']);
					$sucursal['logotipo_sucursal']= $sucArr['logotipo_sucursal'];
					$sucursal['logotipo']=$sucArr['logotipo'];
					
				}

				
				// $almacen['id_almacen']=$_SESSION['Auth']['User']['id_almacen'];
				// $almacen['nombre_almacen']=$_SESSION['Auth']['User']['nombre_almacen'];			
				
				
				if (isset($_SESSION['Auth']['User']['id_almacen'])){
					$almacen['id_almacen']=$_SESSION['Auth']['User']['id_almacen'];
					$almacen['nombre_almacen']=$_SESSION['Auth']['User']['nombre_almacen'];					
				}else{
					$almacenDefault=array();
					$almacenDefault=$this->seleccionarAlmacenDefault();
					if (sizeof($almacenDefault)>0){
						$almacen['id_almacen']=$almacenDefault[0]['id_almacen'];
						$almacen['nombre_almacen']=$almacenDefault[0]['nombre_almacen'];
						
						$_SESSION['Auth']['Almacen']=array();
						$_SESSION['Auth']['Almacen']['id_almacen']=$almacenDefault[0]['id_almacen'];
						$_SESSION['Auth']['Almacen']['nombre_almacen']=$almacenDefault[0]['nombre_almacen'];
						
						$_SESSION['Auth']['User']['id_almacen'] = $almacenDefault[0]['id_almacen'];
						$_SESSION['Auth']['User']['nombre_almacen'] = $almacenDefault[0]['nombre_almacen'];
					}else{
						$almacen['id_almacen']=0;
						$almacen['nombre_almacen']='SIN ALMACEN';	
					}
						
				}
			}                        
        }else{
            $empresa['id_empresa']=0;
            $empresa['nombre_fiscal']='SIN EMPRESA';
            $sucursal['id_sucursal']=0;
            $sucursal['nombre_sucursal']='SIN SUCURSAL';
			$almacen['id_almacen']=0;
            $almacen['nombre_almacen']='SIN ALMACEN';			
        }
        $parametros['Corporativo']=$corporativo;
		 global $RFC_CustomIva;
		 $empresa['RFC_CustomIva']=$RFC_CustomIva;
		if ($empresa){
			$parametros['Empresa'][]=$empresa;
		}
        if ($sucursal){
			$parametros['Sucursal'][]=$sucursal;
		}
		if ($almacen){
			$parametros['Almacen'][]=$almacen;
		}
		if ($turno){
			$parametros['Turno'][]=$turno;
		}
        //--------------------PARAMETROS DEL USUARIO--------------------------------------//
        if ($_SESSION['Auth']['User']['super']==true){
            $userConfig=array();
            $userConfig['forUsu']=$parametros['Parametros']['tipo_texto'];
            $userConfig['emaUsu']=$_SESSION['Auth']['User']['emaUsu'];
			 $userConfig['nomUsu']=$_SESSION['Auth']['User']['NomUsu'];
            $parametros['UserConfig'] = $userConfig;
        }else{
            $userConfig=array();
            $userConfig['forUsu']=$parametros['Parametros']['tipo_texto'];
            $userConfig['emaUsu']=$_SESSION['Auth']['User']['emaUsu'];
			 $userConfig['nomUsu']=$_SESSION['Auth']['User']['NomUsu'];
            $parametros['UserConfig'] = $userConfig;
        }
        /*CIUDAD ESTADO Y PAIS*/

        $ciudadId = $parametros['Parametros']['ciudad_default'];
        $paisId = $parametros['Parametros']['pais_default'];
        $estadoId = $parametros['Parametros']['estado_default'];
        $ciudadModel=new CiudadModel();            
        $ciudad=$ciudadModel->getCiudadEstadoYpais($ciudadId,$estadoId,$paisId);
		if (empty($ciudad)){
			throw new MyException('Revise la configuración de la localidad o consulte con el administrador del sistema',
							'Error obteniendo la Localidad Configurada');
			//
		}
        $parametros['Parametros']['ciudad']=$ciudad[0];
        
		
		//=======================================================================================
		//		ACCESO PROHIBIDO
		//
		$prohibidosJs=$this->prohibidosJs();
		$parametros['prohibidos']=$prohibidosJs;
		//=======================================================================================
		// Para alertar de caducidad de certificado
		//=======================================================================================
		
		// $turnoModel=new TurnoModel();            
        // $turno=$turnoModel->getTurno($ciudadId,$estadoId,$paisId);
		 // $parametros['Parametros']['Turno']=$turno[0];
		
        return $parametros;
    }
	
	function infoCaducidadDelCertificado(){
		$IDEmp = $_SESSION['Auth']['User']['IDEmp'];
		$IDSuc = $_SESSION['Auth']['User']['IDSuc'];
		
		$model=$this->getModelObject();
		
		if (!isset( $_SESSION['Auth']['User']['IDEmp']) ){
			return array(
				'expirando'=>0,
				'dif'=>0
			);
		}
		if ($IDSuc==0){
			$query = 'SELECT IF(FecExpCer< date_add( NOW(),INTERVAL  7 DAY ),1,0) AS expirando,
			DATEDIFF( FecExpCer, now() ) as dif
			FROM cat_empresas
			LEFT JOIN cat_certificados ON(KEYEmpCer = IDEmp)  
			WHERE IDEmp = '.$IDEmp.' AND DefaultCer = 1;';
		}else{
			$query = 'SELECT  IF(FecExpCer<date_add(NOW(),INTERVAL 7 DAY ),1,0) AS expirando,
			DATEDIFF( FecExpCer, now() )  dif
			FROM cat_sucursales 
			LEFT JOIN cat_empresas ON(KEYEmpSuc = IDEmp)  
			LEFT JOIN cat_certificados_sucursales ON(KEYSucCerSuc = IDSuc) 
			LEFT JOIN cat_certificados ON(KEYCerCerSuc = IDCer)  WHERE IDSuc = '.$IDSuc.' AND DefaultCerSuc = 1;';
		}
		 
		$result = $model->query($query);
		if ($result == null){
			$CertInfo =array();
		}else{
			$CertInfo =$result[0];
		}
				
		return $CertInfo;
	}
    
	function seleccionarEmpresa(){
        $empresaId=$_POST['IDEmp'];
        
        $paramModel=new Parametros();
        $usuario=$_SESSION['Auth']['User']['IDUsu'];
        $response=array();
		 // throw new Exception($empresaId);
        
            if ($_SESSION['Auth']['User']['AdminUsu']==1 || $_SESSION['Auth']['User']['AdminUsu']==2){
                $empresa=$paramModel->getEmpresa($empresaId);
            }else{
				// throw new Exception('1');
                $empresa=$paramModel->obtenerEmpresasConPermiso($usuario,$empresaId);
            }
            
            $response['msg']=array(
            	'titulo'=>"Empresa seleccionada",
            	'mensaje'=>''
            );
			
		unset($_SESSION['Auth']['Sucursal']);
        unset($_SESSION['Auth']['Almacen']);
        $_SESSION['Auth']['Empresa']=$empresa;
        
        
		
        $response['success']=true;
        $response['data']=array();
        $response['data']['Empresa'][]=$empresa;
       
		
		//$response['data']['Certific']=$certif;


        /*  POR COMPATIBILIDAD HABRIA QUE VER DONDE SE USAN ESTOS PARAMETROS PARA DEJAR DE USARLOS Y USAR EL ESQUEMA ANTERIOR  */

   
        
        $_SESSION['Auth']['User']['id_empresa'] = $empresa['id_empresa'];
		$_SESSION['Auth']['User']['nombre_fiscal'] = $empresa['nombre_fiscal'];
		$_SESSION['Auth']['User']['id_sucursal'] = 0;
		$_SESSION['Auth']['User']['nombre_sucursal'] = '';
		$_SESSION['Auth']['User']['id_almacen'] = 0;
		$_SESSION['Auth']['User']['nombre_almacen'] = '';
		 
		
        return $response;
    }
	
	function seleccionarSucursal(){
      
        $sucursalId=$_POST['IDSuc'];
        $paramModel=new Parametros();
        $usuario=$_SESSION['Auth']['User']['IDUsu'];
        $response=array();
		 $almacenDefault=array();
     
      
            
            if ($_SESSION['Auth']['User']['AdminUsu']==1 || $_SESSION['Auth']['User']['AdminUsu']==2){
                $sucursal=$paramModel->getSucursal($sucursalId);
                /*$almDef=$paramModel->getAlmacenDefault($sucursalId);
				
				if (sizeof($almDef)>0){
					$almacenDefault=array();
					$almacenDefault =  $almDef;	
				}
				*/		
            }else{
				 $sucursal=$paramModel->getSucursal($sucursalId);
                
                // $sucursal=$paramModel->getSucursalConPermiso($usuario,$sucursalId);
            }
            $response['msg']=array(
            'titulo'=>"Sucursal seleccionada",
            'mensaje'=>'');
			
      
       unset($_SESSION['Auth']['Almacen']);
       $_SESSION['Auth']['Sucursal']=$sucursal;
	   //if($almacenDefault)
	   // $_SESSION['Auth']['Almacen']=$almacenDefault;
        
		
        $response['success']=true;
        $response['data']=array();
        $response['data']['Sucursal'][]=$sucursal;
		//if($almacenDefault)
		// $response['data']['Almacen'][]=$almacenDefault;
		
		 
     
        $_SESSION['Auth']['User']['nombre_sucursal'] = $sucursal['nombre_sucursal'];
        $_SESSION['Auth']['User']['id_sucursal'] = $sucursal['id_sucursal'];
		$_SESSION['Auth']['User']['nombre_almacen'] = '';
        $_SESSION['Auth']['User']['id_almacen'] = 0;
		
		
         // $response['data']['CertInfo']=$this->infoCaducidadDelCertificado();
		
        return $response;
    }
	
	public function seleccionarAlmacen(){
		
		$IDAlmacen=$_POST['IDAlm'];
		$NomAlmacen=$_POST['NomAlm'];
        $paramModel=new Parametros();
       
        $response=array();
     
             
		if (empty($_POST['IDAlm']))
			if($_POST["IDAlm"])
				throw new Exception("Almacén desconocido");
		if (empty($_POST['NomAlm']))throw new Exception("Almacén desconocido");
		
		$_SESSION['Auth']['Almacen']=array();
		$_SESSION['Auth']['Almacen']['id_almacen']=$IDAlmacen;
		$_SESSION['Auth']['Almacen']['nombre_almacen']=$NomAlmacen;
		
		$_SESSION['Auth']['User']['id_almacen'] = $IDAlmacen;
        $_SESSION['Auth']['User']['nombre_almacen'] = $NomAlmacen;
		
		 
		$almacen=array();
		$almacen['id_almacen']=$IDAlmacen;
		$almacen['nombre_almacen']=$NomAlmacen;
		
       
        $response['success']=true;
		$response['msg']=array(
            'titulo'=>'Sistema',
            'mensaje'=>'Almacén seleccionado');
        $response['data']=array();
        $response['data']['Almacen'][]=$almacen;
		
				
        return $response;
	}
	
	public function obtenerempresas(){
        if ($_SESSION['Auth']['User']['super']==true){
                $tipoUser =2;
        }else{
            $tipoUser = $_SESSION['Auth']['User']['AdminUsu'];
        }
        $userId = $_SESSION['Auth']['User']['IDUsu'];
        $empresaModel=new Empresa();
        
        switch ($tipoUser) {
            case 2: //Super User
                $empresas = $empresaModel->obtenerTodasLasEmpresas();
                break;
            case 1: //Admin
                $empresas = $empresaModel->obtenerTodasLasEmpresas();
                break;
            default:    //User
                $empresas = $empresaModel->obtenerEmpresasConPermiso($userId);
                break;
        }
        $response['success']=true;
        $response['data']=$empresas;
        return $response;
    }
	
	public function obtenersucursales(){
		$IDEmp=$_SESSION['Auth']['User']['id_empresa']; 
	   if ($_SESSION['Auth']['User']['super']==true){
                $tipoUser =2;
        }else{
            $tipoUser = $_SESSION['Auth']['User']['AdminUsu'];
        }
        $userId = $_SESSION['Auth']['User']['IDUsu'];
        $sucursalModel=new Sucursal();
        // throw new Exception($userId);
        switch ($tipoUser) {
            case 2: //Super User
                $sucursales = $sucursalModel->obtenerTodasLasSucursales($IDEmp);
                break;
            case 1: //Admin
                $sucursales = $sucursalModel->obtenerTodasLasSucursales($IDEmp);
                break;
            default:    //User
                $sucursales = $sucursalModel->obtenerSucursalesConPermiso($userId,$IDEmp);
                break;
        }
        $response['success']=true;
        $response['data']=$sucursales;
        return $response;
    }
	
	public function getInfoCertificado(){
		$id_suc = $_POST['idSuc'];
		$id_emp = $_POST['idEmp'];
		$paramModel = new Parametros();
		if (is_numeric($id_suc) && $id_suc>0){
			$certif = $paramModel->getInfoCertificado($id_suc, 'SUC');
		} else if(is_numeric($id_emp) && $id_emp>0){
			$certif = $paramModel->getInfoCertificado($id_emp, 'EMP');
		}else{
			$certif=array();	
		}
		
		$certif['chat']=$this->getStatusDelChat();
		return $certif;
	}
	
	public function getStatusDelChat(){		
		/*
	 	El ChatOn debe estar habilitado en los siguientes horarios	
		
		Lunes a viernes
		9:00-14:00 16:00-19:00
		
		Sábado
		9:00-14:00
		
		Fuera de ese horario debe estar en ChatOff (Desabilitado)
	
	
	 	* */	
		
		date_default_timezone_set (CUSTOM_TIMEZONE);		
		$tz=CUSTOM_TIMEZONE;
		$dtzone = @new DateTimeZone($tz);
 
		// first convert the timestamp into a string representing the local time
		$time = date('r', time());
		 
		// now create the DateTime object for this time
		$dtime = @new DateTime($time);
		 
		// convert this to the user's timezone using the DateTimeZone object
		$dtime->setTimeZone($dtzone);

		// print the time using your preferred format
		$hora = $dtime->format('H');
		//$hora = date ('H',time());
		$minutos=$dtime->format('i');
		//$minutos=date ('i',time());
		$mes=$dtime->format('m');
		//$mes=date ('m',time());
		$dia=$dtime->format('d');
		//$dia=date ('d',time());
		$año=$dtime->format('Y');
		//$año=date ('Y',time());
		$hora=intval ($hora);
		$minutos=intval($minutos);


		$diaDeLaSemana=jddayofweek ( cal_to_jd(CAL_GREGORIAN,$mes,$dia, $año) , 0 ); // http://mx.php.net/jddayofweek

		$activarChat=false;
		if($diaDeLaSemana==0){	//Domingo ChatOff
			$activarChat = false;
		}else if($diaDeLaSemana==6){	//Sabado
			if ($hora>8 && $hora<15){
				if ($hora==14 && $minutos>0){
					$activarChat = false;
				}else{					
					$activarChat = true;
				}
			}else{
				$activarChat = false;
			}
		}else if($diaDeLaSemana>0 && $diaDeLaSemana<6){ //Lunes a Viernes
			if ($hora>8 && $hora<15){
				if ($hora==14 && $minutos>0){
					$activarChat = false;
				}else{
					$activarChat = true;
				}
			}else if ($hora>15 && $hora<19){
				if ($hora==19 && $minutos>0){
					$activarChat = false;
				}else{
					$activarChat = true;
				}
			}else{
				$activarChat = false;
			}
		}
		$fecha="$dia/$mes/$año $hora:$minutos";
		
	
		
		 return array(
		 	'activarChat'=>$activarChat,
		 	'fecha'=>$fecha,
		 	'diaDeLaSemana'=>$diaDeLaSemana,
		 	'hora'=>$hora,
		 	'minutos'=>$minutos,
			'otraFecha'=>date('d/m/Y H/i/s',time()),
			'timeZone'=>date_default_timezone_get()
		 );
	}
	
	public function getInfoFolios(){
		//print_r( $_SESSION );exit;
		$id_suc = $_POST['idSuc'];
		$id_emp = $_POST['idEmp'];
		$paramModel = new Parametros();
		if (is_numeric($id_emp) && is_numeric($id_suc)){
			$folios = $paramModel->getInfoFolios($id_emp, $id_suc);
		}else{
			$folios =array();
		}
		return $folios;
	}
	
	public function verificasesion(){
		
		return true;
	}
	
	public function getAuditoria(){
		/*
			Modulos
			1=VENTAS,2=INVENTARIO,3=REMISIONES,4=ABONOS,5=MOVIMIENTOS BANCOS
			
		
		*/
		$id_modulo = $_POST['id_modulo'];
		
		switch($id_modulo){
			case 1:
				
				$datos=array();	
				
				break;
			case 2:
				$datos=array();			
				break;
			case 3:
				$model = new RemisionModel();
				
				$datos=array();		
				
				break;
			
			default:
				throw new Exception("El Modulo es desconocido");
		}	
		
		
		return $datos;
		
		
	}
}
?>
