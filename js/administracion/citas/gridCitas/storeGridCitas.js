/*
 * File: miErpWeb.storeGridCitas.js
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
miErpWeb.storeGridCitas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridCitas.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridCitas',
			idProperty: 'id_cita',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_cita'
                },
				{
                    name: 'horario',
                    type: 'string'
                },
				{
                    name: 'nombre_agente',
                    type: 'string'
                },
				{
                    name: 'observaciones',
                    type: 'string'
                },
				{
                    name: 'fecha',
                    type: 'string'
                },
				{
                    name: 'nombre_fiscal',
                    type: 'string'
                },
				{
                    name: 'status'
                }
			
			],
            url: 'app.php/citas/obtenercitas'
        }, cfg));
    }
});
Ext.reg('storeGridCitas', miErpWeb.storeGridCitas);