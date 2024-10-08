<?php
/*
 *	Esta clase deberia analizar y preparar toda la informacion, dejarla lista para que el PDF imprima usando solamente ciclos
 * por el momento hay logica repartida en esta clase y en reporte_de_facturacion_pdf.php
 */
class ReporteVentasProductosExcel{
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
		
		$idEmp = $params['IDEmp'];	  
		$idSuc = $params['IDSuc'];
		$id_lin=(empty($params['IDLin'])) ?  0: $params['IDLin'];
		$fechaInicio=(empty($params['FechaIni'])) ?  '': $params['FechaIni'];
		$fechaFin=(empty($params['FechaFin'])) ?  '': $params['FechaFin'];
		$fechaInicio.=" 00:00:00"; 
		$fechaFin.=" 23:59:59";
		$fechaFinFiltro=(empty($params['FechaFin'])) ?  '': $params['FechaFin'];
		// $filtros = array();
		// $filtros['fechaInicio'] = ;
		/*
		$filtroSqlVentas='';
        
		if (strlen($id_lin) > 0) {
            $filtroSqlVentas=" WHERE v.fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' AND p.id_linea = $id_lin and v.status = 'A'";
        }else{
			 $filtroSqlVentas=" WHERE v.fecha_venta BETWEEN '$fechaInicio' AND '$fechaFin' and v.status = 'A'";
     
		}
		
		$filtroSqlRemisionesMovimientos='';
        
		if (strlen($id_lin) > 0) {
            $filtroSqlRemisionesMovimientos=" WHERE v.fecha_movimiento BETWEEN '$fechaInicio' AND '$fechaFin' AND p.id_linea = $id_lin and tm.tipo_movimiento = 4 and v.status = 'A'";
        }else{
			 $filtroSqlRemisionesMovimientos=" WHERE v.fecha_movimiento BETWEEN '$fechaInicio' AND '$fechaFin' and tm.tipo_movimiento = 4 and v.status = 'A'";
     
		}
		
		$filtroSqlRemisiones='';
        
		if (strlen($id_lin) > 0) {
            $filtroSqlRemisiones=" WHERE v.fecha BETWEEN '$fechaInicio' AND '$fechaFin' AND p.id_linea = $id_lin and v.status = 'A'";
        }else{
			 $filtroSqlRemisiones=" WHERE v.fecha BETWEEN '$fechaInicio' AND '$fechaFin' and v.status = 'A'";
     
		}
		*/
		$query = "SELECT DATE_FORMAT('$fechaInicio','%d/%m/%Y') as fecha_inicio,DATE_FORMAT('$fechaFinFiltro','%d/%m/%Y') as fecha_fin";
		
		$resArr = $model->query($query);
		if ( empty($resArr) ){
			return array();
		}
		
			/*$query = "SELECT vd.id_producto,v.id_sucursal,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS ventas,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS 
			stock
			FROM ventas_detalles vd
			INNER JOIN ventas v ON v.id_venta = vd.id_venta
			INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
			INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
			INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
			LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen`
			$filtroSql
			GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_almacen,v.id_sucursal,su.nombre_sucursal
			ORDER BY p.descripcion,su.nombre_sucursal";*/
			/*
			$query = "SELECT 		t1.id_producto,t1.id_sucursal,t1.nombre_sucursal,t1.descripcion,t1.codigo_barras,t1.codigo,SUM(t1.cantidad) AS ventas,t1.nombre_linea,SUM(DISTINCT t1.stock) AS 
			stock
				FROM  (
						   SELECT vd.id_producto,v.id_sucursal,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS 
							stock
							FROM ventas_detalles vd
							INNER JOIN ventas v ON v.id_venta = vd.id_venta
							INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
							INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
							INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
							LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen`
							$filtroSqlVentas
							GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_almacen,v.id_sucursal,su.nombre_sucursal
							
				UNION ALL			
							
				SELECT vd.id_producto,v.id_sucursal,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS 
							stock
							FROM movimientos_almacen_detalles vd
							INNER JOIN movimientos_almacen v ON v.id_movimiento = vd.id_movimiento
							INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
							INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
							INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
							INNER JOIN cat_tiposmovimientos tm ON tm.id_tipomovimiento = v.id_tipomovimiento
							LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen_origen`
							$filtroSqlRemisionesMovimientos
							GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_almacen_origen,v.id_sucursal,su.nombre_sucursal
							
				UNION ALL			
							
				SELECT vd.id_producto,v.id_sucursal,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS 
							stock
							FROM remisiones_detalles vd
							INNER JOIN remisiones v ON v.id_remision = vd.id_remision
							INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
							INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
							INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
							LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen`
							$filtroSqlRemisiones
							GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_almacen,v.id_sucursal,su.nombre_sucursal			
							
							
					 ) t1
				GROUP BY t1.id_producto,t1.codigo,t1.descripcion,t1.id_sucursal,t1.nombre_sucursal     
				ORDER BY t1.descripcion,t1.nombre_sucursal";
				*/
				
				
				$query ="CALL spReporteVentas(0,$idSuc,'$fechaInicio','$fechaFin',$id_lin,0,0);";
				
				// throw new Exception($query);
				$resArrVentas = $model->query($query);
				if ( empty($resArrVentas) ){
					return array();
				}
			
		// $query = "SELECT SUM(vd.total) as total
					// FROM ventas_detalles vd
					// INNER JOIN ventas v ON v.id_venta = vd.id_venta	
					// INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
					// LEFT JOIN cat_lineas l ON l.id_linea = p.id_linea					
				// $filtroSql
				// order by v.fecha_venta";
				
		
				
				
		
