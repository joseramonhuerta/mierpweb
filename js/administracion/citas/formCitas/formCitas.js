/*
 * File: formCitas.js
 * Date: Tue Apr 17 2018 14:55:08 GMT-0600 (Hora verano, Montañas (México))
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
Ext.ns('mew');
formCitas = Ext.extend(formCitasUi, {
    inicializarStores:function(){
						
		this.cmbCliente.store =  new miErpWeb.storeFormCitasClientes();		
		
		this.cmbAgente.store =  new miErpWeb.storeFormCitasAgentes();
		
		this.cmbHorario.store =  new miErpWeb.storeFormCitasHorarios();
		
	},
	inicializarEvents:function(){
		var me = this;
		this.frmMain.on('actioncomplete',function(form,action){
			 if (action.result.success){
				 this.cargarDatos(action.result.data);
				 
			 }else{				
				return false;
			}			
		}, this);	
		
		
		this.cmbCliente.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbCliente.onTriggerClick = function(a, e){
			if(e){
				if(e.getAttribute('class').indexOf('x-form-clear-trigger') > -1){
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}
					if(!Ext.isEmpty(me.cmbCliente.getValue())){
						this.reset();
						
						// this.deshabilitarBtns(true);
						// this.cntActivo.setVisible(false);
						// this.spExcel.setVisible(false);
						//this.reloadGrid(null, 0);
					}
				}else{
					if(this.readOnly || this.disabled){
						return;
					}
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}else {
						this.onFocus({});
						if(this.triggerAction == 'all') {
							this.doQuery(this.allQuery, true);
						} else {
							this.doQuery(this.getRawValue());
						}
						this.el.focus();
					}
				} 
			}
		};
		
		this.cmbAgente.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbAgente.onTriggerClick = function(a, e){
			if(e){
				if(e.getAttribute('class').indexOf('x-form-clear-trigger') > -1){
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}
					if(!Ext.isEmpty(me.cmbAgente.getValue())){
						this.reset();
						
						// this.deshabilitarBtns(true);
						// this.cntActivo.setVisible(false);
						// this.spExcel.setVisible(false);
						//this.reloadGrid(null, 0);
					}
				}else{
					if(this.readOnly || this.disabled){
						return;
					}
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}else {
						this.onFocus({});
						if(this.triggerAction == 'all') {
							this.doQuery(this.allQuery, true);
						} else {
							this.doQuery(this.getRawValue());
						}
						this.el.focus();
					}
				} 
			}
		};
		
		this.cmbHorario.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbHorario.getStore().on('beforeload',function(){
			var fecha = this.txtFecha.getValue();
			fecha=fecha.format('Y-m-d'); 
			this.cmbHorario.store.baseParams.id_empresa=miErpWeb.Empresa[0].id_empresa;
			this.cmbHorario.store.baseParams.id_sucursal=miErpWeb.Sucursal[0].id_sucursal;
			this.cmbHorario.store.baseParams.id_agente= Ext.num(this.cmbAgente.getValue(), 0);
			this.cmbHorario.store.baseParams.fecha = fecha;	
			this.cmbHorario.store.baseParams.id_cita = Ext.num(this.txtIdCita.getValue(), 0);
		},this);	
		
		this.cmbHorario.onTriggerClick = function(a, e){
			if(e){
				if(e.getAttribute('class').indexOf('x-form-clear-trigger') > -1){
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}
					if(!Ext.isEmpty(me.cmbHorario.getValue())){
						this.reset();
						
						// this.deshabilitarBtns(true);
						// this.cntActivo.setVisible(false);
						// this.spExcel.setVisible(false);
						//this.reloadGrid(null, 0);
					}
				}else{
					if(this.readOnly || this.disabled){
						return;
					}
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}else {
						this.onFocus({});
						if(this.triggerAction == 'all') {
							this.doQuery(this.allQuery, true);
						} else {
							this.doQuery(this.getRawValue());
						}
						this.el.focus();
					}
				} 
			}
		};
		
		this.cmbAgente.on('select',function(combo, record, index){
			this.cmbHorario.clearValue();
		},this);		
		
		this.on('cambioDeId',function(params){	
			var id=params.id;
			if (id==0){
				this.btnGuardar.setIcon('images/iconos/'+this.iconMaster+'_add.png');
			}else if (id>0){
				// this.btnEliminar.setDisabled(false);
				//this.btnDesactivar.setDisabled(false);
				this.btnGuardar.setIcon('images/iconos/'+this.iconMaster+'_edit.png');
			}			
		},this);
		
		this.btnGuardar.on('click', function(){
			this.guardar();
		}, this );
		
		this.btnEliminar.on('click',function(){	
			this.eliminar();
			
		},this);		
				
	},
	inicializarRenders:function(){
		
				
	},
	initComponent: function() {
        formCitas.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdCita.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };
		
		this.inicializarStores();
		this.inicializarEvents();
		// this.inicializarRenders();
    },
	cargarDatos:function(data){
		if (data.Cita==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos de la cita',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			// miErpWeb.tabContainer.remove(this);	
			
			return;
		}
		var cita=data.Cita;
		
		var form=this.frmMain.getForm();		
        // form.setValues(movimiento);
		this.txtIdCita.setValue(cita.id_cita);
        var fechaMov=cita.fecha;
	
		var dt = Date.parseDate(fechaMov, "d/m/Y H:i:s");
	       
        this.txtFecha.setValue(dt);
      	this.txtObservaciones.setValue(cita.observaciones);		
		
		
		
		if (cita.id_cita==0){
			
			
		}else{/*	SI LA FACTURA YA EXISTE EN EL SERVIDOR, SE ESTABLECE UN NUEVO TITULO Y EL ICONO DEL TAB			*/
						
			
			
			this.btnEliminar.setDisabled(false);
			
			
			this.setTitle(cita.id_cita+"-"+cita.nombre_fiscal);
						
		}
		
		
	
		var cliente={
			id_cliente:cita.id_cliente,
			nombre_fiscal:cita.nombre_fiscal
		};
			
		this.cmbCliente.store.loadData({data:cliente});
		this.cmbCliente.setValue(cliente.id_cliente);
		
		var agente={
			id_agente:cita.id_agente,
			nombre_agente:cita.nombre_agente
		};
			
		this.cmbAgente.store.loadData({data:agente});
		this.cmbAgente.setValue(agente.id_agente);
		
		var horario={
			id_horario:cita.id_horario,
			descripcion_horario:cita.descripcion_horario
		};
			
		this.cmbHorario.store.loadData({data:horario});
		this.cmbHorario.setValue(horario.id_horario);

		this.el.unmask();	
	},
	guardar:function(){
		if (this.frmMain.getForm().isValid()){
			
			var fecha = this.txtFecha.getValue();
			fecha=fecha.format('Y-m-d');   
			var params={};
			//params['Movimiento[id_empresa]'] = this.txtIdMovimientoAlmacen.getValue();
			params['Cita[id_empresa]'] = miErpWeb.Empresa[0].id_empresa;
			params['Cita[id_sucursal]'] = miErpWeb.Sucursal[0].id_sucursal;
			params['Cita[id_cita]'] = this.txtIdCita.getValue();
			params['Cita[fecha]'] = fecha; 
			params['Cita[id_horario]'] =this.cmbHorario.getValue();
			params['Cita[id_cliente]'] = this.cmbCliente.getValue();			
			params['Cita[id_agente]'] = this.cmbAgente.getValue();	
			params['Cita[observaciones]'] = this.txtObservaciones.getValue();	
			params['Cita[status]'] = this.txtStatus.getValue();		

			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/citas/save',
				success:function(){
					this.el.unmask();
				},
				failure:function(form, action){
					switch (action.failureType) {
		            case Ext.form.Action.CLIENT_INVALID:		                
		                msg="Favor de revisar los campos marcados";
		                icon=Ext.MessageBox.WARNING;
		                break;
		            case Ext.form.Action.CONNECT_FAILURE:		                
		                msg="Error en la comunicación ajax, intente de nuevo";
		                icon=Ext.MessageBox.ERROR;
		                break;
		            case Ext.form.Action.SERVER_INVALID:
		                icon=Ext.MessageBox.ERROR;
		                msg=action.result.msg;
					}
					Ext.Msg.show({
					   title:'Error',
					   msg: msg,
					   buttons: Ext.Msg.OK,						  						   
					   icon: icon
					});
					this.el.unmask();
					}

				});
				
			
		}else{
			return;
			
		}	
		
		
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
 			   msg: "¿Desea borrar la cita?",
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
			params: { id_cita: this.txtIdCita.getValue() },
			scope:this,
		   	url: 'app.php/citas/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.fireEvent('eliminado',options.params.id_cita);
				MainContainer.tabContainer.remove(this);
		   	},
		   	failure: function(){
		   		this.el.unmask();
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
    			this.txtIdCita.setValue(this.idValue);
				//this.el.mask(mfw.mensajeDeEspera);    			
    		}
                          
			this.frmMain.load({
				params:{idCit:this.idValue,
						id_empresa:miErpWeb.Empresa[0].id_empresa,
						id_sucursal:miErpWeb.Sucursal[0].id_sucursal,
						id_almacen:miErpWeb.Almacen[0].id_almacen				
				},
				url:'app.php/citas/obtenercita'
			});
			
			return false;
					
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
Ext.reg('formCitas', formCitas);