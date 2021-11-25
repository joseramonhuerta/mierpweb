<?php
class Stock extends Model{
	//Esta clase es un acercamiento (aunque muy lejano)a un ORM tipo Propel
	var $useTable = 'cat_productos_stocks';
    var $name='Stock';
    var $primaryKey = 'id_stock';
	
	function save($params){
		return parent::save($params);
	}
	
	function entrada($almacenId,$productoId,$cantidad){
		$stock=$this->getRegistroDestino($almacenId,$productoId);
		$existenciaActual=floatval($stock['stock']);
		$stock['stock']=$existenciaActual + $cantidad;
		$this->save($stock);
		return true;
	}
		
	function salida($almacenId,$productoId,$cantidad){
		$stock=$this->getRegistroDestino($almacenId,$productoId);
		$existenciaActual=floatval($stock['stock']);
		$stock['stock']=$existenciaActual - $cantidad;
		$this->save($stock);
		return true;
	}
	
	public function getRegistroDestino($almacenId,$productoId){
		$sqlSelStock="SELECT id_stock,stock
		FROM cat_productos_stocks     			
    	WHERE id_almacen=$almacenId AND id_producto=$productoId;";    
    	$stockData=$this->select($sqlSelStock);	
		if ( empty($stockData) ){
			return array(
				'id_almacen'	=>$almacenId,
				'id_producto'	=>$productoId,
				'stock'		=>0
			);
		}else{
			return $stockData[0];
		}
	}
	
	
	
	
	
	
}