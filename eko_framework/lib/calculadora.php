<?php 
class Calculadora{

	//esPorcentaje: cuando es igual a true, el $descuento es tomado como porcentaje, en otro caso, como pesos
	static function  calcularConcepto($cantidad, $costoU, $descuento, $impuestos,$esPorcentaje=false){
		$importe=$cantidad * $costoU;
	
		if ($importe==0){
			$subtotal = 0;
			$descuentoPorcentaje = ($esPorcentaje==true)?  $descuento : 0;
			$descuentoPesos = 0;			
		}else if ($esPorcentaje==true){
			$descuentoPorcentaje=$descuento;
			$descuentoPesos = ($descuentoPorcentaje / 100) * $importe;
			$subtotal = $importe - $descuentoPesos;
		}else{
			$descuentoPesos = $descuento;
			$subtotal = $importe - $descuentoPesos;
			
			$descuentoPorcentaje =  ( $descuentoPesos / $importe) * 100;
		}
		
		$respuesta=array(
			'cantidad'				=>$cantidad,
			'costoU'				=>$costoU,
			'importe'				=>$importe,
			'descuentoPesos'		=>$descuentoPesos,
			'descuentoPorcentaje'	=>$descuentoPorcentaje,
			'subtotal'				=>$subtotal,			
		);
		
		#----------------------------------------------------------------------
		#					 Calculo de Impuestos
		#----------------------------------------------------------------------
		
		/*	
			Cuatro casos:
				- Sin Impuestos.
				- solo con iva. 
				- calculo del ieps.
				- calculo con retenciones.
		*/
		
		# Calculo del IEPS			
		if ( !empty($impuestos['ieps']) ){
			if ( !isset($impuestos['iva']) ){
				throw new Exception('Debe especificar la tasa de iva');
			}
			$res= Calculadora::calcularIeps($subtotal,$impuestos['iva'], $impuestos['ieps'] );
			$respuesta= array_merge($respuesta, $res);
			return $respuesta;
		}
		
		# Calculo de ISR
		if ( !empty($impuestos['isr']) ){
			
			if ( !isset($impuestos['iva']) ){
				throw new Exception('Debe especificar la tasa de iva');
			}
			$res= Calculadora::calcularIsr($subtotal,$impuestos['iva'], $impuestos['isr'] );
			$respuesta= array_merge($respuesta, $res);
			return $respuesta;
		}
		
		# Calculo de IVA
		if ( !empty($impuestos['iva']) ){
			$res= Calculadora::calcularIva($subtotal,$impuestos['iva'] );
			$respuesta= array_merge($respuesta, $res);
			return $respuesta;
		}
		
		# Sin impuestos
		$respuesta['total']=$subtotal;
		return $respuesta;		
	}
	
	static function calcularIva( $importe, $iva ){
		#-----------------------------------
		#		 Calculo del iva	
		#-----------------------------------
		$TasaIva		= $iva / 100;		
		$IvaPesos		= $importe * $TasaIva;		
		$total			= $importe + $IvaPesos;
		return array(
			'total'		=>$total,
			'impuestos'	=>array(
				'ivaPesos'			=>$IvaPesos,
				'ivaPorcentaje'		=>$iva			
			)
		);
	}
	
	static function calcularIsr($importe, $iva, $isr ){
		
		$res=Calculadora::calcularIva($importe, $iva);
		#-----------------------------------
		#		 Calculo isr	
		#-----------------------------------
		$TasaISR		= $isr / 100;
		$isrPesos		= $importe * $TasaISR;
		//$total			= $importe + $isrPesos;
		
		$ivaRet= ($res['impuestos']['ivaPesos'] / 3) * 2;		
		$ivaRetPorcentaje = $iva / 3 * 2;		
		
		$resp=array (
			'total'		=>$importe - $isrPesos - $ivaRet,
			'impuestos'	=>array(
				'ivaPesos' 			=>$res['impuestos']['ivaPesos'],
				'ivaPorcentaje' 	=>$res['impuestos']['ivaPorcentaje'],
				'isrPesos'			=>$isrPesos,
				'isrPorcentaje'		=>$isr,
				'ivaRetPesos'		=>$ivaRet,
				'ivaRetPorcentaje'	=>$ivaRetPorcentaje
				
			)
		);
				
		return $resp;
	}
	
	static function calcularIeps($importe, $iva, $ieps ){
	
		#-----------------------------------
		#		 Calculo del ieps	
		#-----------------------------------
		$tasaiEPS		= $ieps / 100;		
		$IEPSpesos		= $importe * $tasaiEPS;
		$subtotal_base	= $importe + $IEPSpesos;		
	
		#-----------------------------------
		#		 Calculo del iva	
		#-----------------------------------
		$TasaIva		= $iva / 100;		
		$IvaPesos		= $subtotal_base * $TasaIva;		
		$total			= $subtotal_base + $IvaPesos;	
		
		return array(
			'total'=>$total,
			'impuestos'=>array(
				'ivaPesos'			=>$IvaPesos,
				'ivaPorcentaje'		=>$iva,
				'iepsPesos'			=>$IEPSpesos,
				'iepsPorcentaje'	=>$ieps,				
			)	
		);
	}
}
?>