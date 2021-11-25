<?php
require_once(dirname(__FILE__).'/mfpdf.php');

class IBR100823VE8 extends mFPDF {
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
			$this->SetFont('Arial','',7);
			$this->Cell(44, $altoCell, $this->LugarExpedicion, 0, 1,'R');						
		}else{
			$this->Cell(44, $altoCell, '', 0, 1,'R');						
		}
		
		
	}
}

?>