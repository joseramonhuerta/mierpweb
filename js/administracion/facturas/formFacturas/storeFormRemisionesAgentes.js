/*
 * File: miErpWeb.storeFormRemisionesAgentes.js
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
miErpWeb.storeFormRemisionesAgentes = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormRemisionesAgentes.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormRemisionesAgentes',
			idProperty: 'id_agente',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_agente'
                },
				{
                    name: 'nombre_agente',
                    type: 'string'
                }			
			],
            url: 'app.php/remisiones/obteneragentes'
        }, cfg));
    }
});
Ext.reg('storeFormRemisionesAgentes', miErpWeb.storeFormRemisionesAgentes);