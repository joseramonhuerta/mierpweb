<?php
require_once(dirname(__FILE__).'/mfpdf.php');


//class MAZ060628UR6 extends mFPDF{
class MAZ060628UR6 extends mFPDF{
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){		
		parent::__construct($orientation,$unit,$format,$params);
		$this->imprimiendoConceptos=true;	//PARA QUE IMPRIMA EL RECUADRO  CON EL ENCABEZADO PARA LOS CONCEPTOS
		$this->x1Emisor=52;
		$this->x1Sucursal=$this->x1Emisor+76;
	}
	
	function imprimeLogo(){
		$this->Image("images/logos/MazBasculas.png", 10, 13, 40);
	}
	
	function b4Footer(){
		$this->setX(167);
		$this->SetFont('Arial','B',7);   $this->Cell(38, 4, '"EFECTOS FISCALES AL PAGO"', 0, 1, 'R');
	}
	function b4Header(){
		//$this->expedicion=true;		//PARA QUE SIEMPRE IMPRIMA LA SUCURSAL
		return true;
	}
	
	
}
?>