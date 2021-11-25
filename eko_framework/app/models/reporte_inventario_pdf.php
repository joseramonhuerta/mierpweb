<?php
require ('eko_framework/includes/pdf_js.php');
require ('eko_framework/includes/funciones.php');

class ReporteInventarioPDF extends PDF_JavaScript{
	var $cerrando=false;
	var $colorHeader;
	var $colorLetrasHeader;
	var $imprimirHeader=true;
	var $imprimirTotales=false;
	var $nueva=true;
	var $decimales=2;
	var $aceptarSalto=true;
	var $imprimirSubtotalesDesdeFooter=true;
	function ReporteInventarioPDF($orientation='L',$unit='mm',$format='Letter',$datos,$formatos){
		if (empty($this->yEncabezadoDeTabla))	$this->yEncabezadoDeTabla=55;
	 	$this->datos=$datos;
	 	$this->formatos=$formatos;
	 	$this->wCliente=65;		
		 							//$decimales=$this->formatos['decimales'];	 	
	 	$this->acumuladas=0;	//Sumatoria de facturas NO Canceladas	 	
		parent::__construct('L','mm',$format);
	 	$this->AliasNbPages(); 
		// $this->SetAutoPageBreak(true, 40);
		$this->colorHeader;
		$this->colorLetrasHeader;
		$this->SetFillColor(0, 0, 0);
		//$this->SetTextColor(255, 255, 255);
	}
	
