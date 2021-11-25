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

        // $image_file = K_PATH_IMAGES.'/logos/puma.jpg';
        // $this->Image($image_file,$this->lMargin,15, 25,'', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
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
		$this->Cell(0, 0, 'REPORTE DE MOVIMIENTOS Y COSTOS DE '.$nombreAlmacen, 0, false, 'L', 0, '', 1, false, 'M', 'M');
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
		//$this->setY($this->yHeader+5);
		
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
				'header'=>'ACUM. ',
				'width'=>20,
				'dataindex'=>'SaldoKar',
				'align'=>'R',
				'type'=>'cantidad'
			),
			array(
				'header'=>'      U.M',
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
				'header'=>'COSTO MOV.',
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

		//set margins

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
		/*======================================================================================================
			EL OBJETIVO ES GENERAR UN ARREGLO CON LA SIG. ESTRUCTURA:		
		$datos=array(
			'datos'=>array($concepto1, $concepto2, $concepto3, $concepto4)
		);
		
			DONDE CADA CONCEPTO TIENE LA SIG. ESTRUCTURA;
		
		$concepto1=array(
			'concepto'		=>'Nombre del concepto',
			'cant_acumulada'=>1,
			'costo_promedio'=>590,
			'total'			=>590,
			'movimientos'	=>array(				
				'fecha'			=>'2010',
				'referencia'	=>'Tipo Del Movimiento',
				'entrada'		=>0,
				'salida'		=>0,
				'UM'			=>0,
				'cant_acumulada'=>1,
				'costo_promedio'=>590,
				'total'			=>590,
			)
		);		
		
		======================================================================================================*/		
		$model		=$this->getModelObject();
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

		//-----------------------------------------------------------------------------------------------------------------
		//	    Cuando el movimiento haya sido generado desde el catalogo movimientos, se mostrara en la columna referencia lo siguiente:
		// El codigo del tipo de movimiento + la referencia
		
		if($this->params["porSerie"]){ // REPORTE POR SERIES... (ANTONIO)
			$sqlDetalles="SELECT DATE_FORMAT( FechaKar,'%d/%m/%Y %H:%i') as FechaMov,
			if (KEYMovimientoDet>0,concat(CodMovAlm,'-',ReferenciaInventarioDet),ReferenciaInventarioDet ) as referencia,
			if (isnull(CompraKar),CantidadSalidaKar,CantidadEntradaKar) as Cantidad,KEYTipoMovimientoDet,
			if (isnull(CompraKar),VentaKar,CompraKar) as TSubTotalDet,PromedioKar,
			KEYProductoKar as KEYProductoDet, 'P' as TipoProductoDet, concat('P-',KEYProductoKar) as idconcepto,
			DescUni as DescUDMCon_MovDet, DescProd descripcion, ifnull(CantidadSalidaKar,0) as Salida, ifnull(CantidadEntradaKar,0) as Entrada, DesProCla AS Clasificacion, 
			GROUP_CONCAT(DISTINCT Serie ORDER BY Serie ASC SEPARATOR '\n   ') AS Series 
			FROM inventarios_kardex
			LEFT JOIN inventarios_movimientos_detalle InvDet ON KEYInvDetKar=IDInventarioDet
			LEFT JOIN inventarios_movimientos ON IDInv=KEYReferenciaDet
			LEFT JOIN cat_almacenes_movimientos ON IDMovAlm=KEYMovimientoInv

			LEFT JOIN cat_productos ON IDProd=KEYProductoKar 
			LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
			LEFT JOIN inventarios_kardex_detalle ON IDKar = IDKardex 
			WHERE FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59' AND (ISNULL(FechaCancDet) OR EXISTS(SELECT IDInventarioDet FROM inventarios_movimientos_detalle WHERE KEYDetCancelado_MovDet = InvDet.IDInventarioDet )) AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen
			GROUP BY IDKar
			ORDER BY Clasificacion ASC, DescProd ASC, FechaKar ASC;";
		} else {
			$sqlDetalles="SELECT DATE_FORMAT( FechaKar,'%d/%m/%Y %H:%i') as FechaMov,
			if (KEYMovimientoDet>0,concat(CodMovAlm,'-',ReferenciaInventarioDet),ReferenciaInventarioDet ) as referencia,
			if (isnull(CompraKar),CantidadSalidaKar,CantidadEntradaKar) as Cantidad,KEYTipoMovimientoDet,
			if (isnull(CompraKar),VentaKar,CompraKar) as TSubTotalDet,PromedioKar,
			KEYProductoKar as KEYProductoDet, 'P' as TipoProductoDet, concat('P-',KEYProductoKar) as idconcepto,
			DescUni as DescUDMCon_MovDet, DescProd as descripcion, ifnull(CantidadSalidaKar,0) as Salida, ifnull(CantidadEntradaKar,0) as Entrada, DesProCla AS Clasificacion 
			FROM inventarios_kardex
			LEFT JOIN inventarios_movimientos_detalle InvDet ON KEYInvDetKar=IDInventarioDet
			LEFT JOIN inventarios_movimientos ON IDInv=KEYReferenciaDet		
			LEFT JOIN cat_almacenes_movimientos ON IDMovAlm=KEYMovimientoInv

			LEFT JOIN cat_productos ON IDProd=KEYProductoKar 
			LEFT JOIN cat_productos_categorias_relaciones ON IDProd = KEYProRel
			LEFT JOIN cat_productos_clasificaciones ON KEYProClaRel = IDProCla

			LEFT JOIN cat_unidad_medida ON IDUni=KEYUniProd
			WHERE FechaKar BETWEEN '$fInicial' AND '$fFinal 23:59:59'  AND (ISNULL(FechaCancDet) OR EXISTS(SELECT IDInventarioDet FROM inventarios_movimientos_detalle WHERE KEYDetCancelado_MovDet = InvDet.IDInventarioDet )) AND KEYAlmacenKar IN ($idsAlmacenes) $filtroAlmacen
			ORDER BY Clasificacion ASC, DescProd ASC, FechaKar ASC;";
		}
		$data		=$model->select( $sqlDetalles );
		//echo $sqlDetalles;exit;
		//------------------------------------------------------------------------------------------------------
		$ultimoConcepto='';
		for($i=0; $i<sizeof($data); $i++ ){			
			if( $ultimoConcepto	!= $data[$i]['idconcepto'] ){
				if (!empty($concepto)){
					$datos[]=$concepto;
				}
				//$arrInicial		=$kardexDiarioModel->obtenerKardexAnterior( $KEYProductoKar, $lastAlm, $this->params["fInicial"] );				
				if ($data[$i]['TipoProductoDet']=='P'){
					$arrInicial		=$this->ultimos( $data[$i]['KEYProductoDet'] ); 					
				}else{
					$arrInicial=array(
						'existencia_inicial'=>0,
						'promedio_inicial'	=>0
					);
				}
				
				$cant_acumulada	=$arrInicial['existencia_inicial'];		
				$total_inicial	=$arrInicial['promedio_inicial'] * $cant_acumulada;						
				$concepto=array(
					'concepto'			=>$data[$i]['descripcion'],
					'cant_inicial'		=>formatearCantidad( $cant_acumulada ),
					'promedio_inicial'	=>formatearMoneda( $arrInicial['promedio_inicial'] ),
					'total_inicial'		=>formatearMoneda( $total_inicial ),
					'movimientos'		=>array()
				);
				$movimientos	=array();
			}
			$ultimoConcepto	=$data[$i]['idconcepto'];
			
			switch( $data[$i]['KEYTipoMovimientoDet'] ){
				case 1:	//entrada
					$entrada=$data[$i]['Cantidad'];
					$cant_acumulada+=floatval($data[$i]['Cantidad']);
					//$total=$data[$i]['TSubTotalDet'] * $data[$i]['Cantidad'];
					$total=$data[$i]['TSubTotalDet'] ;
					$salida	=0;
				break;
				case 2: //salida
					$salida	=$data[$i]['Cantidad'];
					$cant_acumulada-=floatval($data[$i]['Cantidad']);
					//$total=$data[$i]['TSubTotalDet'] * $data[$i]['Cantidad'] ;
					$total=$data[$i]['TSubTotalDet']  ;
					$entrada	=0;
				break;
				case 3: //traspaso
					$entrada=$data[$i]['Entrada'];
					$salida	=$data[$i]['Salida'];
					$cant_acumulada+=(floatval($data[$i]['Entrada']) - floatval($data[$i]['Salida']));
					//$total=$data[$i]['TSubTotalDet'] * $data[$i]['Cantidad'];
					$total=$data[$i]['PromedioKar'] * $data[$i]['Cantidad'];
					
				break;
			}
			$movimiento=array(
				'FechaMov'		=>$data[$i]['FechaMov'],
				'referencia'	=>(strlen( $data[$i]['referencia'] ) > 11) ?  substr($data[$i]['referencia'],0,11).'...' : $data[$i]['referencia'] ,
				'entrada'		=>formatearCantidad( $entrada ),
				'salida'		=>formatearCantidad( $salida ),
				'UM'			=>$data[$i]['DescUDMCon_MovDet'],
				'cant_acumulada'=>formatearCantidad( $cant_acumulada ),
				'subtotal'		=>formatearMoneda( $data[$i]['TSubTotalDet'] ),
				'total'			=>formatearMoneda( $total ),
				'Series'			=>formatearTexto( $data[$i]['Series'] ),
				'Clasificacion'			=>formatearTexto( $data[$i]['Clasificacion'] ),
				'PromedioKar'	=>formatearMoneda( $data[$i]['PromedioKar'] )
			);
			$concepto['movimientos'][]=$movimiento;				
		}	
		$datos[]=$concepto;

		return $datos;
	}
	
	function ultimos($KEYProductoKar){
		$model=$this->getModelObject();

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

		//--------------------------------------------------------------------
		//	Se obtiene el ultimo registro de este producto en cada almacen
		$sqlUltimosRegistros="SELECT MAX(FechaKar) FechaKar,KEYAlmacenKar 
		FROM inventarios_kardex_diario 
		WHERE FechaKar < '".$this->params["fInicial"]."' AND KEYProductoKar=$KEYProductoKar $filtroAlmacen GROUP BY KEYAlmacenKar";
		$arrUltimosPorAlmacen=$model->select($sqlUltimosRegistros);

		$sumEx	=0;	//contiene la existencia del producto tomando en cuenta todos los almacenes.
		$sumTot	=0;	//contiene el costo total del producto tomando en cuenta todos los almacenes.
		//Se promedian los promedios de los almacenes
		foreach ($arrUltimosPorAlmacen as $registro){
			$fecha=$registro['FechaKar'];
			$almacen=$registro['KEYAlmacenKar'];
			$sql="SELECT PromedioKar,SaldoKar FROM inventarios_kardex_diario 
			WHERE FechaKar = '$fecha' AND KEYProductoKar =$KEYProductoKar AND KEYAlmacenKar=$almacen";
			$arr=$model->select($sql);
			$cosTot=$arr[0]['PromedioKar']*$arr[0]['SaldoKar'];	//Costo del producto
			$sumTot+=$cosTot;
			$sumEx+=$arr[0]['SaldoKar'];					
		}
				
		if (empty($sumEx)){
			$promInicial=0;
		}else{
			$promInicial=$sumTot/$sumEx;
		}
		
		return array(
			'existencia_inicial'=>$sumEx,
			'promedio_inicial'	=>$promInicial
		);
	}
	
	function imprimir($path,$mode='I',$params){	
		$this->params=$params;
		$this->acumulado=0;
		$this->configTabla();
		$this->AddPage();
		$data=$this->getDatos();		
		//---------------------------------------------------------------------				
		$this->ColoredTable($this->header, $data);	
		
		$this->Output($path, $mode);
	}

	
	public function imprime_encabezado_de_tabla($header){
		$this->setY($this->yHeader);
		//----------------------------------------------------------		
		$this->SetDrawColor(255, 255, 255);
		$this->SetFillColor(255, 255, 255);
		$h		=1;		
		$border	=0;		
		//----------------------------------------------------------
		$num_headers = count($header);
		for($i = 0; $i < $num_headers; ++$i) {
			$value	=UTF8_ENCODE($header[$i]['header']);
			$align	=isset($header[$i]['align']	)? $header[$i]['align']	:'L';
			$fill	=isset($header[$i]['fill']	)? $header[$i]['fill'] 	: 0;
			$this->Cell($header[$i]['width'], $h, $value, $border, 0, $align, $fill);
		}
	}
	
    public function ColoredTable($header,$data) {       
		$num_headers = count($header);
		$this->setY($this->yDatos);
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
		$this->SetFont('Courier', '', 8);
        // Data
        $fill 		=0;
		$costoTotal	=0;
		$lastProd	=0;
		$totProdAcum=0;
		$totAcum	=0;
		
		for ($i=0; $i<sizeof($data); $i++){
			//---------------------------------------------------------------------			
			$this->SetFont('Courier', 'B', 8);
			//$nomWidth=$this->GetStringWidth($data[$i]['concepto'],'Courier','B',9);
			$this->Cell(100, 0, $data[$i]['concepto'], '', 0, 'L', 0,'',0);
			
			$this->setX(167);
			$this->Cell(18, 0, $data[$i]['cant_inicial'], '', 0, 'R', 0,'',0);
			
			$this->setX(217);
			//$this->Cell(18, 0, $data[$i]['promedio_inicial'], '', 0, 'R',0,'',0);
			
			$this->setX(240);
			$this->Cell(25, 0, $data[$i]['total_inicial'], '', 0, 'R',0,'',0);
			
			$this->SetFont('Courier', '', 8);
			$this->ln();
			
			$movientos=$data[$i]['movimientos'];		

			for($colIndex = 0; $colIndex < sizeof($movientos); $colIndex++) {
				
				$h=2;
				$border=0;

				$y=$this->getY();
				$value='';
				$numCol=0;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;								
				$w=$header[$numCol]['width'];
				// $this->Cell($w, $h, 	$value, $border, 0, $align, $fill);
				if($numCol==0){
					$clasificacion = $movientos[$colIndex]['Clasificacion']."\n";

					// Antonio: Para imprimir las series en el reporte...
					$x1=$this->getX();
					$y1=$this->getY();
					if($this->params['porSerie']==true)
						$value1 = $clasificacion."   ".$movientos[$colIndex]['Series'];
					else
						$value1 = $clasificacion.$value;
					$this->setXY($x1+$w, $y1);
				} else {
					$this->Cell($w, $h, $value, $border, 0, $align, $fill);
				}
				
				
				$value	=UTF8_ENCODE($movientos[$colIndex]['FechaMov']);
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;	
				$w=$header[$numCol]['width'];				
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);
				
				$value	=UTF8_ENCODE($movientos[$colIndex]['referencia']);
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;		
				$w=$header[$numCol]['width'];								
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);				

				
				$value	=$movientos[$colIndex]['entrada'];
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;		
				$w=$header[$numCol]['width'];								
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);
				
				$value	=$movientos[$colIndex]['salida'];
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;	
				$w=$header[$numCol]['width'];								
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);
				
				$value	=$movientos[$colIndex]['cant_acumulada'];
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;		
				$w=$header[$numCol]['width'];								
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);
				
				$value	=UTF8_ENCODE($movientos[$colIndex]['UM']);
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;		
				$w=$header[$numCol]['width'];								
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);
				
				$value	=$movientos[$colIndex]['PromedioKar'];
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	= 0;		
				$w=$header[$numCol]['width'];								
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);								
				
				$value	=$movientos[$colIndex]['total'];
				$numCol++;
				$align	=isset($header[$numCol]['align']	)? $header[$numCol]['align']	:'L';
				$fill	=isset($header[$numCol]['fill']	)? $header[$numCol]['fill'] 	: 0;		
				$w=$header[$numCol]['width'];								
				$this->Cell($w, $h,$value, $border, 0, $align, $fill);

				// $this->Ln();

				/*$y=$this->getY(); ANT!
				if ( floatval($y)>185 ){
					$this->addPage();
					$this->setY($this->yDatos);
				}*/
				$this->setX($x1);
				$this->MultiCell($header[0]['width'], 0, $value1."\n");
				$this->Ln(3);
			}
			
			// $this->Ln();
			/*$y=$this->getY(); ANT!
			if ( floatval($y)>185 ){
				$this->addPage();
			}*/
			
			$y=$this->getY();
			if ($y<$this->yDatos){
				$this->setY( $this->yDatos );
			}			
		}
		
		//----------------------------------------------
		$this->impresionTerminada=TRUE;
		//----------------------------------------------
		$y = $this->getY() + 1;		
		$this->SetDrawColor( 0, 0, 0 );
		$this->SetFillColor( 0, 0, 0 );
		
		$this->Line( $this->lMargin, $y, 266, $y ); 			
		$this->Ln();
    }
	
	function Footer(){	
		$this->setLineWidth(.7);		
		$this->SetFont('', '',8);
		$fill=0;		
		if (empty($this->impresionTerminada)){
			$y=$this->getY()-10;
			//$this->Line(0,$y,400,$y);
			$this->setY($y);
		//	$this->Cell(220, 0,"Total acumulado:", '', 0, "R");	
			$this->SetFont('', 'B');
		//	$this->Cell(30, 0,formatearMoneda($this->acumulado), '', 0, "R", $fill);
		//	$this->Cell(0, 0,"Total acumulado: $this->acumulado", '', 0, "R");	
			$this->ln();
			$y+=4;
		}else{
			$y=$this->getY()-6;
			$this->setY($y);
		}

		$this->Line($this->lMargin,$y,266,$y); 			
		$this->setY($y+1);
		$pageNumber=$this->PageNo();
		$fecha=date('d/m/Y H:i:s'); 
		$totPageAlias=$this->getAliasNbPages();
		$this->Cell(60, 0,UTF8_ENCODE("Fecha de impresión: $fecha"), '', 0, "L");	
		$this->Cell(0, 0,"Pagina $pageNumber/$totPageAlias", '', 0, "R");	
	}
}
?>