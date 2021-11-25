<?php
require ('../init_tests.php');
require ('../../models/inventario_movimiento_model.php'); 
require ('../../models/Producto.php');
require ('../../models/Stock.php');
require ('../../models/Almacen.php');
require ('../../models/inventario_detalle_model.php');


$invTest=new InventariosTest();
$invTest->runTests();

class InventariosTest {

	function runTests(){
		/*
		
		Datos iniciales:
		Probar que exista un producto llamado "Producto Test" (PT);		
		Probar que existe el almacen A (AA)

		Test 0: Comprobar que tenga existencia 0 para todos los almacenes		
		Consultar la existencia del Producto Test y comprobar que existen 0 unidades
		Consultar el stock del almacen y comprobar que existen 0 unidades
		consultar el costo del PA y comprobar que son $0.
		consultar el costo del Stock en AA y comprobar que son $0
		
		*/
		
		$iniciales=array('titulo'=>'Comprobar datos iniciales','datos'=>array());
		//===================================================================================
		// Probar que existe el ALMACEN PRUEBAS		
		$mAlmacen=new Almacen();
		$arrAlmacen=$mAlmacen->readAll($start=0, $limit=100, $filtro='ALMACEN PRUEBAS');
		if( !empty($arrAlmacen['data']) && $arrAlmacen['data'][0]['descripcion']=='ALMACEN PRUEBAS'){
			$almacenId= $arrAlmacen['data'][0]['idAlm'];
			$res=true;
		}else{
			$res=false;
		}
		$iniciales['datos'][]=array('msg'=>'Probar que existe el ALMACEN PRUEBAS','res'=>$res);
		
		//===================================================================================
		$prodMod=new ProductoModel();
		
		//	Comprobar que tenga existencia 0 para todos los almacenes				
		$prodId=1;
		$prodObj=$prodMod->getById($prodId);
		
		$prodName=$prodObj['Producto']['DescProd'];
		if (is_array($prodObj) &&  isset( $prodObj['Producto'] ) && isset( $prodObj['Producto']['existenciaProd'] ) && $prodObj['Producto']['existenciaProd']==0){			
			$res=true;
		}else{
			$res=false;			
		}
		
		$iniciales['datos'][]=array('msg'=>'Obtener producto ID=1 y Comprobar existencia = 0 ('.$prodObj['Producto']['DescProd'].')','res'=>$res);
		
		//===================================================================================
		//Consultar el stock del almacen y comprobar que existen 0 unidades
		$stockMod=new Stock();
		$arrRes=$stockMod->getStocks($start=0, $limit=0, $IDValue=1,$idUser=0,$rolUser=1);
		
		if (empty($arrRes['data'])){
			$res=true;
		}else{
			$res=false;
		}
		$iniciales['datos'][]=array('msg'=>'Comprobar stock = 0','res'=>$res);
		
		//===================================================================================
		//echo "Pruebas de inventarios: ";
		$comprasTest=$this->comprasTest($almacenId,$prodId,$prodName);		
		$salidasTest=$this->salidasTest();
		//$this->ajustesTest();//Ajustes por entrada y por salida
		//$this->devolucionesTest();
		//$this->traspasosTest();
		echo "<pre>"; 
		print_r($iniciales);		
		print_r($comprasTest);		
		print_r($salidasTest);		
		echo "</pre>";
	}
	
	function comprasTest($almacenId,$prodId, $prodName){
		$pruebas=array('titulo'=>'Pruebas de Movimientos de compras','data'=>array());
		/*

		Realizar una compra:
			fecha	: 01-ene-2012 11:00 AM,  
			almacen	: ALMACEN PRUEBAS
			producto: Producto test.
			cantidad: 10
			costo u : $100		
			
Test 1:	Consultar a las 12:00 PM.	
		Consultar la existencia del producto y comprobar que existen 10 unidades
		Consultar el stock del almacen y comprobar que existen 10 unidades
		consultar el costo U = $100.
		consultar el costo del PA y comprobar que son $1000.
		consultar el costo del Stock en AA y comprobar que son $1000
		
Test 2:	Consultar a las 10:00 PM.		
		Esperar los mismos resultados que el test 0.
		
    	Realizar  compra de PA.
			fecha	: 02-ene-2012 11:00 AM,  
			almacen	: almacen A 
			producto: Producto test.
			cantidad: 15
			costo u : $90
			
Test 3: Realizar pruebas con fecha 02-ene-2012 a las 12:00 PM.	
		comprobar, existencia = 25
		comprobar, stock = 25
		comprobar costo del producto = $2350
		comprobar costo u = $94
		consultar costo del Stock en AA y comprobar que son $2350
		
Test 4: Realizar pruebas con fecha 02-ene-2012 a las 10:00 PM.	
		Esperar los mismos resultados que el Test 1
		
		*/
		$prueba=array(
			'titulo'=>'Realizar una compra',
			'params'=>array(
				array('param'=>'fecha','value'=>'01-ene-2012 11:00 AM'),
				array('param'=>'almacen','value'=>'('.$almacenId.') ALMACEN PRUEBAS'),
				array('param'=>'Producto','value'=>"($prodId) $prodName"),
				array('param'=>'Cantidad','value'=>10),
				array('param'=>'costo u','value'=>100)
			)
		);
		$pruebas['data'][]=$prueba;
		$invMod=new InventarioMovimientoModel();
		$params=array(
			'IDInv'				=>0,
			'KEYMovimientoInv'  =>15,
			'ReferenciaInv'	    =>'TEST 1',
			'KEYAlmOrigenInv'   =>$almacenId,
			'KEYAlmDestinoInv'  =>0,
			'FechaInv'			=>'2012-01-01 11:00:00',
			'StatusInv'			=>'A',
			'ConceptoInv'		=>'TEST',
			'TImporteInv'		=>'100',
			'TDescuentoInv'		=>'0',
			'TSubTotalInv'		=>'100',
			'TImpuestoInv'		=>0,
			'TTotalInv'			=>100
		);    	
		$arrMov=$invMod->save($params);
		return $pruebas;
	}
	
	function salidasTest(){
		$pruebas=array('titulo'=>'Pruebas de Movimientos de Salida:','data'=>array());
		$prueba=array(
			'titulo'=>'Realizar  salida del Producto',
			'params'=>array(
				array('param'=>'fecha','value'=>'01-ene-2012 12:00 PM'),
				array('param'=>'almacen','value'=>'(12) ALMACEN PRUEBAS')
			)
		);
		$pruebas['data'][]=$prueba;
	/*
		Realizar  salida del Producto PA.
			fecha	: 01-ene-2012 12:00 PM,  
			almacen	: almacen A 
			producto: Producto test.
			cantidad: 5.

Test 1: Realizar consulta con fecha 01-ene-2012 a las 12:05 PM.
		comprobar, existencia = 5
		comprobar, stock = 5
		comprobar, costo U del producto = $100
		comprobar, costo Total del producto = $500
		consultar, costo del Stock en AA y comprobar que son $500

Test 2: Realizar consulta con fecha 02-ene-2012 a las 11:05 PM.
		comprobar, existencia = 20
		comprobar, stock = 20
		comprobar, costo U del producto = $94
		comprobar, costo Total del producto = $1880
		consultar, costo del Stock en AA y comprobar que son $1880
		
	*/
		return $pruebas;
	}
	
}