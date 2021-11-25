<?php

class VentaPdf extends FPDF{
	var $formato=false;
	var $fijarX=false;
	
	function generarPdf($ventaObj,$filename='venta.pdf'){
		$this->ventaObj=$ventaObj;
		$this->yFooter=223.5;
		$this->SetFont('Arial','B',19);
		$this->SetAutoPageBreak(true,40);
		$this->AddPage();
		//$this->setY(50);
		$this->imprimeConceptos();
		//------------------------------------------------				
		return $this->Output($filename,'S');
	}
	
	function header(){
		$yLogo=$this->getY();
		$this->imprimeLogo();
		$this->addY(18.3);
		$this->setFont('Arial','B',10);
		$this->addX(2);
		//$this->cell(10,4,'www.detallesbravo.com',0,1);
		$this->setY($yLogo-6);
		$this->imprimeDatosDelDocumento();		
		$y=$this->getY()+6;
		$this->setY($yLogo);
		$this->fijarX(9);		
		$this->imprimeEmisor();
		$y=$this->getY();
		$this->fijarX(107);	$this->setY($yLogo);	
		$this->imprimeSucursal();
		$this->setY($y+4);
		
		$this->liberarX();
		//$this->setY($yLogo);
		$this->setX(7);
		$this->imprimeReceptor();
		//$this->addY(2);//$y=this->getY(); $this->setY($y+5);
		//$this->setX(167);
		//$this->ln();
		//$this->Cell(40,3,'Efectos fiscales al pago',0,1);
		$this->setX(170);
		
		$this->imprimeEncabezados();
		
	}
	
	
	function imprimeAdicional(){
		/*$this->formato=false;
		$this->addy(3);
		$y=$this->getY();
		//---------------------------------------------------------------------------------------
		$this->setFont('Arial','',5); $this->cell(20,3,'Versión del CFD:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,$this->ventaObj['version'],0,1);
		//---------------------------------------------------------------------------------------
		$this->setFont('Arial','',5); $this->cell(20,3,'N0. Certificado:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,$this->ventaObj['noCertificado'],0,1);
		//---------------------------------------------------------------------------------------
		$this->setFont('Arial','',5); $this->cell(20,3,'Año de aprobación:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,$this->ventaObj['anoAprobacion'],0,1);
		//---------------------------------------------------------------------------------------
		$this->setFont('Arial','',5); $this->cell(20,3,'Número de aprobación:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,$this->ventaObj['noAprobacion'],0,1);
		//---------------------------------------------------------------------------------------
		$this->setY($y);
		$x2=60;
		$this->setX($x2);		
		$this->setFont('Arial','',5); $this->cell(20,3,'Tipo de comprobante:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,$this->ventaObj['tipoDeComprobante'],0,1);
		//---------------------------------------------------------------------------------------
		$this->setX($x2);
		$this->setFont('Arial','',5); $this->cell(20,3,'Lugar de expedicion:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,utf8_decode($this->ventaObj['LugarExpedicion']),0,1);
		//---------------------------------------------------------------------------------------
		$this->setX($x2);
		$this->setFont('Arial','',5); $this->cell(20,3,'Metodo de Pago:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,$this->ventaObj['metodoDePago'],0,1);		
		//---------------------------------------------------------------------------------------
		$this->setX($x2);
		$this->setFont('Arial','',5); $this->cell(20,3,'Metodo de Pago:',0,0);
		$this->setFont('Arial','',7); $this->cell(10,3,$this->ventaObj['metodoDePago'],0,1);
		//---------------------------------------------------------------------------------------*/
	}
	function imprimeLogo(){						
		if ( !empty($this->ventaObj['logo']) ){
			$this->Image($this->ventaObj['logo'],190,11,15);
		}		
	}
	
