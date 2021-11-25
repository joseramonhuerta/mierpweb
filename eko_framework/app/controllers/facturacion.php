<?php
/*OJO EL IMPUESTO IVA EN LA BASE DE DATOS DEBE TENER EL ID 1 Y EL IEPS EL 2, POR EL MOMENTO EStAN AMARRADOS... me amarraron como puerco!!!	*/

require_once ('eko_framework/app/models/facturacion.php');

//require_once ('eko_framework/app/models/claseFacturaXML_ene_2012.php'); //Version 2.2 y 3.2
//require_once ('eko_framework/app/models/claseFacturaXML.php');		//Version 2.0 y 3.0

require_once ('eko_framework/lib/rmkdir_r.php');       //CREAR DIRECTORIOS
require_once ('eko_framework/app/models/ciudad.php');       //MODELO
require_once ('eko_framework/app/models/cliente.php');
require_once ('eko_framework/app/models/GetCFDI.php');
require_once ('eko_framework/app/models/razon_social.php');
require_once ('eko_framework/app/models/certificado.php');
require_once ('eko_framework/includes/generaPDF.php');
require_once ('eko_framework/lib/validador/Validador.php');
require_once('eko_framework/includes/zip.class.php');
//include "eko_framework/includes/curl.php";	//Para simular la llamada HTTP
require_once "eko_framework/app/models/reporte_de_facturacion.php";	//
require_once "eko_framework/app/models/reporte_de_saldosfolios.php";	//
require_once "eko_framework/includes/phpmailer/mailer.php";	//
require_once "eko_framework/app/models/factura_email.php";	//

require_once ('eko_framework/includes/fpdf.php');
require_once ('eko_framework/includes/fpdi/fpdf_rotate.php');
require_once ('eko_framework/includes/fpdi/fpdi.php');
require_once ('eko_framework/includes/fpdi/FPDI_Protection.php');

require_once ('eko_framework/app/models/inventario_detalle_model.php');

class Facturacion extends ApplicationController {
	var $model='FacturacionModel';
	var $components=array(
		'ACL'=>array(
			'allowedActions'=>array('viewpdf','findProductos'))
	);
	
	function Facturacion(){
		$this->modelObject=new FacturacionModel(); 		
	}
	
	function getConceptosDeCancelacion(){
		$query=empty($_POST['query']) ? '': $_POST['query'] ;
		$arrQuery=explode(' ',$query);
		$filtro='';
		
		for($i=0; $i<sizeof($arrQuery); $i++){
			$filtro.=" DescCon LIKE '%".$arrQuery[$i]."%' AND";
		}
		$filtro=substr($filtro,0,strlen($filtro)-3);
		$filtro="AND ($filtro)";
		
		$sql="SELECT IDCon idCon,DescCon descripcion FROM cat_conceptos WHERE KEYTDCCon='CDF' $filtro; ";
		$data=$this->modelObject->query($sql);
		return array(
			'success'=>true,
			'data'=>$data
		);
	}
	
	function prepararCancelacion(){
		if ( !empty($_POST['IDFac']) && !empty($_POST['cancelada']) ){
			$idFac=$_POST['IDFac'];
			$sql="SELECT motivo_Cancelacion,KEY_Motivo_Cancelacion,Fecha_Cancelacion FROM facturacion_cancelaciones WHERE KEY_Fac_Cancelacion=$idFac";
			
			$arrDatos=$this->modelObject->select($sql);
			if ( empty($arrDatos) ){
				throw new Exception("No se encontraron los datos de cancelacion de la factura");
			}
			
			$fechaSQL=$arrDatos[0]['Fecha_Cancelacion'];			
			$fecha=date('d/m/Y',strtotime($fechaSQL));
			$hora=date('H:m A',strtotime($fechaSQL));
			$motivo=array(
				'idCon'=>$arrDatos[0]['KEY_Motivo_Cancelacion'],
				'descripcion'=>$arrDatos[0]['motivo_Cancelacion']
			);					
			$data=array(
				'fecha'=>$fecha,
				'hora'=>$hora,
				'motivo'=>$motivo
			);
		}else{			
			
			$fecha=date('d/m/Y');
			$hora=date('H:i A');
			$data=array(
				'fecha'=>$fecha,
				'hora'=>$hora
			);
		}
		
		return array(
			'success'=>true,
			'data'=>$data
		);
	}
	
	function cancelar(){
		//----------------------------------------------------------------------------
		$rutaModelo="eko_framework/app/models/factura_cancelacion.php";
		
		if(!@file_exists($rutaModelo) ) {
		    throw new Exception('Error Grave: Modelo de cancelación no encontado');
		} else {
		   include_once ($rutaModelo);       //MODELO
		}
		
		//----------------------------------------------------------------------------
		if ( !isset($_POST['IDFac']) || !is_numeric($_POST['IDFac']) ){
			throw new Exception("El identificador de la factura es inválido");
		}
		//----------------------------------------------------------------------------		
		$idFac=$_POST['IDFac'];
		$motivo=$_POST['motivo'];				
		//--------------------------------------------------------------------
		$hora=$_POST['hora'];
		$arrFecha=explode("T",$_POST['fecha']);
		if (!strtotime($_POST['fecha'])){
			throw new Exception("EL formato de fecha es incorrecto");
		}
		
		if ( empty($_POST['hora']) ){
			throw new Exception("Debe proporcionar una hora válida");
		}
		$fecha=$arrFecha[0];		
		
		$datetime="$fecha $hora";
		$datetime=strtotime($datetime);
		$datetime=date('Y-m-d H:i:s',$datetime);
		//--------------------------------------------------------------------		
		if ( empty($_POST['motivo']) ){
			$motivo=0;		
		}
		$facModel=new FacturaCancelacion();
		$response=$facModel->cancelar($idFac,$datetime,$motivo);
		$this->cancelarMovimientos($idFac);
		return array(
			'success'=>true,
			'data'=>array('status'=>'C','FecCan'=>$response),
			'msg'=>array(
				'titulo'=>'Facturación',
				'mensaje'=>'Factura Cancelada correctamente'
			)
		); 
	}
	
	function getZipAcuse(){
		//----------------------------------------------------------------------------				
		try{
			$rutaModelo="eko_framework/app/models/acuse_pdf.php";
			if(!@file_exists($rutaModelo) ) {
			    throw new Exception('Error Grave: Modelo de Acuse no encontado');
			} else {
				ob_start();	
			   include_once ($rutaModelo);       //MODELO
			   ob_end_clean();
			}
			
			$rutaZipClass="eko_framework/includes/zip.class.php";
			
			if(!@file_exists($rutaZipClass) ) {
				
			    throw new Exception('Error Grave: Libreria Zip no encontada');
			} else {
				ob_start();	
			   include_once($rutaZipClass);
			   ob_end_clean();
			}
			//----------------------------------------------------------------------------
			if(!isset($_REQUEST['identificador']) || !is_numeric($_REQUEST['identificador'])){
				throw new Exception("Identificador inválido");
			}
			
			$idFac=$_REQUEST['identificador'];
			$facModel=new AcusePDF();
		
			$zipName=$facModel->getAcuseEnZIP($idFac);	
		}catch(Exception $e){
			$error="ERROR: Factura: ".$idFac.". ".$e->getMessage();
			generaLog("ACUSE",$error);
			throw new Exception($e->getMessage());
		}
					
		$zip="tmp/$zipName";
		
		ob_end_clean();

		header ("Content-Disposition: attachment; filename=$zipName");
		header ("Content-Type: application/force-download");
		header ("Content-Length: ".filesize($zip));
		$fp=fopen($zip, "r");
		fpassthru($fp);		
		unlink($zip);
		exit;
	}

	function getAcuseEnPDF(){
		//----------------------------------------------------------------------------
		$rutaModelo="eko_framework/app/models/acuse_pdf.php";
		try{
			if(!@file_exists($rutaModelo) ) {
			    throw new Exception('Error Grave: Modelo de Acuse no encontado');
			} else {
			   include_once ($rutaModelo);       //MODELO
			}
			//----------------------------------------------------------------------------
			if(!isset($_REQUEST['identificador']) || !is_numeric($_REQUEST['identificador'])){
				throw new Exception("Identificador inválido");
			}
			
			$idFac=$_REQUEST['identificador'];
			$facModel=new AcusePDF();
		
			$pdfName=$facModel->getAcuseEnPDF($idFac);	
		}catch(Exception $e){
			$error="ERROR: Factura: ".$idFac.". ".$e->getMessage();
			generaLog("ACUSE",$error);
			throw new Exception($e->getMessage());
		}
		
			
		$pdf="tmp/$pdfName";

		header ("Content-Disposition: attachment; filename=$pdfName");
		header ("Content-Type: application/force-download");
		header ("Content-Length: ".filesize($pdf));
		readfile($pdf);
		unlink($pdf);
		exit;	
	}
	
	
	function pagar(){
		
		if (!isset($_POST['IDFactura']) || !isset($_POST['fechaPago'] )){
			$response['success']=false;
			$response['msg']=array(
				'titulo'=>"Facturación",
				'mensaje'=>"Informacion de pago incompleta"
			);
			return $response;
		}		
				
		$params=array(
			'IDFac'=>$_POST['IDFactura'],
			'fechaPago'=>$_POST['fechaPago']		
		);
		
		$fechaPago=$this->modelObject->pagar($params);
		$response[]=array();
		
		$response['success']=true;
		$response['msg']=array(
			'titulo'=>'Facturacion',
			'mensaje'=>'Factura Pagada'
		);
		$response['data']=array(
			'fechaPago'=>$fechaPago
		);
		return $response;		
	}
	
