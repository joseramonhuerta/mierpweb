/*
 * File: formLineas.js
 * Date: Thu Feb 02 2017 23:47:25 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be generated the first time you export.
 *
 * You should implement event handling and custom methods in this
 * class.
 */
Ext.ns('miErpWeb');
formLineas = Ext.extend(formLineasUi, {
	configurarToolBar(){
		this.btnGuardar.on('click',function(){	
			this.hacerSubmit();
			
		},this);
		
		this.btnEliminar.on('click',function(){	
			this.eliminar();
			
		},this);
		
		this.btnDesactivar.on('click',function(){	
			this.cancelar();
		},this);
		
	},
	inicializarEventos: function(){
		this.on('cambioDeStatus',function(params){			
			var status=params.status;
			switch(status){
				case 'I':
					this.btnDesactivar.setIcon("images/iconos/"+this.iconMaster+"_green.png");
					this.btnDesactivar.setText("Activar");
				break;
				case 'A':
					this.btnDesactivar.setIcon("images/iconos/"+this.iconMaster+"_red.png");
					this.btnDesactivar.setText("Desactivar");
				break;
			}
		},this);
		
		this.on('cambioDeId',function(params){	
			var id=params.id;
			if (id==0){
				this.btnGuardar.setIcon('images/iconos/'+this.iconMaster+'_add.png');
			}else if (id>0){
				this.btnEliminar.setDisabled(false);
				this.btnDesactivar.setDisabled(false);
				this.btnGuardar.setIcon('images/iconos/'+this.iconMaster+'_edit.png');
			}			
		},this);
	},
    initComponent: function() {
        formLineas.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		this.txtIdLinea.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
		};
		
		this.txtNombreLinea.setValue=function(value){
			value=miErpWeb.formatearTexto(value);
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeNombre',value);
		};
		
		this.configurarToolBar();
		this.inicializarEventos();
    },
	cancelar:function(){
		this.el.mask(mew.mensajeDeEspera);
		Ext.Ajax.request({
			params: { 
				id_linea: this.txtIdLinea.getValue(),
				status:this.txtStatus.getValue()
			},
			scope:this,
		   	url: 'app.php/lineas/cambiarstatus',
		   	success: function(response, options){
				this.el.unmask();			
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==true){
						if (respuesta.data.status!=undefined){
							this.txtStatus.setValue(respuesta.data.status);
						}
				}
		   	},
		   	failure: function(){
		   		this.el.unmask();
		   	}		   
		});
	},
	hacerSubmit:function(){
		this.el.mask(mew.mensajeDeEspera);
		this.getForm().submit({
			url:'app.php/lineas/guardar',
			scope:this,
			success:function(){
				this.el.unmask();
				
				
			},
			failure:function(){ 	// ? ? m�sica maestro ? !  
				this.el.unmask();   //	? con cari�o!
			}	
		});
	},
	eliminar:function(btn){
		switch(btn){	//ESTE SWITCH ES USADO PARA ANALIZAR LO QUE TRATA DE HACER EL USUARIO, LA PRIERA VEZ DEBE ENTRAR A default:
    	case 'no':
    		return;
    	break;
    	case 'yes':
    		this.eliminar('borrar');
    		return;
    		break;
    	case 'borrar':
    		break;		//SALE DEL SWITCH Y SIGUE EJECUTANDOSE LA FUNCI�N
    	case undefined:	//AQUI ENTRA LA PRIMERA VEZ
    	case false:    		
    	default:
    		var me=this;    		
    		Ext.Msg.show({
 			   title:'Confirme por favor',
 			   msg: "¿Desea borrar el Linea?",
 			   buttons: Ext.Msg.YESNO,
 			   fn: function(btn){	    				
    				me.eliminar(btn);
    			},
 			   scope:this,
 			   icon: Ext.MessageBox.QUESTION
 			});
    		return;
		} 
		this.el.mask(mew.mensajeDeEspera);
		Ext.Ajax.request({
			params: { id_linea: this.txtIdLinea.getValue() },
			scope:this,
		   	url: 'app.php/lineas/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.fireEvent('eliminado',options.params.id_linea);
				MainContainer.tabContainer.remove(this);
		   	},
		   	failure: function(){
		   		this.el.unmask();
		   	}		   
		});
	},
	load:function(){
		var params={idLin:this.txtIdLinea.getValue()};
		this.el.mask(mew.mensajeDeEspera);
		this.getForm().load({
			params:params,
			url:'app.php/lineas/obtenerlinea',
			scope:this,
			success:function(form ,action){
				this.el.unmask();
			},
			failure:function(){
				MainContainer.remove(this);	
			}
		});
			
	},
	listeners:{
    	activate:function(){
    		if (this.activado==true){
    			return;
    		}
    		this.activado=true;
    		
    		if (this.idValue!=undefined && this.idValue!=0){
    			this.txtIdLinea.setValue(this.idValue);
				this.load();
			} 
				
					
    	},
    	actioncomplete:function(form,action){
    		var respuesta=Ext.decode(action.response.responseText);			
			if (respuesta.success==true){
				var linea = respuesta.data.Linea;
				linea.nombre_linea=miErpWeb.formatearTexto(linea.nombre_linea);
				
				form.setValues(linea);
				
			}
					
    	},
    	cambioDeNombre:function(nombre){
			this.setTitle(Ext.util.Format.ellipsis(this.idValue+'-'+nombre,25,true));
		},
    	cambioDeId:function(params){
    		var id=params.id;
    		this.idValue=id;
    		if (id==0){
				this.setIconClass(Ext.ux.TDGi.iconMgr.getIcon(this.iconMaster+"_add"));
			}else if (id>0){
				this.setIconClass(Ext.ux.TDGi.iconMgr.getIcon(this.iconMaster+"_edit"));				
			}
					
    	}
    }
});
Ext.reg('formLineas', formLineas);