/*
 * File: miErpWeb.storeFormCitasHorarios.js
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
miErpWeb.storeFormCitasHorarios = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormCitasHorarios.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormCitasHorarios',
			idProperty: 'id_horario',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_horario'
                },
				{
                    name: 'descripcion_horario',
                    type: 'string'
                }			
			],
            url: 'app.php/citas/obtenerhorarios'
        }, cfg));
    }
});
Ext.reg('storeFormCitasHorarios', miErpWeb.storeFormCitasHorarios);