<?php 

function generaPDF($xml_file, $rfc="mfpdf",$tipo,$cadenaOriginal='',$params=array()) {
	global $nodo_addenda;

	$tipo=strtolower($tipo);
	
	if (!isset($params['Detalles'])){
		$params['Detalles']=array();
	}
	
	/*======================================================================================
	    Aqui es necesario realizar una operacion con los kits, sus componentes y sustitutos. El asunto es el siguiente:
	    Cada kit tiene esta estructura:
		
	[componentes] => Array
			(
				[0] => Array
					(
						[IDDetalle] => 14
						[TipoArt] => P
						[KEYProdServ] => 1
						[Cantidad] => 1.000000
						[Descripcion] => MB INTEL
						[Subtotal] => 0
						[sugerencias] => Array
							(
								[0] => Array
									(
										[seleccionado] => 1
										[Descripcion] => mb sustituto
										[KEY_Kit_sus] => 4
										[KEYProdServ] => 148
										[Cantidad] => 1.000000
										[TipoArt] => P
										[IDDetalle] => 14
									)
								....
								[N]=>Array
							)
					)
				...
				[N]=>Array
			)
			
			Entonces,del elemento con la propiedad [seleccionado]=1, tomamos el valor [Descripcion]  y [Cantidad] que son los que se mostraran en el pdf
	======================================================================================*/	
	for ($i=0; $i<sizeof($params['Detalles']); $i++ ){
		$detalle=$params['Detalles'][$i];
		if (   $detalle['TipoArt'] == 'K' ){
			for($y=0; $y<sizeof($detalle['componentes']); $y++){
				$componente=$detalle['componentes'][$y];
				if ( isset($componente['seleccionado']) &&  $componente['seleccionado']==1 ){					
					$params['Detalles'][$i]['componentes'][$y]['Descripcion']=$componente['Descripcion'];
					$params['Detalles'][$i]['componentes'][$y]['Cantidad']=$componente['Cantidad'];
					$params['Detalles'][$i]['componentes'][$y]['SKUs']=$componente['SKUs'];
				}else{
					foreach($componente['sugerencias'] as $sugerencia){
						if ( isset($sugerencia['seleccionado']) && $sugerencia['seleccionado']==1 ){
							$params['Detalles'][$i]['componentes'][$y]['Descripcion']=$sugerencia['Descripcion'];
							$params['Detalles'][$i]['componentes'][$y]['Cantidad']=$sugerencia['Cantidad'];
							$params['Detalles'][$i]['componentes'][$y]['SKUs']=$sugerencia['SKUs'];
						}
					}
				}
			}
		}
	}
	//======================================================================================	
	if (!isset($params['Factura'])){
		$params['Factura']=array();
	}
	//	$rfc='MME920406UI7';
	if (file_exists("eko_framework/includes/".$rfc.".php")){
		require("eko_framework/includes/".$rfc.".php");			
		$pdf = new $rfc('P', 'mm', 'Letter');	
	}else{
		require_once("mfpdf.php");				
		$pdf = new mFPDF('P', 'mm', 'Letter');
	}
	$dom = new DOMDocument();
	$dom->preserveWhiteSpace = false;
	$dom->Load($xml_file);

	$pdf->AliasNbPages();
	$pdf->id_suc = $params['Factura']['IDSuc'];
	# Datos generales de la factura
	$nodo_padre = $dom->getElementsByTagName('Comprobante')->item(0);

	if ($tipo=='cfd'){
		$pdf->setTipoFactura('cfd');
		$pdf->fac_sello   = $nodo_padre->getAttribute('sello');
		$pdf->fac_cadena_original   = $cadenaOriginal;
	}else{
		$pdf->setTipoFactura('cfdi');
	}

	$pdf->TipDoc=(!empty($params['Factura']['TipDoc']))? $params['Factura']['TipDoc'] : '';
	//$pdf->NomComFac=(!empty($params['Factura']['NomComFac']))? $params['Factura']['NomComFac'] : '';
	$pdf->NomComFac=(!empty($params['Factura']['NomComFac']))? mb_strtoupper(utf8_decode($params['Factura']['NomComFac'])) : '';
	$pdf->telSuc=$params['Factura']['TelSuc'];
	$pdf->faxSuc=$params['Factura']['FaxSuc'];
	
	$pdf->fac_serie       = utf8_decode($nodo_padre->getAttribute('serie'));
	$pdf->fac_folio       = $nodo_padre->getAttribute('folio');
	$pdf->fac_fecha       = $nodo_padre->getAttribute('fecha');
	$pdf->fac_noAprovacion       = $nodo_padre->getAttribute('noAprobacion');
	$pdf->fac_anoAprovacion       = $nodo_padre->getAttribute('anoAprobacion');
	$pdf->fac_noCertificado       = $nodo_padre->getAttribute('noCertificado');
	
	$pdf->fac_subtotal    = (double)$nodo_padre->getAttribute('subTotal');
	$pdf->fac_total       = (double)$nodo_padre->getAttribute('total');
	$pdf->fac_descuento   = (double)$nodo_padre->getAttribute('descuento');
	$pdf->fac_tipo_cambio = $nodo_padre->getAttribute('TipoCambio');
	$pdf->fac_moneda      = mb_strtoupper(utf8_decode($nodo_padre->getAttribute('Moneda')));
	$pdf->LugarExpedicion = mb_strtoupper( utf8_decode($nodo_padre->getAttribute('LugarExpedicion')) );
	
	$pdf->NumCtaPago      = mb_strtoupper(utf8_decode($nodo_padre->getAttribute('NumCtaPago')));
	
	if ($tipo=='cfd'){		
		$pdf->fac_moneda=$params['Factura']['TipoFactura'];	
	}
	
	$pdf->fac_forma_pago  = mb_strtoupper(utf8_decode($nodo_padre->getAttribute('formaDePago')));
	if (!empty($params['Factura']['parcialidadA'])){		
		$fecha=$params['Factura']['FechaFolioFiscalOrig'];
		
		$date = DateTime::createFromFormat('d/m/Y H:i:s', $fecha);
		
		$serieYfolio=empty($params['Factura']['SerieOrig']) ? '' : $params['Factura']['SerieOrig'].'-';
		$serieYfolio.=$params['Factura']['FolioOrig'];
		
		$pdf->fac_forma_pago.=" DE $serieYfolio ".$date->format('d/m/Y h:i A').'.';		
	}	
	
	$pdf->fac_condic_pago = mb_strtoupper(utf8_decode($nodo_padre->getAttribute('condicionesDePago')));
	$pdf->fac_metodo_pago = mb_strtoupper(utf8_decode($nodo_padre->getAttribute('metodoDePago')));
	$pdf->fac_tipo_comp   = mb_strtoupper(utf8_decode($nodo_padre->getAttribute('tipoDeComprobante')));
	
	# Calcula el importe del IVA y del IEPS (Traslados)
	$pdf->fac_iva_tras  = null;
	$pdf->fac_ieps_tras = null;
	
	if ($_SESSION['Auth']['User']['RFCEmp']=="OAC1002036H4")
		$pdf->Custom_fac_iva_tras = $params['Factura']['TotImpTras'];
		
	$nodo_impuesto = $nodo_padre->getElementsByTagName('Impuestos')->item(0)->getElementsByTagName('Traslados')->item(0);
	if($nodo_impuesto) {
		foreach($nodo_impuesto->getElementsByTagName('Traslado') as $item_impuesto) :
			if ($item_impuesto->getAttribute('impuesto') == 'IVA') {
				if($pdf->fac_iva_tras  == null){
					$pdf->fac_iva_tras=0;
				}
				$pdf->fac_iva_tras += $item_impuesto->getAttribute('importe');
			}
			if ($item_impuesto->getAttribute('impuesto') == 'IEPS') {
				if($pdf->fac_ieps_tras  == null){
					$pdf->fac_ieps_tras=0;
				}
				$pdf->fac_ieps_tras += $item_impuesto->getAttribute('importe');
			}
		endforeach;
	}
	
	# Calcula el importe del IVA y del ISR (Retenciones)
	//$pdf->fac_iva_ret = 0;
	//$pdf->fac_isr_ret = 0;
	$nodo_impuesto = $nodo_padre->getElementsByTagName('Impuestos')->item(0)->getElementsByTagName('Retenciones')->item(0);
	if($nodo_impuesto) {
		foreach($nodo_impuesto->getElementsByTagName('Retencion') as $item_impuesto) :
			if ($item_impuesto->getAttribute('impuesto') == 'IVA') {
				if ( is_null($pdf->fac_iva_ret) ){
					$pdf->fac_iva_ret=0;
				}
				$pdf->fac_iva_ret += $item_impuesto->getAttribute('importe');
			}
			if ($item_impuesto->getAttribute('impuesto') == 'ISR') {
				if ( is_null($pdf->fac_isr_ret) ){
					$pdf->fac_isr_ret=0;
				}
				$pdf->fac_isr_ret += $item_impuesto->getAttribute('importe');
			}
		endforeach;
	}
		
	# Datos del emisor
	$nodo_emisor = $nodo_padre->getElementsByTagName('Emisor')->item(0);
	$pdf->IDRazSoc= $params['Factura']['IDRazSoc'];
	$pdf->em_nombre   = mb_strtoupper(utf8_decode($nodo_emisor->getAttribute('nombre')));
	$pdf->em_rfc      = mb_strtoupper(utf8_decode($nodo_emisor->getAttribute('rfc')));
	$nodo_domicilio   = $nodo_emisor->getElementsByTagName('DomicilioFiscal')->item(0);
	$pdf->em_calle    = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('calle')));
	$pdf->em_no_ext   = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('noExterior')));
	$pdf->em_no_int   = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('noInterior')));
	$pdf->em_colonia  = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('colonia')));
	$pdf->em_ciudad   = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('municipio')));
	$pdf->em_estado   = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('estado')));
	$pdf->em_pais     = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('pais')));
	$pdf->em_cp       =  $nodo_domicilio->getAttribute('codigoPostal');
	$pdf->em_localidad = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('localidad')));
	#======================================================================================================
	#	RegimenFiscal
	#======================================================================================================
	$nodo_regimen = $nodo_emisor->getElementsByTagName('RegimenFiscal');
	$pdf->regimenes='';
	foreach($nodo_regimen as $nodo){		
		$pdf->regimenes.=$nodo->getAttribute('Regimen').', ';
	}
	
	$pdf->regimenes=mb_strtoupper( utf8_decode( substr($pdf->regimenes, 0, -2) ));
	
	#======================================================================================================
	# Expedida en
	#======================================================================================================
	$pdf->expedicion = false;
	$nodo_expedida = $nodo_emisor->getElementsByTagName('ExpedidoEn')->item(0);
	if ($nodo_expedida) {
		$pdf->expedicion  = true;
		$pdf->exp_calle   =  mb_strtoupper(utf8_decode($nodo_expedida->getAttribute('calle')));
		$pdf->exp_no_ext  =  mb_strtoupper(utf8_decode($nodo_expedida->getAttribute('noExterior')));
		$pdf->exp_no_int  =  mb_strtoupper(utf8_decode($nodo_expedida->getAttribute('noInterior')));
		$pdf->exp_colonia =  mb_strtoupper(utf8_decode($nodo_expedida->getAttribute('colonia')));
		$pdf->exp_ciudad  =  mb_strtoupper(utf8_decode($nodo_expedida->getAttribute('municipio')));
		$pdf->exp_estado  =  mb_strtoupper(utf8_decode($nodo_expedida->getAttribute('estado')));
		$pdf->exp_pais    =  mb_strtoupper(utf8_decode($nodo_expedida->getAttribute('pais')));
		$pdf->exp_cp      =  mb_strtoupper($nodo_expedida->getAttribute('codigoPostal'));
		$pdf->exp_localidad= mb_strtoupper($nodo_expedida->getAttribute('localidad'));
	}
	
	# Datos del receptor
	$nodo_receptor = $nodo_padre->getElementsByTagName('Receptor')->item(0);
	$pdf->cli_nombre  =  mb_strtoupper(utf8_decode($nodo_receptor->getAttribute('nombre')));
	$pdf->cli_rfc     =  mb_strtoupper(utf8_decode($nodo_receptor->getAttribute('rfc')));
	$nodo_domicilio   = $nodo_receptor->getElementsByTagName('Domicilio')->item(0);
	if($nodo_domicilio){
		$pdf->cli_calle   =  mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('calle')));
		$pdf->cli_no_ext  =  mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('noExterior')));
		$pdf->cli_no_int  =  mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('noInterior')));
		$pdf->cli_colonia = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('colonia')));
		$pdf->cli_ciudad  = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('municipio')));
		$pdf->cli_localidad  = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('localidad')));
		$pdf->cli_estado  = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('estado')));
		$pdf->cli_pais    = mb_strtoupper(utf8_decode($nodo_domicilio->getAttribute('pais')));
		$pdf->cli_cp      = $nodo_domicilio->getAttribute('codigoPostal');
	}
	
	# Datos del Timbre Fiscal
	if ($nodo_padre->getElementsByTagName('Complemento')->item(0)){
		$nodo_timbre = $nodo_padre->getElementsByTagName('Complemento')->item(0)->getElementsByTagName('TimbreFiscalDigital')->item(0);
		$pdf->fac_sello_dig    = utf8_decode($nodo_timbre->getAttribute('selloCFD'));
		$pdf->fac_sello_SAT    = utf8_decode($nodo_timbre->getAttribute('selloSAT'));
		$pdf->fac_uuid         = utf8_decode($nodo_timbre->getAttribute('UUID'));  // folio fiscal
		$pdf->fac_certif_SAT   = utf8_decode($nodo_timbre->getAttribute('noCertificadoSAT'));
		$pdf->fac_fecha_certif = utf8_decode($nodo_timbre->getAttribute('FechaTimbrado'));
	}
	
	
	
	# Datos de la adenda
	$nodo_padre    = $dom->getElementsByTagName('Comprobante')->item(0);
	$nodo_addenda  = $nodo_padre->getElementsByTagName('Addenda')->item(0); // guardando el nodo adenda

	if ($nodo_addenda){
		$pdf->setNodoAddenda($nodo_addenda);
	}
	
	$pdf->createBCC();
	
	$pdf->setLimiteDetalle();
	$pdf->AnchoDetalles();
	
	
	$pdf->SetAutoPageBreak(1,$pdf->limite_det);
	$pdf->AddPage();
	
	/*
	# Determina total de detalles
	$pdf->total_detalle = 0;
	$nodo_concepto = $nodo_padre->getElementsByTagName('Conceptos')->item(0);
	foreach($nodo_concepto->getElementsByTagName('Concepto') as $item_concepto) :
		$pdf->total_detalle++;
	endforeach;
	
	
	# Imprime los detalles
	foreach($nodo_concepto->getElementsByTagName('Concepto') as $item_concepto) :
		$det_cantidad = $item_concepto->getAttribute('cantidad');
		$det_unidad   =  mb_strtoupper(utf8_decode($item_concepto->getAttribute('unidad')));
		$det_descripc =  mb_strtoupper(utf8_decode(html_entity_decode($item_concepto->getAttribute('descripcion'))));
		$det_val_unit = $item_concepto->getAttribute('valorUnitario');
		$det_importe  = $item_concepto->getAttribute('importe');
		$det_identif  = $item_concepto->getAttribute('noIdentificacion');
		
		$pdf->ImprimeDetalles($det_cantidad, $det_unidad, $det_descripc, $det_val_unit, $det_importe, $det_identif);
	endforeach;
	*/
	$detalles=$params['Detalles'];
	$numDetalles=sizeof($detalles);
	
	for($i=0;$i<$numDetalles;$i++){
		$det_cantidad = $detalles[$i]['Cantidad'];
		$det_unidad   = strtoupper(utf8_decode($detalles[$i]['DescUni']));
		$det_descripc = strtoupper(utf8_decode($detalles[$i]['Descripcion']));
		$tipoArt=strtoupper($detalles[$i]['TipoArt']);
		
		
		$det_val_unit = $detalles[$i]['PrecioU'];
		$det_importe  = $detalles[$i]['Importe'];
		$det_identif  =$detalles[$i]['IDDetalle'];
		
		$detalle=strtoupper(utf8_decode($detalles[$i]['Detalle']));
		$subconceptos=array();			
		if (isset($detalles[$i]['aduana']) && strlen($detalles[$i]['aduana'])>0){
			$subconceptos=json_decode($detalles[$i]['aduana'],true);		
		}
		
		$elementosDeAduana=array();		
		
		if (isset($detalles[$i]['infoAduana'])){			
			if (strlen($detalles[$i]['infoAduana'])>0){				
				$elementosDeAduana=json_decode($detalles[$i]['infoAduana'],true);		
			}	
		}
		$params=array();

		if ( !empty($detalles[$i]['componentes']) ){
			$params['componentes']=$detalles[$i]['componentes'];
		}			

		//$pdf->ImprimeDetalles($det_cantidad, $det_unidad, $det_descripc, $det_val_unit, $det_importe, $det_identif,$detalle,$subconceptos,$elementosDeAduana,$params);
		$pdf->ImprimeDetalles($detalles[$i]);
		
		
	}
	# Final del detalle
	$pdf->FinalDetalle();
	
	$arch_pdf = substr($xml_file,0,-4).".pdf";
	$pdf->Output($arch_pdf); // genera el PDF
	
	unlink($pdf->cbb);
	
	if (file_exists($arch_pdf)) 
		return $arch_pdf;
	else 
		return false;
}


