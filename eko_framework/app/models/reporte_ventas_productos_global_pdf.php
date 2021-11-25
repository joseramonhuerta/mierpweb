<?php
require ('eko_framework/includes/fpdf.php');
//ESTA CLASE DEBERIA TENER PUROS CICLOS DE IMPRESION,
// DEBERIA PROCESAR INFORMACION SOLAMENTE PARA EL ASPECTO VISUAL
class ReporteVentasProductosGlobalPDF extends FPDF{
	var $cerrando=false;
	var $colorHeader;
	var $colorLetrasHeader;
	var $imprimirHeader=true;
	var $imprimirTotales=false;
	var $nueva=true;
	var $decimales=2;
	var $totales=0;
	var $aceptarSalto=true;
	var $imprimirSubtotalesDesdeFooter=true;
	function ReporteVentasProductosGlobalPDF($orientation='L',$unit='mm',$format='Letter',$datos,$formatos){
		if (empty($this->yEncabezadoDeTabla))	$this->yEncabezadoDeTabla=28;
	 	$this->datos=$datos;
	 	$this->formatos=$formatos;
	 	$this->wCliente=65;		
		 							//$decimales=$this->formatos['decimales'];	 	
	 	$this->acumuladas=0;	//Sumatoria de facturas NO Canceladas	 	
		parent::__construct('L','mm','Letter');
	 	$this->AliasNbPages(); 
		$this->SetAutoPageBreak(true, 20);
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
		
		
		$fecha_inicio=$datos['filtros']['fecha_inicio'];
		$fecha_fin=$datos['filtros']['fecha_fin'];
		
		//----------------------------------------------------
		//			Titulo del reporte
		//----------------------------------------------------
		$this->SetTextColor(0, 0, 0);
		$font="Arial";		
		$this->SetY(10,10);
		$this->SetFont($font,'B',14);
					
		$this->Cell(100,10,utf8_decode("Reporte de Ventas por Producto"),0,0,'L');	
		$this->SetX(108);
		
		//-------------------------------------------------
		//	Se muestran los filtros usados para generar el reporte
		//-------------------------------------------------
		$hCell=4;//		Alto de la celda
		$border=0;
		$filtroEstado='';
		
		//-----------------------Ahora a imprimir los datos----------------------
		
		$this->SetFont($font,'',9);		
		
		$this->SetFont($font,'',9);	
		#	Label
							
		$this->Ln();
		
		$this->SetFont($font,'B',9);
		$this->Cell(20,$hCell,"Fecha Inicio:",$border,0,'L');	#	Valor
		$this->SetFont($font,'',9);
		$this->Cell(20,$hCell,mb_strtoupper(UTF8_decode($fecha_inicio)),$border,0,'L');	#	Valor
		
		$this->SetFont($font,'B',9);
		$this->Cell(17,$hCell,"Fecha Fin:",$border,0,'L');	#	Valor
		$this->SetFont($font,'',9);
		$this->Cell(20,$hCell,mb_strtoupper(UTF8_decode($fecha_fin)),$border,0,'L');	#	Valor
		
		
        $this->Cell(106,$hCell,'',0,0);		#			
		//-----------------------------------emprezando con el rango de fechas---------------------------------------//	
		$this->SetX(170);		
		
		
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
			// $this->Cell(41,2,$this->tipoComprobanteTitulo,$border,1,'L');
			// $this->ln();			
			$fill=1;
			$wCliente=$this->wCliente;
        	$this->SetTextColor(255, 255, 255);
        	$this->SetFillColor(0, 0, 0);
	       	$this->SetFont('Arial','',8);
			
			
			$this->Cell(15,5,utf8_decode("CODIGO"),$border,0,"C",$fill);		//------Header 
			$this->Cell(60,5,utf8_decode("PRODUCTO"),$border,0,"C",$fill);		//------Header
			$this->Cell(38,5,utf8_decode("LINEA"),$border,0,"C",$fill);
			$this->Cell(25,5,utf8_decode("SUCURSAL"),$border,0,"C",$fill);	
			$this->Cell(15,5,utf8_decode("CANT"),$border,0,"C",$fill);	
			$this->Cell(15,5,utf8_decode("PRECIO"),$border,0,"C",$fill);			
			$this->Cell(15,5,utf8_decode("STOCK"),$border,0,"C",$fill);
			$this->Cell(15,5,utf8_decode("SUBTOTAL"),$border,0,"C",$fill);	
			
			
			
			
			$this->SetXY(10,$this->yEncabezadoDeTabla + 1 );
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
		$numPag=$this->PageNo();
		// $this->subtotal[$numPag]=0;	//Subtotal Por Pagina
		// $this->subtotalImpuestos[$numPag]=0;	//Subtotal Por Pagina
		// $this->impuestosAcumulados=0;
		$font="Courier";
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
		foreach($datos as $dato){	
			// $this->Cell(15,5,utf8_decode($dato['cantidad']),0,0,'',$fill);
			$saltoDeLinea = 0;
			#		Primer linea
			$this->aceptarSalto=true;	
			
			$this->SetFont($font,'',7);
			
			//Comienzo 
			$heightCell = 4;
			
			$widthFechaUlt = 37;
			$widthMes = 25;
			$widthSaldo = 25;
			$widthConsumo = 25;
			$widthAnio = 16;
			$saltoDeLinea = 0;
			// $fill=0;
			
			//v.serie_venta,v.folio_venta,v.fecha_venta,c.nombre_fiscal,v.importe,v.descuento,v.subtotal,v.impuestos,v.total
					
			$Codigo = $dato['codigo'];
				
			$Descripcion = $dato['descripcion'];
			$NombreLinea = $dato['nombre_linea'];
			$NombreSucursal = $dato['nombre_sucursal'];		

			$Venta = number_format($dato['ventas'] ,$decimales ,  '.' , ',' );
			$Precio = number_format($dato['precio'] ,$decimales ,  '.' , ',' );
			$Subtotal = number_format($dato['subtotal'] ,$decimales ,  '.' , ',' );
			$Stock = number_format($dato['stock'] ,$decimales ,  '.' , ',' );					
		
									
			$this->Cell(15,$heightCell,utf8_decode($Codigo),$border,$saltoDeLinea,'',$fill);						
			$this->Cell(60,$heightCell,utf8_decode($Descripcion),$border,$saltoDeLinea,'',$fill);
			$this->Cell(38,$heightCell,utf8_decode($NombreLinea),$border,$saltoDeLinea,'',$fill);	
			$this->Cell(25,$heightCell,utf8_decode($NombreSucursal),$border,$saltoDeLinea,'',$fill);	
			$this->Cell(15,$heightCell,utf8_decode($Venta),$border,$saltoDeLinea,'R',$fill);
			$this->Cell(15,$heightCell,utf8_decode($Precio),$border,$saltoDeLinea,'R',$fill);
			$this->Cell(15,$heightCell,utf8_decode($Stock),$border,$saltoDeLinea,'R',$fill);
			$this->Cell(15,$heightCell,utf8_decode($Subtotal),$border,$saltoDeLinea,'R',$fill);	
					
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
			
			$this->totales = $this->totales + $dato['subtotal'];
		}
		// $this->ln(2);
		$this->imprimirHeader=false;
		$this->imprimirSubtotalesDesdeFooter=false;
		$ultimo=true;
		$this->imprimirTotales($ultimo);
        $this->SetTextColor(0, 0, 0);
			
		// $hCell=5;
		// $PageBreakTrigger=$this->h-$this->bMargin;
		// $this->despuesdelosdetalles();
	}
	
	function despuesdelosdetalles(){
		$hCell=10;
		$PageBreakTrigger=$this->h-$this->bMargin;
		$limite=$PageBreakTrigger-$hCell*3;
		$y=$this->GetY();
		if ($y>$limite){
			$this->AddPage();
		}
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
		
		$total = number_format ($this->totales ,$decimales ,  '.' , ',' );
		
		$this->SetY(-$this->limite_det);//ES NEGATIVO PARA EMPEZAR A CONTAR DESDE EL MARGEN INFERIOR DE LA PAGINA HACIA ARRIBA
		$y = 260;
		$this->SetY($y);			
		
		$border = 0;
		$columna = 156;
		$alto   = 4.7;
		$this->SetFont('Arial','B',9);
		$this->SetXY($columna,$y);
		
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
		$this->Cell(0,$wFooter,UTF8_decode('Pagina')." ".$this->PageNo().'/{nb}',0,0,'C');
		
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