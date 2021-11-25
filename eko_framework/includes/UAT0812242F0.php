<?php
require_once(dirname(__FILE__).'/mfpdf.php');

class UAT0812242F0 extends mFPDF {
	
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		parent::__construct($orientation,$unit,$format,$params);
		
		$this->x1Emisor=52;
		$this->x1Sucursal=$this->x1Emisor+76;
	}
	//Imprime las imagenes
	function imprimeLogo(){
		$this->Image("images/logos/upct_logo.jpg",10,12,40);
		$this->SetXY(174,15);
		$this->Image("images/logos/Elastix-Certified.jpg",174,12,15);
		$this->Image("images/logos/BannerResellerCFDI_200x200.jpg",190,12,14);
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
	/*public function Header() {
		//---------------------------------------------------------------------------------------------------
		//												 -- Emisor --
		//---------------------------------------------------------------------------------------------------
		IF (!empty($this->NomComFac)){
			$this->yEncabezadoDeTabla=61;	
		}else{
			$this->yEncabezadoDeTabla=61;
		}
		if ($this->TipDoc=='NOTA DE CREDITO'){
			$this->TipDoc=UTF8_DECODE('NOTA DE CRÉDITO');
		}
		$border  = 0;
		$columna = $this->x1Emisor;		
		
		$this->imprimeLogo();
		
		$this->SetTextColor(0,0,0);
		
		$this->SetFont('Arial','B',12);
		$this->SetXY($columna,15); $this->Cell(160, 4, $this->em_nombre, $border, 1);	//ENCABEZADO CON EL NOMBRE DE LA EMPRESA
		$this->SetX($columna);     $this->Cell(80, 1, "", $border, 1);
		$yEmisor=$this->GetY();
		//----------------------------------------------------
		//		EMPRESA
		//----------------------------------------------------
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

		//---------------------------
		//			SUCURSAL
		//---------------------------
		$columna2=$this->x1Sucursal;
		$this->SetXY($columna2,$yEmisor);
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
		
		
		# -- Receptor
		$this->SetY(34);
		$this->SetFont('Arial','B',7);
		$this->Cell(80, 3, "DOMICILIO FISCAL DEL CLIENTE", $border, 1);
		$this->SetFont('Arial','B',9);
		$this->Cell(128, 4, $this->cli_nombre, $border, 1);
		$this->SetFont('Arial','',8);
		IF (empty($this->NomComFac)){
			$this->NomComFac='';
		}
		
		$this->Cell(128, 4,mb_strtoupper(truncate($this->NomComFac,68) ), $border, 1);	
		
		
		
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
		
		//$this->SetFont('Arial','',8);
		//$this->Cell(128, 4, $this->cli_calle." ".$this->cli_no_ext." ".$this->cli_no_int." ".$this->cli_colonia, $border, 1);
		//$cod_pos = ($this->cli_cp) ? "  C.P.: ".$this->cli_cp : "";
		//$this->Cell(128, 4, $this->getLocalidadCliente($this->cli_localidad, $this->cli_ciudad, $this->cli_estado, $this->cli_pais).$cod_pos, $border, 1);
		
		$rfc = ($this->cli_rfc) ? "R.F.C.: ".$this->cli_rfc : "";
		$this->Cell(128, 4, $rfc, $border, 1);
		
		$this->SetFont('Arial','B',8);
		$this->Cell(25.5, 4, utf8_decode('RÉGIMEN FISCAL:'), 0);
		$this->SetFont('Arial','',8);
		$this->Cell(92, 4, utf8_decode($this->regimenes), 0);
		$this->setX(167);
		$this->SetFont('Arial','B',7);   $this->Cell(38, 4, '"EFECTOS FISCALES AL PAGO"', 0, 0, 'R');
		# -- Datos de la factura
		$columna = 127;
		$ver_serie = (empty($this->fac_serie)) ? '' : $this->fac_serie."-";
		$this->SetXY($columna,34);
		if ($this->tipoDeFactura=='cfdi'){		
			$this->SetFont('Arial','B',7);  $this->Cell(27, 4, "FOLIO FISCAL:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(51, 4, $this->fac_uuid, $border, 1, 'R');
			$this->SetX($columna);
			$this->SetFont('Arial','B',7);  $this->Cell(40, 4, "CERTIFICADO SAT:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(38, 4, $this->fac_certif_SAT, $border, 1, 'R');
			$this->SetX($columna);
			$this->SetFont('Arial','B',7);  $this->Cell(40, 4, "FECHA CERTIFICACION:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(38, 4, $this->getFechaFormato($this->fac_fecha_certif), $border, 1, 'R');
		}else{
			$this->Cell(40, 4,'', $border, 1, 'R');
			 $this->Cell(40, 4,'', $border, 1, 'R');
			$this->Cell(40, 4,'', $border, 1, 'R');
		}
		$y=$this->GetY();		 
				

		if ($this->tipoDeFactura=='cfd'){
			if (!empty($this->NomComFac)){
				$this->SetY($y-7);	
			}else{
				$this->SetY($y-10);	
			}
						
			//----------------------------------			
			$this->SetFont('Arial','B',10);
			$this->SetX($columna);		
			$this->Cell(40, 3, "", $border, 0, 'L');
			
			$this->SetTextColor(85, 85, 85);
			$this->Cell(38, 6,$this->TipDoc, $border, 1, 'R');
			$this->SetTextColor(0,0,0);
			//----------------------------------
			$this->SetFont('Arial','B',10); 	
		}

		$this->SetX($columna);
		$this->Cell(40, 4, "SERIE Y FOLIO:", $border, 0, 'L');
		if ($this->tipoDeFactura=='cfd'){
			$this->SetFont('Arial','',15); 	
		}
		$this->Cell(38, 4, $ver_serie.$this->fac_folio, $border, 1, 'R');
		$this->SetX($columna);
		if ($this->tipoDeFactura=='cfd'){
			$this->SetFont('Arial','B',10); 	
		}
		$this->Cell(40, 4, utf8_decode("FECHA ELABORACION:"), $border, 0, 'L');
		if ($this->tipoDeFactura=='cfd'){
			$this->SetFont('Arial','',7); 	
		}
		 $this->Cell(38, 4, $this->getFechaFormato($this->fac_fecha), $border, 1, 'R');
		
		$this->SetX($columna);
		$this->SetFont('Arial','B',7);
		$this->Cell(34, 4, utf8_decode('LUGAR DE EXPEDICIÓN:'), 0);
		$this->SetFont('Arial','',8);
		$this->Cell(44, 4, $this->LugarExpedicion, 0, 1,'R');
		
		$this->imprimiendoConceptos==true;
		$this->imprimeEncabezadoDeTabla();
		
	}*/
		
}

?>