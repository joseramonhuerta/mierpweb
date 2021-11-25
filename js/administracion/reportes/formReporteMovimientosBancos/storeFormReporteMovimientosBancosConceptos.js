/*
 * File: mfw.almacenes.storeFormReporteMovimientosBancosConceptos.js
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

Ext.ns('miErpWeb');
miErpWeb.storeFormReporteMovimientosBancosConceptos = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormReporteMovimientosBancosConceptos.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormReporteMovimientosBancosConceptos',
			autoDestroy: true,
            idProperty: 'id_concepto',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_concepto'
                },
				{
                    name: 'descripcion',
                    type: 'string'
                },
				{
                    name: 'tipo',
                    type: 'string'
                }	
			],
            url: 'app.php/movimientosbanco/obtenerconceptosreporte'
        }, cfg));
    }
});
Ext.reg('storeFormReporteMovimientosBancosConceptos', miErpWeb.storeFormReporteMovimientosBancosConceptos);
