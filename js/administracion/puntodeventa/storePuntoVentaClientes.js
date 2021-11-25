/*
 * File: mfw.almacenes.storePuntoVentaClientes.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storePuntoVentaClientes = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storePuntoVentaClientes.superclass.constructor.call(this, Ext.apply({
            storeId: 'storePuntoVentaClientes',
			autoDestroy: true,
            idProperty: 'id_cliente',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_cliente'
                },
				{
                    name: 'nombre_cliente',
                    type: 'string'
                },
				{
                    name: 'estilista',
                    type: 'int'
                }					
			],
            url: 'app.php/ventas/obtenerclientes'
        }, cfg));
    }
});
Ext.reg('storePuntoVentaClientes', miErpWeb.storePuntoVentaClientes);