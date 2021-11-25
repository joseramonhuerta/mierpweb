<?php
/*
 *	Esta clase deberia analizar y preparar toda la informacion, dejarla lista para que el PDF imprima usando solamente ciclos
 * por el momento hay logica repartida en esta clase y en reporte_de_facturacion_pdf.php
 */
class ReporteExistenciaProductosExcel{
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
		require "eko_framework/app/models/reporte_ventas_productos_pdf.php";
		
		$pdf=new ReporteVentasProductosPDF('P','mm','Letter',$datos,$formatos);
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
		
		$id = $params['IDAlm'];	  
		$id_lin=(empty($params['IDLin'])) ?  '': $params['IDLin'];
		
		$filtroSql='';
        
		if (strlen($id_lin) > 0) {
            $filtroSql=" WHERE l.id_linea = $id_lin ";
        }
		
		
		
        
        $query = "SELECT nombre_almacen
		FROM cat_almacenes 
		WHERE id_almacen = $id";
		
		$resArr = $model->query($query);
		if ( empty($resArr) ){
			return array();
		}
		
		
		
		
		$query = "SELECT p.id_producto,p.descripcion,p.codigo_barras,p.codigo,IFNULL(s.stock,0) AS stock,l.nombre_linea,precio_venta,
	IF(IFNULL(s.stock,0) <= 0,0, (precio_venta * IFNULL(s.stock,0))) AS total	
				FROM cat_productos p
				INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
				left join cat_productos_stocks s on s.id_producto = p.id_producto and s.id_almacen = $id
				$filtroSql
				ORDER BY l.nombre_linea,p.descripcion";
		$resArrDetalles = $model->query($query);
		if ( empty($resArrDetalles) ){
			return array();
		}
		
		// throw new Exception($query);
		// $serie = $resArr[0]['serie_movimiento'];
			// throw new Exception('ramon');
		//--------------Si se ha filtrado por cliente, mando aparte los datos del cliente
		
		$response=array();
		$response['data']=$resArrDetalles;
      	// $response['data']['existencia']=$resArr[0]; 
		// $response['data']['detalles']=$resArrDetalles;   
		// print_r($resArrDetalles,true);		
        //$response['data']=$resArr;   
	    //$response['filtros']=$params;	
			
		// throw new Exception($resArr['data']['serie_movimiento'][0]);
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
		// print_r($preparados);
		// exit;
		// Comienzo
		
			// print_r($informes);
			
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
			$nombreReporte = "Reporte Existencia Productos ";
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
			

