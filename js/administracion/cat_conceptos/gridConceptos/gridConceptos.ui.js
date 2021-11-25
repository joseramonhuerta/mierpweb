/*
 * File: gridConceptos.ui.js
 * Date: Thu Jan 17 2019 21:20:15 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

gridConceptosUi = Ext.extend(Ext.grid.GridPanel, {
    title: 'Conceptos',
    store: 'storeGridConceptos',
    width: 820,
    height: 250,
    stripeRows: true,
    initComponent: function() {
        this.view = new Ext.grid.GridView({

        });
        this.columns = [
            {
                xtype: 'gridcolumn',
                dataIndex: 'descripcion',
                header: 'Concepto',
                sortable: true,
                width: 500,
                id: 'colNombreConceptos'
            },
            {
                xtype: 'gridcolumn',
                header: 'Tipo',
                sortable: true,
                width: 100,
                dataIndex: 'tipo',
                id: 'colTipo'
            }
        ];
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Nuevo',
                    ref: '../btnAgregar'
                },
                {
                    xtype: 'button',
                    text: 'Editar',
                    ref: '../btnEditar'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    text: 'Eliminar',
                    disabled: true,
                    ref: '../btnEliminar'
                },
                {
                    xtype: 'tbfill'
                },
                {
                    xtype: 'label',
                    text: 'Status:',
                    style: 'margin-right:5px;'
                },
                {
                    xtype: 'combo',
                    width: 100,
                    itemId: 'cmbStatus',
                    name: 'status',
                    triggerAction: 'all',
                    mode: 'local',
                    displayField: 'nombre',
                    valueField: 'id',
                    forceSelection: true,
                    allowBlank: false,
                    hiddenName: 'status',
                    editable: false,
                    ref: '../cmbStatus'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'textfield',
                    width: 250,
                    emptyText: 'Introduzca el texto a buscar',
                    ref: '../txtFiltro'
                }
            ]
        };
        this.bbar = {
            xtype: 'paging',
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: 'No hay registros para mostrar'
        };
        gridConceptosUi.superclass.initComponent.call(this);
    }
});