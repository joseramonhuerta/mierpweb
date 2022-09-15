<?php
/*
 *	Esta clase deberia analizar y preparar toda la informacion, dejarla lista para que el PDF imprima usando solamente ciclos
 * por el momento hay logica repartida en esta clase y en reporte_de_facturacion_pdf.php
 */
class ReporteFlujoEfectivoExcel{
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
		$fechaInicio=(empty($params['FechaIni'])) ?  '': $params['FechaIni'];
		$fechaFin=(empty($params['FechaFin'])) ?  '': $params['FechaFin'];
		$fechaInicio.=" 00:00:00"; 
		$fechaFin.=" 23:59:59";
		$fechaFinFiltro=(empty($params['FechaFin'])) ?  '': $params['FechaFin'];
		$perdidas_ganancias = $params['perdidas_ganancias'];
		$nombre_empresa=(empty($params['nombre_empresa'])) ?  'TODAS': $params['nombre_empresa'];
		$nombre_sucursal=(empty($params['nombre_sucursal'])) ?  'TODAS': $params['nombre_sucursal'];

		$params['nombre_empresa'] = $nombre_empresa;
		$params['nombre_sucursal'] = $nombre_sucursal;
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
				
				
				$query ="CALL spReporteFlujoEfectivo('$fechaInicio','$fechaFin',$idEmp,$idSuc, $perdidas_ganancias);";
				
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
		
			 print_r($preparados);
			
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
			
			if($params['perdidas_ganancias'] == 1)
				$nombreReporte = "Reporte Perdidas y Ganancias ";
			else
				$nombreReporte = "Reporte de Flujos de Efectivo ";

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

			$moneda = array(			    
			    'alignment' => array(
			        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT
			    )
			);

