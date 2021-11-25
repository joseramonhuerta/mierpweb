/*
 * File: formTurnos.js
 * Date: Sun Oct 29 2017 20:15:37 GMT-0700 (Hora estándar Montañas (México))
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
formTurnos = Ext.extend(formTurnosUi, {
	total:0.00,
	renderPrecio:function(val,x,rec){
		return "$"+Ext.util.Format.monedaConSeparadorDeMiles(val);
	},
	inicializarRenders:function(){
		var colModel=this.gridDetalles.getColumnModel();
		var columna=colModel.getColumnById('colTotal');
        columna.renderer=this.renderPrecio
	},	
	inicializarStores:function(){
			this.gridDetalles.store=new miErpWeb.storeFormTurnosGrid();
			this.cmbDenominacion.store =  new miErpWeb.storeFormTurnosDenominaciones();
			this.cmbFormaPago.store =  new miErpWeb.storeFormTurnosFormasPagos();
	},
	inicializarEvents:function(){
		this.frmMain.on('actioncomplete',function(form,action){
			 if (action.result.success){
				 this.cargarDatos(action.result.data);				 
			 }else{				
				return false;
			}			
		}, this);	
		
		this.btnAgregar.on('click', function(){
			this.agregarDetalle();
		}, this);		
			
		this.gridDetalles.getColumnModel().getColumnById("colDelete").renderer = function(v, m, rec){
			value = "<img class='btnEliminarDetalle' src='images/iconos/grid_chico_borrar.png' style='cursor:pointer;' />";
			return value;
		}
		
		this.gridDetalles.on("cellclick", function(Grid, rowIndex, columnIndex, e){
			var imgEl = Ext.get(e.getTarget());

			if(imgEl.hasClass("btnEliminarDetalle")){
				var record = this.gridDetalles.getStore().getAt(rowIndex);

				Ext.MessageBox.show({
					scope: this,
					title: "Aviso",
					msg: "Est&aacute; seguro de eliminar este detalle?",
					width: 320,
					buttons: Ext.Msg.YESNO,
					fn: function(btn){
						if(btn == "yes"){
							this.gridDetalles.getStore().removeAt(rowIndex);
							this.gridDetalles.getSelectionModel().selectRow(0);
							// this.gridDetalles.getStore().reload();
							this.calcularTotales();						
						}
						
					}
				});				
			}
				
		}, this);	

		this.btnGuardar.on('click', function(){
			this.guardar();
		}, this );
		
		this.btnEliminar.on('click',function(){	
			this.eliminar();
			
		},this);
	
		this.btnImprimir.on('click', function(){
			this.imprimir();
		}, this);
		
	},
	initComponent: function() {
        formTurnos.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdTurno.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };
		
		this.inicializarStores();
		this.inicializarEvents();
		this.inicializarRenders();
    },
	cargarDatos:function(data){
		if (data.Turno==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos del turno',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			return;
		}
		var turno=data.Turno;
		
		var form=this.frmMain.getForm();		
        this.txtIdTurno.setValue(turno.id_turno);
		// this.txtStatus.setValue(turno.status);
		var fechaTur=turno.fechainicio;
		var dt = Date.parseDate(fechaTur, "d/m/Y H:i:s");
	    this.txtFecha.setValue(dt);
		this.txtHora.setValue(dt.format('H:i:s A'));
       
		this.txtConcepto.setValue(turno.concepto);		
	
		var total = turno.total;
				
		this.lblTotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total));
		
		if (turno.id_turno>0){				
			this.btnEliminar.setDisabled(false);
			this.btnImprimir.setDisabled(false);
			this.setTitle(turno.id_turno+"-"+turno.concepto);
		}
		
		var detalles=data.Detalles;
        if(detalles!=undefined){			
            this.gridDetalles.store.loadData({
                data:detalles
            });
            this.calcularTotales();
        }	

		this.el.unmask();	
	},
	agregarDetalle: function(){		
			if(this.cmbFormaPago.getRawValue()==""){
				Ext.Msg.alert('Aviso', 'Seleccione la forma de pago.', function(){
					// this.btnAgregar.enable();
					this.cmbFormaPago.focus(false, true);
				}, this);
				return;
			}
			
			if(this.cmbDenominacion.getRawValue()==""){
				Ext.Msg.alert('Aviso', 'Seleccione la denominacion.', function(){
					// this.btnAgregar.enable();
					this.cmbDenominacion.focus(false, true);
				}, this);
				return;
			}
			
			if(this.txtCantidad.getValue()=="" || this.txtCantidad.getValue() == 0){
				Ext.Msg.alert('Aviso', 'Introduzca la cantidad.', function(){
					// this.btnAgregar.enable();
					this.txtCantidad.focus(false, true);
				}, this);
				return;
			}
			var existe = false;
			var denominacion = this.cmbDenominacion.getRawValue();
			var formapago = this.cmbFormaPago.getValue();
			var cantidad = 0;
			var total = 0;
			
			var detalles = this.gridDetalles.getStore().getRange();
			indexDetalle = detalles.length;
			
			for(var x = 0; x<detalles.length; x++){
				if(detalles[x].data.id_formapago == formapago && detalles[x].data.denominacion == denominacion ){
					existe = true;
					indexDetalle = x;
					cantidad = detalles[x].data.cantidad;				
					total = detalles[x].data.total;					
				}	
			}			
			
			if(existe == false){			
				var record = new this.gridDetalles.store.recordType({
					id_formapago: this.cmbFormaPago.getValue(),
					nombre_formapago: this.cmbFormaPago.getRawValue(), 
					id_denominacion: this.cmbDenominacion.getValue(),
					denominacion: this.cmbDenominacion.getRawValue(), 
					cantidad: this.txtCantidad.getValue(),
					total: 	 this.cmbDenominacion.getRawValue() * this.txtCantidad.getValue()	
				}, Ext.id());			
				this.gridDetalles.getStore().insert(0,record);
			}else{
				var record = this.gridDetalles.getStore().getAt(indexDetalle);
				
				var can = cantidad + this.txtCantidad.getValue();
				var tot = total + (this.cmbDenominacion.getRawValue() * this.txtCantidad.getValue());
								
				record.set("cantidad",can);			
				record.set("total",tot);
				
			
											
				this.gridDetalles.getStore().commitChanges();	
				
			}
			this.calcularTotales();
			this.frmDetalles.getForm().reset();
			this.cmbFormaPago.focus(true, 0);
			this.txtCantidad.setValue(0);		
	},
	calcularTotales:function(){
		var i=0;
		var numrecs=this.gridDetalles.store.data.length;		
		var total=0;						
		for (i=0; i<numrecs; i++){
			rec=this.gridDetalles.store.getAt(i);		
			total+=	parseFloat( rec.data.total );					
		}
		this.lblTotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total));
		this.total = total;		

		this.txtTotal.setValue(total);	
	},
	renderMoneda:function(val){
		if (val<0){
			return "-$" + Ext.util.Format.monedaConSeparadorDeMiles(val*-1);
		}else{
			return "$" + Ext.util.Format.monedaConSeparadorDeMiles(val);
		}
		
	},
	guardar:function(){
		if (this.frmMain.getForm().isValid()){
			var detalles=gridToJson(this.gridDetalles);
			var fecha = this.txtFecha.getValue();
			fecha=fecha.format('Y-m-d');   
;   
			var params={};
			params['Turno[id_turno]'] = this.txtIdTurno.getValue();
			params['Turno[id_empresa]'] = miErpWeb.Empresa[0].id_empresa;
			params['Turno[id_sucursal]'] = miErpWeb.Sucursal[0].id_sucursal;
			params['Turno[fecha]'] = fecha; 
			params['Turno[hora]'] =this.txtHora.getValue();
			params['Turno[concepto]'] = this.txtConcepto.getValue();
			params['Turno[total]'] = this.txtTotal.getValue();
			params['Turno[status]'] = this.txtStatus.getValue();
			params['Detalles']=detalles;
			
			
			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/turnos/save',
				success:function(data, options){
					this.el.unmask();
					
					
				},
				failure:function(form, action){
					
					this.el.unmask();
					}
				});
				
			
		}else{
			return;
			
		}	
	},
	limpiar: function(){
		Ext.MessageBox.show({
			scope: this,
			title: "Aviso",
			msg: "Est&aacute; seguro que desea borrar la captura?",
			width: 320,
			buttons: Ext.Msg.YESNO,
			fn: function(btn){
				if(btn == "yes"){
					this.setConfiguracionInicial();							
				}else{
					this.cmbProducto.focus(false, true);	
				}						
			}
		});
				
		
	},
	obtenerTurno: function(Id){
		Ext.Ajax.request({
			scope: this,
			url: 'app.php/turnos/obtenerturno',
			params: {
				idTur: Id					
			},
			success: function(data, options){
				var respuesta = Ext.decode(data.responseText);					
				if(respuesta.success==true){
					this.cargarDatos(respuesta.data);						
				}else {
					Ext.Msg.alert('Aviso', 'El turno no existe, verifique por favor.', function(){}, this);
				}					
			}
		});
	},
	getParamsImprimir:function(){
		return {
			IDTur:this.txtIdTurno.getValue()
		};
	},
	imprimir:function(){
		var params=this.getParamsImprimir();
		Ext.Ajax.request({
			params: params,
			   url: 'app.php/turnos/generarreporteturno',
			   success: function(response, opts){					
					var obj = Ext.decode(response.responseText);
					if (!obj.success){	
						return;
					}
					var identificador=obj.data.identificador;
					window.open("app.php/turnos/getpdfturno?identificador="+identificador,'rep_tur',"height=600,width=800");							
				},
			   failure: function(){
					alert("El servidor ha respondido con un mensaje de error");
				}						   
			   
			});
	},
	eliminar:function(btn){
		switch(btn){	
    	case 'no':
    		return;
    	break;
    	case 'yes':
    		this.eliminar('borrar');
    		return;
    		break;
    	case 'borrar':
    		break;		
    	case undefined:
    	case false:    		
    	default:
    		var me=this;    		
    		Ext.Msg.show({
 			   title:'Confirme por favor',
 			   msg: "¿Desea borrar el turno?",
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
			params: { id_turno: this.txtIdTurno.getValue() },
			scope:this,
		   	url: 'app.php/turnos/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.fireEvent('eliminado',options.params.id_turno);
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
    			this.txtIdTurno.setValue(this.idValue);
				//this.el.mask(mfw.mensajeDeEspera);    			
    		}
                          
			this.frmMain.load({
				params:{idTur:this.idValue,
						id_empresa:miErpWeb.Empresa[0].id_empresa,
						id_sucursal:miErpWeb.Sucursal[0].id_sucursal				
				},
				url:'app.php/turnos/obtenerturno'
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
Ext.reg('formTurnos', formTurnos);