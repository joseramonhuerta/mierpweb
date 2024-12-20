<?php
/*
 *	Esta clase deberia analizar y preparar toda la informacion, dejarla lista para que el PDF imprima usando solamente ciclos
 * por el momento hay logica repartida en esta clase y en reporte_de_facturacion_pdf.php
 */
class ReporteClientesCategoriasExcel{
	function obtenerDatos($params){
		
		$this->model=new Model();
		$model=$this->model;
				
		$idCat = $params['IDCat'];					
				
		$query ="CALL spReporteCatalogoClientesCategorias($idCat);";
		
		$resArrClientes = $model->query($query);
		if ( empty($resArrClientes) ){
			return array();
		}			
		
		$response=array();      	
		$response['data']=$resArrClientes;
		
        return $response;
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
			$nombreReporte = "Reporte Clientes Categorias ";
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setCreator("")
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
			$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
			
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
			
			
			//$objPHPExcel->getActiveSheet()->mergeCells('A2:F3');
			// $objPHPExcel->getActiveSheet()->mergeCells('B2:H2');
			// $objPHPExcel->getActiveSheet()->mergeCells('A3:H3');
			
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
			
			$rowCount = 3;

            $objPHPExcel->getActiveSheet()->getStyle('A3:F3')->applyFromArray($encabezado);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(20);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
            
			
			// NOMBRE CLIENTE, RFC, FECHA ULT. REGISTRO, MES DE OPERACIÓN, AÑO DE OPERACIÓN, SALDO, CONSUMO CALCULADO, SALDO 
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "NOMBRE FISCAL")
							  ->SetCellValue('B'.$rowCount, "NOMBRE COMERCIAL")
							  ->SetCellValue('C'.$rowCount, "DIRECCION")
							  ->SetCellValue('D'.$rowCount, "TELEFONO")
							  ->SetCellValue('E'.$rowCount, "CELULAR")
							  ->SetCellValue('F'.$rowCount, "CATEGORIA");
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
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, $value['nombre_fiscal'])
							  ->SetCellValue('B'.$rowCount, $value['nombre_comercial'])
							  ->SetCellValue('C'.$rowCount, $value['direccion'])
							  ->SetCellValue('D'.$rowCount, $value['telefono_contacto'])
							  ->SetCellValue('E'.$rowCount, $value['celular_contacto'])
							  ->SetCellValue('F'.$rowCount, $value['nombre_categoria']);
			 
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

			$objPHPExcel->getActiveSheet()->setTitle($nombreReporte);
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header("Content-Disposition: attachment; filename=\"Reporte Clientes Categorias.xlsx\"");
			header("Cache-Control: max-age=0");
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			ob_end_clean();
			$objWriter->save('php://output');
			exit();
		// Comienzo End
		
		// print_r($preparados);
		// exit;
	}

	function prepararParaImpresion($datos,$filtros){
		$resArr=$datos['data'];
        $response=array();
		$response['data']=$resArr;
		return $response;
	}
}
?>