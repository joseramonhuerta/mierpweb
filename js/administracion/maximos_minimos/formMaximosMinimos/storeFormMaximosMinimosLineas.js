/*
 * File: miErpWeb.storeFormMaximosMinimosLineas.js
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
miErpWeb.storeFormMaximosMinimosLineas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormMaximosMinimosLineas.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormMaximosMinimosLineas',
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
            url: 'app.php/productos/obtenerlineas'
        }, cfg));
    }
});
Ext.reg('storeFormMaximosMinimosLineas', miErpWeb.storeFormMaximosMinimosLineas);