	function imprimeEmisor(){
		$emisor=$this->ventaObj['Emisor'];	
		$y1=$this->gety(); $this->setY($y1+2.5);
		$this->SetFont('Arial','',4);	 $this->formato=false;
		$this->Cell(40,0,'EMISOR:',0,1);
		$this->SetFont('Arial','',12);		
		$altura=3;$this->formato=true;
		$this->Cell(40,6,$emisor['nombre'],0,1);
		$this->SetFont('Arial','',4);		
		$this->Cell(18,2,'DOMICILIO FISCAL:',0,1);				
		//--------- DOMICILIO --------- //
		$domicilio=$emisor['DomicilioFiscal'];
		$dom=$domicilio['calle']. ' #'.$domicilio['noExterior'];
		$dom.=!empty($domicilio['noInterior'])? ' INT '.$domicilio['noInterior'] : '';
		$dom.=' '.$domicilio['colonia'];		
		$this->SetFont('Arial','',6);
		$this->Cell(40,$altura,$dom,0,.1);
		//--------- 
		$loc=$domicilio['localidad'].' '.$domicilio['municipio'].', '.$domicilio['estado'].', '.$domicilio['pais'].', CP:'.$domicilio['codigoPostal'];
		 $this->Cell(40,$altura,$loc,0,1);		
		 #-------------------------------
		$this->SetFont('Arial','B',6);	$this->Cell(6,$altura,'RFC: ',0,0); 
		$this->liberarX();
		$this->SetFont('Arial','',6);	$this->Cell(18,$altura,$emisor['rfc'],0,0);
	}
	
	function imprimeSucursal(){
		if ( empty($this->ventaObj['Sucursal']) ){
			return true;
		}
		$sucursal=$this->ventaObj['Sucursal'];	
		$y1=$this->gety(); $this->setY($y1+2.5);
		$this->SetFont('Arial','',4);	 $this->formato=false;
		$this->Cell(40,0,'SUCURSAL:',0,1);
		$this->SetFont('Arial','',12);		
		$altura=3;$this->formato=true;
		$this->Cell(40,6,$sucursal['nombre'],0,1);
		$this->SetFont('Arial','',4);		
		$this->Cell(18,2,'DOMICILIO FISCAL:',0,1);				
		//--------- DOMICILIO --------- //
		$domicilio=$sucursal['Domicilio'];
		$dom=$domicilio['calle']. ' #'.$domicilio['noExterior'];
		$dom.=!empty($domicilio['noInterior'])? ' INT '.$domicilio['noInterior'] : '';
		$dom.=' '.$domicilio['colonia'];		
		$this->SetFont('Arial','',6);
		$this->Cell(40,$altura,$dom,0,.1);
		//--------- 
		$loc=$domicilio['localidad'].' '.$domicilio['municipio'].', '.$domicilio['estado'].', '.$domicilio['pais'].', CP:'.$domicilio['codigoPostal'];
		 $this->Cell(40,$altura,$loc,0,1);		

	}
	
