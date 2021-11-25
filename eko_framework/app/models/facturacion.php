<?php
require ('eko_framework/app/models/parametros.php');
class FacturacionModel extends Model{
    
    var $useTable = 'tmp_facturacion';
	var $detalleTable="tmp_facturacion_detalle";
    var $name='Factura';
    var $primaryKey = 'IDFac';
    var $specific = true;
       
	var $conceptos;
	var $eliminados;
  
  	var $camposAfiltrar = array('NomCliente','RFCCliente','Folio','SerFol');
	

    function find($params){    //<-------------Busqueda paginada con filtros
        //------------------------------------------------------------------
		//		. . . Se va a armar una cadena con el filtro WHERE . . . 
		//------------------------------------------------------------------	
         $limit = (empty($params['limit'])) ? 20 : $params['limit'];
         $start = (empty($params['start'])) ?  0 : $params['start'];
         $filtro = (empty($params['filtro'])) ?  '': $params['filtro'];
        
        $filtrarActivos = ( isset($params['filtrarActivos']) ) ? $params['filtrarActivos'] : 'false';
        $filtroSql = $this->filtroToSQL( $filtro );     				
     	//----------------------------------------------------- 
     	//		FILTRAR POR EL NEGOCIO QUE EMITIÓ LA FACTURA
     	//-----------------------------------------------------
        $empresaId = $params['IDEmp'];
        if (strlen($filtroSql) > 0) {
            $filtroSql.=" AND IDEmp = $empresaId ";
        } else {
           $filtroSql = "WHERE IDEmp =$empresaId ";
        }

		//	Rango de fechas que pretenden filtrar
		//-------------------- 			
		$fInicial=$this->jsDateToMysql($params['fInicial']);
		$fFinal=$this->jsDateToMysql($params['fFinal']);
		$fFinal.=" 23:59:59";				
    	//-----------------------------------
		

		$sucursalId = $params['IDSuc'];
		$filtroSql.=" AND IDSuc=$sucursalId ";        
				//Mostrar Canceladas ----------------------------------------------
			$muestraCanceladas = '';
			if($params['canceladas'] == 'false'){
				// throw new Exception('Soy cancelada fuego');
				$muestraCanceladas = "AND (IF(FecCan IS NOT NULL,IF(FecCan NOT BETWEEN '$fInicial' AND '$fFinal' ,1,0),1))";
				
		   }
		   // throw new Exception($params['canceladas']);
		   // else if($){
			
		   // }
			$filtroSql.= $muestraCanceladas;
		//Mostrar Canceladas ----------------------------------------------
        //-------------------- 
		//	Filtra por status en el periodo seleccionado
		//-----------------------------------		
		$pagadas=$params['pagadas'];
		$canceladas=$params['canceladas'];
		$realizadas=$params['realizadas'];
   		 //$filtroEstado='';
		$filtroRangoFechas='';
        if ($pagadas==='true'){
           // $filtroEstado.=",'P'";     
            $filtroRangoFechas.=    "OR (FecPago BETWEEN '$fInicial' AND '$fFinal' )";   
        }
       
    	if ($canceladas==='true'){    		    		    		    
    		$filtroRangoFechas.=    "OR (FecCan BETWEEN '$fInicial' AND '$fFinal' )";    		             
        }
        
    	if ($realizadas==='true'){	//    		  
    		/*$filtroRangoFechas.="OR ((FechaFac>'$fFinal' OR FecPago IS NULL OR FecPago ='0000-00-00 00:00:00') AND 
    		(FecCan>'$fFinal'  OR FecCan IS NULL OR FecCan ='0000-00-00 00:00:00') AND
    		(FechaFac <'$fFinal')
    		)"; */
    		$filtroRangoFechas.=    "OR (FechaFac BETWEEN '$fInicial' AND '$fFinal' )";  
    		//throw new Exception("aki mero");                                    
        }
        
		if (strlen($filtroRangoFechas)>0){
			$filtroRangoFechas=substr($filtroRangoFechas,2);	
			$filtroSql.=" AND ($filtroRangoFechas)";
		}
		

        //----------------------------------------------------------
        //
        //--------------
        if (isset($params['cliente']) && !empty($params['cliente'])){
	        $arrClienteYrazSoc=explode ("-",$params['cliente']);
	        
	        if ($arrClienteYrazSoc[1]=='0'){
		        if (is_numeric($arrClienteYrazSoc[1])){
		        	$filtroSql.=" AND IDCli=$arrClienteYrazSoc[0]";
		        }		
	        }else{
	        	if (is_numeric($arrClienteYrazSoc[0])){
		        	$filtroSql.=" AND IDRazSoc=$arrClienteYrazSoc[1]";
		        }
	        }	
        }
                
        /*-------------------GENERICO: TOTAL SIN PAGINAR-------------------------------------*/
        $query = "select count($this->primaryKey) as totalrows  FROM facturacion f $filtroSql;";        
        $resArr = $this->query($query);        
        $totalRows = $resArr[0]['totalrows'];
        //Ahora para conocer el estatus de la factura en el perido se debe realizar algunas condiciones
        //	Si la factura tiene fecha de cancelacion en el periodo la factura está cancelada 
        // si la fecha de cancelacion es despues del periodo la factura o no existe la fecha de cancelacion, el estatus depende de:
        //si la fecha de pago esta dentro del rango, la factura está pagada, si la fecha de pago es mayor al periodo o no existe,+
       	//entonces la factura esta con saldo
        
        /*------Selecciona los registros que nos interesan----------------------------*/
        $query = "SELECT TipoFactura,concat(SerFol,Folio) as serfolOrden,TipComp, TipDoc,IDFac,DATE_FORMAT(FecCan,'%d/%m/%Y') as FecCan,DATE_FORMAT(FechaVen,'%d/%m/%Y') as FechaVen,IDEmp,IDSuc,SerFol,IDSerFol,Folio,Subtotal,Descuento,Total,IDCli,IDRazSoc,RFCCliente,RazSoc,NomCliente,CalleCliente,
				IF(DATE_FORMAT(FecPago,'%d/%m/%Y %H:%i:%S')='00/00/0000 00:00:00','',DATE_FORMAT(FecPago,'%d/%m/%Y')) as FecPago,
				if (FecCan BETWEEN '$fInicial' AND '$fFinal','C',
					IF (FecPago BETWEEN '$fInicial' AND '$fFinal','P','S')
				) as StatusFac,UUIDFac as folioFiscal, DATE_FORMAT(FechaFac,'%d/%m/%Y') FechaFac,FechaFac as FechaFacOrden
				FROM facturacion f                
        $filtroSql
		ORDER BY FechaFacOrden DESC,SerFol ASC, Folio DESC
        limit $start,$limit;";
		// throw new Exception($query);
        
       /*------------------------------------------------------*/
	
        $resArr = $this->query($query);
        $response=array();
		$response['success'] =true;
		$response['totalRows'] = $totalRows;
		//$response['totalRows'] = 5000;
        $response['data']=$resArr;
        //$response['ffin']=$fFinal;

        return $response;
        /*----------------------------------------------------------------*/
    }

    function getCliente($IDCliDet){
        $query="SELECT if(EmaCliDet='',EmaConCorCli,EmaCliDet)  as EmaCliDet,
        IDCliDet,TipoCliDet,RFCCliDet,NomComCliDet,RazSocCliDet,CalleCliDet,NumExtCliDet,NumIntCliDet,ColCliDet,LocCliDet as localidad,
        CiuCliDet,EstCliDet,MunCliDet,PaisCliDet,CPCliDet,CurpCliDet,PasCliDet,NomConCliDet,TelConCliDet,CelConCliDet,PatCli,MatCli,
        FisCli,DiasCredCli,NomConCli,rz.StatusCli,KEYCli,PredCliDet,IDCli,c.EmaConCorCli 
        FROM cat_clientes_detalle rz 
        LEFT JOIN cat_clientes c ON c.IDCli=rz.KEYCli WHERE IDCliDet='$IDCliDet';";
        
        $arrResult=$this->query($query);
        return $arrResult[0];
    }
	function filtroToSQL_Fact($filtro,$camposAfiltrar=array()) {
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
    
    public function getProductosYservicios($OrigenTaR,$KEYOrigenTaR,$filtro,$start,$limit){
		/*		EL RESULTADO DEBE IR PAGINADO, EL INCONVENIENTE AQUI ES QUE EL RESULTADO ES UN MERGE DE DOS TABLAS 		*/    	
    	//"					SELECT COUNT(IDProd) as total FROM cat_productos LEFT JOIN cat_sucursales 					"    	
		//-------------------------------------------PRODUCTOS PAGINADOS-------------------------------------------------------------------

		$filtrosProds=$this->filtroToSQL_Fact($filtro, array('DescProd'));
		$filtrosKits= $this->filtroToSQL_Fact($filtro, array('des_Kit'));
		$filtrosServs=$this->filtroToSQL_Fact($filtro, array('DescServ'));
				
		if (empty($filtrosProds)){
			$filtrosProds.=" WHERE StatusProd='A'";
			$filtrosKits .=" WHERE Status_Kit ='A'";
			$filtrosServs.=" WHERE StatusServ='A'";
		}else{
			$filtrosProds.=" AND StatusProd='A'";
			$filtrosKits .= " AND Status_Kit='A'";
			$filtrosServs.=" AND StatusServ='A'";
		}
		
    	$query="(SELECT COUNT(IDProd) as total FROM cat_productos p $filtrosProds) UNION ";
    	$query.="(SELECT COUNT(ID_Kit) as total FROM cat_productos_kits p $filtrosKits) UNION ";
		$query.="(SELECT COUNT(IDServ) as total FROM cat_servicios p $filtrosServs)";

		
		$arrCount=$this->query($query);
		$totalRows=0;
		for($i=0; $i<sizeof($arrCount); $i++){
			$totalRows+=$arrCount[$i]['total'];	
		}
		
		$query = "(SELECT 1 as TipoArt,'images/iconos/productos.png' as icono,
			IDProd as KEYProdServ, CONCAT('p-',IDProd) as idConcat, p.DescProd as Descripcion,p.PrecioProd as PrecioU
			FROM cat_productos p $filtrosProds) UNION ";

          $query.= "(SELECT 3 as TipoArt,'images/iconos/box.png' as icono,
			ID_Kit as KEYProdServ, CONCAT('k-',ID_Kit) as idConcat, des_Kit as Descripcion,costo_Kit as PrecioU
			FROM cat_productos_kits  $filtrosKits) UNION ";		
		
