<?php
require ('eko_framework/includes/pdf_js.php');
require ('eko_framework/includes/funciones.php');
//ESTA CLASE DEBERIA TENER PUROS CICLOS DE IMPRESION,
// DEBERIA PROCESAR INFORMACION SOLAMENTE PARA EL ASPECTO VISUAL
class ReporteAbonoTicketPDF extends PDF_JavaScript{
	var $cerrando=false;
	var $colorHeader;
	var $colorLetrasHeader;
	var $imprimirHeader=true;
	var $imprimirTotales=false;
	var $nueva=true;
	var $decimales=2;
	var $aceptarSalto=true;
	var $imprimirSubtotalesDesdeFooter=true;
	function ReporteAbonoTicketPDF($orientation='L',$unit='mm',$format='Letter',$datos,$formatos){
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
		$nombre_fiscal = $datos['empresa']['nombre_fiscal'];
		//$direccion = strtoupper($empresa['calle']." #".$empresa['numext']." ".$empresa['numint']);
		// $colonia = strtoupper('COL. '.$empresa['colonia']." ".$empresa['cp']);
		// $localidad = strtoupper($empresa['nom_ciu'].", ".$empresa['nom_est'].", ".$empresa['nom_pai']);
		
		
		$serie=$datos['abono']['serie'];
		$folio=$datos['abono']['folio'];
		$nombre_cliente=$datos['abono']['nombre_cliente'];
		$fecha_turno=$datos['abono']['fecha'];		
		$concepto=$datos['abono']['concepto'];
		$observacion=$datos['abono']['observacion'];
		//----------------------------------------------------
		//			Titulo del reporte
		//----------------------------------------------------
		$this->SetTextColor(0, 0, 0);
		$font="Arial";		
		$ancho=70;	
		if($empresa['logotipo_sucursal'] == 1)
			$this->Image("images/logos/".$sucursal['logotipo'],12,5,40);
		else
			$this->Image("images/logos/".$empresa['logotipo'],12,5,40);
		
		//$this->SetXY(0,0);
		$this->Ln(20);
		
		$this->SetFont($font,'',7);
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode(strtoupper($empresa['nombre_comercial'])),0,0,'C');	
		$this->Ln();
		$this->SetX(0);	
		$this->Cell($ancho,3,utf8_decode("RFC: ".strtoupper($empresa['rfc'])),0,0,'C');	
		
		$hCell=3.5;//		Alto de la celda
		$border=0;
		$filtroEstado='';
		
		//-----------------------Ahora a imprimir los datos----------------------
		$this->SetFont($font,'B',7);		
		
		$this->Ln();
		$this->Ln();
		$this->SetX(2);
		$this->Cell(80,$hCell,mb_strtoupper(UTF8_decode("ABONO CXC")),$border,0,'L');	#	Valor
		$this->Ln();
		$this->SetX(2);
		$this->Cell(80,$hCell,mb_strtoupper(UTF8_decode($nombre_sucursal)),$border,0,'L');	#	Valor
		$this->Ln();
		$this->SetX(2);
		$this->Cell($ancho,$hCell,"FECHA HORA: ".mb_strtoupper(UTF8_decode($fecha_turno)) ,$border,0,'L');	
		$this->Ln();
		$this->SetX(2);
		$this->Cell(80,$hCell,mb_strtoupper(UTF8_decode($serie.' - '.$folio)),$border,0,'L');	#	Valor		
		$this->Ln();
		$this->SetX(2);
		$this->Cell(80,$hCell,mb_strtoupper(UTF8_decode($nombre_cliente)),$border,0,'L');	#	Valor	
		$this->Ln();
		$this->SetX(2);
		$this->Cell(80,$hCell,mb_strtoupper(UTF8_decode($concepto)),$border,0,'L');	#	Valor
		$this->Ln();
		$this->SetX(2);
		$this->Cell(80,$hCell,mb_strtoupper(UTF8_decode($observacion)),$border,0,'L');	#	Valor	
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
			
			
					
			$Formapago = $dato['nombre_formapago'];
			$Denominacion = number_format($dato['denominacion'] ,$decimales ,  '.' , ',' );
			$Cantidad = number_format($dato['cantidad'] ,$decimales ,  '.' , ',' );
			$Total = number_format($dato['total'] ,$decimales ,  '.' , ',' );
			
			$this->Cell(31,$heightCell,utf8_decode($Formapago),$border,$saltoDeLinea,'',$fill);	
			$this->Cell(12,$heightCell,utf8_decode($Denominacion),$border,$saltoDeLinea,'R',$fill);	
			$this->Cell(12,$heightCell,utf8_decode($Cantidad),$border,$saltoDeLinea,'R',$fill);					
			$this->Cell(12,$heightCell,utf8_decode($Total),$border,$saltoDeLinea,'R',$fill);	
			
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
		
	
		$total = number_format ($datos['abono']['importe'] ,$decimales ,  '.' , ',' );
		
		$ancho = 70;
		$border = 0;
		$columna = 0;
		$alto   = 3.5;
		$this->SetFont('Arial','B',15);
		$this->ln(5);
		$this->SetX($columna);
		$this->Cell(60, $alto, "TOTAL: $".$total, $border, 0, 'C');
		
		
		// $decimal = round(($total - floor($total)) * 100); // determina los decimales
		
		// $this->SetX($columna);  
				
		// $moneda='PESOS';
		// $abrevMoneda='M.N.';
		
		// $this->Ln();				
		// $this->SetX(2);
		// $this->MultiCell($ancho, 4, strtoupper(num2letras($total))." $moneda ".str_pad($decimal, 2, '0', STR_PAD_LEFT)."/100 $abrevMoneda", $border, 1,'L');
		
		
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
