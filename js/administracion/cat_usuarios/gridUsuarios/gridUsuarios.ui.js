/*
 * File: gridUsuarios.ui.js
 * Date: Sat Jan 19 2019 12:59:23 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

gridUsuariosUi = Ext.extend(Ext.grid.GridPanel, {
    title: 'Usuarios',
    store: 'storeGridUsuarios',
    width: 805,
    height: 250,
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
                dataIndex: 'nombre_usuario',
                header: 'Nombre Usuario',
                sortable: true,
                width: 400,
                id: 'colNombreUsuario'
            },
            {
                xtype: 'gridcolumn',
                dataIndex: 'usuario',
                header: 'Usuario',
                sortable: true,
                width: 300,
                id: 'colUsuario'
            }
        ];
        this.bbar = {
            xtype: 'paging',
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: 'No hay registros para mostrar'
        };
        gridUsuariosUi.superclass.initComponent.call(this);
    }
});
