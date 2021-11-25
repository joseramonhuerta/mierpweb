/*
 * File: formMovimientosCaja.js
 * Date: Mon Nov 13 2017 11:49:11 GMT-0700 (Hora estándar Montañas (México))
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
formMovimientosCaja = Ext.extend(formMovimientosCajaUi, {
	inicializarStores:function(){
		this.cmbTipo.store = new miErpWeb.storeFormMovimientosCajaTipos();        
        var data=new Array(
        		{id:'1',nombre:miErpWeb.formatearTexto('DEPOSITO')},
        		{id:'2',nombre:miErpWeb.formatearTexto('RETIRO')}
        );
		this.cmbTipo.store.loadData({data:data});
		this.cmbTipo.setValue(1);	
		// this.cmbIva.setValue(0);		
	},
	inicializarEvents:function(){
		this.frmMain.on('actioncomplete',function(form,action){
			 if (action.result.success){
				 this.cargarDatos(action.result.data);				 
			 }else{				
				return false;
			}			
		}, this);

		// this.cmbTipo.store.on('load',function(){
			// this.cmbTipo.setValue(1);			
		// },this);	

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
        formMovimientosCaja.superclass.initComponent.call(this);
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdMovimientoCaja.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };
		
		this.inicializarStores();
		this.inicializarEvents();
		
    },
	cargarDatos:function(data){
		if (data.MovimientoCaja==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos del movimiento de caja',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			return;
		}
		var movimientocaja=data.MovimientoCaja;
		
		var form=this.frmMain.getForm();		
        this.txtIdMovimientoCaja.setValue(movimientocaja.id_movimiento_caja);
		// this.txtStatus.setValue(turno.status);
		var fecha=movimientocaja.fecha;
		var dt = Date.parseDate(fecha, "d/m/Y H:i:s");
	    this.txtFecha.setValue(dt);		
        this.txtHora.setValue(dt.format('H:i:s A'));
		this.txtConcepto.setValue(movimientocaja.concepto);		
		
		this.txtTotal.setValue(movimientocaja.total);		
				
		
		
		if (movimientocaja.id_movimiento_caja>0){		
			this.cmbTipo.setValue(movimientocaja.tipo);	
			this.btnEliminar.setDisabled(false);
			this.btnImprimir.setDisabled(false);
			this.setTitle(movimientocaja.id_movimiento_caja+"-"+movimientocaja.concepto);
		}
		
		this.el.unmask();	
	},
	guardar:function(){
		if (this.frmMain.getForm().isValid()){
			
			var fecha = this.txtFecha.getValue();
			fecha=fecha.format('Y-m-d');   
;   
			var params={};
			params['MovimientoCaja[id_movimiento_caja]'] = this.txtIdMovimientoCaja.getValue();
			params['MovimientoCaja[id_empresa]'] = miErpWeb.Empresa[0].id_empresa;
			params['MovimientoCaja[id_sucursal]'] = miErpWeb.Sucursal[0].id_sucursal;
			params['MovimientoCaja[fecha]'] = fecha; 
			params['MovimientoCaja[hora]'] =this.txtHora.getValue();
			params['MovimientoCaja[tipo]'] =this.cmbTipo.getValue();
			params['MovimientoCaja[concepto]'] = this.txtConcepto.getValue();
			params['MovimientoCaja[total]'] = this.txtTotal.getValue();
			params['MovimientoCaja[status]'] = this.txtStatus.getValue();
			
			
			
			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/movimientoscaja/save',
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
	getParamsImprimir:function(){
		return {
			IDMov:this.txtIdMovimientoCaja.getValue()
		};
	},
	imprimir:function(){
		var params=this.getParamsImprimir();
		
		
		Ext.Ajax.request({
		params: params,
		   url: 'app.php/movimientoscaja/generarreportemovimientoscaja',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				var identificador=obj.data.identificador;
				window.open("app.php/movimientoscaja/getpdfmovimientocaja?identificador="+identificador,'rep_mov',"height=600,width=800");							
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
    			this.txtIdMovimientoCaja.setValue(this.idValue);
				//this.el.mask(mfw.mensajeDeEspera);    			
    		}
                          
			this.frmMain.load({
				params:{idMov:this.idValue,
						id_empresa:miErpWeb.Empresa[0].id_empresa,
						id_sucursal:miErpWeb.Sucursal[0].id_sucursal				
				},
				url:'app.php/movimientoscaja/obtenermovimientocaja'
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
Ext.reg('formMovimientosCaja', formMovimientosCaja);