/*
 * File: winDescuentos.ui.js
 * Date: Tue Apr 24 2018 13:00:58 GMT-0600 (Hora verano, Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

winDescuentosUi = Ext.extend(Ext.Window, {
    title: 'Descuento General',
    width: 293,
    height: 116,
    modal: true,
    border: false,
    draggable: false,
    resizable: false,
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                padding: 10,
                border: false,
                itemId: 'formDescuento',
                ref: 'formDescuento',
                items: [
                    {
                        xtype: 'numberfield',
                        fieldLabel: 'Impote',
                        name: '',
                        allowNegative: false,
                        width: 100,
                        style: 'text-align:right;',
                        itemId: 'txtImporteDescuento',
                        ref: '../txtImporteDescuento'
                    }
                ]
            },
            {
                xtype: 'container',
                height: 10
            },
            {
                xtype: 'container',
                layout: 'hbox',
                items: [
                    {
                        xtype: 'container',
                        flex: 1,
                        width: 70
                    },
                    {
                        xtype: 'button',
                        text: 'Aceptar',
                        itemId: 'btnAceptar',
                        width: 60,
                        ref: '../btnAceptar'
                    },
                    {
                        xtype: 'container',
                        flex: 1,
                        width: 25
                    },
                    {
                        xtype: 'button',
                        text: 'Cancelar',
                        itemId: 'btnCancelar',
                        width: 60,
                        ref: '../btnCancelar'
                    }
                ]
            }
        ];
        winDescuentosUi.superclass.initComponent.call(this);
    }
});
