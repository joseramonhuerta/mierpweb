<?php
//require(dirname(__FILE__).'/fpdf.php');
include_once "eko_framework/includes/fpdf.php";
include_once "eko_framework/app/controllers/malmacen.php";
class KardexDeMovimientosPDF extends TCPDF {
	public function Header() {
        // Logo
		$timeInicial=strtotime($this->params['fInicial']);
		$dia=date('d',$timeInicial);
		$mes=date('m',$timeInicial);
		$a�o=date('Y',$timeInicial);
		$fInicial=$dia.' DE '.nombreDelMes($mes)." DEL $a�o";
		//------------------------------------------------$timeInicial=strtotime($this->params['fInicial']);
		$timeFinal=strtotime( $this->params['fFinal'] );
		
		$dia=date('d',$timeFinal);
		$mes=date('m',$timeFinal);
		$a�o=date('Y',$timeFinal);
		$fFinal=$dia.' DE '.nombreDelMes($mes)." DEL $a�o";
		//---------------------------------------------------------	

		if($this->params['idAlmacen']==0){
			$nombreAlmacen = formatearTexto("Todos los almacenes");
		} else {
			$model = new Model();
			$datos = $model->select("SELECT DesAlm FROM cat_almacenes WHERE IDAlm = ".$this->params['idAlmacen']);
			$nombreAlmacen = formatearTexto($datos[0]['DesAlm']);
		}
		
        $image_file = K_PATH_IMAGES.'/logos/puma.jpg';
       // $this->Image($image_file,$this->lMargin,15, 25,'', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
		$x=$this->getX();
		$this->setY(10);
		$this->setX($x);	
		$corp=$_SESSION['NomCor'];
        $this->Cell(0, 0, $corp, 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$y=$this->getY();
		$this->setY($y);
		$this->setX($x);
		 $this->SetFont('helvetica', '', 8);
		$this->Cell(0, 0, 'REPORTE DE EXISTENCIAS DE '.$nombreAlmacen, 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$this->setX($x);
		$this->Cell(0, 0, "PERIODO DEL $fInicial A $fFinal", 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$this->setX($x);
		$this->Cell(0, 0, 'MATERIALES: TODOS LOS MATERIALES', 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$y=$this->getY();
		//$this->Line(0,$y,$x+400,$y); 
		$this->setLineWidth(.7);
		$this->Line($this->lMargin,$y,266,$y); 			
		$y=$this->getY();
		$this->yHeader=$y+.5;	
	//	$this->setY($this->yHeader+5);
		
		//-------------------------------------------------
		$this->imprime_encabezado_de_tabla($this->header);		
		//$this->Line(0,$y,$x+400,$y); 	
		$this->setLineWidth(.2);
		$this->Line($this->lMargin,$y,266,$y); 			
		$this->Ln();		
		$y=$this->getY()+1;		
		//$this->Line(0,$y,$x+400,$y); 	
		$this->Line($this->lMargin,$y,266,$y); 			
		$this->yDatos=$this->getY()+3;
    }
	
	function configTabla(){
		$this->header=array(
			array(
				'header'=>'C�DIGO ',
				'width'=>20,
				'dataindex'=>'codigo',
				'align'=>'R'
			),
			array(
				'header'=>' DESCRIPCI�N',
				'width'=>58,
				'dataindex'=>'descr',
				'align'=>'L',
				'type'=>'string'
			),
			array(
				'header'=>'INICIAL',
				'width'=>20,
				'dataindex'=>'inicial',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'ENTRADA',
				'width'=>20,
				'dataindex'=>'entrada',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'SALIDA',
				'width'=>20,
				'dataindex'=>'salida',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'FINAL ',
				'width'=>20,
				'dataindex'=>'final',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'UNIDAD',
				'width'=>20,
				'dataindex'=>'unidad',
				'align'=>'L',
				'type'=>'string'
			),
			array(
				'header'=>'�LTIMO ',
				'width'=>26,
				'dataindex'=>'ultimo',
				'align'=>'R',
				'type'=>'moneda'
			),
			array(
				'header'=>'PROMEDIO ',
				'width'=>26,
				'dataindex'=>'promedio',
				'align'=>'R',
				'type'=>'moneda',
				'fill'=>0			),
			array(
				'header'=>'TOTAL',
				'width'=>26,
				'dataindex'=>'total',
				'align'=>'R',
				'type'=>'moneda'
			)
		);
		//-----------------------------------------------------------------------/
			
	}
	function KardexDeMovimientosPDF($orientation='LANDSCAPE', $unit='mm', $format='LETTER', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false){
		// set document information
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$pdf=$this;
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('UPC Corporate');
		$pdf->SetTitle('KARDEX');
		$pdf->SetSubject('KARDEX');
		$pdf->SetKeywords('TCPDF, PDF, kardex, upccorporate, upctechnologies','inventarios');

		/*// set default header data
		$pdf->SetHeaderData('/logos/puma.jpg', PDF_HEADER_LOGO_WIDTH,"PUMA Corporate","KARDEX 1.0");

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));*/

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		//set margins
		//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		$this->SetMargins(15, 42, 7);
		
		$this->setHeaderMargin(30);	
	}
	
	
	function imprimir($path,$mode='I',$params){
	
		$this->params=$params;
		$this->configTabla();
		$this->AddPage();
		//-------------------------------------------------------------------------------------------
		$model		=new Model();		
		$fInicial	=$this->params["fInicial"];
		$fFinal		=$this->params["fFinal"];

		/* Antonio: Almacenes a los que tiene permiso el usuario */
		$IDUsu = $_SESSION['Auth']['User']['IDUsu'];
		$IDEmp = $_SESSION['Auth']['User']['IDEmp'];   
		$IDSuc = $_SESSION['Auth']['User']['IDSuc'];
		$admin = $_SESSION['Auth']['User']['AdminUsu'];
		if($admin==0){
			$sql="SELECT idAlm as ID, IF (KEYID is null,0,1) as permiso
			FROM cat_almacenes 
			LEFT JOIN cat_usuarios_privilegios ON KEYUsuPriv=$IDUsu AND KEYID=IDAlm AND Origen='ALM'
			LEFT JOIN cat_empresas ON IDEmp=KEYEmpAlm
			LEFT JOIN cat_sucursales ON IDSuc=KEYSucAlm
			WHERE KEYEmpAlm='$IDEmp' AND StatAlm='A'
			HAVING permiso=1";			
		} else {
			$sql="SELECT idAlm as ID
			FROM cat_almacenes 
			LEFT JOIN cat_empresas ON IDEmp=KEYEmpAlm
			LEFT JOIN cat_sucursales ON IDSuc=KEYSucAlm
			WHERE KEYEmpAlm='$IDEmp' AND StatAlm='A'";
		}
				
		$almacenes = $model->select($sql);
		$idsAlmacenes = array();
		foreach($almacenes as $almacen)
			$idsAlmacenes[] = $almacen['ID'];
		$idsAlmacenes = implode(",", $idsAlmacenes);

		if(isset($_GET['idAlmacen']))
			if($_GET['idAlmacen']==0)
				$filtroAlmacen = "";
			else
				$filtroAlmacen = "AND KEYAlmacenKar = ".$_GET['idAlmacen'];

		//-------------------------------------------------------------------------------------------
		// Revisar esta consulta, creo que es necesario optimizar, talvez estableciendo un limit x,y.
		//-------------------------------------------------------------------------------------------
		$sqlReporte="SELECT KEYAlmacenKar,KEYProductoKar,MAX(FechaKar) FechaKar, DesProCla AS Clasificacion 
		FROM inventarios_kardex
		LEFT JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar

		LEFT JOIN cat_productos ON IDProd=KEYProductoKar
		LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
		LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

		WHERE FechaKar<='$fFinal' AND FechaCancDet IS NULL /*AND KEYDetCancelado_MovDet=0*/ -- AND KEYTipoMovimientoDet!=3 
		AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen GROUP BY KEYAlmacenKar, KEYProductoKar
		ORDER BY Clasificacion ASC, DescProd ASC, IDProd;";//Selecciona el ultimo registro con fecha menor o igual a la fecha superior del rango especificado
		//-------------------------------------------------------------------------------------------
		
		$arrUltimos=$model->select($sqlReporte); //die($sqlReporte);
		$data=array();
		
		/* ----------------------------------------------------- */

		//En la consulta anterior solo se obtuvo la fecha, el almacen y el producto, con esos valores obtendr� los demas registros
		foreach( $arrUltimos as $rec ){ 
			$fecha	=$rec['FechaKar'];
			$IDProd	=$rec['KEYProductoKar'];
			$IDAlm	=$rec['KEYAlmacenKar'];
			$sql="SELECT IDProd codigo, DescProd descr,DescUni unidad, SaldoKar as final,PromedioKar promedio,
			PromedioKar * SaldoKar as total, 
			IFNULL( IFNULL(CompraKar/CantidadEntradaKar,VentaKar/CantidadSalidaKar),0) as ultimo, FechaKar, DesProCla AS Clasificacion,

			(SELECT GROUP_CONCAT(Serie ORDER BY Serie ASC SEPARATOR '\n   ')
			FROM
				(SELECT 1 AS Cat, Serie
					FROM (
						SELECT Serie, 1 AS num
						FROM inventarios_kardex_detalle 
						JOIN inventarios_kardex ON IDKar = IDKardex
						JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar
						WHERE IDProducto = $IDProd AND VentaKar IS NULL AND FechaKar <= '$fecha' AND KEYAlmacenKar = $IDAlm AND FechaCancDet IS NULL
						UNION ALL
						SELECT Serie, -1 AS num
						FROM inventarios_kardex_detalle 
						JOIN inventarios_kardex ON IDKar = IDKardex
						JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar
						WHERE IDProducto = $IDProd AND CompraKar IS NULL AND FechaKar <= '$fecha' AND KEYAlmacenKar = $IDAlm AND FechaCancDet IS NULL
					) AS t
					GROUP BY Serie
					HAVING SUM(num) > 0
				) AS r
				GROUP BY Cat
			) AS Series

			FROM inventarios_kardex 

			LEFT JOIN cat_productos ON IDProd=KEYProductoKar 
			LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd			
			LEFT JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar
			LEFT JOIN inventarios_kardex_detalle ON IDProducto = IDProd
			WHERE FechaKar='$fecha' AND KEYAlmacenKar=$IDAlm AND KEYProductoKar=$IDProd  AND FechaCancDet IS NULL  /*AND KEYDetCancelado_MovDet=0*/
			GROUP BY IDProd
			ORDER BY Clasificacion ASC, DescProd ASC, FechaKar ASC;";
			//===============================================================================================;			
			$arrData	=$model->select( $sql ); 
			$registro	=$arrData[0];
			//-----------------------------------------------------------------------------------------------
			//se obtiene el ultimo costo de compra para este producto en este almacen
			//-----------------------------------------------------------------------------------------------/
			//con la siguiente consulta se obtienen las entadas y salidas en ese periodo
			$sqlIO="SELECT 
			SUM(CantidadEntradaKar) as entrada,
			SUM(CantidadSalidaKar) as salida
			FROM inventarios_kardex 
		    LEFT JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar			
			WHERE KEYProductoKar=$IDProd AND KEYAlmacenKar=$IDAlm  AND FechaKar BETWEEN '$fInicial' AND '$fFinal'  -- AND KEYTipoMovimientoDet!=3
			-- AND FechaCancDet IS NULL AND KEYDetCancelado_MovDet=0
			GROUP BY KEYProductoKar;";
			$arrIO=$model->select($sqlIO);			
			//-----------------------------------------------------------------------------------------------/
			if ( empty($arrIO) ){
				$registro['entrada']=0;
				$registro['salida']	=0;
				$registro['inicial']=$registro['final'];
			}else{
				$registro['entrada']=$arrIO[0]['entrada'];
				$registro['salida']	=$arrIO[0]['salida'];
				$registro['inicial']=$registro['final']-($registro['entrada']-$registro['salida']);
				
				// echo $sqlIO."<br>";
				// echo "Inicial: $registro[inicial]";
				// echo "Entrada: $registro[entrada]";
				// echo "Salida: $registro[salida]";
				// echo "Final: $registro[final]";
				// echo "<br>";
			}			
			//-----------------------------------------------------------------------------------------------/
			if ( isset($data[$IDProd]) ){
				//� Que significa el cumplimiento de esta condicion?.
				//El reporte es por producto, por almacen, se estan acumulando totales.
				$data[$IDProd]['total']=floatval($data[$IDProd]['total'])+floatval($registro['total']);
				$data[$IDProd]['final']=floatval($data[$IDProd]['final'])+floatval($registro['final']);				
				if ($data[$IDProd]['final']==0){
					$promedio=0;
				}else{
					$promedio=floatval($data[$IDProd]['total'])/floatval($data[$IDProd]['final']);
				}				
				$data[$IDProd]['promedio']=$promedio;
				$data[$IDProd]['inicial']=floatval($data[$IDProd]['inicial'])+floatval($registro['inicial']);
				$data[$IDProd]['entrada']=floatval($data[$IDProd]['entrada'])+floatval($registro['entrada']);
				$data[$IDProd]['salida']=floatval($data[$IDProd]['salida'])+floatval($registro['salida']);
				if ($registro['FechaKar'] > $data[$IDProd]['FechaKar']){
					$data[$IDProd]['ultimo']=$registro['ultimo'];
				}
				
				//fix bug concatenar las series faltantes
				$data[$IDProd]['Series'] .= "\n   ".$registro['Series'];
			}else{
				//� Que significa el cumplimiento de esta condicion?
				//es la primera vez que se encuentra no es necesario acumular
				$data[$IDProd]=$registro;				
			}
		}
				// exit;
		
		$datos=array();
		foreach($data as $dato){
			$datos[]=$dato;	// �Porqu� as�?
		}
		// echo "<br>";
		// print_r($datos);		
		// exit;
		//-----------------------------------------------------------------------/				
		// print colored table	
		$this->ColoredTable($this->header, $datos);		
		//exit;		
		//-----------------------------------------------------------------------/
		$this->Output($path, $mode);

	}

	
	public function imprime_encabezado_de_tabla($header){
	   $this->setY($this->yHeader);
	   //-------------------------------------------
	    $this->SetLineWidth(.1);
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(0, 0,0);
		$h=1;
		
		$border=0;
	    $this->Cell(83, $h, "",0, 0, "", 0);
		$this->SetFillColor(220, 220, 220);
		$this->SetDrawColor(0, 0, 0);
		$this->SetFont('Courier', '', 10);
		$this->Cell(87, $h, "EXISTENCIA", 0, 0, "C", 1);
		$this->Cell(19, $h, "",0, 0, "C", 0);
		$this->Cell(67, $h, "$ COSTO", 0, 0, "C", 1);
		$this->Ln();
		//----------------------------------------------------------
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {
			$value=UTF8_ENCODE($header[$i]['header']);
			$align=isset($header[$i]['align'])? $header[$i]['align']:'L';
			$fill=isset($header[$i]['fill'])? $header[$i]['fill'] : 0;
            $this->Cell($header[$i]['width'], $h, $value, $border, 0, $align, $fill);
        }        
	}
	
    public function ColoredTable($header,$data) {
       
		 $num_headers = count($header);
		$this->setY($this->yDatos);
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
		//$this->SetLineWidth(2);
        $this->SetTextColor(0);

		$this->SetFont('Courier', '', 8);
        // Data
        $fill = 0;
		$costoTotal=0;
		for ($recIndex=0; $recIndex<sizeof($data); $recIndex++){
			//if ($data[$recIndex]['inicial']==0 && $data[$recIndex]['entrada']==0 && $data[$recIndex]['salida']==0 && $data[$recIndex]['final']==0)continue;
			for($colIndex = 0; $colIndex < $num_headers; $colIndex++) {
				$value=isset($data[$recIndex][$header[$colIndex]['dataindex']])? $data[$recIndex][$header[$colIndex]['dataindex']] : '';
				if (isset($header[$colIndex]['type'])){
					switch($header[$colIndex]['type']){
						case 'moneda':$value=formatearMoneda($value);break;
						case 'cantidad':$value=formatearCantidad($value);break;
						case 'string':$value=formatearTexto($value,true);break;						
					}
				}
				$align=$header[$colIndex]['align'];
				if (strlen($value)>$header[$colIndex]['width']){
					$value=substr($value,0,$header[$colIndex]['width']);
				}

				if($colIndex==1){
					$value = $value."\n".$data[$recIndex]['Clasificacion'];

					// Antonio: Para imprimir las series en el reporte...
					$x1=$this->getX();
					$y1=$this->getY();
					if($this->params['porSerie']==true)
						$value1 = $value."\n   ".$data[$recIndex]['Series'];
					else
						$value1 = $value;
					$this->setXY($x1+$header[1]['width'], $y1);
				} else {
					// $this->Cell($header[$colIndex]['width'], 0, $value, '', 0, $align, $fill,'',1);				
					$this->Cell($header[$colIndex]['width'], 0, $value, '', 0, $align, $fill,'',1);
				}
			}
			$this->setX($x1);
			$this->MultiCell($header[1]['width'], 0, formatearTexto($value1)."\n");

			$costoTotal+=floatval($data[$recIndex]['total']);
			$this->acumulado=$costoTotal;
			$this->Ln(3);
			$y=$this->getY();
			/*if (floatval($y)>185){ ANT!
				$this->addPage();
			}*/
			$y=$this->getY();
			if ($y<$this->yDatos){
				$this->setY($this->yDatos);
			}
		}
		$this->impresionTerminada=true;
		$y=$this->getY()+1;		
		$this->SetDrawColor(0, 0,0);
		$this->SetFillColor(0, 0,0);
		//$this->Line(0,$y,400,$y); 
		$this->setLineWidth(.2);
		$this->Line($this->lMargin,$y,266,$y); 			
		$this->Ln(3);
		$this->SetFont('Courier', '', 8);
		$this->Cell(226, 0,"COSTO TOTAL DEL INVENTARIO:", '', 0, "R", $fill);	
		$this->SetFont('', 'B');
		$this->Cell(30, 0,formatearMoneda($costoTotal), '', 0, "R", $fill);	
		//$this->Cell(250, 0,"COSTO TOTAL DEL INVENTARIO:".formatearMoneda($costoTotal), '', 0, $align, $fill);	
    }
	function Footer(){
		$this->setLineWidth(.7);		
		$this->SetFont('', '',8);
		if (empty($this->impresionTerminada)){
			$y=$this->getY()-14;
			//$this->Line(0,$y,400,$y);
			$this->setY($y);
			$this->Cell(220, 0,"Total acumulado:", '', 0, "R");	
			$this->SetFont('', 'B');
			$this->Cell(30, 0,formatearMoneda($this->acumulado), '', 0, "R", $fill);
			//$this->Cell(0, 0,"Total acumulado: $this->acumulado", '', 0, "R");	
			$this->ln();
			$y+=4;
		}else{
			$y=$this->getY()-10;
			$this->setY($y);
		}
		//$this->Line($this->lMargin,$y,400,$y);
		$this->Line($this->lMargin,$y,266,$y); 			
		$this->setY($y+1);
		$pageNumber=$this->PageNo();
		$fecha=date('d/m/Y H:i:s'); //;getFechaActual();
		$totPageAlias=$this->getAliasNbPages();
		$this->Cell(60, 0,UTF8_ENCODE("Fecha de impresi�n: $fecha"), '', 0, "L");	
		$this->Cell(0, 0,"Pagina $pageNumber/$totPageAlias", '', 0, "R");	
	}
}
?>