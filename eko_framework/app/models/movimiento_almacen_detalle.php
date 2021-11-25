<?php

class MovimientoAlmacenDetalleModel extends Model{
    var $useTable = 'movimientos_almacen_detalles';
    var $name='MovimientoAlmacenDetalle';
    var $primaryKey = 'id_movimiento_detalle';
    var $select = array(
        array('id_movimiento_detalle' => 'id_movimiento_detalle'),
        array('id_movimiento' => 'id_movimiento'),
        array('id_producto' => 'id_producto'),
        
        array('cantidad' => 'cantidad'),
        array('costo' => 'costo'),
        array('importe' => 'importe'),
        array('descuento' => 'descuento'),
        array('subtotal' => 'subtotal'),
        array('impuestos' => 'impuestos'),
        array('total' => 'total')
    );
    
   
  public readAll($start, $limit, $filtro, $params, $usarAlias){
      return parent::readAll( $start, $limit, $filtro, $params, $usarAlias );	  
  } 
    



}    