	function imprimeReceptor(){
		$Receptor=$this->ventaObj['Receptor'];			
		$x1=$this->getX();
		
		$x=$x1+2;
		$y1=$this->gety();
		
		$this->setY($y1+2.5);
		$alto=3;		
		
		$this->SetFont('Arial','',4);	 $this->formato=false;
		$this->setX($x); $this->Cell(40,0,'CLIENTE:',0,1);				
		$this->formato=true;
		
		$this->SetFont('Arial','',12);		
		$this->setX($x); $this->Cell(40,6,$Receptor['nombre'],0,1);
		$this->SetFont('Arial','',6);		
		
		//--------- DOMICILIO --------- //
		$this->SetFont('Arial','',4);	 $this->formato=true;
		$this->setX($x); $this->Cell(40,2,'DOMICILIO:',0,1);
		
		$this->SetFont('Arial','',6);		
		$domicilio=$Receptor['Domicilio'];			
		$dom=$domicilio['calle'].' #'.$domicilio['noExterior'];!
		$dom.=( isset($domicilio['noInterior']) )? ' INT '.$domicilio['noInterior'] : '';
		$dom.=' '.$domicilio['colonia'];
		$dom=trim($dom);
		if( !empty($dom) ){ 
			$this->setX($x); $this->Cell(40,$alto,$dom,0,1);
		}
		$loc=$domicilio['localidad'];
		if ( !empty($domicilio['municipio']) ){		
			$loc.=( empty($domicilio['localidad']) )? $domicilio['municipio'] : ', '.$domicilio['municipio'];
		}
		$loc.=', '.$domicilio['estado'].', '.$domicilio['pais'].'. CP:'.$domicilio['codigoPostal']; 
		$this->setX($x); $this->Cell(40,$alto,$loc,0,1);	
		
		
		$this->SetFont('Arial','B',6);		
		$this->setX($x);$this->Cell(6,$alto,'RFC:',0,0);
		$this->SetFont('Arial','',6);		
		 $this->Cell(40,$alto,$Receptor['rfc'],0,0);		
	}
	
	
	function imprimeDatosDelDocumento(){
		$doc=$this->ventaObj;
		$y=37;
		 $this->setY($y-1);
		$x=163;
		$this->SetFont('Courier','B',9);		
		$this->setX($x); $this->Cell(30,3,'ORDEN DE VENTA',0,1,'L');	
		$this->SetFont('Courier','',7);		
		$this->formato=false;
		$this->setX($x); $this->Cell(15,3,'Folio',0,0,'L');		
		$serfol=$doc['serie'].' '.$doc['folio'];
		$this->Cell(30, 3, $serfol,0,1,'R');			
		$y=$this->getY(); $this->setY($y+0.5);
		$this->setX($x); $this->Cell(15,3,'Fecha',0,0,'L');		
		$fecha=$doc['fecha'];
		$this->Cell(30,3,$fecha,0,1,'L');		 
		$this->SetLineWidth(.3);
		$this->SetDrawColor(255,182,193);
		
		//$this->line($x,$y,$x+60,$y);
	}
	
	function imprimeEncabezados(){
		$x1=$this->getx();
		$y1=$this->gety();
		$this->SetFillColor(0,0,0);
		//$this->rect($x1,$y1,190,4,'F');
		$this->setY($y1);
		$this->SetTextColor(255,255,255);
		$this->SetFont('Courier','B',8);		
		$this->SetDrawColor(255,255,255);
		$this->Cell(20,4,'CODIGO',1,0,'L',true);
		$this->Cell(77,4,'DESCRIPCION',1,0,'L',true);		
		$this->Cell(20,4,'CANTIDAD',1,0,'R',true);
		$this->Cell(15,4,'UM',1,0,'L',true);
		$this->Cell(30,4,'P. UNITARIO',1,0,'R',true);
		$this->Cell(35,4,'IMPORTE',1,1,'R',true);
	}
	
	function imprimeConceptos(){
		$this->SetTextColor(0,0,0);
		$this->SetFillColor(255,255,255);
		$this->SetFont('Courier','',7);
		$conceptos=$this->ventaObj['Conceptos'];
		$border=0;
		foreach ($conceptos as $concepto){
			//echo "<pre>"; print_r($concepto); echo "</pre>";exit;	
			$this->Cell(20,4,'',				$border,0,'L',true);
			$this->Cell(77,4,$concepto['descripcion'],	$border,0,'L',true);
			$this->Cell(20,4,formatearCantidad( $concepto['cantidad'] ),		$border,0,'R',true);
			$this->Cell(15,4,$concepto['unidad'],		$border,0,'L',true);
			$this->Cell(30,4,formatearMoneda( $concepto['valorUnitario'] ),$border,0,'R',true);
			$this->Cell(35,4,formatearMoneda( $concepto['importe'] ),		$border,1,'R',true);
			if ( !empty($concepto['componentes'] ) ){
				$this->Cell(20,3,'', $border,0,'R',true);
				$this->SetFont('Courier','B',6);
				$this->Cell(77,3,'COMPONENTES DEL KIT:',	$border,1,'L',true);
				$this->SetFont('Courier','',7);
				foreach($concepto['componentes'] as $componente){
					$this->Cell(15,3,'',				$border,0,'R',true);
					$this->Cell(20,3,formatearCantidad( $componente['cantidad'] ),		$border,0,'R',true);
					$this->Cell(73,3,$componente['descripcion'],	$border,1,'L',true);									
				}
			}
		}
	}
	
