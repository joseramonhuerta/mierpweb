/*
 * File: formReportePedidoSugerido.ui.js
 * Date: Sat Mar 09 2019 16:13:23 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formReportePedidoSugeridoUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Pedido Sugerido',
    width: 717,
    height: 465,
    padding: 10,
    autoScroll: true,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'splitbutton',
                    text: 'Imprimir',
                    icon: 'images/iconos/bullet_printer.png',
                    ref: '../btnEjecutar',
                    menu: {
                        xtype: 'menu',
                        items: [
                            {
                                xtype: 'menuitem',
                                text: 'Excel',
                                itemId: 'btnExcel',
                                icon: 'images/iconos/excel.png',
                                ref: '../../../btnExcel'
                            },
                            {
                                xtype: 'menuitem',
                                text: 'PDF',
                                itemId: 'btnPDF',
                                icon: 'images/iconos/pdf.png',
                                ref: '../../../btnPDF'
                            }
                        ]
                    }
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
                ref: 'cmbSucursal'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Linea',
                width: 400,
                itemId: 'cmbLinea',
                name: 'id_linea',
                displayField: 'nombre_linea',
                valueField: 'id_linea',
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
                ref: 'cmbLinea'
            },
            { 
                xtype: 'checkbox', //defining the type of component
                fieldLabel: 'Productos TOP',//assigning a label
                name: 'chkProductosTop', //and a "name" so we can retrieve it in the server... 
                ref: 'chkProductosTop'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Tipo Reporte',
                width: 170,
                valueField: 'id',
                displayField: 'nombre',
                name: 'tipo',
                itemId: 'cmbTipo',
                allowBlank: false,
                forceSelection: true,
                triggerAction: 'all',
                editable: false,
                mode: 'local',
                hiddenName: 'tipo',
                ref: 'cmbTipo'
            } 
        ];
        formReportePedidoSugeridoUi.superclass.initComponent.call(this);
    }
});
