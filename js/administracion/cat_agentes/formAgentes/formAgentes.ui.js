/*
 * File: formAgentes.ui.js
 * Date: Fri Feb 03 2017 00:15:27 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formAgentesUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Agente',
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
                    icon: 'images/iconos/clientes_add.png',
                    ref: '../btnGuardar'
                },
                {
                    xtype: 'button',
                    text: 'Desactivar',
                    icon: 'images/iconos/clientes_todos.png',
                    disabled: true,
                    ref: '../btnDesactivar'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    text: 'Eliminar',
                    icon: 'images/iconos/clientes_delete.png',
                    disabled: true,
                    ref: '../btnEliminar'
                }
            ]
        };
        this.items = [
            {
                xtype: 'textfield',
                fieldLabel: 'Nombre Agente',
                width: 400,
                name: 'nombre_agente',
                bubbleEvents: [
                    'cambioDeNombre'
                ],
                labelStyle: 'font-weight:bold;',
                ref: 'txtNombreAgente'
            },
            {
                xtype: 'textfield',
                anchor: '100%',
                name: 'id_agente',
                bubbleEvents: [
                    'cambioDeId'
                ],
                hidden: true,
                ref: 'txtIdAgente'
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
        formAgentesUi.superclass.initComponent.call(this);
    }
});