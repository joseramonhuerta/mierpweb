Ext.ns('miFacturaWeb.CatalogoForm');
Ext.ns('miFacturaWeb.FormatedTreePanel');

miFacturaWeb.CatalogoForm = Ext.extend(Ext.FormPanel, {
	frame:  false,
	border: false,
	formatear:   true,
	autoDestroy: true,
	bodyStyle: 'padding:8px;',
	style:'padding-top:0;',
	cls:'x-panel-mc',
	nuevo: function(){
		Ext.Msg.alert("Mi Factura","TODAVIA NO IMPLEMENTADO");
	},
	editar: function(form,id){
		var parametro={};         
		eval("parametro."+this.campoId+"= id"); //<---Agrego la propiedad dinamicamente
		form.load({
			url:this.urlGet,
			method: 'POST',          
			params: parametro
		});
	},
    aceptarEliminar: function(respuesta){
        if (respuesta!='yes')return;
		this.ownerCt.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
        var basicForm=this.getForm();
        
        var ID=basicForm.items.items[0].getValue();
		
        var parametro={};
        eval("parametro."+basicForm.campoId+"= ID"); //<--Agrego la propiedad dinamicamente
		
		Ext.Ajax.request({
			params:parametro,
			url: this.urlDel,
			scope:this,
			success: function(response){
				var responseData = Ext.util.JSON.decode(response.responseText);
				if (this.grid.vertodos){
					this.grid.bottomToolbar.doRefresh();
				} else {
					var grid = basicForm.ownerCt.grid;
					grid.borrar(ID);
				}
				var tab=this.ownerCt;
				var tab_panel = Ext.getCmp('tabContainer');
				tab_panel.remove(tab);
			}
		});
    },
    eliminar:function(){
        Ext.Msg.show({
            title:'Eliminar',
            msg: '¿Desea eliminar el registro seleccionado?',
            buttons: Ext.Msg.YESNO,
            scope:this,
            fn: this.aceptarEliminar,
            animEl: 'elId',
            icon: Ext.MessageBox.QUESTION
        });
    },
	activar: function(){
		var campo_id  = this.ownerCt.grid.campoId;
		var status    = this.textFieldActivo.getValue();
		var id_reg    = this.textFieldId.getValue();
                var texto;
                var icono;
                var text2;
		if (status == 1) { 
			 texto = "Desactivar";
			icono = "images/iconos/"+ this.ownerCt.iconMaster +"_play.png";
			text2 = "Activar";
		} else {
			texto = "Activar";
			icono = "images/iconos/"+ this.ownerCt.iconMaster +"_stop.png";
			text2 = "Desactivar";
		}
		
		Ext.Msg.confirm(texto,'¿Desea '+ texto.toLowerCase() +' el registro actual?',function(btn){
			if (btn == 'yes'){
				this.ownerCt.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
				Ext.Ajax.request({
					url: this.urlAct,
					scope:  this,
					params: {id: id_reg, status: status},
					success: function(){
						status = (status == 1) ? 0 : 1;
						this.textFieldActivo.setValue(status);
						this.ownerCt.botonActivar.setIcon(icono);
						this.ownerCt.botonActivar.setText(text2);
						this.ownerCt.grid.bottomToolbar.doRefresh(); // refresca el grid
						this.ownerCt.el.unmask();
					}
				});
			}
		},this);
	},
    hacerSubmit:function(){
        var form=this.getForm();
                
        form.submit({
            scope:this,
            failure: function (form, action) {

                if (action.failureType=="client"){
                    App.setAlert("Error", "Revise los campos marcados.");
                    return;
                }

            },
            success: function (form, request) {
                try{
                    var responseData = Ext.util.JSON.decode(request.response.responseText);
                }catch(err){
					Ext.Msg.alert("Error decodificando la respuesta del servidor","Intente de nuevo o consulte al administrador del sistema.");       	
					return;
                }
				
                var valores=responseData.data;
                var fields=this.reader.meta.fields;

                var campo;
                var campos=new Array();
                for(i=0;i<fields.length;i++){
                    campo=fields[i];
                    name=campo['name'];
                    map=(campo['mapping']==undefined)?campo['name']:campo['mapping'];
                    valor=valores[map];
                    valor=miFacturaWeb.formatear(valor,campo);
                    campos[i]={id:name,value:valor};
                }
				form.setValues(valores);

                
                this.idValue=responseData.data[this.campoId];

                this.actualizarTab(responseData.data[this.campoId],responseData.data[this.campoAmostrar]);
            }
        });
    },
    actualizarTab:function(idValue,tituloNuevo){ //<--Pone al tab, el icono de edición y actualiza el título
        
        var owner=this.ownerCt;
        owner.setTitle(Ext.util.Format.ellipsis(miFacturaWeb.formatearTexto(tituloNuevo),25,true)); //<--------------Titulo nuevo establecido

        /*
         *          PARA EVITAR ABRIR EL MISMO FORMULARIO MAS DE UNA VEZ
         *          AGREGO UN CSS CON EL CUAL PUEDO IDENTIFICAR AL FORM, ESTE CSS ES UNICO ES COMO MANEJAR IDS PERO CREO QUE LOS
         *          IDS NO PUEDO CAMBIARLOS DINAMICAMENTE
         */
        var form=this.getForm();
        var selectorOld=form.selector;        
        owner.removeClass(selectorOld); //<--QUITÉ EL CSS 
       
        var selectorNuevo=form.moduloText+'-form-'+idValue;
        form.selector=selectorNuevo;                //<--GUARDO LA REFERENCIA AL CSS
        owner.addClass(selectorNuevo);                  //<-- CSS ACTUALIZADO

        /*********************************************************************************************************************
                 *AHORA VOY A CAMBIARLE EL ICONO A EDICION
        *********************************************************************************************************************/
      
		owner.setIconClass(Ext.ux.TDGi.iconMgr.getIcon(owner.iconMaster+"_edit"));
		
		form.ownerCt.botonAgregar.setIconClass("");
		var icono="images/iconos/"+owner.iconMaster+"_edit.png";
		form.ownerCt.botonAgregar.setIcon(icono);
    },
    initComponent: function() {
        this.addClass('x-panel-mc');
        this.initialConfig.url = this.url;
        this.initialConfig.urlGet = this.urlGet;
        this.initialConfig.urlDel = this.urlDel;
        this.initialConfig.reader= this.reader;
        this.initialConfig.iconMaster= this.iconMaster;
        this.initialConfig.moduloText= this.moduloText;
        this.initialConfig.campoAmostrar= this.campoAmostrar;
        this.initialConfig.campoId= this.campoId;
        this.initialConfig.idValue= this.idValue;
        this.initialConfig.setValues=function(valores){
            // var valores=responseData.data;
            if (this.reader==undefined){
                Ext.form.BasicForm.prototype.setValues.call(this,valores);
                return;
            }
            var fields=this.reader.meta.fields;
            var campo;
            var campos=new Array();
            var name;

            for(i=0;i<fields.length;i++){
                if (fields[i]!=undefined ){
                    campo=fields[i];
                    if( campo['name']!=undefined){
                        name=campo['name'];
                        map=(campo['mapping']==undefined)?campo['name']:campo['mapping'];
                        valor=valores[map];
                        valor=miFacturaWeb.formatear(valor,campo);
                        campos[i]={
                            id:name,
                            value:valor
                        };
                    }
                }
            }
            Ext.form.BasicForm.prototype.setValues.call(this,campos);
        };
        if (this.listeners == undefined)  this.listeners = {};
        Ext.applyIf(this.listeners,{
             beforeaction: function(form){
                
                form.ownerCt.getEl().mask(miFacturaWeb.mensajes.mensajeDeEspera);
            },
            actionfailed: function(form,action){
                
                form.ownerCt.getEl().unmask();
            },
            actioncomplete: function(form,action){
                form.ownerCt.getEl().unmask();
				if (this.ownerCt.grid.campoActivo != undefined){
					var json_data = action.result.data;
					if (json_data.ActivoTasa == 1){
						this.ownerCt.botonActivar.setIcon("images/iconos/"+ this.ownerCt.iconMaster +"_stop.png");
						this.ownerCt.botonActivar.setText("Desactivar");
					} else {
						this.ownerCt.botonActivar.setIcon("images/iconos/"+ this.ownerCt.iconMaster +"_play.png");
						this.ownerCt.botonActivar.setText("Activar");
					}
				}
                form.ownerCt.botonEliminar.enable();
				form.ownerCt.botonActivar.enable();
                form.ownerCt.getEl().unmask();
                if (this.grid.mask!=undefined){
                    this.grid.mask.hide();
                }
            }
        });
        miFacturaWeb.CatalogoForm.superclass.initComponent.call(this);
    }
});

