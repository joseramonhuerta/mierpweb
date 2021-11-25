/*
 * File: gridCertificados.ui.js
 * Date: Wed Feb 01 2017 00:31:50 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

gridCertificadosUi = Ext.extend(Ext.grid.GridPanel, {
    title: 'Buscar Certificados',
    store: 'storeGridCertificados',
    height: 250,
    autoExpandColumn: 'colRazonSocialCer',
    stripeRows: true,
    initComponent: function() {
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
                    disabled: true,
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
        this.view = new Ext.grid.GridView({

        });
        this.columns = [
            {
                xtype: 'gridcolumn',
                dataIndex: 'rfc_certificado',
                header: 'RFC',
                sortable: true,
                width: 100,
                id: 'colRfcCer'
            },
            {
                xtype: 'gridcolumn',
                header: 'Empresa',
                sortable: true,
                width: 320,
                dataIndex: 'razonsocial_certificado',
                id: 'colRazonSocialCer'
            },
            {
                xtype: 'gridcolumn',
                header: 'Número Certificado',
                sortable: true,
                width: 200,
                dataIndex: 'numero_certificado',
                id: 'colNumeroCretificado'
            },
            {
                xtype: 'gridcolumn',
                header: 'Fecha Solicitud',
                sortable: true,
                width: 100,
                dataIndex: 'fecha_solicitud',
                align: 'right',
                id: 'colFechaSolicitud'
            },
            {
                xtype: 'gridcolumn',
                header: 'Fecha Vencimiento',
                sortable: true,
                width: 100,
                align: 'right',
                dataIndex: 'fecha_vencimiento',
                id: 'colFechaVencimiento'
            }
        ];
        this.bbar = {
            xtype: 'paging',
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: 'No hay registros para mostrar'
        };
        gridCertificadosUi.superclass.initComponent.call(this);
    }
});