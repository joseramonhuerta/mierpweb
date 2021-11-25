<?php
require ('eko_framework/app/models/folio.php');       //MODELO
class Folios extends ApplicationController {
    
    function find(){  //<----------------PARA EL GRID        
        try {            
             $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro'];

             if ($_POST['filtrarActivos'] == 'true') {
                $filtrarActivos = true;
            } else {
                $filtrarActivos = false;
            }


           $empresaId=$_POST['IDEmpresa'];
		   if ($_POST['IDEmpresa']=='' || !isset($_POST['IDEmpresa'])){
				throw new Exception("Es necesario logearse en una empresa para ver sus Folios correspondientes");
			}
           $sucursalId=(empty($_POST['IDSucursal'])) ?  0 : $_POST['IDSucursal'];
                
            $modelObject=new FolioModel();
            $response = $modelObject->find($start,$limit,$filtro,$empresaId,$sucursalId,$filtrarActivos);
        } catch (Exception $e) {
            $response['succes'] = false;
            $response['msg'] = $e->getMessage();
        }
        return $response;        
    }

   
    function read(){
        $modelObject = new folioModel();
        if (isset($_POST[$modelObject->primaryKey])) {

            $id = $_POST[$modelObject->primaryKey];
            $datos = $modelObject->getById($id);
              
            $response['success'] = true;
            $response['data'] = $datos[$modelObject->name];
        } else {
            $response['success'] = false;
            $response['msg'] = "El servicio está indisponible";
            
        }
        return $response;
    }
	
    /*Actualiza el campo predet*/
    function updateFieldPredet(){
    	if (empty($_POST['IDFol'])){
    		return array(
    			'success'=>false,
    			'msg'=>array(
    				'titulo'=>'Error al actualizar el status del folio',
    				'mensaje'=>'El identificador recibido es inválido <br/> Consulte con el administrador del sistema'
    			)    		
    		);
    	}
    	
    	if ( !isset($_POST['predet']) || !is_numeric($_POST['predet']) ){
    		return array(
    			'success'=>false,
    			'msg'=>array(
    				'titulo'=>'Error al actualizar el status del folio',
    				'mensaje'=>'El estado solicitado es inválido<br/> Consulte con el administrador del sistema'
    			)    		
    		);
    		
    	}
    	$PredetFol=	$_POST['predet'];
    	$IDFol=$_POST['IDFol'];
    	$model=new Model();
		
		/*=======================================================================
		Revisar que si esta serie tiene mas folios, los folios ya se hayan terminado para poder predeterminar este		
		=======================================================================*/
		$sql="SELECT * FROM cat_folios WHERE IDFol=$IDFol";
		$arrFolio	=$model->select($sql);
		$serie		=$arrFolio[0]['SerieFol'];
		$empresa	=$arrFolio[0]['KEYEmpFol'];
		$sucursal	=$arrFolio[0]['KEYSucFol'];
		$folioInicial=$arrFolio[0]['InicialFol'];
		$sql="SELECT * FROM cat_folios WHERE IDFol!=$IDFol AND KEYEmpFol=$empresa AND KEYSucFol=$sucursal 
		AND SerieFol='$serie' AND SigFol<=FinalFol AND InicialFol<$folioInicial;";
		
		$arrFolios=$model->select($sql);
		if ( !empty($arrFolios) ){
			throw new Exception("Todavia hay folios disponibles para la serie <label style='font-weight:bold;'>$serie</label>");
		}
		
    	$sql="UPDATE cat_folios set PredetFol='$PredetFol' WHERE IDFol=$IDFol";    	
        $model->update($sql);        
        
        
        if($PredetFol=='1'){	//SI ESTE NODO FUE ESTABLECIDO COMO PREDETERMINADO
        	//LAS DEMAS SERIES SON ESTABLECIDAS COMO NO PREDETERMINADOS
        	$sql="SELECT KEYEmpFol, KEYSucFol FROM cat_folios WHERE IDFol=$IDFol";
        	$arrDatos=$model->select($sql);
	        if ( empty($arrDatos) ){
	        	throw new Exception("No se encontró el folio editado");
	        }
	        $empresa=$arrDatos[0]['KEYEmpFol'];
	        $sucursal=$arrDatos[0]['KEYSucFol'];

	        $sql="UPDATE cat_folios SET PredetFol=0 WHERE KEYEmpFol=$empresa AND KEYSucFol=$sucursal AND IDFol!=$IDFol";
	        $model->update($sql);
        }
        
        return array(
        	'success'=>true,
        	'msg'=>array(
        		'titulo'=>'Folios',
        		'mensaje'=>'Folio actualizado',
        		'data'=>array(
        			'IDFol'=>$IDFol,
        			'PredetFol'=>$PredetFol
        		)
        	)
        );
        
        
    	
    }
	