			$objPHPExcel->getActiveSheet()->mergeCells('A1:G1');
			$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);
			$objPHPExcel->getActiveSheet()->getStyle('A1:E1')->applyFromArray($titulo);
			
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
			
			
			$objPHPExcel->getActiveSheet()->mergeCells('A2:G3');
			
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

            $objPHPExcel->getActiveSheet()->getStyle('A4:G4')->applyFromArray($encabezado);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(15);
            
			
			// NOMBRE CLIENTE, RFC, FECHA ULT. REGISTRO, MES DE OPERACIÓN, AÑO DE OPERACIÓN, SALDO, CONSUMO CALCULADO, SALDO 
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "CODIGO")
							  ->SetCellValue('B'.$rowCount, "CODIGO BARRAS")
							  ->SetCellValue('C'.$rowCount, "DESCRIPCION")
							  ->SetCellValue('D'.$rowCount, "LINEA")
							  ->SetCellValue('E'.$rowCount, "STOCK")
							  ->SetCellValue('F'.$rowCount, "PRECIO VENTA")
							  ->SetCellValue('G'.$rowCount, "TOTAL");
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
			//ciclo
			$blanco = 1;
			
			foreach ($informes as $key => $value) {
				// if($blanco==1){
					// $objPHPExcel->getActiveSheet()->getStyle('A'.($rowCount).':I'.($rowCount))->applyFromArray($filablanca);
					// $blanco = 0;
				// }else{
					// $objPHPExcel->getActiveSheet()->getStyle('A'.($rowCount).':I'.($rowCount))->applyFromArray($filagris);
					// $blanco = 1;
				// }
				
				// Nombre,RFC,FechaLastRegistro,Mes,Anio,Saldo,ConsumoCalculado,SaldoCalculado,FechaCalculo,UsuarioCalculo
								
				$colorBackGround = 'A3F3B2';
				$colorLetra = '359C33';
				if($value['SaldoCalculado'] < 1) {
					$colorBackGround = 'F59A9C';
					$colorLetra = '790000';
				} 
				// $colorBackGround = '#'.$colorBackGround;
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $value['codigo'])
							  ->SetCellValue('B'.$rowCount, $value['codigo_barras'])
							  ->SetCellValue('C'.$rowCount, $value['descripcion'])
							  ->SetCellValue('D'.$rowCount, $value['nombre_linea'])
							  ->SetCellValue('E'.$rowCount, $value['stock'])
							  ->SetCellValue('F'.$rowCount, $value['precio_venta'])
							  ->SetCellValue('G'.$rowCount, $value['total']);
			  // echo $colorBackGround;
			  // exit;
			  // $objPHPExcel->getActiveSheet()
				// ->getStyle('H'.$rowCount)
				// ->applyFromArray(
					// array(
						// 'fill' => array(
							// 'type' => PHPExcel_Style_Fill::FILL_SOLID,
							// 'color' => array('rgb' => $colorBackGround)
						// ),
						// 'font'  => array(
							// 'color' => array('rgb' => $colorLetra)
						// ),
						// 'borders' => array(
							  // 'allborders' => array(
								  // 'style' => PHPExcel_Style_Border::BORDER_THIN,
								  // 'color' => array('rgb' => $colorLetra)
							  // )
						  // )
					// )
				// );
				// $objPHPExcel->getActiveSheet()->getStyle('H'.$rowCount)->getAlignment()->setIndent(1);
							  // UsuarioCalculo FechaCalculo
				$rowCount++;
			}
			//fin ciclo
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

			// $rowCount += 4;

			// $objPHPExcel->getActiveSheet()->getStyle('B'.($rowCount+1).':D'.($rowCount+5))->applyFromArray($firma);
			// $objPHPExcel->getActiveSheet()->mergeCells('B'.($rowCount+1).':C'.($rowCount+1));
			// $objPHPExcel->getActiveSheet()->mergeCells('B'.($rowCount+2).':C'.($rowCount+2));
			// $objPHPExcel->getActiveSheet()->mergeCells('B'.($rowCount+3).':C'.($rowCount+3));
			// $objPHPExcel->getActiveSheet()->mergeCells('B'.($rowCount+4).':C'.($rowCount+4));
			// $objPHPExcel->getActiveSheet()->mergeCells('B'.($rowCount+5).':C'.($rowCount+5));
			// $objPHPExcel->getActiveSheet()->SetCellValue('B'.($rowCount+1), "__________________________________")
							  // ->SetCellValue('B'.($rowCount+2), "ELABORO")
							  // ->SetCellValue('B'.($rowCount+3), "aaaa")
							  // ->SetCellValue('B'.($rowCount+4), "aaaa")
							  // ->SetCellValue('B'.($rowCount+5), "aaaa");

			// $objPHPExcel->getActiveSheet()->SetCellValue('D'.($rowCount+1), "__________________________________")
							  // ->SetCellValue('D'.($rowCount+2), "aaaa")
							  // ->SetCellValue('D'.($rowCount+3), "aaaa")
							  // ->SetCellValue('D'.($rowCount+4), "aaaa")
							  // ->SetCellValue('D'.($rowCount+5), "aaaaa");	

			$objPHPExcel->getActiveSheet()->setTitle($nombreReporte);
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header("Content-Disposition: attachment; filename=\"Reporte Existencia Excel ".$fechaNueva.".xlsx\"");
			header("Cache-Control: max-age=0");
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			ob_end_clean();
			$objWriter->save('php://output');
			exit();
		// Comienzo End
		
		// print_r($preparados);
		// exit;
	}
}
?>