	function AcceptPageBreak(){
		if ($this->aceptarSalto==true){
			return true;
			parent::AcceptPageBreak();	
		}		
	}
	function AddPage($orientation=''){
		$this->nueva=true;
		
		parent::AddPage($orientation);
	
		$this->subtotal[$numPag]=0;	//Subtotal Por Página
		$this->subtotalImpuestos[$numPag]=0;	//Subtotal Por Página
	}
	function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=0,$link=''){
		parent::Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
		$this->nueva=false;
	}
	function Close(){
		$this->cerrando=true;
		parent::Close();		
	}
	public function Header() {
		//Cell(float w [, float h [, string txt [, mixed border [, int ln [, string align [, int fill [, mixed link]]]]]]])
		$wCliente=65;
		$datos=$this->datos['data'];
		$filtros=$this->datos['filtros'];
		$empresa=$datos['inventario']['nombre_fiscal'];
		$sucursal=$datos['inventario']['nombre_sucursal'];
		$nombre_almacen=$datos['inventario']['nombre_almacen'];		
		
		$serie_inventario=$datos['inventario']['serie_inventario'];
		$folio_inventario=$datos['inventario']['folio_inventario'];
		$fecha_inventario=$datos['inventario']['fecha_inventario'];
		$fecha_aplica=$datos['inventario']['fecha_aplica'];
		$nombre_inventario="INVENTARIO FISICO";
		$concepto_inventario=$datos['inventario']['concepto_inventario'];
		
		$x3 = 65;
		$x2 = 2;
		$this->SetTextColor(0, 0, 0);
		$font="Arial";		
		$this->SetX($x2);	
		$this->SetFont($font,'B',10);
		
				
		$this->Cell($x3,5,utf8_decode("Reporte Inventario Fisico"),0,0,'L');	
		
		$this->Ln();
		$this->SetX($x2);
		$this->Cell($x3,5,utf8_decode($nombre_inventario),0,0,'L');		
		
		$this->Ln();
		$this->SetX($x2);
		
		$hCell=4;
		$border=0;
		$filtroEstado='';
		
		$this->SetFont($font,'B',8);
		$this->Cell($x3,$hCell,mb_strtoupper(UTF8_decode($empresa)),$border,0);	#	Valor
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',8);
		$this->Cell(15,$hCell,"Sucursal:",$border,0,'L');	#	Valor
		$this->SetFont($font,'',9);
		$this->Cell(45,$hCell,mb_strtoupper(UTF8_decode($sucursal)),$border,0,'L');	#	Valor
					
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',8);
		$this->Cell(10,$hCell,"Serie: ",$border,0);							#	Label
		$this->SetFont($font,'',8);
		$this->Cell(50,$hCell,mb_strtoupper(UTF8_decode($serie_inventario)),$border,0);	#	Valor
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',9);	
		$this->Cell(10,$hCell,"Folio: ",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(50,$hCell,mb_strtoupper(UTF8_decode($folio_inventario)),$border,0);	#	Valor
		
		if($nombre_almacen != ''){
			$this->Ln();
			$this->SetX($x2);
			$this->SetFont($font,'B',8);
			$this->Cell(15,$hCell,"Almacen:",$border,0,'L');	#	Valor
			$this->SetFont($font,'',8);
			$this->Cell(45,$hCell,mb_strtoupper(UTF8_decode($nombre_almacen)),$border,0,'L');	#	Valor
		}
		
		
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',8);	
		$this->Cell(18,$hCell,"Fecha Hora: ",$border,0);							#	Label
		$this->SetFont($font,'',8);
		$this->Cell(42,$hCell,mb_strtoupper(UTF8_decode($fecha_inventario)),$border,0);	#	Valor
		
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',8);	
		$this->Cell(15,$hCell,"Concepto: ",$border,0);							#	Label
		$this->SetFont($font,'',8);
		$this->Cell(45,$hCell,mb_strtoupper(UTF8_decode($concepto_inventario)),$border,0);	#	Valor
		
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',8);	
		$this->Cell(25,$hCell,"Fecha Aplicacion: ",$border,0);							#	Label
		$this->SetFont($font,'',8);
		$this->Cell(35,$hCell,mb_strtoupper(UTF8_decode($fecha_aplica)),$border,0);	#	Valor
		
        $this->Cell(70,$hCell,'',0,0);		#			
				
        $this->ln();
		
		$fill=1;
		$border=1;
		$this->subtotal=array();
		$this->subtotalImpuestos=array();
		
		$this->imprimeHeader();
		
      
        
	}
	
	function imprimeHeader(){
		$border =0;
		if ($this->imprimirHeader==true){
			$font="Arial";
			$this->SetFont($font,'',9);
			$this->SetX(2);			
			$this->SetTextColor(0, 0, 0);
					
			$fill=0;
			$wCliente=$this->wCliente;
        	$this->SetTextColor(0, 0, 0);
        	$this->SetFillColor(0, 0, 0);
	       	$this->SetFont('Arial','B',7);
			
			$this->Cell(35,5,utf8_decode("DESCRIPCION"),$border,0,"L",$fill);	
			
			$this->Cell(10,5,"STOCK",$border,0,"C",$fill);
			$this->Cell(10,5,"CONTEO",$border,0,"C",$fill);
			
			$this->Cell(10,5,"DIF",$border,0,"C",$fill);		
			$this->Ln();
			$this->SetX(2);	
			$this->Cell(70,1,utf8_decode("------------------------------------------------------------------------------"),$border,0,"L",$fill);
			
			$this->SetXY(10,$this->yEncabezadoDeTabla + 2 );
        }
		
	}
	
	function CellFit($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $scale=false, $force=true)
    {
        //Get string width
        $str_width=$this->GetStringWidth($txt);
 
        //Calculate ratio to fit cell
        if($w==0)
            $w = $this->w-$this->rMargin-$this->x;
        $ratio = ($w-$this->cMargin*2)/$str_width;
 
        $fit = ($ratio < 1 || ($ratio > 1 && $force));
        if ($fit)
        {
            if ($scale)
            {
                //Calculate horizontal scaling
                $horiz_scale=$ratio*100.0;
                //Set horizontal scaling
                $this->_out(sprintf('BT %.2F Tz ET',$horiz_scale));
            }
            else
            {
                //Calculate character spacing in points
                $char_space=($w-$this->cMargin*2-$str_width)/max($this->MBGetStringLength($txt)-1,1)*$this->k;
                //Set character spacing
                $this->_out(sprintf('BT %.2F Tc ET',$char_space));
            }
            //Override user alignment (since text will fill up cell)
            $align='';
        }
 
        //Pass on to Cell method
        $this->Cell($w,$h,$txt,$border,$ln,$align,$fill,$link);
 
        //Reset character spacing/horizontal scaling
        if ($fit)
            $this->_out('BT '.($scale ? '100 Tz' : '0 Tc').' ET');
    }
 
    function CellFitSpace($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='')
    {
        $this->CellFit($w,$h,$txt,$border,$ln,$align,$fill,$link,false,false);
    }
 
    function MBGetStringLength($s)
    {
        if($this->CurrentFont['type']=='Type0')
        {
            $len = 0;
            $nbbytes = strlen($s);
            for ($i = 0; $i < $nbbytes; $i++)
            {
                if (ord($s[$i])<128)
                    $len++;
                else
                {
                    $len++;
                    $i++;
                }
            }
            return $len;
        }
        else
            return strlen($s);
    }
	
	public function imprimeDetalles($datos){

		$decimales=$this->decimales;
		$datos=$this->datos['data'];
		$wCliente=65;
		$this->SetTextColor(0, 0, 0);
		$border=0;
		$fill=0;
		$zebra=1;
		$hCell=4;
	
		$font="Arial";
		$this->SetFont($font,'',7);
		$this->SetTextColor(0,0,0);
		$canceladas=array();
		$this->imprimirSubtotalesDesdeFooter=true;
		
		$this->SetFillColor(229, 229, 229); //Gris tenue de cada fila
        $this->SetTextColor(3, 3, 3); //Color del texto: Negro
		$fill = false; //Para alternar el relleno
		
		foreach($datos['detalles'] as $dato){	
		
			$this->SetX(2);
			$saltoDeLinea = 0;
			#		Primer linea
			$this->aceptarSalto=true;	
			
			$this->SetFont($font,'',6);
			
			//Comienzo 
			$heightCell = 4;
			
			$widthFechaUlt = 37;
			$widthMes = 25;
			$widthSaldo = 25;
			$widthConsumo = 25;
			$widthAnio = 16;
			$saltoDeLinea = 0;
			$fill=0;			
					
			$Codigo = $dato['codigo'];
			$CodigoBarras = $dato['codigo_barras'];			
			$Descripcion = $dato['descripcion'];
			$strLength = strlen($Descripcion);
			$strLimit = 27;
			if($strLength > $strLimit){
					
					$Descripcion = substr($Descripcion, 0, $strLimit-3);
					$Descripcion.='...';
			}
			$CodigoUnidad = $dato['codigo_unidad'];
			$Stock = number_format($dato['stock'] ,$decimales ,  '.' , ',' );
			$Conteo = number_format($dato['conteo'] ,$decimales ,  '.' , ',' );
			$Diferencia = number_format($dato['diferencia'] ,$decimales ,  '.' , ',' );		
			
			$this->Cell(35,$heightCell,utf8_decode($Descripcion),$border,$saltoDeLinea,'',$fill);				
			$this->Cell(10,$heightCell,utf8_decode($Stock),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(10,$heightCell,utf8_decode($Conteo),$border,$saltoDeLinea,'R',$fill);				
			$this->Cell(10,$heightCell,utf8_decode($Diferencia),$border,$saltoDeLinea,'R',$fill);		
			
			$fill = !$fill;//Alterna el valor de la bandera
			//Comienzo End
			$this->ln();
			
			
		}
		
		$this->imprimirHeader=false;
		$this->imprimirSubtotalesDesdeFooter=false;
		$ultimo=true;
		
        $this->SetTextColor(0, 0, 0);
			
		
	}
	
	function despuesdelosdetalles(){
		// $hCell=50;
		// $PageBreakTrigger=$this->h-$this->bMargin;
		// $limite=$PageBreakTrigger-$hCell*3;
		// $y=$this->GetY();
		// if ($y>$limite){
			// $this->AddPage();
		// }
	}
	
	function jsDateToMysql($jsDate){
        $date = "04/30/1973";
        list($dia, $mes, $aÃ±o) = explode('/', $jsDate);
        @list($aÃ±o,$time) = explode(' ', $aÃ±o);
        $convertida="$aÃ±o-$mes-$dia";

        if ($time!=''){
            list($hora, $minuto, $segundo) = explode(':', $time);
            $convertida.=" $hora:$minuto:$segundo";
        }
        return $convertida;
    }
		
	function imprimirTotales($ultimo=false){
		$this->aceptarSalto=false;
			
		$this->Ln(10);
		$this->Cell(65,5,"___________________________",0,0,'C',$fill);
		$this->Ln(5);
		$this->Cell(65,5,"Firma",0,0,'C',$fill);
		$this->Ln(10);
		
		$this->aceptarSalto=false;
	}
	public function Footer() {	
		$yFooter = -11;
		$wFooter = 10;
		$this->SetY($yFooter);
		$this->SetFont('Arial','I',8);
		
		$this->Ln(10);
		$this->Cell(65,5,"___________________________",0,0,'C',$fill);
		$this->Ln(5);
		$this->Cell(65,5,"Firma",0,0,'C',$fill);
		$this->Ln(10);
	}
	
	function formatearTexto($cadena){
		$formato=$this->formatos['texto'];
		// echo 'formato: '.$formato;
		// exit;
		switch($formato){
			case 1:		//MAYUSCULAS

				return strtoupper($cadena);
				break;
			case 2:		//minusculas
				
				return strtolower($cadena);
				break;
			case 3:		//Capitalizado
				
				return  ucwords($cadena);
				break;
			default:
				throw new Exception("Formato desconocido: ".$formato);
		}

	}
}
?>