    private function leerArchivo (){
        
          if (!empty($_FILES['Folio']['name']['txt'])) {
              $content=file_get_contents($_FILES['Folio']['tmp_name']['txt']);
              $arrContent=split('\|',$content);
              /*
              */

               $data=array();
              
              $data['RFCFol']=$arrContent[1];
              $data['SerieCertFol']=$arrContent[2];
              /*    DE LA FECHA SOLO NECESITO EL AÑO    */
               $jsDate=$arrContent[3];
              list($dia, $mes, $año) = split('[/]', $jsDate);
              list($año, $time) = split('[ ]', $año);
              $data['AnoAprobFol']=$año;                             
              $numeroDeFolios=$arrContent[4];
              $tamaño=6+($numeroDeFolios*3);
          	  if(sizeof($arrContent)!=$tamaño){
                  throw new Exception("No se reconoce el formato del archivo.");
              }
              $data['folios']=array();
              $index=6;
              for($i=0;$i<$numeroDeFolios;$i++){
              	
              	$data['folios'][]=array(
              		'serie'=>$arrContent[$index],
              		'numFolios'=>$arrContent[$index+1]
              	);
              	$index+=3;
              }               
               /*
              $data['InicialFol']
              $data['SerieFol']=$arrContent[6];
              $data['FinalFol']=$arrContent[7];
              */
              return $data;
              
          }else if (!empty($_FILES['Folio']['name']['xml'])) {
              $content=file_get_contents($_FILES['Folio']['tmp_name']['xml']);
               throw new Exception("Lectura del xml todavia no implementada");
          }
          return false;
        
    }
   
    function validaciones(){
    	//Revisar que solo una serie sea predeterminada.
    	//Revisar que la serie predeterminada todavia tengan folios disponibles. 
    }
    
    function guardar(){
        $this->modelObject = new FolioModel();
        $modelObject=$this->modelObject; 

        $soloLeer=$_POST['soloLeer'];
        if ($soloLeer=='true'){ //SI ME MANDARON EL ARCHIVO        	     
            //LEER EL CONTENIDO DEL ARCHIVO XML O TXT Y DEVOLVER SU CONTENIDO            
            $datos=$this->leerArchivo();
            //------------------------------------------------------------------------------
            $params=array();
            $params['NumAprobFol']= $_POST['NumAprobFol'];
	        $params['AnoAprobFol']= $_POST['AnoAprobFol'];
	        $params['folios']= json_decode(stripslashes($_POST['folios']),true); 
	        $params['IDFol']= $_POST['IDFol'];
	        $params['KEYEmpFol']= $_POST['KEYEmpFol'];
	        $params['KEYSucFol']= $_POST['KEYSucFol'];
	        $params['Status']= $_POST['Status'];	
            $merged=array_merge($params,$datos);
            //------------------------------------------------------------------------------
            $datos=$modelObject->calcularFolios($merged);
            //------------------------------------------------------------------------------
            $response['success'] = true;
            $response['data'] = $datos;           
        }else{//GUARDAR CONTENIDO
            $CFDI=	(isset($_POST['folios']))?	false	:	true;
            if ($CFDI){
            	$params= $_POST['Folio'];
            	
            	$modelObject->ultimaSerie		=	$params['SerieFol'];
            	$params['PredetFol']=( empty($params['PredetFol']) )? '0' : '1';
    			$modelObject->predetEncontrado	=	( $params['PredetFol']=='1' ) ? true : false;
            	
    			$valido=$modelObject->validar($params);           
            	$registroAsArray = $modelObject->guardarCFDI($params);
            	$response['msg'] = array('titulo'=>'Folios','mensaje'=>'Folio Guardado Satisfactoriamente');
            	$response['data'] = $registroAsArray['Folio'];
            	$response['success'] = true;
            }else{
            	 /*CUANDO ES FACTURACION CFD, EL CLIENTE ENVIA UN ARREGLO CON LOS FOLIOS*/
		        $response= $this->guardarCFD();
            }
        }         
         return $response;
       
    }
    
