Fact = {};

// ----- Grid de Facturas -----
Fact.Facturacion = Ext.extend(Ext.grid.GridPanel, {
	initComponent: function() {
		Ext.apply(this, {
			height: 480,
			frame: false,
			store: new Ext.data.Store({
				url:  'php/request.php',
				baseParams: { code: 'Facturas' },
				reader: new Ext.data.JsonReader({
					root: 'rows'
				},[
					'id_fac',
					'serie_fac',
					'folio_fac',
					'TipoDoc',
					'TipoComp',
					'subtotal',
					'descuento',
					'TotImpRet',
					'TotImpTras',
					'total_fac',
					'FecPago',
					'RFCCliente',
					'NomCliente',
					'AddFecha'
				]),
				autoDestroy: true
			}),
			columns: [
				{ header: 'Folio',             align: 'left',   dataIndex: 'folio_fac',   sortable: true, width: 60 },
				{ header: 'Serie',             align: 'left',   dataIndex: 'serie_fac',   width: 50 },
				{ header: 'RFC Cliente',       align: 'left',   dataIndex: 'RFCCliente',  hidden: true },
				{ header: 'Nombre',            align: 'left',   dataIndex: 'NomCliente',  width:200, renderer: getClienteNombreRFC },
				{ header: 'Tipo Documento',    align: 'left',   dataIndex: 'TipoDoc',     width:120, renderer: getTipoDocumento },
				{ header: 'Tipo Comprobante',  align: 'left',   dataIndex: 'TipoComp',    width:100, renderer: getTipoComprobante },
				{ header: 'Sub-Total',         align: 'left',   dataIndex: 'subtotal',    width:70,  renderer: Ext.util.Format.numberRenderer('$0,000.00') },
				{ header: 'Descuento',         align: 'left',   dataIndex: 'descuento',   width:70,  renderer: Ext.util.Format.numberRenderer('$0,000.00') },
				{ header: 'Imp. Ret.',         align: 'left',   dataIndex: 'TotImpRet',   width:70,  renderer: Ext.util.Format.numberRenderer('$0,000.00') },
				{ header: 'Imp. Trasl.',       align: 'left',   dataIndex: 'TotImpTras',  width:70,  renderer: Ext.util.Format.numberRenderer('$0,000.00') },
				{ header: 'Total',             align: 'left',   dataIndex: 'total_fac',   width:70,  renderer: Ext.util.Format.numberRenderer('$0,000.00') },
				{ header: 'Fecha Pago',        align: 'left',   dataIndex: 'FecPago',     width:100, renderer: Ext.util.Format.dateRenderer('d-F-Y')  },
				{ header: 'Fecha Reg.',        align: 'left',   dataIndex: 'AddFecha',    width:100, renderer: Ext.util.Format.dateRenderer('d-F-Y')  }
			],
			// toolbar
			tbar: [{ 
				text:  'Agregar',
				width: 70,
				handler: function(){
					// ----- Form de Facturas -----
					var tab_panel = Ext.getCmp('tabContainer');
					tab_panel.add({ 
						title:  'Agregar Facturas', 
						items: [{ xtype: 'formFacturacion' }],
						closable: true	
					}).show();
				}
			}]
		});
		Fact.Facturacion.superclass.initComponent.apply(this, arguments);
	},

	onRender: function() {
		this.store.load();
		Fact.Facturacion.superclass.onRender.apply(this, arguments);
	}
});

Ext.reg('gridFacturacion', Fact.Facturacion);



// ----- Form de Facturas -----
Fact.formFacturacion = Ext.extend(Ext.FormPanel, {
	initComponent: function() {
		Ext.apply(this, {
			url:    'php/request.php',
			//height: 480,
			frame:  true,
			layout: 'table',
			defaults:     { 
				bodystyle:  'padding: 2px 30px 2px 2px'
			},
			layoutConfig: { columns: 3 },
			items: [{
				layout: 'column',
				width:  350,
				defaults: {
					xtype: 'container',
					layout: 'form'
				},
				items: [{
					items: [{
						xtype: 'radio',
						fieldLabel: 'Actividad Empresarial',
						labelStyle: 'width: 140px',
						width: 120,
						style: 'margin-top: 4px',
						boxLabel: 'Fisica',
						value: 'F',
						name:  'actividad'
					}]
				},{
					items: [{
						xtype: 'radio',
						style: 'margin-top: 4px',
						boxLabel: 'Moral',
						hideLabel: true,
						value: 'M',
						name:  'actividad'
					}]
				}]
			},{
				layout: 'form',
				colspan: 2,
				width: 550,
				items: [{
					xtype: 'textfield',
					fieldLabel: 'RFC',
					name:  'RFCCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					width: 180,
					fieldLabel: 'Nombre(s)',
					labelStyle: 'width: 120px',
					name:   'NombreCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					fieldLabel: 'Apellido Paterno',
					name:   'APaternoCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					fieldLabel: 'Apellido Materno',
					name:   'AMaternoCliente'
				}]
			},{
				layout: 'form',
				colspan: 3,
				items: [{
					xtype: 'textfield',
					width: 550,
					fieldLabel: 'Nombre Comercial',
					labelStyle: 'width: 120px',
					name:  'NombComCliente'
				}]
			},{
				layout: 'form',
				colspan: 3,
				items: [{
					xtype: 'textfield',
					width: 550,
					fieldLabel: 'Nombre Fiscal',
					labelStyle: 'width: 120px',
					name:  'NomCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					width: 180,
					fieldLabel: 'Calle',
					labelStyle: 'width: 120px',
					name:   'CalleCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					width:  80,
					fieldLabel: 'Num. Exterior',
					name:   'NumExtCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					width:  80,
					fieldLabel: 'Num. Interior',
					name:   'NumIntCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					width:  180,
					fieldLabel: 'Colonia',
					labelStyle: 'width: 120px',
					name:   'ColCliente'
				}]
			},{
				layout: 'form',
				colspan: 2,
				items: [{
					xtype:  'textfield',
					width:  180,
					fieldLabel: 'Ciudad',
					name:   'MunCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					width:  180,
					fieldLabel: 'Estado',
					labelStyle: 'width: 120px',
					name:   'EdoCliente'
				}]
			},{
				layout: 'form',
				colspan: 2,
				items: [{
					xtype:  'textfield',
					width:  180,
					fieldLabel: 'Pais',
					name:   'PaisCliente'
				}]
			},{
				layout: 'form',
				items: [{
					xtype:  'textfield',
					width:  80,
					fieldLabel: 'C.P.',
					labelStyle: 'width: 120px',
					name:   'CPCliente'
				}]
			},{
				layout: 'form',
				colspan: 2,
				items: [{
					xtype:  'textfield',
					width:  180,
					fieldLabel: 'CURP',
					name:   'CURPCliente'
				}]
			}],
			bbar: [{
				text: 'Guardar',
				handler: function(){
					Ext.Msg.alert('','Guardar el registro');
					this.getForm().submit({
						success: function(f,a) {
							Ext.Msg.alert('','OK');
						},
						failure: function(f,a) {
							Ext.Msg.alert('','Error');
						}
					});
				}
			}]
		});
		Fact.formFacturacion.superclass.initComponent.apply(this, arguments);
	}
});

Ext.reg('formFacturacion', Fact.formFacturacion);

