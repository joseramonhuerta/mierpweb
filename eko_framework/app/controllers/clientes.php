<?php
require ('eko_framework/app/models/cliente.php');       //MODELO
// require ('eko_framework/app/models/razon_social.php');
require ('eko_framework/app/models/ciudad.php');
require ('eko_framework/app/models/pais.php');
require ('eko_framework/app/models/lista_precio.php');
require ('eko_framework/app/models/cliente_categoria.php');
require_once "eko_framework/app/models/reporte_ventas_clientes.php";
require_once "eko_framework/app/models/reporte_cartera_clientes.php";
require_once "eko_framework/app/models/reporte_clientes_categorias.php";
require_once "eko_framework/app/models/reporte_clientes_categorias_excel.php";
class Clientes extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerclientes(){ //<----------------PARA EL GRID

            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		      
            $clienteModel=new ClienteModel();
            $response = $clienteModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	function guardar(){
       
        $empresa=array();
        $response=array();
        
        // $cliente=$_POST['Cliente'];
		//--------------------
		//	Validar Email
		//-----------
		$emails=$_POST['email_contacto'];
		$emailsArr= explode (',' , $emails);
		//echo print_r($emailsArr);
		$numEmail=sizeof($emailsArr);
		//$validos="";
		for($i=0;$i<sizeof($emailsArr);$i++){
			$email=trim($emailsArr[$i]);
			if ($email!=''){ 
				if (!$this->check_email_address($email)){
					$response=array(
						'success'=>false,
						'message'=>array(
							'titulo'=>"Email inválido",
							'Mensaje'=>"El email $email ubicado en la posición $i es inválido."
						)
					);				
					$posicion=$i+1;
					throw new Exception("El email $email ubicado en la posición $posicion es inválido.");
				}
				//$validos[]=$email;
			}
		}
			
			$Cliente=array(
			'id_cliente'=>$_POST['id_cliente'],
			'nombre_fiscal'=>$_POST['nombre_fiscal'],
			'nombre_comercial'=>$_POST['nombre_comercial'],
			'rfc_cliente'=>$_POST['rfc_cliente'],
			'tipo_cliente'=>$_POST['tipo_cliente'],
			'estilista'=>$_POST['estilista'],
			'foraneo'=>$_POST['foraneo'],
			'calle'=>$_POST['calle'],
			'numext'=>$_POST['numext'],
			'numint'=>$_POST['numint'],
			'colonia'=>$_POST['colonia'],
			'cp'=>$_POST['cp'],
			'localidad'=>$_POST['localidad'],
			'id_ciu'=>$_POST['id_ciu'],
			'id_est'=>$_POST['id_est'],
			'id_pai'=>$_POST['id_pai'],
			'nombre_contacto'=>$_POST['nombre_contacto'],
			'calle_contacto'=>$_POST['calle_contacto'],	
			'numext_contacto'=>$_POST['numext_contacto'],
			'numint_contacto'=>$_POST['numint_contacto'],
			'colonia_contacto'=>$_POST['colonia_contacto'],
			'cp_contacto'=>$_POST['cp_contacto'],
			'localidad_contacto'=>$_POST['localidad_contacto'],
			'email_contacto'=>$_POST['email_contacto'],
			'telefono_contacto'=>$_POST['telefono_contacto'],
			'celular_contacto'=>$_POST['celular_contacto'],
			'status'=>$_POST['status'],
			'id_listaprecio'=>$_POST['id_listaprecio'],
			'id_cliente_categoria'=>$_POST['id_cliente_categoria']	
			);							
		
            $clienteModel=new ClienteModel();			
			
            $clienteGuardado=$clienteModel->guardar($Cliente);
            if (!$clienteGuardado)throw new Exception("Error al guardar los datos del cliente");
            
            $response['success'] = true;
            $response['msg'] = array('titulo'=>'Clientes','mensaje'=> 'La información del Cliente ha sido guardada satisfactoriamente') ;            
            $response['data']['Cliente']= $clienteGuardado; 
			
			$ciudadId=$clienteGuardado['id_ciu'];
            $paisId=$clienteGuardado['id_pai'];
            $estadoId=$clienteGuardado['id_est'];
			$listaprecioId=$clienteGuardado['id_listaprecio'];
            $ciudadModel=new CiudadModel();
			
            if (is_numeric($ciudadId)){
                $ciudad=$ciudadModel->getCiudadEstadoYpais($ciudadId,$estadoId,$paisId);               
            }
			$response['data']['Ciudad'] = $ciudad[0];
			
			$listaPrecioModel=new ListaPrecioModel();
			$listaprecio=$listaPrecioModel->readAll(0, 200, ''); 

			$clienteCategoriaModel=new ClienteCategoriaModel();
			$clientescategorias=$clienteCategoriaModel->readAll(0,200,'');
			
			
			//$response['data']['Unidades']=$unidades['data'];
			$response['data']['ListaPrecios']=$listaprecio[0];	
			$response['data']['ClientesCategorias']=$clientescategorias['data'];
			
         

        return $response;
    }

