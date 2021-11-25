<?php
require_once(dirname(__FILE__).'/mfpdf.php');
//class ASC060531PF9 extends mFPDF{
class ASC060531PF9 extends mFPDF{

	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		$this->exp_calle="SIERRA RUMOROSA ";
		$this->exp_no_ext="321-1ER PISO";
		$this->exp_no_int="";
		$this->exp_colonia=UTF8_DECODE("LOMAS DE MAZATLÁN");
		$this->exp_cp="82180";
		$this->exp_localidad="";
		$this->exp_ciudad=UTF8_DECODE("MAZATLÁN");
		$this->exp_estado="SINALOA";
		$this->exp_pais=UTF8_DECODE("MÉXICO");
		$this->telSuc="";
		$this->faxSuc="";
		parent::__construct($orientation,$unit,$format,$params);
	}
	
	function b4Header(){
		$this->expedicion=true;		//PARA QUE SIEMPRE IMPRIMA LA SUCURSAL
		return true;
	}
	
	//-----------------------------------------------
	//				IMPRIME DATOS DE LA SUCURSAL (override solo para mostrar SUCURSAL: en lugar de EXPEDIDO EN: )
	//-----------------------------------------------
	function imprimirSucursal(){
		$border=0;	
		$columna2=$this->x1Sucursal;
		$this->SetXY($columna2,$this->yEmisor);
		$this->SetFont('Arial','B',6);
		$this->Cell(80, 3, ($this->expedicion) ? "SUCURSAL:" : "", $border, 1);

		$this->SetFont('Arial','',6);
		$this->SetX($columna2);
		$this->Cell(80, 3, $this->exp_calle." ".$this->exp_no_ext." ".$this->exp_no_int." ".$this->exp_colonia, $border, 1);
		$this->SetX($columna2);
		
		$cod_pos = ($this->exp_cp) ? "C.P.: ".$this->exp_cp : "";
		$localidad=$this->getLocalidad($this->exp_localidad,$this->exp_ciudad, $this->exp_estado, $this->exp_pais);
		
		if($this->telSuc || $this->faxSuc){	//Si existe el telefono, el codigo postal se imprime jnto con la direccion
			$localidad=$localidad." ".$cod_pos;
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
		
}
?>