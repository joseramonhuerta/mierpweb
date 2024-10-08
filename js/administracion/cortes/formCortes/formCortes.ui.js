/*
 * File: formCortes.ui.js
 * Date: Sat Dec 02 2017 21:31:06 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formCortesUi = Ext.extend(Ext.Panel, {
    title: 'Corte',
    autoScroll: true,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Guardar',
                    icon: 'images/iconos/cortes_add.png',
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
                    icon: 'images/iconos/cortes_delete.png',
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
                                flex: 1,
                                layout: 'form',
                                width: 220,
                                items: [
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: 'Fecha',
                                        width: 100,
                                        itemId: 'txtFecha',
                                        name: 'fechainicio',
                                        ref: '../../../txtFecha'
                                    }
                                ]
                            },
                            {
                                xtype: 'container',
                                flex: 1,
                                layout: 'form',
                                labelWidth: 50,
                                items: [
                                    {
                                        xtype: 'timefield',
                                        fieldLabel: 'Hora',
                                        name: 'hora',
                                        itemId: 'txtHora',
                                        width: 100,
                                        format: 'g:i:s A',
                                        disabled: true,
                                        ref: '../../../txtHora'
                                    }
                                ]
                            }
                        ]
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
                        xtype: 'textfield',
                        itemId: 'id_corte',
                        hidden: true,
                        hideLabel: true,
                        submitValue: false,
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: '',
                        bubbleEvents: [
                            'cambioDeId'
                        ],
                        ref: '../txtIdCorte'
                    },
                    {
                        xtype: 'textfield',
                        hidden: true,
                        hideLabel: true,
                        submitValue: false,
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: 'status',
                        bubbleEvents: '[cambioDeStatus]',
                        value: 'A',
                        ref: '../txtStatus'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Label',
                        anchor: '100%',
                        hidden: true,
                        hideLabel: true,
                        itemId: 'txtTotal',
                        name: 'total',
                        submitValue: false,
                        ref: '../txtTotal'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Label',
                        anchor: '100%',
                        hidden: true,
                        hideLabel: true,
                        itemId: 'txtTotalRetencion',
                        name: 'total',
                        submitValue: false,
                        ref: '../txtTotalRetencion'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Label',
                        anchor: '100%',
                        hidden: true,
                        hideLabel: true,
                        itemId: 'txtTotalCorte',
                        name: 'total',
                        submitValue: false,
                        ref: '../txtTotalCorte'
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Detalles',
                height: 360,
                width: 497,
                collapsible: true,
                style: 'padding-left:0;border-width:1px 0 0 0;',
                bodyStyle: 'padding-left:10px;',
                items: [
                    {
                        xtype: 'tabpanel',
                        activeTab: 0,
                        height: 320,
                        items: [
                            {
                                xtype: 'panel',
                                title: 'Liquidacion',
                                style: 'padding-left:0;border-width:1px 0 0 0;',
                                bodyStyle: 'padding-left:10px;',
                                hideBorders: true,
                                border: false,
                                items: [
                                    {
                                        xtype: 'container',
                                        height: 10
                                    },
                                    {
                                        xtype: 'form',
                                        width: 456,
                                        border: false,
                                        itemId: 'frmDetalles',
                                        ref: '../../../frmDetalles',
                                        items: [
                                            {
                                                xtype: 'container',
                                                layout: 'hbox',
                                                height: 50,
                                                items: [
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        labelAlign: 'top',
                                                        width: 210,
                                                        items: [
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Forma de Pago',
                                                                anchor: '100%',
                                                                labelSeparator: ' ',
                                                                itemId: 'cmbFormaPago',
                                                                name: 'id_formapago',
                                                                valueField: 'id_formapago',
                                                                displayField: 'nombre_formapago',
                                                                editable: false,
                                                                triggerAction: 'all',
                                                                ref: '../../../../../../cmbFormaPago'
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        width: 8
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        labelAlign: 'top',
                                                        width: 90,
                                                        items: [
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Denominacion',
                                                                anchor: '100%',
                                                                labelSeparator: ' ',
                                                                itemId: 'cmbDenominacion',
                                                                name: 'id_denominacion',
                                                                triggerAction: 'all',
                                                                editable: false,
                                                                valueField: 'id_denominacion',
                                                                displayField: 'denominacion',
                                                                style: 'text-align:right;',
                                                                ref: '../../../../../../cmbDenominacion'
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        width: 8
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        labelAlign: 'top',
                                                        width: 90,
                                                        items: [
                                                            {
                                                                xtype: 'numberfield',
                                                                fieldLabel: 'Cantidad',
                                                                anchor: '100%',
                                                                width: 10,
                                                                labelSeparator: ' ',
                                                                itemId: 'txtCantidad',
                                                                style: 'text-align:right;',
                                                                submitValue: false,
                                                                allowNegative: false,
                                                                ref: '../../../../../../txtCantidad'
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        width: 50,
                                                        items: [
                                                            {
                                                                xtype: 'button',
                                                                style: 'margin-left:8px;margin-top:5px;',
                                                                width: 24,
                                                                height: 24,
                                                                scale: 'large',
                                                                itemId: 'btnAgregar',
                                                                icon: 'images/iconos/ventas_add_big.png',
                                                                ref: '../../../../../../btnAgregar'
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'grid',
                                        height: 200,
                                        width: 456,
                                        itemId: 'gridDetalles',
                                        ref: '../../../gridDetalles',
                                        columns: [
                                            {
                                                xtype: 'gridcolumn',
                                                sortable: true,
                                                width: 25,
                                                id: 'colDelete'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Forma de Pago',
                                                sortable: true,
                                                width: 150,
                                                dataIndex: 'nombre_formapago',
                                                id: 'colFormaPago'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Denominacion',
                                                sortable: true,
                                                width: 80,
                                                dataIndex: 'denominacion',
                                                id: 'colDenominacion'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Cantidad',
                                                sortable: true,
                                                width: 90,
                                                dataIndex: 'cantidad',
                                                align: 'right',
                                                id: 'colCantidad'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Total',
                                                sortable: true,
                                                width: 90,
                                                dataIndex: 'total',
                                                align: 'right',
                                                id: 'colTotal'
                                            }
                                        ],
                                        view: new Ext.grid.GridView({

                                        })
                                    },
                                    {
                                        xtype: 'container',
                                        height: 5
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        width: 456,
                                        items: [
                                            {
                                                xtype: 'container',
                                                width: 190
                                            },
                                            {
                                                xtype: 'container',
                                                layout: 'table',
                                                flex: 1,
                                                items: [
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 'Total Liquidado:',
                                                        width: 150,
                                                        style: 'text-align:right;font-size:14px;font-weight:bold',
                                                        height: 20,
                                                        submitValue: false
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 0,
                                                        width: 100,
                                                        style: 'text-align:right;font-size:14px;font-weight:bold',
                                                        itemId: 'lblTotal',
                                                        height: 20,
                                                        submitValue: false,
                                                        ref: '../../../../../lblTotal'
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                ]
                            },
                            {
                                xtype: 'panel',
                                title: 'Retencion',
                                style: 'padding-left:0;border-width:1px 0 0 0;',
                                bodyStyle: 'padding-left:10px;',
                                hideBorders: true,
                                border: false,
                                items: [
                                    {
                                        xtype: 'container',
                                        height: 10
                                    },
                                    {
                                        xtype: 'form',
                                        width: 456,
                                        border: false,
                                        itemId: 'frmDetallesRetencion',
                                        ref: '../../../frmDetallesRetencion',
                                        items: [
                                            {
                                                xtype: 'container',
                                                layout: 'hbox',
                                                height: 50,
                                                items: [
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        labelAlign: 'top',
                                                        width: 210,
                                                        items: [
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Forma de Pago',
                                                                anchor: '100%',
                                                                labelSeparator: ' ',
                                                                itemId: 'cmbFormaPagoRetencion',
                                                                name: 'id_formapago',
                                                                valueField: 'id_formapago',
                                                                displayField: 'nombre_formapago',
                                                                editable: false,
                                                                triggerAction: 'all',
                                                                ref: '../../../../../../cmbFormaPagoRetencion'
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        width: 8
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        labelAlign: 'top',
                                                        width: 90,
                                                        items: [
                                                            {
                                                                xtype: 'combo',
                                                                fieldLabel: 'Denominacion',
                                                                anchor: '100%',
                                                                labelSeparator: ' ',
                                                                itemId: 'cmbDenominacionRetencion',
                                                                name: 'id_denominacion',
                                                                triggerAction: 'all',
                                                                editable: false,
                                                                valueField: 'id_denominacion',
                                                                displayField: 'denominacion',
                                                                style: 'text-align:right;',
                                                                ref: '../../../../../../cmbDenominacionRetencion'
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        width: 8
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        labelAlign: 'top',
                                                        width: 90,
                                                        items: [
                                                            {
                                                                xtype: 'numberfield',
                                                                fieldLabel: 'Cantidad',
                                                                anchor: '100%',
                                                                width: 10,
                                                                labelSeparator: ' ',
                                                                itemId: 'txtCantidadRetencion',
                                                                style: 'text-align:right;',
                                                                submitValue: false,
                                                                allowNegative: false,
                                                                ref: '../../../../../../txtCantidadRetencion'
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        xtype: 'container',
                                                        flex: 1,
                                                        layout: 'form',
                                                        width: 50,
                                                        items: [
                                                            {
                                                                xtype: 'button',
                                                                style: 'margin-left:8px;margin-top:5px;',
                                                                width: 24,
                                                                height: 24,
                                                                scale: 'large',
                                                                itemId: 'btnAgregarRetencion',
                                                                icon: 'images/iconos/ventas_add_big.png',
                                                                ref: '../../../../../../btnAgregarRetencion'
                                                            }
                                                        ]
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'grid',
                                        height: 200,
                                        width: 456,
                                        itemId: 'gridDetallesRetencion',
                                        ref: '../../../gridDetallesRetencion',
                                        columns: [
                                            {
                                                xtype: 'gridcolumn',
                                                sortable: true,
                                                width: 25,
                                                id: 'colDeleteRetencion'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Forma de Pago',
                                                sortable: true,
                                                width: 150,
                                                dataIndex: 'nombre_formapago',
                                                id: 'colFormaPagoRetencion'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Denominacion',
                                                sortable: true,
                                                width: 80,
                                                dataIndex: 'denominacion',
                                                id: 'colDenominacionRetencion'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Cantidad',
                                                sortable: true,
                                                width: 90,
                                                dataIndex: 'cantidad',
                                                align: 'right',
                                                id: 'colCantidadRetencion'
                                            },
                                            {
                                                xtype: 'gridcolumn',
                                                header: 'Total',
                                                sortable: true,
                                                width: 90,
                                                dataIndex: 'total',
                                                align: 'right',
                                                id: 'colTotalRetencion'
                                            }
                                        ],
                                        view: new Ext.grid.GridView({

                                        })
                                    },
                                    {
                                        xtype: 'container',
                                        height: 5
                                    },
                                    {
                                        xtype: 'container',
                                        layout: 'hbox',
                                        width: 456,
                                        items: [
                                            {
                                                xtype: 'container',
                                                width: 190
                                            },
                                            {
                                                xtype: 'container',
                                                layout: 'table',
                                                flex: 1,
                                                items: [
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 'Total Retenido:',
                                                        width: 150,
                                                        style: 'text-align:right;font-size:14px;font-weight:bold',
                                                        height: 20,
                                                        submitValue: false
                                                    },
                                                    {
                                                        xtype: 'displayfield',
                                                        value: 0,
                                                        width: 100,
                                                        style: 'text-align:right;font-size:14px;font-weight:bold',
                                                        itemId: 'lblTotalRetencion',
                                                        height: 20,
                                                        submitValue: false,
                                                        ref: '../../../../../lblTotalRetencion'
                                                    }
                                                ]
                                            }
                                        ]
                                    }
                                ]
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'container',
                items: [
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'container',
                                width: 190
                            },
                            {
                                xtype: 'container',
                                layout: 'table',
                                flex: 1,
                                items: [
                                    {
                                        xtype: 'displayfield',
                                        value: 'Total Corte:',
                                        width: 150,
                                        style: 'text-align:right;font-size:22px;font-weight:bold',
                                        submitValue: false
                                    },
                                    {
                                        xtype: 'displayfield',
                                        value: 0,
                                        width: 130,
                                        style: 'text-align:right;font-size:20px;font-weight:bold',
                                        itemId: 'lblTotalCorte',
                                        submitValue: false,
                                        ref: '../../../lblTotalCorte'
                                    }
                                ]
                            }
                        ]
                    }
                ]
            }
        ];
        formCortesUi.superclass.initComponent.call(this);
    }
});
