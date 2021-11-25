/*
 * File: miErpWeb.storeGridUnidadesMedidas.js
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
miErpWeb.storeGridUnidadesMedidas = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridUnidadesMedidas.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridUnidadesMedidas',
			idProperty: 'id_unidadmedida',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_unidadmedida'
                },
				{
                    name: 'codigo_unidad',
                    type: 'string'
                },
				{
                    name: 'descripcion_unidad',
                    type: 'string'
                },
				{
                    name: 'status'
                }
			
			],
            url: 'app.php/unidades/obtenerunidades'
        }, cfg));
    }
});
Ext.reg('storeGridUnidadesMedidas', miErpWeb.storeGridUnidadesMedidas);