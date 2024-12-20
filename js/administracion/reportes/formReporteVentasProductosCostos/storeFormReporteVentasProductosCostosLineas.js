/*
 * File: mfw.almacenes.storeFormReporteVentasProductosCostosLineas.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeFormReporteVentasProductosCostosLineas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormReporteVentasProductosCostosLineas.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormReporteVentasProductosCostosLineas',
			autoDestroy: true,
            idProperty: 'id_linea',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_linea'
                },
				{
                    name: 'nombre_linea',
                    type: 'string'
                }				
			],
            url: 'app.php/ventas/obtenerlineas'
        }, cfg));
    }
});
Ext.reg('storeFormReporteVentasProductosCostosLineas', miErpWeb.storeFormReporteVentasProductosCostosLineas);
