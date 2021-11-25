Ext.ns('miFacturaWeb');
Ext.ns('miFacturaWeb.CatalogoGrid');



/*******************************************************************************************************
                     <-----                 GRID               ----->
********************************************************************************************************/
miFacturaWeb.CatalogoGrid = Ext.extend(Ext.grid.EditorGridPanel, {
    //xtypeForm:'',               //<---------------form de edicion
    //tituloNew:"Nuevo",          //<---------------Titulo a mostrar en el tab al crear un nuevo registro
    //campoAmostrar:"nombre",     //<---------------El contenido de este campo se mostrará en el titulo del tab cuando este en modo de edicion
    //campoId:"id",               //<---------------PK del registro,
    //--mensajeEditar:'Seleccione el registro a editar, gracias',
 //   frame: false,
  //  border:false,
  //  style:"border-top:none;",
    bodyStyle:'border-width:1px 0 0 0;',
    root: 'data',
    totalProperty: 'totalRows',
    messageProperty: "msg" ,
    textoMenu:'',
    buscar:function(){         
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
        
		var rutaIcono="images/iconos/";
		
		this.botonSwActivos = new Ext.Button({
			text:    'Activos',
			width:   70,
			icon:    rutaIcono + this.iconMaster + "_activos.png",
			enableToogle: true,
			pressed: true,
			scope:   this,
			handler: this.switchVista
		});
		
		this.botonSwTodos = new Ext.Button({
			text:    'Todos',
			width:   70,
			icon:    rutaIcono + this.iconMaster + "_todos.png",
			enableToogle: true,
			scope:   this,
			handler: this.switchVista
		});
		
        this.tbar = {
			style:"border-top:none;border-bottom:none;",
			items:[{
				text:    'Agregar',
				width:   70,
				icon:    rutaIcono + this.iconMaster+"_add.png",
				scope:   this,
				handler: this.agregar
			},{
				text:    "Editar",
				width:   70,
				icon:    rutaIcono+this.iconMaster+"_edit.png",
				scope:   this,
				buttonAlign:'right',  
				handler: this.editar
			}]
		};

		this.bbar= new Ext.PagingToolbar({
			pageSize: 10,
			store:    this.store,
			displayInfo: true,
			displayMsg: 'Mostrando {0} - {1} de {2}',
			emptyMsg:   "No hay registros para mostrar",
			doRefresh: function(){
				vertodos = this.ownerCt.vertodos;
				this.ownerCt.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
				var filtro=this.ownerCt.filtro.getValue();
				this.store.baseParams ={ filtro: filtro, vertodos: vertodos };
				this.store.grid=this.ownerCt;
				this.store.on('load',function(){                        
					this.grid.el.unmask();
				});
				this.store.on('exception',function(misc){                        
					this.grid.el.unmask();
				});
				Ext.PagingToolbar.prototype.doRefresh.apply(this);
			}            
		});
		
		if (this.listeners == undefined)  this.listeners = {};
		Ext.applyIf(this.listeners,{
			activate:function(){
	    		if(this.activado){
	    			return;
	    		}
	    		this.activado=true;
	            this.bottomToolbar.doRefresh();
	        },
			rowdblclick:function(){
				this.editar();

			}
		});
		this.sm= new Ext.grid.RowSelectionModel({singleSelect:true});
		miFacturaWeb.CatalogoGrid.superclass.initComponent.call(this);
		
		/* Verifica si se agregan al toolbar los botones Activos/Todos */
		if (this.verSwitchActivos != undefined) {
			this.topToolbar.addSeparator();
			this.topToolbar.addItem(this.botonSwActivos);
			this.topToolbar.addItem(this.botonSwTodos);
		}
		this.topToolbar.addFill();
		this.topToolbar.addItem(this.filtro);
		this.topToolbar.addSpacer();
		this.topToolbar.addSpacer();
		this.topToolbar.addSpacer();
		/*
		if (this.campoActivo){
			// Pone en color rojo los registros Inactivos 
			this.view = new Ext.grid.GridView({
				scope: this,
				getRowClass: function(row, index){
					return (eval("row.data."+ this.grid.campoActivo)) ? 'columna-activa' : 'columna-inactiva' ;
				}
			});
		}*/
		
	},
	getSelected:function(){
		var selModel=this.getSelectionModel();
		var selected=selModel.getSelected();
		if (selected==null){
			Ext.Msg.alert("Editar","Seleccione el registro a editar");
			return false;
		}
		return selected;
	},
	agregar:function(){
		var idSelected=0;
		var tituloTab=this.tituloNew;
		this.crearForm(tituloTab,"add", idSelected);
	},
	borrar:function(id){
		var index=this.getStore().find(this.campoId,id);
		var rec=this.store.getAt(index);
		this.getStore().remove(rec);
	},
    add:function(){
		// Sin funcionalidad
    },
	editar:function(){
		var selected=this.getSelected();
		if (!selected) return;
		var mask = new Ext.LoadMask(this.el, {msg:miFacturaWeb.mensajes.mensajeDeEspera});
		this.mask=mask;
		mask.show();
		var idProperty=this.store.idProperty;
		var idSelected=selected.get(idProperty);
		var tituloTab=selected.get(this.campoAmostrar);
		this.crearForm(tituloTab,"edit", idSelected);
	},
	
	//ESTA FUNCION REALIZA OPERACIONES QUE NO LE CORRESPONDEN AL GRID, TALVEZ DEBO CREAR UN TERCER OBJETO (TALVEZ EXTENDER EL PANEL )
	crearForm:function(tituloTab,estado,idSelected){
		(idSelected == undefined) ? idSelected = 0 : idSelected = idSelected;
		
		if (this.xtypeForm==undefined){
			Ext.Msg.alert("Error","No se ha definido el formulario a cargar.");
			return;
		}
		var tabContainer = Ext.getCmp('tabContainer');
		/* Buscar el TAB usando selectores CSS */ 
		var tabCls=this.moduloText+'-form-'+idSelected;
		/* Con esta clase identifico al tab, modulo+"-form-"+id_registro,
		*  el tercer parámetro cambia de valor cuando guardo un registro nuevo (ver CatalogoForm.actualizarTab() ) */
		var elTabPanel=tabContainer.getEl();
		var tabEl=elTabPanel.child('.'+tabCls);
		if (tabEl){
			tabEl.setStyle('background-image:url(images/icons/add.png)');
			var idTab=tabEl.id;
			var tabCmp=Ext.getCmp(idTab);
			tabContainer.setActiveTab(tabCmp);
			this.mask.hide();
			return;
		}
		
		var rutaIcono="images/iconos/";
		
		// Determina como muestra el boton Activar/Desactivar y su separador
		var hideActivarDesactivar = false;
		var separador = '';
		if (this.campoActivo == undefined){
			hideActivarDesactivar = true;
		} else {
			separador = '-';
		}
		
		var botonEliminar = new Ext.Button({
			text: 'Eliminar',
			icon: rutaIcono + this.iconMaster + "_delete.png",
			disabled: true,
			handler: function(){
				var toolbar=this.ownerCt;
				var tab=toolbar.ownerCt;
				var form=tab.items.items[0];
				form.eliminar();
			}
		});
		
		var botonActivar = new Ext.Button({
			text: '.....',  // creo el boton sin texto ni icono, los asigno mas adelante
			icon: '',
			disabled: true,
			hidden: hideActivarDesactivar,
			activo: status,
			handler: function(){
				var toolbar = this.ownerCt;
				var tab  = toolbar.ownerCt;
				form.activar();
			}
		});
		
		var iconButonAdd;
		if (idSelected != 0){
			iconButonAdd = rutaIcono + this.iconMaster + "_edit.png";
		}else{
			iconButonAdd = rutaIcono + this.iconMaster + "_add.png";
		}
        
		var botonAgregar = new Ext.Button({
			text: 'Guardar',
			type: 'submit',
			icon: iconButonAdd,
			handler: function(){
				var toolbar=this.ownerCt;
				var tab=toolbar.ownerCt;
				var form=tab.items.items[0];
				form.hacerSubmit();
			}
		});
		
		var tab=tabContainer.add({  // No lo encontró, agregará uno nuevo
			iconCls:  Ext.ux.TDGi.iconMgr.getIcon(this.iconMaster),
			iconMaster: this.iconMaster,
			grid:   this,
			autoScroll: true,
			frame:  false,
			cls:tabCls+' x-panel-mc',
			border: true,
			bodyStyle: 'border-top:none;border-left: none;border-right:none;',
			style:'padding-top:0;',
			title:  Ext.util.Format.ellipsis(tituloTab,25,true),
			botonEliminar: botonEliminar,
			botonAgregar:  botonAgregar,
			botonActivar:  botonActivar,
			listeners:{
				render:function(){
					var height = tabContainer.body.getHeight(true);
					//this.items.items[0].setHeight(height);
				}
                                
			},
			tbar:{
				style:'border-top: none;border-left:none;border-right:none;',
				items:[botonAgregar,botonEliminar,separador,botonActivar]
			},
			items: [{
				xtype: this.xtypeForm,
				selector: tabCls,    //<--Esta clase fue agregada al tab para identidicarlo
				iconMaster: this.iconMaster,
				grid: this,
				moduloText:this.moduloText,
				campoAmostrar:this.campoAmostrar,
				campoId:this.campoId,
				idValue:idSelected
			}],
			closable: true
		});
		tab.show();
                
                
		form = tab.items.items[0];
		if (idSelected!=0){
                    tab.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
			form.editar(form,idSelected);            
		} else {
			tab.setIconClass(Ext.ux.TDGi.iconMgr.getIcon(this.iconMaster+"_add"));
		}
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
			}
		}
	}
});

Ext.reg('CatalogoGrid', miFacturaWeb.CatalogoGrid);

