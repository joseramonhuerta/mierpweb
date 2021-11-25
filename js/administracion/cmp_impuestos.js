Ext.ns('miFacturaWeb');
Ext.ns('miFacturaWeb.gridImpuestos');


/****************************************************************************
//                       ----- Grid de Impuestos -----                       
****************************************************************************/
miFacturaWeb.gridImpuestos = Ext.extend(miFacturaWeb.CatalogoGridEditor, {
	
	autoExpandColumn: 'gridImpuestos_Descripcion',
	stripeRows:true,
	initComponent: function() {
	
		this.campoId     = "IDImp";
		this.campoActivo = "ActivoImp";
		
		this.columns = [{
			header: 'ID',
			align:  'left',
			dataIndex: 'IDImp',
			hidden: true
		},{
			header: 'Descripción',
			align:  'left',
			dataIndex: 'DescImp',
			id:     'gridImpuestos_Descripcion',
			sortable: true,
			editor: new Ext.form.TextField({
				allowBlank: false,
				autoCreate: {tag: 'input', maxlength: '40'},
				listeners: {
					specialKey: function(field, e){
						if(e.getKey() === e.ENTER){
							if (Ext.isIE){
								return false; // LMNT - BUG en IE: al dar enter asigna el valor del siguiente renglon
							}
						}
					}
				}
			})
		}];
		
		this.campos = [{
			name: 'IDImp',
			type: 'int'
		},{
			name: 'DescImp',
			type: 'string'
		},{
			name: 'ActivoImp',
			type: 'int',
			defaultValue: 1  // Al agregar uno registro se mostrara activo por default
		}];
		
		this.urlActivar = 'app.php/impuestos/activar';
		this.store = new Ext.data.JsonStore({
			idProperty: 'IDImp',
			root: 'data',
			totalProperty: 'totalRows',
			autoDestroy: true,
			messageProperty: "msg",
			autoSave: false,
			fields: this.campos,
			proxy:new Ext.data.HttpProxy({
				api: {
					read    : 'app.php/impuestos/getimpuestos',
					create  : 'app.php/impuestos/nuevo',
					update  : 'app.php/impuestos/actualizar',
					destroy : 'app.php/impuestos/eliminar'
				}
			}),
			writer : new Ext.data.JsonWriter({
				writeAllFields  : true,
				encodeDelete: true
			})
		});
        miFacturaWeb.gridImpuestos.superclass.initComponent.call(this);
	},
	
	eliminaVacios: function(){
		var s = this.store;
		for (var i = 0; i < s.getCount(); i++){
			if(s.getAt(i).data.DescImp == undefined){  // Eliminar registros sin datos
				s.removeAt(i);
			}
		}
	}
	
});

Ext.reg('gridImpuestos', miFacturaWeb.gridImpuestos);

