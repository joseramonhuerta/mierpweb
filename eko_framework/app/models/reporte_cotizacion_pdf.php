<?php
require ('eko_framework/includes/fpdf.php');
require ('eko_framework/includes/funciones.php');
//ESTA CLASE DEBERIA TENER PUROS CICLOS DE IMPRESION,
// DEBERIA PROCESAR INFORMACION SOLAMENTE PARA EL ASPECTO VISUAL
class ReporteCotizacionPDF extends FPDF{
	var $cerrando=false;
	var $colorHeader;
	var $colorLetrasHeader;
	var $imprimirHeader=true;
	var $imprimirTotales=false;
	var $nueva=true;
	var $decimales=2;
	var $aceptarSalto=true;
	var $imprimirSubtotalesDesdeFooter=true;
	function ReporteCotizacionPDF($orientation='L',$unit='mm',$format='Letter',$datos,$formatos){
		if (empty($this->yEncabezadoDeTabla))	$this->yEncabezadoDeTabla=55;
	 	$this->datos=$datos;
	 	$this->formatos=$formatos;
	 	$this->wCliente=65;		
		 							//$decimales=$this->formatos['decimales'];	 	
	 	$this->acumuladas=0;	//Sumatoria de facturas NO Canceladas	 	
		parent::__construct('L','mm',$format);
	 	$this->AliasNbPages(); 
		$this->SetAutoPageBreak(true, 63);	
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
		$wCliente=65;
		$datos=$this->datos['data'];
		$filtros=$this->datos['filtros'];
		$empresa=$datos['empresa'];
		$sucursal=$datos['sucursal'];		
		$nombre_empresa=$empresa['nombre_fiscal'];
		$nombre_sucursal=$sucursal['nombre_sucursal'];
		$nombre_cliente=$datos['cotizacion']['nombre_cliente'];
		$serie=$datos['cotizacion']['serie'];
		$folio=$datos['cotizacion']['folio'];
		$fecha=$datos['cotizacion']['fecha'];
		$nombre_movimiento="COTIZACION";
		$concepto=$datos['cotizacion']['concepto'];
		
		//----------------------------------------------------
		//			Titulo del reporte
		//----------------------------------------------------
		$x3 = 65;
		$x2 = 10;
		$this->SetTextColor(0, 0, 0);
		$font="Arial";		
		$this->SetX($x2);	
		$this->SetFont($font,'B',14);
		
		if($empresa['logotipo_sucursal'] == 1)
			$this->Image("images/logos/".$sucursal['logotipo'],160,7,45);
		else
			$this->Image("images/logos/".$empresa['logotipo'],160,7,45);
		
		//$this->Image("images/logos/lagranbelleza_reportes.jpeg",160,7,45);

		
		$this->SetY(10,10);	
		$this->Cell(200,5,utf8_decode($nombre_movimiento),0,0,'L');	
		$this->Ln();
		$this->SetX($x2);
		//-------------------------------------------------
		//	Se muestran los filtros usados para generar el reporte
		//-------------------------------------------------
		$hCell=4;//		Alto de la celda
		$border=0;
		$filtroEstado='';
		
		//-----------------------Ahora a imprimir los datos----------------------
		
	
		#	Label
		
		$this->SetFont($font,'I',11);
		$this->Cell($x3,$hCell,mb_strtoupper(UTF8_decode($nombre_empresa.' ( '.$nombre_sucursal.' )')),$border,0);	#	Valor
		$this->Ln();
		
		$this->SetX($x2);		
					
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',9);
		$this->Cell(20,$hCell,"Serie Folio: ",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(100,$hCell,mb_strtoupper(UTF8_decode($serie.' - '.$folio)),$border,0,'L');	#	Valor
		$this->SetFont($font,'B',9);		
		$this->Cell(15,$hCell,"",$border,0,'L');	#	Valor
		$this->SetFont($font,'',9);
		$this->Cell(60,$hCell,"",$border,0,'R');	#	Valor		
				
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',9);	
		$this->Cell(20,$hCell,"Fecha Hora: ",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(100,$hCell,mb_strtoupper(UTF8_decode($fecha)),$border,0);	#	Valor		
		$this->SetFont($font,'B',9);
		$this->Cell(15,$hCell,"",$border,0,'L');	#	Valor
		$this->SetFont($font,'',9);
		$this->Cell(60,$hCell,"",$border,0,'R');	#	Valor				
		
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',9);	
		$this->Cell(20,$hCell,"Cliente: ",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(100,$hCell,mb_strtoupper(UTF8_decode($nombre_cliente)),$border,0);	#	Valor		
		$this->SetFont($font,'B',9);	
		$this->Cell(18,$hCell,"",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(57,$hCell,"",$border,0,'R');	#	Valor
		
		$this->Ln();
		$this->SetX($x2);	
		$this->SetFont($font,'B',9);	
		$this->Cell(20,$hCell,"Concepto: ",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(100,$hCell,mb_strtoupper(UTF8_decode($concepto)),$border,0);	#	Valor
		$this->SetFont($font,'B',9);	
		$this->Cell(18,$hCell,"",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(57,$hCell,"",$border,0,'R');	#	Valor
		
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
			//IMPRIMIR EGRESOS O INGRESOS
			$font="Arial";
			$this->SetFont($font,'',9);
			$this->SetX(10);			
			$this->SetTextColor(0, 0, 0);
			$fill=1;
			$wCliente=$this->wCliente;
        	$this->SetTextColor(255, 255, 255);
        	$this->SetFillColor(0, 0, 0);
	       	$this->SetFont('Arial','B',7);
			$this->Cell(10,5,"CLAVE",$border,0,'',$fill);		
			$this->Cell(22,5,utf8_decode("COD. BARRAS"),$border,0,"C",$fill);		//------Header 
			$this->Cell(50,5,utf8_decode("DESCRIPCION"),$border,0,"L",$fill);		//------Header
			$this->Cell(8,5,"U.M.",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(15,5,"CANTIDAD",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(15,5,"COSTO",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(15,5,"IMPORTE",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(15,5,"DESC.",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(15,5,"SUBTOTAL",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(15,5,"IMPUESTO",$border,0,"C",$fill);		//------Header TOTAL	
			$this->Cell(15,5,"TOTAL",$border,0,"C",$fill);		//------Header TOTAL	
			$this->Ln();
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
		$wCliente=65;
		$this->SetTextColor(0, 0, 0);
		$border=0;
		$fill=0;
		$zebra=1;
		$hCell=4;
		
		$font="Arial";
		$this->SetFont($font,'',8);
		$this->SetTextColor(0,0,0);
		$canceladas=array();
		$this->imprimirSubtotalesDesdeFooter=true;
		// echo 'sizeof: '.sizeof($datos);
		// exit;
		//meen
		$this->SetFillColor(229, 229, 229); //Gris tenue de cada fila
        $this->SetTextColor(3, 3, 3); //Color del texto: Negro
		$fill = false; //Para alternar el relleno
		// for($i=0;$i<sizeof($datos);$i++){
		foreach($datos['detalles'] as $dato){	
			// $this->Cell(15,5,utf8_decode($dato['cantidad']),0,0,'',$fill);
			$this->SetX(10);
			$saltoDeLinea = 40;
			#		Primer linea
			$this->aceptarSalto=true;	
			
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
			$strLimit = 30;
			if($strLength > $strLimit){
					
					$Descripcion = substr($Descripcion, 0, $strLimit-3);
					$Descripcion.='...';
			}
			$CodigoUnidad = $dato['codigo_unidad'];
			$Cantidad = number_format($dato['cantidad'] ,$decimales ,  '.' , ',' );
			$Costo = number_format($dato['costo'] ,$decimales ,  '.' , ',' );
			$Importe = number_format($dato['importe'] ,$decimales ,  '.' , ',' );
			$Descuento = number_format($dato['descuento'] ,$decimales ,  '.' , ',' );
			$Subtotal = number_format($dato['subtotal'] ,$decimales ,  '.' , ',' );
			$Impuestos = number_format($dato['impuestos'] ,$decimales ,  '.' , ',' );
			$Total = number_format($dato['total'] ,$decimales ,  '.' , ',' );
			
			$this->Cell(10,$heightCell,utf8_decode($Codigo),$border,$saltoDeLinea,'',$fill);	
			$this->Cell(22,$heightCell,utf8_decode($CodigoBarras),$border,$saltoDeLinea,'',$fill);
			$this->Cell(50,$heightCell,utf8_decode($Descripcion),$border,$saltoDeLinea,'',$fill);	
			$this->Cell(8,$heightCell,utf8_decode($CodigoUnidad),$border,$saltoDeLinea,'',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Cantidad),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Costo),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Importe),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Descuento),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Subtotal),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Impuestos),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Total),$border,$saltoDeLinea,'R',$fill);		
			$fill = !$fill;//Alterna el valor de la bandera
			//Comienzo End
			$this->ln();
			
			
		}
		
		$this->imprimirHeader=false;
		$this->imprimirSubtotalesDesdeFooter=true;
		$ultimo=true;
		// $this->imprimirTotales();
        $this->SetTextColor(0, 0, 0);
			
		// $hCell=5;
		// $PageBreakTrigger=$this->h-$this->bMargin;
		$this->despuesdelosdetalles();
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
		$decimales=$this->decimales;	
		$i=$this->PageNo();
		
		$datos=$this->datos['data'];
		
		$importes = number_format ($datos['cotizacion']['importe'] ,$decimales ,  '.' , ',' );
		$descuento = number_format ($datos['cotizacion']['descuento'] ,$decimales ,  '.' , ',' );
		$subtotal = number_format ($datos['cotizacion']['subtotal'] ,$decimales ,  '.' , ',' );
		$comision = number_format ($datos['cotizacion']['comision'] ,$decimales ,  '.' , ',' );
		$impuestos = number_format ($datos['cotizacion']['impuestos'] ,$decimales ,  '.' , ',' );
		$total = number_format ($datos['cotizacion']['total'] ,$decimales ,  '.' , ',' );
		
		$this->SetY(-33+3);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		// $y = 240;
		// $this->SetY($y);			
		// $this->SetY(-20);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		// $y = $this->GetY() + 3;
		// $this->SetY(240);	

		$ancho = 70;
		$border = 0;
		$columna = 175;
		$alto   = 3.5;
		
		// $this->SetXY($columna,$y);
		// $this->Ln();
		$this->SetX($columna);	
		$this->SetFont('Arial','B',9);
		$this->Cell(15, $alto, "IMPORTE:", $border, 0, 'R');
		$this->SetFont('Arial','',9);
		$this->Cell(15, $alto,$importes, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->SetFont('Arial','B',9);
		$this->Cell(15, $alto, "DESCUENTOS:", $border, 0, 'R');
		$this->SetFont('Arial','',9);
		$this->Cell(15, $alto, $descuento, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->SetFont('Arial','B',9);
		$this->Cell(15, $alto, "SUBTOTAL:", $border, 0, 'R');
		$this->SetFont('Arial','',9);
		$this->Cell(15, $alto, $subtotal, $border, 1, 'R');
		/*
		$this->SetX($columna);
		$this->SetFont('Arial','B',9);
		$this->Cell(15, $alto, "COMISION:", $border, 0, 'R');
		$this->SetFont('Arial','',9);
		$this->Cell(15, $alto, $comision, $border, 1, 'R');
		*/
		$this->SetX($columna);
		$this->SetFont('Arial','B',9);
		$this->Cell(15, $alto, "IMPUESTOS:", $border, 0, 'R');
		$this->SetFont('Arial','',9);
		$this->Cell(15, $alto, $impuestos, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->SetFont('Arial','B',9);
		$this->Cell(15, $alto, "TOTAL:", $border, 0, 'R');
		$this->SetFont('Arial','',9);
		$this->Cell(15, $alto, $total, $border, 1, 'R');

		
		$this->aceptarSalto=false;
	}
	public function Footer() {
		$this->SetY(-68);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		$y = $this->GetY() + 3;
		$this->SetY($y);
		$y=$this->GetY();
		
		
		$border = 1;
		$columna = 10;
		$alto   = 4;
		$y = $this->GetY();
		
		
		if ($this->imprimirSubtotalesDesdeFooter==true){
			$this->imprimirTotales();
		}		
		
		$yFooter = -11;
		$wFooter = 10;
		$this->SetY($yFooter);
		$this->SetFont('Arial','I',8);
		$this->Cell(0,$wFooter,UTF8_decode('Pagina')." ".$this->PageNo().'/{nb}',0,0,'C');
		
	
		
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
