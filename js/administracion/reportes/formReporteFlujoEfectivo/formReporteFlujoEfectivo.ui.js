/*
 * File: formReporteFlujoEfectivo.ui.js
 * Date: Sun Jul 14 2019 01:44:09 GMT-0600 (Mountain Daylight Time (Mexico))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formReporteFlujoEfectivoUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Reporte Flujo de Efectivo',
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
                fieldLabel: 'Empresa',
                width: 400,
                itemId: 'cmbEmpresa',
                emptyText: 'Todas',
                name: 'id_empresa',
                displayField: 'nombre_fiscal',
                valueField: 'id_empresa',
                enableKeyEvents: true,
                pageSize: 20,
                triggerAction: 'all',
                hiddenName: 'id_empresa',
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
                allowBlank: true,
                labelStyle: 'font-weight:bold',
                ref: 'cmbEmpresa'
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
                emptyText: 'Todas',
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
                allowBlank: true,
                labelStyle: 'font-weight:bold',
                ref: 'cmbSucursal'
            },
            { 
                xtype: 'checkbox', //defining the type of component
                fieldLabel: 'Reporte Perdidas y ganancias',//assigning a label
                name: 'chkPerdidasGanancias', //and a "name" so we can retrieve it in the server... 
                ref: 'chkPerdidasGanancias'
            } 
        ];
        formReporteFlujoEfectivoUi.superclass.initComponent.call(this);
    }
});
