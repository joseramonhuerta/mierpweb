/*
 * File: miErpWeb.storeGridMovimientosAlmacen.js
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
miErpWeb.storeGridMovimientosAlmacen = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridMovimientosAlmacen.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridMovimientosAlmacen',
			idProperty: 'id_movimiento',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_movimiento'
                },
				{
                    name: 'tipo_movimiento',
                    type: 'string'
                },
				{
                    name: 'serie_folio',
                    type: 'string'
                },
				{
                    name: 'concepto_movimiento',
                    type: 'string'
                },
				{
                    name: 'fecha_movimiento',
                    type: 'string'
                },
				{
                    name: 'almacen_origen',
                    type: 'string'
                },
				{
                    name: 'almacen_destino',
                    type: 'string'
                },
				{
                    name: 'total',
                    type: 'string'
                },
				{
                    name: 'status'
                }
			
			],
            url: 'app.php/movimientosalmacen/obtenermovimientos'
        }, cfg));
    }
});
Ext.reg('storeGridMovimientosAlmacen', miErpWeb.storeGridMovimientosAlmacen);