		// $serie = $resArr[0]['serie_movimiento'];
			// throw new Exception('ramon');
		//--------------Si se ha filtrado por cliente, mando aparte los datos del cliente
		
		$response=array();
      	//$response['data']['filtros']=$resArr[0]; 
		$response['data']=$resArrVentas;
		//$response['data']['detalles']=$resArrDetalles;   
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
			$nombreReporte = "Reporte de Ventas de Productos ";
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
			// $fechaNueva2 = $rest = substr($fechaNueva, 2, 2);
			// $fechaNueva = $rest = substr($fechaNueva, 0, 2);
			
			
			// $fechaNueva = $fechaNueva.' '.$fechaNueva2.' '.$fechaNuevaY;

			//setlocale(LC_ALL,"es_ES");
			//$fechaNueva = $_GET['dateDesde'];
			//$fechaNueva = DateTime::createFromFormat("Y d F", $fechaNueva);
			//$fechaNueva = strftime("%Y %d %B",$fechaNueva);

			$objPHPExcel->getActiveSheet()->mergeCells('A1:F1');
			// $objPHPExcel->getActiveSheet()->mergeCells('C1:H1');

			// $objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(120);
			$objPHPExcel->getActiveSheet()->getRowDimension('1')->setRowHeight(40);

			// $objDrawing = new PHPExcel_Worksheet_Drawing();
			// $objDrawing->setName('Logo');
			// $objDrawing->setDescription('Logo');
			// // $logo = $_SERVER['DOCUMENT_ROOT']. '
			// $logo = 'images/productos/7/2/329_Puntuel Logo.png'; // Provide path to your logo file
			// $objDrawing->setPath($logo);
			// $objDrawing->setOffsetX(50);    // setOffsetX works properly
			// $objDrawing->setOffsetY(10);  //setOffsetY has no effect
			// $objDrawing->setCoordinates('A1');
			// $objDrawing->setHeight(120); // logo height
			// $objDrawing->setWidth(120);      
			// $objDrawing->setWorksheet($objPHPExcel->getActiveSheet());  //save
			// $objPHPExcel->getActiveSheet()->getStyle('C1:E1')->applyFromArray($titulo);
			// $objPHPExcel->getActiveSheet()->SetCellValue('C1', "REPORTE DE SALDOS DE FOLIOS ");
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
			
			
			$objPHPExcel->getActiveSheet()->mergeCells('A2:F3');
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

			// $objBold = $objRichText->createTextRun('EMPRESA:  ');
			// $objBold->getFont()->setBold(true);
			// $objBold->getFont()->setSize(13);
			
			// $objoNotBold = $objRichText->createTextRun($params['NombreCliente']);
			// $objoNotBold->getFont()->setBold(false);
			// $objoNotBold->getFont()->setSize(13);
			// $objRichText->createText($params['NombreCliente']);
			

			// $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($filtrosFormat);
			// $objPHPExcel->getActiveSheet()->SetCellValue('A2', "EMPRESA: ");
			// $objPHPExcel->getActiveSheet()->SetCellValue('A2', "EMPRESA: ".$params['NombreCliente']);
			// $objPHPExcel->getActiveSheet()->SetCellValue('A2', $objRichText);

			// $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($filtrosFormat);
			// $objPHPExcel->getActiveSheet()->SetCellValue('B2', $params['NombreCliente']);
			
			$rowCount = 4;

            $objPHPExcel->getActiveSheet()->getStyle('A4:F4')->applyFromArray($encabezado);
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(40);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            
			
			// NOMBRE CLIENTE, RFC, FECHA ULT. REGISTRO, MES DE OPERACIÓN, AÑO DE OPERACIÓN, SALDO, CONSUMO CALCULADO, SALDO 
            $objPHPExcel->getActiveSheet()->SetCellValue('A'.$rowCount, "CODIGO")
							  ->SetCellValue('B'.$rowCount, "DESCRIPCION")
							  ->SetCellValue('C'.$rowCount, "LINEA")
							  ->SetCellValue('D'.$rowCount, "SUCURSAL")
							  ->SetCellValue('E'.$rowCount, "VENTAS")
							  ->SetCellValue('F'.$rowCount, "EXISTENCIA");
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
							  ->SetCellValue('B'.$rowCount, $value['descripcion'])
							  ->SetCellValue('C'.$rowCount, $value['nombre_linea'])
							  ->SetCellValue('D'.$rowCount, $value['nombre_sucursal'])
							  ->SetCellValue('E'.$rowCount, $value['ventas'])
							  ->SetCellValue('F'.$rowCount, $value['stock']);
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
							  // ->SetCellValue('B'.($rowCount+2), "xxxxx")
							  // ->SetCellValue('B'.($rowCount+3), "xxxx")
							  // ->SetCellValue('B'.($rowCount+4), "xxxx")
							  // ->SetCellValue('B'.($rowCount+5), "xxxxx");

			// $objPHPExcel->getActiveSheet()->SetCellValue('D'.($rowCount+1), "__________________________________")
							  // ->SetCellValue('D'.($rowCount+2), "xxxxx")
							  // ->SetCellValue('D'.($rowCount+3), "xxxxx")
							  // ->SetCellValue('D'.($rowCount+4), "xxxxx")
							  // ->SetCellValue('D'.($rowCount+5), "xxxxx");	

			$objPHPExcel->getActiveSheet()->setTitle($nombreReporte);
			$objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
			header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
			header("Content-Disposition: attachment; filename=\"Reporte de Ventas Excel ".$fechaNueva.".xlsx\"");
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