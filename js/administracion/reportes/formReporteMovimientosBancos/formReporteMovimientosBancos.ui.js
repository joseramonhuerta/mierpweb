/*
 * File: formReporteMovimientosBancos.ui.js
 * Date: Sat Mar 09 2019 16:15:57 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formReporteMovimientosBancosUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Reporte Movimientos Bancos',
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
                    icon: 'images/iconos/excel.png',
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
                name: 'FechaFin',
                allowBlank: false,
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
                ref: 'cmbSucursal'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Concepto',
                width: 400,
                itemId: 'cmbConcepto',
                name: 'id_concepto',
                displayField: 'descripcion',
                valueField: 'id_concepto',
                enableKeyEvents: true,
                pageSize: 20,
                triggerAction: 'all',
                hiddenName: 'id_concepto',
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
                ref: 'cmbConcepto'
            }
        ];
        formReporteMovimientosBancosUi.superclass.initComponent.call(this);
    }
});
