/*
 * File: formMovimientosCaja.ui.js
 * Date: Mon Nov 13 2017 13:41:16 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formMovimientosCajaUi = Ext.extend(Ext.Panel, {
    title: 'Movimiento de Caja',
    autoScroll: true,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Guardar',
                    icon: 'images/iconos/facturas_add.png',
                    itemId: 'btnGuardar',
                    ref: '../btnGuardar'
                },
                {
                    xtype: 'button',
                    text: 'Imprimir',
                    height: 22,
                    itemId: 'btnImprimir',
                    icon: 'images/iconos/pdf.png',
                    disabled: true,
                    ref: '../btnImprimir'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    text: 'Eliminar',
                    disabled: true,
                    itemId: 'btnEliminar',
                    icon: 'images/iconos/facturas_delete.png',
                    ref: '../btnEliminar'
                }
            ]
        };
        this.items = [
            {
                xtype: 'form',
                itemId: 'frmMain',
                padding: 10,
                border: false,
                ref: 'frmMain',
                items: [
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'form',
                                flex: 1,
                                width: 220,
                                items: [
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: 'Fecha',
                                        width: 100,
                                        itemId: 'txtFecha',
                                        name: 'fecha',
                                        labelStyle: 'font-weight:bold',
                                        ref: '../../../txtFecha'
                                    }
                                ]
                            },
                            {
                                xtype: 'container',
                                layout: 'form',
                                labelWidth: 50,
                                flex: 1,
                                items: [
                                    {
                                        xtype: 'timefield',
                                        fieldLabel: 'Hora',
                                        width: 100,
                                        disabled: true,
                                        name: 'hora',
                                        itemId: 'txtHora',
                                        format: 'g:i:s A',
                                        ref: '../../../txtHora'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: 'Tipo',
                        displayField: 'nombre',
                        valueField: 'id',
                        itemId: 'cmbTipo',
                        name: 'tipo',
                        width: 150,
                        labelStyle: 'font-weight:bold',
                        mode: 'local',
                        triggerAction: 'all',
                        forceSelection: true,
                        allowBlank: false,
                        hiddenName: 'tipo',
                        ref: '../cmbTipo'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Concepto',
                        itemId: 'txtConcepto',
                        name: 'concepto',
                        width: 350,
                        labelStyle: 'font-weight:bold',
                        submitValue: false,
                        ref: '../txtConcepto'
                    },
                    {
                        xtype: 'numberfield',
                        fieldLabel: 'Importe',
                        itemId: 'txtTotal',
                        name: 'total',
                        width: 100,
                        labelStyle: 'font-weight:bold',
                        allowNegative: false,
                        style: 'text-align:right;',
                        ref: '../txtTotal'
                    },
                    {
                        xtype: 'textfield',
                        itemId: 'id_movimiento_caja',
                        hidden: true,
                        hideLabel: true,
                        submitValue: false,
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: '',
                        bubbleEvents: [
                            'cambioDeId'
                        ],
                        ref: '../txtIdMovimientoCaja'
                    },
                    {
                        xtype: 'textfield',
                        hidden: true,
                        hideLabel: true,
                        submitValue: false,
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: 'status',
                        value: 'A',
                        ref: '../txtStatus'
                    }
                ]
            }
        ];
        formMovimientosCajaUi.superclass.initComponent.call(this);
    }
});
