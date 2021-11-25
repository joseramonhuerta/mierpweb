/*
 * File: miErpWeb.storeGridUsuarios.js
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
miErpWeb.storeGridUsuarios = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridUsuarios.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridUsuarios',
			idProperty: 'id_usuario',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_usuario'
                },
				{
                    name: 'nombre_usuario',
                    type: 'string'
                },
				{
                    name: 'usuario',
                    type: 'string'
                },
				{
                    name: 'status'
                }
			
			],
            url: 'app.php/usuarios/obtenerusuarios'
        }, cfg));
    }
});
Ext.reg('storeGridUsuarios', miErpWeb.storeGridUsuarios);