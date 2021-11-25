/*
 * File: miErpWeb.storePuntoVentaWinVentas.js
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
miErpWeb.storePuntoVentaWinVentas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storePuntoVentaWinVentas.superclass.constructor.call(this, Ext.apply({
            storeId: 'storePuntoVentaWinVentas',
			idProperty: 'id_venta',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_venta'
                },
				{
                    name: 'serie_folio',
                    type: 'string'
                },
				{
                    name: 'fecha_venta',
                    type: 'string'
                },
				{
                    name: 'nombre_cliente',
                    type: 'string'
                },
				{
                    name: 'total_venta',
                    type: 'float'
                }
				
			],
            url: 'app.php/ventas/obtenerventasbusqueda'
        }, cfg));
    }
});
Ext.reg('storePuntoVentaWinVentas', miErpWeb.storePuntoVentaWinVentas);