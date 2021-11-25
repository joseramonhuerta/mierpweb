Ext.ns('miFacturaWeb');
Ext.ns('miFacturaWeb.gridUnidadesMedidas');


/****************************************************************************
//                       ----- Grid de Unidades de Medida -----                       
****************************************************************************/
miFacturaWeb.gridUnidadesMedidas = Ext.extend(miFacturaWeb.CatalogoGridEditor, {
	//autoExpandColumn: 1,
	stripeRows:true,
	initComponent: function() {
		//-----------------------------------------------------
		this.columnaStatus='ActivoUni';
		this.cancelValue='0';	
		//this.campoActivo = "ActivoUni";
		this.campoId     = "IDUni";
		
		
		this.txtAbreviacion = new Ext.form.TextField({
			allowBlank: false,
			autoCreate: {tag: 'input', maxlength: '10'}
		});
		
		this.columns = [{
			header: 'ID',
			align:  'left',
			dataIndex: 'IDUni',
			hidden: true
		},{
			header: 'Descripción',
			align:  'left',
			dataIndex: 'DescUni',
			sortable: true,
			editor: new Ext.form.TextField({
				allowBlank: false,
				autoCreate: {tag: 'input', maxlength: '40'},
				listeners: {
					specialkey: function(field, e){ /* ... Al dar ENTER evito que se cierre el editor,  */
						if (e.getKey() == e.ENTER){ /* ... en su lugar mando a el foco al campo de abreviacion */
							this.ownerCt.grid.txtAbreviacion.focus();
							return false;
						}else if (e.getKey()==9){
							this.ownerCt.grid.txtAbreviacion.focus();
							return false;
						}
					}
				}
			}),
			width: 450
		},{
			header: 'Abreviatura',
			align: 'left',
			dataIndex: 'AbrevUni',
			sortable: true,
			editor: this.txtAbreviacion,
			width: 150
		}];
		
		this.campos = [{
			name: 'IDUni',
			type: 'int'
		},{
			name: 'DescUni',
			type: 'string'
		},{
			name: 'AbrevUni',
			type: 'string'
		},{
			name: 'ActivoUni',
			type: 'int',
			defaultValue: 1  // Al agregar uno registro se mostrara activo por default
		}];
		
		this.urlActivar = 'app.php/unidades/activar';
		this.store = new Ext.data.JsonStore({
			idProperty: 'IDUni',
			root: 'data',
			totalProperty: 'totalRows',
			autoDestroy: true,
			messageProperty: "msg",
			autoSave: false,
			fields: this.campos,
			proxy:new Ext.data.HttpProxy({
				api: {
					read    : 'app.php/unidades/getunidadesmedida',
					create  : 'app.php/unidades/nuevo',
					update  : 'app.php/unidades/actualizar',
					destroy : 'app.php/unidades/eliminar'
				}
			}),
			writer : new Ext.data.JsonWriter({
				writeAllFields  : true,
				encodeDelete: true
			})
		});
        miFacturaWeb.gridUnidadesMedidas.superclass.initComponent.call(this);
	},
	
	eliminaVacios: function(){
		var s = this.store;
		for (var i = 0; i < s.getCount(); i++){
			if(s.getAt(i).data.DescUni == undefined){  // Eliminar registros sin datos
				s.removeAt(i);
			}
		}
	}
	
});

Ext.reg('gridUnidadesMedidas', miFacturaWeb.gridUnidadesMedidas);

