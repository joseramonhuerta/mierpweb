/*
 * File: formGastos.ui.js
 * Date: Sat Jan 19 2019 14:23:32 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formGastosUi = Ext.extend(Ext.Panel, {
    title: 'Gastos',
    autoScroll: true,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Guardar',
                    itemId: 'btnGuardar',
                    icon: 'images/iconos/facturas_add.png',
                    ref: '../btnGuardar'
                },
                {
                    xtype: 'button',
                    text: 'Imprimir',
                    itemId: 'btnImprimir',
                    icon: 'images/iconos/pdf.png',
                    disabled: true,
                    height: 22,
                    ref: '../btnImprimir'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    text: 'Eliminar',
                    itemId: 'btnEliminar',
                    icon: 'images/iconos/facturas_delete.png',
                    disabled: true,
                    ref: '../btnEliminar'
                }
            ]
        };
        this.items = [
            {
                xtype: 'form',
                padding: 10,
                border: false,
                itemId: 'frmMain',
                ref: 'frmMain',
                items: [
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'container',
                                width: 790,
                                items: [
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        height: 30,
                                        width: 780,
                                        items: [
                                            {
                                                xtype: 'container',
                                                flex: 1,
                                                layout: 'form',
                                                width: 250,
                                                labelWidth: 120,
                                                items: [
                                                    {
                                                        xtype: 'combo',
                                                        fieldLabel: 'Serie',
                                                        width: 120,
                                                        displayField: 'nombre_serie',
                                                        valueField: 'id_serie',
                                                        name: 'id_serie',
                                                        itemId: 'cmbSerie',
                                                        forceSelection: true,
                                                        editable: false,
                                                        triggerAction: 'all',
                                                        hiddenValue: 'id_serie',
                                                        allowBlank: false,
                                                        ref: '../../../../../cmbSerie'
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'container',
                                                flex: 1,
                                                layout: 'form',
                                                labelWidth: 40,
                                                width: 150,
                                                items: [
                                                    {
                                                        xtype: 'textfield',
                                                        fieldLabel: 'Folio',
                                                        width: 100,
                                                        itemId: 'txtFolio',
                                                        name: 'folio',
                                                        style: 'text-align:right;',
                                                        readOnly: true,
                                                        submitValue: false,
                                                        allowBlank: false,
                                                        ref: '../../../../../txtFolio'
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'container',
                                                flex: 1,
                                                layout: 'form',
                                                labelWidth: 40,
                                                width: 150,
                                                items: [
                                                    {
                                                        xtype: 'datefield',
                                                        fieldLabel: 'Fecha',
                                                        width: 100,
                                                        itemId: 'txtFecha',
                                                        name: 'fecha',
                                                        submitValue: false,
                                                        ref: '../../../../../txtFecha'
                                                    }
                                                ]
                                            },
                                            {
                                                xtype: 'container',
                                                flex: 1,
                                                layout: 'form',
                                                labelWidth: 35,
                                                width: 200,
                                                items: [
                                                    {
                                                        xtype: 'timefield',
                                                        fieldLabel: 'Hora',
                                                        width: 100,
                                                        itemId: 'txtHora',
                                                        name: 'hora',
                                                        format: 'g:i:s A',
                                                        ref: '../../../../../txtHora'
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'form',
                                        labelWidth: 120,
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                fieldLabel: 'Observaciones',
                                                itemId: 'txtObservaciones',
                                                name: 'observaciones',
                                                width: 400,
                                                ref: '../../../../txtObservaciones'
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'form',
                                        height: 30,
                                        labelWidth: 120,
                                        items: [
                                            {
                                                xtype: 'combo',
                                                fieldLabel: 'Tipo Movimiento',
                                                itemId: 'cmbTipoMovimiento',
                                                name: 'tipo_movimiento',
                                                labelStyle: 'font-weight:bold',
                                                displayField: 'nombre',
                                                valueField: 'id',
                                                mode: 'local',
                                                triggerAction: 'all',
                                                forceSelection: true,
                                                editable: false,
                                                allowBlank: false,
                                                disabled: true,
                                                ref: '../../../../cmbTipoMovimiento'
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        height: 30,
                                        width: 780,
                                        items: [
                                            {
                                                xtype: 'container',
                                                flex: 1,
                                                layout: 'form',
                                                labelWidth: 120,
                                                items: [
                                                    {
                                                        xtype: 'combo',
                                                        fieldLabel: 'Concepto',
                                                        width: 400,
                                                        itemId: 'cmbConcepto',
                                                        name: 'id_concepto',
                                                        valueField: 'id_concepto',
                                                        displayField: 'descripcion',
                                                        allowBlank: false,
                                                        forceSelection: true,
                                                        enableKeyEvents: true,
                                                        minChars: 0,
                                                        pageSize: 20,
                                                        labelStyle: 'font-weight:bold',
                                                        ref: '../../../../../cmbConcepto'
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        height: 30,
                                        width: 780,
                                        items: [
                                            {
                                                xtype: 'container',
                                                flex: 1,
                                                layout: 'form',
                                                labelWidth: 120,
                                                items: [
                                                    {
                                                        xtype: 'combo',
                                                        fieldLabel: 'Tipo',
                                                        itemId: 'cmbTipoOrigen',
                                                        name: 'tipo_origen',
                                                        labelStyle: 'font-weight:bold',
                                                        displayField: 'nombre',
                                                        valueField: 'id',
                                                        editable: false,
                                                        mode: 'local',
                                                        triggerAction: 'all',
                                                        allowBlank: false,
                                                        disabled: true,
                                                        ref: '../../../../../cmbTipoOrigen'
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        height: 30,
                                        width: 780,
                                        items: [
                                            {
                                                xtype: 'container',
                                                flex: 1,
                                                layout: 'form',
                                                labelWidth: 120,
                                                items: [
                                                    {
                                                        xtype: 'combo',
                                                        fieldLabel: 'Origen',
                                                        width: 300,
                                                        itemId: 'cmbChequera',
                                                        name: 'id_chequera',
                                                        valueField: 'id_chequera',
                                                        displayField: 'descripcion',
                                                        editable: false,
                                                        triggerAction: 'all',
                                                        forceSelection: true,
                                                        minChars: 0,
                                                        pageSize: 20,
                                                        labelStyle: 'font-weight:bold',
                                                        ref: '../../../../../cmbChequera'
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        hidden: true,
                                        ref: '../../../pnlInfoRemision',
                                        id: 'pnlInfoRemision',
                                        items: [
                                            {
                                                xtype: 'container',
                                                width: 100
                                            },
                                            {
                                                xtype: 'container',
                                                width: 200,
                                                layout: 'table',
                                                height: 70,
                                                layoutConfig: {
                                                    columns: 2
                                                },
                                                items: [
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 'Total:',
                                                        width: 90,
                                                        submitValue: false,
                                                        style: 'font-size:14px;font-weight:bold'
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 0,
                                                        width: 90,
                                                        style: 'text-align:right;font-size:14px;font-weight:bold',
                                                        itemId: 'lblImporte',
                                                        ref: '../../../../../lblImporte'
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 'Abonos:',
                                                        width: 90,
                                                        submitValue: false,
                                                        style: 'font-size:14px;font-weight:bold'
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 0,
                                                        width: 90,
                                                        style: 'text-align:right;font-size:14px;font-weight:bold',
                                                        itemId: 'lblAbonos',
                                                        ref: '../../../../../lblAbonos'
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 'Saldo:',
                                                        width: 90,
                                                        submitValue: false,
                                                        style: 'font-size:14px;font-weight:bold'
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 0,
                                                        width: 90,
                                                        style: 'text-align:right;font-size:14px;font-weight:bold',
                                                        itemId: 'lblSaldo',
                                                        ref: '../../../../../lblSaldo'
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'form',
                                        labelWidth: 120,
                                        items: [
                                            {
                                                xtype: 'numberfield',
                                                fieldLabel: 'Importe',
                                                itemId: 'txtImporte',
                                                name: 'importe',
                                                allowNegative: false,
                                                allowBlank: false,
                                                labelStyle: 'font-weight:bold',
                                                style: 'text-align:right;',
                                                selectOnFocus: true,
                                                ref: '../../../../txtImporte'
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: '',
                        bubbleEvents: [
                            'cambioDeId'
                        ],
                        hidden: true,
                        hideLabel: true,
                        submitValue: false,
                        itemId: 'id_movimiento_banco',
                        ref: '../txtIdMovimientoBanco'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: 'status',
                        value: 'A',
                        hidden: true,
                        hideLabel: true,
                        submitValue: false,
                        ref: '../txtStatus'
                    }
                ]
            }
        ];
        formGastosUi.superclass.initComponent.call(this);
    }
});
