/*
 * File: formAbonos.js
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
formAbonos = Ext.extend(formAbonosUi, {
    inicializarStores:function(){
		this.cmbSerie.store =  new miErpWeb.storeFormAbonosSeries();
				
		this.cmbCliente.store =  new miErpWeb.storeFormAbonosClientes();		
		
		this.cmbRemision.store =  new miErpWeb.storeFormAbonosRemisiones();
		
	},
	inicializarEvents:function(){
		
		this.frmMain.on('actioncomplete',function(form,action){
			 if (action.result.success){
				 this.cargarDatos(action.result.data);
				 
			 }else{				
				return false;
			}			
		}, this);	
		
		
		this.cmbRemision.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbRemision.getStore().on('beforeload',function(){
			this.cmbRemision.store.baseParams.id_empresa=miErpWeb.Empresa[0].id_empresa;
			this.cmbRemision.store.baseParams.id_sucursal=miErpWeb.Sucursal[0].id_sucursal;
			this.cmbRemision.store.baseParams.id_cliente= Ext.num(this.cmbCliente.getValue(), 0);
		},this);	
		
		this.cmbRemision.on('select',function(combo, record, index){
			this.pnlInfoRemision.show();
			var total = record.get('total');
			var abonos = record.get('abonos');
			var saldo = record.get('saldo');
			
			this.lblImporte.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total));
			this.lblAbonos.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(abonos));
			this.lblSaldo.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(saldo));
			this.doLayout();
		},this);	
		
		this.cmbCliente.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		
		
		this.txtImporte.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};
		
		this.cmbSerie.on('select',function(combo, record, index){
			this.txtFolio.setValue(record.get('foliosig'));				
		}, this);
		
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
	
		this.btnImprimir.on('click', function(){
			this.imprimir();
		}, this);
				
	},
	inicializarRenders:function(){
		
				
	},
	initComponent: function() {
        formAbonos.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdAbono.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };
		
		this.inicializarStores();
		this.inicializarEvents();
		// this.inicializarRenders();
    },
	cargarDatos:function(data){
		if (data.Abono==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos del abono',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			// miErpWeb.tabContainer.remove(this);	
			
			return;
		}
		var abono=data.Abono;
		
		var form=this.frmMain.getForm();		
        // form.setValues(movimiento);
		this.txtIdAbono.setValue(abono.id_cxc_abono);
        var fechaMov=abono.fecha;
	
		var dt = Date.parseDate(fechaMov, "d/m/Y H:i:s");
	       
        this.txtFecha.setValue(dt);
        this.txtHora.setValue(dt.format('H:i:s A'));
		this.txtFolio.setValue(abono.folio);
		this.txtObservacion.setValue(abono.observacion);		
		var total = abono.total;
		var abonos = abono.abonos;
		var saldo = abono.saldo;
		var importe = Ext.num(abono.importe,0);
		this.txtImporte.setValue(importe);		
		
		
		if (abono.id_cxc_abono==0){
			this.cmbSerie.store.baseParams.id_empresa=abono.id_empresa;		
			this.cmbSerie.store.baseParams.id_sucursal=abono.id_sucursal;	
			this.cmbSerie.store.on('load',this.cargarPrimerFolio,this);	
			this.cmbSerie.store.load();
			
		}else{/*	SI LA FACTURA YA EXISTE EN EL SERVIDOR, SE ESTABLECE UN NUEVO TITULO Y EL ICONO DEL TAB			*/
			var series={
				id_serie:abono.id_serie,
				nombre_serie:abono.serie
			};
			this.cmbSerie.store.loadData({data:series});
			this.cmbSerie.setValue(series.id_serie);
			this.cmbSerie.setDisabled(true);
			
			this.txtFecha.setDisabled(true);
			this.txtHora.setDisabled(true);
			this.txtFolio.setDisabled(true);
			
			this.btnEliminar.setDisabled(false);
			this.btnImprimir.setDisabled(false);
			
			this.setTitle(abono.id_cxc_abono+"-"+abono.concepto);
						
		}
		
		
		
		// ALMACEN DE ORIGEN
			
		// AGENTE
		var remision={
			id_remision:abono.id_remision,
			descripcion:abono.descripcion,
			id_cxc:abono.id_cxc
		};
		this.cmbRemision.store.loadData({data:remision});
		
		var storeRemision=this.cmbRemision.getStore();
		var index=storeRemision.find('id_remision',abono.id_remision);
		var rec=storeRemision.getAt(index);
			
		this.cmbRemision.setValue(remision.id_remision);
		if (abono.id_cxc_abono>0)
			this.cmbRemision.fireEvent('select',this.cmbRemision,rec);
		
		this.lblImporte.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total));
		this.lblAbonos.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(abonos));
		this.lblSaldo.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(saldo));
		
		var cliente={
			id_cliente:abono.id_cliente,
			nombre_fiscal:abono.nombre_fiscal
		};
			
		this.cmbCliente.store.loadData({data:cliente});
		this.cmbCliente.setValue(cliente.id_cliente);

		this.el.unmask();	
	},
	cargarPrimerFolio:function(){			
		this.cmbSerie.store.removeListener('load',this.cargarPrimerFolio,this);
		var primerRecord=this.cmbSerie.store.getAt(0);			
		this.cmbSerie.setValue(primerRecord.data.id_serie);

		this.txtFolio.setValue(primerRecord.data.foliosig);
		
	},
	guardar:function(){
		if (this.frmMain.getForm().isValid()){
			
			var fecha = this.txtFecha.getValue();
			fecha=fecha.format('Y-m-d');   
			var params={};
			//params['Movimiento[id_empresa]'] = this.txtIdMovimientoAlmacen.getValue();
			params['Abono[id_empresa]'] = miErpWeb.Empresa[0].id_empresa;
			params['Abono[id_sucursal]'] = miErpWeb.Sucursal[0].id_sucursal;
			params['Abono[id_almacen]'] = miErpWeb.Almacen[0].id_almacen;
			params['Abono[id_cxc_abono]'] = this.txtIdAbono.getValue();
			params['Abono[id_serie]'] = this.cmbSerie.getValue();
			params['Abono[nombre_serie]'] = this.cmbSerie.getRawValue();
			params['Abono[folio]'] = this.txtFolio.getValue();
			params['Abono[fecha]'] = fecha; 
			params['Abono[hora]'] =this.txtHora.getValue();
			params['Abono[id_cliente]'] = this.cmbCliente.getValue();			
			params['Abono[id_remision]'] = this.cmbRemision.getValue();
			params['Abono[importe]'] = this.txtImporte.getValue();
			params['Abono[status]'] = this.txtStatus.getValue();
			params['Abono[observacion]'] = this.txtObservacion.getValue();
			
			
			var id_remision = this.cmbRemision.getValue();
		    var store=this.cmbRemision.getStore();
		    var index=store.find('id_remision', id_remision);
		    	 
		    var rec=store.getAt(index);
			
			params['Abono[id_cxc]'] = rec.data.id_cxc;

			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/abonos/save',
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
	getParamsImprimir:function(){
		return {
			IDAbo:this.txtIdAbono.getValue()
		};
	},
	imprimir:function(){
		var params=this.getParamsImprimir();			
		Ext.Ajax.request({
		params: params,
		   url: 'app.php/abonos/generarreporteabono',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				var identificador=obj.data.identificador;
				window.open("app.php/abonos/getpdfabono?identificador="+identificador,'rep_rem',"height=600,width=800");							
			},
		   failure: function(){
				alert("El servidor ha respondido con un mensaje de error");
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
 			   msg: "¿Desea borrar la remision?",
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
			params: { id_cxc_abono: this.txtIdAbono.getValue() },
			scope:this,
		   	url: 'app.php/abonos/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.fireEvent('eliminado',options.params.id_cxc_abono);
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
    			this.txtIdAbono.setValue(this.idValue);
				//this.el.mask(mfw.mensajeDeEspera);    			
    		}
                          
			this.frmMain.load({
				params:{idAbo:this.idValue,
						id_empresa:miErpWeb.Empresa[0].id_empresa,
						id_sucursal:miErpWeb.Sucursal[0].id_sucursal,
						id_almacen:miErpWeb.Almacen[0].id_almacen				
				},
				url:'app.php/abonos/obtenerabono'
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
Ext.reg('formAbonos', formAbonos);