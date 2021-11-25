<?php
require_once(dirname(__FILE__).'/mfpdf.php');

class CZA090810U11 extends mFPDF {
	
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		parent::__construct($orientation,$unit,$format,$params);
		
		$this->x1Emisor=52;
		$this->x1Sucursal=$this->x1Emisor+76;
	}
	//Imprime las imagenes
	function imprimeLogo(){
		$this->Image("images/logos/logo-san-martin.jpg",11,16,39);
		$this->SetXY(174,15);		
	}
	
}

?>