        $query.= "(SELECT 2 as TipoArt,'images/iconos/servicios.png' as icono,
        	IDServ as KEYProdServ, CONCAT('s-',IDServ) as idConcat, DescServ as Descripcion,PrecioServ as PrecioU
			FROM cat_servicios $filtrosServs) Order By Descripcion LIMIT $start,$limit;";
		
		$productos_y_servicios = $this->query($query);
		//------------------------------------RESPUESTA
        $datos = array();
		$datos['data']=$productos_y_servicios;
        $datos['total'] =$totalRows;
        return $datos;
    }
	public function getKit($OrigenTaR,$KEYOrigenTaR,$IdKit,$tipoDocumento){
		$query = "SELECT 'K' as TipoArt,'Kit' as DescUni,des_Kit Detalle,ifnull((SELECT SUM(ImpTasa) FROM impuestos_relaciones r
		LEFT JOIN cat_tasas_relaciones TR ON TR.KEYImpTaR = r.KEYImpImpR AND OrigenTaR='$OrigenTaR' AND KEYOrigenTaR=$KEYOrigenTaR
		LEFT JOIN cat_tasas t ON t.IDTasa=TR.KEYTasaTaR
		WHERE TipoImpR='K' AND KEYProdServImpR=ID_Kit ),0) as SumPorImp,ID_Kit as KEYProdServ, CONCAT('k-',ID_Kit) as idConcat, des_Kit as Descripcion,costo_Kit as PrecioU
        FROM cat_productos_kits s WHERE ID_Kit=$IdKit";
        $arrRes= $this->query($query);	
		//-----------------------------------------IMPUESTOS DEL KIT-----------------------------------------
		IF ($tipoDocumento!='RECIBO DE HONORARIOS' && $tipoDocumento!='RECIBO DE ARRENDAMIENTO'){
				$filtro=" AND i.IDImp < 3";
			}else{
				$filtro='';
			}
			 $kit=$arrRes[0];
			$IDKit=$kit['KEYProdServ'];
													
			$queryImpuestos="SELECT 1 as selected,ImpTasa,DescTasa,DescImp,IDImp FROM impuestos_relaciones r
			LEFT JOIN cat_tasas_relaciones TR ON TR.KEYImpTaR = r.KEYImpImpR AND OrigenTaR='$OrigenTaR' AND KEYOrigenTaR=$KEYOrigenTaR
			LEFT JOIN cat_tasas t ON t.IDTasa=TR.KEYTasaTaR
			LEFT JOIN cat_impuestos i ON i.IDImp=t.KEYImpTasa
			WHERE TipoImpR='K' AND KEYProdServImpR=$IdKit $filtro ORDER BY i.IDImp ASC;";
			$impuestosArray=$this->query($queryImpuestos);
			$kit['impuestos']=$impuestosArray;	
			$kit['componentes']	=$this->getComponentesDelKit($OrigenTaR,$KEYOrigenTaR,$IDKit);	
		return $kit;
	}
    
	public function getComponentesDelKit($OrigenTaR,$KEYOrigenTaR,$idKit){
		$query = "SELECT 'P' as TipoArt,DetProd Detalle,DescUni,0 as SumPorImp,IDProd as KEYProdServ,
		 CONCAT('p-',IDProd) as idConcat, p.DescProd as Descripcion,p.PrecioProd as PrecioU, -1 as KEYConDep_FacDet, can_Prod_rel as Cantidad,
		0 DescuentoPesos,0 as impuestos,(PrecioProd * can_Prod_rel) as Subtotal,(PrecioProd * can_Prod_rel) as Total,0 DescuentoPorcentaje  
        FROM cat_productos_kit_relaciones
        LEFY JOIN cat_productos p ON IDProd=KEY_Prod_rel
		LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
		WHERE KEY_Kit_rel=$idKit;";
		
        $arrRes= $this->query($query);		
		//-----------------------------------------IMPUESTOS DEL PRODUCTO-----------------------------------------			
			 
        for($i=0; $i<sizeof($arrRes); $i++){        
        	$IDProd=$arrRes[$i]['KEYProdServ'];
			
			$queryImpuestos="SELECT 1 as selected,ImpTasa,DescTasa,DescImp,IDImp 
			FROM cat_tasas_relaciones TR 
			LEFT JOIN impuestos_relaciones r ON TR.KEYImpTaR = r.KEYImpImpR AND OrigenTaR='$OrigenTaR' AND KEYOrigenTaR=$KEYOrigenTaR
			LEFT JOIN cat_tasas t ON t.IDTasa=TR.KEYTasaTaR
			LEFT JOIN cat_impuestos i ON i.IDImp=t.KEYImpTasa
			WHERE TipoImpR='P' AND KEYProdServImpR=$IDProd";
			$impuestosArray=$this->query($queryImpuestos);
			
			//-------------------------------------------------------------------------------
			//			CALCULA EL TOTAL DEL PRODUCTO, TOMADO EN CUENTA CADA UNO DE LOS IMPUESTOS DEL PRODUCTO
			//-------------------------------------------------------------------------------
			
			$sub = $arrRes[$i]['Subtotal'];
			$acumulado_de_impuestos_en_pesos=0;
			for($x=0; $x<sizeof($impuestosArray); $x++){	//Primero calcula el impuesto en pesos y se acumula el valor.				
				$imp=$impuestosArray[$x]['ImpTasa'];
				$imp=$imp/100;
				$impPesos=$imp*$sub;
				$acumulado_de_impuestos_en_pesos+=$impPesos;				
			}
			
			$arrRes[$i]['Total']=$sub+$acumulado_de_impuestos_en_pesos;			
			$arrRes[$i]['impuestos']=$impuestosArray;
        }
        
        				
		return $arrRes;
	}
    
	public function getProducto($OrigenTaR,$KEYOrigenTaR,$IdProd,$tipoDocumento){
		$query = "SELECT 'P' as TipoArt,DetProd Detalle,DescUni,ifnull((SELECT SUM(ImpTasa) FROM impuestos_relaciones r
	LEFT JOIN cat_tasas_relaciones TR ON TR.KEYImpTaR = r.KEYImpImpR AND OrigenTaR='$OrigenTaR' AND KEYOrigenTaR=$KEYOrigenTaR
	LEFT JOIN cat_tasas t ON t.IDTasa=TR.KEYTasaTaR	
	WHERE TipoImpR='P' AND KEYProdServImpR=IDProd ),0) as SumPorImp,IDProd as KEYProdServ, CONCAT('p-',IDProd) as idConcat, p.DescProd as Descripcion,p.PrecioProd as PrecioU, p.Seriado
        FROM cat_productos p 
		LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
		WHERE IDProd=$IdProd ";

        $arrRes= $this->query($query);			
		//-----------------------------------------IMPUESTOS DEL PRODUCTO-----------------------------------------			
			 $producto=$arrRes[0];
			$IDProd=$producto['KEYProdServ'];
			// IF ($tipoDocumento!='RECIBO DE HONORARIOS' && $tipoDocumento!='RECIBO DE ARRENDAMIENTO'){
				// $filtro=" AND i.IDImp < 3";
			// }else{
				$filtro='';
			// }
			$queryImpuestos="SELECT 1 as selected,ImpTasa,DescTasa,DescImp,IDImp FROM impuestos_relaciones r
			LEFT JOIN cat_tasas_relaciones TR ON TR.KEYImpTaR = r.KEYImpImpR AND OrigenTaR='$OrigenTaR' AND KEYOrigenTaR=$KEYOrigenTaR
			LEFT JOIN cat_tasas t ON t.IDTasa=TR.KEYTasaTaR
			LEFT JOIN cat_impuestos i ON i.IDImp=t.KEYImpTasa
			WHERE TipoImpR='P' AND KEYProdServImpR=$IDProd $filtro ORDER BY i.IDImp ASC;";
			//WHERE TipoImpR='P' AND KEYProdServImpR=$IDProd AND IDImp ";
			$impuestosArray=$this->query($queryImpuestos);
			$producto['impuestos']=$impuestosArray;	
		
		return $producto;
	}
	
	public function getServicio($OrigenTaR,$KEYOrigenTaR,$IdServ,$tipoDocumento){
		$datosDelNegocio=$this->getDatosDelaEmpresa($_POST['IDEmp']);
		
		if($datosDelNegocio[RFCEmisor] == 'LIN070614CK3')
			$servicio = 'NO APLICA';
		else
			$servicio = 'Servicio';
		
		 $query = "SELECT 'S' as TipoArt,'$servicio' as DescUni, DetServ Detalle,ifnull((SELECT SUM(ImpTasa) FROM impuestos_relaciones r
	LEFT JOIN cat_tasas_relaciones TR ON TR.KEYImpTaR = r.KEYImpImpR AND OrigenTaR='$OrigenTaR' AND KEYOrigenTaR=$KEYOrigenTaR
	LEFT JOIN cat_tasas t ON t.IDTasa=TR.KEYTasaTaR
	WHERE TipoImpR='S' AND KEYProdServImpR=IDServ ),0) as SumPorImp,IDServ as KEYProdServ, CONCAT('s-',IDServ) as idConcat, DescServ as Descripcion,PrecioServ as PrecioU
        FROM cat_servicios s WHERE IDServ=$IdServ";
        $arrRes= $this->query($query);	

			//-----------------------------------------IMPUESTOS DE CADA SERVICIO-----------------------------------------
			// IF ($tipoDocumento!='RECIBO DE HONORARIOS' && $tipoDocumento!='RECIBO DE ARRENDAMIENTO'){
				// $filtro=" AND i.IDImp < 3";
			// }else{
				$filtro='';
			// }
			$servicio=$arrRes[0];
			$IDServ=$servicio['KEYProdServ'];
													
			$queryImpuestos="SELECT 1 as selected,ImpTasa,DescTasa,DescImp,IDImp FROM impuestos_relaciones r
			LEFT JOIN cat_tasas_relaciones TR ON TR.KEYImpTaR = r.KEYImpImpR AND OrigenTaR='$OrigenTaR' AND KEYOrigenTaR=$KEYOrigenTaR
			LEFT JOIN cat_tasas t ON t.IDTasa=TR.KEYTasaTaR
			LEFT JOIN cat_impuestos i ON i.IDImp=t.KEYImpTasa
			WHERE TipoImpR='S' AND KEYProdServImpR=$IDServ $filtro ORDER BY i.IDImp ASC;";
			//WHERE TipoImpR='S' AND KEYProdServImpR=$IDServ AND  ";
			$impuestosArray=$this->query($queryImpuestos);
			//throw new Exception($queryImpuestos);
			$servicio['impuestos']=$impuestosArray;						
		return $servicio;
	}

	private function validarFolioYfecha(){
		//Cuando la factura es CFD,
		//verificar la fecha y folio de la factura
		//la fecha de la factura debe ser posterior a cualquier otra factura 
	}
	
    public function guardar($datos,$folioQuemado=0){
		$this->referencia = "FAC-".$datos['SerFol'].'-'.$datos['Folio'];

		if ($folioQuemado){
			$this->folioQuemado=$folioQuemado;
		}
		
		if (sizeof($this->conceptos)==0){
			throw new Exception("Disculpe, debe agregar detalles a la factura");
		}
		
        $datos['FechaFac']=  $this->jsDateToMysql($datos['FechaFac']);	//<--Aplicar formato a la fecha para poder guardarla
		if ( isset($datos['FecPago']) ){
			$datos['FecPago'] = $datos['FechaFac'];
		}
        $registroNuevo=false;	//<---Marcador que indica el tipo de consulta a ejecutar ( false=update,  true=insert )
		 
        $IDUsu=$_SESSION['Auth']['User']['IDUsu'];
        $IDcli=	   $datos['IDRazSoc'];
		$empresaId=$datos['IDEmp'];
		$efectivo=false;
		
		//Este switch quemado deberia estar en el modelo FormasDePagoModel, la funcion getDescripcion($id)nos regresaria la descripcion
		//$datos['MetPago']=$formasPagoModel->getDescripcion($datos['KEYMetPago']);		
		// throw new Exception($datos['KEYMetPago']);
		switch($datos['KEYMetPago']){
			case 1:
				$datos['MetPago']='EFECTIVO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 2:
				$datos['MetPago']='CRÉDITO';			
				break;
			case 3:
				$datos['MetPago']='CHEQUE';		
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 4:
				$datos['MetPago']='TRANSFERENCIA';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 5:
				$datos['MetPago']='DEPÓSITO EN VENTANILLA';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 6:
				$datos['MetPago']='TARJETA DE CRÉDITO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 7:
				$datos['MetPago']='TARJETA DE DÉBITO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 8:
				$datos['MetPago']='CHEQUE NOMINATIVO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 9:
				$datos['MetPago']='CHEQUE AL PORTADOR';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 10:
				$datos['MetPago']='NO IDENTIFICADO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 11:
				$datos['MetPago']='NO APLICA';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 12:
				$datos['MetPago']='TRANSFERENCIA ELECTRONICA DE FONDOS';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			default:
				throw new Exception("El Método de pago que intenta almacenar es desconocido");
		}
		//---------------------------------------------------------------
		$sucursalId=$datos['IDSuc'];
		$where='';
        if ($datos[$this->primaryKey]){
			throw new Exception("La Factura no puede editarse");				
        }else{  //INSERT
            $registroNuevo=true;           
            $query="INSERT INTO $this->useTable SET ";
            $query.="AddUsuario=$IDUsu";    //LOG
            $query.=",AddFecha=now()";
			$query.=",FechaTimbrado=now(
			),";			
			
			$datosDelNegocio=$this->getDatosDelaEmpresa($empresaId);


			//--------------------------------------------------------------------------------------------------------------------------------------
			//	Se agrega una validación, si la empresa está configurada para CFD, se revisa que la fecha de la factura por crear sea posterior  
			// 	a la fecha de la factura anterior (las canceladas no serán tomadas en cuenta), 
			//--------------------------------------------------------------------------------------------------------------------------------------
			$CFDiEmp=$datosDelNegocio['CFDiEmp'];
			if ($datosDelNegocio['CFDiEmp']==='0'){
				$params=array(
					'IDEmp'=>$empresaId,
					'IDSuc'=>$sucursalId,
					'FechaFac'=>$datos['FechaFac']					
				);
				
				if ($_SESSION['IDCor']==0 && $_SESSION['Auth']['User']['IDEmp']==0 ){
				}else{
					$valida=$this->validarFechaParaCFD($params);
				}
				
			}elseif($datosDelNegocio['CFDiEmp']==='1'){
				$params=array(
					'IDEmp'=>$empresaId,
					'IDSuc'=>$sucursalId,
					'FechaFac'=>$datos['FechaFac']					
				);
				$valida=$this->validarFechaParaCFD($params);
			}else{
				throw new Exception("la empresa debe tener configurado el modo de facturación (CFD ó CFDI) ");
			}
			
			//--------------------------------------------------------------------------------------------------------------------------------------			
        	unset($datosDelNegocio['CFDiEmp']);
        	$negocioSlashes=array();
        	
			foreach($datosDelNegocio as $key=>$value){
				$negocioSlashes[$key]=addslashes($value);
			}
			
			$datosDelNegocio=$negocioSlashes;
			$datosDelaSucursal=$this->getDatosDelaSucursal($sucursalId);
        	$sucursalSlashes=array();
        	foreach($datosDelaSucursal as $key=>$value){
				$sucursalSlashes[$key]=addslashes($value);
			}
			$datosDelaSucursal=$sucursalSlashes;
			//$datos=array_merge($clienteReparado,$datos);
			$datos=array_merge($datosDelNegocio,$datos);
			$datos=array_merge($datosDelaSucursal,$datos);
			
						
			$parametrosModel=new Parametros();
			if(!empty($datos['RFCEmisor'])){
				$datosInfoFolios = $parametrosModel->getInfoFoliosNuevo($datos['RFCEmisor']);
				// $datosInfoFolios = $parametrosModel->getInfoFoliosNuevo('PART7911141K9');
				$saldoFolios = 				$datosInfoFolios['Saldo'];
				$consumidosALaFechaFolios = $datosInfoFolios['ConsumidosAlaFecha'];
				$saldosRestantesFolios = 	$saldoFolios - $consumidosALaFechaFolios;
				if($saldosRestantesFolios <= 0){
					throw new Exception('No hay timbres suficientes para generar el documento fiscal.');
				}
				
			}
			
			//$datos['TipComp']="ingreso";
			
			
			
			if ( empty($datosDelaSucursal) ){
				$datos['LugarExpedicion']=$datosDelNegocio['MunEmisor'].', '.$datosDelNegocio['EdoEmisor'];			
			}else{
				$datos['LugarExpedicion']=$datosDelaSucursal['Munsuc'].', '.$datosDelaSucursal['EdoSuc'];			
			}
        }
		//-----------------------------------------------------
		// Cuando la factura es el pago de una parcialidad, se realizan algunas validaciones adicionales
		
		if (isset($this->parcialidades)){			
			if ($this->parcialidades){
				$origen=$this->paramsOrigen['IDFacOrigen'];				
				$numParcialidad=$datos['numParcialidad'];
				$totParcialidades=$datos['totParcialidades'];
				//<------Que el numero de parcialidad no haya sido registrado
				$sqlParcialidad="SELECT IDFac,SerFol,Folio  FROM facturacion_parcialidades 
				LEFT JOIN facturacion ON IDFac=KEY_Factura_Par 
				WHERE KEY_Factura_Origen_Par=$origen AND status!='C' AND numParcialidad =$numParcialidad";
				$arrParcialidad=$this->select($sqlParcialidad);				
				if (!empty($arrParcialidad)){
					$serie=$arrParcialidad[0]['SerFol'];
					$folio=$arrParcialidad[0]['Folio'];
					$serFol=empty($serie)? ''.$folio : $serie.'-'.$folio;
					throw new Exception("La parcialidad $numParcialidad ya ha sido registrada en la factura <label style='font-weight:bold;'> $serFol </label>");
				}
				
				$sqlOrigen="SELECT Total,numParcialidad FROM facturacion WHERE IDFac=$origen";
				$arrOrigen=$this->select($sqlOrigen);				
				$totalOrigen=floatval($arrOrigen[0]['Total']);				
				$numParcialidadOrigen=empty($arrOrigen[0]['numParcialidad'])? 0 : intval($arrOrigen[0]['numParcialidad']);
				
				//<-------Que el numero de parcialidad sea menor  o igual al total de parcialidades
				if (intval($numParcialidad) > intval($totParcialidades) ){
					throw new Exception('El número de la parcialidad a pagar ($numParcialidad) debe ser menor o igual al numero total de parcialidades ($totParcialidades)');
				}
				//<-------Que la suma de las parcialidades no exceda el total de la "Factura Origen"
				$sqlSumaDeParcialidades="SELECT SUM(Monto_Par) as totalParcialidades 
				FROM facturacion_parcialidades 
				LEFT JOIN facturacion ON IDFac=KEY_Factura_Par
				WHERE KEY_Factura_Origen_Par=$origen AND status!='C'";
				//throw new exception($sqlSumaDeParcialidades);
				$arrSuma=$this->select($sqlSumaDeParcialidades);
				if (!empty($arrSuma)){
					$totalParcialidades=floatval($arrSuma[0]['totalParcialidades']);
					$totalParcialidades+=floatval($datos['Total']);
					if ($totalParcialidades > $totalOrigen ){
						$totalOrigen="$".formatearMoneda($totalOrigen);
						$totalParcialidades="$".formatearMoneda($totalParcialidades);
						$mensaje="El total de factura original es $totalOrigen,<br/> la suma de las parcialidades($totalParcialidades) excedería este total.";
						$mensaje.='<br/>';
						$mensaje.='Intente cambiando los importes de esta factura';
						throw new Exception($mensaje);
					}else if($totalParcialidades == $totalOrigen){
						//throw new Exception("Factura Pagada");
						$this->facturaSaldada=true;
					}
				}								
			}			
		}
        #----------------------------------------------------------------
        # OBTENGO EL FOLIO QUE DEBE GUARDARSE
		$IDSerFol=$datos['IDSerFol'];
		$cfdData = $this->getDatosParaCfd($empresaId,$sucursalId,$IDSerFol);		
		$SerFol=$datos['SerFol'];
		//-------------------------------------
		
		$modoPrueba=0;
		if( $CFDiEmp==1 ){		//		CUANDO LA FACTURA SERA CFDI, SE REVISA LA LICENCIA        	
			$RFCEmisor=$datos['RFCEmisor'];			
        	$licencia=$this->validarLicencia($RFCEmisor);
			
			$modoPrueba=$licencia['modoPrueba'];				
			if ($modoPrueba==0){				//		EN MODO NORMAL, SE ACTUALIZA LA TABLA DE FOLIOS
				$arrFolio=$this->obtenerFolio( $empresaId,$sucursalId,$SerFol );	
				$this->IDFol=$arrFolio['IDFol'];
				$this->folioQuemado=$arrFolio['SigFol'];
				$folio=$arrFolio['SigFol'];
											
			}else{
				
				$folio = $datos['Folio'];
			}        					
        }else{
        	$arrFolio=$this->obtenerFolio($empresaId,$sucursalId,$SerFol);
			$this->IDFol=$arrFolio['IDFol'];
			$this->folioQuemado=$arrFolio['SigFol'];			
			$folio=$arrFolio['SigFol'];
        	
        }
		$datos['Folio']=$folio;
		//-------------------------------------
        $datos=array_merge($cfdData,$datos);
        foreach($datos as $key=>$value){
            if ($value!=''){
                $query.="$key='$value',";
            }
        }
        $query=substr($query, 0,strlen($query)-1);   
       // throw new Exception($query);
        #----------------------------------------------------------------
        $query=$query.$where;
		if ($registroNuevo){            
			$id= $this->insert($query); 			
			$SerFol=$datos['SerFol'];
			$temp=true;
		}else{		
			$result=$this->update($query);               
			$id=$datos['IDFac'];
			$temp=false;
		}

        $this->guardarConceptos($id);

        $data=$this->getById($id,$temp);        
        $folio=$data['Factura']['Folio'];        
        $data['Factura']['Folio']=$folio;
        $data['modoPrueba']=$modoPrueba;

        return $data;        
    }
    
	function validarFechaParaCFD($params){
		//	Se agrega una validación, si la empresa está configurada para CFD, se revisa que la fecha de la factura por crear sea posterior  
		// 	a la fecha de la factura anterior (las canceladas no serán tomadas en cuenta),
		$empresa=$params['IDEmp'];
		$sucursal=$params['IDSuc'];
		$fecha=$params['FechaFac'];
		$serie=$_POST['SerFol'];
		
		$query="SELECT DATE_FORMAT('$fecha','%d/%m/%Y %H:%i:%S') as Fecha, 
		IDFac,DATE_FORMAT(FechaFac,'%d/%m/%Y %H:%i:%S') as FechaFac FROM facturacion 
		WHERE IDEmp=$empresa AND IDSuc=$sucursal AND FechaFac > '$fecha' AND status!='C' AND SerFol='$serie' 
		ORDER BY FechaFac DESC";		
		
		//throw new Exception($query);
		$arrRes=$this->query($query);
		$total=sizeof($arrRes);
		if ($total>0){
			$fechaPosterior=$arrRes[0]['FechaFac'];
			$fecha=$arrRes[0]['Fecha'];
			throw new Exception("Existen facturas($total) con fecha posterior a la actual ($fecha), ej:$fechaPosterior");
		}
		//-----------------------------------------------
		// Otra validaciòn:
		//Valida que la fecha de la factura sea NO SEA MAYOR que la fecha actual
		$date = DateTime::createFromFormat('Y-m-d H:i:s', $fecha);
		
		//Que la fecha no sea posterior a la fecha actual
		$fechaJS= $date->format('d/m/Y H:i:s');			
		
    	//si no pudo convertirse se lanza un error
		if (!is_object($date)){
			throw new Exception("Error al guardar. La Fecha o el formato es incorrecto (".$fecha."). ");
		}
		$time=time();
		$fechaActualString=date('d/m/Y H:i:s',$time);//$fechaActualString=getFechaActual("%d/%m/%Y %H:%i:%s");
		
		$fecha_actual=new DateTime();	//$fecha_actual=DateTime::createFromFormat('d/m/Y H:i:s', $fechaActualString);
		
		if ($date>$fecha_actual){			
			throw new Exception("La Fecha de la factura ($fechaJS) es mayor que la fecha actual ($fechaActualString)");
		}	
		return true;
	}
	
	public function guardarPreview($datos){
		
		if (sizeof($this->conceptos)==0){
			throw new Exception("Disculpe, debe agregar detalles a la factura");
		}
		
        $datos['FechaFac']=  $this->jsDateToMysql($datos['FechaFac']);	//<--Aplicar formato a la fecha para poder guardarla
		
        $registroNuevo = false; //<---Marcador que indica el tipo de consulta a ejecutar ( false=update,  true=insert )
		 
        $IDUsu=$_SESSION['Auth']['User']['IDUsu'];
		
		$empresaId=$datos['IDEmp'];
		$efectivo=false;
		//Señoras y señores a falta de una tabla con métodos de pago tenemos este código quemado
		switch($datos['KEYMetPago']){
			case 1:
				$datos['MetPago']='EFECTIVO';
				$datos['FecPago']=$datos['FechaFac'];
				break;
			case 2:
				$datos['MetPago']='CRÉDITO';					
				break;
			case 3:
				$datos['MetPago']='CHEQUE';		
					$datos['FecPago']=$datos['FechaFac'];
				break;
			case 4:
				$datos['MetPago']='TRANSFERENCIA';	
					$datos['FecPago']=$datos['FechaFac'];
				break;
			case 5:
				$datos['MetPago']='DEPÓSITO EN VENTANILLA';	
					$datos['FecPago']=$datos['FechaFac'];
				break;
			case 6:
				$datos['MetPago']='TARJETA DE CRÉDITO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 7:
				$datos['MetPago']='TARJETA DE DÉBITO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 8:
				$datos['MetPago']='CHEQUE NOMINATIVO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 9:
				$datos['MetPago']='CHEQUE AL PORTADOR';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 10:
				$datos['MetPago']='NO IDENTIFICADO';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 11:
				$datos['MetPago']='NO APLICA';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
			case 12:
				$datos['MetPago']='TRANSFERENCIA ELECTRONICA DE FONDOS';	
				//$datos['FecPago']=$datos['FechaFac'];
				break;
		}
		
		$sucursalId=$datos['IDSuc'];
		$where='';
        if ($datos[$this->primaryKey]){
			// NO se puede editar una factura, de hecho el boton "GUARDAR" aparece deshabilitado
			throw new Exception("La Factura no puede editarse");
			
        }else{  //INSERT
            $registroNuevo=true;
            $query="INSERT INTO $this->useTable SET ";
            $query.="AddUsuario=$IDUsu";
            $query.=",AddFecha=now()";
			$query.=",FechaTimbrado=now(),";			
            
			$datosDelNegocio=$this->getDatosDelaEmpresa($empresaId);
			unset($datosDelNegocio['CFDiEmp']);
			foreach($datosDelNegocio as $key=>$value){
				$datosEmpFix[$key]=addslashes($value);
			}
			$datosDelaSucursal=$this->getDatosDelaSucursal($sucursalId);
			foreach($datosDelaSucursal as $key=>$value){
				$datosSucFix[$key]=addslashes($value);
			}
			
			$datos=array_merge($datosEmpFix,$datos);
			if (isset($datosSucFix)){
				$datos=array_merge($datosSucFix,$datos);
			}
			
			$cfdData = $this->getDatosParaCfd($empresaId,$sucursalId,$datos['IDSerFol']);	
			
			$datos=array_merge($cfdData,$datos);
        }
		
        foreach($datos as $key=>$value){
            if ($value!=''){
                $query.="$key='$value',";
            }
        }
        $query = substr($query, 0,strlen($query)-1); // elimina la ultima coma del query
        $query = $query.$where;
		
		if ($registroNuevo){
			$id= $this->insert($query);
			$SerFol=$datos['SerFol'];
			$temp=true;
		} else {
			$result=$this->update($query);               
			$id=$datos['IDFac'];
			$temp=false;
		}
		
        $this->guardarConceptos($id);
        
        $data=$this->getById($id,$temp,true);
        
        return $data;
	}
	
	private function obtenerFolio($empresaId,$sucursalId,$SerFol){		
		//bloquear tabla en bd
		ignore_user_abort(true);
		set_time_limit(0);
		$this->update("LOCK TABLES cat_folios WRITE;");
		
		//Obtiene el siguiente folio y los campos del bloqueo  lógico
		$query="SELECT IDFol,SerieFol,SigFol,FinalFol,BlockUserIdFol ,BlockFecha
			FROM cat_folios 
			WHERE KEYEmpFol=$empresaId AND KEYSucFol=$sucursalId AND SigFol<=FinalFol AND StatusFol='A' AND SerieFol='$SerFol'
			ORDER BY PredetFol DESC,SerieFol,SigFol ASC";
		$arrFolio=$this->select($query);				

		if ( empty($arrFolio) ){			
			throw new Exception("No hay folios disponibles para la serie seleccionada");
		}
		
		$idUser=$_SESSION['Auth']['User']['IDUsu'];
		//Revisar que no tenga bloqueo logico el select
		if ( !empty($arrFolio[0]['BlockFecha']) ){

			if ($arrFolio[0]['BlockUserIdFol']==0){
				$usuario="Super Usuario";
			}else{
				//buscar nombre del usuario
				$sqlUserName="SELECT NomUsu FROM cat_usuarios WHERE IDUsu=".$arrFolio[0]['BlockUserIdFol'];
				$arrUser=$this->select($sqlUserName);
				if ( empty($arrUser) ){
					throw new Exception("No se encontró el nombre del usuario que bloqueo la tabla");
				}
				$usuario=$arrUser[0]['NomUsu'];
			}
			throw new Exception("Los folios están esperando respuesta del usuario $usuario");
		}
		
		//si esta  desbloqueda: entonces update bloqueo lógico
		$idFol=$arrFolio[0]['IDFol'];				
		
		$sql="UPDATE cat_folios SET BlockUserIdFol=$idUser,BlockFecha=now() WHERE IDFol=$idFol";
		$this->update($sql);
		//desbloquer físico
		$this->execute("UNLOCK TABLES;");

		return $arrFolio[0];
	}
	
	private function gastarFolio($IDFol,$folio){
		$sigFol=floatval($folio)+1;
		$sql="UPDATE cat_folios set SigFol=$sigFol,BlockUserIdFol=NULL,BlockFecha=NULL WHERE IDFol=$IDFol";		
		$this->update($sql);
	}
	
	function desbloquearFolio($IDFol){
		$sql="UPDATE cat_folios set BlockUserIdFol=NULL,BlockFecha=NULL WHERE IDFol=$IDFol";		
		$this->update($sql);
	}
	
	/*private function asignarFolio($empresaId,$sucursalId,$SerFol,$IDFac){		
		try{			
			$this->update("LOCK TABLES cat_folios WRITE;");			
			$nextFol=$this->query("CALL asignar_folio($empresaId,$sucursalId,'$SerFol',$IDFac);");
			$this->update("UNLOCK TABLES;");			
			$SigFol=$nextFol[0]['V_NextFol'];
			if (!is_numeric($SigFol)){
				throw new Exception("Se han terminado los folios");
			}						
			return $SigFol;
		}catch(Exception $e){
			$this->update("UNLOCK TABLES");
			throw new Exception($e->getMessage());
		}		
	}*/
	
	public function getDatosParaCfd($IDemp,$SucId,$IDFol){
		//SELECCIONA EL CERTIFICADO POR DEFAULT DE LA EMPRESA RELACIONADO CON EL FOLIO
		
		if ($SucId=='0'){
			
			$query="SELECT NumSerCer as NumCert,NumAprobFol as NumApro,AnoAprobFol as AnioApro,CerPemData Cert from cat_certificados
			LEFT JOIN cat_folios  ON KEYEmpCer=KEYEmpFol 
			WHERE KEYEmpFol=$IDemp AND KEYSucFol=0 AND IDFol=$IDFol AND DefaultCer=1";
		}else{			
			$query="SELECT NumSerCer as NumCert,NumAprobFol as NumApro,AnoAprobFol as AnioApro,CerPemData Cert 
			FROM cat_certificados_sucursales 
			LEFT JOIN cat_folios ON IDFol=$IDFol
			LEFT JOIN cat_certificados ON IDCer=KEYCerCerSuc
			WHERE KEYSucCerSuc=$SucId AND DefaultCerSuc=1 AND IDFol=$IDFol";			
		}

		$arrRes=$this->query($query);
		if (sizeof($arrRes)==0){
			throw new Exception('No hay un certificado asignado a la empresa. Es necesario contar con un certificado para emitir facturas');
		}
		$cerPem=$arrRes[0]['Cert'];
		/*				
 		VOY A QUITARLE LAS CADENAS 
		-----BEGIN CERTIFICATE-----   
		012345678901234567890123456
		-----END CERTIFICATE-----
		1234567890123456789012345
		*/		
		$sinBegin=str_replace("-----BEGIN CERTIFICATE-----", '' , $cerPem );
		$sinEnd=str_replace("-----END CERTIFICATE-----", '' , $sinBegin );
		$cerPemSinEspacios=trim($sinEnd);				
		$arrRes[0]['Cert']=$cerPemSinEspacios;
		return $arrRes[0];
	}
	
	public function getDatosDelCliente($IDRazSoc){
		$query="
		SELECT c.IDCli,RFCCliDet RFCCliente, RazSocCliDet RazSoc, NomCli as NomCliente,CalleCliDet as CalleCliente, 
			NumExtCliDet as NumExtCliente, NumIntCliDet as NumIntCliente, ColCliDet as ColCliente, 
			LocCliDet as LocCliente, nom_ciu as MunCliente,	nom_est as EdoCliente,nom_pai as PaisCliente,
			CPCliDet as CPCliente,CurpCliDet as CurpCliente ,PasCliDet as PasaporteCliente 
		FROM cat_clientes_detalle d  
			LEFT JOIN cat_clientes c ON d.KEYCli=c.IDCli 
			LEFT JOIN cat_paises ON id_pai=PaisCliDet 
			LEFT JOIN cat_estados ON id_est=EstCliDet AND key_pai_est=PaisCliDet 
			LEFT JOIN cat_ciudades ON id_ciu=CiuCliDet AND key_est_ciu=EstCliDet AND key_pai_ciu=PaisCliDet 
		WHERE d.IDCliDet=$IDRazSoc";
		$arrRes=$this->query($query);
		return $arrRes[0];		
	}
	
	public function getDatosDelaEmpresa($empresaId){
		$query="SELECT  RFCEmp RFCEmisor,CFDiEmp,FisEmp NomEmisor,CalleEmp CalleEmisor,NumExtEmp NumExtEmisor,NumIntEmp NumIntEmisor,ColEmp ColEmisor,
		LocEmp LocEmisor,nom_ciu MunEmisor,nom_est EdoEmisor,nom_pai PaisEmisor,CPEmp CPEmisor
		FROM cat_empresas 
		LEFT JOIN cat_paises ON id_pai=PaisEmp 
		LEFT JOIN cat_estados ON id_est=EstEmp AND key_pai_est=PaisEmp 
		LEFT JOIN cat_ciudades ON id_ciu=MunEmp AND key_est_ciu=EstEmp AND key_pai_ciu=PaisEmp
		WHERE IDEmp=$empresaId";
		$arrRes=$this->query($query);
		return $arrRes[0];	
	}
	
	public function getDatosDelaSucursal($sucursalId){
		$query="SELECT  CalleSuc,NumExtSuc,NumIntSuc,ColSuc,LocSuc,nom_ciu Munsuc,nom_est EdoSuc,nom_pai PaisSuc,CPSuc
		FROM cat_sucursales 
		LEFT JOIN cat_paises ON id_pai=PaisSuc 
		LEFT JOIN cat_estados ON id_est=EstSuc AND key_pai_est=PaisSuc 
		LEFT JOIN cat_ciudades ON id_ciu=Munsuc AND key_est_ciu=EstSuc AND key_pai_ciu=PaisSuc
		WHERE IDSuc=$sucursalId";
		
		$arrRes=$this->query($query);
		if (sizeof($arrRes)==0){
			return array();
		}else{
			return $arrRes[0];	
		}
		
	}

	public function importarVariasOrdenes($KEYEmpOrdVen, $folios){
		
		$foliosSQL='';
		foreach($folios as $folio){
				$foliosSQL.='FolOrdVen='.$folio['FolOrdVen'].' OR ';
		}
		$foliosSQL=substr($foliosSQL,0,strlen($foliosSQL)-4);
		
		//$FolOrdVen=$folios[0]['FolOrdVen'];		//<-------SELECCIONO EL ENCABEZADO DEL PRIMER FOLIO
		$querymaxDias="SELECT MAX(DiasCreOrdVen) as DiasCreOrdVen FROM orden_venta WHERE KEYEmpOrdVen=$KEYEmpOrdVen AND ($foliosSQL)";
		
		$arrMaxDias=$this->query($querymaxDias);
		if ($arrMaxDias[0]['DiasCreOrdVen']==null){
			$diasCredito=0;
		}else{
			$diasCredito=$arrMaxDias[0]['DiasCreOrdVen'];
		}	
		
        $query="SELECT if (TipDocOrdVen='RECIBO DE HONORARIOS','RECIBO DE HONORARIOS','FACTURA' ) as TipDoc,TipoCambio,referencia,FormaPago, TipPagOrdVen KEYMetPago,TipoVenta TipoFactura,numParcialidad parcialidadA,totParcialidades parcialidadB, 
        CFDiEmp,DATE_FORMAT(now(),'%d/%m/%Y %H:%i:%S') as FechaFac,TipPagOrdVen MetPago,KEYFacOrdVen,FolOrdVen,DiasCreOrdVen,
			0 IDFac,KEYEmpOrdVen IDEmp,KEYSucOrdVen IDSuc,1 StatusFac,KEYCliOrdVen IDRazSoc,RazSocCliDet RazSoc,RFCCliDet RFCCliente,NomCli NomCliente
		FROM orden_venta 
		LEFT JOIN cat_clientes_detalle ON IDCliDet=KEYCliOrdVen
		LEFT JOIN cat_clientes ON IDCli=KEYCli
		LEFT JOIN cat_empresas e ON e.IDEmp = KEYEmpOrdVen		
		WHERE KEYEmpOrdVen=$KEYEmpOrdVen AND ($foliosSQL)";	   
        $arrresult=$this->query($query);    
        
        $sqlClientes="SELECT count(DISTINCT KeyCliOrdVen)  numClientes FROM orden_venta WHERE KEYEmpOrdVen=$KEYEmpOrdVen AND ($foliosSQL)";
		$arrClientes=$this->select($sqlClientes);
		if ( intval($arrClientes[0]['numClientes'])>1 ){
				$arrresult[0]['NomCliente']	='';
				$arrresult[0]['IDRazSoc']	=0;
				$arrresult[0]['RazSoc']		='';
				$arrresult[0]['RFCCliente']	='';
		}
		
        $arrresult[0]['DiasCreOrdVen']=$diasCredito;
        
		foreach($arrresult as $factura){
			if($factura['KEYFacOrdVen']){
				throw new Exception("La orden de venta con Folio: ".$factura['FolOrdVen']." está marcada como facturada");
			}
		}
		$datos['Factura']=$arrresult[0];

	
		switch($datos['Factura']['FormaPago']){
			case strtoupper("Pago en una sola EXHIBICIÓN"):
				$datos['Factura']['FormaPago']=1;
			break;
			case "PAGO EN PARCIALIDADES":
				$datos['Factura']['FormaPago']=3;
			break;
			default:
			//	throw new Exception("asd".$data['Factura']['numParcialidad']);
				if (is_numeric($datos['Factura']['parcialidadA'])){ //Pago en parcialidades
					$datos['Factura']['FormaPago']=2;
				}
			break;
		}
			 
		//GENERO EL FILTRO PARA OBTENER LOS DETALLES CONCATENANDO FolOrdVen=x OR...
		$orsConcatenados='';
		
		foreach($folios as $folio){
			$orsConcatenados.=" KEYFolOrdVenDet=".$folio['FolOrdVen']." OR";
		}
		
		$sizeOfOrs=sizeof($orsConcatenados);
		$orsConcatenados=substr($orsConcatenados,0,$sizeOfOrs-4);	//<----ELIMINA EL ULTIMO OR
		
		/*case TipoArtOrdVenDet as TipoArt
			when 1 then 'P' 
			when 2 then 'S'
			when 3 then 'K'
			end as TipoArt,*/
		$queryDetalles="SELECT 	0 IDFac,CantOrdVenDet Cantidad,DescProdServOrdVenDet Descripcion,Detalle,IDOrdVenDet, Importe Subtotal,
		TipoArtOrdVenDet as TipoArt,KEYEmpOrdVenDet, KEYFolOrdVenDet,
		PreOrdVenDet PrecioU,DescUni,TotOrdVenDet Total,DesOrdVenDet DescuentoPesos,KEYProdServOrdVenDet as KEYProdServ,
		if (TipoArtOrdVenDet=1,(SELECT AbrevUni FROM cat_productos LEFT JOIN 
		cat_unidad_medida ON IDUni=KEYUniProd WHERE IDProd=KEYProdServOrdVenDet),'') as DescUni,
		DesPorOrdVenDet DescuentoPorcentaje,PIva PIvaTras,PIEPS PIepsTras,IvaOrdVenDet IvaTras,IEPS IepsTras,
		PIvaRetOrdVenDet PIvaRet,IvaRetOrdVenDet IvaRet,PIsrRetOrdVenDet PIsrRet,IsrRetOrdVenDet IsrRet
		FROM orden_venta_detalle 
		where KEYEmpOrdVenDet=$KEYEmpOrdVen AND ($orsConcatenados) AND padre_IDOrdVenDet IS NULL";	
        $arrDetalles=$this->query($queryDetalles);
		#=====================================================================================================================
		#	Analizar cada elemento del detalle, en caso de ser un kit, obtener sus componentes y sustitutos
		#=====================================================================================================================
		for($i=0; $i<sizeof($arrDetalles); $i++){
			if( $arrDetalles[$i]['TipoArt']=='K' ){	
				//Traer los remplazos seleccionados
				$kit_id=$arrDetalles[$i]['IDOrdVenDet'];
				//Sustitutos seleccionados
				/*$sqlSustitutosDelKit="SELECT  predial,IDFac,  
				 PIvaTras, PIepsTras, IvaTras, IepsTras,  
				, ,KEYConFacDet, 
				from facturacion_detalle
				 ";*/
				
				$sqlSustitutosDelKit="SELECT IDOrdVenDet IDDetalle,DescUni,KEYFolOrdVenDet,KEYEmpOrdVenDet,Detalle,TipoArtOrdVenDet TipoArt,
				KEYProdServOrdVenDet KEYProdServ,DescProdServOrdVenDet Descripcion,CantOrdVenDet Cantidad,PreOrdVenDet PrecioU,Importe,DesOrdVenDet DescuentoPesos,
				IvaOrdVenDet,PIva,PIEPS,IEPS,TotOrdVenDet Total,DesPorOrdVenDet DescuentoPorcentaje,Importe Subtotal,
				IsrRetOrdVenDet IsrRet,PIsrRetOrdVenDet PIsrRet,PIvaRetOrdVenDet PIvaRet,IvaRetOrdVenDet IvaRet,remplazado_id 
				from orden_venta_detalle
				 where padre_IDOrdVenDet= $kit_id;";
				 
				$arrSustitutos=$this->select($sqlSustitutosDelKit);
				
				
				//remplazado_id contiene la informacion del producto remplazado
				$arrComponentes=array();
				$idkit= $arrDetalles[$i]['KEYProdServ'];				
				
				for($y=0; $y<sizeof($arrSustitutos); $y++){
					//cuando remplazado_id == KEYProdServ siginifica que componente ha sido remplazado por si mismo.
					//Entonces el componente siempre tiene un remplazo, esto simplifica las condiciones al programar.
					$arrSustitutos[$y]['impuestos']=array();
					$IdProd			=$arrSustitutos[$y]['remplazado_id'];
					$sustituto_id	=$arrSustitutos[$y]['KEYProdServ'];
					
					/*=============================================================================================================					
					Se compara la fecha de la factura y la fecha de modificacion del kit
					si la fecha de modificacon del kit es mayor a la fecha de la factura, los remplazos se obtienen de
					la factura, (solo se obtiene 1 remplazo a lo mucho), se impide la modificacin del kit
																																*/
					$sqlKitDate="SELECT ModFecha FROM cat_productos_kits WHERE ID_kit=".$arrDetalles[$i]['KEYProdServ'];
					$arrDate=$this->select($sqlKitDate);							
					if ( empty($arrDate) ) throw new Exception("No encontré la fecha de modificaión del kit");
					
					/*KEYEmpOrdVenDet, KEYFolOrdVenDet,*/
										
					$sqlVentaDate="SELECT FecOrdVen FROM orden_venta  WHERE KEYEmpOrdVen=".$arrDetalles[$i]['KEYEmpOrdVenDet']. ' AND 
					FolOrdVen='.$arrDetalles[$i]['KEYFolOrdVenDet'] ;
					$arrVentaDate=$this->select( $sqlVentaDate );							
					if ( empty($arrVentaDate) ) throw new Exception("No encontré la fecha de creación de la Factura");					
					//=============================================================================================================					
					if ( $IdProd == $sustituto_id ){
						//----------------------------------------------------------------
						//Impedir modificar los componentes o sustitutos del kit
						if( $arrDate[0]['ModFecha'] > $arrVentaDate[0]['FecOrdVen'] ){
							$arrSustitutos[$y]['bloqueado']=true;
						}
						//----------------------------------------------------------------
						//Ahora obtendré los sustitutos:					

						$arrSustitutos[$y]['seleccionado']=true;
						$componente=$arrSustitutos[ $y ];
						$componente['impuestos']=array();
					}else{
						//throw new Exception('Obtener los datos del producto (componente de la orden (importando))');
						//Obtener los datos del producto (componente del kit)
						if ( $datos['Factura']['IDSuc']=='0' ){
							$OrigenTaR	 ='E';
							$KEYOrigenTaR=$datos['Factura']['IDEmp'];
						}else{
							$OrigenTaR='S';
							$KEYOrigenTaR=$datos['Factura']['IDSuc'];
						}
						
						$tipoDocumento=$datos['Factura']['TipDoc'];	
												
						$sqlComponente="SELECT DescProd ,can_Prod_rel as Cantidad 
						FROM cat_productos_kit_relaciones 
						LEFT JOIN cat_productos ON IDProd=KEY_Prod_rel 
						WHERE KEY_Kit_rel=$idkit AND KEY_Prod_rel=$IdProd";			
						
						$arrCmp=$this->select( $sqlComponente );
						$componente=array(
							'IDDetalle'		=>$arrSustitutos[$y]['IDDetalle'],
							'TipoArt'		=>'P',
							'KEYProdServ'	=>$IdProd,
							'Cantidad'		=>$arrCmp[0]['Cantidad'],
							'Descripcion'	=>$arrCmp[0]['DescProd'],
							'Subtotal'		=>0,
							'impuestos'		=>array()
							//,'bloqueado'		=>true
						);					
						//$arrDetalles[$i]['bloqueado']=true;
					}
					unset( $arrSustitutos[$y]['remplazado_id'] );
					/* Obtener los remplazos configuradas para este producto en el kit */
					$IDDetalle	=$arrSustitutos[$y]['IDDetalle'];				
					
					/* 	Ahora obtendré los sustitutos	*/
					
					if( $arrDate[0]['ModFecha'] > $arrVentaDate[0]['FecOrdVen'] ){
						$componente['sugerencias']=array(
							array(
								'seleccionado'=>true,
								'Descripcion' =>$arrSustitutos[$y]['Descripcion'],
								'KEY_Kit_sus' =>$idkit,
								'KEYProdServ' =>$arrSustitutos[$y]['KEYProdServ'],
								'Cantidad'	  =>$arrSustitutos[$y]['Cantidad'],
								'TipoArt'	  =>'P',
								'IDDetalle'	  =>$arrSustitutos[$y]['IDDetalle'],
								'impuestos'	  =>array()
							)						
						);
						$componente['bloqueado']=true;

					}else{
						$sqlSustitos="SELECT if(KEY_Prod_sus=$sustituto_id, 1, 0) as seleccionado,DescProd as Descripcion,KEY_Kit_sus,KEY_Prod_sus as KEYProdServ,can_sus as Cantidad,'P' as 'TipoArt',
						$IDDetalle as IDDetalle
						FROM cat_productos_kit_relaciones 
						LEFT JOIN cat_productos_kits_sustitutos ON KEY_Kit_Pro_sus=ID_kit_rel 
						LEFT JOIN cat_productos  ON IDProd=KEY_Prod_sus
						WHERE KEY_Kit_rel=$idkit AND KEY_Prod_rel=$IdProd;";
						$componente['sugerencias']=$this->select( $sqlSustitos );
						$componente['impuestos']=array();
						
					}
					$arrComponentes[]=$componente;					
					// Obtener los sustitutos configurados para el kit					
				}					
				$arrDetalles[$i]['componentes'] = $arrComponentes;
			}
		}
		//--------------------------------------------------------------------------------------------------------------------------------
		
		
		
		//-----------------------------------------------------------------------------------------------
		//		Arreglo con los detalles de los impuestos relacionados con el concepto		
		//		IMPUESTOS QUEMADOS (IVA Y IEPS)
		//-----------------------------------------------------------------------------------------------
		
		for($i=0;$i<sizeof($arrDetalles);$i++){
			$SumPorImp=0;
			$SumIvaPesos=0;
			$ImpRetenidosPesos=0;
			$ImpRetenidosPor=0;
			$concepto=$arrDetalles[$i];
			$impuestos=array();	
			if (isset($concepto['PIvaTras'])){			
				$impuestos[]=array(
					'ImpTasa'	=>$concepto['PIvaTras'],
					'DescTasa'	=>'',
					'DescImp'	=>'I.V.A',
					'IDImp'		=>1,
					'selected'	=>1,
					'importe'	=>$concepto['IvaTras']
				);
				$SumPorImp+=$concepto['PIvaTras'];
				$SumIvaPesos+=$concepto['IvaTras'];
			}
			
			if ( isset($concepto['PIepsTras']) ){			
				$impuestos[]=array(
					'ImpTasa'	=>$concepto['PIepsTras'],
					'DescTasa'	=>'',
					'DescImp'	=>'I.E.P.S',
					'IDImp'		=>2,
					'selected'	=>1,
					'importe'	=>$concepto['IepsTras']
				);				
				$SumPorImp+=$concepto['PIepsTras'];
				$SumIvaPesos+=$concepto['IepsTras'];
			}
		
			if ( !is_null($concepto['PIsrRet']) ){			
				$impuestos[]=array(
					'ImpTasa'	=>$concepto['PIsrRet'],
					'DescTasa'	=>'',
					'DescImp'	=>'I.S.R',
					'IDImp'		=>3,
					'selected'	=>1,
					'importe'	=>$concepto['IsrRet']
				);								
				$ImpRetenidosPesos+=$concepto['IsrRet'];;
				$ImpRetenidosPor+=$concepto['PIsrRet'];
			}
			if ( !is_null($concepto['PIvaRet']) ){			
				$impuestos[]=array(
					'ImpTasa'	=>$concepto['PIvaRet'],
					'DescTasa'	=>'',
					'DescImp'	=>'I.V.A. RET',
					'IDImp'		=>4,
					'selected'	=>1,
					'importe'	=>$concepto['IvaRet']
				);			
				$ImpRetenidosPesos+=$concepto['IvaRet'];;
				$ImpRetenidosPor+=$concepto['PIvaRet'];				
			}
			$arrDetalles[$i]['SumPorImp']=$SumPorImp;
			$arrDetalles[$i]['IvaPesos']=$SumIvaPesos;
			$arrDetalles[$i]['ImpRetenidosPor']=$ImpRetenidosPor;
			$arrDetalles[$i]['ImpRetenidosPesos']=$ImpRetenidosPesos;
			$arrDetalles[$i]['impuestos']=$impuestos;
		
		}
        $datos['Detalles']=$arrDetalles;
		
        return $datos;
    }
	
    public function getById($IDFac,$temp=true,$leerComponentesDelKit=false) {
		$movimientoModel=new InventarioMovimientoModel();
		if ($temp){
			$tablaFac="tmp_facturacion";
			$tablaDet="tmp_facturacion_detalle";
			$tablaSub="tmp_facturacion_detalle_subconceptos";
			$tablaAduana="tmp_facturacion_detalle_aduana";
			$tipoEsquema='CFDiEmp';			
		}else{
			$tablaFac="facturacion";
			$tablaDet="facturacion_detalle";
			$tablaSub="facturacion_detalle_subconceptos";
			$tablaAduana="facturacion_detalle_aduana";
			$tipoEsquema="IF (UUIDFac='',0,1) as CFDiEmp";
		}
		
        $query="SELECT TipoCambio,TotImpTras, TipoFactura,NumCtaPago,TipDoc,TipComp,DATE_FORMAT(FecCan,'%d/%m/%Y') FecCan, $tipoEsquema,UUIDFac,DATE_FORMAT(FechaTimbrado,'%d/%m/%Y %H:%i:%S') as FechaTimbrado,RFCEmp,DATE_FORMAT(FechaFac,'%d/%m/%Y %H:%i:%S') as FechaFac,
			DATE_FORMAT(FecCan,'%d/%m/%Y %H:%i:%S') FechaCan,DATE_FORMAT(FechaVen,'%d/%m/%Y %H:%i:%S') as FechaVen,DiasCreFac DiasCreOrdVen,FormaPago,referencia,numParcialidad parcialidadA,totParcialidades parcialidadB,
			IF(DATE_FORMAT(FecPago,'%d/%m/%Y %H:%i:%S')='00/00/0000 00:00:00','',DATE_FORMAT(FecPago,'%d/%m/%Y %H:%i:%S')) as FecPago,
        	IDFac,e.IDEmp,IDSuc,SerFol,IDSerFol,Folio,estado StatusFac,status,IDRazSoc,RazSoc,NomComFac,RazSoc RazSocCliDet,RFCCliente RFCCliDet,NomCliente,MetPago,KEYMetPago,
        	CalleCliente CalleCliDet,NumExtCliente NumExtCliDet,NumIntCliente NumIntCliDet,ColCliente ColCliDet,MunCliente CiuCliDet,PaisCliente PaisCliDet,
        	EdoCliente EstCliDet,LocCliente as localidad,CPCliente CPCliente,IDCli,EmaRazSoc EmaCliDet
		FROM $tablaFac f 
		LEFT JOIN cat_empresas e ON e.IDEmp = f.IDEmp
		WHERE IDFac=$IDFac";       
        $arrresult=$this->query($query);
        
        if (sizeof($arrresult)==0){
        	throw new Exception("Error: No se encontró una factura con esos parámetros");
        }

       $datos=array();
       $datos['Factura']=$arrresult[0];     
       if (!$temp){       
			//--------------------------------------------------------------------
			//	Si esta factura es el pago de una parcialidad se procede a obtener los datos de la factura origen			
			
			if (!empty($datos['Factura']['parcialidadA'])){
				$idFac=$datos['Factura']['IDFac'];
				$sqlFacturaOrigen="SELECT IDFac IDFacOrigen,UUIDFac FolioFiscalOrig,SerFol SerieOrig,Folio FolioOrig,DATE_FORMAT(FechaFac,'%d/%m/%Y %H:%i:%s') as FechaFolioFiscalOrig,
				Total MontoFolioFiscalOrig FROM facturacion_parcialidades 
				LEFT JOIN facturacion ON KEY_Factura_Origen_Par=IDFac				
				WHERE KEY_Factura_Par=$idFac;";				
				$arrOrigen=$this->select($sqlFacturaOrigen);
				if (!empty($arrOrigen)){					
					$datos['Factura']=array_merge($arrOrigen[0],$datos['Factura']);
					$facOrigen=$datos['Factura']['IDFacOrigen'];					
					$sqlSaldo="SELECT SUM(Monto_Par) as sumParcialidades FROM facturacion_parcialidades
					LEFT JOIN facturacion ON IDFac=KEY_Factura_Par
					WHERE KEY_Factura_Origen_Par=$facOrigen AND status!='C';";

					$arrSaldo=$this->select($sqlSaldo);
					$saldo=floatval($arrOrigen[0]['MontoFolioFiscalOrig'])-floatval($arrSaldo[0]['sumParcialidades']);
					$datos['Factura']['saldo']=$saldo;
				}
			}			
		}
        $queryDetalles="SELECT IDDet IDDetalle,Unidad DescUni,predial,IDFac,Cant Cantidad,Descrip Descripcion,Detalle,TipoPYS TipoArt,Vunit PrecioU, Total,Descuento DescuentoPesos,
		DescuentoPor DescuentoPorcentaje,PIvaTras,PIepsTras,IvaTras,IepsTras,PIvaRet,IvaRet,PIsrRet,IsrRet,Importe,KEYConFacDet,KEYConFacDet as KEYProdServ, Seriado, 1 Edicion, Sku SKUs 
		FROM $tablaDet 
		LEFT JOIN cat_productos ON IDProd = KEYConFacDet 
		WHERE IDFac='$IDFac' AND KEYConDep_FacDet = 0 ORDER BY IDDet";
     
        $arrDetalles=$this->query($queryDetalles);
		
    	//-----------------------------------------------------------------------------------------------
		//		Arreglo con los detalles de los impuestos relacionados con el concepto		
		//		IMPUESTOS QUEMADOS (IVA Y IEPS)
		//-----------------------------------------------------------------------------------------------
		
		for($i=0;$i<sizeof($arrDetalles);$i++){			
			//--------------------------------------------------------
			$idDet=$arrDetalles[$i]['IDDetalle'];
			// $arrDetalles[$i]['SKUs'] = $movimientoModel->getDetallesSKUS($arrDetalles[$i]['KEYProdServ'], 164);
			//--------------------------------------------------------
			if($leerComponentesDelKit==true ){		//Cuando el sistema desea conocer los componentes del kit
				if ($arrDetalles[$i]['TipoArt']=='K'){	//Se verifica que este elemento sea un kit, en cuyo caso se obtendrán sus componentes
					
					$idKit=$arrDetalles[$i]['KEYConFacDet'];
					$queryComponentes="SELECT IDDet IDDetalle,Unidad DescUni,IDFac,Cant Cantidad,Descrip Descripcion,Detalle,TipoPYS TipoArt,Vunit PrecioU, Total,Descuento DescuentoPesos,
		DescuentoPor DescuentoPorcentaje,PIvaTras,PIepsTras,IvaTras,IepsTras,Importe,KEYConFacDet
		FROM $tablaDet WHERE IDFac='$IDFac' AND KEYConDep_FacDet =$idDet";

	     			$arrComponentes=$this->select($queryComponentes);

	     			if(! empty($arrComponentes) ){
	     				$arrDetalles[$i]['componentes'] = $arrComponentes;	
	     			}	     			
				}
	     	}
			//-------------------------------------------------------------------------------------
			//				Selecciono los subconceptos por cada detalle
			//-------------------------------------------------------------------------------------			
			$querySub="SELECT ConFacDetSub concepto,SubFacDetSub subconcepto,ImpFacDetSub,IDDetSub FROM $tablaSub WHERE KEYFacDet=$idDet ORDER BY IDDetSub";			
			$arrSubs=$this->query($querySub);
			if (sizeof($arrSubs)==0){
				$aduana='';	
			}else{
				$aduana=json_encode($arrSubs);	
			}			
			$arrDetalles[$i]['aduana']=$aduana;
			
			//-------------------------------------------------------------------------------------
			//				Selecciono los subconceptos por cada detalle
			//-------------------------------------------------------------------------------------
			$idDet=$arrDetalles[$i]['IDDetalle'];
			$queryAduana="SELECT NumAduana, DATE_FORMAT(FecAduana,'%d/%m/%Y') AS FecAduana,NomAduana FROM $tablaAduana WHERE KEYFacDetAdu=$idDet ORDER BY IDAdu";			
			$arrAduana=$this->query($queryAduana);

			if (sizeof($arrAduana)==0){
				$aduana='';	
			}else{
				$aduana=json_encode($arrAduana);	
			}			
			$arrDetalles[$i]['infoAduana']=$aduana;
			//----------------------------------------------------------------------------------
			
			$SumPorImp=0;
			$SumIvaPesos=0;
			$ImpRetenidosPesos=0;
			$ImpRetenidosPor=0;
			$concepto=$arrDetalles[$i];
			$impuestos=array();	
			if ($concepto['PIvaTras']!=0){			
				$impuestos[]=array(
					'ImpTasa'=>$concepto['PIvaTras'],
					'DescTasa'=>'',
					'DescImp'=>'I.V.A',
					'IDImp'=>1,
					'selected'=>1,
					'importe'=>$concepto['IvaTras']
				);
				$SumPorImp+=$concepto['PIvaTras'];
				$SumIvaPesos+=$concepto['IvaTras'];
			}
			
			if ($concepto['PIepsTras']!=0){			
				$impuestos[]=array(
					'ImpTasa'=>$concepto['PIepsTras'],
					'DescTasa'=>'',
					'DescImp'=>'I.E.P.S',
					'IDImp'=>2,
					'selected'=>1,
					'importe'=>$concepto['IepsTras']
				);				
				$SumPorImp+=$concepto['PIepsTras'];
				$SumIvaPesos+=$concepto['IepsTras'];
			}
		
			if ( !is_null($concepto['PIsrRet']) ){			
				$impuestos[]=array(
					'ImpTasa'=>$concepto['PIsrRet'],
					'DescTasa'=>'',
					'DescImp'=>'I.S.R',
					'IDImp'=>3,
					'selected'=>1,
					'importe'=>$concepto['IsrRet']
				);								
				$ImpRetenidosPesos+=$concepto['IsrRet'];;
				$ImpRetenidosPor+=$concepto['PIsrRet'];
			}
			if ( !is_null($concepto['PIvaRet']) ){			
				$impuestos[]=array(
					'ImpTasa'=>$concepto['PIvaRet'],
					'DescTasa'=>'',
					'DescImp'=>'I.V.A. RET',
					'IDImp'=>4,
					'selected'=>1,
					'importe'=>$concepto['IvaRet']
				);			
				$ImpRetenidosPesos+=$concepto['IvaRet'];;
				$ImpRetenidosPor+=$concepto['PIvaRet'];				
			}
			$arrDetalles[$i]['SumPorImp']=$SumPorImp;
			$arrDetalles[$i]['IvaPesos']=$SumIvaPesos;
			$arrDetalles[$i]['ImpRetenidosPor']=$ImpRetenidosPor;
			$arrDetalles[$i]['ImpRetenidosPesos']=$ImpRetenidosPesos;
			$arrDetalles[$i]['impuestos']=$impuestos;
		}
		/* Ahora por cada detalle, si es un kit, se obtienen sus componentes, y luego por cada componente, su sustituto*/		
		for($i=0; $i<sizeof($arrDetalles); $i++){
			if( $arrDetalles[$i]['TipoArt']=='K' ){	
				//Traer los remplazos seleccionados
				$kit_id=$arrDetalles[$i]['IDDetalle'];
				//Sustitutos seleccionados
				$sqlSustitutosDelKit="SELECT IDDet IDDetalle, Unidad DescUni, predial,IDFac, Cant Cantidad, Descrip Descripcion, Detalle, TipoPYS TipoArt, Vunit PrecioU,
				Total, Descuento DescuentoPesos, DescuentoPor DescuentoPorcentaje, PIvaTras, PIepsTras, IvaTras, IepsTras, PIvaRet, IvaRet,
				PIsrRet, IsrRet, Importe, KEYConFacDet, KEYConFacDet as KEYProdServ,remplazado_id 
				from $tablaDet
				 where KEYConDep_FacDet= $kit_id;";
				
				$arrSustitutos=$this->select($sqlSustitutosDelKit);
				
				
				//remplazado_id contiene la informacion del producto remplazado
				$arrComponentes=array();
				$idkit= $arrDetalles[$i]['KEYProdServ'];				
				
				for($y=0; $y<sizeof($arrSustitutos); $y++){
					//cuando remplazado_id == KEYProdServ siginifica que componente ha sido remplazado por si mismo.
					//Entonces el componente siempre tiene un remplazo, esto simplifica las condiciones al programar.
					
					$IdProd			=$arrSustitutos[$y]['remplazado_id'];
					$sustituto_id	=$arrSustitutos[$y]['KEYProdServ'];
					
					/*=============================================================================================================					
					Se compara la fecha de la factura y la fecha de modificacion del kit
					si la fecha de modificacon del kit es mayor a la fecha de la factura, los remplazos se obtienen de
					la factura, (solo se obtiene 1 remplazo a lo mucho), se impide la modificacin del kit
																																*/
					$sqlKitDate="SELECT ModFecha FROM cat_productos_kits WHERE ID_kit=".$arrDetalles[$i]['KEYProdServ'];
					$arrDate=$this->select($sqlKitDate);							
					if ( empty($arrDate) ) throw new Exception("No encontré la fecha de modificaión del kit");
					
					$sqlVentaDate="SELECT FechaFac FROM $tablaFac WHERE IDFac=".$datos['Factura']['IDFac'];
					$arrVentaDate=$this->select( $sqlVentaDate );							
					if ( empty($arrVentaDate) ) throw new Exception("No encontré la fecha de creación de la Factura");					
					//=============================================================================================================					
					if ( $IdProd == $sustituto_id ){				
						//----------------------------------------------------------------
						//Impedir modificar los componentes o sustitutos del kit
						if( $arrDate[0]['ModFecha'] > $arrVentaDate[0]['FechaFac'] ){
							$arrSustitutos[$y]['bloqueado']=true;
						}
						//----------------------------------------------------------------
						//Ahora obtendré los sustitutos:					

						$arrSustitutos[$y]['seleccionado']=true;
						$componente=$arrSustitutos[ $y ];
					}else{						
						//Obtener los datos del producto (componente del kit)
						if ( $datos['Factura']['IDSuc']=='0' ){
							$OrigenTaR	 ='E';
							$KEYOrigenTaR=$datos['Factura']['IDEmp'];
						}else{
							$OrigenTaR='S';
							$KEYOrigenTaR=$datos['Factura']['IDSuc'];
						}
						
						$tipoDocumento=$datos['Factura']['TipDoc'];	
												
						$sqlComponente="SELECT DescProd ,can_Prod_rel as Cantidad 
						FROM cat_productos_kit_relaciones 
						LEFT JOIN cat_productos ON IDProd=KEY_Prod_rel 
						WHERE KEY_Kit_rel=$idkit AND KEY_Prod_rel=$IdProd";			
						
						$arrCmp=$this->select( $sqlComponente );
						
						$componente=array(
							'IDDetalle'		=>$arrSustitutos[$y]['IDDetalle'],
							'TipoArt'		=>'P',
							'KEYProdServ'	=>$IdProd,
							'Cantidad'		=>$arrCmp[0]['Cantidad'],
							'Descripcion'	=>$arrCmp[0]['DescProd'],
							'Subtotal'		=>0
							//,'bloqueado'		=>true
						);					
						//$arrDetalles[$i]['bloqueado']=true;
					}
					unset( $arrSustitutos[$y]['remplazado_id'] );
					/* Obtener los remplazos configuradas para este producto en el kit */
					$IDDetalle	=$arrSustitutos[$y]['IDDetalle'];				
					
					/* 	Ahora obtendré los sustitutos	*/
					
					if( $arrDate[0]['ModFecha'] > $arrVentaDate[0]['FechaFac'] ){
						$componente['sugerencias']=array(
							array(
								'seleccionado'=>true,
								'Descripcion' =>$arrSustitutos[$y]['Descripcion'],
								'KEY_Kit_sus' =>$idkit,
								'KEYProdServ' =>$arrSustitutos[$y]['KEYProdServ'],
								'Cantidad'	  =>$arrSustitutos[$y]['Cantidad'],
								'TipoArt'	  =>'P',
								'IDDetalle'	  =>$arrSustitutos[$y]['IDDetalle']
							)						
						);
						$componente['bloqueado']=true;
					}else{
						$sqlSustitos="SELECT if(KEY_Prod_sus=$sustituto_id, 1, 0) as seleccionado,DescProd as Descripcion,KEY_Kit_sus,KEY_Prod_sus as KEYProdServ,can_sus as Cantidad,'P' as 'TipoArt',
						$IDDetalle as IDDetalle
						FROM cat_productos_kit_relaciones 
						LEFT JOIN cat_productos_kits_sustitutos ON KEY_Kit_Pro_sus=ID_kit_rel 
						LEFT JOIN cat_productos  ON IDProd=KEY_Prod_sus
						WHERE KEY_Kit_rel=$idkit AND KEY_Prod_rel=$IdProd;";
						$componente['sugerencias']=$this->select( $sqlSustitos );
					}
					$arrComponentes[]=$componente;					
					// Obtener los sustitutos configurados para el kit					
				}
				$arrDetalles[$i]['componentes'] = $arrComponentes;
			}
		}
		//--------------------------------------------------------------------------------------------------------------------------------
        $datos['Detalles']=$arrDetalles;	 
		
        return $datos;
    }
	
	private function guardarConceptos($IDFac, $moviendoConceptos=false, $conceptos_a_guardar=array(), $KEYConDep_FacDet=0){        		
        $conceptos = (	empty($conceptos_a_guardar) ) ? $this->conceptos : $conceptos_a_guardar ;

        $updates = '';
        $inserts = '';
        $deletes = '';
        $updates = array();
		//------------------------------------- IMPUESTOS DEL CONCEPTO--------------------------------------
		if (!$moviendoConceptos){		
			for($i=0;$i<sizeof($conceptos);$i++){	
				$conceptos[$i]['IvaPesos']='NULL';
				$conceptos[$i]['IvaPor']='NULL';
				$conceptos[$i]['PIepsTras']='NULL';
				$conceptos[$i]['IepsTras']='NULL';
				$conceptos[$i]['PIsrRet']='NULL';
				$conceptos[$i]['IsrRet']='NULL';
				$conceptos[$i]['IvaRet']='NULL';
				$conceptos[$i]['PIvaRet']='NULL';
				
				$concepto=$conceptos[$i];			
				$cantidad=$concepto['Cantidad'];
				$precio=(!isset($concepto['PrecioU']) )? 0 : $concepto['PrecioU'];
				$descuento=$concepto['DescuentoPesos'];
				$subtotal=$cantidad*$precio-$descuento;			
				
				$impuestos=empty($concepto['impuestos'])? array(): $concepto['impuestos'];
				if (!isset($concepto['impuestos'])){
					throw new Exception("El servidor esperaba el parametro impuestos");
				}

				foreach($impuestos as $impuesto){
					$impuestoPorcentaje=$impuesto['ImpTasa'];			
					$impuestoPesos=$impuesto['importe'];
					
					switch($impuesto['IDImp']){
						case 1://IVA							
							if ($impuesto['selected']=='1'){
								$conceptos[$i]['IvaPesos']=$impuestoPesos;
								$conceptos[$i]['IvaPor']=$impuestoPorcentaje;
							}else{
								$conceptos[$i]['IvaPesos']='NULL';
								$conceptos[$i]['IvaPor']='NULL';
							}
						break;	
						case 2://IEPS
							if ($impuesto['selected']=='1'){
								$conceptos[$i]['IepsTras']=$impuestoPesos;
								$conceptos[$i]['PIepsTras']=$impuestoPorcentaje;
							}else{
								$conceptos[$i]['PIepsTras']='NULL';
								$conceptos[$i]['IepsTras']='NULL';
							}							
						break;
						case 3://ISR RETENIDO							
							if ($impuesto['selected']=='1'){
								$conceptos[$i]['PIsrRet']=$impuestoPorcentaje;
								$conceptos[$i]['IsrRet']=$impuestoPesos;
							}else{
								$conceptos[$i]['IsrRet']='NULL';
								$conceptos[$i]['PIsrRet']='NULL';
							}							
						break;
						case 4://IVA RETENIDO
							if ($impuesto['selected']=='1'){
								$conceptos[$i]['IvaRet']=$impuestoPesos;
								$conceptos[$i]['PIvaRet']=$impuestoPorcentaje;
							}else{
								$conceptos[$i]['IvaRet']='NULL';
								$conceptos[$i]['PIvaRet']='NULL';
							}							
						break;
					}				
				}
			}			
		}
		
		if ($moviendoConceptos){	//Al pasar de la tabla temporal a la tabla permanente			
			for($i=0; $i<sizeof($conceptos); $i++){
			//SELECCIONO LOS SUBCONCEPTOS DEL CONCEPTO Y LOS CONVIERTO EN JSON
				$idDetalle=$conceptos[$i]['IDDetalleOLD'];
				$querySub="SELECT ConFacDetSub concepto,SubFacDetSub subconcepto,ImpFacDetSub,0 IDDetSub FROM tmp_facturacion_detalle_subconceptos WHERE KEYFacDet=$idDetalle ORDER BY IDDetSub";	
				$arrSubdetalles=$this->query($querySub);				
				$aduana=json_encode($arrSubdetalles);
				$conceptos[$i]['aduana']=$aduana;
			//TAMBIEN LOS DATOS DE LA ADUANA						
				$queryAduana="SELECT NumAduana,DATE_FORMAT(FecAduana,'%d/%m/%Y') as FecAduana,NomAduana,0 IDAdu FROM tmp_facturacion_detalle_aduana WHERE KEYFacDetAdu=$idDetalle ORDER BY IDAdu";	
				$arrAduana=$this->query($queryAduana);		
						
				$infoAduana=json_encode($arrAduana);
				$conceptos[$i]['infoAduana']=$infoAduana;	
			//TAMBIEN LOS COMPONENTES DEL KIT
			
				if ($conceptos[$i]['TipoArt']=='K'){	//Se verifica que este elemento sea un kit, en cuyo caso se obtendrán sus componentes					
					
					$queryComponentes="SELECT 0 IDDetalle,Unidad DescUni,predial,IDFac,Cant Cantidad,Descrip Descripcion,Detalle,TipoPYS TipoArt,Vunit PrecioU, Total,Descuento DescuentoPesos,
					DescuentoPor DescuentoPorcentaje,PIvaTras IvaPor,PIepsTras,IvaTras as IvaPesos,IepsTras,KEYConFacDet KEYProdServ,Importe Subtotal,remplazado_id
					FROM tmp_facturacion_detalle WHERE KEYConDep_FacDet=$idDetalle  ORDER BY IDDetalle";			            	
				            	
	     			$arrComponentes=$this->select($queryComponentes);

	     			if(! empty($arrComponentes) ){						
	     				$conceptos[$i]['componentes'] = $arrComponentes;	
	     			}	     			
				}
	     	
			}
		}

		//-------------------------------------GUARDAR
        foreach ($conceptos  as $concepto) {

            if (empty($concepto['IDDetalle']) ) {
            	//Por cada concepto puede existir una N cantidad de subconceptos de aduana
            	//asi que cada vez que guardo un concepto, guardo todos sus subconceptos

            	$TipoPYS=$concepto['TipoArt'];
            	$KEYConFacDet=$concepto['KEYProdServ'];            	
            	$Descrip=addslashes($concepto['Descripcion']);
            	$Detalle=addslashes($concepto['Detalle']);
            	$Cant=$concepto['Cantidad'];
            	$Unidad=$concepto['DescUni'];
            	$Vunit=$concepto['PrecioU'];
            	$Importe=$concepto['Subtotal'];
            	$Total=$concepto['Total'];
            	$Descuento=$concepto['DescuentoPesos'];
            	$DescuentoPor=$concepto['DescuentoPorcentaje'];
            	$IvaTras=$concepto['IvaPesos'];
            	$PIvaTras=$concepto['IvaPor'];
            	$PIepsTras=$concepto['PIepsTras'];
            	$IepsTras=$concepto['IepsTras'];            	
            	$IsrRet=$concepto['IsrRet'];
            	$PIsrRet=$concepto['PIsrRet'];
            	$IvaRet=$concepto['IvaRet'];
            	$PIvaRet=$concepto['PIvaRet'];
				$SKUs=$concepto['SKUs']; // ANTONIO
				
				$remplazado_id=( empty($concepto['remplazado_id']) )? 0 : $concepto['remplazado_id'];
            	$predial="";
				if ( isset($concepto['predial']) ){
					$predial="predial='".$concepto['predial']."',";
				}
            	//$KEYConDep_FacDet=empty($concepto['KEYConDep_FacDet'])? 0 : $concepto['KEYConDep_FacDet'] ;

            	$queryInsert="INSERT INTO $this->detalleTable SET $predial IDFac=$IDFac,TipoPYS='$TipoPYS',KEYConFacDet='$KEYConFacDet', Descrip='$Descrip',Detalle='$Detalle',
            	Cant='$Cant',Unidad='$Unidad',Vunit='$Vunit',Importe='$Importe',Total='$Total',Descuento='$Descuento',DescuentoPor='$DescuentoPor',remplazado_id='$remplazado_id',
            	IvaTras=$IvaTras,PIvaTras=$PIvaTras,PIepsTras=$PIepsTras,IepsTras=$IepsTras, KEYConDep_FacDet=$KEYConDep_FacDet,IsrRet=$IsrRet,PIsrRet=$PIsrRet,IvaRet=$IvaRet,PIvaRet=$PIvaRet,Sku='$SKUs';";

            	$IDDetalle= $this->insert($queryInsert); //<----------------INSERT DETALLE 

				$idAlmacen = empty($_SESSION['Auth']['Almacen'])? 0 : $_SESSION['Auth']['Almacen']['IDAlmacen'];
				// $kardex = new InventariosKardexModel(); // ANTONIO DIAZ: SERIES...
				// $consulta = $this->select("SELECT IDInventarioDet FROM inventarios_movimientos_detalle WHERE ReferenciaInventarioDet = '".$this->referencia."'");
				// $kardex->guardaSKUs(false, $idKardex, $KEYConFacDet, $SKUs, $idAlmacen);
            	
            	//-----------------------------------------------------------------------
            	switch ($this->detalleTable){
            		case 'tmp_facturacion_detalle':
            			$this->subconceptoTable="tmp_facturacion_detalle_subconceptos";
            			$this->aduanaTable="tmp_facturacion_detalle_aduana";
            		break;
            		case 'facturacion_detalle':
            			$this->subconceptoTable="facturacion_detalle_subconceptos";
            			$this->aduanaTable="facturacion_detalle_aduana";
            		break;
            		default:
            			throw new Exception("Tabla de detalles desconocida");
            	}
            	//--------------------------------------------------------------------------
           		if (! empty($concepto['aduana']) ){	

            		$aduana=json_decode($concepto['aduana'],true);
            		foreach($aduana as $itemAd){            			
            			$itemConcepto=addslashes($itemAd['concepto']);
            			$pedimento=addslashes($itemAd['subconcepto']);
            			$importe=is_numeric($itemAd['ImpFacDetSub']) ? $itemAd['ImpFacDetSub'] : 'NULL' ;
            			$insertSubconcepto="INSERT INTO $this->subconceptoTable SET KEYFacDet=$IDDetalle,ConFacDetSub='$itemConcepto',SubFacDetSub='$pedimento',ImpFacDetSub=$importe";

            			$this->insert($insertSubconcepto);            			
            		}	
           		}	
            		//----------------------------------------------------------------------
            	if (! empty($concepto['infoAduana']) ){	
            		$infoAduana=json_decode($concepto['infoAduana'],true);   
            		if (sizeof($infoAduana)>0){    		
	            		foreach($infoAduana as $itemAd){            			
	            			$NumAduana=addslashes($itemAd['NumAduana']);
	            			$FecAduana=$this->jsDateToMysql($itemAd['FecAduana']);
	            			$NomAduana=$itemAd['NomAduana'];
	            			$insertAduana="INSERT INTO $this->aduanaTable SET KEYFacDetAdu=$IDDetalle,NumAduana='$NumAduana',FecAduana='$FecAduana',NomAduana='$NomAduana'";
	            			$this->insert($insertAduana);
	            			
	            		}
            		}
            	}
            		//----------------------------------------------------------------------
            		 
            		if ( !empty($concepto['componentes']) ){
            			if ($moviendoConceptos){
            				$componentes=$concepto['componentes'];        
	            			foreach($componentes as $componente){            			
		            			
		            			$TipoPYS=$componente['TipoArt'];
				            	$KEYConFacDet=$componente['KEYProdServ'];            	
				            	$Descrip=addslashes($componente['Descripcion']);
				            	$Detalle=addslashes($componente['Detalle']);
				            	$Cant=$componente['Cantidad'];
				            	$Unidad=$componente['DescUni'];
				            	$Vunit=$componente['PrecioU'];
				            	$Importe=$componente['Subtotal'];
				            	$Total=$componente['Total'];
				            	$Descuento=$componente['DescuentoPesos'];
				            	$DescuentoPor=$componente['DescuentoPorcentaje'];
				            	$IvaTras=$componente['IvaPesos'];
				            	$PIvaTras=$componente['IvaPor'];
				            	$PIepsTras=$componente['PIepsTras'];
				            	$IepsTras=$componente['IepsTras'];
								$remplazado_id=empty($componente['remplazado_id'])? 0 : $componente['remplazado_id'];
				            	//$KEYConDep_FacDet=$IDDetalle;
				
				            	$sqlInsertComponente="INSERT INTO $this->detalleTable SET IDFac=$IDFac,TipoPYS='$TipoPYS',KEYConFacDet='$KEYConFacDet', Descrip='$Descrip',Detalle='$Detalle',
				            	Cant='$Cant',Unidad='$Unidad',Vunit='$Vunit',Importe='$Importe',Total='$Total',Descuento='$Descuento',DescuentoPor='$DescuentoPor',remplazado_id=$remplazado_id,
				            	IvaTras='$IvaTras',PIvaTras='$PIvaTras',PIepsTras='$PIepsTras',IepsTras='$IepsTras', KEYConDep_FacDet=$IDDetalle;";           			
		            			//	    	echo $sqlInsertComponente;      				            	
		            			$this->insert($sqlInsertComponente);	            			
		            		}
            			}else{
            				$componentes=$concepto['componentes'];      
							
							if ( is_string($componentes) ){
								$componentes=json_decode($componentes, true);							
							}
							for($iC=0; $iC<sizeof($componentes); $iC++){
								
								if ( !isset($componentes[$iC]['impuestos']) ){
									$componentes[$iC]['impuestos']=array();
								}
								for($iI=0; $iI<sizeof($componentes[$iC]['impuestos']); $iI++){
									$componentes[$iC]['impuestos'][$iI]['importe']=0;
								}								
							}
            				$this->guardarConceptos( $IDFac,false,$componentes,$IDDetalle );	
            			}            				
            		}            	           
            } 
        }

    }

	
	public function getInitialInfo($empresaId,$sucursalId){		
		$query="SELECT DATE_FORMAT(now(),'%d/%m/%Y %H:%i:%S') as FechaFac,CFDiEmp FROM cat_empresas WHERE IDEmp=$empresaId";		
		$arrResult=$this->query($query);
		$arrResult[0]['IDSuc'] = $sucursalId;
		$arrResult[0]['StatusFac'] = '1';
		$arrResult[0]['IDFac'] = 0;
		$arrResult[0]['IDEmp'] = $empresaId;
        return $arrResult[0];		
	}
	
	public function getSeriesYFolios($empresaId,$sucursalId){
		$query="SELECT IDFol,SerieFol,SigFol,FinalFol 
			FROM cat_folios 
			WHERE KEYEmpFol=$empresaId AND KEYSucFol=$sucursalId AND SigFol<=FinalFol AND StatusFol='A'   
			ORDER BY PredetFol DESC,SerieFol,SigFol ASC";
		$arrRes=$this->select($query);	
			
		$folios=array();
		$series=array();
		foreach($arrRes as $folio){			
			if ( !in_array($folio['SerieFol'], $series) ){
				$series[]=$folio['SerieFol'];
				$folios[]=	$folio;
			}			
		}
		return $folios;
    }
	
	//-------------------------------------------------------------------------
	//   	   ----deveria llamarse getDatosParaUbicarFactura----
	//	Regresa los datos necesarios para encontrar la ubicacion del pdf
	//	IDEmpresa,IDSucursal,RFC del cliente, Folio y Serie, 
	//-------------------------------------------------------------------------
	public function getDatosDeLaFactura($IDFac, $IDUsr, $tipoUsu, $modoPrueba){
		if ($modoPrueba){
			$tabla='tmp_facturacion';
		}else{
			$tabla='facturacion';
		}

		$query="SELECT IF (UUIDFac='',0,1)as CFDiEmp,RFCEmisor as RFCEmp,SerFol,Folio,DATE_FORMAT(FechaFac,'%d/%m/%Y %H:%i:%S') as FechaFac";
		$query.=" FROM $tabla f ";
		$query.=" WHERE IDFac=$IDFac";

		$arrRes=$this->query($query);
		if (sizeof($arrRes)==0){
			return false;
		}
		return array ('Factura'=>$arrRes[0]);		
	}
	

	public function borrarTemporal($IDTempFac){
		
		$queryDeleteFactura="DELETE FROM tmp_facturacion WHERE IDFac=$IDTempFac";	//Se borra la factura recien movida
		
		$query="SELECT IDDet FROM tmp_facturacion_detalle WHERE IDFac=$IDTempFac";	//Selecciono el id de los detalles para borrar sus relaciones
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal
		
		$numConceptos=sizeof($detalles);
		for($i=0;$i<$numConceptos;$i++){
			$IDDetalleOLD=$detalles[$i]['IDDet'];
			$queryDeleteSubconceptos="DELETE FROM tmp_facturacion_detalle_subconceptos WHERE KEYFacDet=$IDDetalleOLD";
			$this->queryDelete($queryDeleteSubconceptos);

			$queryDeleteAduana="DELETE FROM tmp_facturacion_detalle_aduana WHERE KEYFacDetAdu=$IDDetalleOLD";
			$this->queryDelete($queryDeleteAduana);						
		}

		$this->queryDelete($queryDeleteFactura);
		$queryDeleteDetalles="DELETE FROM tmp_facturacion_detalle WHERE IDFac= $IDTempFac;";
		$this->queryDelete($queryDeleteDetalles);
		return true;
	}
	
	//----------------------------------------------------
	//			Mueve la factura de la tabla temporal a la permanente
	//----------------------------------------------------
	public function moverFactura($params){

		$modoDeFacturacion=$params['modo']; //CFD o CFDI
		$IDTempFac=$params['IDFac'];
		
		$query="SELECT * FROM tmp_facturacion T WHERE T.IDFac=$IDTempFac";
		
		$arrFac=$this->query($query);
		
		unset($arrFac[0]['IDFac']);
		
		switch($modoDeFacturacion){
			case 'CFD':
				$SD=$params['SD'];
				$CadOri=$params['CadOri'];
				$arrFac[0]['SD']=$SD;
				$arrFac[0]['CadOri']=$CadOri;
				break;
			case 'CFDI':							
				$arrFac[0]['SD']=$params['SD'];
				$arrFac[0]['UUIDFac']=$params['UUIDFac']; //FOLIO FISCAL:
				$arrFac[0]['SelloCFD']=$params['SelloCFD'];//SELLO DIGITAL DEL CFDI
				$arrFac[0]['SelloSAT']=$params['SelloSAT'];//SELLO DEL SAT
				$arrFac[0]['NumCertSAT']=$params['NumCertSAT']; //CERTIFICADO SAT
				$arrFac[0]['FechaTimbrado']=$params['FechaTimbrado'];
				$arrFac[0]['CadOri']=$params['CadOri'];		//CADENA ORIGINAL DEL TIMBRE FISCAL DIGITAL DEL SAT
				break;
			default:
				throw new Exception("Error al aceptar la factura: Modo de facturación no especificado");	
		}
		$arrFac[0]['xml_origen']=$params['xml_origen'];
				
		//------------------------------------------------------------
		//			Se copian los datos de tmp a permanente
		//------------------------------------------------------------
		$queryInsert="INSERT INTO facturacion SET ";
		 foreach($arrFac[0] as $key=>$value){
            if ($value!=''){
                $queryInsert.="$key='".addslashes($value)."',";
            }
        }
        $queryInsert=substr($queryInsert, 0,strlen($queryInsert)-1);                   
		$IDFac=$this->insert($queryInsert);	//				<---INSERT
	
		//$this->update($query);	//la funcion UPDATE solo regresa TRUE O FALSE, (o Affected Rows si ya actualizé la funcion)  
		$query="DELETE FROM tmp_facturacion WHERE IDFac=$IDTempFac";	//Se borra la factura recien movida
		$this->queryDelete($query);
		//------------------------------------------------------------------------------------------------------------
		//			AHORA ACTULIZO LOS DETALLES
		//	(ESTO ES COMO MOVER DE LA TABLA TEMPORAL A LA TABLA PERMANENTE PERO ES NECESARIO MODIFICAR EL IDFac)
		//------------------------------------------------------------------------------------------------------------		
		$query="SELECT KEYConDep_FacDet,IDDet IDDetalleOLD,predial,0 IDDetalle,KEYConFacDet as KEYProdServ, TipoPYS TipoArt,KEYConFacDet,Descrip Descripcion,
		Detalle,Cant Cantidad,Unidad DescUni,Vunit PrecioU,Importe Subtotal, Total,Descuento DescuentoPesos,DescuentoPor DescuentoPorcentaje,
		ifnull(IvaTras,'null') as IvaPesos,ifnull(PIvaTras,'null') as IvaPor,ifnull(PIepsTras,'null') as PIepsTras,ifnull(IepsTras,'null') as IepsTras,ifnull(PIvaRet,'null') PIvaRet,ifnull(IvaRet,'null') IvaRet,ifnull(PIsrRet,'null') PIsrRet,ifnull(IsrRet,'null') IsrRet, Sku as SKUs";
		$query.=" FROM tmp_facturacion_detalle WHERE IDFac=$IDTempFac AND KEYConDep_FacDet=0 ORDER BY IDDet";
		
		$detalles=$this->query($query);	//<--Lee los detalles de la tabla temporal		

		$this->conceptos=$detalles;
		
		$this->detalleTable="facturacion_detalle";
		
		//-------------------------------------------------------------------------------------------------------------
		//LA FUNCION guardarConceptos se encarga de obtener los subconceptos para cada concepto
		//-------------------------------------------------------------------------------------------------------------	
		$obtenerRelacionados=true;	
		$this->guardarConceptos($IDFac,$obtenerRelacionados);	//GUARDO LOS DETALLES
		
		$queryDelTempDetalles="DELETE FROM tmp_facturacion_detalle WHERE IDFac=$IDTempFac";
		$this->queryDelete($queryDelTempDetalles);
		
		$numConceptos=sizeof($detalles);
		for($i=0;$i<$numConceptos;$i++){
			$IDDetalleOLD=$detalles[$i]['IDDetalleOLD'];
			$queryDeleteSubconceptos="DELETE FROM tmp_facturacion_detalle_subconceptos WHERE KEYFacDet=$IDDetalleOLD";
			$this->queryDelete($queryDeleteSubconceptos);
			$queryDeleteAduana="DELETE FROM tmp_facturacion_detalle_aduana WHERE KEYFacDetAdu=$IDDetalleOLD";
			$this->queryDelete($queryDeleteAduana);						
		}
		//-----------------------------------------------------
		// Cuando la factura es el pago de una parcialidad, se agrega un registro a la tabla facturacion_parcialidades		
		
		if (isset($this->parcialidades)){						
			if ($this->parcialidades){
				
				$monto=$arrFac[0]['Total'];
				$origen=$this->paramsOrigen['IDFacOrigen'];
				$sqlParcialidad="INSERT INTO facturacion_parcialidades SET Monto_Par=$monto,KEY_Factura_Par=$IDFac,KEY_Factura_Origen_Par=$origen;";
				$this->insert($sqlParcialidad);
				//Tambien se actualiza en la factura origen, el numero de parcialidad
				$numParcialidad=$arrFac[0]['numParcialidad'];
				$totParcialidades=$arrFac[0]['totParcialidades'];
				if( isset($this->facturaSaldada) ){
					$fechaPago=' ,FecPago="'.$arrFac[0]['FechaFac'].'"';
				}else{
					$fechaPago='';
				}
				
				$sql="UPDATE facturacion SET numParcialidad=$numParcialidad,totParcialidades=$totParcialidades $fechaPago WHERE IDFac=$origen;";
				$this->update($sql);
			}			
		}
		$factura= $this->getById($IDFac,false,true);

		$this->gastarFolio( $factura['Factura']['IDSerFol'], $factura['Factura']['Folio'] );
		
		
		
		if ( isset($pagoParams) ){
			$query='UPDATE  facturacion set FecPago="'.$datos['FechaFac'].'" WHERE IDFac='.$origen;		
			$this->update($query);
		}
		return $factura;
        
	}
	
	function cancelarFactura($IDFac){
		$queryUpdate="UPDATE facturacion SET estado=0,status='C',FecCan=now() WHERE IDFac=$IDFac";
		$this->update($queryUpdate);		
		return true;
	}
	
	function relacionarOrdenesConFactura($IDFac,$IDEmp,$folios){
		$foliosSQL='';
		foreach($folios as $folio){
				$foliosSQL.='FolOrdVen='.$folio['FolOrdVen'].' OR ';
		}
		$foliosSQL=substr (  $foliosSQL,0,strlen($foliosSQL)-4);
		$queryUpdate="UPDATE orden_venta SET KEYFacOrdVen=$IDFac WHERE KEYEmpOrdVen=$IDEmp AND ($foliosSQL)";
		$this->update($queryUpdate);		
		return true;
	}
	
	function paraPrepararMail($facturasStr){
		$facturas=explode(',',$facturasStr);
		
		$facturasSQL='';	
			
		for($i=0;$i<sizeof($facturas);$i++){
			$facturasSQL.=" OR IDFac=".$facturas[$i];			
		}
		
		if (strlen($facturasSQL)>0){
			$facturasSQL=substr($facturasSQL,3);
		}else{
			throw new Exception("Necesita especificar las facturas a buscar");
		}	
		
		$queryDatos="SELECT 
			if (EmaConCorCli='',EmaRazSoc,EmaConCorCli) as EmaConCorCli, TipDoc,
			IDFac,Folio,SerFol,UUIDFac,f.IDEmp,f.IDSuc,IDRazSoc,f.IDCli,EmaRazSoc EmaCliDet,MailConEmp,MailConSuc,
			NomEmisor nomComEmi,RazSoc as nomComOrSocRec ,EmaRazSoc,
			if (f.IDSuc=0,NomEmisor,NomSuc) as NomEmisor,
			SerFol,DATE_FORMAT(FechaTimbrado,'%d/%m/%Y %H:%i:%s') as FechaTimbrado,TotImpTras,Total
			FROM facturacion f			
			LEFT JOIN cat_clientes c ON  c.IDCli=f.IDCli
			LEFT JOIN cat_empresas e ON e.IDEmp=f.IDEmp
			LEFT JOIN cat_sucursales s ON s.IDSuc=f.IDSuc WHERE $facturasSQL";		

		$arrRes= $this->query($queryDatos);
		return $arrRes;
	}
	
	function pagar($params){
		$IDFac=$params['IDFac'];
		$fechaPago=$this->jsDateToMysql($params['fechaPago']);
		$queryFecPago="SELECT FecPago FROM facturacion WHERE IDFac=$IDFac";
		$arrFecPago=$this->query($queryFecPago);
		if ($arrFecPago[0]['FecPago']=='0000-00-00 00:00:00' || $arrFecPago[0]['FecPago']==''){			
			$queryPagar="UPDATE facturacion set status='P', FecPago ='$fechaPago' WHERE IDFac=$IDFac;";
			$this->update($queryPagar); 
		}else{
			throw new Exception('La factura ya ha sido pagada'.$arrFecPago[0]['FecPago']);
		}

		return $params['fechaPago'];

	}
	
	function validarLicencia($rfc){
		try{
			$sql="SELECT PAC_Test, DB_Corporativo, KEY_EMP_Corporativo FROM licencias WHERE rfc = '$rfc' AND PAC = 1;";
			$reg =$this->query($sql,'db_mifactura');	
		}catch(Exception $e){
			throw new Exception("Error: 109 Error al contactar con el servicio de licencias");	
		}
		
		if (sizeof($reg )<=0){
			throw new Exception(utf8_decode("Error: 101 No tienes Licencia para utilizar este servicio"));
		}
		//Se agrego para validar que el cliente tenga el contrato de edicom de lo contrario no lo dejara facturar. jrhc 15-02-2014
		try{
			$sql="SELECT PAC_Test, DB_Corporativo, KEY_EMP_Corporativo FROM licencias WHERE rfc = '$rfc' AND PAC = 1 AND ContratoEdicom = 1;";
			$regB =$this->query($sql,'db_mifactura');	
		}catch(Exception $e){
			throw new Exception("Error: 109 Error al contactar con el servicio de licencias");	
		}
		
		if (sizeof($regB )<=0){
			throw new Exception(utf8_decode("Error: 101 Su Servicio ha sido suspendido temporalmente por falta de contrato, favor de contactar al area de soporte de UPC Technologies"));
		}
		//fin modificacion jrhc 15-02-2014	
		
		//$reg = mysql_fetch_array($res);
		$params=array();
		$params['modoPrueba']      = $reg[0]['PAC_Test'];    //  <--  Modo de prueba
		$params['db_corporativo']  = $reg[0]['DB_Corporativo'];
		$params['emp_corporativo'] = $reg[0]['KEY_EMP_Corporativo'];
		
		return $params;
	}
	function imprimirFacturas(){
		throw new Exception("Todavia no se implementa");
	}
    
}
?>