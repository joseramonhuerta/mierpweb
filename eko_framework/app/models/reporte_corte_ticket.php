<?php
/*
 *	Esta clase deberia analizar y preparar toda la informacion, dejarla lista para que el PDF imprima usando solamente ciclos
 * por el momento hay logica repartida en esta clase y en reporte_de_facturacion_pdf.php
 */
class ReporteCorteTicket{
	function generarReporte($params,$formatos){
		
		$datos=$this->obtenerDatos($params);					//Obtiene los datos de la base de datos
		if (empty($datos) ){
			throw new Exception("No hay datos que mostrar");
		}		
		// $preparados=$this->prepararParaImpresion($datos,$params);		//analiza los datos y los agrupa listos para imprimirlos en el PDF
		
		
		
		$pdf=$this->crearReporte($datos,$formatos);		//Crea el pdf
		return $pdf; 
	}
	function getPDF($nombrePDF){
		
		$ruta="tmp/";
		$pdf=$ruta.$nombrePDF;
		
		if (!file_exists ($pdf)){
			throw new Exception("No fue encontrado el archivo, realize de nuevo la consulta");
		}
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");		
		header ("Content-Type: application/pdf");
		//header ("Content-Type: application/force-download");
		header("Content-Disposition: inline; filename=$nombrePDF");
		//header ("Content-Disposition: attachment; filename=$nombrePDF ");
		header ("Content-Length: ".filesize($pdf));		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		$fp=fopen($pdf, "r");

		fpassthru($fp);
		fclose ($fp);	
		if (!unlink($pdf)){
			throw new Exception("No Borrado");	
		}
		
	}
	function crearReporte($datos,$formatos){
		//--------------------------------------------------------------------------------------------------------
		/*	BUSCAR SI EXISTE UN FORMATO ESPECIFICO PARA ESTA EMPRESA O SUCURSAL,SI NO EXISTE, USAR EL GENÉRICO	*/
		require "eko_framework/app/models/reporte_corte_ticket_pdf.php";
		

		$pdf=new ReporteCorteTicketPDF('P','mm',array(250,70),$datos,$formatos);
		//--------------------------------------------------------------------------------------------------------
		$pdf->AddPage();
		// throw new Exception($datos['data']['detalles']['cantidad']);
		$pdf->SetMargins(1,1,1);
		$pdf->imprimeDetallesLiquidacion($datos);
		$pdf->imprimeDetallesRetencion($datos);
		// $pdf->imprimirTotales($datos);		
					
		$nombrePDF='rep_fact_.pdf';
		$pdf->Output('tmp/'.$nombrePDF);	
			
		return $nombrePDF;
	}
	function obtenerDatos($params){
		 //------------------------------------------------------------------
		//		. . . Se va a armar una cadena con el filtro WHERE . . . 
		//------------------------------------------------------------------
		$this->model=new Model();
		$model=$this->model;
		//$model->camposAfiltrar = array('NomCliente','RFCCliente');  
		
        $id = $params['IDCor'];	 
		$id_empresa =  $_SESSION['Auth']['User']['id_empresa'];
		$id_sucursal =  $_SESSION['Auth']['User']['id_sucursal'];
		
		$query = "SELECT e.nombre_comercial, e.nombre_fiscal, e.rfc,e.calle,e.numext, e.numint, e.colonia, e.cp, c.nom_ciu, es.nom_est, p.nom_pai,e.logotipo, e.regimen_fiscal, e.telefono, e.email,ifnull(e.logotipo_sucursal,0) as logotipo_sucursal
			FROM cat_empresas e
			INNER JOIN cat_ciudades c ON c.id_ciu = e.id_ciu
			INNER JOIN cat_estados es ON es.id_est = e.id_est
			INNER JOIN cat_paises p ON p.id_pai = e.id_pai
			WHERE e.id_empresa  = $id_empresa";	
        
		$resArrEmpresa = $model->query($query);
		if ( empty($resArrEmpresa) ){
			return array();
		}
		
		$query = "SELECT nombre_sucursal, logotipo
			FROM cat_sucursales
			WHERE id_sucursal  = $id_sucursal";	
        
		$resArrSucursal = $model->query($query);
		if ( empty($resArrSucursal) ){
			return array();
		}
		
        $query = "SELECT t.id_corte,t.consecutivo,t.concepto,DATE_FORMAT(t.fecha_corte,'%d/%m/%y %H:%i') as fecha,
		t.total_liquidado,t.total_retenido,t.total_corte,t.total_turno,t.total_ventas,t.total_depositos,t.total_retiros,t.diferencia_corte,
		s.nombre_sucursal, t.total_apartados, t.total_deudores
		FROM cortes t 
		inner join cat_sucursales s on s.id_sucursal = t.id_sucursal
		WHERE t.id_corte = $id";
		
		$resArr = $model->query($query);
		if ( empty($resArr) ){
			return array();
		}
		
		$query = "SELECT td.id_corte_liquidacion,f.nombre_formapago,d.denominacion,td.cantidad,td.total
		FROM cortes_liquidaciones td 
		INNER JOIN cat_formaspagos f ON f.id_formapago = td.id_formapago
		INNER JOIN cat_denominaciones d ON d.id_denominacion = td.id_denominacion
		WHERE td.id_corte = $id";
		$resArrDetallesLiquidacion = $model->query($query);
		if ( empty($resArrDetallesLiquidacion) ){
			return array();
		}
		
		$query = "SELECT td.id_corte_retencion,f.nombre_formapago,d.denominacion,td.cantidad,td.total
		FROM cortes_retenciones td 
		INNER JOIN cat_formaspagos f ON f.id_formapago = td.id_formapago
		INNER JOIN cat_denominaciones d ON d.id_denominacion = td.id_denominacion
		WHERE td.id_corte = $id";
		$resArrDetallesRetencion = $model->query($query);
		if ( empty($resArrDetallesRetencion) ){
			return array();
		}
		// $serie = $resArr[0]['serie_movimiento'];
			// throw new Exception($serie);
		//--------------Si se ha filtrado por cliente, mando aparte los datos del cliente
		
		$response=array();
      	$response['data']['corte']=$resArr[0]; 
		$response['data']['empresa']=$resArrEmpresa[0]; 
		$response['data']['sucursal']=$resArrSucursal[0]; 
		$response['data']['detallesLiquidacion']=$resArrDetallesLiquidacion; 
		$response['data']['detallesRetencion']=$resArrDetallesRetencion;		
		// print_r($resArrDetalles,true);		
        //$response['data']=$resArr;   
	    //$response['filtros']=$params;	
			
		// throw new Exception($resArr['data']['serie_venta'][0]);
        return $response;
	}
	function prepararParaImpresion($datos,$filtros){
		 /*-------RESPUESTA: -----------------------------------------------*/
		$resArr=$datos['data'];
		$fInicial=$filtros['fInicial'];
		$fFinal=$filtros['fFinal'];
		$fFinal.=" 23:59:59";

        //---------------Calcula el total de las facturas y de las canceladas
        $sumTotal=0;
        $sumIvaTotal=0;
        $sumIvaCan=0; 
        $sumCanceladas=0;
        $sumPorMes=array();
        $response=array();
		
		for($i=0;$i<sizeof($resArr);$i++){
			//---------------------------------------------------------
			//$FechaFac=$resArr[$i]['FechaFac'];
			list($FechaFac,$horaFac)=explode(' ',$resArr[$i]['FechaFac']);
			list($dia,$mes,$año)=explode('/',$FechaFac);
			$mes=$this->mesToLetras($mes);
			$periodo="$año/$mes";
			
			if (!isset($sumPorMes[$periodo])){
				$sumPorMes[$periodo]=array(
					'facturadas'=>array(
						'total'=>0,
						'impuestos'=>0
					),
        			'canceladas'=>array(
						'total'=>0,
						'impuestos'=>0
					)
				);					
			}
			//---------------------------------------------------------
			$resArr[$i]['Total']=$this->redondeado($resArr[$i]['Total'],2);
			
			$resArr[$i]['TotImpTras']=$this->redondeado($resArr[$i]['TotImpTras'],2);
			//echo $resArr[$i]['TotImpTras']."<br/>";
			
			if (($resArr[$i]['TipComp']=='ingreso') && ($resArr[$i]['StatusFac'] != 'C')){				
				$sumTotal+=$resArr[$i]['Total'];
				$sumIvaTotal+=$resArr[$i]['TotImpTras'];
				//------------------------------------------------------
				$sumPorMes[$periodo]['facturadas']['total']+=$resArr[$i]['Total'];
				$sumPorMes[$periodo]['facturadas']['impuestos']+=$resArr[$i]['TotImpTras'];
			}else if (($resArr[$i]['TipComp']=='egreso') && ($resArr[$i]['StatusFac'] != 'C')){
				//$creadaEnElPeriodo=$this->creadaEnElPeriodo($fInicial,$fFinal,$FechaFac);
				//if(!$creadaEnElPeriodo){
					$sumCanceladas+=$resArr[$i]['Total'];
					$sumIvaCan+=$resArr[$i]['TotImpTras'];
					//------------------------------------------------------
					$sumPorMes[$periodo]['canceladas']['total']+=$resArr[$i]['Total'];
					$sumPorMes[$periodo]['canceladas']['impuestos']+=$resArr[$i]['TotImpTras'];	
				//} 
				
			}
		}
		$response['sumatorias']=array(
			'facturadas'=>array(
				'total'=>$sumTotal,
				'impuestos'=>$sumIvaTotal
			),
			'canceladas'=>array(
				'total'=>$sumCanceladas,
				'impuestos'=>$sumIvaCan
			),
			'mensuales'=>$sumPorMes
		);
		$response['data']=$resArr;
		return $response;
	}
	function creadaEnElPeriodo($fInicial,$fFinal,$FechaFacSinHora){		
		//throw new Exception("$fInicial,$fFinal,$FechaFacSinHora");
		$fechaInicial=strtotime ($this->model->jsDateToMysql($fInicial));		
		$fechaFinal=strtotime ($this->model->jsDateToMysql($fFinal));
		$fechaFacturacion=strtotime ($this->model->jsDateToMysql($FechaFacSinHora));
		
		
		//$fechaInicial=DateTime::createFromFormat('d/m/Y', $fInicial)->getTimestamp();
		//$fechaFinal=DateTime::createFromFormat('d/m/Y H:i:s', $fFinal)->getTimestamp();
		//$fechaFacturacion=DateTime::createFromFormat('d/m/y', $FechaFacSinHora)->getTimestamp();
		if ($fechaInicial<=$fechaFacturacion && $fechaFacturacion<=$fechaFinal){
			//throw new Exception("TRUE: "."$fechaInicial<=$fechaFacturacion && $fechaFacturacion<=$fechaFinal");
			return true;
		}else{
			//throw new Exception("FALSE: "."$fechaInicial<=$fechaFacturacion && $fechaFacturacion<=$fechaFinal");
			return false;
		}	
	}
	function mesToLetras($mes){
		$numMes=floor ($mes);
		$mesEnLetras='';
		switch($numMes){
			case 1:
				$mesEnLetras="ENERO";
			break;
			case 2:
				$mesEnLetras="FEBRERO";
			break;
			case 3:
				$mesEnLetras="MARZO";
			break;
			case 4:
				$mesEnLetras="ABRIL";
			break;
			case 5:
				$mesEnLetras="MAYO";
			break;
			case 6:
				$mesEnLetras="JUNIO";
			break;
			case 7:
				$mesEnLetras="JULIO";
			break;
			case 8:
				$mesEnLetras="AGOSTO";
			break;
			case 9:
				$mesEnLetras="SEPTIEMBRE";
			break;
			case 10:
				$mesEnLetras="OCTUBRE";
			break;
			case 11:
				$mesEnLetras="NOVIEMBRE";
			break;
			case 12:
				$mesEnLetras="DICIEMBRE";
			break;
			default:
				throw new Exception("Mes desconocido: ".$numMes);			
		}
		return $mesEnLetras;
	}
	function redondeado ($numero, $decimales) {
   		$factor = pow(10, $decimales);
   		return (round($numero*$factor)/$factor); 
	} 
}
?>