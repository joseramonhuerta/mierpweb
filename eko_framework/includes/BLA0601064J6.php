<?php
require_once(dirname(__FILE__).'/mfpdf.php');

class BLA0601064J6 extends mFPDF {
	// TotImpTras
	function imprimeLogo(){

		$imagen='images/logos/BLA0601064J6.jpg';
		$this->Image($imagen,162,15,40);			
	}
}

?>