	private function guardarCFD(){
		$modelObject=$this->modelObject;
		
		$params['KEYSucFol']= $_POST['KEYSucFol'];
		       
        $IDS=array();
        $foliosGuardados=array();
        $folios= json_decode(stripslashes($_POST['folios']),true); 
         
        $numFolios=sizeof($folios);
        $errores=false;
        $aciertos=0;
        //----------------------------------------------------------------------
        //	Guarda los folios uno por uno, si existe un error almacena cada error
        //----------------------------------------------------------------------
        $modelObject->ultimaSerie		=	$folios[0]['SerieFol'];
        
        if (empty($folios[0]['PredetFol'])){
        	$folios[0]['PredetFol']='0';
        }
        
    	$modelObject->predetEncontrado	=	( $folios[0]['PredetFol']=='1' ) ? true : false;
    			
        for($i=0;$i<$numFolios;$i++){
        	$folio=array(
        		'NumAprobFol'=> $_POST['NumAprobFol'],
        		'SerieCertFol'=> $_POST['SerieCertFol'],	        		
        		'AnoAprobFol'=>$_POST['AnoAprobFol'],
        		'KEYEmpFol'=>$_POST['KEYEmpFol'],		        			
        		'KEYSucFol'=>$_POST['KEYSucFol']		        				        		
        	);
        	
        	$folio=array_merge($folio,$folios[$i]);
        	try{
        		unset($folio['error']);
        		
        		$valido=$modelObject->validar($folio);           
            	$registroAsArray = $modelObject->guardarCFDI($folio);
            	$foliosGuardados[$i]=$registroAsArray['Folio'];
            	$aciertos++;	
        	}catch(Exception $e){
        		//$errores=true;
        		$foliosGuardados[$i]=$folio;
        		$foliosGuardados[$i]['error']=$e->getMessage();
        	}
        }
		$numFoliosGuardados=$i;
        
        
        $msg=($aciertos!=1)? $aciertos.' folios guardados':' 1 folio guardado';	
        

        $response['data']=array(
        		'NumAprobFol'=> $_POST['NumAprobFol'],
        		'SerieCertFol'=> $_POST['SerieCertFol'],	        		
        		'AnoAprobFol'=>$_POST['AnoAprobFol'],
        		'KEYEmpFol'=>$_POST['KEYEmpFol'],		        			
        		'KEYSucFol'=>$_POST['KEYSucFol']		        				        		
        );
        
   
        //-----------------------------------------------------------------
        //ELIMINAR FOLIOS
        //-----------------------------------------------------------------
        $eliminar	  = json_decode(stripslashes($_POST['eliminados']),true);
        $numEliminar=sizeof($eliminar);
        
        $numEliminados=0;
        $errorEliminados=0;
        for($i=0;$i<$numEliminar;$i++){        	
        	try{
        		
        		$folio=array(
        			'NumAprobFol'=> $_POST['NumAprobFol'],
        			'SerieCertFol'=> $_POST['SerieCertFol'],	        		
        			'AnoAprobFol'=>$_POST['AnoAprobFol'],
        			'KEYEmpFol'=>$_POST['KEYEmpFol'],		        			
        			'KEYSucFol'=>$_POST['KEYSucFol']		        				        		
        		);
        	
        		$folio=array_merge($folio,$eliminar[$i]);
        		
        		$IDFol=$folio['IDFol'];
        		$valido=$modelObject->validar($folio);      
        		$modelObject->delete($IDFol);	
        		$numEliminados++;
        	}catch(Exception $e){
        		$errorEliminados++;
        		//CUANDO NO SE PUEDE ELIMINAR EL FOLIO SE ENVIA DE VUELTA PARA QUE SEA MARCADO CON ERROR
        		$foliosGuardados[$numFoliosGuardados]=$eliminar[$i];
        		/*$serie=$folio['SerieFol'];
        		$inicial=$folio['InicialFol'];
        		$final=$folio['FinalFol'];*/
        		//$errMsg=" serie:<span style='font-weight:bold;'>$serie</span>, NumInicial:<span style='font-weight:bold;'>$inicial</span>, numFinal:<span style='font-weight:bold;'>$final</span>";
        		$foliosGuardados[$numFoliosGuardados]['error']=$e->getMessage(); 
        		$numFoliosGuardados++;//       		
        	}        	
        }
        if ($numEliminados || $errorEliminados){
        	$msg.=" ". $numEliminados . " Eliminados";
        }
        
        $response['msg']= array(
        	'titulo'=>'Folios',
        	'mensaje'=>$msg
        ); 
        $response['success']=true;
        $response['data']['folios']=$foliosGuardados;
        $response['data']['guardados']=true;
        return $response;
	}
    function eliminar(){
        $modelObject = new FolioModel();
        $idValue = $_POST[$modelObject->primaryKey];
        $response = array();
        try {
            $idBorrado = $modelObject->delete($idValue);
            $response['success'] = true;
            $response['msg'] = array('titulo'=>'Folios','mensaje'=>'Folio Eliminado Satisfactoriamente');
            $response['data'] = array($modelObject->primaryKey => $idBorrado);
        } catch (Exception $e) {
            $response['success'] = false;
            $response['msg'] = 'no se eliminó el folio con Id=' . $idValue . $e->getMessage();
        }
        return $response;
    }

  
}
?>
