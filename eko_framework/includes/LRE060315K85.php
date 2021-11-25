<?php
require_once(dirname(__FILE__).'/mfpdf.php');
class LRE060315K85 extends mFPDF{
	function __construct($orientation='P',$unit='mm',$format='Letter',$params=array()){
		$logo=array(
			'imagen'=>'images/logos/logo_luz_record.jpg',
			'x'=>168,
			'y'=>15
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
			$this->Image($imagen,$x,$y,35);	
		}
	}
	/*private function imprimirReceptor(){	
		$border=0;	
		$this->SetY(34);
		$this->SetFont('Arial','B',7);
		$this->Cell(80, 3, "DOMICILIO FISCAL DEL CLIENTE", $border, 1);
		$this->SetFont('Arial','B',9);
		$this->Cell(128, 5, $this->cli_nombre, $border, 1);
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
	}*/
}
?>