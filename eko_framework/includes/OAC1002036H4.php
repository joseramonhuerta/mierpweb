<?php
require_once(dirname(__FILE__).'/mfpdf.php');

class OAC1002036H4 extends mFPDF {
	
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		parent::__construct($orientation,$unit,$format,$params);
		
		$this->x1Emisor=10;
		$this->x1Sucursal=$this->x1Emisor+66;
	}
	//Imprime las imagenes
	function imprimeLogo(){
		//$this->Image("images/logos/upct_logo.jpg",10,12,40);
		$this->Image("images/logos/Arq_1.jpg",131,14,75);		
		$this->SetXY(174,15);
		//$this->Image("images/logos/Elastix-Certified.jpg",174,12,15);
		//$this->Image("images/logos/BannerResellerCFDI_200x200.jpg",190,12,14);
	}
	function afterFooterx(){
		$border=0;
		# -- Leyenda Final
		$this->SetY(258);
		$this->SetFont('Arial','B',8);
		if ($this->tipoDeFactura=='cfd'){
			$this->Cell(140,4, utf8_decode("ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFD."), $border, 0);	
		}else{
			$this->Cell(140,4, utf8_decode("ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI."), $border, 0);
		}
		
		$this->SetXY(156,258);
		$this->Cell(18,4, "SUCURSAL:", $border, 0);
		$this->SetFont('Arial','',8);
		$this->SetXY(173,258);
		$this->Cell(10,4, "237", $border, 0);		
		$this->SetFont('Arial','B',8);
		$this->SetXY(180,258);
		$this->Cell(15,4, "CUENTA:", $border, 0);
		$this->SetFont('Arial','',8);
		$this->SetXY(193,258);
		$this->Cell(10,4, "7814684", $border, 0);
		
		$this->SetXY(162,261);
		$this->SetFont('Arial','B',8);
		$this->Cell(14,4, "CLABE:", $border, 0);
		$this->SetXY(173,261);
		$this->SetFont('Arial','',8);
		$this->Cell(10,4, "002-744-02377814684-1", $border, 0);
		
		$this->Image("images/logos/logo_banamex.jpg",128,258,28);
		
		$this->SetFont('Arial','B',8);
		$this->SetY(261);
		$this->Cell(45,4,  "http://www.upctechnologies.com",$border, 0, 'L', 0, 'http://www.upctechnologies.com');
	}
	public function Footer() {
		//print_r($this);exit;
		$this->SetY(-$this->limite_det);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		$y = $this->GetY() + 3;
		$this->SetY($y);	

		$this->b4Footer();

		$y=$this->GetY();
		
		# -- Dibuja marcos al detalle y al footer
		$this->SetDrawColor(0, 0, 0);
		$this->SetLineWidth(0.2);
		$this->SetFillColor(0,0,0);
		$this->Rect(152, 223, 28, 34, true); // marco relleno de los totales
		$this->Rect(40, 223, 165, 34); // marco del footer
		
		# -- Marco CBB
		$this->SetFont('Arial','B',6);
		$this->SetXY($this->GetX(), 223);
		$this->SetFillColor(0,0,0);
		$this->SetTextColor(255,255,255);
		$this->Cell(28, 4, "CODIGO BIDIMENSIONAL", 1, 1, 'C', true);
		$this->Image($this->cbb, $this->GetX() - 1, $this->GetY() + 1, 30, 30);
		$this->SetTextColor(0,0,0);
		
		# -- Totales
		$border = 0;
		$columna = 152;
		$alto   = 4.7;
		$this->SetFont('Arial','B',9);
		$this->SetXY($columna,$y);
		$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "SUBTOTAL:", $border, 0, 'R');
		$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_subtotal,2), $border, 1, 'R');
		$this->SetX($columna);
		if($this->fac_descuento!=0){
			$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "DESCUENTO:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_descuento,2), $border, 1, 'R');	
		}else{
			$this->ln();
		}		
		$this->SetX($columna);
		if (isset($this->Custom_fac_iva_tras) ) {

			$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "IVA:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->Custom_fac_iva_tras,2), $border, 1, 'R');
		}else if (!is_null($this->fac_iva_tras) ) {

			$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "IVA:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_iva_tras,2), $border, 1, 'R');
		}
		if ($this->fac_ieps_tras) {
			$this->SetX($columna);
			$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "IEPS:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_ieps_tras,2), $border, 1, 'R');
		}
		if ( !is_null($this->fac_iva_ret) ) {
			$this->SetX($columna);
			$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "RET. IVA:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_iva_ret,2), $border, 1, 'R');
		}
		if ( !is_null($this->fac_isr_ret) ) {
			$this->SetX($columna);
			$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "RET. ISR:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_isr_ret,2), $border, 1, 'R');
		}
		$this->SetX($columna);
		$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "TOTAL:", $border, 0, 'R');
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