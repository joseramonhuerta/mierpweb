/*
 * File: formPuntoVentaWinVentas.ui.js
 * Date: Tue Dec 25 2018 23:34:27 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formPuntoVentaWinVentasUi = Ext.extend(Ext.Window, {
    title: 'Ventas',
    width: 590,
    height: 303,
    modal: true,
    border: false,
    draggable: false,
    resizable: false,
    initComponent: function() {
        this.items = [
            {
                xtype: 'panel',
                items: [
                    {
                        xtype: 'container',
                        height: 35,
                        layout: 'column',
                        style: 'margin-top:10px;margin-left:10px',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'form',
                                width: 220,
                                items: [
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: 'Fecha Inicio',
                                        itemId: 'txtFechaInicio',
                                        ref: '../../../txtFechaInicio'
                                    }
                                ]
                            },
                            {
                                xtype: 'container',
                                layout: 'form',
                                width: 220,
                                items: [
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: 'Fecha Fin',
                                        itemId: 'txtFechaFin',
                                        ref: '../../../txtFechaFin'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'container',
                        layout: 'column',
                        style: 'margin-left:10px',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Folio',
                                        anchor: '100%',
                                        width: 150,
                                        itemId: 'txtSerieFolioBusqueda',
                                        ref: '../../../txtSerieFolioBusqueda'
                                    }
                                ]
                            },
                            {
                                xtype: 'container',
                                width: 10,
                                height: 1
                            },
                            {
                                xtype: 'container',
                                items: [
                                    {
                                        xtype: 'button',
                                        text: 'Buscar',
                                        itemId: 'btnFiltro',
                                        icon: 'images/iconos/buscar.png',
                                        ref: '../../../btnFiltro'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'grid',
                        itemId: 'gridVentas',
                        height: 200,
                        border: false,
                        hideBorders: true,
                        ref: '../gridVentas',
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                header: 'Serie Folio',
                                sortable: true,
                                width: 100,
                                dataIndex: 'serie_folio'
                            },
                            {
                                xtype: 'gridcolumn',
                                header: 'Fecha Venta',
                                sortable: true,
                                width: 80,
                                dataIndex: 'fecha_venta'
                            },
                            {
                                xtype: 'gridcolumn',
                                header: 'Cliente',
                                sortable: true,
                                width: 270,
                                dataIndex: 'nombre_cliente'
                            },
                            {
                                xtype: 'gridcolumn',
                                header: 'Total Venta',
                                sortable: true,
                                width: 100,
                                dataIndex: 'total_venta',
                                align: 'right',
                                id: 'colTotalVenta'
                            }
                        ],
                        view: new Ext.grid.GridView({

                        }),
                        bbar: {
                            xtype: 'paging',
                            itemId: 'bottomToolbar',
                            ref: '../../bottomToolbar'
                        }
                    }
                ]
            }
        ];
        formPuntoVentaWinVentasUi.superclass.initComponent.call(this);
    }
});