	function obtenercliente(){
		$id=$_POST['idCli'];
		$clienteModel=new ClienteModel();
		$datos=$clienteModel->getcliente($id);
		// $response['success']=	true;    
		// $response['data']=	$cliente;          
        // return $response;
		// $datos = $razonModel->getById($id);
            $response['success'] = true;
            $response['data']['Cliente'] = $datos['Cliente'];

            $ciudadId=$datos['Cliente']['id_ciu'];
            $paisId=$datos['Cliente']['id_pai'];
            $estadoId=$datos['Cliente']['id_est'];
			$listaprecioId=$datos['Cliente']['id_listaprecio'];
            $ciudadModel=new CiudadModel();
            if (is_numeric($ciudadId)){
                $ciudad=$ciudadModel->getCiudadEstadoYpais($ciudadId,$estadoId,$paisId);               
            }
			$response['data']['Ciudad'] = $ciudad[0];

			$listaPrecioModel=new ListaPrecioModel();
				 
			if (is_numeric($listaprecioId)){
				$listaprecio=$listaPrecioModel->readAll(0, 200, ''); 
			}

			$response['data']['ListaPrecio'] = $listaprecio[0];
			return $response;  
			
		}

	function eliminar(){
		 $clienteModel=new ClienteModel();
		$titulo=$clienteModel->name;
		
		if ( empty($_POST['id_cliente']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia al Almacén que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_cliente'];	
	
		$clienteModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="El Cliente no fue eliminado";
		}else{
			$success=false;
			$mensaje="Cliente eliminado de la base de datos";
		}	
		$data=array('id_cliente'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Clientes',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_cliente'];
   
		
		$statusOld=$_POST['status'];			
		if ($statusOld=='A'){
			$nuevoStatus="I";
		}else if ($statusOld=='I'){
			$nuevoStatus="A";
		}else{
			return array(
				'success'=>false,
				'msg'=>array(
					'titulo'=>'Error en la peticion de cambio de status',
					'mensaje'=>"El estado (<span style='font-weight:bold;'>$statusOld</span>) es desconocido por el sistema."
				)				
			);
		}
		
		
        $query="UPDATE cat_clientes SET status='$nuevoStatus' WHERE id_cliente=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_cliente'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Clientes',
					'mensaje'=>"Error al actualizar el estado del Cliente:".mysql_error()
				);
        }else{
            $response['success'] = true;
            $estado='';
            if ($nuevoStatus=="I"){
                $estado="Desactivado";
            }else{
                $estado="Activado";
            }
            $response['msg'] = array(
					'titulo'=>'Clientes',
					'mensaje'=>"El Cliente ha sido $estado"
				);
			
			$response['data'] = $data;
        }
		
