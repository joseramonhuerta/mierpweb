/*
 * File: mfw.almacenes.storeFormReporteMovimientosBancosSucursales.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeFormReporteMovimientosBancosSucursales = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormReporteMovimientosBancosSucursales.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormReporteMovimientosBancosSucursales',
			autoDestroy: true,
            idProperty: 'id_sucursal',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_sucursal'
                },
				{
                    name: 'nombre_sucursal',
                    type: 'string'
                },
				{
                    name: 'id_empresa'
                },
				{
                    name: 'nombre_empresa',
                    type: 'string'
                }	
			],
            url: 'app.php/movimientosbanco/obtenersucursales'
        }, cfg));
    }
});
Ext.reg('storeFormReporteMovimientosBancosSucursales', miErpWeb.storeFormReporteMovimientosBancosSucursales);
