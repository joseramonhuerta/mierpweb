/*
 * File: miErpWeb.storeFormReporteCarteraClientesAgentes.js
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
miErpWeb.storeFormReporteCarteraClientesAgentes = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormReporteCarteraClientesAgentes.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormReporteCarteraClientesAgentes',
			autoDestroy: true,
			idProperty: 'id_agente',
			messageProperty: 'msg',
			url: 'app.php/agentes/obteneragentes',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[{
						name: 'id_agente',
						type: 'int'
					},{
						name: 'nombre_agente',
						type: 'string'
					}
			]
			}, cfg));
    }
});
Ext.reg('storeFormReporteCarteraClientesAgentes', miErpWeb.storeFormReporteCarteraClientesAgentes);