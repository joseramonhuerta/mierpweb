/*
 * File: formListaPrecios.js
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
formListaPrecios = Ext.extend(formListaPreciosUi, {
	edicion:false,
	edicionDetalle:false,
	indexDetalle:0,
	id_producto:0,
	inicializaStores: function(){
		this.gridDetalles.store = new miErpWeb.storeFormListaPreciosGrid();
	},
	inicializaEvents:function(){
		var me = this;

		this.frmMain.on('actioncomplete',function(form,action){
			if (action.result.success){
				this.cargarDatos(action.result.data);				
			}else{				
			   return false;
		   }			
	   }, this);
	   
	   this.on('cambioDeId',function(params){	
			var id=params.id;
			if (id==0){
				this.btnGuardar.setIcon('images/iconos/'+this.iconMaster+'_add.png');
			}else if (id>0){
				this.btnEliminar.setDisabled(false);
				//this.btnDesactivar.setDisabled(false);
				this.btnGuardar.setIcon('images/iconos/'+this.iconMaster+'_edit.png');
			}			
		},this);

		this.cmbProducto.updateAlways = true;
		
		this.cmbProducto.on("keydown", function(cmb, e){
			if(e.getKey()==13){
				this.id_producto = 0;
				this.aceptaProducto();
			}
		}, this);

		this.cmbProducto.onTriggerClick = function(){
				
			this.busquedaProducto = new winBuscadorProductos();
			this.busquedaProducto.show();
			
			this.busquedaProducto.on("productoSeleccionado", function(Id){
				
				me.id_producto = Id;
				me.cmbProducto.setValue('');
				me.aceptaProducto();
			}, this);
			
		}

		this.txtDescripcion.setValue=function(value){
			value=miErpWeb.formatearTexto(value);
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	//this.cambioDeNombre(value);
			this.fireEvent('cambioDeNombre',value);
		}; 

		this.btnAgregar.on("click", function(){
			this.agregarProducto();
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
							this.cmbProducto.focus(false, true);
							
						}else{
							this.cmbProducto.focus(false, true);	
						}
						
					}
				});			
			}
				
		}, this);
		
		this.gridDetalles.on("celldblclick", function(Grid, rowIndex, columnIndex, e){
				var record = this.gridDetalles.getStore().getAt(rowIndex);
				this.indexDetalle = rowIndex;
				this.id_producto = record.data.id_producto;
				this.cmbProducto.setValue(record.data.id_producto);
				this.cmbProducto.setRawValue(record.data.descripcion);
				this.txtPrecio.setValue(Ext.util.Format.number(record.data.precio,'0.00'));
				this.txtPuntos.setValue(Ext.util.Format.number(record.data.valor_puntos,'0.00'));
				this.edicionDetalle = true;
				this.cmbProducto.setDisabled(true);
		}, this);

		this.txtPrecio.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};

		this.txtPuntos.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};

		this.btnGuardar.on('click', function(){
			this.guardar();
		}, this );
		
		this.btnEliminar.on('click',function(){	
			this.eliminar();
			
		},this);

	},
	inicializaRenders:function(){
		var colMod=this.gridDetalles.getColumnModel();
		
		var column=colMod.getColumnById("colPuntos");
		column.renderer=function(val){
			return Ext.util.Format.cantidadConSeparadorDeMiles(val);
		};	
		
		column=colMod.getColumnById("colPrecio");
		column.renderer=this.renderMoneda;
		
	},
	initComponent: function() {
        formListaPrecios.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdListaPrecio.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };

		this.inicializaStores();
		this.inicializaEvents();
		this.inicializaRenders();
		
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
			var conceptos=gridToJson(this.gridDetalles);
			
			var params={};
			params['ListaPrecio[id_listaprecio]'] = this.txtIdListaPrecio.getValue();
			params['ListaPrecio[descripcion]'] = this.txtDescripcion.getValue();
			params['ListaPrecio[status]'] = this.txtStatus.getValue();

			params['Conceptos']=conceptos;
			
			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/listaprecios/save',
				success:function(){
					this.el.unmask();
				},
				failure:function(form, action){
					this.el.unmask();
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
					msg: "¿Desea borrar la lista de precios?",
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
				params: { id_listaprecio: this.txtIdListaPrecio.getValue() },
				scope:this,
				   url: 'app.php/listaprecios/eliminar',
				   success: function(response,options){	
					var respuesta=Ext.decode(response.responseText);
					if (respuesta.success==false){
						this.el.unmask();
						return;
					}
					
					this.fireEvent('eliminado',options.params.id_listaprecio);
					MainContainer.tabContainer.remove(this);
				   },
				   failure: function(){
					   this.el.unmask();
				   }		   
			});
	},
	aceptaProducto:function(){
		if(this.cmbProducto.getValue != "" || this.id_producto > 0 ){
			//this.id_producto = 0;
			Ext.Ajax.request({
				scope: this,
				url: 'app.php/listaprecios/obtenerproducto',
				params: {
					ID: this.id_producto,
					Descripcion: this.cmbProducto.getValue()
				},
				success: function(data, options){
					var respuesta = Ext.decode(data.responseText);
					
					if(respuesta.success==true){
						this.id_producto = respuesta.data[0].id_producto;
						//this.cmbProducto.setValue(respuesta.data[0].id_producto);
						this.cmbProducto.setRawValue(respuesta.data[0].descripcion);
						this.txtPrecio.setValue(miErpWeb.formatearMoneda(respuesta.data[0].precio_venta));
						this.txtPuntos.setValue(miErpWeb.formatearMoneda(respuesta.data[0].valor_puntos));
			
						this.txtPrecio.focus(true,100);
						// this.id_producto = 0;
						} else {
							Ext.Msg.alert('Aviso', 'El Producto no existe, verifique por favor.', function(){
							this.id_producto = 0;	
							this.cmbProducto.focus(true, 0);
						}, this);
						
					}					
				}
			});
		}
	},
	agregarProducto: function(){
		if(this.cmbProducto.getRawValue()==""){
				Ext.Msg.alert('Aviso', 'Capture el Producto.', function(){
					this.btnAgregar.enable();
					this.cmbProducto.focus(false, true);
				}, this);
				return;
			}
			
			if (!this.frmDetalles.getForm().isValid()) {	//<---Si hay errores informa al usuario
			  Ext.Msg.show({
				   title:'Error al agregar el Producto',
				   msg: 'Por favor revise los campos marcados',
				   buttons: Ext.Msg.OK,
				   fn: function(){
						this.cmbProducto.focus();
						this.cmbProducto.allowBlank=true;
				   },			   
				   scope:this,
				   icon: Ext.MessageBox.WARNING
				});			
				return false;
			}
			
			var prec = this.txtPrecio.getValue();
			var punt = this.txtPuntos.getValue();
									
			var id_producto = this.id_producto;
			var existe = false;
			var indexDetalle = 0;
			var precio = this.txtPrecio.getValue();
			var puntos = this.txtPuntos.getValue();		
			
			var detalles = this.gridDetalles.getStore().getRange();
			indexDetalle = detalles.length;
			
			for(var x = 0; x<detalles.length; x++){
				if(detalles[x].data.id_producto == id_producto){
					existe = true;
					indexDetalle = x;
					precio = detalles[x].data.precio;
					valor_puntos = detalles[x].data.puntos;								
				}	
			}
			
			if(existe == false){
				var record = new this.gridDetalles.store.recordType({
					id_producto: this.id_producto,
					descripcion: this.cmbProducto.getRawValue(), 
					precio: this.txtPrecio.getValue(),
					valor_puntos: this.txtPuntos.getValue()
				}, Ext.id());
				
				this.gridDetalles.getStore().insert(0,record);
			}else{
				var record = this.gridDetalles.getStore().getAt(indexDetalle);
				if(this.edicionDetalle){					
					var prec = this.txtPrecio.getValue();
					var punt = this.txtPuntos.getValue();					
				}
				record.set("precio",prec);
				record.set("valor_puntos",punt);				
											
				this.gridDetalles.getStore().commitChanges();				
			}
			this.frmDetalles.getForm().reset();
			this.cmbProducto.setDisabled(false);
			this.cmbProducto.focus(true, 0);
			this.id_producto = 0;
			this.edicionDetalle=false;
			this.indexDetalle = 0;			
		
	},
	cargarDatos:function(data){
		if (data.ListaPrecio==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos de la lista de precio',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			// miErpWeb.tabContainer.remove(this);	
			
			return;
		}
		var listaprecio=data.ListaPrecio;
		var form=this.frmMain.getForm();		
        // form.setValues(movimiento);
		this.txtIdListaPrecio.setValue(listaprecio.id_listaprecio);
        
		this.txtDescripcion.setValue(listaprecio.descripcion);				
		
		if (listaprecio.id_listaprecio==0){
						
		}else{/*	SI LA FACTURA YA EXISTE EN EL SERVIDOR, SE ESTABLECE UN NUEVO TITULO Y EL ICONO DEL TAB			*/
			this.btnEliminar.setDisabled(false);		
			
			this.setTitle(listaprecio.id_listaprecio+"-"+listaprecio.descripcion);
						
		}		
		
		//Cargar detalles
		var detalles=data.Detalles;
        if(detalles!=undefined){			
            this.gridDetalles.store.loadData({
                data:detalles
            });
           
        }	

		this.el.unmask();	
	},
	listeners:{
    	activate:function(){
			
    		if (this.activado==true){
    			return;
    		}
    		this.activado=true;
    	
			if (this.idValue!=undefined && this.idValue!=0){
    			this.txtIdListaPrecio.setValue(this.idValue);
				//this.el.mask(mfw.mensajeDeEspera);    			
    		}
                          
			this.frmMain.load({
				params:{idLis:this.idValue				
				},
				url:'app.php/listaprecios/obtenerlista'
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
Ext.reg('formListaPrecios', formListaPrecios);