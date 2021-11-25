<?php
require_once(dirname(__FILE__).'/mfpdf.php');
class AAM991229GB2 extends mFPDF{
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		$logo=array(
			'imagen'=>'img/aam.jpg',
			'x'=>183,
			'y'=>13
		);
		/*if(isset($params['logo'])){
			$this->logo=$params['logo'];
		}*/
		$params['logo']=$logo;
		parent::__construct($orientation,$unit,$format,$params);
	}

}
?>