<?php
require_once(dirname(__FILE__).'/mfpdf.php');
class ASI011112MD6 extends mFPDF{
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		$logo=array(
			'imagen'=>'img/AzulejosIbarra.jpg',
			'x'=>153,
			'y'=>15
		);
		/*if(isset($params['logo'])){
			$this->logo=$params['logo'];
		}*/
		$params['logo']=$logo;
		parent::__construct($orientation,$unit,$format,$params);
	}
	
	//09/07/2012
	//Jorge Eduardo Lopez
	//Se le agregaron los numeros de telefono de Azulejos Ibarra
	public function imprimirMatriz(){	
		$border=0;
		$columna = $this->x1Emisor;		
		$this->SetFont('Arial','B',6);
		$this->SetX($columna);
		$this->Cell(80, 3, "DOMICILIO FISCAL:", $border, 1);
		
		$this->SetFont('Arial','',6);
		$this->SetX($columna);
		$this->Cell(80, 3, $this->em_calle." ".$this->em_no_ext." ".$this->em_no_int." ".$this->em_colonia, $border, 1);		
		$this->SetX($columna);
		$cod_pos = ($this->em_cp) ? " C.P.: ".$this->em_cp."   " : "";	//CODIGO POSTAL
		$this->Cell(80, 3, $this->getLocalidad($this->em_localidad,$this->em_ciudad, $this->em_estado, $this->em_pais).$cod_pos, $border, 1);	//Imprime Ubicacion y CP				
		$this->SetX($columna);		
		$this->Cell(80, 3, "R.F.C.: ".$this->em_rfc." Tels. (669) 984-82-87 y (669) 986-03-38", $border, 1);
		//----------------------------------------------------------------
		$this->SetX($columna);		
		if ( !empty($this->regimenes) ){
			$this->SetFont('Arial','B',6);
			$this->Cell(21, 3, utf8_decode('RÉGIMEN FISCAL:'), 0);
			$this->SetFont('Arial','',6);
			$this->MultiCell($this->anchoCellRegimen, 3, mb_strtoupper($this->regimenes),0,1);
		}else{
			$this->MultiCell($this->anchoCellRegimen, 3, mb_strtoupper(''),0,1);
		}						
		
		//----------------------------------------------------------------
	}
	
	function afterHeader(){
		$x=$this->getX();
		$y=$this->getY();
				
		$this->setXY(167, 34);
		$this->SetFont('Arial','B',12);
		$this->Cell(38, 4, $this->TipDoc, 0, 0,'R');
		$this->setX($x);
		$this->setY($y);
		$this->imprimeEncabezadoDeTabla();
	}
	
	function imprimeLogo(){
		if(isset($this->logo)){
			$imagen=$this->logo['imagen'];
			$x=$this->logo['x'];
			$y=$this->logo['y'];

			$this->Image($imagen,$x,$y,50);	
		}
	}
	
	public function FinalDetalle() {
		$columna = 10;
		$this->SetFillColor(0,0,0);
		$this->Ln(3);
		if ($this->tipoDeFactura=='cfdi'){
			$this->SetX($columna);
			$this->SetFont('Arial','B',9);
			$this->SetTextColor(0,0,0);
			$this->Cell(195, 4, "SELLO DIGITAL DEL CFDI ", 0, 1, 'L');
			$mensajeSello="SELLO DEL SAT ";
			$sello= $this->fac_sello_SAT;
			$mensajeCadena="CADENA ORIGINAL DEL TIMBRE FISCAL DIGITAL DEL SAT";
			$cadenaOriginal= $this->getCadenaOriginal();
		}else{
			$mensajeSello="SELLO DIGITAL";
			$sello=$this->fac_sello;
			$mensajeCadena="CADENA ORIGINAL";
			$cadenaOriginal= utf8_decode($this->fac_cadena_original);
		}

		$this->SetTextColor(0,0,0);
		$this->SetX($columna);
		$this->SetFont('Arial','',7);
		$this->MultiCell(195, 4, $this->fac_sello_dig);
		$this->Ln(2);
		$this->SetX($columna);
		$this->SetFont('Arial','B',9);
		$this->SetTextColor(0,0,0);
		$this->Cell(195, 4, $mensajeSello, 0, 1, 'L');
		$this->SetTextColor(0,0,0);
		$this->SetX($columna);
		$this->SetFont('Arial','',7);
		$this->MultiCell(195, 4,$sello );
		$this->Ln(2);
		$this->SetX($columna);
		$this->SetFont('Arial','B',9);
		$this->SetTextColor(0,0,0);
		$this->Cell(195, 4, $mensajeCadena, 0, 1, 'L');
		$this->SetTextColor(0,0,0);
		$this->SetX($columna);
		$this->SetFont('Arial','',7);
		$this->MultiCell(195, 4,$cadenaOriginal,0,'L');
	}
	
	/*Sobre escrita solo para cambiar el color del encabezado de la tabla*/
	function imprimeEncabezadoDeTabla(){	
		if ($this->imprimiendoSubdetalles==true || $this->imprimiendoAduanas || $this->imprimiendoConceptos==true || $this->imprimiendoComponentes==true ){				
			$this->SetFont('Arial','B',8);
			
			if ($this->tipoDeFactura=='cfdi'){
				$this->SetXY(10,$this->yEncabezadoDeTabla + 2 );
			}else{
				$this->SetXY(10,$this->yEncabezadoDeTabla );
			}
			
			$this->SetFillColor(222,222,222);		//<-----------Override
			$this->SetTextColor(0, 0, 2);			//<-----------Override
			$this->Cell($this->arr_det[0], 5, "CANTIDAD", '', 0, 'L', true);
			$this->Cell($this->arr_det[1], 5, utf8_decode("DESCRIPCIÓN"), '', 0, 'L', true);
			$this->Cell($this->arr_det[2], 5, "U.M.", '', 0, 'L', true);
			$this->Cell($this->arr_det[3], 5, "PRECIO UNITARIO", '', 0, 'R', true);
			$this->Cell($this->arr_det[4], 5, "IMPORTE", '', 0, 'R', true);
			$this->SetTextColor(0,0,0);			
			# -- Imprime saltos de linea para que el detalle comience correctamente
			$this->Ln();
			$this->Cell(195,1,"",'',1);			
		}
		if ($this->imprimiendoAduanas==true){
			//	IMPRIME EL ENCABEZADO Y VIÑETA										
			$this->SetFont('ARIAL', 'B', 7);
			$encabezado="CONCEPTOS ADUANALES";		
			$x1Viñeta=29;			
			$this->SetX($x1Viñeta+2);
			$this->Cell(0,4,$encabezado,0,1);
			$y1Viñeta=$this->GetY()-4;	
			$this->SetLineWidth(.5);	
			$this->Rect($x1Viñeta,$y1Viñ+1,1.5,1.5,'DF');				
		}else if ($this->imprimiendoSubdetalles==true){
			$this->SetFont('ARIAL', 'B', 7);
			$encabezado=UTF8_DECODE("DESCRIPCIÓN DE GASTOS DE TERCEROS");		
			$x1Viñeta=29;			
			$this->SetX($x1Viñeta+2);
			$this->Cell(0,4,$encabezado,0,1);
			$y1Viñeta=$this->GetY()-4;
			$this->SetLineWidth(.5);		
			$this->Rect($x1Viñeta,$y1Viñeta+1,1.5,1.5,'DF');	
		}else if($this->imprimiendoComponentes==true){
			$this->SetFont('ARIAL', 'B', 7);
			$encabezado=UTF8_DECODE("COMPONENTES DEL KIT");		
			$x1Viñeta=29;
			$this->SetX($x1Viñeta+2);
			$this->Cell(0,4,$encabezado,0,1);
			$y1Viñeta=$this->GetY()-4;
			$this->SetLineWidth(.5);		
			$this->Rect($x1Viñeta,$y1Viñeta+1,1.5,1.5,'DF');									
		}		
	}
	
	public function Footer() {
		$this->SetY(-$this->limite_det);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		$y = $this->GetY() + 3;
		$this->SetY($y);	

		$this->b4Footer();

		$y=$this->GetY();
		
		# -- Dibuja marcos al detalle y al footer
		$this->SetDrawColor(0, 0, 0);
		$this->SetLineWidth(0.2);
		$this->SetFillColor(222,222,222);
		$this->Rect(152, 223, 28, 34, true); // marco relleno de los totales
		$this->Rect(40, 223, 165, 34); // marco del footer
		
		# -- Marco CBB
		$this->SetFont('Arial','B',6);
		$this->SetXY($this->GetX(), 223);
		$this->SetFillColor(222,222,222);
		$this->SetTextColor(0,0,0);
		$this->Cell(28, 4, "CODIGO BIDIMENSIONAL", 0, 1, 'C', true);
		$this->Image($this->cbb, $this->GetX() - 1, $this->GetY() + 1, 30, 30);
		$this->SetTextColor(0,0,0);
		
		# -- Totales
		$border = 0;
		$columna = 152;
		$alto   = 4.7;
		$this->SetFont('Arial','B',9);
		$this->SetXY($columna,$y);
		$this->SetTextColor(0,0,0);  $this->Cell(28, $alto, "SUBTOTAL:", $border, 0, 'R');
		$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_subtotal,2), $border, 1, 'R');
		$this->SetX($columna);
		if($this->fac_descuento!=0){
			$this->SetTextColor(0,0,0);  $this->Cell(28, $alto, "DESCUENTO:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_descuento,2), $border, 1, 'R');	
		}else{
			$this->ln();
		}		
		$this->SetX($columna);
		if (!is_null($this->fac_iva_tras) ) {

			$this->SetTextColor(0,0,0);  $this->Cell(28, $alto, "IVA:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_iva_tras,2), $border, 1, 'R');
		}
		if ($this->fac_ieps_tras) {
			$this->SetX($columna);
			$this->SetTextColor(0,0,0);  $this->Cell(28, $alto, "IEPS:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_ieps_tras,2), $border, 1, 'R');
		}
		if ( !is_null($this->fac_iva_ret) ) {
			$this->SetX($columna);
			$this->SetTextColor(0,0,0);  $this->Cell(28, $alto, "RET. IVA:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_iva_ret,2), $border, 1, 'R');
		}
		if ( !is_null($this->fac_isr_ret) ) {
			$this->SetX($columna);
			$this->SetTextColor(0,0,0);  $this->Cell(28, $alto, "RET. ISR:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_isr_ret,2), $border, 1, 'R');
		}
		$this->SetX($columna);
		$this->SetTextColor(0,0,0);  $this->Cell(28, $alto, "TOTAL:", $border, 0, 'R');
		$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_total,2), $border, 1, 'R');
		
		# -- Importe con letra
		$columna = 40;
		$this->SetY($y);
		$this->SetX($columna);
		$decimal = round(($this->fac_total - floor($this->fac_total)) * 100); // determina los decimales
		$this->SetX($columna);  $this->SetFont('Arial','B',8);
		$this->Cell(40, 4, "IMPORTE CON LETRA", $border, 1);
		$this->SetX($columna);  $this->SetFont('Arial','',7);
		
		$moneda=strtoupper($this->fac_moneda);

		if ($moneda=='PESOS'){
			$abrevMoneda='M.N.';	
		}else if($moneda=="DOLARES"){
			$abrevMoneda='USCY.';
		}
		
		$this->MultiCell(110, 4, strtoupper(num2letras($this->fac_total))." $moneda ".str_pad($decimal, 2, '0', STR_PAD_LEFT)."/100 $abrevMoneda", $border, 1);
		$y = $this->GetY();
		
		# -- Otros datos de la factura
		$this->Ln(2);
		$this->SetX($columna);  $this->SetFont('Arial','B',7);  $this->Cell(60, 3, "FORMA DE PAGO", $border, 1);
		$this->SetX($columna);  $this->SetFont('Arial','',7);   $this->Cell(60, 3, strtoupper($this->fac_forma_pago), $border, 1);
		//$this->Ln(2);
		if (!empty($this->fac_metodo_pago)) {
			$this->Ln(2);
			$this->SetX($columna);  $this->SetFont('Arial','B',7);  $this->Cell(50, 3, "METODO DE PAGO", $border, 1);
			$MetPago=strtoupper($this->fac_metodo_pago);
			$MetPago.= empty ($this->NumCtaPago)? '' : ' CTA '.$this->NumCtaPago;
			$this->SetX($columna);  $this->SetFont('Arial','',7);   $this->Cell(50, 3,$MetPago , $border, 1);
		}
		//
		$this->Ln(2);
		if ($this->tipoDeFactura=='cfd'){
			$this->SetX($columna);  $this->SetFont('Arial','B',7);  $this->Cell(50, 3, utf8_decode("NO. APROBACIÓN"), $border, 0);
			$this->SetX(70);  $this->SetFont('Arial','B',7);  $this->Cell(50, 3, utf8_decode("AÑO APROBACIÓN"), $border, 0);
			$this->SetX(100);  $this->SetFont('Arial','B',7);  $this->Cell(50, 3, utf8_decode("CERTIFICADO"), $border, 1);
			$this->SetX($columna);  $this->SetFont('Arial','',7);   $this->Cell(50, 3, strtoupper($this->fac_noAprovacion), $border, 0);
			$this->SetX(70);        $this->SetFont('Arial','',7);   $this->Cell(50, 3, strtoupper($this->fac_anoAprovacion), $border, 0);
			$this->SetX(100);       $this->SetFont('Arial','',7);   $this->Cell(50, 3, strtoupper($this->fac_noCertificado), $border, 1);
		}
		if ($this->tipoDeFactura=='cfdi'){
			$this->SetX($columna);  $this->SetFont('Arial','B',7);  $this->Cell(50, 3, utf8_decode("CERTIFICADO"), $border, 1);
			$this->SetX($columna);       $this->SetFont('Arial','',7);   $this->Cell(50, 3, strtoupper($this->fac_noCertificado), $border, 1);
		}
		$columna = 100;
		$this->SetY($y);
		$this->Ln(2);
		$this->SetX($columna+18);  $this->SetFont('Arial','B',7);  $this->Cell(60, 3, "TIPO DE DOCUMENTO", $border, 1);
		$this->SetX($columna+18);  $this->SetFont('Arial','',7);   $this->Cell(60, 3, strtoupper($this->fac_tipo_comp), $border, 1);
		
		if (!empty($this->fac_condic_pago)) {
			$this->Ln(2);
			//$this->SetX($columna);  $this->SetFont('Arial','B',7);  $this->Cell(50, 3, "CONDICIONES DE PAGO", $border, 1);
			//$this->SetX($columna);  $this->SetFont('Arial','',7);   $this->MultiCell(50, 3, strtoupper($this->fac_condic_pago), $border, 1);
		}
		$this->afterFooter();		
	}
}
?>