<?php
require ('eko_framework/includes/pdf_js.php');
require ('eko_framework/includes/funciones.php');
//ESTA CLASE DEBERIA TENER PUROS CICLOS DE IMPRESION,
// DEBERIA PROCESAR INFORMACION SOLAMENTE PARA EL ASPECTO VISUAL
class ReporteVentaTicketPDF extends PDF_JavaScript{
	var $cerrando=false;
	var $colorHeader;
	var $colorLetrasHeader;
	var $imprimirHeader=true;
	var $imprimirTotales=false;
	var $nueva=true;
	var $decimales=2;
	var $aceptarSalto=true;
	var $imprimirSubtotalesDesdeFooter=true;
	function ReporteVentaTicketPDF($orientation='L',$unit='mm',$format='Letter',$datos,$formatos){
		if (empty($this->yEncabezadoDeTabla))	$this->yEncabezadoDeTabla=45;
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
	function AutoPrint($dialog=false)
	{
		//Open the print dialog or start printing immediately on the standard printer
		$param=($dialog ? 'true' : 'false');
		$script="print($param);";
		$this->IncludeJS($script);
	}

	function AutoPrintToPrinter($server, $printer, $dialog=false)
	{
		//Print on a shared printer (requires at least Acrobat 6)
		$script = "var pp = getPrintParams();";
		if($dialog)
			$script .= "pp.interactive = pp.constants.interactionLevel.full;";
		else
			$script .= "pp.interactive = pp.constants.interactionLevel.automatic;";
		$script .= "pp.printerName = '\\\\\\\\".$server."\\\\".$printer."';";
		$script .= "print(pp);";
		$this->IncludeJS($script);
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
		$nombre_sucursal=$sucursal['nombre_sucursal'];
		$nombre_fiscal = $datos['venta']['nombre_fiscal'];
		$direccion = strtoupper($empresa['calle']." #".$empresa['numext']." ".$empresa['numint']);
		$colonia = strtoupper('COL. '.$empresa['colonia']." ".$empresa['cp']);
		$localidad = strtoupper($empresa['nom_ciu'].", ".$empresa['nom_est'].", ".$empresa['nom_pai']);
		$nombre_agente = $datos['venta']['nombre_agente'];

		$direccion_sucursal = strtoupper($sucursal['calle']." #".$sucursal['numext']." ".$sucursal['numint']);
		$colonia_sucursal = strtoupper('COL. '.$sucursal['colonia']." ".$sucursal['cp']);
		$localidad_sucursal = strtoupper($sucursal['nom_ciu'].", ".$sucursal['nom_est'].", ".$sucursal['nom_pai']);
		
		$serie_venta=$datos['venta']['serie_venta'];
		$folio_venta=$datos['venta']['folio_venta'];
		$fecha_venta=$datos['venta']['fecha_venta'];
		
		$concepto_venta=$datos['venta']['concepto_venta'];
		//----------------------------------------------------
		//			Titulo del reporte
		//----------------------------------------------------
		$this->SetTextColor(0, 0, 0);
		$font="Arial";		
		$ancho=70;	
		
		if($empresa['logotipo_sucursal']==1)
			$this->Image("images/logos/".$sucursal['logotipo'],12,5,40);
		else
			$this->Image("images/logos/".$empresa['logotipo'],12,5,40);
		
		//$this->SetXY(0,0);
		$this->Ln(20);
		
		$this->SetFont($font,'',7);
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode(strtoupper($empresa['nombre_fiscal'])),0,0,'C');	
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode("RFC: ".strtoupper($empresa['rfc'])),0,0,'C');	
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode($direccion),0,0,'C');
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode($colonia),0,0,'C');
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode($localidad),0,0,'C');
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode('TEL. '.$empresa['telefono']),0,0,'C');
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode(strtoupper($empresa['regimen_fiscal'])),0,0,'C');
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode($empresa['email']),0,0,'C');
		
		//-------------------------------------------------
		//	Se muestran los filtros usados para generar el reporte
		//-------------------------------------------------
		$hCell=3.5;//		Alto de la celda
		$border=0;
		$filtroEstado='';
		
		//-----------------------Ahora a imprimir los datos----------------------
		$this->SetFont($font,'B',7);		
		
		$this->Ln();
		//$this->Ln();
		//$this->SetX(2);
		//$this->Cell(80,$hCell,mb_strtoupper(UTF8_decode($sucursal)),$border,0,'L');	#	Valor
		$this->Ln();
		$this->SetX(2);
		$this->Cell($ancho,$hCell,"VENTA: ".$serie_venta." ".$folio_venta,$border,0,'L');	#	Valor
		$this->Ln();
		$this->SetX(2);
		$this->Cell($ancho,$hCell,"FECHA HORA: ".mb_strtoupper(UTF8_decode($fecha_venta)) ,$border,0,'L');			
		$this->Ln();
		$this->SetX(2);
		$this->Cell($ancho,$hCell,"CLIENTE: ".mb_strtoupper(UTF8_decode($nombre_fiscal)) ,$border,0,'L');
		
		if(!empty($nombre_agente)){
			$this->Ln();
			$this->SetX(2);
			$this->Cell($ancho,$hCell,"AGENTE: ".mb_strtoupper(UTF8_decode($nombre_agente)) ,$border,0,'L');	
		
		}
		
						#	Label
		$this->SetFont($font,'',7);						
		$this->Ln();
		$this->SetX(0);		
       // $this->Cell(80,$hCell,'------------------------------------------------------------------------------------------',0,0,'C');		#			
		//$this->ln();
		
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
			$this->SetFont($font,'',8);
			//$this->SetX(10);			
			$this->SetTextColor(0, 0, 0);
			// $this->Cell(41,2,$this->tipoComprobanteTitulo,$border,1,'L');
			// $this->ln();			
			$fill=1;
			$this->SetX(2);
			$wCliente=$this->wCliente;
        	$this->SetTextColor(255, 255, 255);
        	$this->SetFillColor(0, 0, 0);
	       	$this->SetFont('Arial','',7);
			$this->Cell(10,5,"CANT.",$border,0,"R",$fill);//------Header Impuestos
			$this->Cell(30,5,utf8_decode("DESCRIPCION"),$border,0,"C",$fill);		//------Header
			$this->Cell(12,5,"PRECIO",$border,0,"R",$fill);//------Header Impuestos
			$this->Cell(15,5,"IMPORTE",$border,0,"R",$fill);//------Header Impuestos
			$this->ln();
			//$this->SetXY(10,$this->yEncabezadoDeTabla + 1 );
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
		//$numPag=$this->PageNo();
		// $this->subtotal[$numPag]=0;	//Subtotal Por Pagina
		// $this->subtotalImpuestos[$numPag]=0;	//Subtotal Por Pagina
		// $this->impuestosAcumulados=0;
		$font="Arial";
		$this->SetFont($font,'',6);
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
		$this->SetX(2);
			// $this->Cell(15,5,utf8_decode($dato['cantidad']),0,0,'',$fill);
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
			// $fill=0;
			
			
					
			$Codigo = $dato['codigo'];
			$CodigoBarras = $dato['codigo_barras'];			
			$Descripcion = $dato['descripcion'];
			$strLength = strlen($Descripcion);
			$strLimit = 23;
			if($strLength > $strLimit){
					
					$Descripcion = substr($Descripcion, 0, $strLimit-3);
					$Descripcion.='...';
			}
			$CodigoUnidad = $dato['codigo_unidad'];
			$Cantidad = number_format($dato['cantidad'] ,$decimales ,  '.' , ',' );
			$Precio = number_format($dato['precio'] ,$decimales ,  '.' , ',' );
			$Importe = number_format($dato['importe'] ,$decimales ,  '.' , ',' );
			$Descuento = number_format($dato['descuento'] ,$decimales ,  '.' , ',' );
			$Subtotal = number_format($dato['subtotal'] ,$decimales ,  '.' , ',' );
			$Impuestos = number_format($dato['impuestos'] ,$decimales ,  '.' , ',' );
			$Total = number_format($dato['total'] ,$decimales ,  '.' , ',' );
			
			
			$this->Cell(10,$heightCell,utf8_decode($Cantidad),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(29,$heightCell,utf8_decode($Descripcion),$border,$saltoDeLinea,'',$fill);	
		
			
			$this->Cell(12,$heightCell,utf8_decode($Precio),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Importe),$border,$saltoDeLinea,'R',$fill);	
			
			// $this->Cell(31,$heightCell,utf8_decode($datos[$i]['cantidad']),$border,$saltoDeLinea,"L",$fill);		//------Header 
			// $this->Cell($widthFechaUlt,$heightCell,utf8_decode($datos[$i]['FechaLastRegistro']),$border,$saltoDeLinea,"L",$fill);		//------Header
			// $this->Cell($widthMes,$heightCell,utf8_decode($mes),$border,$saltoDeLinea,"L",$fill);		//------Header
			// $this->Cell($widthAnio,$heightCell,$datos[$i]['Anio'],$border,$saltoDeLinea,"R",$fill);//------Header Impuestos
			// $this->Cell(25,$heightCell,$datos[$i]['Saldo'],$border,0,"R",$fill);		//------Header TOTAL	
			// $this->Cell($widthConsumo,$heightCell,$datos[$i]['ConsumoCalculado'],$border,$saltoDeLinea,"R",$fill);//------Header Impuestos
			// $this->Cell($widthSaldo,$heightCell,$datos[$i]['SaldoCalculado'],$border,1,"R",$fill);//------Header Impuestos
			//$fill = !$fill;//Alterna el valor de la bandera
			//Comienzo End
			$this->ln();
			
			
		}
		// $this->ln(2);
		$this->imprimirHeader=false;
		$this->imprimirSubtotalesDesdeFooter=false;
		$ultimo=true;
		// $this->imprimirTotales($ultimo);
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
		//$i=$this->PageNo();
		
		$datos=$this->datos['data'];
		
		$importes = number_format ($datos['venta']['importe'] ,$decimales ,  '.' , ',' );
		$descuento = number_format ($datos['venta']['descuento'] ,$decimales ,  '.' , ',' );
		$subtotal = number_format ($datos['venta']['subtotal'] ,$decimales ,  '.' , ',' );
		$impuestos = number_format ($datos['venta']['impuestos'] ,$decimales ,  '.' , ',' );
		$total = number_format ($datos['venta']['total'] ,$decimales ,  '.' , ',' );
		$pago = number_format ($datos['venta']['pago'] ,$decimales ,  '.' , ',' );
		$cambio = number_format ($datos['venta']['cambio'] ,$decimales ,  '.' , ',' );
		
		$sucursal=$datos['sucursal'];		
		$direccion_sucursal = strtoupper($sucursal['calle']." ".$sucursal['numext']." ".$sucursal['numint']);
		$colonia_sucursal = strtoupper('COL. '.$sucursal['colonia']." CP: ".$sucursal['cp']);
		$localidad_sucursal = strtoupper($sucursal['nom_ciu'].", ".$sucursal['nom_est'].", ".$sucursal['nom_pai']);
			
		// $this->SetY(-$this->limite_det);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		// $y = 240;
		// $this->SetY($y);			
		$ancho = 70;
		$border = 0;
		$columna = 41;
		$alto   = 3.5;
		$this->SetFont('Arial','',7);
		// $this->SetXY($columna,$y);
		// $this->Ln();
		$this->SetX($columna);	
		$this->Cell(12, $alto, "IMPORTE:$", $border, 0, 'R');
		$this->Cell(15, $alto,$importes, $border, 1, 'R');
		// $this->Ln();
		$this->SetX($columna);
		$this->Cell(12, $alto, "DESCUENTOS:$", $border, 0, 'R');
		$this->Cell(15, $alto, $descuento, $border, 1, 'R');
		// $this->Ln();
		$this->SetX($columna);
		$this->Cell(12, $alto, "SUBTOTAL:$", $border, 0, 'R');
		$this->Cell(15, $alto, $subtotal, $border, 1, 'R');
		// $this->Ln();				
		$this->SetX($columna);
		$this->Cell(12, $alto, "TOTAL:$", $border, 0, 'R');
		$this->Cell(15, $alto, $total, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->Cell(12, $alto, "SU PAGO:$", $border, 0, 'R');
		$this->Cell(15, $alto, $pago, $border, 1, 'R');
		
		$this->SetX($columna);
		$this->Cell(12, $alto, "CAMBIO:$", $border, 0, 'R');
		$this->Cell(15, $alto, $cambio, $border, 1, 'R');
		
		$decimal = round(($total - floor($total)) * 100); // determina los decimales
		
		$this->SetX($columna);  
				
		$moneda='PESOS';
		$abrevMoneda='M.N.';
		
		$this->Ln();				
		$this->SetX(2);
		$this->MultiCell($ancho, 4, strtoupper(num2letras($total))." $moneda ".str_pad($decimal, 2, '0', STR_PAD_LEFT)."/100 $abrevMoneda", $border, 1,'L');
		
		$this->Ln();
		$this->SetX(0);
		$this->Cell($ancho,$alto,mb_strtoupper(UTF8_decode("GRACIAS POR SU COMPRA")),$border,0,'C');	#	Valor
		
		$this->Ln();
		$this->Ln();
		$this->SetX(0);
		$this->Cell($ancho,$alto,mb_strtoupper(UTF8_decode("EXPEDIDO EN")),$border,0,'C');	#	Valor
		$this->Ln();
		$this->SetX(0);
		$this->Cell($ancho,$alto,mb_strtoupper(utf8_decode($direccion_sucursal)),$border,0,'C');	#	Valor
		$this->Ln();
		$this->SetX(0);
		$this->Cell($ancho,$alto,utf8_decode($colonia_sucursal),$border,0,'C');	#	Valor
		$this->Ln();
		$this->SetX(0);
		$this->Cell($ancho,$alto,mb_strtoupper(UTF8_decode($localidad_sucursal)),$border,0,'C');	#	Valor
		
		$this->aceptarSalto=true;
		
		
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
		//$this->Cell(0,$wFooter,UTF8_decode('PÃ¡gina')." ".$this->PageNo().'/{nb}',0,0,'C');
		
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
