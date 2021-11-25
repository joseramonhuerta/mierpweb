/*
 * File: formReporteVentasClientes.ui.js
 * Date: Sat Mar 09 2019 16:08:03 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formReporteVentasClientesUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Reporte Ventas Clientes',
    width: 717,
    height: 465,
    padding: 10,
    autoScroll: true,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Ejecutar',
                    icon: 'images/iconos/pdf.png',
                    ref: '../btnEjecutar'
                }
            ]
        };
        this.items = [
            {
                xtype: 'datefield',
                fieldLabel: 'Fecha Inicio',
                itemId: 'txtFechaInicio',
                name: 'FechaInicio',
                allowBlank: false,
                labelStyle: 'font-weight:bold',
                ref: 'txtFechaInicio'
            },
            {
                xtype: 'datefield',
                fieldLabel: 'Fecha Fin',
                itemId: 'txtFechaFin',
                allowBlank: false,
                name: 'FechaFin',
                labelStyle: 'font-weight:bold',
                ref: 'txtFechaFin'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Vendedor',
                displayField: 'nombre_agente',
                valueField: 'id_agente',
                itemId: 'cmbAgente',
                name: 'id_agente',
                triggerAction: 'all',
                editable: false,
                width: 300,
                forceSelection: true,
                triggerConfig: {
                    tag: 'span',
                    cls: 'x-form-twin-triggers',
                    style: 'padding-right:2px',
                    cn: [
                        {
                            tag: "img",
                            src: Ext.BLANK_IMAGE_URL,
                            cls: "x-form-trigger x-form-clear-trigger"
                        },
                        {
                            tag: "img",
                            src: Ext.BLANK_IMAGE_URL,
                            cls: "x-form-trigger x-form-search-trigger"
                        }
                    ]
                },
                ref: 'cmbAgente'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Cliente',
                width: 300,
                itemId: 'cmbCliente',
                name: 'id_cliente',
                displayField: 'nombre_fiscal',
                valueField: 'id_cliente',
                enableKeyEvents: true,
                pageSize: 20,
                triggerAction: 'all',
                hiddenName: 'id_cliente',
                minChars: 0,
                triggerConfig: {
                    tag: 'span',
                    cls: 'x-form-twin-triggers',
                    style: 'padding-right:2px',
                    cn: [
                        {
                            tag: "img",
                            src: Ext.BLANK_IMAGE_URL,
                            cls: "x-form-trigger x-form-clear-trigger"
                        },
                        {
                            tag: "img",
                            src: Ext.BLANK_IMAGE_URL,
                            cls: "x-form-trigger x-form-search-trigger"
                        }
                    ]
                },
                ref: 'cmbCliente'
            }
        ];
        formReporteVentasClientesUi.superclass.initComponent.call(this);
    }
});
