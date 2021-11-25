/*
 * File: miErpWeb.storePuntoVentaWinFormaPagosGrid.js
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
miErpWeb.storePuntoVentaWinFormaPagosGrid = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storePuntoVentaWinFormaPagosGrid.superclass.constructor.call(this, Ext.apply({
            storeId: 'storePuntoVentaWinFormaPagosGrid',
			idProperty: 'id_formapago',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_formapago'
                },
				{
                    name: 'nombre_formapago',
                    type: 'string'
                },
				{
                    name: 'tipo_formapago',
                    type: 'int'
                },
				{
                    name: 'importe',
                    type: 'float'
                }
				
			],
            // url: 'app.php/ventas/obtenerformaspagosgrid'
        }, cfg));
    }
});
Ext.reg('storePuntoVentaWinFormaPagosGrid', miErpWeb.storePuntoVentaWinFormaPagosGrid);