/*
 * File: miErpWeb.storeFormReporteExistenciaAlmacenes.js
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
miErpWeb.storeFormReporteExistenciaAlmacenes = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormReporteExistenciaAlmacenes.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormReporteExistenciaAlmacenes',
			idProperty: 'id_almacen',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_almacen'
                },
				{
                    name: 'nombre_almacen',
                    type: 'string'
                }			
			],
            url: 'app.php/productos/obteneralmacenes'
        }, cfg));
    }
});
Ext.reg('storeFormReporteExistenciaAlmacenes', miErpWeb.storeFormReporteExistenciaAlmacenes);