			$subtitulo = array(
				'font'  => array(
			        'bold' => true,
			        'size'  => 12,
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
			$objPHPExcel->getActiveSheet()->getStyle('B7:B51')->applyFromArray($moneda);
			
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
			
			$objPHPExcel->getActiveSheet()->SetCellValue('A2', 'EMPRESA');
			$objPHPExcel->getActiveSheet()->SetCellValue('B2', $params['nombre_empresa']);
			$objPHPExcel->getActiveSheet()->SetCellValue('A3', 'SUCURSAL');
			$objPHPExcel->getActiveSheet()->SetCellValue('B3', $params['nombre_sucursal']);
			
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
			
			$rowCount = 4;

            
            $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
            $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		
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
			$totalIngresos = 0;
			$totalEgresos = 0;
			foreach ($informes as $key => $value) {
				
				$ventas_tienda =  $value['ventas_tienda'];
				$cobranza_vendedores =  $value['cobranza_vendedores'];
				$ventas_remision =  $value['ventas_remision'];
				$anticipos_compras = $value['anticipos_compras'];
				$deudores_diversos = $value['deudores_diversos'];

				$totalIngresos = $ventas_tienda + $cobranza_vendedores + $ventas_remision + $anticipos_compras + $deudores_diversos;

				$pagos_proveedores =  $value['pagos_proveedores'];
				$pagos_rentas =  $value['pagos_rentas'];
				$pagos_sueldos =  $value['pagos_sueldos'];
				$pagos_imss_infonavit =  $value['pagos_imss_infonavit'];
				$pagos_impuestos =  $value['pagos_impuestos'];
				$pagos_paqueteria =  $value['pagos_paqueteria'];
				$pagos_servicios =  $value['pagos_servicios'];
				$pagos_papeleria =  $value['pagos_papeleria'];
				$pagos_productos_limpieza =  $value['pagos_productos_limpieza'];
				$pagos_mantenimiento_tiendas =  $value['pagos_mantenimiento_tiendas'];
				$pagos_mantenimiento_vehiculos =  $value['pagos_mantenimiento_vehiculos'];
				$pagos_gasolina =  $value['pagos_gasolina'];
				$pagos_despensa  =  $value['pagos_despensa'];
				$pagos_viaticos =  $value['pagos_viaticos'];
				$pagos_comida =  $value['pagos_comida'];
				$pagos_comisiones_bonos =  $value['pagos_comisiones_bonos'];
				$pagos_mermas =  $value['pagos_mermas'];
				$pagos_prestamos =  $value['pagos_prestamos'];
				$pagos_gastos_bancarios =  $value['pagos_gastos_bancarios'];
				$pagos_publicidad =  $value['pagos_publicidad'];
				$pagos_servicios_digitales =  $value['pagos_servicios_digitales'];
				$pagos_promotoria_eventos =  $value['pagos_promotoria_eventos'];
				$pagos_patrocinios =  $value['pagos_patrocinios'];
				$pagos_mobiliario =  $value['pagos_mobiliario'];
				$pagos_equipo_reparto =  $value['pagos_equipo_reparto'];
				$pagos_equipo_computo =  $value['pagos_equipo_computo'];
				$pagos_herramientas =  $value['pagos_herramientas'];
				$pagos_gastos_importacion =  $value['pagos_gastos_importacion'];
				$pagos_acreedores_diversos =  $value['pagos_acreedores_diversos'];
				$pagos_gastos_administrativos =  $value['pagos_gastos_administrativos'];

				$totalEgresos = $pagos_proveedores + $pagos_rentas + $pagos_sueldos + $pagos_imss_infonavit + $pagos_impuestos + $pagos_paqueteria +
								$pagos_servicios + $pagos_papeleria + $pagos_productos_limpieza + $pagos_mantenimiento_tiendas + $pagos_mantenimiento_vehiculos +
								$pagos_gasolina + $pagos_despensa + $pagos_viaticos + $pagos_comida + $pagos_comisiones_bonos + $pagos_mermas +
								$pagos_prestamos + $pagos_gastos_bancarios + $pagos_publicidad + $pagos_servicios_digitales + $pagos_promotoria_eventos +
								$pagos_patrocinios + $pagos_mobiliario + $pagos_equipo_reparto + $pagos_equipo_computo + $pagos_herramientas +
								$pagos_gastos_importacion + $pagos_acreedores_diversos + $pagos_gastos_administrativos;

				$colorBackGround = 'A3F3B2';
				$colorLetra = '359C33';
				if($value['SaldoCalculado'] < 1) {
					$colorBackGround = 'F59A9C';
					$colorLetra = '790000';
				} 
				/* INGRESOS*/
				
				$objPHPExcel->getActiveSheet()->getStyle('A6:B6')->applyFromArray($subtitulo);

				$objPHPExcel->getActiveSheet()->mergeCells('A6:B6');
				$objPHPExcel->getActiveSheet()->SetCellValue('A6', 'INGRESOS');
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A8', 'VENTAS TIENDAS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B8','$ '.number_format($ventas_tienda, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A9', 'COBRANZA VENDEDORES');
				$objPHPExcel->getActiveSheet()->SetCellValue('B9','$ '.number_format($cobranza_vendedores, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A10', 'VENTAS DE REMISION');
				$objPHPExcel->getActiveSheet()->SetCellValue('B10','$ '.number_format($ventas_remision, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A11', 'ANTICIPO VENDEDORES');
				$objPHPExcel->getActiveSheet()->SetCellValue('B11','$ '.number_format($anticipos_compras, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A12', 'DEUDORES DIVERSOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B12','$ '.number_format($deudores_diversos, 2, '.', ','));
						  
				$objPHPExcel->getActiveSheet()->SetCellValue('A14', 'TOTAL INGRESOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B14','$ '.number_format($totalIngresos, 2, '.', ','));	


				/* EGRESOS */		
				$objPHPExcel->getActiveSheet()->getStyle('A16:B16')->applyFromArray($subtitulo);
				$objPHPExcel->getActiveSheet()->mergeCells('A16:B16');
				$objPHPExcel->getActiveSheet()->SetCellValue('A16', 'EGRESOS');
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A18', 'PROVEEDORES');
				$objPHPExcel->getActiveSheet()->SetCellValue('B18','$ '.number_format($pagos_proveedores, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A19', 'RENTAS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B19','$ '.number_format($pagos_rentas, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A20', 'SUELDOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B20','$ '.number_format($pagos_sueldos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A21', 'IMSS-INFONAVIT');
				$objPHPExcel->getActiveSheet()->SetCellValue('B21','$ '.number_format($pagos_imss_infonavit, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A22', 'IMPUESTOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B22','$ '.number_format($pagos_impuestos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A23', 'PAQUETERIAS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B23','$ '.number_format($pagos_paqueteria, 2, '.', ','));								

				$objPHPExcel->getActiveSheet()->SetCellValue('A24', 'SERVICIOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B24','$ '.number_format($pagos_servicios, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A25', 'PAPELERIA');
				$objPHPExcel->getActiveSheet()->SetCellValue('B25','$ '.number_format($pagos_papeleria, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A26', 'PRODUCTOS DE LIMPIEZA');
				$objPHPExcel->getActiveSheet()->SetCellValue('B26','$ '.number_format($pagos_productos_limpieza, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A27', 'MANTENIMIENTO TIENDAS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B27','$ '.number_format($pagos_mantenimiento_tiendas, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A28', 'MANTENIMIENTO VEHICULOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B28','$ '.number_format($pagos_mantenimiento_vehiculos, 2, '.', ','));								

				$objPHPExcel->getActiveSheet()->SetCellValue('A29', 'GASOLINA');
				$objPHPExcel->getActiveSheet()->SetCellValue('B29','$ '.number_format($pagos_gasolina, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A30', 'DESPENSA');
				$objPHPExcel->getActiveSheet()->SetCellValue('B30','$ '.number_format($pagos_despensa, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A31', 'VIATICOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B31','$ '.number_format($pagos_viaticos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A32', 'COMIDA');
				$objPHPExcel->getActiveSheet()->SetCellValue('B32','$ '.number_format($pagos_comida, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A33', 'COMISIONES Y BONOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B33','$ '.number_format($pagos_comisiones_bonos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A34', 'MERMAS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B34','$ '.number_format($pagos_mermas, 2, '.', ','));							

				$objPHPExcel->getActiveSheet()->SetCellValue('A35', 'PRESTAMOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B35','$ '.number_format($pagos_prestamos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A36', 'GASTOS BANCARIOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B36','$ '.number_format($pagos_gastos_bancarios, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A37', 'PUBLICIDAD');
				$objPHPExcel->getActiveSheet()->SetCellValue('B37','$ '.number_format($pagos_publicidad, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A38', 'SERVICIOS DIGITALES');
				$objPHPExcel->getActiveSheet()->SetCellValue('B38','$ '.number_format($pagos_servicios_digitales, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A39', 'PROMOTORIA EVENTOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B39','$ '.number_format($pagos_promotoria_eventos, 2, '.', ','));					

				$objPHPExcel->getActiveSheet()->SetCellValue('A40', 'PATROCINIOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B40','$ '.number_format($pagos_patrocinios, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A41', 'MOBILIARIO');
				$objPHPExcel->getActiveSheet()->SetCellValue('B41','$ '.number_format($pagos_mobiliario, 2, '.', ','));
				
				$objPHPExcel->getActiveSheet()->SetCellValue('A42', 'EQUIPO DE REPARTO');
				$objPHPExcel->getActiveSheet()->SetCellValue('B42','$ '.number_format($pagos_equipo_reparto, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A43', 'EQUIPO DE COMPUTO');
				$objPHPExcel->getActiveSheet()->SetCellValue('B43','$ '.number_format($pagos_equipo_computo, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A44', 'HERRAMIENTAS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B44','$ '.number_format($pagos_herramientas, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A45', 'GASTOS DE IMPORTACION');
				$objPHPExcel->getActiveSheet()->SetCellValue('B45','$ '.number_format($pagos_gastos_importacion, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A46', 'ACREEDORES DIVERSOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B46','$ '.number_format($pagos_acreedores_diversos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A47', 'GASTOS ADMINISTRATIVOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B47','$ '.number_format($pagos_gastos_administrativos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A49', 'TOTAL EGRESOS');
				$objPHPExcel->getActiveSheet()->SetCellValue('B49','$ '.number_format($totalEgresos, 2, '.', ','));

				$objPHPExcel->getActiveSheet()->SetCellValue('A51', 'GANANCIA/PERDIDA');
				$objPHPExcel->getActiveSheet()->SetCellValue('B51','$ '.number_format(($totalIngresos - $totalEgresos), 2, '.', ','));


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
			header("Content-Disposition: attachment; filename=\"".$nombreReporte." ".$fechaNueva.".xlsx\"");
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