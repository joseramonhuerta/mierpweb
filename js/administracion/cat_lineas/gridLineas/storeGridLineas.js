/*
 * File: miErpWeb.storeGridLineas.js
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
miErpWeb.storeGridLineas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridLineas.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridLineas',
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
                },
				{
                    name: 'status'
                }
			
			],
            url: 'app.php/lineas/obtenerlineas'
        }, cfg));
    }
});
Ext.reg('storeGridLineas', miErpWeb.storeGridLineas);