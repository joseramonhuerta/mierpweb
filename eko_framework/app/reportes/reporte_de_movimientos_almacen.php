<?php
//require(dirname(__FILE__).'/fpdf.php');
include_once "eko_framework/includes/fpdf.php";
include_once "eko_framework/app/controllers/malmacen.php";
class ReporteDeMovimientosPDF extends TCPDF {
	public function Header() {
        // Logo
		$this->setLineWidth(.7);
		$timeInicial=strtotime($this->params['fInicial']);
		$dia=date('d',$timeInicial);
		$mes=date('m',$timeInicial);
		$año=date('Y',$timeInicial);
		$fInicial=$dia.' DE '.nombreDelMes($mes)." DEL $año";
		//------------------------------------------------$timeInicial=strtotime($this->params['fInicial']);
		$timeFinal=strtotime( $this->params['fFinal'] );		
		$dia=date('d',$timeFinal);
		$mes=date('m',$timeFinal);
		$año=date('Y',$timeFinal);
		$fFinal=$dia.' DE '.nombreDelMes($mes)." DEL $año";
		//---------------------------------------------------------		

		if($this->params['idAlmacen']==0){
			$nombreAlmacen = formatearTexto("Todos los almacenes");
		} else {
			$model = new Model();
			$datos = $model->select("SELECT DesAlm FROM cat_almacenes WHERE IDAlm = ".$this->params['idAlmacen']);
			$nombreAlmacen = formatearTexto($datos[0]['DesAlm']);
		}

        //$image_file = K_PATH_IMAGES.'/logos/BasculasSantana.png';
       // $this->Image($image_file,$this->lMargin,15, 25,'', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
        // Set font
        $this->SetFont('helvetica', 'B', 20);
        // Title
		$x=$this->getX();
		$this->setY(15);
		$this->setX($x);		
		$corp=$_SESSION['NomCor'];
        $this->Cell(0, 0, $corp, 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$y=$this->getY();
		//$this->setY($y-2);
		$this->setX($x);
		 $this->SetFont('helvetica', '', 8);
		$this->Cell(0, 0, 'REPORTE DE MOVIMIENTOS POR ALMACEN Y COSTOS DE '.$nombreAlmacen, 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$this->setX($x);
		$this->Cell(0, 0, "PERIODO DEL $fInicial A $fFinal", 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$this->setX($x);
		$this->Cell(0, 0, 'MATERIALES: TODOS LOS MATERIALES', 0, false, 'L', 0, '', 1, false, 'M', 'M');
		$this->ln();
		$y=$this->getY();
		//$this->Line(0,$y,$x+400,$y); 
		$y=$this->getY();
		$this->yHeader=$y+1;	
	//	$this->setY($this->yHeader+5);		
		//-------------------------------------------------
		$this->imprime_encabezado_de_tabla($this->header);		
		//$this->Line(0,$y,$x+400,$y); 	
		$this->Ln();		
		$y=$this->getY()+1;		
		$this->SetDrawColor(0, 0,0);
		$this->SetFillColor(0, 0,0);
		$ancho=$this->wPt-$this->lMargin-$this->rMargin;

		$this->Line($this->lMargin,$y,266,$y); 	
		$this->yDatos=$this->getY()+3;
    }
	
	function configTabla(){
		$this->header=array(
			array(
				'header'=>' PRODUCTO',
				'width'=>60,
				'dataindex'=>'DescProd',
				'align'=>'L',
				'type'=>'string'
			),
			array(
				'header'=>'FECHA MOV. ',
				'width'=>30,
				'dataindex'=>'FechaKar',
				'align'=>'L'
			),
			array(
				'header'=>'REFERENCIA ',
				'width'=>20,
				'dataindex'=>'ReferenciaInventarioDet',
				'align'=>'L',
			),
			array(
				'header'=>'ENTRADA ',
				'width'=>20,
				'dataindex'=>'entrada',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'SALIDAS ',
				'width'=>20,
				'dataindex'=>'salida',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'ACUMULADO ',
				'width'=>20,
				'dataindex'=>'SaldoKar',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'   UNIDAD',
				'width'=>20,
				'dataindex'=>'DescUni',
				'align'=>'L',
				'type'=>'string'
			),
			array(
				'header'=>'PROMEDIO ',
				'width'=>30,
				'dataindex'=>'PromedioKar',
				'align'=>'R',
				'type'=>'moneda',
				'fill'=>1
			),
			array(
				'header'=>'TOTAL',
				'width'=>30,
				'dataindex'=>'total',
				'align'=>'R',
				'type'=>'moneda'
			)
		);
		//-----------------------------------------------------------------------/			
	}
	
	function ReporteDeMovimientosPDF($orientation='LANDSCAPE', $unit='mm', $format='LETTER', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false){
		// set document information
		parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
		$pdf=$this;

		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('UPC Corporate');
		$pdf->SetTitle('KARDEX');
		$pdf->SetSubject('KARDEX');
		$pdf->SetKeywords('TCPDF, PDF, kardex, upccorporate, upctechnologies','inventarios');

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		//set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		//set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
		//$this->setLineWidth(.5);
		
		$this->SetMargins(15, 42, 7);
		$this->setHeaderMargin(30);
	}
	function getModelObject(){
		if (empty($this->model)){
			$this->model=new Model();
		}
		return $this->model;
	}
	function getDatos(){
		//---------------------------------------------------
		$model=$this->getModelObject();

		$fInicial=$this->params["fInicial"];
		$fFinal=$this->params["fFinal"];
		// $porSerie=$this->params["porSerie"];

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

		if($this->params["porSerie"]){ // REPORTE POR SERIES... (ANTONIO)
			// $sqlReporte="SELECT DescProd producto,KEYAlmacenKar,DesAlm,DATE_FORMAT( FechaKar,'%d/%m/%Y %H:%i') as FechaKar,FechaKar Fecha,KEYReferenciaDet,
			// if (KEYMovimientoDet>0,concat(CodMovAlm,'-',ReferenciaInventarioDet),ReferenciaInventarioDet ) as ReferenciaInventarioDet,
			// CantidadEntradaKar as entrada,
			// CantidadSalidaKar  as salida,
			// DescUni,0 total, GROUP_CONCAT(Serie ORDER BY Serie ASC SEPARATOR '\n   ') AS Series,
			// IDKar,KEYInvDetKar KEYESDetalleKar,KEYAlmacenKar,KEYProductoKar,CompraKar,VentaKar,SaldoKar,PromedioKar, DesProCla AS Clasificacion  
			// FROM inventarios_kardex k

			// LEFT JOIN cat_productos ON IDProd=KEYProductoKar
			// LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			// LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			// LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
			// LEFT JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar
			// LEFT JOIN inventarios_movimientos ON IDInv=KEYReferenciaDet		
			// LEFT JOIN cat_almacenes_movimientos ON IDMovAlm=KEYMovimientoInv
			// LEFT JOIN cat_almacenes ON IDAlm=KEYAlmacenKar 
			// LEFT JOIN inventarios_kardex_detalle ON IDKar = IDKardex 
			// WHERE FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND StatusInv='A' AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen 
			// GROUP BY IDKar 
			// ORDER BY KEYAlmacenKar ASC, Clasificacion ASC, DescProd ASC, Fecha ASC";
			
			//CONSULTA DE TOÑITO
			/*
			$sqlReporte="SELECT DescProd producto,KEYAlmacenKar,DesAlm,DATE_FORMAT( FechaKar,'%d/%m/%Y %H:%i') as FechaKar,FechaKar Fecha,KEYReferenciaDet,
			if (KEYMovimientoDet>0,concat(CodMovAlm,'-',ReferenciaInventarioDet),ReferenciaInventarioDet ) as ReferenciaInventarioDet,
			CantidadEntradaKar as entrada,
			CantidadSalidaKar  as salida,
			DescUni,0 total, GROUP_CONCAT(Serie ORDER BY Serie ASC SEPARATOR '\n   ') AS Series,
			IDKar,KEYInvDetKar KEYESDetalleKar,KEYAlmacenKar,KEYProductoKar,CompraKar,VentaKar,SaldoKar,PromedioKar, DesProCla AS Clasificacion 
			FROM inventarios_kardex k

			LEFT JOIN cat_productos ON IDProd=KEYProductoKar
			LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
			LEFT JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar
			LEFT JOIN inventarios_movimientos ON IDInv=KEYReferenciaDet		
			LEFT JOIN cat_almacenes_movimientos ON IDMovAlm=KEYMovimientoInv
			LEFT JOIN cat_almacenes ON IDAlm=KEYAlmacenKar
			LEFT JOIN inventarios_kardex_detalle ON IDKar = IDKardex 
			WHERE FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND ISNULL(FechaCancDet) AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen 
			GROUP BY IDKar 
			ORDER BY KEYAlmacenKar ASC, Clasificacion ASC, DescProd ASC, Fecha ASC";
			*/
			//CONSULTA ANGELICA			
			$sqlReporte = "SELECT DescProd producto,KEYAlmacenKar,DesAlm,DATE_FORMAT( FechaKar,'%d/%m/%Y %H:%i') AS FechaKar,FechaKar Fecha,KEYReferenciaDet,
			IF (KEYMovimientoDet>0,CONCAT(CodMovAlm,'-',ReferenciaInventarioDet),ReferenciaInventarioDet ) AS ReferenciaInventarioDet,
			CantidadEntradaKar AS entrada,
			CantidadSalidaKar  AS salida,
			DescUni,0 total, GROUP_CONCAT(DISTINCT Serie ORDER BY Serie ASC SEPARATOR '\n   ') AS Series,
			IDKar,KEYInvDetKar KEYESDetalleKar,KEYAlmacenKar,KEYProductoKar,CompraKar,VentaKar,SaldoKar,PromedioKar, DesProCla AS Clasificacion,
			(SELECT SaldoKar FROM inventarios_kardex kk
			LEFT JOIN inventarios_movimientos_detalle InvDet ON InvDet.IDInventarioDet= kk.KEYInvDetKar
			WHERE kk.KEYProductoKar = k.KEYProductoKar AND kk.FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND ISNULL(InvDet.FechaCancDet) AND  kk.KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen AND kk.KEYAlmacenKar = k.KEYAlmacenKar
			ORDER BY FechaKar DESC
			LIMIT 1) AS SaldoFinalPeriodo
			FROM inventarios_kardex k

			LEFT JOIN cat_productos ON IDProd=KEYProductoKar
			LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
			LEFT JOIN inventarios_movimientos_detalle InvDet ON InvDet.IDInventarioDet=k.KEYInvDetKar
			LEFT JOIN inventarios_movimientos ON IDInv=KEYReferenciaDet		
			LEFT JOIN cat_almacenes_movimientos ON IDMovAlm=KEYMovimientoInv
			LEFT JOIN cat_almacenes ON IDAlm=KEYAlmacenKar
			LEFT JOIN inventarios_kardex_detalle ON IDKar = IDKardex 
			WHERE FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND (ISNULL(FechaCancDet) OR EXISTS(SELECT IDInventarioDet FROM inventarios_movimientos_detalle WHERE KEYDetCancelado_MovDet = InvDet.IDInventarioDet )) AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen 
			GROUP BY IDKar 
			ORDER BY KEYAlmacenKar ASC, Clasificacion ASC, DescProd ASC, Fecha ASC";

		} else {
			//CONSULTA TOÑITO
			/*
			$sqlReporte="SELECT DescProd producto,KEYAlmacenKar,DesAlm,DATE_FORMAT( FechaKar,'%d/%m/%Y %H:%i') as FechaKar,FechaKar Fecha,KEYReferenciaDet,
			if (KEYMovimientoDet>0,concat(CodMovAlm,'-',ReferenciaInventarioDet),ReferenciaInventarioDet ) as ReferenciaInventarioDet,
			CantidadEntradaKar as entrada,
			CantidadSalidaKar  as salida,
			DescUni,0 total,		
			IDKar,KEYInvDetKar KEYESDetalleKar,KEYAlmacenKar,KEYProductoKar,CompraKar,VentaKar,SaldoKar,PromedioKar, DesProCla AS Clasificacion 
			FROM inventarios_kardex k

			LEFT JOIN cat_productos ON IDProd=KEYProductoKar
			LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
			LEFT JOIN inventarios_movimientos_detalle ON IDInventarioDet=KEYInvDetKar
			LEFT JOIN inventarios_movimientos ON IDInv=KEYReferenciaDet		
			LEFT JOIN cat_almacenes_movimientos ON IDMovAlm=KEYMovimientoInv
			LEFT JOIN cat_almacenes ON IDAlm=KEYAlmacenKar
			WHERE FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND ISNULL(FechaCancDet) AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen 
			ORDER BY KEYAlmacenKar ASC, Clasificacion ASC, DescProd ASC, Fecha ASC";
			*/
			//CONSULTA ANGELICA
			$sqlReporte="SELECT DescProd producto,KEYAlmacenKar,DesAlm,DATE_FORMAT( FechaKar,'%d/%m/%Y %H:%i') AS FechaKar,FechaKar Fecha,KEYReferenciaDet,
			IF (KEYMovimientoDet>0,CONCAT(CodMovAlm,'-',ReferenciaInventarioDet),ReferenciaInventarioDet ) AS ReferenciaInventarioDet,
			CantidadEntradaKar AS entrada,
			CantidadSalidaKar  AS salida,
			DescUni,0 total,		
			IDKar,KEYInvDetKar KEYESDetalleKar,KEYAlmacenKar,KEYProductoKar,CompraKar,VentaKar,SaldoKar,PromedioKar, DesProCla AS Clasificacion,
			(SELECT SaldoKar FROM inventarios_kardex kk
			LEFT JOIN inventarios_movimientos_detalle InvDet ON InvDet.IDInventarioDet= kk.KEYInvDetKar
			WHERE kk.KEYProductoKar = k.KEYProductoKar AND kk.FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND ISNULL(InvDet.FechaCancDet) AND  kk.KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen AND kk.KEYAlmacenKar = k.KEYAlmacenKar
			ORDER BY FechaKar DESC
			LIMIT 1) AS SaldoFinalPeriodo
			FROM inventarios_kardex k

			LEFT JOIN cat_productos ON IDProd=KEYProductoKar
			LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
			LEFT JOIN inventarios_movimientos_detalle InvDet ON InvDet.IDInventarioDet=k.KEYInvDetKar
			LEFT JOIN inventarios_movimientos ON IDInv=KEYReferenciaDet		
			LEFT JOIN cat_almacenes_movimientos ON IDMovAlm=KEYMovimientoInv
			LEFT JOIN cat_almacenes ON IDAlm=KEYAlmacenKar
			WHERE FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND (ISNULL(FechaCancDet) OR EXISTS(SELECT IDInventarioDet FROM inventarios_movimientos_detalle WHERE KEYDetCancelado_MovDet = InvDet.IDInventarioDet )) AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen 
			ORDER BY KEYAlmacenKar ASC, Clasificacion ASC, DescProd ASC, Fecha ASC";
		}
		$data=$model->select($sqlReporte);
		/*echo "<pre>";		
		file_put_contents('data.txt',print_r($data));
		echo "<pre>";*/
		return $data;
	}
	
	function imprimir($path,$mode='I',$params){	
		$this->params=$params;
		$this->configTabla();
		$this->AddPage();
		$data=$this->getDatos();		
		//---------------------------------------------------------------------				
		$this->ColoredTable($this->header, $data);		
		//-----------------------------------------------------------------------/		
		// print colored table	
		//----------------------------------------------------------------------------
		$this->Output($path, $mode);
	}

	
	public function imprime_encabezado_de_tabla($header){
	   $this->setY($this->yHeader);
	   //-------------------------------------------
	    //$this->SetLineWidth(.1);
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255,255);
		$h=1;		
		$border=0;		
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
		$lastProd=0;
		//$totProdAcum=0;
		$lastAlm=0;
		$totAcum=0;
		$arrTotales=array();
		$existenciaAcumulada=0;
		$kardexDiarioModel=new InventariosKardexDiarioModel();
		$arrIniciales=array();
		$existenciaFinal=0;

		
		for ($recIndex=0; $recIndex<sizeof($data); $recIndex++){
			//---------------------------------------------------------------------
			if ($lastAlm!=$data[$recIndex]['KEYAlmacenKar']){				
				//--------- Imprimir Nombre del Almacén ----------------
				$this->SetFont('Courier', 'B', 9);				
				$nomAlm=formatearTexto($data[$recIndex]['DesAlm'],true); 
				$this->Cell(60, 0,$nomAlm, '', 1, 'L', 1,'',0);
				$this->SetFont('Courier', 'B', 8);
				$this->ln();
				$y=$this->getY();
				//Tomar el valor de Y para posicion del acumulado final
				$yAcum=$this->getY();
				//-----------------------------------------------------
				$lastProd=0;				
				$arrIniciales=array();				
			}			
			$lastAlm=$data[$recIndex]['KEYAlmacenKar'];
			if ($lastProd!=$data[$recIndex]['KEYProductoKar']){
				//Imprimir el nombre del producto solo una vez, y el saldo inicial
				$costoTotal+=$totAcum;
				$this->acumulado=$costoTotal;
				//--------------------------
				//Imprime el nombre del producto y el inventario inicial
				$NomProd=formatearTexto($data[$recIndex]['producto'],true);
				$KEYProductoKar=$data[$recIndex]['KEYProductoKar'];
				//-------------------------------------------------------------
				if (in_array($KEYProductoKar,$arrIniciales) ){
					$totAcum		=0;
					$promAcum		=0;
					$inicial		=0;
					$existenciaFinal = 0;
					
				}else{	//Obtener valores iniciales
					$arrIniciales[]	=$KEYProductoKar;					
												
					$model			=$this->getModelObject();
					$arrInicial		=$kardexDiarioModel->obtenerKardexAnterior($KEYProductoKar,$lastAlm,$this->params["fInicial"]);
					//  print_r($arrInicial);
					if ( empty($arrInicial) ){
						$inicial	=0;	
						$promAcum	=0;
						$totAcum	=0;
						$existenciaFinal =floatval($data[$recIndex]['SaldoFinalPeriodo']);
					}else{
						$inicial	=floatval($arrInicial['SaldoKar']);	
						$promAcum	.=$arrInicial['PromedioKar'];
						$existenciaFinal =floatval($data[$recIndex]['SaldoFinalPeriodo']);
						$totAcum	=$promAcum*$existenciaFinal;
						
					}					
					$existenciaAcumulada=$inicial;					
					$inicial		=formatearCantidad($inicial);	
					$existenciaFinal = formatearCantidad ($existenciaFinal);
				}
				//-----------------------------				
				$this->SetFont('Courier', 'B', 9);
				$nomWidth=$this->GetStringWidth($NomProd,'Courier','B',9);
				$this->Cell(100, 0, $NomProd, '', 0, 'L', 0,'',0);
				$this->SetFont('Courier', 'B', 8);
				
				//Cantidad acumulada final del periodo				
				$this->setX(167);
				$this->Cell(18, 0,"Stock Inicial: ".$inicial."       Stock Final: ".$existenciaFinal, '', 0, 'R', 0,'',0);		

				//Cantidad inicial del periodo
				/*
			|	$this->setX(167);		
				$this->Cell(18, 0,$inicial."exis acum", '', 0, 'R', 0,'',0);				
				*/
				
				/* COSTO PROMEDIO - TOTAL */
				// $this->setX(217);				
				// $this->Cell(18, 0,formatearMoneda($promAcum), '', 0, 'R',0,'',0);
				
				// $this->setX(240);
				// $this->Cell(25, 0,formatearMoneda($totAcum), '', 0, 'R',0,'',0);
				
				$this->SetFont('Courier', '', 8);
				$this->ln();
			}
			$lastProd=$data[$recIndex]['KEYProductoKar'];
			//---------------------------------------------------------------------
			for($colIndex = 0; $colIndex < $num_headers; $colIndex++) {
				$value=isset($data[$recIndex][$header[$colIndex]['dataindex']])? $data[$recIndex][$header[$colIndex]['dataindex']] : '';
				if ($header[$colIndex]['dataindex']=='total'){
					$value=$data[$recIndex]['PromedioKar']*$data[$recIndex]['SaldoKar'];
					$totAcum=$value;				
						
				}

				if ($header[$colIndex]['dataindex']=='SaldoKar'){
					$value=$data[$recIndex]['SaldoKar'];
					$existenciaAcumulada=$value;
				}
				if (isset($header[$colIndex]['type'])){
					switch($header[$colIndex]['type']){
						case 'moneda':$value=formatearMoneda($value);break;
						case 'cantidad':$value=formatearCantidad($value);break;
						case 'string':$value=formatearTexto(UTF8_ENCODE($value));break;						
					}
				}
				
				$align=$header[$colIndex]['align'];
				if (strlen($value)>$header[$colIndex]['width']){
					$value=substr($value,0,$header[$colIndex]['width']);
				}
				if ($header[$colIndex]['dataindex']=='DescUni'){
					$value='  '.$value;
				}
				
				$y=$this->getY();
				/*if ( floatval($y)>185 ){ ANT!
					$this->addPage();
					$this->setY($this->yDatos);
				}*/

				if($colIndex==0){
					$clasificacion = $data[$recIndex]['Clasificacion']."\n";

					// Antonio: Para imprimir las series en el reporte...
					$x1=$this->getX();
					$y1=$this->getY();
					if($this->params['porSerie']==true)
						$value1 = $clasificacion."   ".formatearTexto(UTF8_ENCODE($data[$recIndex]['Series']));
					else
						$value1 = $clasificacion.$value;
					$this->setXY($x1+$header[$colIndex]['width'], $y1);
				} else {
					$this->Cell($header[$colIndex]['width'], 0, $value, '', 0, $align, $fill, '', 0);
				}
			
			}			
						
				
			$this->setX($x1);
			$this->MultiCell($header[0]['width'], 0, $value1."\n");

			$this->Ln(3);
			/*$y=$this->getY(); ANT!
			if (floatval($y)>185){
				$this->addPage();
			}*/
			$y=$this->getY();
			if ($y<$this->yDatos){
				$this->setY($this->yDatos);				
			}	

		}
		
		$costoTotal+=$totAcum;
		//----------------------------------------------
		$this->impresionTerminada=TRUE;
		//----------------------------------------------
		$y=$this->getY()+1;		
		$this->SetDrawColor(0, 0,0);
		$this->SetFillColor(0, 0,0);		
		$this->Line($this->lMargin,$y,266,$y); 			
		$this->Ln(3);		
		$this->Cell(220, 0,"COSTO DE MOVIMIENTOS:", '', 0, "R", $fill);	
		$this->SetFont('', 'B');
		$this->Cell(30, 0,formatearMoneda($costoTotal), '', 0, "R", $fill);	
    }
	
	
	function Footer(){
	
		$this->setLineWidth(.7);
		
		$this->SetFont('', '',8);
		$fill=0;

		if (empty($this->impresionTerminada)){
			$y=$this->getY()-10;
			//$this->Line(0,$y,400,$y);
			$this->setY($y);
			$this->Cell(220, 0,"Total acumulado:", '', 0, "R");	
			$this->SetFont('', 'B');
			$this->Cell(30, 0,formatearMoneda($this->acumulado), '', 0, "R", $fill);
			//$this->Cell(0, 0,"Total acumulado: $this->acumulado", '', 0, "R");	
			$this->ln();
			$y+=4;
		}else{
			$y=$this->getY()-6;
			$this->setY($y);
		}
		//$this->Line($this->lMargin,$y,400,$y);
		$this->Line($this->lMargin,$y,266,$y); 			
		$this->setY($y+1);
		$pageNumber=$this->PageNo();
		$fecha=date('d/m/Y H:i:s'); //;getFechaActual();
		$totPageAlias=$this->getAliasNbPages();
		$this->Cell(60, 0,UTF8_ENCODE("Fecha de impresión: $fecha"), '', 0, "L");	
		$this->Cell(0, 0,"Pagina $pageNumber/$totPageAlias", '', 0, "R");	
	}
}
?>