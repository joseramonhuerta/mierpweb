/*
 * File: miErpWeb.storeFormCitasClientes.js
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
miErpWeb.storeFormCitasClientes = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormCitasClientes.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormCitasClientes',
			idProperty: 'id_cliente',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_cliente'
                },
				{
                    name: 'nombre_fiscal',
                    type: 'string'
                }			
			],
            url: 'app.php/citas/obtenerclientes'
        }, cfg));
    }
});
Ext.reg('storeFormCitasClientes', miErpWeb.storeFormCitasClientes);