/*
 * File: miErpWeb.storePuntoVentaProductos.js
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
miErpWeb.storePuntoVentaProductos = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storePuntoVentaProductos.superclass.constructor.call(this, Ext.apply({
            storeId: 'storePuntoVentaProductos',
			idProperty: 'id_producto',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_producto'
                },
				{
                    name: 'descripcion',
                    type: 'string'
                },
				{
                    name: 'codigo_barras',
                    type: 'string'
                },
				{
                    name: 'codigo',
                    type: 'string'
                }			
			],
            url: 'app.php/ventas/obtenerproductos'
        }, cfg));
    }
});
Ext.reg('storePuntoVentaProductos', miErpWeb.storePuntoVentaProductos);