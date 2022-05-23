/*
 * File: formProductos.js
 * Date: Mon Apr 11 2016 19:21:42 GMT-0600 (Hora verano, Montañas (México))
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
formProductos = Ext.extend(formProductosUi, {
	inicializarStores: function(){
		this.cmbTipoProducto.store = new miErpWeb.storeTipoProducto();        
        var data=new Array(
        		{id:'P',nombre:miErpWeb.formatearTexto('PRODUCTO')},
        		{id:'S',nombre:miErpWeb.formatearTexto('SERVICIO')}
        );
		this.cmbTipoProducto.store.loadData({data:data});
		
		this.cmbIva.store 	 = new miErpWeb.storeProductosImpuestos(); 
		this.cmbRetIva.store = new miErpWeb.storeProductosImpuestos(); 
		this.cmbRetIsr.store = new miErpWeb.storeProductosImpuestos(); 
		
		this.cmbUnidades.store = new miErpWeb.storeProductosUnidades();
		this.cmbUnidades.store.load();	
		
		this.cmbLineas.store = new miErpWeb.storeProductosLineas();
		this.cmbLineas.store.load();	
		
		 var dataImp=new Array(
        		{id:'0',nombre:miErpWeb.formatearTexto('NO')},
        		{id:'1',nombre:miErpWeb.formatearTexto('SI')}
        );
		this.cmbIva.store.loadData({data:dataImp});
		this.cmbRetIva.store.loadData({data:dataImp});
		this.cmbRetIsr.store.loadData({data:dataImp});
		
		this.cmbIva.setValue(0);
		this.cmbRetIva.setValue(0);
		this.cmbRetIsr.setValue(0);
		
	},
	inicializarEventos: function(){
		var me = this;
		this.cmbUnidades.on('beforequery',function(qe){	
			delete qe.combo.lastQuery;
			
		},this);
		
		
		this.cmbUnidades.on('keypress',function(t, e){	
			if (e['altKey'] || (e['shiftKey'] && e.getKey() == 34) || e.getKey() == 39) {
                    e.stopEvent();
                }			
		},this);
		
		this.cmbUnidades.on('keyup',function(t, e){	
				 if (e.getKey() == 114 && e['ctrlKey'] && !e['altKey']) {
                    if(this.disabled){
						return;
					}
                    this.expand();
                    this.el.focus();
                    if(this.getRawValue())	this.doQuery(this.getRawValue());
                    else this.doQuery('@ALL001X23');
                    this.el.focus();
                }	
		},this);
		
		this.cmbLineas.onTriggerClick = function(a, e){
			if(e){
				if(e.getAttribute('class').indexOf('x-form-clear-trigger') > -1){
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}
					if(!Ext.isEmpty(me.cmbLineas.getValue())){
						this.reset();					
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
    configurarCostos: function(){
		
		this.txtUltimoCosto.setValue=function(value){
			if (value!=null && !isNaN(value)){								
				value="$"+Ext.util.Format.monedaConSeparadorDeMiles(value);				
			}
			Ext.form.DisplayField.prototype.setValue.apply(this,arguments);
		};
		this.txtCostoPromedio.setValue=function(value){
			if (value!=null && !isNaN(value)){								
				value="$"+Ext.util.Format.monedaConSeparadorDeMiles(value);				
			}
			Ext.form.DisplayField.prototype.setValue.apply(this,arguments);
		};		

		this.txtPrecioCompra.on('change',function(){
			var idProd=this.txtIdProducto.getValue();
			if (idProd==='' || idProd===0){
				var costo=this.txtPrecioCompra.getValue();
				if (!isNaN(costo)){
					this.txtCostoPromedio.setValue(costo);
					this.txtUltimoCosto.setValue(costo);
				}
			}
		},this);
	},
	initComponent: function() {
        formProductos.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdProducto.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };
        this.txtDescripcion.setValue=function(value){
			value=miErpWeb.formatearTexto(value);
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	//this.cambioDeNombre(value);
			this.fireEvent('cambioDeNombre',value);
		}; 
		
		 this.txtPrecioVenta.getErrors=function(){
        	var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
            var msg='';
            
            var valor=this.getValue();
            
            if (isNaN(this.getValue())){		
                msg = "Numero incorrecto";
                errors.push(msg);
            }else{	//Si es un numero, se verifica que no exceda el maximo de la base de datos decimal 14,6
          	  var maximo=99999999.999999;
          	  if (valor>maximo){
              	  msg = "El número debe ser menor a "+Ext.util.Format.separarMiles(maximo);
                    errors.push(msg);
                }
            }
            return errors;
        };
		
		this.txtPrecioEstilista.getErrors=function(){
        	var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
            var msg='';
            
            var valor=this.getValue();
            
            if (isNaN(this.getValue())){		
                msg = "Numero incorrecto";
                errors.push(msg);
            }else{	//Si es un numero, se verifica que no exceda el maximo de la base de datos decimal 14,6
          	  var maximo=99999999.999999;
          	  if (valor>maximo){
              	  msg = "El número debe ser menor a "+Ext.util.Format.separarMiles(maximo);
                    errors.push(msg);
                }
            }
            return errors;
        };
		
		this.txtPrecioCompra.getErrors=function(){
        	var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
            var msg='';
            
            var valor=this.getValue();
            
            if (isNaN(this.getValue())){		
                msg = "Numero incorrecto";
                errors.push(msg);
            }else{	//Si es un numero, se verifica que no exceda el maximo de la base de datos decimal 14,6
          	  var maximo=99999999.999999;
          	  if (valor>maximo){
              	  msg = "El número debe ser menor a "+Ext.util.Format.separarMiles(maximo);
                    errors.push(msg);
                }
            }
            return errors;
        };
		
		this.txtMinimo.getErrors=function(){
        	var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
            var msg='';
            
            var valor=this.getValue();
            
            if (isNaN(this.getValue())){		
                msg = "Numero incorrecto";
                errors.push(msg);
            }else{	//Si es un numero, se verifica que no exceda el maximo de la base de datos decimal 14,6
          	  var maximo=99999999.999999;
          	  if (valor>maximo){
              	  msg = "El número debe ser menor a "+Ext.util.Format.separarMiles(maximo);
                    errors.push(msg);
                }
            }
            return errors;
        };
		
		this.txtMaximo.getErrors=function(){
        	var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
            var msg='';
            
            var valor=this.getValue();
            
            if (isNaN(this.getValue())){		
                msg = "Numero incorrecto";
                errors.push(msg);
            }else{	//Si es un numero, se verifica que no exceda el maximo de la base de datos decimal 14,6
          	  var maximo=99999999.999999;
          	  if (valor>maximo){
              	  msg = "El número debe ser menor a "+Ext.util.Format.separarMiles(maximo);
                    errors.push(msg);
                }
            }
            return errors;
        };

		this.txtValorPuntos.getErrors=function(){
        	var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
            var msg='';
            
            var valor=this.getValue();
            
            if (isNaN(this.getValue())){		
                msg = "Numero incorrecto";
                errors.push(msg);
            }else{	//Si es un numero, se verifica que no exceda el maximo de la base de datos decimal 14,6
          	  var maximo=99999999.999999;
          	  if (valor>maximo){
              	  msg = "El número debe ser menor a "+Ext.util.Format.separarMiles(maximo);
                    errors.push(msg);
                }
            }
            return errors;
        };
		
		this.inicializarStores();
		this.configurarToolBar();
		this.configurarCostos();
		this.inicializarEventos();
    },
	cancelar:function(){
		this.el.mask(mew.mensajeDeEspera);
		Ext.Ajax.request({
			params: { 
				id_producto: this.txtIdProducto.getValue(),
				status:this.txtStatus.getValue()
			},
			scope:this,
		   	url: 'app.php/productos/cambiarstatus',
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
			url:'app.php/productos/guardar',
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
 			   msg: "¿Desea borrar el Producto?",
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
			params: { id_producto: this.txtIdProducto.getValue() },
			scope:this,
		   	url: 'app.php/productos/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.fireEvent('eliminado',options.params.id_producto);
				MainContainer.tabContainer.remove(this);
		   	},
		   	failure: function(){
		   		this.el.unmask();
		   	}		   
		});
	},
	load:function(){
		var params={idPro:this.txtIdProducto.getValue()};
		this.el.mask(mew.mensajeDeEspera);
		this.getForm().load({
			params:params,
			url:'app.php/productos/obtenerproducto',
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
    			this.txtIdProducto.setValue(this.idValue);
    			this.load();
			} 
				
					
    	},
    	actioncomplete:function(form,action){
    		var respuesta=Ext.decode(action.response.responseText);			
			if (respuesta.success==true){
				var producto = respuesta.data.Producto;
				var unidades=respuesta.data.Unidades;
				var lineas=respuesta.data.Lineas;
				if (unidades!=undefined){
					this.cmbUnidades.store.loadData({data:unidades});
				}   
				if (lineas!=undefined){
					this.cmbLineas.store.loadData({data:lineas});
				}   
								
				producto.codigo=miErpWeb.formatearTexto(producto.codigo);
				producto.codigo_barras=miErpWeb.formatearTexto(producto.codigo_barras);
				producto.descripcion=miErpWeb.formatearTexto(producto.descripcion);
				producto.detalles=miErpWeb.formatearTexto(producto.detalles);
				producto.precio_venta=miErpWeb.formatearMoneda(producto.precio_venta);
				producto.valor_puntos=miErpWeb.formatearMoneda(producto.valor_puntos);
				producto.precio_estilista=miErpWeb.formatearMoneda(producto.precio_estilista);
				producto.precio_compra=miErpWeb.formatearMoneda(producto.precio_compra);
				// producto.ultimo_costo=miErpWeb.monedaConSeparadorDeMiles(producto.ultimo_costo);
				// producto.costo_promedio=miErpWeb.monedaConSeparadorDeMiles(producto.costo_promedio);
				form.setValues(producto);
				
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
Ext.reg('formProductos', formProductos);