	function find(){  //<----------------PARA EL GRID
		
        $params= $_POST;
		if ($params['IDEmp']=='' || !isset($params['IDEmp'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar sus Facturas correspondientes");
		}
        $modelObject=new FacturacionModel();        
        $response = $modelObject->find($params);		
        return $response;
    }
	
	function findClientes(){
        $modelObject=$this->modelObject;
        $query=$_POST['query'];
  		 $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
         $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
         
         $queryCount="SELECT COUNT(IDCliDet) as total FROM cat_clientes_detalle d
            LEFT JOIN cat_clientes c ON c.IDCli=d.KEYCli WHERE (c.NomCli LIKE '%$query%' OR d.RazSocCliDet LIKE '%$query%') AND c.StatusCli='A'";
         $arrTotal=$modelObject->query($queryCount);
         
        $query="SELECT IDCliDet,RFCCliDet,RazSocCliDet,CalleCliDet,NumExtCliDet,NumIntCliDet,ColCliDet,IDCli,NomCli
            FROM cat_clientes_detalle d
            LEFT JOIN cat_clientes c ON c.IDCli=d.KEYCli WHERE (c.NomCli LIKE '%$query%' OR d.RazSocCliDet LIKE '%$query%') AND c.StatusCli='A'
            ORDER BY IDCli,c.NomCli,d.RazSocCliDet limit $start,$limit;";
	//		throw new Exception($query);
        $clientes=$modelObject->query($query);
        $response=array();
        $response['success']=true;
        $response['data']=$clientes;
        $response['total']=$arrTotal[0]['total'];
        return $response;
    }
    
	function findClientesParaReporte(){
        $modelObject=$this->modelObject;
        $query=$_POST['query'];
        
  		 $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
         $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
         
         $queryCount="SELECT COUNT(IDCliDet) as total FROM cat_clientes_detalle d
            LEFT JOIN cat_clientes c ON c.IDCli=d.KEYCli WHERE (c.NomCli LIKE '%$query%' OR d.RazSocCliDet LIKE '%$query%') AND c.StatusCli='A'";
         $arrTotal=$modelObject->query($queryCount);
         
        $query="SELECT CONCAT(IDCli,'-',IDCliDet) as idConcat,IDCliDet,RFCCliDet,RazSocCliDet,CalleCliDet,NumExtCliDet,NumIntCliDet,ColCliDet,IDCli,NomCli
            FROM cat_clientes_detalle d
            LEFT JOIN cat_clientes c ON c.IDCli=d.KEYCli WHERE (c.NomCli LIKE '%$query%' OR d.RazSocCliDet LIKE '%$query%') AND c.StatusCli='A'
            ORDER BY IDCli,c.NomCli,d.RazSocCliDet limit $start,$limit;";
		//	throw new Exception($query);

        $clientes=$modelObject->query($query);
        $idCliAnterior=0;
        $arrClienteAgregado=array();
        $totalRows=$arrTotal[0]['total'];
        
        for($i=0;$i<sizeof($clientes);$i++){
        	$idCli=$clientes[$i]['IDCli'];
        	if ($idCli!=$idCliAnterior){
        		$arrClienteAgregado[]=array(
        			'idConcat'=>$idCli.'-0',
        			'IDCli'=>$idCli,
        			'NomCli'=>$clientes[$i]['NomCli'],
        			'IDCliDet'=>0,
        			'RazSocCliDet'=>$clientes[$i]['NomCli']
        		);  
        		$totalRows++;
        		$idCliAnterior=$idCli; 		
        	};
        	$arrClienteAgregado[]=$clientes[$i];        	
        }
        
        $response=array();
        $response['success']=true;
        $response['data']=$arrClienteAgregado;
        $response['total']=$totalRows;
        return $response;
    }
    
    function getCliente(){
       $IDCliDet=$_POST['IDCliDet'];
       $cliente=$this->modelObject->getCliente($IDCliDet);
       $ciudadModel=new CiudadModel();
	   if (!is_numeric($cliente['CiuCliDet'])){
			$IDRazSoc=$IDCliDet;
			
			$query="SELECT PaisCliDet id_pai,EstCliDet id_est,EstCliDet nom_est,MunCliDet,CiuCliDet id_ciu,CiuCliDet nom_ciu,nom_pai FROM cat_clientes_detalle
			LEFT JOIN cat_paises ON id_pai = PaisCliDet WHERE IDCliDet=$IDRazSoc;";
			$ciudadData=$this->modelObject->query($query);
	   }else{
			$ciudadData=$ciudadModel->getCiudadEstadoYpais($cliente['CiuCliDet'],$cliente['EstCliDet'] , $cliente['PaisCliDet']);
	   }
       
       $response=array();
       $response['success']=true;
       $response['data']=$cliente;
       $response['data']['ciudad']=$ciudadData;
       return $response;
    }
	/*
		Crea un nuevo cliente o actualiza sus datos si ya existe	
	*/
	function editarcliente(){
        
        $params=array();

        try{
        	$this->validarCorreos($_POST['EmaCliDet']);
		}catch(Exception $e){
			throw new Exception($e->getMessage());
		}
		$razocParams=array();
		 $razocParams['RFCCliDet']  = $_POST['RFCCliDet'];
        $razocParams['NumExtCliDet'] = $_POST['NumExtCliDet'];
        $razocParams['NumIntCliDet'] = $_POST['NumIntCliDet'];
        $razocParams['CalleCliDet']=$_POST['CalleCliDet'];
        $razocParams['PaisCliDet'] = $_POST['PaisCliDet'];
        $razocParams['CiuCliDet']=$_POST['CiuCliDet'];
		$razocParams['EstNCliDet']='';		
		$razocParams['EstCliDet']=$_POST['EstCliDet'];
       $razocParams['CPCliDet']=$_POST['CPCliDet'];						
		$razocParams['CurpCliDet']='';
		$razocParams['PasCliDet']='';	
        $razocParams['TipoCliDet'] = $_POST['TipoCliDet'];
		$razocParams['ColCliDet']=$_POST['ColCliDet'];
		$razocParams['EmaCliDet']=$_POST['EmaCliDet'];			
		$razocParams['LocCliDet']=$_POST['localidad'];
		$response=array();
		/*
		 * Posibilidades, 
		 * crear cliente y crear razon social
		 * Crear razon social para el cliente existente
		 * Modificar datos de la razon social
		*/
		if ( empty($_POST['KEYCli']) && empty($_POST['RazSocCliDet']) ){
			throw new Exception("Debe escribir algun valor para la razón social o nombre del cliente");			
		}
		
		if ( empty($_POST['KEYCli']) || !is_numeric($_POST['KEYCli']) ){	
			//crear cliente y crear razon social
			//-----------------------------------------------
			if (empty($_POST['KEYCli'])){
				$params['NomCli']=$_POST['RazSocCliDet'];
			}else{
				$params['NomCli']=$_POST['KEYCli'];
			}			
			$params['StatusCli']='A';
			$params['NomConCorCli']=$params['NomCli'];
			$params['EmaConCorCli']=$_POST['EmaCliDet'];
			$params['NomComCliDet']=$params['NomCli'];
			$params['TelConCorCli']='';
			$params['CelConCorCli']='';		
			//-----------------------------------------------
			$clienteModel=new ClienteModel();	
			$arrSavedClient=$clienteModel->guardar($params);	//<-----Cliente almacenado
			//-----------------------------------------------
			if(empty($_POST['RazSocCliDet'])){		
				$razocParams['RazSocCliDet']=$params['NomCli'];
				$razocParams['NomComCliDet']=$params['NomCli'];						
			}else{
				$razocParams['RazSocCliDet']=$_POST['RazSocCliDet'];
				$razocParams['NomComCliDet']=$_POST['RazSocCliDet'];
			}
			$razocParams['PredCliDet']='S';					
			$razocParams['KEYCli']=$arrSavedClient['IDCli'];
			$razonModel=new RazonSocialModel();
			$arrSavedRZ=$razonModel->guardar($razocParams);
			$idRazSoc=$arrSavedRZ['IDCliDet'];
			$idCli=$arrSavedClient['IDCli'];			
			$respCliente['data']['NomCli']=$params['NomCli'];
			$mensaje="Cliente Creado";
		}else if(is_numeric($_POST['KEYCli'])){
			if ( empty($_POST['IDCliDet']) ){
				throw new Exception("Crear razon social para el cliente existente");
			}else if(is_numeric($_POST['IDCliDet'])){
				// Modificar datos de la razon social
				//throw new Exception(" Modificar datos de la razon social");
				if(empty($_POST['RazSocCliDet'])){			
					throw new Exception("Debe especificar un nombre para la razón social");	
				}else{
					$razocParams['RazSocCliDet']=$_POST['RazSocCliDet'];
					$razocParams['NomComCliDet']=$_POST['RazSocCliDet'];
				}
				
				$razocParams['KEYCli']=$_POST['KEYCli'];
				$razocParams['IDCliDet']=$_POST['IDCliDet'];
				$razonModel=new RazonSocialModel();
				$arrSavedRZ=$razonModel->guardar($razocParams);
				$idRazSoc=$arrSavedRZ['IDCliDet'];
				$idCli=$_POST['KEYCli'];
				//$nomCli=$params['NomCli'];
				$mensaje="Razón social almacenada";
				
			}else{
				throw new Exception("Error al analizar la razón social, consulte al administrador del sistema");
			}
		}else{
			throw new Exception("Error al analizar la información del cliente, consulte al administrador del sistema");
		}
		//-------------------------------------------------
		$_POST['IDCliDet']=$idRazSoc;
		$respCliente= $this->getCliente();			
		$respCliente['data']['IDCli']=$idCli;

		$respCliente['msg']=array('titulo'=>'Facturación','mensaje'=>$mensaje);
		return $respCliente;
			
		throw new Exception("Implementando la edicion del cliente");
		if ($_POST['IDCliDet']==''){
			//crear un nuevo cliente
			//crear una razon social para este cliente 
			$clienteModel=new ClienteModel();
			
			$params['NomCli']=$_POST['KEYCli'];
			$params['StatusCli']='A';
			$params['NomConCorCli']=$_POST['KEYCli'];
			$params['EmaConCorCli']=$_POST['EmaCliDet'];
			$params['TelConCorCli']='';
			$params['CelConCorCli']='';			
			$arrSavedClient=$clienteModel->guardar($params);
		
			$params['RFCCliDet']=$_POST['RFCCliDet'];
			$params['RazSocCliDet']=$_POST['KEYCli'];
			$params['CalleCliDet']=$_POST['CalleCliDet'];
			$params['NumExtCliDet']=$_POST['NumExtCliDet'];
			$params['NumIntCliDet']=$_POST['NumIntCliDet'];
			$params['ColCliDet']=$_POST['ColCliDet'];
			$params['EmaCliDet']=$_POST['EmaCliDet'];
			
			$params['LocCliDet']=$_POST['localidad'];
			$params['TipoCliDet']=$_POST['TipoCliDet'];
			$params['PredCliDet']='S';
			$params['CiuCliDet']=$_POST['CiuCliDet'];
			$datos['EstNCliDet']='';		
			$params['EstCliDet']=$_POST['EstCliDet'];
			$params['PaisCliDet']=$_POST['PaisCliDet'];
			$params['CPCliDet']=$_POST['CPCliDet'];			
			
			$params['CurpCliDet']='';
			$params['PasCliDet']='';			
			$params['KEYCli']=$arrSavedClient['IDCli'];
			$razonModel=new RazonSocialModel();
			$arrSavedRZ=$razonModel->guardar($params);
						
			$_POST['IDCliDet']=$arrSavedRZ['IDCliDet'];
			$respCliente= $this->getCliente();
			$respCliente['data']['IDCli']=$arrSavedClient['IDCli'];
			$respCliente['data']['NomCli']=$_POST['RazSocCliDet'];
			$respCliente['msg']=array('titulo'=>'Facturación','mensaje'=>"Cliente creado");
			return $respCliente;
		}else{
			$params['RFCCliDet']=$_POST['RFCCliDet'];
			$params['RazSocCliDet']=$_POST['RazSocCliDet'];
			$params['CalleCliDet']=$_POST['CalleCliDet'];
			$params['NumExtCliDet']=$_POST['NumExtCliDet'];
			$params['NumIntCliDet']=$_POST['NumIntCliDet'];
			$params['ColCliDet']=$_POST['ColCliDet'];
			$params['EmaCliDet']=$_POST['EmaCliDet'];
			
			$params['LocCliDet']=$_POST['localidad'];
			$params['TipoCliDet']=$_POST['TipoCliDet'];
			$params['PredCliDet']='S';
			$params['CiuCliDet']=$_POST['CiuCliDet'];						
			$params['PaisCliDet']=$_POST['PaisCliDet'];
			$params['EstCliDet']=$_POST['EstCliDet'];
			$params['CPCliDet']=$_POST['CPCliDet'];			
			$params['CurpCliDet']='';
			$params['PasCliDet']='';			
			$params['KEYCli']=$_POST['KEYCli'];
			$razonModel=new RazonSocialModel();
			$arrSavedRZ=$razonModel->guardar($params);
			
			$_POST['IDCliDet']=$_POST['KEYCliOrdVen'];
			$respCliente= $this->getCliente();			
			$respCliente['msg']=array('titulo'=>'Facturacion ','mensaje'=>"Cliente Actualizado");
			return $respCliente;
		}
       
        $response['success']=true;   
            
        return $response;
    }
  
    function findProductos(){
        $modelObject=$this->modelObject;
        $filtro=$_POST['query'];
		$response=array();
		if (!isset($_SESSION['Auth']['User']['Origen'])){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en un negocio para obtener las tasas a aplicar';
			return $response;
		}
        $origen=$_SESSION['Auth']['User']['Origen'];
		
        if ($origen=='EMP'){
            $OrigenTaR="E";
        }else if ($origen=='SUC'){
            $OrigenTaR="S";
        }
        $KEYOrigenTaR = $_SESSION['Auth']['User']['IDOrigen'];
		
		$start = (!empty($_POST['start'])) ? $_POST['start'] : 0;		//<---------------
        $limit = (!empty($_POST['limit'])) ? $_POST['limit'] : 20;	//<---------------
        if (isset($_POST['TipDoc']) && $_POST['TipDoc']=='NOTA DE CREDITO'){        	
        	require_once ('eko_framework/app/models/concepto.php');
        	$conceptoModel=new ConceptoModel();
        	$tipoConcepto='';
        	switch($_POST['TipDoc']){
        		case 'NOTA DE CREDITO':
        			$tipoConcepto='NDC';
        		break;
        		default:
        			throw new Exception("Error al buscar los conceptos: Tipo de concepto desconocido (".$_POST['TipDoc'].")");
        	}
        	//---------------------------------------------------
        	//	Configura los campos a seleccionar y sus alias
        	$params=array();
        	$params['select']=array(        	 
        		0=>array('3','TipoArt'),
        		1=>array('IDCon','KEYProdServ'),
        		2=>array('CONCAT("c-",IDCon)','idConcat'),
        		3=>array('DescCon','Descripcion')
        	);
        	$datos=$conceptoModel->find($start, $limit, $filtro, true, $tipoConcepto,$params);
        }else{
        	$datos=$modelObject->getProductosYservicios($OrigenTaR,$KEYOrigenTaR,$filtro,$start,$limit);	
        }

        $response=$datos;
		$response['success']=true;
        return $response;
    }
	  
	 
	/*
	  Proporciona el detalle de un producto o servicio con los impuestos que aplican para el negocio que emite la factura.
	  Params:
		idConcepto: 	Cadena concatenada (id-tipoConceto) de la cual se quiere saber el detalle
		IDSuc:		ID numerico en caso de que sea una sucursal quien emite la factura
		IDEmp:		Empresa matriz de la sucursal o empresa emisora.		
	*/
	function getConcepto(){
		$idConcepto=$_POST['idConcepto'];
		$splitedIdConcepto=explode('-',$idConcepto);/*viene concatenado (id-tipoConcepto)*/
		$sucursalId=$_POST['IDSuc'];
		$empresaId=$_POST['IDEmp'];
		$origen=($sucursalId==0)?'EMP':'SUC';
		$tipoDocumento = empty($_POST['tipoDocumento'])? '' : $_POST['tipoDocumento'];
		
        if ($origen=='EMP'){
            $OrigenTaR="E";
			$KEYOrigenTaR=$empresaId;
        }else if ($origen=='SUC'){
            $OrigenTaR="S";
			$KEYOrigenTaR=$sucursalId;
        }
       
		$modelObject=$this->modelObject;
		
		switch($splitedIdConcepto[0]){
			case 'p':
				$datos=$modelObject->getProducto($OrigenTaR,$KEYOrigenTaR,$splitedIdConcepto[1],$tipoDocumento);				
			break;
			case 's':
				$datos=$modelObject->getServicio($OrigenTaR,$KEYOrigenTaR,$splitedIdConcepto[1],$tipoDocumento);				
			break;
			case 'c':
				require_once ('eko_framework/app/models/concepto.php');
        		$conceptoModel=new ConceptoModel();
				$datos=$conceptoModel->getConcepto($OrigenTaR,$KEYOrigenTaR,$splitedIdConcepto[1], $tipoDocumento);				
				break;
			case 'k':
				$datos=$modelObject->getKit($OrigenTaR,$KEYOrigenTaR,$splitedIdConcepto[1],$tipoDocumento);				
				break;
			default:
				throw new Exception("Concepto desconocido: ".$splitedIdConcepto[0]);
		}
		$response=array();
        $response['success']=true;
        $response['data']=$datos;
        return $response;
	}
	
	private function ejecutarGuardar(){
		//exit();
		set_time_limit( 60 );	
		$params=$this->getParams();
		$folioQuemado=( isset($_POST['folioQuemado']) ) ? $_POST['folioQuemado']: 0; 
		//-----------------------------------------------------------------------------------------------
    	//			SI LA FACTURA ES NUEVA, VERIFICAR QUE EL EMISOR (Emp o Suc) NO ESTÉ SUSPENDIDA
    	//-----------------------------------------------------------------------------------------------
    	
    	$sucursalId=$_POST['IDSuc'];
    	$empresaId=$_POST['IDEmp'];
    	$modelObject=$this->modelObject;
		
		#==========================================================================
		# si se están vendiendo kits, revisar que haya existencia de cada producto,
		# del producto que no haya existencia, sugerir el remplazo.
		
		$this->TipDocOrdVen =$params['TipDoc'];
		$this->KEYEmpOrdVen =$empresaId;
		$this->KEYSucOrdVen	=$sucursalId;
		//----------------------------------------------------------------------------
		if ($sucursalId==0){
			$query="SELECT StatusEmp as status,ComEmp as nombre FROM cat_empresas WHERE IDEmp=$empresaId";
			$arrStatus=$modelObject->query($query);        		
			$tipo_de_negocio="Empresa";	
		}else{
			$query="SELECT StatusSuc as status,NomSuc as nombre FROM cat_sucursales WHERE IDSuc=$sucursalId";
			$arrStatus=$modelObject->query($query);
			$tipo_de_negocio="Sucursal";
		}

		$status=$arrStatus[0]['status'];
		if ($status=='I'){
			$nombre=$arrStatus[0]['nombre'];
			$response=array(
				'success'=>false,
				'data'	 =>array(),
				'msg'	 =>array(
					'titulo'	=>'Facturación',
					'mensaje'	=>"No puede crear facturas cuando la $tipo_de_negocio ($nombre) está suspendida"        		
				)
			);
			return $response;
        }
		//-----------------------------------------------------------------------------------------------
        //  				VALIDAR RFC del Certificado y de la empresa Emisora	
		//-----------------------------------------------------------------------------------------------
		$certificado = new CertificadoModel;
		$valida_certif = $certificado->validarRFCCertificado($_POST['IDEmp'],$_POST['IDSuc']);
		if ($valida_certif){
			throw new Exception($valida_certif);
		}
		
		
		/*if ($valida_certif){
			throw new Exception($valida_certif);
		}*/

    	$cliente=json_decode(stripslashes($_POST['Cliente']),true);
		
		//--------------------------------------------------------------------------
		if (is_numeric($cliente['IDCli'])){
			$IDCli=$cliente['IDCli'];		
			$NomCliente=$cliente['NomCli'];	
		}else{		// El cliente no existe en la base de datos, guardelo o seleccione un cliente
			$IDCli=0;
			$NomCliente=$cliente['RazSocCliDet'];
		}		
		
		try{
			$this->validarCorreos($cliente['EmaCliDet']);	
		}catch(Exception $e){
			throw new Exception($e->getMessage()." no es un email valido (mail@dominio.abc)");
		}
				
    	if (isset($_POST['folios'])){	//Folios de la orden de venta que deben relacionarse con esta factura
			$folios=json_decode(stripslashes($_POST['folios']),true);	
		}
		//------------------------------------------------------------------------------------------
		//		Se analiza la información del cliente, aqui se decide si crear un
		//------------------------------------------------------------------------------------------
		if ($IDCli==0){	//Si la razón social no existe en la BD	(IDCli en realidad es el identificador de la razon social) 
			//$cliente=json_decode(stripslashes($_POST['Cliente']),true);
			
			$_POST=array();	//	VOY A SIMULAR LA LLAMADA POST, Primero limpio el arreglo
			foreach($cliente as $key => $value){	//Y lo relleno con el Json que envié
				$_POST[$key]=$value;
			}		
			$result=$this->editarcliente();	//se guarda el cliente
			
			if ($result['success']){
				$cliente=$result['data'];			
				$IDCliDet=$result['data']['IDCliDet'];		
				$IDCli=$result['data']['KEYCli'];	//Se obtiene el ID del cliente recien almacenado para relacionarlo con la factura
				$params['IDRazSoc']=$IDCliDet;	
				$NomCliente=addslashes($cliente['RazSocCliDet']);						
			}else{			
				return $result;
			}
		}
		//-----------------------------------------------------------------------------------------------
		//Se obtiene el nombre del pais,estado y ciudad usando sus ids	
		$idCiu=$cliente['CiuCliDet'];
		$idPais=$cliente['PaisCliDet'];
		$idEst=$cliente['EstCliDet'];
		
		
		if (!is_numeric($idCiu)){
			$IDRazSoc=$params['IDRazSoc'];
			$query="SELECT EstCliDet nom_est,MunCliDet,CiuCliDet nom_ciu,nom_pai FROM cat_clientes_detalle
			LEFT JOIN cat_paises ON id_pai = PaisCliDet WHERE IDCliDet=$IDRazSoc;";
			$arrUbicacion=$this->modelObject->query($query);			
		}else{
			$query="call spCatCiudadesConsultar('$idCiu', $idEst, $idPais,'',0, 1);";
			$arrUbicacion=$this->modelObject->query($query);
		}
		
    	if (sizeof($arrUbicacion)>0){
			$ciudad=$arrUbicacion[0]['nom_ciu'];
			$estado=$arrUbicacion[0]['nom_est'];
			$pais=$arrUbicacion[0]['nom_pai'];
		}else{
			throw new Exception("No se encontró la localidad del cliente");
		}		
			$cliente=array(
			'IDCli'=>$IDCli,//
			'RFCCliente'=>$cliente['RFCCliDet'],//
			'RazSoc'=>addslashes($cliente['RazSocCliDet']),//
			'NomComFac'=> addslashes($cliente['NomComCliDet']),
			'NomCliente'=>addslashes($NomCliente),//
			'CalleCliente'=>addslashes($cliente['CalleCliDet']),//
			'NumExtCliente'=>addslashes($cliente['NumExtCliDet']),//NumExtCliDet
			'NumIntCliente'=>addslashes($cliente['NumIntCliDet']),//NumIntCliDet
			'ColCliente'=>addslashes($cliente['ColCliDet']),//ColCliDet
			'LocCliente'=>addslashes($cliente['localidad']),//localidad
			'MunCliente'=>$ciudad,//
			'EdoCliente'=>$estado,//
			'PaisCliente'=>$pais,//
			'CPCliente'=>$cliente['CPCliDet'],//CPCliDet
			'CurpCliente'=>'',
			'PasaporteCliente'=>'',		
			'EmaRazSoc'=>addslashes($cliente['EmaCliDet'])
		);
		$params=array_merge($params,$cliente);
		#===================================================================================================
		#	
		#===================================================================================================
		$almacenId = empty($_SESSION['Auth']['Almacen'])? 0 : $_SESSION['Auth']['Almacen']['IDAlmacen'];
		
		$respuesta=$this->procesarKits( $this->modelObject->conceptos, $almacenId );
		
		if ( $respuesta['success']===false ){
			return $respuesta;
		}
		if ( $respuesta['codigo']==100 ){
			$this->modelObject->conceptos=$respuesta['detalles'];
		}
		#===================================================================================================		
		
		$arraymodel=$this->modelObject->guardar($params,$folioQuemado);			    //<-----------------------SE almacena la factura en la tabla temporal
		
		$modoPrueba=$arraymodel['modoPrueba'];		

		$resp=$this->crearComprobanteDigital($arraymodel, $modoPrueba); //<-------------CREAR EL XML, SELLAR O TIMBRAR, GENERAR PDF, MOVER A TABLA PERMANENTE 

		if ($resp['success']==false){
			
			return $resp;
		}
			    	
		//-----------------------------------------------------------------------------------------------        
        //  										PREPARAR LA RESPUESTA
		//-----------------------------------------------------------------------------------------------   	
        $response=array();
        $response['success']=true;		
							        
		if ($modoPrueba){
			$response['msg']=array('titulo'=>'Factura','mensaje'=>'Factura Creada En Modo De Prueba');			
		}else{
			if (isset($folios)){	//ESTA VARIABLE EXISTE CUANDO LA FACTURA FUE GENERADA A PARTIR DE UNA O MAS ORDENES DE VENTA				
					$IDFac=$resp['data']['Factura']['IDFac'];
					$IDEmp=$resp['data']['Factura']['IDEmp'];					
					$this->modelObject->relacionarOrdenesConFactura($IDFac,$IDEmp,$folios);
			}
			$response['msg']=array('titulo'=>'Factura','mensaje'=>'Factura Almacenada Satisfactoriamente');	
		}
        $response['data']=$resp['data'];
		$response['data']['Factura']['modoPrueba']=$modoPrueba;		
        return $response;		//<---------Responde!
	}
	
	function inventariar($empresaId){
		$sql='SELECT manejaInvEmp FROM cat_empresas WHERE IDEmp='.$empresaId;
		$model=$this->getModelObject();
		$arrResp=$model->select($sql);
		if ( $arrResp[0]['manejaInvEmp']=='1' ){
			return true;
		}else{
			return false;
		}
	}
    function guardar(){ 
		if ($this->inventariar($_POST['IDEmp'])===true){
			if (empty($_SESSION['Auth']['Almacen']) ){
				throw new MyException("Seleccione al almacén que dará salida a los productos.",'Seleccione un almacén','WARNING');
			}
		}	
		
    	try{
			
    		$resp= $this->ejecutarGuardar();
			if ( $resp['success']===false) return $resp;
			/*if ( isset($resp['codigo']) ){
				if ( $resp['success']===false) return $resp;
			}*/
			
			//==============================================================================
			//			
			if ( !isset($resp['data']['modoPrueba']) ){
				try{
					if ($this->inventariar($_POST['IDEmp'])===true){
						$this->modelObject->startTransaction();		
						//--------------------------------------------------------
						$almacenId=$_SESSION['Auth']['Almacen']['IDAlmacen'];
						$respuesta=$this->procesarKits($resp['data']['Detalles'], $almacenId);			
						if( !$respuesta['success'] ){
							throw new Exception("Error al enviar la Factura a inventarios");
						}						
			
						$this->registrarMovimiento($resp['data']['Factura'],$respuesta['detalles']);
						$this->modelObject->execute('COMMIT;'); 					
						$resp['movimiento']=true;
					}
				}catch(Exception $e){
					$resp['movimiento']=false;
					$resp['msg']['titulo']="El movimiento no ha sido registrado";
					$resp['msg']['mensaje']=$e->getMessage();
					$resp['msg']['icon']='WARNING';
				}
			}
			//==============================================================================
			return $resp;		
    	}catch(Exception $e){
    		$response =array(
    			'success'=>false,
    			'msg'	 =>array('titulo'=>'Error en la creación de la factura','mensaje'=>$e->getMessage()),
    			'data'	 =>array()
    		);
    		if ( isset($this->modelObject->folioQuemado) ){
    			$response['data']['folioQuemado']=$this->modelObject->folioQuemado;	
    		}    	

			if ( isset($this->modelObject->timbrado) ){
    			//$this->modelObject->gastarFolio();	//Ya no es necesario, se hizo una modificacion en el modelo facturacion (bloqueo folios)
    		} else if ( isset($this->modelObject->IDFol) ){
				$this->modelObject->desbloquearFolio( $this->modelObject->IDFol );	
			}  
			
    		return $response;
    	}
    }
	function getParams(){
		// --------- Preparar valores a ser almacenados ---------
		$rfc = '';
		if(isset($_POST['rfc'])){
			$rfc = $_POST['rfc'];
		}
		$sumatorias = $_POST['Totales'];       
		$params = array();
		$params['Subtotal']   = $sumatorias['Subtotal'];		
		$params['Descuento']  = $sumatorias['DescuentoPesos'];
		$params['Total']      = $sumatorias['Total'];
		$params['TotImpTras'] = $sumatorias['IvaPesos'];
		$params['TotImpRet'] = isset($sumatorias['RetImpPesos'])? $sumatorias['RetImpPesos'] : null;
		if ( isset($sumatorias['excento']) ){
			if ($sumatorias['excento']=='true'){				
				$params['TotImpTras']=null;
				$params['TotImpRet']=null;
			}
		}
		
        $params['FechaFac']   = $_POST['FechaFac'];
        $params['Folio']      = $_POST['Folio'];
        $params['IDRazSoc']   = $_POST['IDRazSoc'];
        $params['IDEmp']      = $_POST['IDEmp'];
        $params['IDSuc']      = $_POST['IDSuc'];
        $params['IDFac']      = $_POST['IDFac'];
		$params['SerFol']     = $_POST['SerFol'];
		$params['IDSerFol']   = $_POST['IDSerFol'];
		$params['TipDoc']	  =$_POST['TipDoc'];
		$params['TipComp']	  =$_POST['TipComp'];
		$params['rfc']		  =$rfc;	
		//--------------------------------------------------------------------------
		//La fecha viene separada en fecha y hora, aqui se unen
		$concatenada=$_POST['FechaFac']." ".$_POST['horaFac'];				
		$date = DateTime::createFromFormat('d/m/Y h:i:s A', $concatenada);
		
    	//si no pudo convertirse se lanza un error
		if ( !is_object($date) ){
			throw new Exception("La Fecha o el formato es incorrecto (".$concatenada."). Deberia ser dd/mm/aaaa hh:mm am/pm");
		}
		//-------------------------------------------------------------------------
		/*
		$fechaActualString=getFechaActual("%d/%m/%Y %H:%i:%s");
		$fecha_actual=DateTime::createFromFormat('d/m/Y H:i:s', $fechaActualString);
		//Que la fecha no sea posterior a la fecha actual
		if ($date>$fecha_actual){
			$fechaFactura=$concatenada;
			throw new Exception("La Fecha de la factura ($fechaFactura) es mayor que la fecha actual ($fechaActualString)");
		}*/	
		//-------------------------------------------------------------------------		
		$fecha= $date->format('d/m/Y H:i:s');			
		$params['FechaFac']=$fecha;
		//--------------------------------------------------------------------------
		$params['estado']=(isset($_POST['StatusFac'])) ? $_POST['StatusFac'] : '1';
		
        $conceptos=json_decode( stripslashes($_POST['Conceptos']), true);			//Los articulos y productos son enviados en esta variable
		//$conceptos=json_decode($_POST['Conceptos'], true);
		
        //--------------------------------------------------------------------------------------------
        //Obtengo los componentes del kit para agregarlos al detalle de la factura
		$sucursalId=$_POST['IDSuc'];
    	$empresaId=$_POST['IDEmp'];
    	
    	if ($sucursalId==0){
    		$KEY_origen=$empresaId;
    		$tipo_origen="E";
    	}else{
    		$KEY_origen=$sucursalId;
    		$tipo_origen="S";
    	}
    	$model=$this->getModelObject();
		$arrRegimen=$model->select("SELECT Regimen_EmpReg as Regimen 
		FROM cat_empresas_regimen 
		WHERE KEY_Emp_EmpReg=$empresaId
		ORDER BY Regimen_EmpReg ASC");
		if (empty($arrRegimen)){
			throw new Exception("La empresa debe tener configurado al menos 1 Régimen Fiscal");
		}
		$params['RegimenFiscal']='';
		foreach($arrRegimen as $regimen){
			$params['RegimenFiscal'].=$regimen['Regimen'].', ';
		}
		if ( !empty($params['RegimenFiscal']) ){
			$params['RegimenFiscal']=substr($params['RegimenFiscal'], 0, strlen($params['RegimenFiscal'])-2);
		}		
		

        
		$this->modelObject->conceptos=$conceptos;					
		
        $formaPago=json_decode(stripslashes($_POST['formaPago']),true);			//Los detalles de la forma de pago vienen aqui
        
        $params['TipoFactura']=$formaPago['TipoFactura'];
        
        if (!is_numeric($formaPago['TipoCambio'])){
        	throw new Exception("El tipo de cambio debe ser numérico");
        }        
		$params['TipoCambio']=$formaPago['TipoCambio'];
		
		$monedaDefault="Pesos";		
		if ($params['TipoFactura']==$monedaDefault){
			$params['TipoCambio']=1;
		}		
		
		$params['KEYMetPago']=$formaPago['TipPagOrdVen'];
		$params['MetPago']=$formaPago['cmbTipPagDesc'];
		$params['codMetPag']=$formaPago['codigoMetodoPago'];
		$params['status']='P';	
		/*
		switch($params['KEYMetPago']){
		case '1':	//Pago en efectivo
			$params['status']='P';
			break;
		case '2':	//Pago a credito
			$params['status']='S';	//Con saldo
			//Cuando es pago a crédito se calcula la fecha de vencimiento en base a los dias de vencimiento
			$fecha=$this->modelObject->jsDateToMysql($params['FechaFac']);				
			$start = strtotime($fecha);			
			$dias= $formaPago['DiasCreOrdVen'];			
			$vencimiento = strtotime("+$dias days", $start);		
			$vencimiento =date("d/m/Y H:i:s ", $vencimiento);	
			$params['FechaVen']= $this->modelObject->jsDateToMysql($vencimiento);
			$params['DiasCreFac']=$formaPago['DiasCreOrdVen'];				
		break;
		case '3':		
			$params['status']='P';		//Pagado	
			break;
		case '4':		
			$params['status']='P';		//Pagado
			break;
		case '5':
			$params['status']='P';		//Pagado
			break;
		case '10':
			$params['status']='S';	//Con saldo
			break;
		case '11':
			$params['status']='P';		//Pagado
			break;
		}
		*/
		//----------------------------------------------------------
		//	Forma de pago
		//----------------------------------------------------------
		if (isset($formaPago['formaPago'])){
			$params['FormaPago']=$formaPago['formaPago'];
			$params['referencia']=isset($formaPago['referencia'])? $formaPago['referencia'] : '';
			if ( isset($formaPago['NumCtaPago']) ){	
				$NumCtaPago=$formaPago['NumCtaPago'];
				if ( empty($NumCtaPago) ||  $NumCtaPago=='NO IDENTIFICADO'){
					$NumCtaPago='NO IDENTIFICADO';
				}else {
					if (strlen($NumCtaPago)<4 ){
						throw new Exception("El tamaño mínimo para el número de cuenta es 4.");
					}
					if ( !is_numeric($NumCtaPago) ){
						throw new Exception("El número de cuenta ($NumCtaPago) debe ser numérico. ");
					}
				}
				$params['NumCtaPago']= $NumCtaPago;	
			}
			
			switch($formaPago['formaPago']){
				case '1':
					$params['FormaPago']="PAGO EN UNA SOLA EXHIBICIÓN";
					$params['FecPago']=$params['FechaFac'];
				break;
				case '3':
					$params['FormaPago']="PAGO EN PARCIALIDADES";
				break;	
				case '2':
					$px=$formaPago['parcialidadA'];
					$py=$formaPago['parcialidadB'];
					if (!is_numeric($px) || !is_numeric($py)){
						throw new Exception("las parcialidades deben ser números");
					}
					
					if (empty($px)){
						throw new Exception("La parcialidad a pagar debe ser un número mayor que cero");
					}
					
					if ($px>$py){
						throw new Exception("la parcialidad pagada es mayor al total de parcilidades");
					}
					
					$params['numParcialidad']=$formaPago['parcialidadA'];
					$params['totParcialidades']=$formaPago['parcialidadB'];
					
					$paramsOrigen=array();
					
					$paramsOrigen['FolioFiscalOrig'] = $formaPago['FolioFiscalOrig'];					
					$paramsOrigen['esquemaOrig'] = $formaPago['esquemaOrig'];
					
					$paramsOrigen['FolioOrig'] = $formaPago['FolioOrig'];										

					$paramsOrigen['SerieFolioFiscalOrig'] = $formaPago['SerieOrig'];					
					// convertir la fecha y hora a formato mysql					
					$date = DateTime::createFromFormat('d/m/Y H:i:s', $formaPago['FechaFolioFiscalOrig']);					
					if ( !is_object($date) ){
						throw new MyException("El formato de fecha es incorrecto",'Error en la Factura Origen');
					}
					
					$FechaFolioFiscalOrig=$date->format('Y-m-d H:i:s');					
										
					$paramsOrigen['FechaFolioFiscalOrig']=$FechaFolioFiscalOrig;					
					$paramsOrigen['MontoFolioFiscalOrig']=str_replace(',', '', $formaPago['MontoFolioFiscalOrig']);	
					$paramsOrigen['IDFacOrigen']=$formaPago['IDFacOrigen'];						
					$this->modelObject->paramsOrigen=$paramsOrigen;
					$this->modelObject->parcialidades=true;					
					$serieYfolio=empty($paramsOrigen['SerieFolioFiscalOrig']) ? '' : $paramsOrigen['SerieFolioFiscalOrig'].'-';
					$serieYfolio.=$paramsOrigen['FolioFiscalOrig'];
					//$params['FormaPago']="PARCIALIDAD $px DE $py DE $serieYfolio ".$date->format('d/m/Y h:i A').'.';
					$params['FormaPago']="PARCIALIDAD $px DE $py";
					
				break;
			}
		}		
		
		return $params;
	}
	
	function preview(){		
       // --------- Preparar valores a ser almacenados ---------
	   
		$params=$this->getParams();

		set_time_limit(0);
		
		// traigo todos los valores del form del cliente
		$cliente = json_decode(stripslashes($_POST['Clientes']),true);
		foreach($cliente as $key => $value){
			$cliente_fix[$key]=addslashes($value);
		}
				
		// buscar si existe el cliente, sino tomar el tecleado en el combo
		$arr_cliente = $this->modelObject->getCliente($params['IDRazSoc']);
		if($arr_cliente){
			$razon_social = $arr_cliente['RazSocCliDet'];
		} else {
			$razon_social = $params['IDRazSoc'];
		}
		
		// buscar nombres de municipio, estado y pais
		$id_ciu  = $cliente['CiuCliDet'];
		$id_est  = $cliente['EstCliDet'];
		$id_pais = $cliente['PaisCliDet'];
		$idCiu=$id_ciu;
		$idEst=$id_est;
		$idPais=$id_pais;
		if (!is_numeric($idCiu)){
			$IDRazSoc=$params['IDRazSoc'];
			$query="SELECT EstCliDet nom_est,MunCliDet,CiuCliDet nom_ciu,nom_pai FROM cat_clientes_detalle
			LEFT JOIN cat_paises ON id_pai = PaisCliDet WHERE IDCliDet=$IDRazSoc;";
			$arr_localidad=$this->modelObject->query($query);			
		}else{
			$query="call spCatCiudadesConsultar('$idCiu', $idEst, $idPais,'',0, 1);";
			$arr_localidad=$this->modelObject->query($query);
		}
		
		
		$params['RFCCliente']    = $cliente_fix['RFCCliDet'];
		$params['RazSoc']        = $razon_social;
		$params['NomComFac']     = $cliente_fix['NomComCliDet'];
		$params['CalleCliente']  = $cliente_fix['CalleCliDet'];
		$params['NumExtCliente'] = $cliente_fix['NumExtCliDet'];
		$params['NumIntCliente'] = $cliente_fix['NumIntCliDet'];
		$params['ColCliente']    = $cliente_fix['ColCliDet'];
		$params['MunCliente']    = $arr_localidad[0]['nom_ciu'];
		$params['EdoCliente']    = $arr_localidad[0]['nom_est'];
		$params['PaisCliente']   = $arr_localidad[0]['nom_pai'];
		$params['CPCliente']     = $cliente_fix['CPCliDet'];
		$params['LocCliente']    = $cliente_fix['localidad'];
		#===================================================================================================
		$sucursalId=$_POST['IDSuc'];
    	$empresaId=$_POST['IDEmp'];    			
		#==========================================================================
		# si se están vendiendo kits, revisar que haya existencia de cada producto,
		# del producto que no haya existencia, sugerir el remplazo.
		
		$this->TipDocOrdVen =$params['TipDoc'];
		$this->KEYEmpOrdVen =$empresaId;
		$this->KEYSucOrdVen	=$sucursalId;
		$almacenId = empty($_SESSION['Auth']['Almacen'])? 0 : $_SESSION['Auth']['Almacen']['IDAlmacen'];
		
		$respuesta=$this->procesarKits( $this->modelObject->conceptos, $almacenId );
		
		if ( $respuesta['success']===false ){
			return $respuesta;
		}
		if ( $respuesta['codigo']==100 ){
			$this->modelObject->conceptos=$respuesta['detalles'];
		}
		#===================================================================================================		
        // ------------  Almacenar factura en tabla temporal ------------
		/*
		$params['KEYMetPago']=$formaPago['TipPagOrdVen'];
		$params['MetPago']=$formaPago['cmbTipPagDesc'];
		$params['codMetPag']=$formaPago['codigoMetodoPago'];
		*/
		$arraymodel = $this->modelObject->guardarPreview($params);  // guardar la factura en la tabla temporal y regresar el ID		
		
		$IDSuc=$arraymodel['Factura']['IDSuc'];
		//Modificacion para que imprima el telefono de la matriz en los datos de expedicion jrhc
		if($IDSuc>0){
			$contactoSucArr=$this->modelObject->query("SELECT TelSuc,FaxSuc FROM cat_sucursales WHERE IDSuc = $IDSuc;");
			$arraymodel['Factura']['TelSuc']=isset($contactoSucArr[0]['TelSuc'])? $contactoSucArr[0]['TelSuc'] : '';
			$arraymodel['Factura']['FaxSuc']=isset($contactoSucArr[0]['FaxSuc'])? $contactoSucArr[0]['FaxSuc'] : '';
			
		}else{
			$IDEmp=$arraymodel['Factura']['IDEmp'];
			$contactoEmpArr=$this->modelObject->query("SELECT TelConEmp FROM cat_empresas WHERE IDEmp = $IDEmp;");
			$arraymodel['Factura']['TelSuc']=isset($contactoEmpArr[0]['TelConEmp'])? $contactoEmpArr[0]['TelConEmp'] : '';
			$var=$arraymodel['Factura']['TelSuc'];
		}
		//
		
		if ( isset($this->modelObject->paramsOrigen) ){		
			
			$fecha=$this->modelObject->paramsOrigen['FechaFolioFiscalOrig'];
			$date = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
			
			$paramsOrigen['SerieOrig']				=$this->modelObject->paramsOrigen['SerieFolioFiscalOrig'];
			$paramsOrigen['FechaFolioFiscalOrig']	=$date->format('d/m/Y H:i:s');
			$paramsOrigen['FolioOrig']				=$this->modelObject->paramsOrigen['FolioOrig'];
			$paramsOrigen['FolioFiscalOrig']				=$this->modelObject->paramsOrigen['FolioFiscalOrig'];
			$paramsOrigen['esquemaOrig']				=$this->modelObject->paramsOrigen['esquemaOrig'];					
			$arraymodel['Factura']=array_merge($paramsOrigen, $arraymodel['Factura']);			
		}
		
		$respuesta    = $this->crearPreview($arraymodel);   // Generar XML y PDF
	
		$idTempFac=$arraymodel['Factura']['IDFac'];	
		
		$this->modelObject->borrarTemporal($idTempFac);
		
		$respuesta ['preview']=true;
		
		return $respuesta;
	}
	
	private function crearPreview($arraymodel){
		$id_usu  = $_SESSION['Auth']['User']['IDUsu'];
		$CFDiEmp = $arraymodel['Factura']['CFDiEmp'];	//Obtengo el tipo de factura a crear, CFD ó CFDi
		$RFCEmisor = $arraymodel['Factura']['RFCEmp'];
		$isXmlParaCFDI = ($CFDiEmp) ? true : false;		//Indica si el XML tendrá formato CFDi ó CFD(Medios Propios)
		$tipo_cfd = ($CFDiEmp) ? 'cfdi' : 'cfd'; 		// es necesario especificar en texto para generar el PDF
		
		// ---------- Generar el nombre de los archivos ----------
		$id_archivo   = $id_usu.rand();
		$nombre_file  = "Rpt".$id_archivo;
		
		//====================================================================================================================
		$fecha=$arraymodel['Factura']['FechaFac'];
		//global $rfcs_2012;		
		$fechaObj = DateTime::createFromFormat('d/m/Y H:i:s', $fecha);
		$fecha2011= DateTime::createFromFormat('d/m/Y H:i:s', '31/12/2011 23:59:59');
		
				
		if (  $fechaObj > $fecha2011){					
			require_once ('eko_framework/app/models/claseFacturaXML_ene_2012.php'); //Version 2.2 y 3.2
		}else{						
			require_once ('eko_framework/app/models/claseFacturaXML.php');		//Version 2.0 y 3.0
		}
		//====================================================================================================================
		
		$facturaXML   = new claseFacturaXML();
		global $RFC_CustomIva;
		if ($_SESSION['Auth']['User']['RFCEmp']==$RFC_CustomIva){			
			
			$facturaXML->Custom_fac_iva_tras =  $arraymodel['Factura']['TotImpTras'] ;			
		}
			
			
		$facturaXML->setNombreXML("tmp/".$nombre_file.".xml");
		$IDFac = $arraymodel['Factura']['IDFac'];
		

		$IDEmp=$arraymodel['Factura']['IDEmp'];
		$model=$this->getModelObject();
		$arrRegimen=$model->select("SELECT Regimen_EmpReg as Regimen 
		FROM cat_empresas_regimen 
		WHERE KEY_Emp_EmpReg=$IDEmp
		ORDER BY Regimen_EmpReg ASC");
		if (empty($arrRegimen)){
			throw new Exception("La empresa debe tener configurado al menos 1 Régimen Fiscal");
		}
		
		$extraParams= isset($model->paramsOrigen) ?  $model->paramsOrigen : array() ;
		$extraParams['regimens']=$arrRegimen;
		
		if (!$facturaXML->generarXML($IDFac,$isXmlParaCFDI,'',$extraParams)){ // Generar XML CFD ó CFDI
			throw new Exception("No pudo generarse el XML: ".$facturaXML->getError(true));
		}
		//
		$validador=new Validador();
		$valRes=$validador->validar("tmp/".$nombre_file.".xml");
		if ( !$valRes['success'] ){
			$mensaje='';
			foreach($valRes['errores'] as $error){
				$mensaje.='<div>'.$error['message'].'</div><br/>';
			}
			return array(
				'success'			=>false,
				'validationError'	=>true,
				'errores'			=>$valRes['errores'],
				'msg'				=>array(
					'titulo'=>'Validation Errors',
					'mensaje'=>$mensaje
				)
			);
		}
				
		if (!generaPDF("tmp/".$nombre_file.".xml", $RFCEmisor, $tipo_cfd, '',$arraymodel)){
			throw new Exception("El Pdf no fué Generado");
		}
		
		@unlink("tmp/".$nombre_file.".xml");				
		return array(
			'success'	=>true,
			'file_id'	=>$id_archivo
		);
	}
	
	private function crearComprobanteDigital($arraymodel,$modoPrueba=0){		
		//------------------------------------------------------------------
		//								GENERAR EL XML
		//------------------------------------------------------------------
		$CFDiEmp=$arraymodel['Factura']['CFDiEmp'];	//Obtengo el tipo de factura a crear, CFD ó CFDi

		$isXmlParaCFDI=($CFDiEmp)? true : false;	//Indica si el XML tendrá formato CFDi ó CFD(Medios Propios)

		//----------------------------------------------------------------------------------------------------------
		//				ARMAR LA RUTA PARA EL COMPROBANTE
		//----------------------------------------------------------------------------------------------------------
		$rutaBase=($CFDiEmp)?'CFDI':'CFD';							//Carpeta donde se almacenará el zip con el xml Y PDF
		$RFCEmisor=$arraymodel['Factura']['RFCEmp'];				//Usado para formar la ruta		y el nombre del archivo 							
		
		$fecha=substr($arraymodel['Factura']['FechaFac'],0,10);		//Obtengo la fecha sin la hora			
		$splitedFecha=preg_split ('/\//',$fecha);					
		$dia=$splitedFecha[0];										//Usado para formar el nombre del archivo 
		$mes=$splitedFecha[1];										//Usado para formar la ruta		y el nombre del archivo 
		$año=$splitedFecha[2];										//Usado para formar la ruta		y el nombre del archivo 
		$año=substr($año,2);										//Usado para formar la ruta		y el nombre del archivo 
		
		if(!$isXmlParaCFDI && $año > 13){
			throw new Exception("No puede generarse Facturacion para CFD.");
		}


		$ruta = "$rutaBase/$RFCEmisor/$año/$mes/";					//Aqui se guardará el archivo
		$serie=$arraymodel['Factura']['SerFol'];					//Usado para formar el nombre del archivo 
		$folio=$arraymodel['Factura']['Folio'];						//Usado para formar el nombre del archivo 
		
        !@rmkdir_r($ruta, 0775, true);								//si no existe crea la ruta
		$nombreXml=$RFCEmisor."_".$serie."_".$folio."_".$año.$mes.$dia.".xml";					
		//----------------------------------------------------------------------------------------------------------
		//				RUTA Y NOMBRE DE ARCHIVO LISTA,	
		//				CREAR EL XML
		//----------------------------------------------------------------------------------------------------------
		
		//====================================================================================================================
		//global $rfcs_2012;		
		$fechaObj = DateTime::createFromFormat('d/m/Y', $fecha);
		$fecha2011= DateTime::createFromFormat('d/m/Y H:i:s', '31/12/2011 23:59:59');
				
		if (  $fechaObj > $fecha2011){
			require_once ('eko_framework/app/models/claseFacturaXML_ene_2012.php'); //Version 2.2 y 3.2
		}else{
			require_once ('eko_framework/app/models/claseFacturaXML.php');		//Version 2.0 y 3.0
		}
		//====================================================================================================================
		
		$facturaXML=new claseFacturaXML();	
		$facturaXML->setNombreXML('tmp/'.$nombreXml);		
		$IDFac=$arraymodel['Factura']['IDFac'];		
		global $RFC_CustomIva;
		if ($_SESSION['Auth']['User']['RFCEmp']==$RFC_CustomIva)
			$facturaXML->Custom_fac_iva_tras =  $arraymodel['Factura']['TotImpTras'] ;
			
		$model=$this->getModelObject();
		$extraParams= isset($model->paramsOrigen) ?  $model->paramsOrigen : array() ;
		
		$IDEmp=$arraymodel['Factura']['IDEmp'];
		
		$arrRegimen=$model->select("SELECT Regimen_EmpReg as Regimen 
		FROM cat_empresas_regimen 
		WHERE KEY_Emp_EmpReg=$IDEmp
		ORDER BY Regimen_EmpReg ASC");
		if (empty($arrRegimen)){
			throw new Exception("La empresa debe tener configurado al menos 1 Régimen Fiscal");
		}
		$extraParams['regimens']=$arrRegimen;
		if (!$facturaXML->generarXML($IDFac,$isXmlParaCFDI,'',$extraParams)){						//GENERAR XML CFD ó CFDI
			throw new Exception("No pudo generarse el XML: ".$facturaXML->getError(true));			
		}
		
		//Se comento porque estaba tronando y no dejaba timbrar Ramon Huerta 04/06/2016
		// $validador=new Validador();
		// $valRes=$validador->validar('tmp/'.$nombreXml);
		// $valRes['success'] = true;
		// if ( !$valRes['success'] ){
			// $mensaje='';
			// foreach($valRes['errores'] as $error){
				// $mensaje.='<div>'.$error['message'].'</div><br/>';
			// }
			// return array(
				// 'success'			=>false,
				// 'validationError'	=>true,
				// 'errores'			=>$valRes['errores'],
				// 'msg'				=>array(
					// 'titulo'=>'Validation Errors',
					// 'mensaje'=>$mensaje
				// )
			// );
		// }
		//----------------------------------------------		
		// throw new Exception("Antes de timbrar");	
		$this->xmlOrigenText=file_get_contents('tmp/'.$nombreXml);//Lo almaceno para despues guardar el contenido en la bdd				
		
		if ($CFDiEmp==1){
			$this->validarFechaFac();
			$modoFacturacion="CFDI";		
			$proceso="timbrada";	//Por si las moscas para el mensaje de error						
			//------------------------------------------------------			
			$cfdiService=new GetCFDI();		
		
			$params =	$cfdiService->timbrar($nombreXml,$modoPrueba); 	
			//------------------------------------------------------
			// El servicio de timbrado nos regresa los siguientes valores:					
			# UUIDFac
			# SelloCFD
			# SelloSAT
			# NumCertSAT
			# FechaTimbrado
			# CadOri
			# 'xml'	
			//------------------------------------------------------
		}else{
			$modoFacturacion="CFD";
			$proceso="sellada";	//Por si las moscas para el mensaje de error
			$params=array(					
				'CadOri'=>$facturaXML->verCadenaOriginal()							
			);
		}	

		//---------------------------------------------------
		//	Ahora que la factura ha sido timbrada o sellada (CFDI o CFD), se PROCEDE A MOVER DE TEMPORAL A TABLA NORMAL
		
		$this->modelObject->timbrado=true;
			
		try{
			$params['modo']=$modoFacturacion;
			$params['IDFac']=$IDFac;	
			$params['SD']=$facturaXML->sello_digital;
			$params['xml_origen']=$this->xmlOrigenText;
			
			if ($modoPrueba==false){
				$arraymodel=$this->modelObject->moverFactura($params);		//MUEVE LA FACTURA DE LA TABLA TEMPORAL A LA TABLA DE FACTURACION	
			}else{
				if ( isset($this->modelObject->paramsOrigen) ){		
					
					$fecha=$this->modelObject->paramsOrigen['FechaFolioFiscalOrig'];
					$date = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
					
					$paramsOrigen['SerieOrig']				=$this->modelObject->paramsOrigen['SerieFolioFiscalOrig'];
					$paramsOrigen['FechaFolioFiscalOrig']	=$date->format('d/m/Y H:i:s');
					$paramsOrigen['FolioOrig']				=$this->modelObject->paramsOrigen['FolioOrig'];
					$paramsOrigen['FolioFiscalOrig']		=$this->modelObject->paramsOrigen['FolioFiscalOrig'];
					//$paramsOrigen['FolioOrig'] ='5090D9E1-7E57-4C8D-ABB7-2FAA7B5943A4';
					$arraymodel['Factura']=array_merge($paramsOrigen, $arraymodel['Factura']);			
				}				
			}								
		}catch(Exception $e){
			//la variable $proceso solo sirve para indicar si fue timbrada (CFDI) o sellada (CFD)
			$idserfol=isset($arraymodel['Factura']['IDSerFol'])? $arraymodel['Factura']['IDSerFol'] : "UNDEFINED";
			$IDFac=isset($arraymodel['Factura']['IDFac'])? $arraymodel['Factura']['IDFac'] : "UNDEFINED";
			if ($modoPrueba==true){
				$IDFac="MODO PRUEBA";
			}
			file_put_contents("tmp/".date("Ymd").".log"," IDFOL = $idserfol,IDFac=$IDFac La factura ya ha sido $proceso, regenere a partir del xml.". $e->getMessage(),FILE_APPEND);	
			return array(
				'success'=>false,
				'msg'=>array(
					'titulo'=>'Error al agregar la factura a la BDD',
					'mensaje'=>"La factura ya ha sido $proceso, regenere a partir del xml.'". $e->getMessage()
				)					
			);	
		}
			
											
		$IDSuc=$arraymodel['Factura']['IDSuc'];
		$contactoSucArr=$this->modelObject->query("SELECT TelSuc,FaxSuc FROM cat_sucursales WHERE IDSuc = $IDSuc;");
		if (sizeof($contactoSucArr)>0){
			$arraymodel['Factura']['TelSuc']=$contactoSucArr[0]['TelSuc'];
			$arraymodel['Factura']['FaxSuc']=$contactoSucArr[0]['FaxSuc'];	
		}else{
			$arraymodel['Factura']['TelSuc']='';
			$arraymodel['Factura']['FaxSuc']='';
		}
		
		
		$cadena= $facturaXML->verCadenaOriginal();											//SE AGREGA LA CADENA ORIGINAL PARA MOSTRARLA EN EL PDF
		$pdf=generaPDF('tmp/'.$nombreXml,$RFCEmisor,$modoFacturacion,$cadena,$arraymodel); 	//<--Genera el PDF 
		//
		if (!$pdf){		
			
			return array(
					'success'=>false,
					'msg'=>array(
						'titulo'=>'El Pdf no Fué Generado',
						'mensaje'=>"La factura ya ha sido $proceso, regenere a partir del xml"
					)					
				);
		}

		if($modoPrueba) {
			try{
				agregarMarcaDeAgua($pdf);
			} catch(Exception $e){
				//echo $e->getMessage();
				throw new Exception("La factura ya ha sido $proceso, regenere a partir del xml. <br/> Error: 114 ".$e->getMessage());
			}
		}
		/*--------------------------COMPRIMIR PDF Y XML----------------------------*/
		$file_pdf= str_ireplace(".xml", ".pdf", $nombreXml);
		$file_zip= str_ireplace(".xml", ".zip", $nombreXml);
					
		//$zipfile->create_file(file_get_contents("tmp/".$nombreXml), $nombreXml); 	
		//$zipfile->create_file(file_get_contents("tmp/".$file_pdf), $file_pdf); 	
		if ($modoPrueba){
			$ruta="tmp/";
		}
		
		$files=array(
			array("tmp/".$nombreXml,$nombreXml),
			array("tmp/".$file_pdf,$file_pdf)			
		);	
		create_zip($files,$ruta.$file_zip);	
		//file_put_contents($ruta.$file_zip, $zipfile->zipped_file());
		
				
		unlink('tmp/'.$nombreXml);	//BORRA EL XML DE LA CARPETA TEMPORAL
		unlink('tmp/'.$file_pdf);	//BORRA EL PDF DE LA CARPETA TEMPORAL
		
		return array(
			'success'=>true,
			'data'=>$arraymodel	,
			'modoPrueba'=>$modoPrueba		
		); 		
	}
	
	/*
	BUSCA Y ENTREGA EL PDF RELACIOADO CON LA FACTURA, EL PDF YA DEBE EXISTIR 
	@params
	ide= El ID de la factura de la cual se requiere el PDF
	*/
	function getPDF(){		
		$IDFac=$_GET['ide'];
		//<SE REVISA QUE LA FACTURA EXISTE, Y QUE EL USUARIO TENGA PERMISO A LA EMPRESA Y/O SUCURSAL QUE EMITIO LA FACTURA
		$IDUsr=$_SESSION['Auth']['User']['IDUsu'];
		$tipoUsu=$_SESSION['Auth']['User']['AdminUsu'];	
		$modoPrueba=isset($_GET['prueba'])?$_GET['prueba']:false;	//SI LA FACTURA FUE CREADA EN MODO PRUEBA, SE BUSCA EN tmp_facturacion (deberia borrarla)
		$modoPrueba=($modoPrueba=='false')?false:$modoPrueba;
		$datos=$this->modelObject->getDatosDeLaFactura($IDFac,$IDUsr,$tipoUsu,$modoPrueba);//Obtengo los datos necesarios para armar la ruta y el nombre del zip		
		//----------------------------------------------------------------------------------------------------------
		//				PREPARO LOS DATOS PARA ENCONTRAR EL PDF
		//----------------------------------------------------------------------------------------------------------														
		$RFCEmisor=$datos['Factura']['RFCEmp'];				//Usado para formar la ruta		y el nombre del archivo 
		$fecha=substr($datos['Factura']['FechaFac'],0,10);		//Obtengo la fecha sin la hora			
		$splitedFecha=preg_split ('/\//',$fecha);					
		$dia=$splitedFecha[0];										//Usado para formar el nombre del archivo 
		$mes=$splitedFecha[1];										//Usado para formar la ruta		y el nombre del archivo 
		$año=$splitedFecha[2];										//Usado para formar la ruta		y el nombre del archivo 
		$año=substr($año,2);										//Usado para formar la ruta		y el nombre del archivo 
		
		if ($modoPrueba){
			$ruta='tmp/';
		}else{
			$rutaBase=($datos['Factura']['CFDiEmp'])?'CFDI':'CFD';		//Ubicacion del zip que contiene el xml Y PDF			
			$ruta = "$rutaBase/$RFCEmisor/$año/$mes/";					//Ruta Armada
			//RUTA PARA UPCCONNECTOR DESDE URL
			$rutaConector = "http://upcconnector.pontuel.mx/CFDI/$RFCEmisor/$año/$mes/"; //Ruta UPCConnector	
		}
					
		$serie=$datos['Factura']['SerFol'];					//Usado para formar el nombre del archivo 
		$folio=$datos['Factura']['Folio'];					//Usado para formar el nombre del archivo 
					
		$nombrePDF=$RFCEmisor."_".$serie."_".$folio."_".$año.$mes.$dia.".pdf";												
		//----------------------------------------------------------------------------------------------------------
		//				DESCOMPRIMO EL PDF DEL ZIP
		//----------------------------------------------------------------------------------------------------------
		$zipFileName = str_ireplace(".pdf", ".zip", $nombrePDF);
		if (!file_exists ( $ruta.$zipFileName )){
			//BUSCAR EN EL CONECTOR
			$zipConector=buscarZipEnConector($ruta, $RFCEmisor, $serie, $folio, "$año$mes$dia");							
			
			if ($zipConector===false){ 	//LANZAR LA EXCEPCION SINO SE ENCONTO EL ARCHIVO
				throw new Exception("No existe el archivo ZIP: ".$zipFileName);
			}						
			
			$zipFileName=$zipConector.'.zip';	//Archivo encontrado, el nombre del archivo tiene formato alamo								
			$nombrePDF =$zipConector.'.pdf';
			if ($zipConector===false){ 	//LANZAR LA EXCEPCION SINO SE ENCONTO EL ARCHIVO
				throw new Exception("No existe el archivo ZIP: ".$zipFileName);
			}							
			//SI EXISTE EL ARCHIVO DESCARGARLO DESDE LA URL
			$ruta = RUTA_CONECTOR."/CFDI/$RFCEmisor/$año/$mes/";
			$zip = $ruta.$zipFileName;
			@copy($zip,"tmp/$zipFileName");							
			if(!file_exists("tmp/$zipFileName")){
				throw new Exception("No existe el archivo ZIP: ".$zipFileName );
			}		
			$ruta = "tmp/";
			//ESTABLECER QUE SE DESCARGO DESDE EL CONECTOR
			$conector = true;	
		}

		$zipfile_origen = new ZipArchive;
		$encontrado=false;
		if ($zipfile_origen->open($ruta.$zipFileName) === true) {
		                    
		    for($i = 0; $i < $zipfile_origen->numFiles; $i++) {
		        if($zipfile_origen->getNameIndex($i)==$nombrePDF){
		        	$encontrado=true;
		        	$zipfile_origen->extractTo($ruta, array($zipfile_origen->getNameIndex($i)));
		        }
		    }
		                    
		    $zipfile_origen->close();
		                    
		}

		unset($zipfile_origen);	//la variable es liberada
		if (!$encontrado){
			throw new Exception("No existe el archivo PDF dentro del ZIP");
		}
		
		$pdf=$ruta.$nombrePDF;
		//header ("Content-Disposition: attachment; filename=$nombrePDF ");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header ("Content-Type: application/pdf");
		header ("Content-Length: ".filesize($pdf));
		header("Content-Disposition: inline; filename=$nombrePDF");
		readfile($pdf);
		// $fp=fopen($pdf, "r");
		// fpassthru($fp);
		
		//if(!$modoPrueba){	//solo cuando NO es modo Prueba, se borra el pdf
			unlink($pdf);
		//}
		
		if ($modoPrueba || $conector){	//SI ES MODO DE PRUEBA BORRO EL ZIP
			unlink($ruta.$zipFileName);
		}
		exit;			
	}
	
	function viewPDF(){	
	
		$id_fac  = $_POST['id_fac'];
		$id_usr  = $_SESSION['Auth']['User']['IDUsu'];
		$tipo_us = $_SESSION['Auth']['User']['AdminUsu'];
		$modoPrueba = isset($_POST['prueba'])  ?  $_POST['prueba'] : false;  // si se creó en modo prueba, se busca en "tmp_facturacion"
		$modoPrueba = ($modoPrueba == 'false') ?  false : $modoPrueba;
		
		if ($_SESSION['Auth']['User']['IDSuc'] != 0){
			$id_empsuc   = $_SESSION['Auth']['User']['IDSuc'];
			$tipo_empsuc = 'SUC';
		} else {
			$id_empsuc   = $_SESSION['Auth']['User']['IDEmp'];
			$tipo_empsuc = 'EMP';
		}
		
		// Revisa que el usuario tenga permiso a la empresa y/o sucursal que emitio la factura
		if ($_SESSION['Auth']['User']['AdminUsu'] == 0){
			if (!$this->modelObject->query("CALL permisoUserEmpresa($id_usr, $id_empsuc, '$tipo_empsuc');")){
				throw new Exception("No tiene acceso a esta operacion.");
			}
		}
		
		// Obtengo los datos necesarios para armar la ruta y el nombre del zip
		$datos   = $this->modelObject->getDatosDeLaFactura($id_fac,$id_usr,$tipo_us,$modoPrueba);
		// Preparo los datos para encontrar el ZIP
		$RFCEmisor = $datos['Factura']['RFCEmp'];  //Usado para formar la ruta y el nombre del archivo
		$fecha     = substr($datos['Factura']['FechaFac'],0,10);  //Obtengo la fecha sin la hora
		$splitedFecha = preg_split ('/\//',$fecha);
		$dia  = $splitedFecha[0];   // Usado para formar el nombre del archivo
		$mes  = $splitedFecha[1];   // Usado para formar la ruta y el nombre del archivo
		$anio = $splitedFecha[2];   // Usado para formar la ruta y el nombre del archivo
		$anio = substr($anio,2);    // Usado para formar la ruta y el nombre del archivo
		
		if ($modoPrueba){
			$ruta='tmp/';
		} else {
			$rutaBase = ($datos['Factura']['CFDiEmp']) ? 'CFDI' : 'CFD';  // Ubicacion del zip que contiene el xml Y PDF
			$ruta = "$rutaBase/$RFCEmisor/$anio/$mes/";   // Ruta Armada
			//RUTA PARA UPCCONNECTOR DESDE URL
			$rutaConector = "http://upcconnector.pontuel.mx/CFDI/$RFCEmisor/$anio/$mes/"; //Ruta UPCConnector	
			//SE USA PARA DETERMINAR SI DESCARGO EL ARCHIVO DEL CONECTOR
			$conector = false;
		}
		$serie = $datos['Factura']['SerFol'];  // Usado para formar el nombre del archivo 
		$folio = $datos['Factura']['Folio'];   // Usado para formar el nombre del archivo 
		
		$nombrePDF   = $RFCEmisor."_".$serie."_".$folio."_".$anio.$mes.$dia.".pdf";
		$zipFileName = str_ireplace(".pdf", ".zip", $nombrePDF);
			
		// Extraer el PDF del ZIP
		if (!file_exists ( $ruta.$zipFileName )){			
			//BUSCAR EN EL CONECTOR
			$zipConector=buscarZipEnConector($ruta, $RFCEmisor, $serie, $folio, "$anio$mes$dia");							
			
			if ($zipConector===false){ 	//LANZAR LA EXCEPCION SINO SE ENCONTO EL ARCHIVO
				throw new Exception("No existe el archivo ZIP: ".$zipFileName);
			}						
			
			$zipFileName=$zipConector.'.zip';	//Archivo encontrado, el nombre del archivo tiene formato alamo								
			$nombrePDF =$zipConector.'.pdf';
			if ($zipConector===false){ 	//LANZAR LA EXCEPCION SINO SE ENCONTO EL ARCHIVO
				throw new Exception("No existe el archivo ZIP: ".$zipFileName);
			}						
						
			//SI EXISTE EL ARCHIVO DESCARGARLO DESDE LA URL
			$ruta = RUTA_CONECTOR."/CFDI/$RFCEmisor/$anio/$mes/";			
			$zip = $ruta.$zipFileName;
			@copy($zip,"tmp/$zipFileName");	
			if(!file_exists("tmp/$zipFileName")){
				throw new Exception("No existe el archivo ZIP: ".$zipFileName );
			}		
			$ruta = "tmp/";
			//ESTABLECER QUE SE DESCARGO DESDE EL CONECTOR
			$conector = true;											
		}		
		

		$zipfile_origen = new ZipArchive;
		$encontrado=false;
		if ($zipfile_origen->open($ruta.$zipFileName) === true) {
		                    
		    for($i = 0; $i < $zipfile_origen->numFiles; $i++) {
		        if($zipfile_origen->getNameIndex($i)==$nombrePDF){
		        	$zipfile_origen->extractTo($ruta, array($zipfile_origen->getNameIndex($i)));
		        	$encontrado=true;
		        }
		    }
		                    
		    $zipfile_origen->close();
		                    
		}

		unset($zipfile_origen);
		//SI SE DESCARGO DESDE EL CONECTOR ENTONCES ELIMINAR EL ARVHIVO DE TMP
		if($conector == true) unlink("tmp/$zipFileName");
		if (!$encontrado){
			throw new Exception("No existe el archivo PDF ($nombrePDF) dentro del ZIP");
		}
		
		// Mover el archivo PDF a "tmp"
		$file_id = $id_usr.rand();
		if (!rename($ruta.$nombrePDF, "tmp/Rpt".$file_id.".pdf")){
			throw new Exception("No se pudo generar el PDF para su descarga");
		}
		
		$response['success'] = true;
		$response['preview'] = true;
		$response['file_id'] = $file_id;
		return $response;
	}
	
	function getZIP(){
		$IDFac=$_GET['ide'];
		//<SE REVISA QUE LA FACTURA EXISTE, Y QUE EL USUARIO TENGA PERMISO A LA EMPRESA Y/O SUCURSAL QUE EMITIO LA FACTURA
		$IDUsr=$_SESSION['Auth']['User']['IDUsu'];
		$tipoUsu=$_SESSION['Auth']['User']['AdminUsu'];	
		$modoPrueba=isset($_GET['prueba'])?$_GET['prueba']:false;	//SI LA FACTURA FUE CREADA EN MODO PRUEBA, SE BUSCA EN tmp_facturacion (deberia borrarla)
		$modoPrueba=($modoPrueba=='false')?false:$modoPrueba;
		$datos=$this->modelObject->getDatosDeLaFactura($IDFac,$IDUsr,$tipoUsu,$modoPrueba);//Obtengo los datos necesarios para armar la ruta y el nombre del zip		
		//----------------------------------------------------------------------------------------------------------
		//				PREPARO LOS DATOS PARA ENCONTRAR EL ZIP
		//----------------------------------------------------------------------------------------------------------														
		$RFCEmisor=$datos['Factura']['RFCEmp'];				//Usado para formar la ruta		y el nombre del archivo 
		$fecha=substr($datos['Factura']['FechaFac'],0,10);		//Obtengo la fecha sin la hora			
		$splitedFecha=preg_split ('/\//',$fecha);					
		$dia=$splitedFecha[0];										//Usado para formar el nombre del archivo 
		$mes=$splitedFecha[1];										//Usado para formar la ruta		y el nombre del archivo 
		$año=$splitedFecha[2];										//Usado para formar la ruta		y el nombre del archivo 
		$año=substr($año,2);										//Usado para formar la ruta		y el nombre del archivo 
		
		if ($modoPrueba){
			$ruta='tmp/';
		}else{
			$rutaBase=($datos['Factura']['CFDiEmp'])?'CFDI':'CFD';		//Ubicacion del zip 		
			$ruta = "$rutaBase/$RFCEmisor/$año/$mes/";					//Ruta Armada
			//RUTA PARA UPCCONNECTOR DESDE URL
			//$rutaConector = "http://upcconnector.pontuel.mx/CFDI/$RFCEmisor/$año/$mes/"; //Ruta UPCConnector					
		}
					
		$serie=$datos['Factura']['SerFol'];					//Usado para formar el nombre del archivo 
		$folio=$datos['Factura']['Folio'];					//Usado para formar el nombre del archivo 
					
		$zipFileName=$RFCEmisor."_".$serie."_".$folio."_".$año.$mes.$dia.".zip";												
		
		if (!file_exists ( $ruta.$zipFileName )){
			//BUSCAR EN EL CONECTOR
			$zipConector=buscarZipEnConector($ruta, $RFCEmisor, $serie, $folio, "$año$mes$dia");							
			
			if ($zipConector===false){ 	//LANZAR LA EXCEPCION SINO SE ENCONTO EL ARCHIVO
				throw new Exception("No existe el archivo ZIP: ".$zipFileName);
			}						
			
			$zipFileName=$zipConector.'.zip';	//Archivo encontrado, el nombre del archivo tiene formato alamo		
			//SI EXISTE EL ARCHIVO DESCARGARLO DESDE LA URL
			$ruta = RUTA_CONECTOR."/CFDI/$RFCEmisor/$año/$mes/";													
		}

		$zip=$ruta.$zipFileName;

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=$zipFileName");
		readfile($zip);
			
		if ($modoPrueba){	//SI ES MODO DE PRUEBA BORRO EL ZIP
			//unlink($ruta.$zipFileName);
		}		
		exit;			
	}
	
	private function buscarZipEnConector($ruta, $RFCEmisor, $serie, $folio, $fecha){				
		require_once('eko_framework/includes/nusoap_lib/nusoap.php');
		// Create the client instance
		$client = new nusoap_client('http://mfw/ws_get_zipAlamo.php?wsdl',true);								  
		// Call the SOAP method
		$result = $client->call( 'buscarArchivo', array(
			'ruta' 		=> $ruta,
			'RFCEmisor' => $RFCEmisor,
			'serie' => $serie,
			'folio' => $folio,
			'fecha' => $fecha) 
		);
		// return the result
		if ( empty ($result) ){
			return false;
		}else{
			return $result;
		}	
	}
	function getXML(){
		$id_fac  = $_POST['id_fac'];
		$id_usr  = $_SESSION['Auth']['User']['IDUsu'];
		$tipo_us = $_SESSION['Auth']['User']['AdminUsu'];
		$modoPrueba = isset($_POST['prueba'])  ?  $_POST['prueba'] : false;  // si se creó en modo prueba, se busca en "tmp_facturacion"
		$modoPrueba = ($modoPrueba == 'false') ?  false : $modoPrueba;
		
		if ($_SESSION['Auth']['User']['IDSuc'] != 0){
			$id_empsuc   = $_SESSION['Auth']['User']['IDSuc'];
			$tipo_empsuc = 'SUC';
		} else {
			$id_empsuc   = $_SESSION['Auth']['User']['IDEmp'];
			$tipo_empsuc = 'EMP';
		}
		
		// Revisa que el usuario tenga permiso a la empresa y/o sucursal que emitio la factura
		if ($_SESSION['Auth']['User']['AdminUsu'] == 0){
			if (!$this->modelObject->query("CALL permisoUserEmpresa($id_usr, $id_empsuc, '$tipo_empsuc');")){
				throw new Exception("No tiene acceso a esta operacion.");
			}
		}
		
		// Obtengo los datos necesarios para armar la ruta y el nombre del zip
		$datos   = $this->modelObject->getDatosDeLaFactura($id_fac,$id_usr,$tipo_us,$modoPrueba);
		// Preparo los datos para encontrar el ZIP
		$RFCEmisor = $datos['Factura']['RFCEmp'];  //Usado para formar la ruta y el nombre del archivo
		$fecha     = substr($datos['Factura']['FechaFac'],0,10);  //Obtengo la fecha sin la hora
		$splitedFecha = preg_split ('/\//',$fecha);
		$dia  = $splitedFecha[0];   // Usado para formar el nombre del archivo
		$mes  = $splitedFecha[1];   // Usado para formar la ruta y el nombre del archivo
		$anio = $splitedFecha[2];   // Usado para formar la ruta y el nombre del archivo
		$anio = substr($anio,2);    // Usado para formar la ruta y el nombre del archivo
		
		if ($modoPrueba){
			$ruta='tmp/';
		} else {
			$rutaBase = ($datos['Factura']['CFDiEmp']) ? 'CFDI' : 'CFD';  // Ubicacion del zip que contiene el xml Y PDF
			$ruta = "$rutaBase/$RFCEmisor/$anio/$mes/";   // Ruta Armada
		}
		$serie = $datos['Factura']['SerFol'];  // Usado para formar el nombre del archivo 
		$folio = $datos['Factura']['Folio'];   // Usado para formar el nombre del archivo 
		
		$nombreXML   = $RFCEmisor."_".$serie."_".$folio."_".$anio.$mes.$dia.".xml";
		$zipFileName = str_ireplace(".xml", ".zip", $nombreXML);
		
		// Extraer el PDF del ZIP
		if (!file_exists ( $ruta.$zipFileName )){
			throw new Exception("No existe el archivo ZIP: ".$ruta.$zipFileName );
		}

		$zipfile_origen = new ZipArchive;
		$encontrado=false;
		if ($zipfile_origen->open($ruta.$zipFileName) === true) {
		                    
		    for($i = 0; $i < $zipfile_origen->numFiles; $i++) {
		        if($zipfile_origen->getNameIndex($i)==$nombreXML){
		        	$encontrado=true;
		        	$zipfile_origen->extractTo($ruta, array($zipfile_origen->getNameIndex($i)));
		        }
		    }
		                    
		    $zipfile_origen->close();
		                    
		}

		unset($zipfile_origen);
		if (!$encontrado){
			throw new Exception("No existe el archivo XML dentro del ZIP");
		}
		
		// Mover el archivo XML a "tmp"
		if (!rename($ruta.$nombreXML, "tmp/".$nombreXML)){
			throw new Exception("No se pudo colocar el XML para su descarga");
		}
		
		$response['success']   = true;
		$response['file_name'] = $nombreXML;
		return $response;
	}
	
	function load(){

        $modelObject = $this->modelObject;

        $params=$_POST['Factura'];
        $empresaId=$params['IDEmp'];
		if ($empresaId==0){
			$response['success']=false;
			$response['msg']='Es necesario Logearse en una Empresa para crear Facturas';
			return $response;
		}
		
		if (!isset($params['TipDoc'])){
			$response['success']=false;
			$response['msg']=array('titulo'=>'Facturacion','mensaje'=>'Es necesario espcificar el tipo de documento para proseguir');
			return $response;
		}
		
		$sucursalId=$params['IDSuc'];
        $IDFac=$params['IDFac'];
        if ($IDFac==0){  
        	//SI LA FACTURA ES NUEVA, VERIFICAR QUE EL EMISOR (Emp o Suc) NO ESTÉ SUSPENDIDA
        	if ($sucursalId==0){
        		$query="SELECT StatusEmp as status,ComEmp as nombre FROM cat_empresas WHERE IDEmp=$empresaId";
        		$arrStatus=$modelObject->query($query);        		
				$tipo_de_negocio="Empresa";	
        	}else{
        		$query="SELECT StatusSuc as status,NomSuc as nombre FROM cat_sucursales WHERE IDSuc=$sucursalId";
        		$arrStatus=$modelObject->query($query);
        		$tipo_de_negocio="Sucursal";
        	}

        	$status=$arrStatus[0]['status'];
        	if ($status=='I'){
        		$nombre=$arrStatus[0]['nombre'];
        		$response=array(
        			'success'=>false,
        			'data'=>array(),
        			'msg'=>array(
        				'titulo'=>'Facturación',
						'mensaje'=>"No puede crear facturas cuando la $tipo_de_negocio ($nombre) está suspendida"        		
        			)
        		);
        		return $response;
        	}
        	//--------------------------------------------------------------------------------
			
        	if (isset($params['FolOrdVen'])){
				$FolOrdVen=$params['FolOrdVen'];
				$folios=array();
				$folios[0]['FolOrdVen']=$FolOrdVen;					
				$data=$modelObject->importarVariasOrdenes( $empresaId, $folios );				
			}else if (isset($params['folios'])){
				$folios=json_decode(stripslashes($params['folios']),true);				
				$data=$modelObject->importarVariasOrdenes($empresaId,$folios);			
			}else{
				$data=array();
				$data['Factura']=$modelObject->getInitialInfo($empresaId,$sucursalId);
				$data['Factura']['TipDoc']=$params['TipDoc'];							
			}
			
			
			switch($data['Factura']['TipDoc']){
				case 'FACTURA':
					$data['Factura']['TipComp']='ingreso';
					break;
				case 'NOTA DE CREDITO':
					$data['Factura']['TipComp']='egreso';
					break;
				case 'RECIBO DE HONORARIOS':
					$data['Factura']['TipComp']='ingreso';
					break;
				case 'RECIBO DE ARRENDAMIENTO':
					$data['Factura']['TipComp']='ingreso';
					break;
				default:						
			}
			
        }else{
			$temp=false;
            $data=$modelObject->getById($IDFac,$temp);
			switch($data['Factura']['FormaPago']){
				case strtoupper("Pago en una sola EXHIBICIÓN"):
					$data['Factura']['FormaPago']=1;
				break;
				case strtoupper("PAGO EN PARCIALIDADES"):
					$data['Factura']['FormaPago']=3;
				break;
				default:
				//	throw new Exception("asd".$data['Factura']['numParcialidad']);
					if (is_numeric($data['Factura']['parcialidadA'])){ //Pago en parcialidades
						$data['Factura']['FormaPago']=2;
					}
				break;
			}
        }               
        $response=array();
        $response['success']=true;
        
        $response['data']=$data;
		if (isset($folios)){
			$response['data']['folios']=$folios;
		}
        return $response;
    }
	
	function getseries(){	
		$empresaId=$_POST['IDEmp'];
		$sucursalId=$_POST['IDSuc'];
		$series=$this->modelObject->getSeriesYFolios($empresaId,$sucursalId);
		if (sizeof($series)==0){
			throw new Exception("No tiene series asignadas");
		}
		$response=array();
		$response['success']=true;
		$response['data']=$series;
		return $response;
	}
		
	function enviarCorreo(){
		require_once("templates/mailfactura1.php");
		$emisor=$_REQUEST["de"];			//email
		$destinatarios=$_REQUEST["para"];	//emails separados por comas	
		$cc=$_REQUEST["cc"];				//emails separados por comas o cadena vacia
		$cuerpo=$_REQUEST["observaciones"];	//texto
		$asunto=$_REQUEST["asunto"];	
		
		$facturas=$_POST["facturas"];		//IDs de facturas separados por comas
		$titulo="Facturas";
		$modoDeEnvio=$_POST["modoDeEnvio"];
		$modoPrueba=($_POST["modoPrueba"]=='true')? true : false;
		
		$emailSender=new FacturasPorEmail($asunto, $cuerpo, $destinatarios, $cc,$facturas,$emisor,$modoPrueba);
		
		$tipo=(isset($_POST["tipo"])) ? $_POST["tipo"] : ''; 

		$enviados=false;
		switch($modoDeEnvio){
			case 'zips':
				$enviados=$emailSender->enviarZips($tipo);
			break;
			case 'sueltos':
				$enviados=$emailSender->enviarDescomprimidos($tipo);
			break;
		}
		
		if ($enviados==true){
			$response=array(
				'success'=>true,
				'msg'=>array(
					'titulo'=>"Facturación",
					'mensaje'=>"Facturas enviadas"
				)
			);
		
		}else{
			$response=array(
				'success'=>false,
				'msg'=>array(
					'titulo'=>"Facturación",
					'mensaje'=>"No pudieron enviarse los correos"
				)
			);
		}
			return $response;		
	}
	
	/*Regresa el email del contacto del negocio y el email del cliente
	 * Además un arreglo donde cada elemento contiene el id de la factura y el 
	 * */
	function prepararmail(){
		//$facturas=json_decode(stripslashes($params['facturas']),true);	
		$facturas=$_POST['facturas'];
		//$facturas="48,49,50";
		$datos=$this->modelObject->paraPrepararMail($facturas);				
		$facturas=array();
		
		$tamaño=sizeof($datos);
		$fecha=date('d/m/Y h:i A',time());
		if ($tamaño==1){
			$asunto="Recibiste una factura electrónica ".$fecha;
		}else{
			$asunto="Recibiste $tamaño facturas electrónicas ".$fecha;
		}

		if ( !empty($datos[0]['MailConSuc']) ){
			$emailDe=$datos[0]['MailConSuc'];
		}else if( !empty($datos[0]['MailConEmp']) ){
			$emailDe=$datos[0]['MailConEmp'];
		}else{
			$sql="SELECT smtp_usr_par FROM cat_parametros WHERE activo_par='S';";
			$arr=$this->modelObject->select($sql);
			if (!empty($arr)){
				$emailDe=$arr[0]['smtp_usr_par'];
			}else{
				$emailDe='';
			}			
		}
		
		$response=array(
			'success'=>true,
			'data'=>$datos,
			'asunto'=>$asunto,
			'fecha'=>$fecha,
			'emailDe'=>$emailDe
			//'templateData'=>$templateData
		);
		
		return $response;
	}
	
	function imprimeMailTemplate(){
		require_once ('templates/mailfactura1.php');
		//$nomComEmi =utf8_decode(strtolower($_POST['nomComEmi']));
		//$nomComEmi  = ucwords($nomComEmi );
		$nomComEmi =mb_strtolower($_POST['nomComEmi'],'UTF-8');
		$nomComEmi =ucwords($nomComEmi );
		
		$nomComOrSocRec =mb_strtolower($_POST['nomComOrSocRec'],'UTF-8');
		$nomComOrSocRec  = ucwords($nomComOrSocRec );
		
		$razSocEmi =mb_strtolower($_POST['razSocEmi'],'UTF-8');
		$razSocEmi  = ucwords($razSocEmi );
		
		$templateData=array(
			'nomComEmi'		 =>$nomComEmi
			,'nomComOrSocRec'=>$nomComOrSocRec
			,'razSocEmi'	 =>$razSocEmi
			,'facturas'	 	 =>json_decode(stripslashes($_POST['facturas']),true)
		);
		imprimeTemplate($templateData);
	}
	function generarReporte(){
	 	$params = $_POST;
	 	$params['IDEmp']=$_SESSION['Auth']['User']['IDEmp'];
	 	$params['IDSuc']=$_SESSION['Auth']['User']['IDSuc'];
	 	
		$formatos=array(
	 		'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	);
	 	
		if ($params['IDEmp']=='' || !isset($params['IDEmp'])){
			throw new Exception("Es necesario logearse en una Empresa para buscar sus Facturas correspondientes");
		}
		$reporte=new ReporteDeFacturacion();
		
		$pdf=$reporte->generarReporte($params,$formatos);
		//--------------------------------------
		//El cliente realizará la descarga del PDF mediante ajax, para ocultar el nombre y ruta real del archivo, almaceno en la sesion del usuario
		//   repFact=array(
		//		'rand'=>$random
		//		'path'=>$pdf 
		//	)
		//--------------------------------------
		mt_srand (time());
		//	generamos un número aleatorio
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repFact']['rand']=$numero_aleatorio ;
		$_SESSION['repFact']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
	}
	
	function getPDFRepFact(){		
		if (!isset($_SESSION['repFact'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repFact']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repFact']['pdf'];
		
		$reporte=new ReporteDeFacturacion();
		$reporte->getPDF($pdfName);
	}
	
	function getPDFRepFactSaldoFolios(){		
		if (!isset($_SESSION['repFact'])){				
			throw new Exception('El archivo ha caducado, realice una nueva consulta');
		}
		if (!isset($_SESSION['repFact']['pdf'])){				
			throw new Exception('Se ha perdido la referencia al archivo, realice una nueva consulta');
		}
		$pdfName=$_SESSION['repFact']['pdf'];
		
		$reporte=new ReporteDeSaldosFolios();
		$reporte->getPDF($pdfName);
	}
	
	function generarReporteSaldoFolios(){
	 	$params = $_POST;
		
	 	// $params['RFC']= isset($_POST['RFC']) ? $_POST['RFC'] : '';
	 	
		// $formatos=array(
	 		// 'decimales'=>$_SESSION['Auth']['Parametros']['dec_mon_par'],
			// 'texto'=>$_SESSION['Auth']['UserConfig']['forUsu']
	 	// );
		$formatos=array(
	 		'decimales'=>$params['dec_mon_par'],
			'texto'=>$params['forUsu']
	 	);
		// print_r($formatos);
		// exit;
		$reporte=new ReporteDeSaldosFolios();
		
		$pdf=$reporte->generarReporte($params,$formatos);
		//--------------------------------------
		//El cliente realizará la descarga del PDF mediante ajax, para ocultar el nombre y ruta real del archivo, almaceno en la sesion del usuario
		//   repFact=array(
		//		'rand'=>$random
		//		'path'=>$pdf 
		//	)
		//--------------------------------------
		mt_srand (time());
		//	generamos un número aleatorio
		$numero_aleatorio = mt_rand(0,5000); 
		$_SESSION['repFact']['rand']=$numero_aleatorio ;
		$_SESSION['repFact']['pdf']=$pdf ;		
		$response=array(
			'success'=>true,
			'data'=>array(
				'identificador'=>$numero_aleatorio
			)
		);
		return $response;
	}
	
	function generarReporteSaldoFoliosExcel(){
			$params = $_GET;
			$reporte=new ReporteDeSaldosFolios();
			$pdf=$reporte->generarReporteExcel($params);
	}
	
	function getFacturaOrigen(){
		if (!isset($_POST['SerFol']))throw new Exception("Parámetros inconrrectos");
		if (!isset($_POST['Folio']))throw new Exception("Parámetros inconrrectos");
		if (empty($_POST['SerFol'])){
			$SerFol='IS NULL';
		}else{
			$SerFol="='".$_POST['SerFol']."'";
		}
		
		if (empty($_POST['Folio'])){
			throw new Exception("Debe proporcionar el folio para buscar la factura");
		}
		if (! is_numeric($_POST['Folio'])){
			throw new Exception("El folio proporcionado debe ser numérico");
		}
		$Folio=$_POST['Folio'];
		$IDEmp=$_SESSION['Auth']['User']['IDEmp'];
		$sqlFacturaOrigen="SELECT IDFac,UUIDFac,SerFol,Folio,DATE_FORMAT(FechaFac,'%d/%m/%Y %H:%i:%s') as FechaFac,
		if (UUIDFac='','CFD','CFDI') as esquemaOrig,
		Total,numParcialidad,totParcialidades,TipoFactura,TipoCambio FROM facturacion 		
		WHERE SerFol $SerFol AND Folio=$Folio AND IDEmp=$IDEmp";
		
		$model=$this->getModelObject();
		$arrOrigen=$model->select($sqlFacturaOrigen);
		if (empty($arrOrigen)){
			throw new Exception("La Factura origen no ha sido encontrada");
		}
		if ( empty($arrOrigen[0]['IDFac']) ){
			return array(
				'success'=>false,
				'msg'=>array(
					'titulo'=>'Factura Origen No Encontrada',
					'mensaje'=>'La combinación Serie-Folio no produjo resultados.',
					'icon'=>'INFO'
				)
			);
		}
		$IDFac=$arrOrigen[0]['IDFac'];//ID de la factura Origen
		//sugiere el siguiente número de parcialidad
		$sqlNumParcialidad="SELECT MAX(numParcialidad) as numParcialidad FROM facturacion_parcialidades
		LEFT JOIN facturacion ON IDFac=KEY_Factura_Par
		WHERE KEY_Factura_Origen_Par=$IDFac AND status!='C'";
		$arrNumParcialidad=$model->select($sqlNumParcialidad);	
		/*
		//Buscar parcialidades faltantes, esto se puede dar por haber cancelado una factura-parcialidad o por haber saltado
		// de parcialidad
		$sqlNumParcialidad="SELECT numParcialidad as numParcialidad 
		FROM facturacion_parcialidades 
		LEFT JOIN facturacion ON IDFac=KEY_Factura_Par 
		WHERE KEY_Factura_Origen_Par=$IDFac AND status!='C' ORDER BY numParcialidad ASC";		
		throw new Exception($sqlNumParcialidad);
		$arrNumParcialidad=$model->select($sqlNumParcialidad);	
		
		$numParcialidades=sizeof($arrNumParcialidad);
		for($i=0; $i<$numParcialidades; $i++){
			$numPar=intval($numParcialidades[0]['numParcialidad']);
			if ($numPar!=$i+1){
				//parcialidad faltante
			}
		}*/
		if ( empty($arrNumParcialidad[0]['numParcialidad']) ){
			$arrOrigen[0]['numParcialidad']=1;
		}else{
			$parcialidad=intval( $arrNumParcialidad[0]['numParcialidad'] );
			$parcialidad++;
			$arrOrigen[0]['numParcialidad']=$parcialidad;
		}
		
		$sqlSumaParcialidades="SELECT SUM(Monto_Par) as sumParcialidades FROM facturacion_parcialidades
		LEFT JOIN facturacion ON IDFac=KEY_Factura_Par 
		WHERE KEY_Factura_Origen_Par=$IDFac AND status!='C'";
		$arrParcialidad=$model->select($sqlSumaParcialidades);
		
		$saldo=floatval($arrOrigen[0]['Total']) - floatval($arrParcialidad[0]['sumParcialidades']);
		$saldo=formatearMoneda($saldo);
		$arrOrigen[0]['saldo']=$saldo;
		$arrOrigen[0]['Total']=formatearMoneda($arrOrigen[0]['Total']);
		
		return array(
			'success'=>true,
			'data'=>$arrOrigen[0]
		);		
	}
	function validarFechaFac(){
		//SOLO PARA CFDI
		$concatenada=$_POST['FechaFac']." ".$_POST['horaFac'];				
		$date = DateTime::createFromFormat('d/m/Y h:i A', $concatenada);
    	//si no pudo convertirse se lanza un error
		if (!is_object($date)){
			throw new Exception("La Fecha o el formato es incorrecto (".$concatenada."). Deberia ser dd/mm/aaaa hh:mm am/pm");
		}
		$fechaActualString=date("d/m/Y H:i:s");

		//Validar que la fecha de la factura esté dentro de las 72 horas		
		$timestamp_actual=time();
		$timestamp_fecha_a_comparar=$date->getTimestamp();
		
		$tres_dias_antes=$timestamp_actual -  (3 * 24 * 60 * 60);
		if ($timestamp_fecha_a_comparar<$tres_dias_antes){
			$fecha72horas=date( 'd/m/Y h:i:s A', $tres_dias_antes);
			throw new Exception("La fecha de la factura debe ser mayor que $fecha72horas (72 horas de la fecha actual)");
		}
	}
	
	private function cancelarMovimientos($facturaId){
		
		//Obtengo la fecha de la factura y la fecha de cancelacion, la manera de cancelar el movimiento en inventarios depende de si es el mismo dia o no
		$sql='SELECT DATE_FORMAT(FechaFac,"%Y/%m/%d") as FechaFac,DATE_FORMAT(FecCan,"%Y/%m/%d") AS FechaCan, FecCan FROM facturacion WHERE IDFac='.$facturaId;
		$model=$this->getModelObject();
		$arrData=$model->select( $sql );
		if ($arrData[0]['FechaFac']==$arrData[0]['FechaCan']){
			$mismaFecha=true;
		}else{
			$mismaFecha=false;
		}
				
		if ( $mismaFecha ){
			$sqlConceptos="SELECT IDDet, Sku, KEYConFacDet FROM facturacion_detalle WHERE IDFac=$facturaId;";
			$detalleModel=new InventarioDetalleModel();
			$arrConceptos=$detalleModel->select($sqlConceptos);
			foreach($arrConceptos as $concepto){
				$sql_KEYMovimiento	="SELECT IDInventarioDet, KEYAlmacenDet
				FROM inventarios_movimientos_detalle WHERE KEYReferenciaDet=".$concepto['IDDet'].' AND KEYMovimientoDet=-1;';						
				$arrKeyMovimiento	=$detalleModel->select($sql_KEYMovimiento);
				if (!empty($arrKeyMovimiento) ){
					$IDInventarioDet	=$arrKeyMovimiento[0]['IDInventarioDet'];
					$detalleModel->delete($IDInventarioDet, $arrData[0]['FecCan']);		//<-------------------Borra el movimiento
					$this->regresaSku($arrKeyMovimiento[0]['KEYAlmacenDet'], $concepto['KEYConFacDet'], $concepto['Sku']);
				}
			} 
		}else{
			$model=$this->getModelObject();			
			$arrFac=$model->getById($facturaId, FALSE);			
			$this->cancelarFacturaInventario($arrFac['Factura'], $arrFac['Detalles']);			
		}
	}
	
	private function registrarMovimiento($maestro,$conceptos){
		
		//======================================================================================================
		//Revisar si hay una orden de venta relacionada con esta factura
		$sql="SELECT IDOrdVen FROM
		orden_venta WHERE KEYFacOrdVen=".$maestro['IDFac'];		
		$model=$this->getModelObject();
		$arrOrdVen=$model->select($sql);
		if ( !empty($arrOrdVen) ){
			return true;	//Si la orden existe no registra movimientos
		}
		//Prepara los datos 
		$detalleModel	=new InventarioDetalleModel();			
		$date			=DateTime::createFromFormat ('d/m/Y H:i:s' , $maestro['FechaFac']);
		$fecha			=$date->format('Y-m-d H:i:s');
		$generaRecosteo	=true;
		$log			=false;
		$recuperado		=false;
		$transaccionIniciada=true;
		$ser_fol=$maestro['SerFol'].'-'.$maestro['Folio'];
		foreach($conceptos as $concepto){
			if ($concepto['TipoArt'] == 'S'){
				//Solo los productos Y Kits son registrados				
				continue;
			}
			//---------------------------
			//Se realiza el mapeo de la orden de venta al detalle del movimiento	
			$PIvaDet	=0;
			$IvaDet		=0;
			$IEPSDet	=0;
			$PIEPSDet	=0;			
			//---Busca un movimiento relacionado a este detalle de venta.
			$sql_KEYMovimiento="SELECT IDInventarioDet,KEYAlmacenDet 
			FROM inventarios_movimientos_detalle WHERE KEYReferenciaDet=".$concepto['IDDetalle'].' AND KEYReferenciaDet=-1;';												
			$arrKeyMovimiento=$detalleModel->select($sql_KEYMovimiento);
					
			if ( empty($arrKeyMovimiento) ){
				$IDInventarioDet=0;
				$concepto['PrecioU']=$concepto['Importe'] / $concepto['Cantidad'];				
				if (empty($_SESSION['Auth']['Almacen']) ){
					throw new MyException("Seleccione al almacén que dará salida a los productos.",'Seleccione un almacén','WARNING');
				}
				$KEYAlmacen=$_SESSION['Auth']['Almacen']['IDAlmacen'];
			}else{
				$IDInventarioDet	= $arrKeyMovimiento[0]['IDInventarioDet'];
				$KEYAlmacen			= $arrKeyMovimiento[0]['KEYAlmacenDet'];
			}
			$importe	=$concepto['Importe'];
			$subtotal	=$concepto['Importe']-$concepto['DescuentoPesos'];	
			
			$detalleMovimiento=array(
				'IDInventarioDet' 			=> $IDInventarioDet,
				'FechaMovDet'				=> $fecha,	
				'KEYReferenciaDet' 			=> $concepto['IDDetalle'],
				'KEYProductoDet' 			=> $concepto['KEYProdServ'],
				'TipoProductoDet'			=> $concepto['TipoArt'],
				'KEYUDMProductoDet' 		=> 1, //<----------------------------------
				'CantidadDet' 				=> $concepto['Cantidad'],
				'CostoUDet' 				=> $concepto['PrecioU'],
				'TImporteDet'				=> $importe,
				'TDescuentoDet' 			=> $concepto['DescuentoPesos'],
				'TDescPorDet' 				=> $concepto['DescuentoPorcentaje'],
				'TSubTotalDet'    			=> $subtotal,
				'TImpuestosDet' 			=> $concepto['IvaPesos'],
				'PImpuestosDet' 			=> $concepto['SumPorImp'],
				'TTotalDet' 				=> $concepto['Total'],
				'ReferenciaInventarioDet' 	=> 'FAC-'.$ser_fol,
				'KEYMovimientoDet' 			=> -1,	//
				'KEYTipoMovimientoDet'		=> 2,	//SALIDA
				'GenCosMovimientoDet'		=> 1,
				'KEYAlmacenDet' 			=> $KEYAlmacen, //			
				'PIvaDet' 					=> $concepto['PIvaTras'],
				'IvaDet' 					=> $concepto['IvaTras'],
				'IEPSDet'					=> $concepto['PIepsTras'],
				'PIEPSDet'					=> $concepto['IepsTras'],
				'SKUs'						=> $concepto['SKUs']
			);
			$componentes=array();
			
			if ($concepto['TipoArt']=='K'){				
				$componentes=$concepto['componentes'];
				
				for($i=0; $i<sizeof($componentes); $i++){
					if ( !isset($componentes[$i]['Subtotal']) ){
						$componentes[$i]['Subtotal'] = $componentes[$i]['Importe'];
					}
				}
			}
			
			$arr=$detalleModel->save($detalleMovimiento, $recuperado, 0, true, $componentes);	
		}
	}
	
	#===================================================================================================================================
	#				
	#===================================================================================================================================	
	private function procesarKits( $detalles, $almacenId){
		//Se revisa la existencia de cada componente del kit, si la existencia no es suficiente se proponen remplazos
		//Si el componente ya tiene remplazo no se revisa la existencia
		
		//Los sustitutos son sugeridos por el sistema siempre que el concepto-kit sea nuevo o el concepto-kit tenga una fecha de creacion
		//superior a la fecha de modificacion del kit (esto de las fechas es para mantener sincronizacion).
		
		$kitsConRemplazos=array();
		$ref=0;
		$solicitar_confirmacion_de_remplazo=false;
		//se analiza cada elemento del detalle
		
		for($i=0; $i<sizeof( $detalles ); $i++){
		
			//En este ciclo solo nos interesa los kits.
			if( $detalles[$i]['TipoArt'] != 'K' ) continue;			
			
			// si el elemento componentes no está definido, no hay mas que hacer aquí
			if( !isset($detalles[$i]['componentes']) ) continue;
			
			$remplazos=array();
			
			$componentes= ( is_string($detalles[$i]['componentes']) )?  
				json_decode($detalles[$i]['componentes'], true) : $detalles[$i]['componentes'];			
			
			//buscar el sustituto de cada componente
			for($y=0; $y<sizeof($componentes); $y++){			
				//reviso si tiene un sustituto seleccionado
				$indice_del_seleccionado = -1;	//en esta variable almacenarè el indice del sustituto seleccionado en caso de que exista.
				
				if ( !empty( $componentes[$y]['sugerencias'] ) )						
				for($z=0; $z<sizeof( $componentes[$y]['sugerencias'] ); $z++){
					if ( !empty($componentes[$y]['sugerencias'][$z]['seleccionado']) ){
						$indice_del_seleccionado=$z;
						break;
					}
				}

				if ( $indice_del_seleccionado>-1 ){
					$idRemplazado=$componentes[$y]['KEYProdServ'];
					$componentes[$y]=$componentes[$y]['sugerencias'][$indice_del_seleccionado];
					
					$componentes[$y]['remplazado_id']=$idRemplazado;
					$componentes[$y]['Subtotal']=0;
					$componentes[$y]['DescuentoPesos']=0;
					$componentes[$y]['Total']=0;
					$componentes[$y]['DescuentoPorcentaje']=0;
					
					continue;	//analizar el siguiente componente
				}		
				
				//------------------------------------------------------------------------------------------				
				//si no hay un sustituto seleccionado llegamos a este punto
				//------------------------------------------------------------------------------------------				
				if(  !empty($componentes[$y]['seleccionado']) ){
					//Si el mismo componente ha sido seleccionado como sustituto,
					$componentes[$y]['remplazado_id'] = $componentes[$y]['KEYProdServ'];
					$componentes[$y]['Subtotal']=0;
				}else{					
					$resp=$this->revisarExistencia( $componentes[$y]['KEYProdServ'], $almacenId, $componentes[$y]['Cantidad']);
					if ( !$resp['success'] ){
						$solicitar_confirmacion_de_remplazo=true;
						//Cuando es un registro nuevo, se obtienen los remplazos del catalogo del kit.						
						//Cuando es un registro editado, se compara la fecha de la orden y la fecha de modificacion del kit
						//si la fecha de modificacon del kit es mayor a la fecha de la orden, los remplazos se obtienen de
						//la orden de venta, (solo se obtiene 1 remplazo a lo mucho), se envia una notificacion al usuario
						$remplazos= $this->getRemplazos( $detalles[$i]['KEYProdServ'], $componentes[$y]['KEYProdServ'] ,$almacenId);
						//$registroNuevo= empty( $detalles[$i]['IDDetalle'] )? true: false ;
						$remplazos= $this->getRemplazos( $detalles[$i]['KEYProdServ'], $componentes[$y]['KEYProdServ'] ,$almacenId);						
						//----------------------------------------------------------------------------------------------------------
						$componentes[$y]['sugerencias']=$remplazos;
						$componentes[$y]['existenciaSuficiente']=false;
						$componentes[$y]['existenciaEnAlmacen']=$resp['existenciaEnAlmacen'];
						$componentes[$y]['existenciaTotal']=$resp['existenciaTotal'];
					}else{
						$componentes[$y]['remplazado_id'] = $componentes[$y]['KEYProdServ'];
						$componentes[$y]['existenciaSuficiente']=true;
						$componentes[$y]['existenciaEnAlmacen']=$resp['existenciaEnAlmacen'];
						$componentes[$y]['existenciaTotal']=$resp['existenciaTotal'];
					}
				}
			}											
			$detalles[$i]['componentes']=$componentes;
			//$kitsConRemplazos[]=$kit;
		}

		$respuesta=array(
			'codigo'	 =>100,	
			'success'	 =>!$solicitar_confirmacion_de_remplazo,
			//'solicitar_confirmacion_de_remplazo'=>$solicitar_confirmacion_de_remplazo,
			'data'		 =>array(),
			'mensaje'	 =>'Seleccione los componentes del kit',
			'detalles'=>$detalles
		);

		return $respuesta;
	}
	
	private function revisarExistencia($productoId,$almacenId, $cantidadSolicitada){
		
		if ($almacenId==0) return true;		
		$sqlExistencia="SELECT ExiStock,ExistenciaProd FROM cat_productos 
		LEFT JOIN cat_productos_stocks ON KEYProdStock=$productoId AND KEYAlmStock=$almacenId		
		WHERE IDProd=$productoId";
		$model=$this->getModelObject();
		$arrExistencia=$model->select($sqlExistencia);
		
		if ( empty($arrExistencia) ){
			
			return array(
				'success'=>false,
				'existenciaEnAlmacen'=> empty($arrExistencia[0]['ExiStock'])? 0 : $arrExistencia[0]['ExiStock'] ,
				'existenciaTotal'=> $arrExistencia[0]['ExistenciaProd']
			);
		}else{			
			return array(
				'success'=>($arrExistencia[0]['ExiStock'] < $cantidadSolicitada) ? false : true,
				'existenciaEnAlmacen'=> empty($arrExistencia[0]['ExiStock'])? 0 : $arrExistencia[0]['ExiStock'] ,
				'existenciaTotal'=> $arrExistencia[0]['ExistenciaProd']
			);			
		}
		return false;
	}
	
	private function getRemplazos($keyKit, $IdProd, $keyAlmacen ){
		/*		
		Primero obtengo los ids de los productos sustitutos, luego uso la funcion getProducto del modelo para obtener los datos tal y 
		como los necesita el grid.		
		*/
		$model=$this->getModelObject();
		$query="SELECT can_sus, KEY_Prod_sus,0 as Subtotal, existenciaProd existenciaTotal ,ExiStock existenciaEnAlmacen
		FROM cat_productos_kit_relaciones
		JOIN cat_productos_kits_sustitutos ON KEY_Kit_Pro_Sus=ID_Kit_rel 
		LEFT JOIN cat_productos ON IDProd=KEY_Prod_sus
		LEFT JOIN cat_productos_stocks ON KEYProdStock=KEY_Prod_sus AND KEYAlmStock=$keyAlmacen
		WHERE KEY_Kit_rel=$keyKit AND KEY_Prod_rel=$IdProd;";
		$arrData = $model->query( $query );
		/*   ------------------------------   */
		if ( empty($this->KEYSucOrdVen) ){
			$OrigenTaR="EMP";
			$KEYOrigenTaR=$this->KEYEmpOrdVen;						
		}else{
			$OrigenTaR="SUC";
			$KEYOrigenTaR=$this->KEYSucOrdVen;	
		}		
		$productos=array();
		foreach($arrData as $sustituto){
			$IDSustituto=$sustituto['KEY_Prod_sus'];
			$listProductos=$model->getProducto( $OrigenTaR, $KEYOrigenTaR, $IDSustituto, $this->TipDocOrdVen );	
			
			if ( !empty($listProductos) ){				
				$listProductos['Cantidad']=$sustituto["can_sus"];
				$listProductos['existenciaTotal']=$sustituto["existenciaTotal"];
				$listProductos['existenciaEnAlmacen']=is_numeric($sustituto["existenciaEnAlmacen"])? $sustituto["existenciaEnAlmacen"] : 0;
				$productos[]=$listProductos;				
			}			
		}
		return $productos;
	}
	
	private function cancelarFacturaInventario($maestro, $componentes){
		
		//======================================================================================================
		//Revisar si hay una orden de venta relacionada con esta factura
		$sql="SELECT IDOrdVen FROM
		orden_venta WHERE KEYFacOrdVen=".$maestro['IDFac'];		
		$model=$this->getModelObject();
		$arrOrdVen=$model->select($sql);
		if ( !empty($arrOrdVen) ){
			return true;	//TODO: notificar al usuario: Si la orden existe no registra movimientos (Porque la orden es quien genera los movimientos) 
		}
		
		// en el caso de los kits, es necesario extraer los componentes seleccionados
		$conceptos=array();
		foreach($componentes as $componente){
			if ($componente['TipoArt']=='K'){
				foreach($componente['componentes'] as $comKit){
					if ( isset($comKit['seleccionado']) && $comKit['seleccionado']=='1' ){
						unset( $comKit['sugerencias'] );
						$conceptos[]=$comKit;
					}else{
						foreach($comKit['sugerencias'] as $sugerencia){
							if ($sugerencia['seleccionado']=='1'){
								$conceptos[]=$sugerencia;
							}
						}
					}
				}
			}else{
				$conceptos[]=$componente;
			}
		}
		
		$detalleModel	=new InventarioDetalleModel();			
		$date			=DateTime::createFromFormat ('d/m/Y H:i:s' , $maestro['FechaCan']);
		$fecha			=$date->format('Y-m-d H:i:s');
		$generaRecosteo	=true;
		$log			=false;
		$recuperado		=false;
		$transaccionIniciada=true;
		$ser_fol=$maestro['SerFol'].'-'.$maestro['Folio'];
		foreach($conceptos as $concepto){
			if ($concepto['TipoArt'] == 'S'){
				//Solo los productos Y Kits son registrados				
				continue;
			}
			//---------------------------
			//Se realiza el mapeo de la orden de venta al detalle del movimiento	
			$PIvaDet	=0;
			$IvaDet		=0;
			$IEPSDet	=0;
			$PIEPSDet	=0;			
			//---Busca un movimiento relacionado a este detalle de factura.
			//Obtener el costo de salida de los componentes de esta factura
			
			$sql_KEYMovimiento="SELECT IDInventarioDet,KEYAlmacenDet, KEYProductoDet 
			FROM inventarios_movimientos_detalle WHERE KEYReferenciaDet=".$concepto['IDDetalle'].' AND KEYMovimientoDet=-1;';												
			$arrKeyMovimiento=$detalleModel->select($sql_KEYMovimiento);			
						
			$empresaId = $maestro['IDEmp'];
			if ( empty($arrKeyMovimiento)  ){
				if ( $this->inventariar($empresaId)==false ){
					return;
				}
				$IDInventarioDet=0;	
				throw new Exception("No se encontró el movimiento de la factura: ");
				$concepto['PrecioU']=$concepto['Importe'] / $concepto['Cantidad'];				
				if (empty($_SESSION['Auth']['Almacen']) ){
					throw new MyException("Seleccione al almacén que dará salida a los productos.",'Seleccione un almacén','WARNING');
				}
				$KEYAlmacen=$_SESSION['Auth']['Almacen']['IDAlmacen'];
				$KEYProductoDet = 0;
			}else{
				$IDInventarioDet	= $arrKeyMovimiento[0]['IDInventarioDet'];
				$KEYAlmacen			= $arrKeyMovimiento[0]['KEYAlmacenDet'];
				$KEYProductoDet		= $arrKeyMovimiento[0]['KEYProductoDet'];
			}
			
			//-----------------------------------------------------------------
			//	Agrego la fecha de cancelación al movimiento
			//-----------------------------------------------------------------
			$sql='UPDATE inventarios_movimientos_detalle SET FechaCancDet="'.$fecha.'" WHERE IDInventarioDet='.$IDInventarioDet;
			$detalleModel->update($sql);
			
			$importe	=$concepto['Importe'];
			$subtotal	=$concepto['Importe']-$concepto['DescuentoPesos'];	
						
			$detalleMovimiento=array(
				//'IDInventarioDet' 			=> $IDInventarioDet,
				'FechaMovDet'				=> $fecha,	
				'KEYReferenciaDet' 			=> $concepto['IDDetalle'],
				'KEYProductoDet' 			=> $concepto['KEYProdServ'],
				'TipoProductoDet'			=> $concepto['TipoArt'],
				'KEYUDMProductoDet' 		=> 1,//<----------------------------------
				'CantidadDet' 				=> $concepto['Cantidad'],
				'CostoUDet' 				=> $concepto['PrecioU'],
				'TImporteDet'				=> $importe,
				'TDescuentoDet' 			=> $concepto['DescuentoPesos'],
				'TDescPorDet' 				=> $concepto['DescuentoPorcentaje'],
				'TSubTotalDet'    			=> $subtotal,
				//'TImpuestosDet' 			=> $concepto['IvaPesos'],
				//'PImpuestosDet' 			=> $concepto['SumPorImp'],
				'TTotalDet' 				=> $concepto['Total'],
				'ReferenciaInventarioDet' 	=> 'CAN_FAC-'.$ser_fol,
				'KEYMovimientoDet' 			=> -1,	//
				'KEYTipoMovimientoDet'		=> 1,	//SALIDA
				'GenCosMovimientoDet'		=> 1,
				'KEYAlmacenDet' 			=> $KEYAlmacen, //	
				'esCancelacion_MovDet'      => 1,
				'KEYDetCancelado_MovDet'	=> $IDInventarioDet
				//'PIvaDet' 					=> $concepto['PIvaTras'],
				//'IvaDet' 					=> $concepto['IvaTras'],
				//'IEPSDet'					=> $concepto['PIepsTras'],
				//'PIEPSDet'					=> $concepto['IepsTras']
			);
			
			$componentes=array();
			
			if ($concepto['TipoArt']=='K'){				
				$componentes=$concepto['componentes'];				
				for($i=0; $i<sizeof($componentes); $i++){
					if ( !isset($componentes[$i]['Subtotal']) ){
						$componentes[$i]['Subtotal'] = $componentes[$i]['Importe'];
					}
				}
			}	
			$arr=$detalleModel->save($detalleMovimiento, $recuperado, 0, true, $componentes);
			$this->regresaSku($KEYAlmacen, $KEYProductoDet, $concepto['SKUs']);	
		}
	}
	
	private function regresaSku($idAlmacen, $idProducto, $Sku){
		if($idProducto == 0) return;
		
		$skus = json_decode($Sku);
		
		$stockModel = new Stock();
		
		foreach($skus as $item){
			$query = "INSERT INTO cat_productos_sku (IDSKU, KEYProSKU, SKU, IDAlmacen, Status)
					VALUES(".$item->id.", $idProducto, '".$item->sku."', $idAlmacen, 'A');";
			
			$stockModel->insert($query);
		}
	}
	
	function getgestionfolios(){
		$model=$this->getModelObject();
		// $hostTmp = 'localhost';//'mifactura.upctechnologies.com';
		// $userTmp = 'mifactura';
		// $passTmp = 'fac9845o';
		$search = isset($_POST['query']) ? strtoupper($_POST['query']) : '';
		$dbnameTmp = 'db_mifactura';
		$sql = "CALL spConsultarGestionFolios('".$search."')";
		// echo $sql;
		// exit;
		$arrFolios=$model->select($sql,$dbnameTmp);
		
		$response=array();
		$response['success'] =true;
		// $response['totalRows'] = $totalRows;
		//$response['totalRows'] = 5000;
        $response['data']=$arrFolios;
		
        return $response;
	}
	
	function getgestionfoliosreport(){
		$model=$this->getModelObject();
		// $hostTmp = 'localhost';//'mifactura.upctechnologies.com';
		// $userTmp = 'mifactura';
		// $passTmp = 'fac9845o';
		$search = isset($_POST['query']) ? strtoupper($_POST['query']) : '';
		$EsFechaCalculo = isset($_POST['EsFechaCalculo']) ? strtoupper($_POST['EsFechaCalculo']) : 0;
		$dbnameTmp = 'db_mifactura';
		$sql = "CALL spConsultarSaldosFolios('".$search."', ".$EsFechaCalculo.")";
		// echo $sql;
		// exit;
		$arrFolios=$model->select($sql,$dbnameTmp);
		
		$response=array();
		$response['success'] =true;
		// $response['totalRows'] = $totalRows;
		//$response['totalRows'] = 5000;
        $response['data']=$arrFolios;
		
        return $response;
	}
	
	function getgestionfoliosusuarios(){
		$search = isset($_POST['query']) ? strtoupper($_POST['query']) : '';
		$model=$this->getModelObject();
		$dbnameTmp = 'db_mifactura';
		$sql = "CALL spConsultarGestionFoliosUsuarios('".$search."')";
		// echo $sql;
		// exit;
		$arr=$model->select($sql,$dbnameTmp);
		
		$response=array();
		$response['success'] =true;
        $response['data']=$arr;
		
        return $response;
	}
	
	function getgestionfoliosusuariosbyupdate(){
		$search = isset($_POST['query']) ? strtoupper($_POST['query']) : '';
		$model=$this->getModelObject();
		$dbnameTmp = 'db_mifactura';
		$sql = "CALL spConsultarGestionFoliosUsuarios('".$search."')";
		// echo $sql;
		// exit;
		$arr=$model->select($sql,$dbnameTmp);
		
		$numResults = count($arr);
		if($numResults > 0){
			$arr[$numResults -1]['lastRow'] = 1;
		}

		$response=array();
		$response['success'] =true;
        $response['data']=$arr;
		
        return $response;
	}
	
	function getInfoFoliosByDB($rfc, $dbName){
		$model=$this->getModelObject();
		$cad = "SELECT Anio, Mes, Saldo, Fecha FROM facturacion_edocta WHERE RFC = '$rfc' ORDER BY Folio DESC LIMIT 1;";
		$arr = $model->select($cad, 'db_mifactura');
		$saldo = 0;
		$consumidosAlaFecha = 0;
		$oldMes = 0;
		$oldAnio = 0;
		$oldFecha = '';
		$nuevoMes = 0;
		
		if (count($arr) > 0)
		{	
			$nuevoAnio = $arr[0]['Anio'];
			$nuevoMes = $arr[0]['Mes'];
			$oldFecha = $arr[0]['Fecha'];
			$oldAnio = $nuevoAnio;
			$oldMes = $nuevoMes;
			$saldo = $arr[0]['Saldo'];
			if($nuevoMes == 12){
				$nuevoAnio = $nuevoAnio +1;
				$nuevoMes = 01;
			}else{
				$nuevoMes = $nuevoMes +1;
			}
			
			$fecha = $nuevoAnio.'-'.str_pad($nuevoMes, 2, 0, STR_PAD_LEFT).'-01 00:00:00';
			
			$cad = "SELECT COUNT(IDFac) AS Total 
			FROM facturacion 
			WHERE (rfcEmisor  = '$rfc' AND FechaTimbrado > '$fecha' AND NOT ISNULL(FechaTimbrado)) AND (ISNULL(FecCan) AND STATUS <> 'C')";
			
			// $cad = "SELECT SUM(total) as Total FROM (
			// SELECT COUNT(IDFac) AS total FROM facturacion WHERE FechaTimbrado > '$fecha' AND NOT ISNULL(FechaTimbrado) and rfcEmisor  = '$rfc'
			// UNION
			// SELECT COUNT(IDFac) AS total FROM facturacion WHERE (FechaTimbrado > '$fecha' AND NOT ISNULL(FechaTimbrado)) AND (Status = 'C' AND NOT ISNULL(FecCan)) and rfcEmisor  = '$rfc'
			// ) dte;";
			// echo $cad;
			// exit;
			$arr2 = $model->select($cad, $dbName);
			if(count($arr2) > 0){
				$consumidosAlaFecha = $arr2[0]['Total'];
			}
		}
		
		$anioActual = date("Y");
		$mesActual = date("m");
		$diaActual = date("d");
		
		$fechaActualOld= $anioActual.'-'.$mesActual.'-01 00:00:00';
		if($mesActual == 1 || str_pad($mesActual, 2, 0, STR_PAD_LEFT) == 01){
			$anioActual = $anioActual - 1;
			$mesActual = 12;
		}else{
			$mesActual = $mesActual = $mesActual -1;
		}
		$fechaActualNew = $anioActual.'-'.$mesActual.'-01 00:00:00';
		
		$response=array();			
		$response['Anio'] = $oldAnio;
		$response['Mes'] = $oldMes;
		$response['Fecha'] = $oldFecha;
		$response['NuevoMes'] = $nuevoMes;
		$response['ConsumidosAlaFecha'] = $consumidosAlaFecha;
		$response['Saldo'] = $saldo;
		$response['SaldoCalculado'] = $saldo - $consumidosAlaFecha;
		// $response['SumaTotalVR'] = $sumaTotalVR;
		return $response;
	}
	
	function actualizarsaldosfolios(){
		$RFC = $_POST['RFC'];
		$position = $_POST['position'];
		$positionCor = $_POST['positionCor'];
		$Nombre = $_POST['Nombre'];
		$dbnameTmp = $_POST['DBCor'];
		// $corporativos = json_decode($_POST['Corp'], true);
		$model=$this->getModelObject();
		
		if($position == 0 && $positionCor == 0){
			$sql = 'TRUNCATE TABLE saldos_folios';
			$resp = $model->select($sql,'db_mifactura');
		}
		//Se comprueba si la BD existe, ya no se utiliza
		/*$countResult = 0;
		$dbComp = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "'.$dbnameTmp.'"';
		$arrDB=$model->select($dbComp,'cfd_master'); 
		$countResult = count($arrDB);
		if($countResult > 0){*/
		try{
			$countResult = 0;
			//$sql = 'SELECT 1 FROM Facturacion where rfcEmisor = "'.$RFC.'" LIMIT 1';
			$sql = 'SELECT 1 FROM cat_empresas WHERE RFCEmp = "'.$RFC.'" AND StatusEmp = "A"';
			$arr=$model->select($sql,$dbnameTmp);
			$countResult = count($arr);
			// if($countResult != 1 && $dbnameTmp !='cfdi_loga770918BG9' && $dbnameTmp != 'cfdi_upccorporate')
				// echo $countResult.$dbnameTmp;
			
			if($countResult > 0){
				$resultToInsert = $this->getInfoFoliosByDB($RFC, $dbnameTmp);
				$UserAdd = $_SESSION['Auth']['User']['emaUsu'];
				// print_r($resultToInsert);
				$Anio = $resultToInsert['Anio'] > 0 ? $resultToInsert['Anio'] : 'NULL';
				$Mes = $resultToInsert['Mes'] > 0 ? $resultToInsert['Mes'] : 'NULL';
				$Saldo = $resultToInsert['Saldo'];
				$Fecha = $resultToInsert['Fecha'] != '' ? '"'.$resultToInsert['Fecha'].'"' : 'NULL';
				$consumoCalculado = $resultToInsert['ConsumidosAlaFecha'];
				$saldoCalculado = $resultToInsert['SaldoCalculado'];
				
				$sql = 'INSERT INTO saldos_folios(Nombre, RFC, FechaLastRegistro, Mes, Anio, Saldo, ConsumoCalculado, SaldoCalculado, FechaCalculo, UsuarioCalculo) 
				SELECT "'.$Nombre.'", "'.$RFC.'",  '.$Fecha.',  '.$Mes.', '.$Anio.',  '.$Saldo.', '.$consumoCalculado.', '.$saldoCalculado.', NOW(), "'.$UserAdd.'" ';
				
				$arrResult=$model->select($sql,'db_mifactura');
			}
		}catch(Exception $e){
			//No hago nada si hay error, es porque no se pudo conectar a la BD
		}
		
		$response=array();
		$response['countResult'] = $countResult;
		$response['success'] =true;
        
		
        return $response;
	}
	
	function actualizarsaldosfolioslimit(){
		$RFC = $_POST['RFC'];
		$position = $_POST['position'];
		$positionCor = $_POST['positionCor'];
		$Nombre = $_POST['Nombre'];
		$EsInicio = $_POST['EsInicio'];
		$CorpString = $_POST['Corporativos'];
		$CorpString = str_replace("\\", '', $CorpString);
		$corporativos = json_decode($CorpString, true);
			
		$model=$this->getModelObject();
		
		if($EsInicio == 1){
			$sql = 'TRUNCATE TABLE saldos_folios';
			$resp = $model->select($sql,'db_mifactura');
		}
		foreach($corporativos as $corp){
			$dbnameTmp = $corp['DBCor'];
			$countResult = 0;
			try{
				$sql = 'SELECT 1 FROM cat_empresas WHERE RFCEmp = "'.$RFC.'" AND StatusEmp = "A"';
				$arr=$model->select($sql,$dbnameTmp);
				$countResult = count($arr);
				if($countResult > 0){
					$resultToInsert = $this->getInfoFoliosByDB($RFC, $dbnameTmp);
					$UserAdd = $_SESSION['Auth']['User']['emaUsu'];
					$Anio = $resultToInsert['Anio'] > 0 ? $resultToInsert['Anio'] : 'NULL';
					$Mes = $resultToInsert['Mes'] > 0 ? $resultToInsert['Mes'] : 'NULL';
					$Saldo = $resultToInsert['Saldo'];
					$Fecha = $resultToInsert['Fecha'] != '' ? '"'.$resultToInsert['Fecha'].'"' : 'NULL';
					$consumoCalculado = $resultToInsert['ConsumidosAlaFecha'];
					$saldoCalculado = $resultToInsert['SaldoCalculado'];
					$sql = 'INSERT INTO saldos_folios(Nombre, RFC, FechaLastRegistro, Mes, Anio, Saldo, ConsumoCalculado, SaldoCalculado, FechaCalculo, UsuarioCalculo) 
					SELECT "'.$Nombre.'", "'.$RFC.'",  '.$Fecha.',  '.$Mes.', '.$Anio.',  '.$Saldo.', '.$consumoCalculado.', '.$saldoCalculado.', NOW(), "'.$UserAdd.'" ';
					$arrResult=$model->select($sql,'db_mifactura');
				}
			}catch(Exception $e){
				//No hago nada si hay error, es porque no se pudo conectar a la BD en caso de que no exista
			}
			
			if($countResult > 0)
				break;
		}
		$response=array();
		$response['countResult'] = $countResult;
		$response['success'] =true;
        
		
        return $response;
	}
	
	function consultargestionfolioscorp(){
		$search = isset($_POST['query']) ? strtoupper($_POST['query']) : '';
		$model=$this->getModelObject();
		$dbnameTmp = 'cfd_master';
		$sql = "CALL spConsultarGestionFoliosCorp()";
		$arr=$model->select($sql,$dbnameTmp);
		
		$numResults = count($arr);
		if($numResults > 0){
			$arr[$numResults -1]['lastRow'] = 1;
		}
		
		$response=array();
		$response['success'] =true;
        $response['data']=$arr;
		
        return $response;
	}
	
	function insertargestionfolios(){
		// $id_usu  = $_SESSION['Auth']['User']['IDUsu'];
		// $sql = "SELECT NomUsu FROM cat_usuarios WHERE IDUsu=".$id_usu;
		// $arrDatos=$this->modelObject->select($sql);
		$model=$this->getModelObject();
		$dbnameTmp = 'db_mifactura';
		$ID = isset($_POST['ID']) ? $_POST['ID'] : 0;	
		$RFC = $_POST['RFC'];	
		$Compras = $_POST['Folios'];	
		$Observacion = $_POST['Observacion'];	
		$UserAdd = $_SESSION['Auth']['User']['emaUsu'];//$arrDatos[0]['NomUsu'];	
		$sql = "CALL spInsertarGestionFolios(".$ID.", '".$RFC."', ".$Compras.", '".$Observacion."', '".$UserAdd."')";
		// echo $sql;
		// exit;
		$respGuardado=$model->select($sql,$dbnameTmp);
		if($respGuardado[0]['Error'] == 0){
			$dbnameTmp = $_SESSION['dbcorp'];
			$sql = 'SELECT 1'; 
			$respchangebd=$model->select($sql,$dbnameTmp);
			
			$json= file_get_contents("./stJson/stGeneral.json");
			$data=json_decode($json);
			$correosGestion = $data->CorreosGestionFolios;
			$subject = '';
			if($ID == 0){
				$subject = 'Se agregaron '.$Compras.' timbres a '.$RFC;
				$body = 'Hola, se informa que el usuario '.$UserAdd.' ha registrado '.$Compras.' timbres al cliente '.$RFC.'<br><br>Muchas gracias';
			}else{
				if($respGuardado[0]['ComprasBefore'] != $Compras){
					$subject = 'Se editó un registro de compra de timbres de '.$RFC;
					$body = 'Hola, se informa que el usuario '.$UserAdd.' ha modificado el registro '.$ID.' de '.$respGuardado[0]['ComprasBefore'].' timbres a '.$Compras.' del cliente '.$RFC.'<br><br>Muchas gracias';
				}
			}
			if(!empty($subject)){
				foreach($correosGestion as $correo){
					$dest = array($correo->correo);
					$enviado = enviarCorreo($subject, $body, $dest, null, null, true, null, '', 'Modificación de timbres de cliente', false);
				}
			}
			
		}
		// $rfc = $_SESSION['Auth']['User']['RFCEmp'];
		// echo $rfc;
		// exit;
		return $respGuardado[0];
	}
	
	function eliminargestionfolios(){
		$model=$this->getModelObject();
		$dbnameTmp = 'db_mifactura';
		$ID = isset($_POST['ID']) ? $_POST['ID'] : 0;	
		$UserAdd = $_SESSION['Auth']['User']['emaUsu'];
		$sql = "CALL spEliminarGestionFolios(".$ID.", '".$UserAdd."')";
		// echo $sql;
		// exit;
		$respGuardado=$model->select($sql,$dbnameTmp);
		if($respGuardado[0]['Error'] == 0){
			$dbnameTmp = $_SESSION['dbcorp'];
			$sql = 'SELECT 1'; 
			$respchangebd=$model->select($sql,$dbnameTmp);
			
			$json= file_get_contents("./stJson/stGeneral.json");
			$data=json_decode($json);
			$correosGestion = $data->CorreosGestionFolios;
			
			$subject = 'Se eliminó una compra de timbres de '.$respGuardado[0]['RFC'];
			$body = 'Hola, se informa que el usuario '.$UserAdd.' ha eliminado el registro '.$respGuardado[0]['ID'].' de '.$respGuardado[0]['Compras'].' timbres del cliente '.$respGuardado[0]['RFC'].'<br><br>Muchas gracias';
			foreach($correosGestion as $correo){
				$dest = array($correo->correo);
				$enviado = enviarCorreo($subject, $body, $dest, null, null, true, null, '', 'Modificación de timbres de cliente', false);
			}
			
		}
		return $respGuardado[0];
	}
}