        return $response;
	}

	function generarreportecartera(){
		$params = $_POST;
		$reporte=new ReporteCarteraClientes();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repCar']['rand']=$numero_aleatorio ;
		$_SESSION['repCar']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;		
	}
	
	function getpdfreportecartera(){		
		if (!isset($_SESSION['repCar'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repCar']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repCar']['pdf'];
		
		$reporte=new ReporteCarteraClientes();
		$reporte->getPDF($pdfName);
	}
	
	function generarreporteventasclientes(){
		$params = $_POST;
		$reporte=new ReporteVentasClientes();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repVen']['rand']=$numero_aleatorio ;
		$_SESSION['repVen']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;		
	}
	
	function getpdfreporteventasclientes(){		
		if (!isset($_SESSION['repVen'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repVen']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repVen']['pdf'];
		
		$reporte=new ReporteVentasClientes();
		$reporte->getPDF($pdfName);
	}
	
	function obtenerclientescombo(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			$filtro="";
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_fiscal'));
			// throw new Exception($filtro_query);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A'";
			
			$query = "SELECT COUNT(id_cliente) AS totalrows FROM cat_clientes $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 50 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_cliente,nombre_fiscal FROM cat_clientes $filtro ";
			$query.= " ORDER BY id_cliente LIMIT $start, $limit ";
			
			// throw new Exception($query);
			$res = mysqlQuery($query);
			if (!$res)  throw new Exception(mysql_error()." ".$query);
				
			$response = ResulsetToExt::resToArray($res);
			$response['totalRows'] = $total_rows;
		} catch (Exception $e) {
			$response['totalRows'] = $total_rows;
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		echo json_encode($response);
		
	}	
	
	function filtroToSQL($filtro,$camposAfiltrar=array()) {
     	 $where = '';
     	 
        if (!empty($filtro)) {
			$filtroArray = explode(" ", $filtro);
	        $condiciones = "";
	        $condicion = "";

	        foreach ($camposAfiltrar as $campo) {
	
	            foreach ($filtroArray as $text) {
	                if (strlen($text) > 0){
						$condicion.="$campo LIKE '%$text%' AND ";	 									
					}
	            }
	
	            if (strlen($condicion) > 0) {
	                $condicion = substr($condicion, 0, strlen($condicion) - 4); //<----LE BORRO LA ULTIMA PARTE "AND ";
	                $condicion = "(" . $condicion . ") OR ";
	                $condiciones.=$condicion;
	                $condicion = "";
	            }
	        }
	       
	        if (strlen($condiciones) > 0) {
	            $condiciones = substr($condiciones, 0, strlen($condiciones) - 3); //<----LE BORRO LA ULTIMA PARTE "or ";
	            $where = "WHERE ($condiciones)";
	        }
        }
        return $where;
    }	

	function obtenerlistasprecios(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro = (isset($_POST['query'])) ? $this->filtroToSQL($_POST['query']) : '';
			// $filtro = $this->filtroToSQL($_POST['query'],);
			$filtro=$this->filtroToSQL($filtro_query,array('descripcion'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A' ";
			
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_listaprecio) AS totalrows FROM cat_listaprecios $filtro ";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_listaprecio,descripcion AS descripcion_listaprecio, status FROM cat_listaprecios $filtro ";
			$query.= " ORDER BY id_listaprecio LIMIT $start, $limit ";
			$res = mysqlQuery($query);
			if (!$res)  throw new Exception(mysql_error()." ".$query);
				
			$response = ResulsetToExt::resToArray($res);
			$response['totalRows'] = $total_rows;
		} catch (Exception $e) {
			$response['totalRows'] = $total_rows;
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		echo json_encode($response);
	}

	function obtenercategorias(){
		try {
			$filtro_query= ( empty($_POST['query']) )? '' : $_POST['query']; 
			// $filtro = (isset($_POST['query'])) ? $this->filtroToSQL($_POST['query']) : '';
			// $filtro = $this->filtroToSQL($_POST['query'],);
			$filtro=$this->filtroToSQL($filtro_query,array('nombre_categoria'));
			// throw new Exception($filtro);		
			$filtro.= ($filtro) ? " AND status = 'A'" : " WHERE status = 'A' ";
			
			$query = "SELECT COUNT(id_cliente_categoria) AS totalrows FROM cat_clientes_categorias $filtro ";
			
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
				
			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
			$start = (empty($_POST['start'])) ?  0 : $_POST['start'];
				
			$query = " SELECT id_cliente_categoria,nombre_categoria, status FROM cat_clientes_categorias $filtro ";
			$query.= " ORDER BY id_cliente_categoria LIMIT $start, $limit ";
			$res = mysqlQuery($query);
			if (!$res)  throw new Exception(mysql_error()." ".$query);
				
			$response = ResulsetToExt::resToArray($res);
			$response['totalRows'] = $total_rows;
		} catch (Exception $e) {
			$response['totalRows'] = $total_rows;
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		echo json_encode($response);
	}

	function generarreporteclientescategorias(){
		$params = $_POST;
		$reporte=new ReporteClientesCategorias();
		
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
		$pdf = '';
		$pdf=$reporte->generarReporte($params,$formatos);
		mt_srand (time());
		
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repExis']['rand']=$numero_aleatorio ;
		$_SESSION['repExis']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
		
		
		
		
	}
	
	function getpdfclientescategorias(){		
		if (!isset($_SESSION['repExis'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repExis']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repExis']['pdf'];
		
		$reporte=new ReporteClientesCategorias();
		$reporte->getPDF($pdfName);
	}
	
	function generarreporteclientescategoriasexcel(){
		$params = $_GET;
		$reporte=new ReporteClientesCategoriasExcel();
		
		$pdf=$reporte->generarReporteExcel($params);
	}

}
?>
