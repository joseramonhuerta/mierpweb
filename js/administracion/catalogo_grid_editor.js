Ext.ns('miFacturaWeb');
Ext.ns('miFacturaWeb.CatalogoGridEditor');


/*******************************************************************************************************
                     <-----                 GRID               ----->
********************************************************************************************************/
miFacturaWeb.CatalogoGridEditor = Ext.extend(Ext.grid.EditorGridPanel, {
    
   //	 frame:  false,
    //border: false,
   // style:  "border-top:none;",
    bodyStyle:'border-width:1px 0 0 0;',
    root:   'data',
    totalProperty:  'totalRows',
    messageProperty: "msg" ,
    textoMenu: '',
    buscar: function(){
		this.bottomToolbar.doRefresh();
    },
	
    initComponent: function() {
		this.vertodos = 0;
		
		var filtro = new Ext.form.TextField({
			grid:  this,
			width: 200,
			emptyText: "Escriba el texto a buscar",
			listeners: {
				specialkey: function(field, e){
					// e.HOME, e.END, e.PAGE_UP, e.PAGE_DOWN,
					// e.TAB, e.ESC, arrow keys: e.LEFT, e.RIGHT, e.UP, e.DOWN
					if (e.getKey() == e.ENTER) {
						this.grid.buscar();
					}
				}
			}
		});
		this.filtro = filtro;
		
		this.rutaIcono = "images/iconos/";
		
		this.botonEliminar = new Ext.Button({
			text:    'Eliminar',
			width:   70,
			icon:    this.rutaIcono + this.iconMaster + "_delete.png",
			scope:   this,
			handler: this.eliminar
		});
		
		this.botonActivar = new Ext.Button({
			text:    'Activar',
			width:   70,
			icon:    this.rutaIcono + this.iconMaster + "_add.png",
			scope:   this,
			hidden:  true,
			handler: this.activar
		});
		
		this.botonSwActivos = new Ext.Button({
			text:    'Activos',
			width:   70,
			icon:    this.rutaIcono + this.iconMaster + "_activos.png",
			enableToogle: true,
			pressed: true,
			scope:   this,
			handler: this.switchVista
		});
		
		this.botonSwTodos = new Ext.Button({
			text:    'Todos',
			width:   70,
			icon:    this.rutaIcono + this.iconMaster + "_todos.png",
			enableToogle: true,
			scope:   this,
			handler: this.switchVista
		});
		
		this.tbar= {
			style:"border-top:none;border-bottom:none;",
			items:[{
				text:    'Agregar',
				width:   70,
				icon:    this.rutaIcono + this.iconMaster + "_add.png",
				scope:   this,
				handler: this.agregar
			},{
				text:    'Editar',
				width:   70,
				icon:    this.rutaIcono + this.iconMaster + "_edit.png",
				scope:   this,
				handler: this.editar,
				buttonAlign:'right'
			},this.botonEliminar, this.botonActivar,"-", this.botonSwActivos, this.botonSwTodos,"->",filtro,' ',' ',' ']
		};
		
		this.bbar= new Ext.PagingToolbar({
			pageSize: miFacturaWeb.parametros.reg_pag_par,
			store: this.store,
			displayInfo: true,
			displayMsg: 'Mostrando {0} - {1} de {2}',
			emptyMsg: "No hay registros para mostrar",
			doRefresh: function() {
				this.ownerCt.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
				var filtro = this.ownerCt.filtro.getValue();
				var todos  = this.ownerCt.vertodos;
				this.store.baseParams ={filtro: filtro, vertodos: todos};
				this.store.grid = this.ownerCt;
				this.store.on('load',function(){
					this.grid.el.unmask();
				});
				this.store.on('exception',function(misc){
					this.grid.el.unmask();
				});
				Ext.PagingToolbar.prototype.doRefresh.apply(this);
			}
		});
		
		if (this.listeners == undefined) { this.listeners = {}; }
		Ext.applyIf(this.listeners,{
			activate:function(){
	    		if(this.activado){
	    			return;
	    		}
	    		this.activado=true;
	            this.bottomToolbar.doRefresh();
	        },
			rowdblclick: function(){
				this.editar();
			},
			rowclick: function(){
				this.editor.stopEditing(false);
			}
		});
		this.sm = new Ext.grid.RowSelectionModel({singleSelect:true});
		this.editor = new Ext.ux.grid.RowEditor({
			saveText: 'Guardar',
			cancelText: 'Cancelar',
			commitChangesText: 'Debe guardar o cancelar los cambios',
			errorSummary:  false
		});
		this.plugins = [ this.editor ];
		
		if (this.campoActivo){
			// Pone en color rojo los registros Inactivos 
			this.view = new Ext.grid.GridView({
				scope: this,
				getRowClass: function(row, index){
					return (eval("row.data."+ this.grid.campoActivo)) ? 'columna-activa' : 'columna-inactiva' ;
				}
			});
		}
		
		miFacturaWeb.CatalogoGridEditor.superclass.initComponent.call(this);
		
		this.editor.on({
			afteredit: function(e, o, rec, row){
				this.ownerCt.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
				this.ownerCt.store.save();
			},
			beforeedit: function(){
				this.ownerCt.topToolbar.disable();
				this.ownerCt.bottomToolbar.disable();
			},
			hide: function(){ // Se ejecuta al "Guardar" o "Cancelar"
				this.ownerCt.eliminaVacios();
				this.ownerCt.topToolbar.enable();
				this.ownerCt.bottomToolbar.enable();
			}
		});
		
		this.store.on({
			save: function(){
				this.grid.el.unmask();
			}
		});
		
		if (this.campoActivo){
			// Habilita/Deshabilita botones Eliminar/Activar segun el registro seleccionado 
			this.getSelectionModel().on({
				rowselect: function(sm, row, rec){
					if (eval("rec.data."+ this.grid.campoActivo) == 0) {
						this.grid.botonEliminar.hide();
						this.grid.botonActivar.show();
					} else {
						this.grid.botonEliminar.show();
						this.grid.botonActivar.hide();
					}
				}
			});
		}
	},
	getSelected: function(){
		var selModel = this.getSelectionModel();
		var selected = selModel.getSelected();
		if (selected == null){
			Ext.Msg.alert("Registro","Debe seleccionar un registro");
			return false;
		}
		return selected;
	},
	agregar: function(){
		var e = Ext.data.Record.create(this.campos);
		var indice = this.store.getCount();
		this.editor.stopEditing();
		this.store.insert(indice, new e({}));
		this.getView().refresh();
		this.getSelectionModel().selectRow(indice);
		this.editor.startEditing(indice);
    },
	eliminar: function(id){
		var selected = this.getSelected();
		if (!selected){ return;}
		Ext.Msg.show({
			scope: this,
			title: 'Eliminar',
			msg:   String.fromCharCode(191)+'Deseas eliminar el registro seleccionado?',
			buttons: Ext.Msg.YESNO,
			icon:   Ext.Msg.QUESTION,
			fn: function(btn){
				if (btn == 'yes') {
					this.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
					var rec = this.store.getById(selected.id);
					this.store.remove(rec);
					this.store.save();
					if (this.vertodos)  this.store.reload();
				}
			}
		});
	},
	editar: function(){
		var selected = this.getSelected();
        if (!selected) {
			return;
		}
		this.editor.stopEditing();
		this.getView().refresh();
		this.getSelectionModel().selectRow(selected);
		this.editor.startEditing(selected);
	},
	switchVista: function(obj){
		if (obj.text == 'Todos') {
			if (obj.pressed == false) {
				this.botonSwTodos.toggle();
				this.botonSwActivos.toggle();
				this.vertodos = 1;
				this.bottomToolbar.doRefresh();
			}
		} else {
			if (obj.pressed == false) {
				this.botonSwActivos.toggle();
				this.botonSwTodos.toggle();
				this.vertodos = 0;
				this.bottomToolbar.doRefresh();
				// Esconder boton "Activar" y mostrar "Eliminar"
				this.botonActivar.hide();
				this.botonEliminar.show();
			}
		}
	},
	activar: function(){
		Ext.Msg.show({
			scope: this,
			title: 'Activar',
			msg:   String.fromCharCode(191)+'Deseas activar el registro seleccionado?',
			buttons: Ext.Msg.YESNO,
			icon:   Ext.Msg.QUESTION,
			fn: function(btn){
				if (btn == 'yes') {
					Ext.Ajax.request({
						scope: this,
						url: this.urlActivar,
						params:  'campoId='+ eval("this.getSelectionModel().getSelected().data."+ this.campoId),
						success: function(){
							this.botonActivar.hide();
							this.botonEliminar.show();
							this.bottomToolbar.doRefresh();
						}
					});
				}
			}
		});
	}
});

Ext.reg('CatalogoGridEditor', miFacturaWeb.CatalogoGridEditor);