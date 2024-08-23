/*
 * File: formReporteCatalogoClientesUi.ui.js
 * Date: Sat Mar 09 2019 15:27:55 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be auto-generated each and everytime you export.
 *
 * Do NOT hand edit this file.
 */

formReporteCatalogoClientesUi = Ext.extend(Ext.form.FormPanel, {
	title: 'Reporte Flujo de Efectivo',
	width: 717,
	height: 465,
	padding: 10,
	autoScroll: true,
	initComponent: function() {
		this.tbar = {
			xtype: 'toolbar',
			items: [
				{
                    xtype: 'splitbutton',
                    text: 'Imprimir',
                    icon: 'images/iconos/bullet_printer.png',
                    ref: '../btnImprimir',
                    menu: {
                        xtype: 'menu',
                        items: [
                            {
                                xtype: 'menuitem',
                                text: 'PDF',
                                itemId: 'btnPDF',
                                icon: 'images/iconos/pdf.png',
                                ref: '../../../btnPDF'
                            },
                            {
                                xtype: 'menuitem',
                                text: 'Excel',
                                itemId: 'btnExcel',
                                icon: 'images/iconos/excel.png',
                                ref: '../../../btnExcel'
                            }
                        ]
                    }
                }
			]
		};
		this.items = [{
			xtype: 'combo',
			fieldLabel: 'Categoria',
			width: 300,
			itemId: 'cmbCategorias',
			name: 'id_cliente_categoria',
			displayField: 'nombre_categoria',
			valueField: 'id_cliente_categoria',
			enableKeyEvents: true,
			emptyText: 'Todas',
			pageSize: 20,
			triggerAction: 'all',
			hiddenName: 'id_cliente_categoria',
			minChars: 0,
			triggerConfig: {
				tag: 'span',
				cls: 'x-form-twin-triggers',
				style: 'padding-right:2px',
				cn: [{
					tag: "img",
					src: Ext.BLANK_IMAGE_URL,
					cls: "x-form-trigger x-form-clear-trigger"
				}, {
					tag: "img",
					src: Ext.BLANK_IMAGE_URL,
					cls: "x-form-trigger x-form-search-trigger"
				}]
			},
			allowBlank: true,
			labelStyle: 'font-weight:bold',
			ref: 'cmbCategorias'
		}];
		formReporteCatalogoClientesUi.superclass.initComponent.call(this);
	}
});