<?php
require ('eko_framework/includes/pdf_js.php');
require ('eko_framework/includes/funciones.php');
//ESTA CLASE DEBERIA TENER PUROS CICLOS DE IMPRESION,
// DEBERIA PROCESAR INFORMACION SOLAMENTE PARA EL ASPECTO VISUAL
class ReporteMovimientoAlmacenPDF extends PDF_JavaScript{
	var $cerrando=false;
	var $colorHeader;
	var $colorLetrasHeader;
	var $imprimirHeader=true;
	var $imprimirTotales=false;
	var $nueva=true;
	var $decimales=2;
	var $aceptarSalto=true;
	var $imprimirSubtotalesDesdeFooter=true;
	function ReporteMovimientoAlmacenPDF($orientation='L',$unit='mm',$format='Letter',$datos,$formatos){
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
	//	$this->tieneDetalles=false;
		// $numPag=$this->PageNo();
		// $this->subtotal[$numPag]=0;	//Subtotal Por Página
		// $this->subtotalImpuestos[$numPag]=0;	//Subtotal Por Página
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
		$empresa=$datos['movimiento']['nombre_fiscal'];
		$sucursal=$datos['movimiento']['nombre_sucursal'];
		$almacenOrigen=$datos['movimiento']['almacen_origen'];
		$almacenDestino=$datos['movimiento']['almacen_destino'];
		$agente=$datos['movimiento']['nombre_agente'];
		
		$serie_movimiento=$datos['movimiento']['serie_movimiento'];
		$folio_movimiento=$datos['movimiento']['folio_movimiento'];
		$fecha_movimiento=$datos['movimiento']['fecha_movimiento'];
		$nombre_movimiento=$datos['movimiento']['nombre_movimiento'];
		$concepto_movimiento=$datos['movimiento']['concepto_movimiento'];
		//----------------------------------------------------
		//			Titulo del reporte
		//----------------------------------------------------
		$x3 = 65;
		$x2 = 2;
		$this->SetTextColor(0, 0, 0);
		$font="Arial";		
		$this->SetX($x2);	
		$this->SetFont($font,'B',10);
		
		// $this->SetX(170);
		// $this->Cell(41,$hCell,"$fechaInicio A $fechaFin",$border,1);	//Se muestra el rango de fechas
		// $this->SetFont($font,'',9);
		//$this->SetX($x1);	
		
		// $this->Cell(200,10,utf8_decode("Reporte de saldos de Folios ".$day.' '.$month.' '.$year),0,2,'L');			
		$this->Cell($x3,5,utf8_decode("Reporte Movimiento de Almacen"),0,0,'L');	
		// $this->SetX(108);
		// $this->SetX($x1);	
		$this->Ln();
		$this->SetX($x2);
		$this->Cell($x3,5,utf8_decode($nombre_movimiento),0,0,'L');		
		// $this->SetX($x1);	
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
		$this->Cell(50,$hCell,mb_strtoupper(UTF8_decode($serie_movimiento)),$border,0);	#	Valor
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',9);	
		$this->Cell(10,$hCell,"Folio: ",$border,0);							#	Label
		$this->SetFont($font,'',9);
		$this->Cell(50,$hCell,mb_strtoupper(UTF8_decode($folio_movimiento)),$border,0);	#	Valor
		
		if($almacenOrigen != ''){
			$this->Ln();
			$this->SetX($x2);
			$this->SetFont($font,'B',8);
			$this->Cell(25,$hCell,"Almacen Origen:",$border,0,'L');	#	Valor
			$this->SetFont($font,'',8);
			$this->Cell(35,$hCell,mb_strtoupper(UTF8_decode($almacenOrigen)),$border,0,'L');	#	Valor
		}
		
		if($almacenDestino != ''){
			$this->Ln();
			$this->SetX($x2);
			$this->SetFont($font,'B',8);
			$this->Cell(25,$hCell,"Almacen Destino:",$border,0,'L');	#	Valor
			$this->SetFont($font,'',8);
			$this->Cell(35,$hCell,mb_strtoupper(UTF8_decode($almacenDestino)),$border,0,'L');	#	Valor
		}
		if($agente != ''){
			$this->Ln();
			$this->SetX($x2);
			$this->SetFont($font,'B',8);
			$this->Cell(15,$hCell,"Vendedor:",$border,0,'L');	#	Valor
			$this->SetFont($font,'',8);
			$this->Cell(45,$hCell,mb_strtoupper(UTF8_decode($agente)),$border,0,'L');	#	Valor			
		}
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',8);	
		$this->Cell(18,$hCell,"Fecha Hora: ",$border,0);							#	Label
		$this->SetFont($font,'',8);
		$this->Cell(42,$hCell,mb_strtoupper(UTF8_decode($fecha_movimiento)),$border,0);	#	Valor
		
		$this->Ln();
		$this->SetX($x2);
		$this->SetFont($font,'B',8);	
		$this->Cell(15,$hCell,"Concepto: ",$border,0);							#	Label
		$this->SetFont($font,'',8);
		$this->Cell(45,$hCell,mb_strtoupper(UTF8_decode($concepto_movimiento)),$border,0);	#	Valor
		
		
        $this->Cell(70,$hCell,'',0,0);		#			
		//-----------------------------------emprezando con el rango de fechas---------------------------------------//	
		// $this->SetX(170);		
		
		
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
			$this->SetX(2);			
			$this->SetTextColor(0, 0, 0);
			// $this->Cell(41,2,$this->tipoComprobanteTitulo,$border,1,'L');
			// $this->ln();			
			$fill=0;
			$wCliente=$this->wCliente;
        	$this->SetTextColor(0, 0, 0);
        	$this->SetFillColor(0, 0, 0);
	       	$this->SetFont('Arial','B',7);
			// $this->Cell(12,5,"CLAVE",$border,0,'',$fill);		
			// $this->Cell(22,5,utf8_decode("COD. BARRAS"),$border,0,"C",$fill);		//------Header 
			$this->Cell(38,5,utf8_decode("DESCRIPCION"),$border,0,"L",$fill);		//------Header
			// $this->Cell(7,5,"U.M.",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(7,5,"CANT",$border,0,"C",$fill);//------Header Impuestos
			$this->Cell(10,5,"COSTO",$border,0,"C",$fill);//------Header Impuestos
			// $this->Cell(18,5,"IMPORTE",$border,0,"R",$fill);//------Header Impuestos
			// $this->Cell(15,5,"DESC.",$border,0,"R",$fill);//------Header Impuestos
			// $this->Cell(18,5,"SUBTOTAL",$border,0,"R",$fill);//------Header Impuestos
			// $this->Cell(18,5,"IMPUESTO",$border,0,"R",$fill);		//------Header TOTAL	
			$this->Cell(12,5,"TOTAL",$border,0,"C",$fill);		//------Header TOTAL	
			$this->Ln();
			$this->SetX(2);	
			$this->Cell(70,1,utf8_decode("------------------------------------------------------------------------------"),$border,0,"L",$fill);
			// $this->Ln();
			// $this->Cell(60,5,utf8_decode("------------------------"),$border,0,"L",$fill);
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
		//$this->SetY(45);
		// $numPag=$this->PageNo();
		// $this->subtotal[$numPag]=0;	//Subtotal Por Pagina
		// $this->subtotalImpuestos[$numPag]=0;	//Subtotal Por Pagina
		// $this->impuestosAcumulados=0;
		$font="Arial";
		$this->SetFont($font,'',7);
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
			$Cantidad = number_format($dato['cantidad'] ,$decimales ,  '.' , ',' );
			$Costo = number_format($dato['costo'] ,$decimales ,  '.' , ',' );
			$Importe = number_format($dato['importe'] ,$decimales ,  '.' , ',' );
			$Descuento = number_format($dato['descuento'] ,$decimales ,  '.' , ',' );
			$Subtotal = number_format($dato['subtotal'] ,$decimales ,  '.' , ',' );
			$Impuestos = number_format($dato['impuestos'] ,$decimales ,  '.' , ',' );
			$Total = number_format($dato['total'] ,$decimales ,  '.' , ',' );
			
			// $this->Cell(12,$heightCell,utf8_decode($Codigo),$border,$saltoDeLinea,'',$fill);	
			// $this->Cell(22,$heightCell,utf8_decode($CodigoBarras),$border,$saltoDeLinea,'',$fill);
			$this->Cell(38,$heightCell,utf8_decode($Descripcion),$border,$saltoDeLinea,'',$fill);	
			// $this->Cell(7,$heightCell,utf8_decode($CodigoUnidad),$border,$saltoDeLinea,'',$fill);	
			$this->Cell(5,$heightCell,utf8_decode($Cantidad),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(10,$heightCell,utf8_decode($Costo),$border,$saltoDeLinea,'R',$fill);	
			// $this->Cell(18,$heightCell,utf8_decode($Importe),$border,$saltoDeLinea,'R',$fill);	
			// $this->Cell(15,$heightCell,utf8_decode($Descuento),$border,$saltoDeLinea,'R',$fill);	
			// $this->Cell(18,$heightCell,utf8_decode($Subtotal),$border,$saltoDeLinea,'R',$fill);	
			// $this->Cell(18,$heightCell,utf8_decode($Impuestos),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(12,$heightCell,utf8_decode($Total),$border,$saltoDeLinea,'R',$fill);		
			// $this->Cell(31,$heightCell,utf8_decode($datos[$i]['cantidad']),$border,$saltoDeLinea,"L",$fill);		//------Header 
			// $this->Cell($widthFechaUlt,$heightCell,utf8_decode($datos[$i]['FechaLastRegistro']),$border,$saltoDeLinea,"L",$fill);		//------Header
			// $this->Cell($widthMes,$heightCell,utf8_decode($mes),$border,$saltoDeLinea,"L",$fill);		//------Header
			// $this->Cell($widthAnio,$heightCell,$datos[$i]['Anio'],$border,$saltoDeLinea,"R",$fill);//------Header Impuestos
			// $this->Cell(25,$heightCell,$datos[$i]['Saldo'],$border,0,"R",$fill);		//------Header TOTAL	
			// $this->Cell($widthConsumo,$heightCell,$datos[$i]['ConsumoCalculado'],$border,$saltoDeLinea,"R",$fill);//------Header Impuestos
			// $this->Cell($widthSaldo,$heightCell,$datos[$i]['SaldoCalculado'],$border,1,"R",$fill);//------Header Impuestos
			$fill = !$fill;//Alterna el valor de la bandera
			//Comienzo End
			$this->ln();
			
			
		}
		// $this->ln(2);
		$this->imprimirHeader=false;
		$this->imprimirSubtotalesDesdeFooter=false;
		$ultimo=true;
		// $this->imprimirTotales();
        $this->SetTextColor(0, 0, 0);
			
		// $hCell=5;
		// $PageBreakTrigger=$this->h-$this->bMargin;
		// $this->despuesdelosdetalles();
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
		// $i=$this->PageNo();
		
		$datos=$this->datos['data'];
		
		$importes = number_format ($datos['movimiento']['importe'] ,$decimales ,  '.' , ',' );
		$descuento = number_format ($datos['movimiento']['descuento'] ,$decimales ,  '.' , ',' );
		$subtotal = number_format ($datos['movimiento']['subtotal'] ,$decimales ,  '.' , ',' );
		$impuestos = number_format ($datos['movimiento']['impuestos'] ,$decimales ,  '.' , ',' );
		$total = number_format ($datos['movimiento']['total'] ,$decimales ,  '.' , ',' );
		
		// $this->SetY(-$this->limite_det);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		// $y = 240;
		// $this->SetY($y);			
		
		$ancho = 70;
		$border = 0;
		$columna = 37;
		$alto   = 3.5;
		$this->SetFont('Arial','',7);
		// $this->SetXY($columna,$y);
		// $this->Ln();
		$this->SetX($columna);	
		
		$this->Cell(15, $alto, "IMPORTE:", $border, 0, 'R');
		$this->Cell(15, $alto,$importes, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->Cell(15, $alto, "DESCUENTOS:", $border, 0, 'R');
		$this->Cell(15, $alto, $descuento, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->Cell(15, $alto, "SUBTOTAL:", $border, 0, 'R');
		$this->Cell(15, $alto, $subtotal, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->Cell(15, $alto, "IMPUESTOS:", $border, 0, 'R');
		$this->Cell(15, $alto, $impuestos, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->Cell(15, $alto, "TOTAL:", $border, 0, 'R');
		$this->Cell(15, $alto, $total, $border, 1, 'R');
		
		$this->Ln(10);
		$this->Cell(65,5,"___________________________",0,0,'C',$fill);
		$this->Ln(5);
		$this->Cell(65,5,"Firma",0,0,'C',$fill);
		$this->Ln(10);
		
		$this->aceptarSalto=false;
	}
	public function Footer() {
		
		// if ($this->imprimirSubtotalesDesdeFooter==true){
			$this->imprimirTotales();
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
