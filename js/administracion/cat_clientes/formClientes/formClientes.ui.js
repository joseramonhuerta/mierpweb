/*
 * File: formClientes.ui.js
 * Date: Tue May 29 2018 00:14:06 GMT-0600 (Hora verano, Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formClientesUi = Ext.extend(Ext.form.FormPanel, {
    title: 'Cliente',
    width: 820,
    height: 581,
    padding: 10,
    labelWidth: 120,
    autoScroll: true,
    initComponent: function() {
        this.tbar = {
            xtype: 'toolbar',
            items: [
                {
                    xtype: 'button',
                    text: 'Guardar',
                    icon: 'images/iconos/clientes_add.png',
                    ref: '../btnGuardar'
                },
                {
                    xtype: 'button',
                    text: 'Desactivar',
                    icon: 'images/iconos/clientes_todos.png',
                    disabled: true,
                    ref: '../btnDesactivar'
                },
                {
                    xtype: 'tbseparator'
                },
                {
                    xtype: 'button',
                    text: 'Eliminar',
                    icon: 'images/iconos/clientes_delete.png',
                    disabled: true,
                    ref: '../btnEliminar'
                }
            ]
        };
        this.items = [
            {
                xtype: 'combo',
                fieldLabel: 'Tipo Cliente',
                width: 160,
                labelStyle: 'font-weight:bold;',
                allowBlank: false,
                forceSelection: true,
                name: 'tipo_cliente',
                displayField: 'nombre',
                valueField: 'id',
                mode: 'local',
                triggerAction: 'all',
                hiddenName: 'tipo_cliente',
                editable: false,
                ref: 'cmbTipoCliente'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'RFC',
                width: 120,
                allowBlank: false,
                labelStyle: 'font-weight:bold;',
                name: 'rfc_cliente',
                value: 'XAXX010101000'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Razon Social',
                allowBlank: false,
                width: 380,
                labelStyle: 'font-weight:bold;',
                name: 'nombre_fiscal',
                bubbleEvents: [
                    'cambioDeNombre'
                ],
                ref: 'txtNombreFiscal'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Nombre Comercial',
                width: 300,
                labelStyle: 'font-weight:bold;',
                allowBlank: false,
                name: 'nombre_comercial',
                autoCreate: {
                    tag: 'input',
                    type: 'text',
                    maxLength: '255',
                    size: '20',
                    autocomplete: 'off'
                }
            },
            {
                xtype: 'combo',
                fieldLabel: 'Estilista',
                name: 'estilista',
                width: 60,
                labelStyle: 'font-weight:bold;',
                mode: 'local',
                triggerAction: 'all',
                allowBlank: false,
                forceSelection: true,
                editable: false,
                displayField: 'nombre',
                valueField: 'id',
                hiddenName: 'estilista',
                ref: 'cmbEstilista'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Foraneo',
                name: 'foraneo',
                width: 60,
                labelStyle: 'font-weight:bold;',
                mode: 'local',
                triggerAction: 'all',
                allowBlank: false,
                forceSelection: true,
                editable: false,
                displayField: 'nombre',
                valueField: 'id',
                hiddenName: 'foraneo',
                ref: 'cmbForaneo'
            },
            {
                xtype: 'combo',
                fieldLabel: 'Categoría',
                width: 180,
                labelStyle: 'font-weight:bold;',
                allowBlank: false,
                forceSelection: true,
                hiddenName: 'id_cliente_categoria',
                name: 'id_cliente_categoria',
                displayField: 'nombre_categoria',
                valueField: 'id_cliente_categoria',
                enableKeyEvents: true,
                pageSize: 20,
                triggerAction: 'all',
                minChars: 2,
                triggerClass: 'x-form-search-trigger',
                ref: 'cmbCategorias'
            },
            {
                xtype: 'fieldset',
                title: 'Direccion',
                height: 242,
                width: 500,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Calle',
                        width: 350,
                        name: 'calle'
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'form',
                                flex: 1,
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Num Ext',
                                        anchor: '100%',
                                        width: 100,
                                        name: 'numext'
                                    }
                                ]
                            },
                            {
                                xtype: 'container',
                                layout: 'form',
                                labelWidth: 50,
                                style: 'margin-left:20px;',
                                flex: 1,
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Num Int',
                                        width: 100,
                                        name: 'numint'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Colonia',
                        width: 275,
                        name: 'colonia'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Código Postal',
                        width: 75,
                        labelStyle: 'font-weight:bold;',
                        allowBlank: false,
                        name: 'cp'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Localidad',
                        width: 280,
                        name: 'localidad'
                    },
                    {
                        xtype: 'combo',
                        fieldLabel: 'Ciudad',
                        width: 220,
                        labelStyle: 'font-weight:bold;',
                        allowBlank: false,
                        forceSelection: true,
                        name: 'id_ciu',
                        displayField: 'nom_ciu',
                        valueField: 'id_ciu',
                        enableKeyEvents: true,
                        pageSize: 20,
                        listWidth: 300,
                        maskRe: /[^\\\/"']/,
                        itemSelector: 'div.search-item',
                        hiddenName: 'id_ciu',
                        ref: '../cmbCiudades'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Estado',
                        width: 200,
                        labelStyle: 'font-weight:bold;',
                        allowBlank: false,
                        name: 'estado',
                        ref: '../txtEstado'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Pais',
                        width: 200,
                        labelStyle: 'font-weight:bold;',
                        allowBlank: false,
                        name: 'pais',
                        ref: '../txtPais'
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Información de Contacto',
                height: 250,
                width: 500,
                items: [
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Nombre Contacto',
                        width: 350,
                        name: 'nombre_contacto'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Calle',
                        width: 350,
                        name: 'calle_contacto'
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'form',
                                flex: 1,
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Num Ext',
                                        anchor: '100%',
                                        width: 100,
                                        name: 'numext_contacto'
                                    }
                                ]
                            },
                            {
                                xtype: 'container',
                                layout: 'form',
                                labelWidth: 50,
                                style: 'margin-left:20px;',
                                flex: 1,
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Num Int',
                                        width: 100,
                                        name: 'numint_contacto'
                                    }
                                ]
                            }
                        ]
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Colonia',
                        width: 275,
                        name: 'colonia_contacto'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Código Postal',
                        width: 75,
                        name: 'cp_contacto'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Localidad',
                        width: 280,
                        name: 'localidad_contacto'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Email Contacto',
                        width: 200,
                        name: 'email_contacto'
                    },
                    {
                        xtype: 'container',
                        layout: 'hbox',
                        items: [
                            {
                                xtype: 'container',
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Telefono',
                                        anchor: '100%',
                                        width: 135,
                                        name: 'telefono_contacto'
                                    }
                                ]
                            },
                            {
                                xtype: 'container',
                                layout: 'form',
                                labelWidth: 50,
                                style: 'margin-left:20px;',
                                items: [
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: 'Celular',
                                        width: 135,
                                        name: 'celular_contacto'
                                    }
                                ]
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'fieldset',
                title: 'Información de Ventas',
                height: 60,
                width: 500,
                items: [
                    {
                        xtype: 'combo',
                        fieldLabel: 'Lista Precio',
                        width: 250,
                        name: 'id_listaprecio',
                        hiddenName: 'id_listaprecio',
                        displayField: 'descripcion_listaprecio',
                        valueField: 'id_listaprecio',
                        enableKeyEvents: true,
                        pageSize: 20,
                        triggerAction: 'all',
                        minChars: 0,
                        allowBlank: true,
                        forceSelection: false,
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
                        ref: '../cmbListaPrecio'
                    }
                ]
            },
            {
                xtype: 'textfield',
                anchor: '100%',
                hidden: true,
                name: 'id_cliente',
                bubbleEvents: [
                    'cambioDeId'
                ],
                ref: 'txtIdCliente'
            },
            {
                xtype: 'textfield',
                anchor: '100%',
                hidden: true,
                name: 'id_est',
                ref: 'txtIdEstado'
            },
            {
                xtype: 'textfield',
                anchor: '100%',
                hidden: true,
                name: 'id_pai',
                ref: 'txtIdPais'
            },
            {
                xtype: 'textfield',
                anchor: '100%',
                hidden: true,
                name: 'status',
                value: 'A',
                bubbleEvents: [
                    'cambioDeStatus'
                ],
                ref: 'txtStatus'
            }
        ];
        formClientesUi.superclass.initComponent.call(this);
    }
});
