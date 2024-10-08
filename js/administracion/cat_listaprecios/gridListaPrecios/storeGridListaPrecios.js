/*
 * File: miErpWeb.storeGridListaPrecios.js
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
miErpWeb.storeGridListaPrecios = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridListaPrecios.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridListaPrecios',
			idProperty: 'id_listaprecio',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_listaprecio'
                },
				{
                    name: 'descripcion',
                    type: 'string'
                },
				{
                    name: 'status'
                }
			
			],
            url: 'app.php/listaprecios/obtenerlistaprecios'
        }, cfg));
    }
});
Ext.reg('storeGridListaPrecios', miErpWeb.storeGridListaPrecios);