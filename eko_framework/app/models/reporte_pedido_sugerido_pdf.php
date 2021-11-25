<?php
require ('eko_framework/includes/pdf_js.php');
require ('eko_framework/includes/funciones.php');
//ESTA CLASE DEBERIA TENER PUROS CICLOS DE IMPRESION,
// DEBERIA PROCESAR INFORMACION SOLAMENTE PARA EL ASPECTO VISUAL
class ReportePedidoSugeridoPDF extends PDF_JavaScript{
	var $cerrando=false;
	var $colorHeader;
	var $colorLetrasHeader;
	var $imprimirHeader=true;
	var $imprimirTotales=false;
	var $nueva=true;
	var $decimales=2;
	var $totalreporte=0;
	var $aceptarSalto=true;
	var $imprimirSubtotalesDesdeFooter=true;
	function ReportePedidoSugeridoPDF($orientation='L',$unit='mm',$format='Letter',$datos,$formatos){
		if (empty($this->yEncabezadoDeTabla))	$this->yEncabezadoDeTabla=19;
	 	$this->datos=$datos;
	 	$this->formatos=$formatos;
	 	$this->wCliente=45;		
		 							//$decimales=$this->formatos['decimales'];	 	
	 	$this->acumuladas=0;	//Sumatoria de facturas NO Canceladas	 	
		parent::__construct('L','mm',$format);
	 	$this->AliasNbPages(); 
		// $this->SetAutoPageBreak(true, 10);
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
	//	$this->tieneDetalles=false;
		$numPag=$this->PageNo();
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
		
		
		$fill=1;
		$border=1;
		$this->subtotal=array();
		$this->subtotalImpuestos=array();
		
		$this->imprimeHeader();
		
      
        
	}
	
