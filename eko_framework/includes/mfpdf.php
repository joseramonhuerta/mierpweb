<?php
require(dirname(__FILE__).'/fpdf.php');

class mFPDF extends FPDF {
	public $em_nombre, $em_calle, $em_no_ext, $em_no_int, $em_colonia, $em_ciudad, $em_estado, $em_pais, $em_cp, $em_rfc;
	public $exp_calle, $exp_no_ext, $exp_no_int, $exp_colonia, $exp_ciudad, $exp_estado, $exp_pais, $exp_cp;
	public $cli_nombre, $cli_calle, $cli_no_ext, $cli_no_int, $cli_colonia, $cli_ciudad, $cli_estado, $cli_pais, $cli_cp, $cli_rfc;
	public $fac_serie, $fac_folio, $fac_fecha, $fac_subtotal, $fac_iva_tras, $fac_ieps_tras, $fac_iva_ret, $fac_isr_ret, $fac_total, $fac_descuento;
	public $fac_forma_pago, $fac_condic_pago, $fac_metodo_pago, $fac_tipo_comp, $fac_tipo_cambio, $fac_moneda;
	public $fac_sello_dig, $fac_sello_SAT, $fac_cadena_orig, $fac_uuid, $fac_certif_SAT, $fac_fecha_certif;
	public $limite_det, $total_detalle, $detalle_actual, $expedicion;
	public $arr_det, $arr_det_add, $contador_det;
	public $id_suc;
	public $exp_localidad;
	var $imprimiendoAduanas=false;
	var $imprimiendoSubdetalles =false;
	var $imprimiendoComponentes =false;
	//var $imprimiendoAduanas=false;
	
	function mFPDF($orientation='P',$unit='mm',$format='Letter',$params=array()){
		if (empty($this->yEncabezadoDeTabla))	$this->yEncabezadoDeTabla=58;
		
		if(isset($params['logo'])){
			$this->logo=$params['logo'];
		}
		
		if(isset($params['x1Emisor'])){
			$this->x1Emisor=$params['x1Emisor'];
		}else{
			$this->x1Emisor=10;
		}
		
		if(isset($params['x1Sucursal'])){
			$this->x1Sucursal=$params['x1Sucursal'];
		}else{
			$this->x1Sucursal=$this->x1Emisor+88;
		}
		if ( empty($this->anchoCellRegimen) ) {
			$this->anchoCellRegimen= 130;
		}
		
		parent::__construct( $orientation, $unit, $format );
		$this->imprimiendoConceptos=true;
		$this->SetAutoPageBreak(true, 63);	
	}
	
	function b4Header(){
		return true;
	}
	
	public function Header() {
		if ($this->b4Header()!=true){
			return false;
		}
		
		$this->imprimirEmisor();
		$this->imprimirReceptor();
		$this->imprimirDatosDeLaFactura();
		
		/*$this->SetFont('Arial','B',8);
		$this->Cell(25.5, 4, utf8_decode('RÉGIMEN FISCAL:'), 0);
		$this->SetFont('Arial','',8);
		$this->Cell(92, 4, utf8_decode($this->regimenes), 0);*/
		/*$this->setX(167);
		$this->SetFont('Arial','B',7);   $this->Cell(38, 4, '"EFECTOS FISCALES AL PAGO"', 0, 0, 'R');*/
		$this->afterHeader();		
	}
	
	function afterHeader(){
		$this->imprimeEncabezadoDeTabla();
	}
	
