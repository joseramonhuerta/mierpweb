/*
 * File: mfw.almacenes.storeFormReporteVentasSucursales.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeFormReporteFlujoEfectivoSucursales = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormReporteFlujoEfectivoSucursales.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormReporteFlujoEfectivoSucursales',
			autoDestroy: true,
            root: 'data',
            fields:["id_sucursal","nombre_sucursal"],
            url: 'app.php/ventas/obtenersucursalesempresa'
        }, cfg));
    }
});
Ext.reg('storeFormReporteFlujoEfectivoSucursales', miErpWeb.storeFormReporteFlujoEfectivoSucursales);
