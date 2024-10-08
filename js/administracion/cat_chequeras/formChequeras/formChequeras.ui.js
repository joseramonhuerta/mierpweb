/*
 * File: formChequeras.ui.js
 * Date: Sat Jan 19 2019 00:01:57 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formChequerasUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Chequeras',
    width: 820,
    height: 580,
    padding: 10,
    autoScroll: true,
    labelWidth: 120,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Guardar',
                    icon: 'images/iconos/razon_edit.png',
                    ref: '../btnGuardar'
                },
                {
                    xtype: 'button',
                    text: 'Desactivar',
                    icon: 'images/iconos/razon_red.png',
                    disabled: true,
                    ref: '../btnDesactivar'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    text: 'Eliminar',
                    icon: 'images/iconos/razon_delete.png',
                    disabled: true,
                    ref: '../btnEliminar'
                }
            ]
        };
        this.items = [
            {
                xtype: 'textfield',
                fieldLabel: 'Descripcion',
                width: 300,
                name: 'descripcion',
                allowBlank: false,
                labelStyle: 'font-weight:bold;',
                bubbleEvents: [
                    'cambioDeNombre'
                ],
                itemId: 'txtDescripcion',
                ref: 'txtDescripcion'
            },
            {
                xtype: 'textfield',
                anchor: '100%',
                name: 'id_chequera',
                hidden: true,
                bubbleEvents: [
                    'cambioDeId'
                ],
                ref: 'txtIdChequera'
            },
            {
                xtype: 'textfield',
                anchor: '100%',
                hidden: true,
                name: 'status',
                bubbleEvents: [
                    'cambioDeStatus'
                ],
                value: 'A',
                ref: 'txtStatus'
            }
        ];
        formChequerasUi.superclass.initComponent.call(this);
    }
});
