<?php
/*
 *	Esta clase deberia analizar y preparar toda la informacion, dejarla lista para que el PDF imprima usando solamente ciclos
 * por el momento hay logica repartida en esta clase y en reporte_de_facturacion_pdf.php
 */
class ReporteVentasProductosCostos{
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
		require "eko_framework/app/models/reporte_ventas_productos_costos_pdf.php";
		
		$pdf=new ReporteVentasProductosCostosPDF('P','mm','Letter',$datos,$formatos);
		//--------------------------------------------------------------------------------------------------------
		$pdf->AddPage('P');
		// throw new Exception($datos['data']['detalles']['cantidad']);
		$pdf->imprimeDetalles($datos);				
		//---------------------------------------------------------------------------
		// ¿Como se va a nombrar el archivo?	rep_fact_filtro1_filtro2_...filtroN.pdf 
		// ejemplo:			 FecIni:  10/02/2010
		//					 FenFin:  02/02/2011 
		//					 status:  C,P,S
		//					 cliente: todos
		//	 	    rep_fact_100210_020211_C_P_S_all.pdf
		//			rep_fact_100210_020211_C_P_S_RFC.pdf
		//---------------------------------------------------------------------------
		// $fInicial=str_replace( '/', '', $datos['filtros']['fInicial']);
		// $fFinal=str_replace( '/', '', $datos['filtros']['fFinal']); 		
		// $estados='';		
		// $estados.=($datos['filtros']['canceladas']=='true')? '_C' : '';
		// $estados.=($datos['filtros']['pagadas']=='true')? '_P' : '';
		// $estados.=(isset($datos['filtros']['pendientes']) && $datos['filtros']['pendientes']=='true')? '_S' : '';
		// $cliente=(isset($datos['cliente']))? '_'.$datos['cliente']['RFCCliDet'] : '' ;				
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
		
		$idEmp = $params['IDEmp'];	  
		$idSuc = $params['IDSuc'];
		$id_lin= $params['IDLin'];
		$fechaInicio=(empty($params['FechaIni'])) ?  '': $params['FechaIni'];
		$fechaFin=(empty($params['FechaFin'])) ?  '': $params['FechaFin'];
		$fechaInicio.=" 00:00:00"; 
		$fechaFin.=" 23:59:59";
		$fechaFinFiltro=(empty($params['FechaFin'])) ?  '': $params['FechaFin'];
		// $filtros = array();
		// $filtros['fechaInicio'] = ;
		
		$filtroSql='';
        
		if ($idSuc > 0 && $id_lin > 0) {
            $filtroSql=" WHERE v.id_sucursal = $idSuc AND v.fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' AND p.id_linea = $id_lin ";
        }else if ($idSuc > 0 && $id_lin == 0) {
            $filtroSql=" WHERE v.id_sucursal = $idSuc AND v.fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' ";
        }elseif ($idSuc == 0 && $id_lin > 0) {
            $filtroSql=" WHERE v.fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' AND p.id_linea = $id_lin ";
        }else{
			 $filtroSql=" WHERE v.fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' ";
     
		}
		
		$query = "SELECT DATE_FORMAT('$fechaInicio','%d/%m/%Y') as fecha_inicio,DATE_FORMAT('$fechaFinFiltro','%d/%m/%Y') as fecha_fin";
		
		$resArr = $model->query($query);
		if ( empty($resArr) ){
			return array();
		}
		
		$query = "SELECT SUM(vd.cantidad * p.precio_compra) as total
					FROM ventas_detalles vd
					INNER JOIN ventas v ON v.id_venta = vd.id_venta	
					INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
					LEFT JOIN cat_lineas l ON l.id_linea = p.id_linea					
				$filtroSql
				order by v.fecha_venta";
				
		
				
		$resArrVentas = $model->query($query);
		if ( empty($resArrVentas) ){
			return array();
		}
				
		$query = "SELECT p.codigo,p.codigo_barras,ifnull(l.nombre_linea,'') as nombre_linea,p.descripcion,SUM(vd.cantidad) as cantidad,SUM(vd.cantidad * p.precio_compra) AS total 
					FROM ventas_detalles vd
					INNER JOIN ventas v ON v.id_venta = vd.id_venta
					INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
					LEFT JOIN cat_lineas l ON l.id_linea = p.id_linea 
					$filtroSql
					GROUP BY vd.id_producto,p.codigo,p.codigo_barras,l.nombre_linea,p.descripcion
					order by p.descripcion";		
				
		
		// throw new Exception($query);		
		$resArrDetalles = $model->query($query);
		if ( empty($resArrDetalles) ){
			return array();
		}
		
		
		// $serie = $resArr[0]['serie_movimiento'];
			// throw new Exception('ramon');
		//--------------Si se ha filtrado por cliente, mando aparte los datos del cliente
		
		$response=array();
      	$response['data']['filtros']=$resArr[0]; 
		$response['data']['ventas']=$resArrVentas[0];
		$response['data']['detalles']=$resArrDetalles;   
		// print_r($resArrDetalles,true);		
        //$response['data']=$resArr;   
	    //$response['filtros']=$params;	
			
		// throw new Exception($resArr['data']['serie_movimiento'][0]);
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