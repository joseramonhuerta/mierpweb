/*
 * File: miErpWeb.storeMovimientosAlmacenWinMovimientos.js
 * Date: Wed May 25 2011 16:58:59 GMT-0600 (Hora verano, Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeMovimientosAlmacenWinMovimientos = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeMovimientosAlmacenWinMovimientos.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeMovimientosAlmacenWinMovimientos',
			idProperty: 'id_movimiento',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_movimiento'
                },
				{
                    name: 'serie_folio',
                    type: 'string'
                },
				{
                    name: 'fecha_movimiento',
                    type: 'string'
                },
				{
                    name: 'nombre_sucursal',
                    type: 'string'
                },
				{
                    name: 'nombre_almacen_origen',
                    type: 'string'
                },
				{
                    name: 'total',
                    type: 'float'
                }
				
			],
            url: 'app.php/movimientosalmacen/obtenermovimientosbusqueda'
        }, cfg));
    }
});
Ext.reg('storeMovimientosAlmacenWinMovimientos', miErpWeb.storeMovimientosAlmacenWinMovimientos);