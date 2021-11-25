/*
 * File: formReporteVentas.ui.js
 * Date: Sun Jul 14 2019 01:44:09 GMT-0600 (Mountain Daylight Time (Mexico))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formReporteVentasUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Reporte Ventas',
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
                labelStyle: 'font-weight:bold',
                ref: 'txtFechaInicio'
            },
            {
                xtype: 'datefield',
                fieldLabel: 'Fecha Fin',
                itemId: 'txtFechaFin',
                name: 'FechaFin',
                labelStyle: 'font-weight:bold',
                ref: 'txtFechaFin'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Sucursal',
                width: 400,
                itemId: 'cmbSucursal',
                name: 'id_sucursal',
                displayField: 'nombre_sucursal',
                valueField: 'id_sucursal',
                enableKeyEvents: true,
                pageSize: 20,
                triggerAction: 'all',
                hiddenName: 'id_sucursal',
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
                allowBlank: false,
                labelStyle: 'font-weight:bold',
                ref: 'cmbSucursal'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Cliente',
                width: 400,
                itemId: 'cmbCliente',
                name: 'id_cliente',
                displayField: 'nombre_cliente',
                valueField: 'id_cliente',
                enableKeyEvents: true,
                pageSize: 20,
                triggerAction: 'all',
                hiddenName: 'id_linea',
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
            },
            {
                xtype: 'combo',
                fieldLabel: 'Vendedor',
                width: 400,
                itemId: 'cmbAgente',
                name: 'id_agente',
                displayField: 'nombre_agente',
                valueField: 'id_agente',
                enableKeyEvents: true,
                pageSize: 20,
                triggerAction: 'all',
                hiddenName: 'id_agente',
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
                ref: 'cmbAgente'
            }
        ];
        formReporteVentasUi.superclass.initComponent.call(this);
    }
});