function colocarCFDi($ArchivoZIP,$ArchivoXMLOrigen){
	/*DETERMINAR RFC, A�O y MES*/
    $arrArchivoZIP = explode("_", $ArchivoZIP);
    $sRFC  = $arrArchivoZIP[0];
    $sAnio = date("y");
    $sMes  = date("m");

    /* CREA LAS CARPETAS PARA COLOCAR ARCHIVOS DE CLIENTE*/
    $sRutaColocarCFDi = "CFDI/$sRFC/$sAnio/$sMes";
    rmkdir_r($sRutaColocarCFDi);
    
    if ((@copy("tmp/$ArchivoZIP","$sRutaColocarCFDi/$ArchivoZIP")) &&
        (@copy("tmp/$ArchivoXMLOrigen","$sRutaColocarCFDi/$ArchivoXMLOrigen"))) {
        
        return 1;
    } else {
        return 0;
    }


}


function agregarMarcaDeAgua($nombre_archivo = "") {
	$pdf = new FPDI_Protection('P', 'mm', 'Letter');
	

	// set the sourcefile
	$count_pages=$pdf->setSourceFile($nombre_archivo); // contamos las p?ginas del archivo en cuesti?n, ubicado en el server.

	for ($i = 1; $i <= $count_pages; $i++) {

		$pdf->AddPage();

		$x = 20;
		$y = 250;
		$angle = 55;
		//Image rotated around its upper-left corner
		$pdf->Rotate($angle, $x, $y);
		$pdf->Image("img/CFDISINVALIDEZ.jpg",$x,$y,250,20);
		$pdf->Rotate(0);

		// import page 1
		$tplIdx = $pdf->importPage($i);
		// use the imported page and place it at point 10,10 with a width of 100 mm
		$pdf->useTemplate($tplIdx);
	}

	$pdf->Output($nombre_archivo, "F");
}


