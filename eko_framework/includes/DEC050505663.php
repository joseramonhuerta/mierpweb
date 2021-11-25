<?php
require_once(dirname(__FILE__).'/mfpdf.php');
class DEC050505663 extends mFPDF{
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		$logo=array(
			'imagen'=>'images/logos/DECONURBE.jpg',
			'x'=>180,
			'y'=>13
		);

		$params['logo']=$logo;
		parent::__construct($orientation,$unit,$format,$params);
	}
	function imprimeLogo(){
		if(isset($this->logo)){
			$imagen=$this->logo['imagen'];
			$x=$this->logo['x'];
			$y=$this->logo['y'];

			//$w=$this->logo['w'];
			//$h
			$this->Image($imagen,$x,$y,24);	
		}
	}

}
?>