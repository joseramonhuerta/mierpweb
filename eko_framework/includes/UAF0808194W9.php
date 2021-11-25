<?php
require_once(dirname(__FILE__).'/mfpdf.php');

class UAF0808194W9 extends mFPDF {
	
	//Imprime las imagenes
	function imprimeLogo(){
		$this->x1Emisor=45;
		$this->x1Sucursal=$this->x1Emisor+76;
		$this->Image("images/logos/financial_logo.png",11,14,30);
		$this->SetXY(174,15);
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
		$this->imprimeLogo();
		$border  = 0;
		$columna = $this->x1Emisor;		
		
		
		
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
		
		if ($this->tipoDeFactura=='cfdi'){		
			$this->SetXY($columna,33);
			$this->SetFont('Arial','B',7);  $this->Cell(27, 4, "FOLIO FISCAL:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(51, 4, $this->fac_uuid, $border, 1, 'R');
			$this->SetX($columna);
			$this->SetFont('Arial','B',7);  $this->Cell(40, 4, "CERTIFICADO SAT:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(38, 4, $this->fac_certif_SAT, $border, 1, 'R');
			$this->SetX($columna);
			$this->SetFont('Arial','B',7);  $this->Cell(40, 4, "FECHA CERTIFICACION:", $border, 0, 'L');
			$this->SetFont('Arial','',7);   $this->Cell(38, 4, $this->getFechaFormato($this->fac_fecha_certif), $border, 1, 'R');
		}else{
			$this->SetXY($columna,34);
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
		if ($this->tipoDeFactura=='cfdi'){
			$y=$this->GetY();
			$this->SetY($y-1);
		}
		//$this->SetX($columna+40);
		//$this->SetFont('Arial','B',7);   $this->Cell(38, 4, '"EFECTOS FISCALES AL PAGO"', $border, 1, 'R');		
		
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