function num2letras($num, $fem = false, $dec = false) { 
	$matuni[2]  = "dos"; 
	$matuni[3]  = "tres"; 
	$matuni[4]  = "cuatro"; 
	$matuni[5]  = "cinco"; 
	$matuni[6]  = "seis"; 
	$matuni[7]  = "siete"; 
	$matuni[8]  = "ocho"; 
	$matuni[9]  = "nueve"; 
	$matuni[10] = "diez"; 
	$matuni[11] = "once"; 
	$matuni[12] = "doce"; 
	$matuni[13] = "trece"; 
	$matuni[14] = "catorce"; 
	$matuni[15] = "quince"; 
	$matuni[16] = "dieciseis"; 
	$matuni[17] = "diecisiete"; 
	$matuni[18] = "dieciocho"; 
	$matuni[19] = "diecinueve"; 
	$matuni[20] = "veinte"; 
	$matunisub[2] = "dos"; 
	$matunisub[3] = "tres"; 
	$matunisub[4] = "cuatro"; 
	$matunisub[5] = "quin"; 
	$matunisub[6] = "seis"; 
	$matunisub[7] = "sete"; 
	$matunisub[8] = "ocho"; 
	$matunisub[9] = "nove"; 
	
	$matdec[2] = "veint"; 
	$matdec[3] = "treinta"; 
	$matdec[4] = "cuarenta"; 
	$matdec[5] = "cincuenta"; 
	$matdec[6] = "sesenta"; 
	$matdec[7] = "setenta"; 
	$matdec[8] = "ochenta"; 
	$matdec[9] = "noventa"; 
	$matsub[3]  = 'mill'; 
	$matsub[5]  = 'bill'; 
	$matsub[7]  = 'mill'; 
	$matsub[9]  = 'trill'; 
	$matsub[11] = 'mill'; 
	$matsub[13] = 'bill'; 
	$matsub[15] = 'mill'; 
	$matmil[4]  = 'millones'; 
	$matmil[6]  = 'billones'; 
	$matmil[7]  = 'de billones'; 
	$matmil[8]  = 'millones de billones'; 
	$matmil[10] = 'trillones'; 
	$matmil[11] = 'de trillones'; 
	$matmil[12] = 'millones de trillones'; 
	$matmil[13] = 'de trillones'; 
	$matmil[14] = 'billones de trillones'; 
	$matmil[15] = 'de billones de trillones'; 
	$matmil[16] = 'millones de billones de trillones'; 
	if ($num=='1'){
			return 'un';
	}
	if ($num=='-1'){
			return 'menos un';
	}
	if ($num>1 && $num<2){
		return 'un';
	}
	if ($num>-2 && $num<-1){
		return 'menos un';
	}
	
	$num = trim((string)@$num); 
	if ($num[0] == '-') { 
		$neg = 'menos '; 
		$num = substr($num, 1); 
	}else 
		$neg = ''; 
	while ($num[0] == '0') $num = substr($num, 1); 
	if ($num[0] < '1' or $num[0] > 9) $num = '0' . $num; 
	$zeros = true; 
	$punt = false; 
	$ent = ''; 
	$fra = ''; 
	for ($c = 0; $c < strlen($num); $c++) { 
		$n = $num[$c]; 
		if (! (strpos(".,'''", $n) === false)) { 
			if ($punt) break; 
			else{ 
				$punt = true; 
				continue; 
			} 

		}elseif (! (strpos('0123456789', $n) === false)) { 
			if ($punt) { 
				if ($n != '0') $zeros = false; 
				$fra .= $n; 
			}else 
				$ent .= $n; 
		}else 
			break; 
	} 
	$ent = '     ' . $ent; 
	if ($dec and $fra and ! $zeros) { 
		$fin = ' coma'; 
		for ($n = 0; $n < strlen($fra); $n++) { 
			if (($s = $fra[$n]) == '0') 
				$fin .= ' cero'; 
			elseif ($s == '1') 
				$fin .= $fem ? ' una' : ' un'; 
			else 
				$fin .= ' ' . $matuni[$s]; 
		} 
	}else 
		$fin = ''; 
	if ((int)$ent === 0) return 'Cero ' . $fin; 
	$tex = ''; 
	$sub = 0; 
	$mils = 0; 
	$neutro = false; 
	while ( ($num = substr($ent, -3)) != '   ') { 
		
		$ent = substr($ent, 0, -3); 
		if (++$sub < 3 and $fem) { 
			$matuni[1] = 'una'; 
			$subcent = 'as'; 
		}else{ 
			$matuni[1] = $neutro ? 'un' : 'uno'; 
			$subcent = 'os'; 
		} 
		$t = ''; 
		$n2 = substr($num, 1); 
		if ($n2 == '00') { 
		}elseif ($n2 < 21) 
			$t = ' ' . $matuni[(int)$n2]; 
		elseif ($n2 < 30) { 
			$n3 = $num[2]; 
			if ($n3 != 0) $t = 'i' . $matuni[$n3]; 
			$n2 = $num[1]; 
			$t = ' ' . $matdec[$n2] . $t; 
		}else{ 
			$n3 = $num[2]; 
			if ($n3 != 0) $t = ' y ' . $matuni[$n3]; 
			$n2 = $num[1]; 
			$t = ' ' . $matdec[$n2] . $t; 
		} 
		$n = $num[0]; 
		if ($n == 1) { 
			$t = ' ciento' . $t; 
		}elseif ($n == 5){ 
			$t = ' ' . $matunisub[$n] . 'ient' . $subcent . $t; 
		}elseif ($n != 0){ 
			$t = ' ' . $matunisub[$n] . 'cient' . $subcent . $t; 
		} 
		if ($sub == 1) { 
		}elseif (! isset($matsub[$sub])) { 
			if ($num == 1) { 
				$t = ' mil'; 
			}elseif ($num > 1){ 
				$t .= ' mil'; 
			} 
		}elseif ($num == 1) { 
			$t .= ' ' . $matsub[$sub] . UTF8_decode('Ón'); 
		}elseif ($num > 1){ 
			$t .= ' ' . $matsub[$sub] . 'ones'; 
		}   
		if ($num == '000') $mils ++; 
		elseif ($mils != 0) { 
			if (isset($matmil[$sub])) $t .= ' ' . $matmil[$sub]; 
			$mils = 0; 
		} 
		$neutro = true; 
		$tex = $t . $tex; 
	} 
	$tex = $neg . substr($tex, 1) . $fin; 
	return ucfirst($tex); 
} 
function guardarDatosFactura($xml_file, $database, $empresa){
	global $dbname;
	$dbname = $database; // cambia de BD
	if (!empty($database) && !empty($empresa)){
		require_once("class_factura.php");
		$fac = new claseFactura;
		$fac->setEmpresaSucursal($empresa);
		$res = $fac->guardaFactura($xml_file);
		//echo $fac->getSaveQuery();
		return $res;
	}
}
?>