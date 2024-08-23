<?php
/*
 *	Esta clase deberia analizar y preparar toda la informacion, dejarla lista para que el PDF imprima usando solamente ciclos
 * por el momento hay logica repartida en esta clase y en reporte_de_facturacion_pdf.php
 */
class ReporteSaldosLineas{
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
		require "eko_framework/app/models/reporte_saldos_lineas_pdf.php";
		
		$pdf=new ReporteSaldosLineasPDF('P','mm','Letter',$datos,$formatos);
		//--------------------------------------------------------------------------------------------------------
		$pdf->AddPage('P');
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
		//$model->camposAfiltrar = array('NomCliente','RFCCliente');  $_SESSION['Auth']['Almacen']['IDAlmacen'];
		$id_lin=(empty($params['IDLin'])) ?  0: $params['IDLin'];		
		$tipoCalculo = (empty($params['TipoCalculo'])) ?  0: $params['TipoCalculo'];
		$sinExistencia = (empty($params['SinExistencia'])) ?  0: $params['SinExistencia'];
		
		$query ="CALL spReporteSaldosLineas($id_lin,$sinExistencia, $tipoCalculo);";
				
		$resArrVentas = $model->query($query);
		if ( empty($resArrVentas) ){
			return array();
		}
			
		
		$response=array();      	
		
