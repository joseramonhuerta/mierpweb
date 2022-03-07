/*
 * File: formListaPrecios.ui.js
 * Date: Sat Dec 02 2017 21:31:15 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formListaPreciosUi = Ext.extend(Ext.Panel, {
    title: 'Lista de Precios',
    autoScroll: true,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Guardar',
                    icon: 'images/iconos/conceptos_add.png',
                    itemId: 'btnGuardar',
                    ref: '../btnGuardar'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    text: 'Eliminar',
                    disabled: true,
                    itemId: 'btnEliminar',
                    icon: 'images/iconos/conceptos_delete.png',
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
                        xtype: 'textfield',
                        fieldLabel: 'Descripcion',
                        itemId: 'txtDescripcion',
                        name: 'concepto',
                        width: 350,
                        labelStyle: 'font-weight:bold',
                        submitValue: false,
                        ref: '../txtDescripcion'
                    },
                    {
                        xtype: 'textfield',
                        itemId: 'id_listaprecio',
                        hidden: true,
                        hideLabel: true,
                        submitValue: false,
                        fieldLabel: 'Label',
                        anchor: '100%',
                        name: '',
                        bubbleEvents: [
                            'cambioDeId'
                        ],
                        ref: '../txtIdListaPrecio'
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
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Productos',
                height: 310,
                width: 480,
                collapsible: true,
                style: 'padding-left:0;border-width:1px 0 0 0;',
                bodyStyle: 'padding-left:10px;',
                items: [
                    {
                        xtype: 'form',
                        width: 456,
                        border: false,
                        itemId: 'frmDetalles',
                        ref: '../frmDetalles',
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
                                                xtype: 'trigger',
                                                fieldLabel: 'Producto',
                                                labelSeparator: ' ',
                                                width: 200,
                                                itemId: 'cmbProducto',
                                                triggerClass: 'x-form-search-trigger',
                                                enableKeyEvents: true,
                                                selectOnFocus: true,
                                                name: 'descripcion',
                                                ref: '../../../../cmbProducto'
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
                                                fieldLabel: 'Precio',
                                                anchor: '100%',
                                                width: 10,
                                                labelSeparator: ' ',
                                                itemId: 'txtPrecio',
                                                style: 'text-align:right;',
                                                submitValue: false,
                                                allowNegative: false,
                                                ref: '../../../../txtPrecio'
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
                                                fieldLabel: 'Puntos',
                                                anchor: '100%',
                                                width: 10,
                                                labelSeparator: ' ',
                                                itemId: 'txtPuntos',
                                                style: 'text-align:right;',
                                                submitValue: false,
                                                allowNegative: false,
                                                ref: '../../../../txtPuntos'
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
                                                ref: '../../../../btnAgregar'
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
                        ref: '../gridDetalles',
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                sortable: true,
                                width: 25,
                                id: 'colDelete'
                            },
                            {
                                xtype: 'gridcolumn',
                                header: 'Descripcion',
                                sortable: true,
                                width: 225,
                                dataIndex: 'descripcion',
                                id: 'colDescripcion'
                            },
                            {
                                xtype: 'gridcolumn',
                                header: 'Precio',
                                sortable: true,
                                width: 90,
                                dataIndex: 'precio',
                                align: 'right',
                                id: 'colPrecio'
                            },
                            {
                                xtype: 'gridcolumn',
                                header: 'Puntos',
                                sortable: true,
                                width: 90,
                                dataIndex: 'valor_puntos',
                                align: 'right',
                                id: 'colPuntos'
                            }
                        ],
                        view: new Ext.grid.GridView({

                        })
                    }
                ]
            }
        ];
        formListaPreciosUi.superclass.initComponent.call(this);
    }
});
