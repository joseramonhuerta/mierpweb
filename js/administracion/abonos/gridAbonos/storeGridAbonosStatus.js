/*
 * File: mfw.almacenes.storeGridAbonosStatus.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeGridAbonosStatus = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridAbonosStatus.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridAbonosStatus',
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
Ext.reg('storeGridAbonosStatus', miErpWeb.storeGridAbonosStatus);