	private function imprimirEmisor(){
		//---------------------------------------------------------------------------------------------------
		//												 -- Emisor --
		//---------------------------------------------------------------------------------------------------
		$this->imprimeLogo();
		$border  = 0;
		$columna = $this->x1Emisor;				
				
		$this->SetTextColor(0,0,0);		
		$this->SetFont('Arial','B',12);
		$this->SetXY($columna,15); $this->Cell(160, 4, $this->em_nombre, $border, 1);	//ENCABEZADO CON EL NOMBRE DE LA EMPRESA
		$this->SetX($columna);     $this->Cell(80, 1, "", $border, 1);
		
		
		
		$this->yEmisor=$this->GetY();		
		$this->imprimirMatriz();	
		
		$this->imprimirSucursal();
	}
	//----------------------------------------------------
	//				IMPRIME DATOS DE LA EMPRESA
	//----------------------------------------------------
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
		$this->Cell(80, 3, "R.F.C.: ".$this->em_rfc, $border, 1);
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
	//-----------------------------------------------
	//				IMPRIME DATOS DE LA SUCURSAL
	//-----------------------------------------------
	 public function imprimirSucursal(){	
		if ( !$this->expedicion ){
			$this->expedicion=true;
			
			$this->exp_calle=$this->em_calle;
			$this->exp_no_ext=$this->em_no_ext;
			$this->exp_no_int=$this->em_no_int;
			$this->exp_colonia=$this->em_colonia;
			$this->exp_cp=$this->em_cp;
			$this->exp_localidad=$this->em_localidad;
			$this->exp_ciudad=$this->em_ciudad;
			$this->exp_estado=$this->em_estado;
			$this->exp_pais= $this->em_pais;

		}
		$border=0;	
		$columna2=$this->x1Sucursal;
		$this->SetXY($columna2,$this->yEmisor);
		$this->SetFont('Arial','B',6);
		$this->Cell(80, 3, ($this->expedicion) ? "EXPEDIDO EN:" : "", $border, 1);

		$this->SetFont('Arial','',6);
		$this->SetX($columna2);
		$this->Cell(80, 3, $this->exp_calle." ".$this->exp_no_ext." ".$this->exp_no_int." ".$this->exp_colonia, $border, 1);
		$this->SetX($columna2);
		
		$cod_pos = ($this->exp_cp) ? "C.P.: ".$this->exp_cp : "";
		$localidad=$this->getLocalidad($this->exp_localidad,$this->exp_ciudad, $this->exp_estado, $this->exp_pais);
		
		if($this->telSuc || $this->faxSuc){	//Si existe el telefono, el codigo postal se imprime jnto con la direccion
			$localidad=$localidad." ".$cod_pos;
			//$telSuc =  "TEL.: ".$this->telSuc;
			$telSuc = ($this->telSuc) ? "TEL.: ".$this->telSuc : "";
			$faxSuc = ($this->faxSuc) ? "FAX: ".$this->faxSuc : "";
			if ($telSuc){
				$ultimaLinea=$telSuc." ".$faxSuc;	
			}else{
				$ultimaLinea=$faxSuc;
			}
			
		}else{
			$ultimaLinea= $cod_pos;
		}
		
		$this->Cell(80, 3, $localidad, $border, 1);
		$this->SetX($columna2);
		$this->Cell(80, 3, $ultimaLinea, $border, 1);
	}
	//------------------------------------
	//		IMPRIME DATOS DEL Receptor
	//------------------------------------
	function imprimirReceptor(){
		
		$border=0;	
		$this->SetY(38);
		$this->SetFont('Arial','B',7);
		$this->Cell(80, 3, "DOMICILIO FISCAL DEL CLIENTE", $border, 1);
		$this->SetFont('Arial','B',9);
		$this->Cell(128, 5, $this->cli_nombre, $border, 1);
		// En el siguiente codigo, verifico si el domicilio completo es demasiado largo como para que se encime en
		// las leyendas de la derecha, para truncarlo a un determinado numero de caracteres y el sobrante lo agrego
		// a la siguiente linea (localidad)
		$domicilio = $this->cli_calle." ".$this->cli_no_ext." ".$this->cli_no_int." ".$this->cli_colonia;
		$cod_pos = ($this->cli_cp) ? "  C.P.: ".$this->cli_cp : "";
		$localidad = $this->getLocalidadCliente($this->cli_localidad, $this->cli_ciudad, $this->cli_estado, $this->cli_pais).$cod_pos;
		$max_posic = 68;  // maximo largo de la cadena del domicilio
		if (strlen($domicilio) > $max_posic){
			$tmp = '';
			$arr_dom  = explode(' ',$domicilio);
			$arr_cont = count($arr_dom);
			if ($arr_cont > 1){
				while ($arr_cont > 0){
					$arr_cont--;
					$tmp = trim($arr_dom[$arr_cont].' '.$tmp);
					$pos = strripos($domicilio, $tmp);
					if ($pos < $max_posic){
						$domicilio = substr($domicilio, 0, $pos-1);
						$localidad = $tmp.' '.$localidad;
						break;
					}
				}
			} else {
				$domicilio = substr($domicilio,0,$max_posic-1);
				$localidad = substr($domicilio,$max_posic).$localidad;
			}
		}
		$this->SetFont('Arial','',8);
		$this->Cell(128, 4, $domicilio, $border, 1);
		$this->Cell(128, 4, $localidad, $border, 1);
				
		
		$rfc = ($this->cli_rfc) ? "R.F.C.: ".$this->cli_rfc : "";
		$this->Cell(128, 4, $rfc, $border, 1);		
	}
	//------------------------------------
	//		IMPRIME DATOS DE LA  FACTURA
	//------------------------------------
	public function imprimirDatosDeLaFactura(){
		# -- Datos de la factura
		$border=0;
		$columna = 127;
		$ver_serie = (empty($this->fac_serie)) ? '' : $this->fac_serie."-";
		$altoCell=4;
		if ($this->tipoDeFactura=='cfdi'){		
			$altoCell=3.5;
			$this->SetXY($columna,38.7);
			$this->SetFont('Arial','B',7);  $this->Cell(27, $altoCell, "FOLIO FISCAL:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(51, $altoCell, $this->fac_uuid, $border, 1, 'R');
			$this->SetX($columna);
			$this->SetFont('Arial','B',7);  $this->Cell(40, $altoCell, "CERTIFICADO SAT:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(38, $altoCell, $this->fac_certif_SAT, $border, 1, 'R');
			$this->SetX($columna);
			$this->SetFont('Arial','B',7);  $this->Cell(40, $altoCell, "FECHA CERTIFICACION:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(38, $altoCell, $this->getFechaFormato($this->fac_fecha_certif), $border, 1, 'R');
		}else{
			$this->SetXY($columna,36);
			$this->Cell(40, $altoCell,'', $border, 1, 'R');
			 $this->Cell(40, $altoCell,'', $border, 1, 'R');
			$this->Cell(40, $altoCell,'', $border, 1, 'R');
		}
		$y=$this->GetY();		 
		
		if ($this->tipoDeFactura=='cfd'){
			$this->SetY($y-4);
			$this->SetFont('Arial','B',10); 	
		}
		$this->SetX($columna);
		$this->Cell(40, $altoCell, "SERIE Y FOLIO:", $border, 0, 'L');
		if ($this->tipoDeFactura=='cfd'){
			$this->SetFont('Arial','',15); 	
		}
		$this->Cell(38, $altoCell, $ver_serie.$this->fac_folio, $border, 1, 'R');
		$this->SetX($columna);
		if ($this->tipoDeFactura=='cfd'){
			$this->SetFont('Arial','B',10); 	
		}
		$this->Cell(40, $altoCell, utf8_decode("FECHA ELABORACION:"), $border, 0, 'L');
		if ($this->tipoDeFactura=='cfd'){
			$this->SetFont('Arial','',7); 	
		}
		 $this->Cell(38, $altoCell, $this->getFechaFormato($this->fac_fecha), $border, 1, 'R');
		
		
		if ($this->tipoDeFactura=='cfdi'){
			$y=$this->GetY();
			//	$this->SetY($y-1);
		}
		
		$this->SetX($columna);
		if ( !empty( $this->LugarExpedicion ) ){
			$this->SetFont('Arial','B',7);
			$this->Cell(34, $altoCell, utf8_decode('LUGAR DE EXPEDICIÓN:'), 0);
			$this->SetFont('Arial','',8);
			$this->Cell(44, $altoCell, $this->LugarExpedicion, 0, 1,'R');						
		}else{
			$this->Cell(44, $altoCell, '', 0, 1,'R');						
		}
		
		
	}
	//--------------------------------------------------------------------------------------
	//						 Encabezado del detalle
	//--------------------------------------------------------------------------------------
	function imprimeEncabezadoDeTabla(){	//DESPUES DE IMPRIMIR EL HEADER VIENE AKI
		if ($this->imprimiendoSubdetalles==true || $this->imprimiendoAduanas || $this->imprimiendoConceptos==true || $this->imprimiendoComponentes==true ){				
			$this->SetFont('Arial','B',8);
			
			if ($this->tipoDeFactura=='cfdi'){
				$this->SetXY(10,$this->yEncabezadoDeTabla + 2 );
			}else{
				$this->SetXY(10,$this->yEncabezadoDeTabla );
			}
			
			$this->SetFillColor(0,0,0);
			$this->SetTextColor(255,255,255);
			$this->Cell($this->arr_det[0], 5, "CANTIDAD", 'LTB', 0, 'L', true);
			$this->Cell($this->arr_det[1], 5, utf8_decode("DESCRIPCIÓN"), 'TB', 0, 'L', true);
			$this->Cell($this->arr_det[2], 5, "U.M.", 'TB', 0, 'L', true);
			$this->Cell($this->arr_det[3], 5, "PRECIO UNITARIO", 'TB', 0, 'R', true);
			$this->Cell($this->arr_det[4], 5, "IMPORTE", 'TRB', 0, 'R', true);
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
			$this->Rect($x1Viñeta,$y1Viñeta+1,1.5,1.5,'DF');				
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
	function setTipoFactura($tipo){
		//$tipo: cfd | cfdi
		$this->tipoDeFactura=strtolower($tipo);
	}
	public function AnchoDetalles() { // define los anchos de las columnas del detalle
		$this->arr_det = array(18,98,23,28,28);
	}
	
	public function setLimiteDetalle() {
		$this->limite_det = 63;
		$this->bMargin=100;
		$this->contador_det = 0; // Iniciio el contador de impresion de conceptos
	}
	public function createBCC(){
		require_once("phpqrcode.php");		
		$PNG_TEMP_DIR = dirname(__FILE__).DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'tmp'.DIRECTORY_SEPARATOR;
				
		$filename = $PNG_TEMP_DIR.'cbb'.md5("?re=$this->em_rfc&rr=$this->cli_rfc&tt=$this->fac_total&id=$this->fac_uuid").'.png';
        QRcode::png("?re=$this->em_rfc&rr=$this->cli_rfc&tt=$this->fac_total&id=$this->fac_uuid", $filename, 'H', 2, 2);
		$this->cbb = $filename;
		$this->detalle_actual = 0;
	}
	function imprimeLogo(){
		if(isset($this->logo)){
			$imagen=$this->logo['imagen'];
			$x=$this->logo['x'];
			$y=$this->logo['y'];

			//$w=$this->logo['w'];
			//$h
			$this->Image($imagen,$x,$y);	
		}
	}
	
	
	function b4Footer(){
		$this->SetX(40);	
		$this->SetFont('Arial','',8);
		$this->Cell(0, 4, "Estimado Cliente, gracias por habernos brindado la oportunidad de servirle.", 0, 0, 'L');
		
		$this->setX(167);
		$this->SetFont('Arial','B',7);   $this->Cell(38, 4, '"EFECTOS FISCALES AL PAGO"', 0, 1, 'R');		
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
		if ( isset($this->Custom_fac_iva_tras) ) {
			$this->SetTextColor(255,255,255);  $this->Cell(28, $alto, "IVA:", $border, 0, 'R');
			$this->SetTextColor(0,0,0);        $this->Cell(25, $alto, $this->moneda_format($this->fac_iva_tras,2), $border, 1, 'R');
		}else if ( !is_null($this->fac_iva_tras) ) {
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
	
	function afterFooter(){
		$border=0;
		# -- Leyenda Final
		$this->SetY(258);
		$this->SetFont('Arial','B',8);
		if ($this->tipoDeFactura=='cfd'){
			$this->Cell(140,4, utf8_decode("ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFD."), $border, 0);	
		}else{
			$this->Cell(140,4, utf8_decode("ESTE DOCUMENTO ES UNA REPRESENTACIÓN IMPRESA DE UN CFDI."), $border, 0);
		}
		
		$this->SetFont('Arial','B',8);
		$this->Cell(55,4,  "http://www.upctechnologies.com",$border=0, 0, 'R', 0, 'http://www.upctechnologies.com');
	}
	
	
	
	//public function ImprimeDetalles($cantidad, $unidad, $descripc, $valor_unit, $importe, $no_identif,$detalle,$subconceptos,$elementosDeAduana=array(),$params=array()) {
	public function ImprimeDetalles($detalle) {
		$unidad   = strtoupper(utf8_decode($detalle['DescUni']));
		$tipoArt=strtoupper($detalle['TipoArt']);
		if ($tipoArt=="S"){
			if($this->em_rfc == 'LIN070614CK3')
				$unidad = 'NO APLICA';
			else
				$unidad="SERVICIO";
		}
		$border=0;
		
		$this->imprimiendoConceptos=true;
		$cantidad=$detalle['Cantidad'];
		$valor_unit=$detalle['PrecioU'];
		$importe=$detalle['Importe'];	//$det_importe$cantidad*$valor_unit;
		
		$descripcion = strtoupper(utf8_decode($detalle['Descripcion']));
		$detalleProd=strtoupper(utf8_decode($detalle['Detalle']));		
		$skus=utf8_decode($detalle['SKUs']);		
		
		$this->SetFont('Arial','',8);
		
		$this->SetX($this->getCoord(0) + 9.5);
		$this->Cell($this->arr_det[0]-1, 4,  number_format($cantidad,2), 0, 0, 'R');
		
		$this->ImpDetalle(2, $unidad, '', '');
		
		$this->ImpDetalle(3, $this->moneda_format($valor_unit,2), '', 'R');
		$this->ImpDetalle(4, $this->moneda_format($importe,2), '', 'R');
		
		$this->ImpDetalleM(1, $descripcion, '', 'L',$detalleProd, $skus);
		
		if (!empty($detalle['predial'])){
			$this->SetX($this->getCoord(1) + 10);
			$border=0;
			$this->SetFont('ARIAL', 'B', 7);
			$this->Cell(24, 4,formatearTexto("Cuenta Predial:"), $border, 0, "L");
			$border=0;
			$this->SetFont('ARIAL', '', 7);
			$this->SetX($this->getCoord(1) + 34);
			$this->Cell(30, 4,$detalle['predial'], $border, 0, "L");
			
			$this->ln();
		}
		$this->imprimiendoConceptos=false;

			if ( !empty($detalle['componentes']) ){
				$this->imprimeComponentes($detalle['componentes']);
			}	
			
		
		
		$subconceptos=array();			
		if (strlen($detalle['aduana'])>0){
			$subconceptos=json_decode($detalle['aduana'],true);		
		}
		$this->imprimeSubconceptos($subconceptos);
		
		$elementosDeAduana=array();				
		if (isset($detalle['infoAduana'])){			
			if (strlen($detalle['infoAduana'])>0){				
				$elementosDeAduana=json_decode($detalle['infoAduana'],true);		
			}	
		}
		$this->imprimeElementosDeAduana($elementosDeAduana);
		$this->Cell(195,1,"",''.$border,1); // espaciado entre detalles
	}
	
	public function imprimeElementosDeAduana($elementosDeAduana=array()){
		$numElements=sizeof($elementosDeAduana);
		$xConcepto=29;
		$xPedimento=100;
		$xImporte=150;
		$this->subconceptoY1=$this->GetY();
		$this->subconceptoX1=$xConcepto-1;

		
		$columns=array(
			0=>array(
				'dataindex'=>'NomAduana',
				'width'=>120,
				'align'=>'L'
			),
			1=>array(
				'dataindex'=>'FecAduana',
				'width'=>20,
				'align'=>'C'),
			2=>array(
				'dataindex'=>'NumAduana',
				'width'=>20,
				'align'=>'R')
		);

		/*	IMPRIME EL ENCABEZADO Y VIÑETA	*/
			$this->imprimiendoConceptos=true;		//PARA QUE IMPRIMA EL ENCABEZADO DE CONCEPTOS
		if (sizeof($elementosDeAduana)>0){
			$this->SetFont('ARIAL', 'B', 7);
			$encabezado="CONCEPTOS ADUANALES";		
			$x1Viñeta=$xConcepto;
			
			$this->SetX($x1Viñeta+2);
			$this->Cell(0,4,$encabezado,0,0);	
			
			$y1Viñeta=$this->GetY();	
			$this->SetLineWidth(.5);	
			$this->Rect($x1Viñeta,$y1Viñeta+1,1.5,1.5,'DF');
			
			$this->ln();
		}
				
		
		/*		 IMPRIME EL CONTENIDO DEL ARREGLO			*/				
		$border=0;
		$this->SetFont('Arial','I',6);
		$this->escribiendoSubconceptos=true;
		$this->subconceptoEscrito=false;
		$xConcepto--;
		for($i=0;$i<$numElements;$i++){
			$this->imprimiendoAduanas=true;
			//Antes de escribir la celda, FPDF revisa si la celda cabe en la pagina, sino cabe crea una nueva pagina
			//Antes de saltar a la nueva página, talvez sea necesario cerrar el recuadro que cubre a los subcobceptos			
			//La pagina podria saltar antes de escribir el primer concepto, o con la instruccion $this->ln();			
			//EN el caso de saltar al escribir el concepto, se va a pintar el recuadro siempre y cuando ya haya sido impreso por lo menos un subconcepto
						
			$NomAduana=utf8_decode($elementosDeAduana[$i]['NomAduana']);
			$FecAduana=utf8_decode($elementosDeAduana[$i]['FecAduana']);
			$NumAduana=$elementosDeAduana[$i]['NumAduana'];			
			
			$this->SetX($xConcepto);

			$rec=array(
				'NomAduana'=>$NomAduana,
				'FecAduana'=>$FecAduana,
				'NumAduana'=>$NumAduana
			);
			//$params['border']=1;
			$this->recMultiline($columns,$rec,$xConcepto);
						
			/*$this->Cell(75,4,strtoupper($concepto),$border,0);
			$this->Cell(75,4,strtoupper($pedimento),$border,0);
			$this->Cell(20,4,$importe,$border,0,'R');*/
			
			$this->subconceptoEscrito=true;
			
			$x2=$this->GetX();		
			
			//$this->ln(); 	
			$y2=$this->GetY();				  	
		}
			$this->imprimiendoConceptos=false;		//PARA QUE IMPRIMA EL ENCABEZADO DE CONCEPTOS
		$this->imprimiendoAduanas=false;		
	}
	
	public function imprimeComponentes($subconceptos){
		
		$numSubconceptos=sizeof($subconceptos);
		$xConcepto=29;

		$columns=array(
			0=>array(
				'dataindex'=>'Cantidad',
				'width'=>18,
				'align'=>'R'
			),
			1=>array(
				'dataindex'=>'Descripcion',
				'width'=>70,
				'align'=>'L')
		);

		/*	IMPRIME EL ENCABEZADO Y VIÑETA	 */
		$this->imprimiendoConceptos=true;
		if (sizeof($subconceptos)>0){			
			$this->SetFont('ARIAL', 'B', 7);
			$encabezado=UTF8_DECODE("COMPONENTES DEL KIT");		
			$x1Viñeta=$xConcepto;
			
				
			$this->SetX($x1Viñeta+2);
			$this->Cell(0,4,$encabezado,0,0);
			//-------------
			$this->SetLineWidth(.5);	
			$y1Viñeta=$this->GetY();
			$this->Rect($x1Viñeta,$y1Viñeta+1,1.5,1.5,'DF');
			$this->ln();
			
		}
		$xConcepto--;
		/*		 IMPRIME EL CONTENIDO DEL ARREGLO			*/
		$border=0;
		$this->SetFont('Arial','',6);
		
		
		for($i=0;$i<$numSubconceptos;$i++){
			$this->imprimiendoComponentes=true;
			//Antes de escribir la celda, FPDF revisa si la celda cabe en la pagina, sino cabe crea una nueva pagina
			//Antes de saltar a la nueva página, talvez sea necesario cerrar el recuadro que cubre a los subcobceptos			
			//La pagina podria saltar antes de escribir el primer concepto, o con la instruccion $this->ln();			
			//EN el caso de saltar al escribir el concepto, se va a pintar el recuadro siempre y cuando ya haya sido impreso por lo menos un subconcepto
						
			$this->SetX($xConcepto);
			
			$rec=array(
				'Cantidad'=>number_format( $subconceptos[$i]['Cantidad'], 2),
				'Descripcion'=>strtoupper(utf8_decode($subconceptos[$i]['Descripcion']))				
			);
			$this->recMultiline($columns,$rec,$xConcepto);			
			//$this->imprimiendoComponentes=false;			
			$x2=$this->GetX();					
			//$this->ln(); 	
			$y2=$this->GetY();				  	
		}
		$this->imprimiendoComponentes=false;
		$this->imprimiendoConceptos=false;
		if ($numSubconceptos>0){
			//$this->Rect($this->subconceptoX1, $this->subconceptoY1, 202-$this->subconceptoX1, $y2-$this->subconceptoY1);	
		}
		
	}
	public function imprimeSubconceptos($subconceptos){
		
		$numSubconceptos=sizeof($subconceptos);
		$xConcepto=29;		
		//------------------------------------------------
		//				ESTO ES PARA EL MARCO
		//------------------------------------------------
		//$this->subconceptoY1=$this->GetY();	//MARCO
		//$this->subconceptoX1=$xConcepto-1;	//MARCO
		//$this->subconceptoEscrito=false;
				
		
		//-----------------------------------------
		//			
		//-----------------------------------------
		$columns=array(
			0=>array(
				'dataindex'=>'concepto',
				'width'=>70,
				'align'=>'L'
			),
			1=>array(
				'dataindex'=>'pedimento',
				'width'=>70,
				'align'=>'L'),
			2=>array(
				'dataindex'=>'importe',
				'width'=>20,
				'align'=>'R')
		);

		/*	IMPRIME EL ENCABEZADO Y VIÑETA	*/
		$this->imprimiendoConceptos=true;		//PARA QUE IMPRIMA EL ENCABEZADO DE CONCEPTOS
		if (sizeof($subconceptos)>0){
			
			$this->SetFont('ARIAL', 'B', 7);
			$encabezado=UTF8_DECODE("DESCRIPCIÓN DE GASTOS DE TERCEROS");		
			$x1Viñeta=$xConcepto;
						
			$this->SetX($x1Viñeta+2);
			$this->Cell(0,4,$encabezado,0,0);	
			$y1Viñeta=$this->GetY();
			$this->SetLineWidth(.5);		
			$this->Rect($x1Viñeta,$y1Viñeta+1,1.5,1.5,'DF');
			$this->ln();
			
		}
		$xConcepto--;
		/*		 IMPRIME EL CONTENIDO DEL ARREGLO			*/
		$border=0;
		$this->SetFont('Arial','',6);
		$this->escribiendoSubconceptos=true;
		
		for($i=0;$i<$numSubconceptos;$i++){
			$this->imprimiendoSubdetalles=true;
			//Antes de escribir la celda, FPDF revisa si la celda cabe en la pagina, sino cabe crea una nueva pagina
			//Antes de saltar a la nueva página, talvez sea necesario cerrar el recuadro que cubre a los subcobceptos			
			//La pagina podria saltar antes de escribir el primer concepto, o con la instruccion $this->ln();			
			//EN el caso de saltar al escribir el concepto, se va a pintar el recuadro siempre y cuando ya haya sido impreso por lo menos un subconcepto
						
			$concepto=utf8_decode($subconceptos[$i]['concepto']);
			$pedimento=utf8_decode($subconceptos[$i]['subconcepto']);
			$importe=$subconceptos[$i]['ImpFacDetSub'];

			if (is_numeric($importe)){
				$importe=$this->moneda_format($importe,2);
			}
			
			$this->SetX($xConcepto);
			
			$rec=array(
				'concepto'=>$concepto,
				'pedimento'=>$pedimento,
				'importe'=>$importe
			);
			$this->recMultiline($columns,$rec,$xConcepto);
						
			/*$this->Cell(75,4,strtoupper($concepto),$border,0);
			$this->Cell(75,4,strtoupper($pedimento),$border,0);
			$this->Cell(20,4,$importe,$border,0,'R');*/
			
			$this->subconceptoEscrito=true;
			
			$x2=$this->GetX();		
			
			//$this->ln(); 	
			$y2=$this->GetY();				  	
		}
		$this->imprimiendoSubdetalles=false;
		$this->imprimiendoConceptos=false;
		if ($numSubconceptos>0){
			//$this->Rect($this->subconceptoX1, $this->subconceptoY1, 202-$this->subconceptoX1, $y2-$this->subconceptoY1);	
		}
		
	}
	protected function ImpDetalle($campo, $texto, $border, $align) {
		$this->SetX($this->getCoord($campo) + 10);
		$this->Cell($this->arr_det[$campo], 4, $texto, $border, 0, $align);
	}
	
	protected function ImpDetalleM($campo, $texto, $border, $align,$detalle, $skus) {
		$this->SetX($this->getCoord($campo) + 10);
		$pattern = "(/\*( )*)";
		$textoConSalto = preg_replace($pattern, "\n", $texto);
		 
		// voy a determinar si el concepto tiene detallado en la addenda para mostrarlo
		$this->contador_det++;
		//$detalle = (isset($this->arr_det_add[$this->contador_det])) ? "\n".$this->arr_det_add[$this->contador_det] : '';
		$detalle = ($detalle!='') ? "\n".$detalle : '';
		$detalle = preg_replace($pattern, "\n", $detalle);
		$detalle=mb_strtoupper($detalle);
		$detalle=$detalle;
		$skus = json_decode($skus);
		$series = array();
		foreach($skus as $sku)
			$series[] = $sku->sku;
		if(count($series)!=0)
			$series = "\nSERIES: ".strtoupper(implode(", ", $series));
		else
			$series = "";
		$this->MultiCell($this->arr_det[$campo], 4, $textoConSalto.$detalle.$series, $border, $align);
	}
	
	public function FinalDetalle() {
		$columna = 10;
		$this->SetFillColor(0,0,0);
		$this->Ln(3);
		if ($this->tipoDeFactura=='cfdi'){
			$this->SetX($columna);
			$this->SetFont('Arial','B',7);
			$this->SetTextColor(255,255,255);
			$this->Cell(195, 4, "SELLO DIGITAL DEL CFDI ", 1, 1, 'L', true);
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
		$this->SetFont('Arial','B',7);
		$this->SetTextColor(255,255,255);
		$this->Cell(195, 4, $mensajeSello, 1, 1, 'L', true);
		$this->SetTextColor(0,0,0);
		$this->SetX($columna);
		$this->SetFont('Arial','',7);
		$this->MultiCell(195, 4,$sello );
		$this->Ln(2);
		$this->SetX($columna);
		$this->SetFont('Arial','B',7);
		$this->SetTextColor(255,255,255);
		$this->Cell(195, 4, $mensajeCadena, 1, 1, 'L', true);
		$this->SetTextColor(0,0,0);
		$this->SetX($columna);
		$this->SetFont('Arial','',7);
		$this->MultiCell(195, 4,$cadenaOriginal,0,'L');
	}
	
	protected function getCadenaOriginal() {
		return utf8_decode("||1.0|$this->fac_uuid|$this->fac_fecha_certif|$this->fac_sello_dig|$this->fac_certif_SAT||");
	}
	
	protected function getFechaFormato($fecha) {
		$anio = substr($fecha, 0, 4);
		$mes  = substr($fecha, 5, 2);
		$dia  = substr($fecha, 8, 2);
		$hora = substr($fecha, 11);
		return $dia."/".strtoupper($this->getNombreMes($mes))."/".$anio." ".$hora;
	}
	
	protected function getNombreMes($mes) {
		
		switch ($mes) :
			case '01' : $nombre = 'Ene';  break;
			case '02' : $nombre = 'Feb';  break;
			case '03' : $nombre = 'Mar';  break;
			case '04' : $nombre = 'Abr';  break;
			case '05' : $nombre = 'May';  break;
			case '06' : $nombre = 'Jun';  break;
			case '07' : $nombre = 'Jul';  break;
			case '08' : $nombre = 'Ago';  break;
			case '09' : $nombre = 'Sep';  break;
			case '10' : $nombre = 'Oct';  break;
			case '11' : $nombre = 'Nov';  break;
			case '12' : $nombre = 'Dic';  break;
			            default: $nombre=$mes;
		endswitch;
		return $nombre;
	}
	
	protected function getLocalidad($localidad='',$ciudad, $estado, $pais) {
		//$localidad=()? $this->exp_localidad : '';
		 
		//$localidad = '';
		$localidad=trim($localidad);
		//if (!empty($ciudad)) $localidad .= $ciudad;
		if (!empty($ciudad)) {
			if (!empty($localidad)) $localidad .= ", ";
			$localidad .= $ciudad;
		}
		if (!empty($estado)) {
			if (!empty($localidad)) $localidad .= ", ";
			$localidad .= $estado;
		}
		
		if (!empty($pais)) {
			if (!empty($localidad)) $localidad .= ", ";
			$localidad .= $pais;
		}
		
		return $localidad;
	}
	
	protected function getLocalidadCliente($localidad='', $ciudad='', $estado='', $pais='') {
		if (!empty($localidad)) {
			if (!empty($ciudad)) {
				$localidad .= ", $ciudad";
			}
		} else {
			$localidad = $ciudad;
		}

		if (!empty($localidad)) {
			if (!empty($estado)) {
				$localidad .= ", $estado";
			}
		} else {
			$localidad = $estado;
		}

		if (!empty($localidad)) {
			if (!empty($pais)) {
				$localidad .= ", $pais";
			}
		} else {
			$localidad = $pais;
		}
		return  $localidad;
	}
	
	protected function getCoord($num) {
		$coordenada = 0;
		for ($i = 0 ; $i < $num; $i++) :
			$coordenada += $this->arr_det[$i];
		endfor;
		return $coordenada;
	}
	
	public function setNodoAddenda($nodo){
		
		$this->setDetallesConceptos($nodo); // Detalles de los conceptos
	}
	
	private function setDetallesConceptos($nodo){
		// Si existen, guarda en un arreglo los detalles de los conceptos
		$lista_nodos = $nodo->getElementsByTagName('MiFacturaConceptos')->item(0);
		if ($lista_nodos) {
			foreach ($lista_nodos->getElementsByTagName('MiFacturaConcepto') as $nodo_item) {
				$this->arr_det_add[$nodo_item->getAttribute('id')] = utf8_decode(html_entity_decode($nodo_item->getAttribute('subdetalle')));
			}
		}
	}
	public function recMultiline($columns=array(),$rec=array(),$x1,$params=array()){
		$numColumns=sizeof($columns);
		$border= ( isset($params['border']) ) ? $params['border'] : 0 ;

		do{
			$this->SetX($x1);
			for($i=0; $i<$numColumns; $i++){
				$w=$columns[$i]['width'];
				$dataindex=$columns[$i]['dataindex'];
				$dato=$rec[$dataindex];
				$params=$this->partialCell($w,4,$dato);
				$cadena=$params['cadena'];
				$longitud=$params['longitud'];
				$salto=0;
				if ($i==$numColumns-1){
					$salto=1;
				}
				$rec[$dataindex]=substr($dato,$longitud);
				
				$align=isset($columns[$i]['align']) ? $columns[$i]['align']: '' ;
				
				$this->cell($w,3,trim($cadena),$border,$salto,$align);
				$x=$this->GetX();
				$this->SetX($x+5);
				if ($salto==1){
					$this->SetX($x1);
				}

			}
			$hayTexto=false;
			for($i=0;$i<$numColumns;$i++){
				$dataindex=$columns[$i]['dataindex'];
				$dato=$rec[$dataindex];
				if (strlen($dato)>0){
					$hayTexto=true;
				} 
			}
			
		}while($hayTexto==true);

	}
	
	function partialCell($w,$h,$txt,$border=0,$align='J',$fill=0){
		//Output text with automatic or explicit line breaks
		$cw=&$this->CurrentFont['cw'];
		if($w==0)
			$w=$this->w-$this->rMargin-$this->x;
		$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
		$s=str_replace("\r",'',$txt);
		$nb=strlen($s);
		if($nb>0 && $s[$nb-1]=="\n")
			$nb--;
		$b=0;
		if($border)
		{
			if($border==1)
			{
				$border='LTRB';
				$b='LRT';
				$b2='LR';
			}
			else
			{
				$b2='';
				if(strpos($border,'L')!==false)
					$b2.='L';
				if(strpos($border,'R')!==false)
					$b2.='R';
				$b=(strpos($border,'T')!==false) ? $b2.'T' : $b2;
			}
		}
		$sep=-1;
		$i=0;
		$j=0;
		$l=0;
		$ns=0;
		$nl=1;
		while($i<$nb)
		{
			//Get next character
			$c=$s{$i};
			if($c=="\n")
			{
				//Explicit line break
				if($this->ws>0)
				{
					$this->ws=0;
					$this->_out('0 Tw');
				}
				return array(
					'cadena'=>substr($s,$j,$i-$j),
					'longitud'=>$i-$j
				);
			}
			if($c==' ')
			{
				$sep=$i;
				$ls=$l;
				$ns++;
			}
			$l+=$cw[$c];
			if($l>$wmax)
			{
				//Automatic line break
				if($sep==-1)
				{
					if($i==$j)
						$i++;
					if($this->ws>0)
					{
						$this->ws=0;
						$this->_out('0 Tw');
					}				
					return array(
						'cadena'=>substr($s,$j,$i-$j),
						'longitud'=>$i-$j
					);
					//$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
				}
				else
				{
					if($align=='J')
					{
						$this->ws=($ns>1) ? ($wmax-$ls)/1000*$this->FontSize/($ns-1) : 0;
						$this->_out(sprintf('%.3f Tw',$this->ws*$this->k));
					}
					return array(
						'cadena'=>substr($s,$j,$sep-$j),
						'longitud'=>$sep-$j
					);				
					//$this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);				
				}
				$sep=-1;
				$j=$i;
				$l=0;
				$ns=0;
				$nl++;
				if($border && $nl==2)
					$b=$b2;
			}
			else
				$i++;
		}
		//Last chunk
		if($this->ws>0)
		{
			$this->ws=0;
			$this->_out('0 Tw');
		}
		if($border && strpos($border,'B')!==false)
			$b.='B';
			return array(
				'cadena'=>substr($s,$j,$i-$j),
				'longitud'=>$i-$j
			);
			//$this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);

	}
	public function moneda_format($valor,$decimales=2){
		if ($valor<0){
			$valor=$valor*-1;
			$signo="-$";
		}else{
			$signo="$";
		}
		return $signo.number_format($valor,$decimales);
		
	} 
	
}

?>