	function imprimeLeyendas(){
		$this->Cell(40,10,'LEYENDAS',0,1);		
	}
	
	function imprimeTotales(){
		$factura=$this->ventaObj;
		$x=161;
		$y1=$this->getY();
		$this->addY(12);
		$this->formato=false;
		$border=0;
		$this->rect($x,$y1,20,26.5,'F');

		# -- Dibuja marcos al detalle y al footer
		$this->SetFont('ARIAL','B',9);	
		$this->SetDrawColor(0, 0, 0);
		$this->SetLineWidth(0.2);
		$this->SetFillColor(0,0,0);
		$this->Rect(152, 238.5, 28, 26.5, true); // marco relleno de los totales
		$this->Rect(10, 238.5, 197, 26.5); // marco del footer
		//$this->liberarX();
		$this->setTextColor(255,255,255);	
		$this->setX($x);$this->Cell(20,5,'SUBTOTAL:',$border,0,'R');	
		$this->setTextColor(0,0,0);							$this->Cell(26,4,formatearMoneda( $factura['subTotal'] ),$border,1,'R');
		$this->setTextColor(255,255,255);	$this->setX($x);$this->Cell(20,5,'IMPUESTOS:',$border,0,'R');	
		$this->setTextColor(0,0,0); 						$this->Cell(26,4,formatearMoneda( '0.000000' ),0,1,'R');
		$this->SetFont('ARIAL','B',9);	
		$this->setTextColor(255,255,255);	$this->setX($x);$this->Cell(20,5,'TOTAL:',$border,0,'R');
		$this->setTextColor(0,0,0); 						$this->Cell(26,5,formatearMoneda( $factura['total'] ),$border,1,'R');
		$y2=$this->getY();
	}
	
	function footer(){
		$this->setY($this->yFooter);
		
		$this->SetFillColor(0,0,0);
		$this->addY(15);
		//$this->rect($this->getX(),$this->getY(),190,.2,'F');		
		$this->Cell(40,1,'',0,1);
		
		$y=$this->getY()-1;		
		
		$this->setY(238.5);
		$this->imprimeAdicional();
		$this->setY($y);
		$this->imprimeTotales();
		
		$y=$this->getY();
		
		$alto=1.3;
		$this->cell(0,3,'',0,1);
		$this->SetFont('Arial','',7);				
		//--------------------------------------------------------------------
		$this->setY(265);$this->SetFillColor(0,0,0);
		//$this->rect($this->getX(),$this->getY(),190,.1,'F');		
		$this->Cell(20,4,'Pagina '.$this->PageNo().' de {nb}',0,0);
		$this->setx(163);
		 $this->Cell(25,3,'www.upctechnologies.com',0,1);
	}
	//======================================================================================
	//				Funciones de utilería
	//======================================================================================
	function fijarX($x){
		$this->x1Fija=$x;
		$this->fijarX=true;		
	}
	function liberarX(){
		$this->fijarX=false;
	}
	function addX($cantidad){
		$x=$this->getX()+$cantidad;
		$this->setX($x);		
	}
	function addY($cantidad){
		$y=$this->getY()+$cantidad;
		$this->setY($y);		
	}
	function Cell($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link=''){
		if ($this->fijarX){
			$this->setX($this->x1Fija);
		}
		//
		if ($this->formato){
			$txt=utf8_decode($txt);
			$txt=strtoupper($txt);
		}
		
		return parent::Cell($w, $h, $txt, $border, $ln, $align, $fill, $link);
	}
}
?>