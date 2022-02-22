/*
 * File: miErpWeb.storeGridProductos.js
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
miErpWeb.winBuscadorProductosStore = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.winBuscadorProductosStore.superclass.constructor.call(this, Ext.apply({
            storeId: 'winBuscadorProductosStore',
			idProperty: 'id_producto',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_producto'
                },
				{
                    name: 'codigo',
                    type: 'string'
                },
				{
                    name: 'codigo_barras',
                    type: 'string'
                },
				{
                    name: 'descripcion',
                    type: 'string'
                },
				{
                    name: 'stock',
                    type: 'string'
                },
				{
                    name: 'precio_venta',
                    type: 'string'
                }
			
			],
            url: 'app.php/productos/obtenerproductosbusqueda'
        }, cfg));
    }
});
Ext.reg('winBuscadorProductosStore', miErpWeb.winBuscadorProductosStore);