	function imprimeHeader(){
		$border =0;
		if ($this->imprimirHeader==true){
			$wCliente=65;
			$datos=$this->datos['data'];
			$filtros=$this->datos['filtros'];
			
			//$almacenOrigen=$datos['existencia']['nombre_almacen'];
				
			//----------------------------------------------------
			//			Titulo del reporte
			//----------------------------------------------------
			$this->SetTextColor(0, 0, 0);
			$font="Arial";	
			$x2 = 2;	
			$x3 = 65;
			$this->SetXY(1,1);
			$this->SetFont($font,'B',11);
			$this->SetX($x2);
						
			$this->Cell($x3,10,utf8_decode("Reporte Pedido Sugerido"),0,0,'L');	
			$this->SetX($x2);
			
			//-------------------------------------------------
			//	Se muestran los filtros usados para generar el reporte
			//-------------------------------------------------
			$hCell=4;//		Alto de la celda
			$border=0;
			$filtroEstado='';
			
			//-----------------------Ahora a imprimir los datos----------------------
			
			$this->SetFont($font,'',9);		
			
			$this->SetFont($font,'',9);	
								
			$this->Ln();
			
			$this->SetX($x2);
			//$this->SetFont($font,'B',9);
			//$this->Cell(20,$hCell,"Sucursal:",$border,0,'L');	#	Valor
			//$this->SetFont($font,'',9);
			//$this->Cell(40,$hCell,mb_strtoupper(UTF8_decode($almacenOrigen)),$border,0,'R');	#	Valor
				
			$this->Cell($x3,$hCell,'',0,0);		#			
			//-----------------------------------emprezando con el rango de fechas---------------------------------------//	
			$this->SetX($x2);		
			
			
			$this->ln();
			//IMPRIMIR EGRESOS O INGRESOS
			$font="Arial";
			$this->SetFont($font,'',9);
			$this->SetX(2);			
			$this->SetTextColor(0, 0, 0);
			$fill=1;
			$wCliente=$this->wCliente;
        	$this->SetTextColor(255, 255, 255);
        	$this->SetFillColor(0, 0, 0);
	       	$this->SetFont('Arial','',8);
			$this->Cell(12,5,"CLAVE",$border,0,'',$fill);			
			$this->Cell(38,5,utf8_decode("DESCRIPCION"),$border,0,"C",$fill);		//------Header
			$this->Cell(15,5,"PEDIDO",$border,0,"R",$fill);//------Header Impuestos
			// $this->Cell(15,5,"PRECIO.",$border,0,"R",$fill);//------Header Impuestos
			// $this->Cell(15,5,"TOTAL.",$border,0,"R",$fill);//------Header Impuestos
						
			$this->SetXY(2,$this->yEncabezadoDeTabla + 1 );
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
 
    //Patch to also work with CJK double-byte text
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
		$wCliente=45;
		$this->SetTextColor(0, 0, 0);
		$border=0;
		$fill=0;
		$zebra=1;
		$hCell=4;
		//$this->SetY(45);
		$numPag=$this->PageNo();
		$this->imprimirHeader=false;
		$font="Arial";
		$this->SetFont($font,'',7);
		$this->SetTextColor(0,0,0);
		$canceladas=array();
		$this->imprimirSubtotalesDesdeFooter=true;
		
		$this->SetFillColor(229, 229, 229); //Gris tenue de cada fila
        $this->SetTextColor(3, 3, 3); //Color del texto: Negro
		$fill = false; //Para alternar el relleno
		$totalexistencia = 0;
		foreach($datos as $dato){	
			$saltoDeLinea = 0;
			#		Primer linea
			$this->aceptarSalto=true;	
			$this->SetX(2);
			$this->SetFont($font,'',7);
			
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
			//$Linea = $dato['nombre_linea'];
			$strLength = strlen($Descripcion);
			$strLimit = 27;
			if($strLength > $strLimit){
					
					$Descripcion = substr($Descripcion, 0, $strLimit-2);
					$Descripcion.='..';
			}		
			
			$Stock = number_format($dato['stock'] ,$decimales ,  '.' , ',' );
			$Stock_min = number_format($dato['stock_min'] ,$decimales ,  '.' , ',' );
			$Stock_max = number_format($dato['stock_max'] ,$decimales ,  '.' , ',' );
			$Pedido_sugerido = number_format($dato['pedido_sugerido'] ,$decimales ,  '.' , ',' );
			
						
			$this->Cell(12,$heightCell,utf8_decode($Codigo),$border,$saltoDeLinea,'',$fill);	
			
			$this->Cell(38,$heightCell,utf8_decode($Descripcion),$border,$saltoDeLinea,'',$fill);
			
			$this->Cell(15,$heightCell,utf8_decode($Pedido_sugerido),$border,$saltoDeLinea,'R',$fill);
			
			$this->Cell(38,$heightCell,"",$border,$saltoDeLinea,'',$fill);	
			
			
			$this->ln();
			//$this->Cell(65,$heightCell,"Min: ".utf8_decode($Stock_min),$border,$saltoDeLinea,'R',$fill);
			//$this->Cell(20,$heightCell,"Max: ".utf8_decode($Stock_max),$border,$saltoDeLinea,'R',$fill);
			$this->Cell(65,$heightCell,"Stock: ".utf8_decode($Stock),$border,$saltoDeLinea,'L',$fill);	
			
			//$this->Cell(38,$heightCell,"",$border,$saltoDeLinea,'',$fill);	
			//$this->Cell(15,$heightCell,utf8_decode(number_format($dato['precio_venta'] ,$decimales ,  '.' , ',' )),$border,$saltoDeLinea,'R',$fill);
			
			$fill = !$fill;//Alterna el valor de la bandera
		
			$this->ln();
			$totalexistencia = $totalexistencia + $Total;
			
		}
		$this->totalreporte = $totalexistencia;
		
		// $this->ln(2);
		$this->imprimirHeader=false;
		$this->imprimirSubtotalesDesdeFooter=false;
		$ultimo=true;
	     $this->SetTextColor(0, 0, 0);
		$this->despuesdelosdetalles();	
		
	}
	
	function despuesdelosdetalles(){
		
		
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
		$decimales=$this->decimales;	
		$i=$this->PageNo();
		
		$total = number_format ($this->totalreporte ,$decimales ,  '.' , ',' );
		
		$this->SetY(-$this->limite_det);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		$y = 240;
		$this->SetY($y);			
		
		$border = 0;
		$columna = 0;
		$alto   = 4.7;
		$this->SetFont('Arial','B',9);
		$this->SetXY($columna,$y);
		
		$this->SetX($columna);
		$this->Cell(28, $alto, "TOTAL:", $border, 0, 'R');
		$this->Cell(25, $alto, $total, $border, 1, 'R');
		
		
		
		$this->aceptarSalto=true;
	}
	public function Footer() {
		
		// if ($this->imprimirSubtotalesDesdeFooter==true){
			// $this->imprimirTotales();
		// }
		//
		$yFooter = -11;
		$wFooter = 10;
		$this->SetY($yFooter);
		$this->SetFont('Arial','I',8);
		// $this->Cell(0,$wFooter,UTF8_decode('PÃ¡gina')." ".$this->PageNo().'/{nb}',0,0,'C');
		
		// $this->SetY($yFooter);
		// $this->SetFont('Arial','I',8);
		// $this->Cell(0,$wFooter,$this->datos['data'][0]['FechaImpresion'],0,0,'L');		
		
		// $this->SetY($yFooter);	
		// $this->SetX(185);
		// $this->SetFont('Arial','I',8);
		// $this->Cell(0,$wFooter,"http://www.pontuel.mx",0,0,'R');
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
