<?php
require_once(dirname(__FILE__).'/mfpdf.php');
class AEHJ7811083SA extends mFPDF{
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){		
		$this->anchoCellRegimen=120;
		parent::__construct($orientation,$unit,$format,$params);
		
	}
	function afterHeader(){
		$x=$this->getX();
		$y=$this->getY();
				
		$this->setXY(167, 34);
		$this->SetFont('Arial','B',10);
		$this->Cell(38, 4, $this->TipDoc, 0, 0,'R');
		$this->setX($x);
		$this->setY($y);
		$this->imprimeEncabezadoDeTabla();
	}

}
?>