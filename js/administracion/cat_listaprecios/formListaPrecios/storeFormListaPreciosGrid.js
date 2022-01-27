/*
 * File: miErpWeb.storeFormCortesGrid.js
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
miErpWeb.storeFormCortesGrid = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormCortesGrid.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormCortesGrid',
			idProperty: 'id_corte_liquidacion',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_corte_liquidacion'
                },
				{
                    name: 'id_formapago'
                },
				{
                    name: 'id_denominacion'
                },
				{
                    name: 'nombre_formapago',
                    type: 'string'
                },
				{
                    name: 'denominacion',
                    type: 'string'
                },
				{
                    name: 'cantidad',
                    type: 'float'
                },
				{
                    name: 'total',
                    type: 'float'
                }
				
			],
            // url: 'app.php/ventas/obtenerformaspagosgrid'
        }, cfg));
    }
});
Ext.reg('storeFormCortesGrid', miErpWeb.storeFormCortesGrid);