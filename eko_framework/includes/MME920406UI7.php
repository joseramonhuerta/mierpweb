<?php
require_once(dirname(__FILE__).'/mfpdf.php');

class MME920406UI7 extends mFPDF {
	// TotImpTras
	function imprimeLogo(){

		$imagen='images/logos/MME920406UI7.jpg';
		$this->Image($imagen,162,15,40);			
	}
}

?>