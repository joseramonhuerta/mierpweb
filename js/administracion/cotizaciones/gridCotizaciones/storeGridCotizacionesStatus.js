/*
 * File: mfw.almacenes.storeGridCotizacionesStatus.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeGridCotizacionesStatus = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridCotizacionesStatus.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridCotizacionesStatus',
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
Ext.reg('storeGridCotizacionesStatus', miErpWeb.storeGridCotizacionesStatus);
