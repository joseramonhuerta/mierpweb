/*
 * File: miErpWeb.storeGridClientes.js
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
miErpWeb.storeGridClientes = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeGridClientes.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeGridClientes',
			idProperty: 'id_cliente',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_cliente'
                },
				{
                    name: 'nombre_fiscal',
                    type: 'string'
                },
				{
                    name: 'rfc_cliente',
                    type: 'string'
                },
				{
                    name: 'tipo_cliente',
                    type: 'string'
                },
				{
                    name: 'nombre_comercial',
                    type: 'string'
                },
				{
                    name: 'nombre_contacto',
                    type: 'string'
                },
				{
                    name: 'email_contacto',
                    type: 'string'
                },
				{
                    name: 'telefono_contacto',
                    type: 'string'
                },
				{
                    name: 'celular_contacto',
                    type: 'string'
                },
				{
                    name: 'status'
                },
                {
                    name: 'nombre_categoria',
                    type: 'string'
                }
			
			],
            url: 'app.php/clientes/obtenerclientes'
        }, cfg));
    }
});
Ext.reg('storeGridClientes', miErpWeb.storeGridClientes);//new miErpWeb.storeGridClientes();