/*
 * File: formChecador.ui.js
 * Date: Sat Jul 13 2019 01:30:40 GMT-0600 (Mountain Daylight Time (Mexico))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formChecadorUi = Ext.extend(Ext.Panel, {
    title: 'Checador',
    autoScroll: true,
    initComponent: function() {
        this.items = [
            {
                xtype: 'form',
                width: 990,
                padding: 10,
                border: false,
                itemId: 'frmMain',
                labelWidth: 180,
                ref: 'frmMain',
                items: [
                    {
                        xtype: 'trigger',
                        fieldLabel: 'Codigo Empleado',
                        itemId: 'cmbEmpleado',
                        triggerClass: 'x-form-search-trigger',
                        selectOnFocus: true,
                        enableKeyEvents: true,
                        labelStyle: 'font-size:16px;font-weight:bold;',
                        width: 200,
                        style: 'font-size:14px;font-weight:bold;',
                        submitValue: false,
                        allowBlank: false,
                        ref: '../cmbEmpleado'
                    },
                    {
                        xtype: 'container',
                        height: 25
                    },
                    {
                        xtype: 'displayfield',
                        anchor: '100%',
                        itemId: 'lblHora',
                        style: 'font-size:35px;font-weight:bold;text-align:left;color:#FFFFF;',
                        hideLabel: true,
                        ref: '../lblHora'
                    },
                    {
                        xtype: 'container',
                        height: 25
                    },
                    {
                        xtype: 'displayfield',
                        anchor: '100%',
                        itemId: 'lblMensaje',
                        style: 'font-size:35px;font-weight:bold;text-align:left;color:#1E90FF;',
                        hideLabel: true,
                        ref: '../lblMensaje'
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
                        itemId: 'id_venta',
                        ref: '../txtIdVenta'
                    }
                ]
            }
        ];
        formChecadorUi.superclass.initComponent.call(this);
    }
});
