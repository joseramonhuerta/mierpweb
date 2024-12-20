/*
 * File: formGastos.js
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
formGastos = Ext.extend(formGastosUi, {
	origen:2,//1=Movimientos Bancos,2=Gastos
    inicializarStores:function(){
		this.cmbTipoMovimiento.store = new miErpWeb.storeFormGastosTiposMovimientos();        
        var data=new Array(
        		{id:2,nombre:miErpWeb.formatearTexto('EGRESO')}
        );
		this.cmbTipoMovimiento.store.loadData({data:data});
		
		this.cmbTipoMovimiento.setValue(2);
		
		this.cmbTipoOrigen.store = new miErpWeb.storeFormGastosTiposOrigen();        
        var dataTipoOrigen=new Array(
        		{id:1,nombre:miErpWeb.formatearTexto('EFECTIVO')}
        );
		this.cmbTipoOrigen.store.loadData({data:dataTipoOrigen});
		
		this.cmbTipoOrigen.setValue(1);
		
		this.cmbSerie.store =  new miErpWeb.storeFormGastosSeries();
				
		this.cmbChequera.store =  new miErpWeb.storeFormGastosChequeras();		
		
		this.cmbConcepto.store =  new miErpWeb.storeFormGastosConceptos();
		
	},
	inicializarEvents:function(){
		
		this.cmbTipoMovimiento.setDisabled(true);
		this.frmMain.on('actioncomplete',function(form,action){
			 if (action.result.success){
				 this.cargarDatos(action.result.data);
				 
			 }else{				
				return false;
			}			
		}, this);	
		
		
		this.cmbConcepto.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbConcepto.getStore().on('beforeload',function(){
			this.cmbConcepto.store.baseParams.id_tipo= Ext.num(this.cmbTipoMovimiento.getValue(), 0);
		},this);	
		/*
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
		*/
		
		this.cmbTipoMovimiento.on('select',function(combo, record, index){
			this.cmbConcepto.clearValue();
		},this);	
		
		this.cmbTipoOrigen.on('select',function(combo, record, index){
			var tipo_mov = this.cmbTipoOrigen.getValue();
			switch(tipo_mov){
				case '1':	    	
					this.cmbChequera.setDisabled(true);
					this.cmbChequera.clearValue();					
				break;
				case '2':	    
					this.cmbChequera.setDisabled(false);
					this.cmbChequera.clearValue();					
				break;
				default:	
					return;
				break;
			}	
						
		},this);
		
		
		this.cmbChequera.addListener('beforequery',function(qe){
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
        formGastos.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdMovimientoBanco.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };
		
		this.inicializarStores();
		this.inicializarEvents();
		// this.inicializarRenders();
    },
	cargarDatos:function(data){
		if (data.MovimientoBanco==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos del movimiento',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			// miErpWeb.tabContainer.remove(this);	
			
			return;
		}
		var movimientobanco=data.MovimientoBanco;
		
		var form=this.frmMain.getForm();		
        // form.setValues(movimiento);
		this.txtIdMovimientoBanco.setValue(movimientobanco.id_movimiento_banco);
        var fechaMov=movimientobanco.fecha;
	
		var dt = Date.parseDate(fechaMov, "d/m/Y H:i:s");
	       
        this.txtFecha.setValue(dt);
        this.txtHora.setValue(dt.format('H:i:s A'));
		this.txtFolio.setValue(movimientobanco.folio);
		this.txtObservaciones.setValue(movimientobanco.observaciones);
		var importe = Ext.num(movimientobanco.importe,0);
		this.txtImporte.setValue(importe);		
		
		
		if (movimientobanco.id_movimiento_banco==0){
			this.cmbSerie.store.baseParams.id_empresa=movimientobanco.id_empresa;		
			this.cmbSerie.store.baseParams.id_sucursal=movimientobanco.id_sucursal;	
			this.cmbSerie.store.on('load',this.cargarPrimerFolio,this);	
			this.cmbSerie.store.load();
			
		}else{/*	SI LA FACTURA YA EXISTE EN EL SERVIDOR, SE ESTABLECE UN NUEVO TITULO Y EL ICONO DEL TAB			*/
			var series={
				id_serie:movimientobanco.id_serie,
				nombre_serie:movimientobanco.serie
			};
			this.cmbSerie.store.loadData({data:series});
			this.cmbSerie.setValue(series.id_serie);
			this.cmbSerie.setDisabled(true);
			
			this.txtFecha.setDisabled(true);
			this.txtHora.setDisabled(true);
			this.txtFolio.setDisabled(true);
			
			this.btnEliminar.setDisabled(false);
			this.btnImprimir.setDisabled(false);
			
			this.setTitle(movimientobanco.id_movimiento_banco+"-"+movimientobanco.serie+" - "+movimientobanco.folio);
						
		}		
		/*
		var tipos_movimientos={
				id:movimientobanco.tipo_movimiento,
				nombre:movimientobanco.nombre_movimiento
			};
		this.cmbTipoMovimiento.store.loadData({data:tipos_movimientos});	*/		
		this.cmbTipoMovimiento.setValue(2);
		this.cmbTipoOrigen.setValue(1);

		
		this.cmbChequera.setDisabled(true);
			
		var concepto={
			id_concepto:movimientobanco.id_concepto,
			descripcion:movimientobanco.concepto,
			
		};
		this.cmbConcepto.store.loadData({data:concepto});
		this.cmbConcepto.setValue(concepto.id_concepto);
		
		/*if (movimientobanco.id_movimiento_banco>0)
			this.cmbConcepto.fireEvent('select',this.cmbConcepto,rec);*/
		
		
		var chequera={
			id_chequera:movimientobanco.id_chequera,
			descripcion:movimientobanco.chequera
		};
			
		this.cmbChequera.store.loadData({data:chequera});
		this.cmbChequera.setValue(chequera.id_chequera);

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
			params['MovimientoBanco[origen]'] = this.origen;
			params['MovimientoBanco[id_empresa]'] = miErpWeb.Empresa[0].id_empresa;
			params['MovimientoBanco[id_sucursal]'] = miErpWeb.Sucursal[0].id_sucursal;
			params['MovimientoBanco[id_movimiento_banco]'] = this.txtIdMovimientoBanco.getValue();
			params['MovimientoBanco[id_serie]'] = this.cmbSerie.getValue();
			params['MovimientoBanco[nombre_serie]'] = this.cmbSerie.getRawValue();
			params['MovimientoBanco[folio]'] = this.txtFolio.getValue();
			params['MovimientoBanco[fecha]'] = fecha; 
			params['MovimientoBanco[hora]'] =this.txtHora.getValue();
			params['MovimientoBanco[observaciones]'] = this.txtObservaciones.getValue();
			params['MovimientoBanco[tipo_movimiento]'] = this.cmbTipoMovimiento.getValue();
			params['MovimientoBanco[id_concepto]'] = this.cmbConcepto.getValue();	
			params['MovimientoBanco[tipo_origen]'] = this.cmbTipoOrigen.getValue();
			params['MovimientoBanco[id_chequera]'] = this.cmbChequera.getValue();
			params['MovimientoBanco[importe]'] = this.txtImporte.getValue();
			params['MovimientoBanco[status]'] = this.txtStatus.getValue();
			/*			
			var id_remision = this.cmbRemision.getValue();
		    var store=this.cmbRemision.getStore();
		    var index=store.find('id_remision', id_remision);
		    	 
		    var rec=store.getAt(index);
			
			params['Abono[id_cxc]'] = rec.data.id_cxc;
			*/
			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/movimientosbanco/save',
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
			IDMovBanco:this.txtIdMovimientoBanco.getValue()
		};
	},
	imprimir:function(){
		var params=this.getParamsImprimir();			
		Ext.Ajax.request({
		params: params,
		   url: 'app.php/movimientosbanco/generarreportemovimientobanco',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				var identificador=obj.data.identificador;
				window.open("app.php/movimientosbanco/getpdfmovimientobanco?identificador="+identificador,'rep_rem',"height=600,width=800");							
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
 			   msg: "¿Desea borrar el movimiento de banco?",
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
			params: { id_movimiento_banco: this.txtIdMovimientoBanco.getValue() },
			scope:this,
		   	url: 'app.php/movimientosbanco/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.fireEvent('eliminado',options.params.id_movimiento_banco);
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
    			this.txtIdMovimientoBanco.setValue(this.idValue);
				//this.el.mask(mfw.mensajeDeEspera);    			
    		}
                          
			this.frmMain.load({
				params:{idMovBanco:this.idValue,
						id_empresa:miErpWeb.Empresa[0].id_empresa,
						id_sucursal:miErpWeb.Sucursal[0].id_sucursal			
				},
				url:'app.php/movimientosbanco/obtenermovimientobanco'
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
Ext.reg('formGastos', formGastos);