		$response['data']['detalles']=$resArrVentas;   
        return $response;
	}
	function prepararParaImpresion($datos,$filtros){
		$resArr=$datos['data'];
        $response=array();
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
	function generarReporteExcel($params){
		$datos=$this->obtenerDatos($params);					
		if (empty($datos) ){
			throw new Exception("No hay datos que mostrar");
		}	
		
		$preparados=$this->prepararParaImpresion($datos,$params);		
				
			$informes = $preparados["data"];
			foreach($res as $row){
				$informes[] = $row;
			}

			if(empty($informes)){
				if(!empty($_GET['ajax'])){
					echo '0'; 	
				}
				exit;
			}else if(!empty($_GET['ajax'])){
				echo '1';
				exit;
			}
			//exit;
			// $existe = file_exists('eko_framework/includes/phpexcel/PHPExcel.php') ? 1 : 0;
			require_once 'eko_framework/includes/phpexcel/PHPExcel.php';
			include 'eko_frameworkincludes/phpexcel/PHPExcel/IOFactory.php';
			//Reporte de Saldos de Folios
			$nombreReporte = "Reporte Pedido Sugerido ";
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("Ramon Huerta")
            	->setLastModifiedBy("")
            	->setTitle($nombreReporte)
            	->setSubject("reporte");
            $objPHPExcel->setActiveSheetIndex(0);

            $rowCount = 1;

            $encabezado = array(
			    'font'  => array(
			        'bold' => true,
			        'color' => array('rgb' => 'FFFFFF'),
			        'size'  => 12,
			    ),
			    'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb' => $_GET['color'])
			    ),
			    'alignment' => array(
			        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP ,
			    )
			);

			$titulo = array(
			    'font'  => array(
			        'bold' => true,
			        'size'  => 18,
			    ),
			    'alignment' => array(
			        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
			    )
			);

			$_GET['empresasText'] = trim($_GET['empresasText'], " ");
			$_GET['empresasText'] = trim($_GET['empresasText'], ",");
			$_GET['empresasText'] = trim($_GET['empresasText'], "undefined");

			setlocale(LC_ALL,"es_ES");
			$fechaNueva = $_GET['dateDesde']; //1234 6789
			$fechaNueva = date("dm", strtotime($fechaNueva));
			$fechaNuevaY = date("Y", strtotime($fechaNueva));
			

			$objPHPExcel->getActiveSheet()->mergeCells('A1:L1');
			$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
			$objPHPExcel->getActiveSheet()->getStyle('A1:L1')->applyFromArray($titulo);
			
			$fechaNueva = $rest = substr($fechaNueva, 0, 7);
			setlocale(LC_ALL,"es_ES");
			// $hoy = date("j F Y g:i a");
			$year = date("Y");
			$month = date("m");
			$day = date("d");
			// echo $day.$month.$year;
			// exit;
			if($month=='01'){
				$month = 'Enero';
			}else if($month=='02'){
				$month = 'Febrero';
			}else if($month=='03'){
				$month = 'Marzo';
			}else if($month=='04'){
				$month = 'Abril';
			}else if($month=='05'){
				$month = 'Mayo';
			}else if($month=='06'){
				$month = 'Junio';
			}else if($month=='07'){
				$month = 'Julio';
			}else if($month=='08'){
				$month = 'Agosto';
			}else if($month=='09'){
				$month = 'Septiembre';
			}else if($month=='10'){
				$month = 'Octubre';
			}else if($month=='11'){
				$month = 'Noviembre';
			}else if($month=='12'){
				$month = 'Diciembre';
			}
			
			$objPHPExcel->getActiveSheet()->SetCellValue('A1', $nombreReporte.$day.' '.$month.' '.$year);
			
			
			$objPHPExcel->getActiveSheet()->mergeCells('A2:M3');
			
			$filtrosFormat = array(
			    'font'  => array(
			        // 'bold' => true,
			        'size'  => 13
			    ),
			    'alignment' => array(
			        'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP
			    )
			);
			
			$objRichText = new PHPExcel_RichText();
			$rowCount = 4;

            $objPHPExcel->getActiveSheet()->getStyle('A4:M4')->applyFromArray($encabezado);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(35);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('I')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('J')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('K')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('L')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('M')->setWidth(20);
			
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "CODIGO")
							  ->SetCellValue('B'.$rowCount, "CODIGO BARRAS")
							  ->SetCellValue('C'.$rowCount, "DESCRIPCION")
							  ->SetCellValue('D'.$rowCount, "LINEA")
							  ->SetCellValue('E'.$rowCount, "SUCURSAL")
							  ->SetCellValue('F'.$rowCount, "ALMACEN")
							  ->SetCellValue('G'.$rowCount, "STOCK")
							  ->SetCellValue('H'.$rowCount, "VENTAS")
							  ->SetCellValue('I'.$rowCount, "MINIMO")
							  ->SetCellValue('J'.$rowCount, "MAXIMO")
							  ->SetCellValue('K'.$rowCount, "MINIMO NVO")
							  ->SetCellValue('L'.$rowCount, "MAXIMO NVO")							  
							  ->SetCellValue('M'.$rowCount, "PEDIDO SUGERIDO");
			$rowCount++;
			$filablanca = array(
			    'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb' => 'FFFFFF')
			    )
			);

			$filagris = array(
			    'fill' => array(
			        'type' => PHPExcel_Style_Fill::FILL_SOLID,
			        'color' => array('rgb' => 'F4F4F4')
			    )
			);
			
			$blanco = 1;
			
			foreach ($informes as $key => $value) {
				$colorBackGround = 'A3F3B2';
				$colorLetra = '359C33';
				if($value['SaldoCalculado'] < 1) {
					$colorBackGround = 'F59A9C';
					$colorLetra = '790000';
				} 
							
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $value['codigo'])
							  ->SetCellValue('B'.$rowCount, $value['codigo_barras'])
							  ->SetCellValue('C'.$rowCount, $value['descripcion'])
							  ->SetCellValue('D'.$rowCount, $value['nombre_linea'])
							  ->SetCellValue('E'.$rowCount, $value['nombre_sucursal'])
							  ->SetCellValue('F'.$rowCount, $value['nombre_almacen'])
							  ->SetCellValue('G'.$rowCount, $value['stock'])
							  ->SetCellValue('H'.$rowCount, $value['ventas'])
							  ->SetCellValue('I'.$rowCount, $value['stock_min'])
							  ->SetCellValue('J'.$rowCount, $value['stock_max'])
							  ->SetCellValue('K'.$rowCount, $value['stock_min_nvo'])
							  ->SetCellValue('L'.$rowCount, $value['stock_max_nvo'])
							  ->SetCellValue('M'.$rowCount, $value['pedido_sugerido']);
			  
				$rowCount++;
			}
			
			$firma = array(
			    'font'  => array(
			        'color' => array('rgb' => '000000'),
			        'bold' => true,
			    ),
			    'alignment' => array(
	                'horizontal' =>	PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
	                'wrap' => true
	            )
			);

			$objPHPExcel->getActiveSheet()->setTitle($nombreReporte);
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header("Content-Disposition: attachment; filename=\"Reporte Pedido Sugerido ".$fechaNueva.".xlsx\"");
			header("Cache-Control: max-age=0");
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			ob_end_clean();
			$objWriter->save('php://output');
			exit();
		
	}
}
?>