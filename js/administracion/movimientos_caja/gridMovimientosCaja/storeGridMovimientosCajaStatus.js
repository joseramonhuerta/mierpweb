/*
 * File: mfw.almacenes.storeGridMovimientosCajaStatus.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeGridMovimientosCajaStatus = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridMovimientosCajaStatus.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridMovimientosCajaStatus',
            root: 'data',
			// autoLoad: false,
            fields: [
                {
                    name: 'id',
					 type: 'string'
                },
                {
                    name: 'nombre',
                    type: 'string'
                }
            ]
        }, cfg));
    }
});
Ext.reg('storeGridMovimientosCajaStatus', miErpWeb.storeGridMovimientosCajaStatus);