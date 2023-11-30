/*
 * File: formPuntoVenta.js
 * Date: Sun Jun 11 2017 14:02:01 GMT-0600 (Hora verano, Montañas (México))
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
formPuntoVenta = Ext.extend(formPuntoVentaUi, {
	id_venta:0,
	impresion_ticket:0,
	edicion:false,
	edicionDetalle:false,
	indexDetalle:0,
	id_producto:0,
	formasPago:[],
	id_cliente_default:0,
	id_cliente_estilista:0,
	nombrecliente_default:'',
	id_serie_default:0,
	nombreserie_default:'',
	seriefolio:'',
	foliosig_default:0,
	agregar_concepto_auto:0,
	importe:0.00,
	descuentoGeneral:0.00,
	cambio:0.00,
	mostrar_agente:0,
	setConfiguracionInicial: function(){
			
		this.id_venta=0;
		this.edicion=0;
		this.id_producto=0;
		this.formasPago=[];
		this.id_cliente_default=0;
		this.cliente_estilista=0;
		this.nombrecliente_default='';
		this.id_serie_default=0;
		this.nombreserie_default='';
		this.seriefolio='';
		this.foliosig_default=0;
		this.agregar_concepto_auto=0;
		this.importe=0.00;
		this.cambio=0.00;
		this.descuentoGeneral=0.00;
		this.mostrar_agente=0;
		
		this.txtIdVenta.setValue(0);
		this.cmbVenta.setValue('');
		this.txtConceptoMovimiento.setValue('');
			
		Ext.Ajax.request({
			scope:this,
			params:{
				id_empresa:miErpWeb.Empresa[0].id_empresa,
				id_sucursal:miErpWeb.Sucursal[0].id_sucursal
			},
		   url: 'app.php/ventas/obtenerconfiguracionpos',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				// alert(obj.data[0].agrega_concepto_auto);
				this.id_serie_default = obj.data[0].id_serie;
				this.nombreserie_default = obj.data[0].nombre_serie;
				this.foliosig_default = obj.data[0].foliosig;
				this.id_cliente_default = obj.data[0].id_cliente;
				this.cliente_estilista = obj.data[0].estilista;
				this.nombrecliente_default = obj.data[0].nombre_cliente;
				this.agregar_concepto_auto = obj.data[0].agrega_concepto_auto;
				this.seriefolio = obj.data[0].seriefolio;
				this.impresion_ticket = obj.data[0].impresion_ticket;
				this.mostrar_agente = obj.data[0].mostrar_agente;
				 var fechaMov=obj.data[0].fecha_venta;
	
				var dt = Date.parseDate(fechaMov, "d/m/Y H:i:s");
				   
				this.txtFecha.setValue(dt);
				this.txtHora.setValue(dt.format('H:i:s A'));
				
				var clientes={
				id_cliente:this.id_cliente_default,
				nombre_cliente:this.nombrecliente_default,
				estilista:this.cliente_estilista
				};
				this.cmbCliente.store.loadData({data:clientes});
				
				var storeCliente=this.cmbCliente.store;
				var index=storeCliente.find('id_cliente',this.id_cliente_default);
				var rec=storeCliente.getAt(index);
				
				this.cmbCliente.setValue(clientes.id_cliente);
				this.cmbCliente.fireEvent('select',this.cmbCliente,rec);
			
				this.txtCantidad.setValue(1);
				this.lblSerieFolio.setValue(this.seriefolio);
				this.cmbAgente.reset();
				if(this.mostrar_agente == 1)
					this.muestraOcultaAgente(true);
				else
					this.muestraOcultaAgente(false);
				
				this.doLayout();
												
			},
		   failure: function(){
				alert("El servidor ha respondido con un mensaje de error");
			}						   
		   
		});
		
		
		var detalles=[];
        this.gridDetalles.store.loadData({
                data:detalles
            });
        
		
		
		this.calcularTotales();
		this.activarPuntoVenta('SI');
		this.cmbProducto.focus(false, true);
		
	},
	cargarDatos:function(data){
		if (data.Venta==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos de la venta',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			// miErpWeb.tabContainer.remove(this);	
			
			return;
		}
		var venta=data.Venta;
		
		var form=this.frmMain.getForm();		
        // form.setValues(movimiento);
		this.txtIdVenta.setValue(venta.id_venta);
        var fechaVen=venta.fecha_venta;
	
		var dt = Date.parseDate(fechaVen, "d/m/Y H:i:s");
	    // alert(venta.id_venta);   
        this.txtFecha.setValue(dt);
        this.txtHora.setValue(dt.format('H:i:s A'));
		this.cmbVenta.setValue(venta.SerieFolio);
		this.cmbCliente.setValue(venta.id_cliente);
		this.cmbAgente.setValue(venta.id_agente);
		this.txtConceptoMovimiento.setValue(venta.concepto_venta);
		
		var importe = venta.importe;
		var descuento = venta.descuento;
		var subtotal = venta.subtotal;
		var impuestos = venta.impuestos;
		var total = venta.total;
		
		this.lblImporte.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(importe));
		this.lblDescuento.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(descuento));
		this.lblSubtotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(subtotal));
		this.lblImpuestos.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(impuestos));
		this.lblTotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total));

		this.txtTotalImporte.setValue(importe);
		this.txtTotalDescuento.setValue(descuento);
		this.txtTotalSubtotal.setValue(subtotal);
		this.txtTotalImpuestos.setValue(impuestos);
		this.txtTotalMovimiento.setValue(total);
		
		
		this.cmbCliente.setDisabled(true);
		this.cmbAgente.setDisabled(true);
		this.txtFecha.setDisabled(true);
		this.txtHora.setDisabled(true);
		this.cmbVenta.setDisabled(true);
		
		//Cargar detalles
		var detalles=data.Detalles;
        if(detalles!=undefined){			
            this.gridDetalles.store.loadData({
                data:detalles
            });
            //this.calcularTotales();
        }	
		
		this.activarPuntoVenta('NO');
		
		this.el.unmask();	
	},
	aceptaProducto:function(){
		if(this.cmbProducto.getValue != "" || this.id_producto > 0 ){
			//this.id_producto = 0;
			Ext.Ajax.request({
				scope: this,
				url: 'app.php/ventas/obtenerproducto',
				params: {
					ID: this.id_producto,
					Descripcion: this.cmbProducto.getValue(),
					ID_Cliente: this.cmbCliente.getValue()
				},
				success: function(data, options){
					var respuesta = Ext.decode(data.responseText);
					
					if(respuesta.success==true){
						this.id_producto = respuesta.data[0].id_producto;
						//this.cmbProducto.setValue(respuesta.data[0].id_producto);
						this.cmbProducto.setRawValue(respuesta.data[0].descripcion);
						var precio = 0;
						if(this.cliente_estilista == 1)
							precio = respuesta.data[0].precio_estilista;
						else
							precio = respuesta.data[0].precio_venta;
						
						this.txtCosto.setValue(miErpWeb.formatearMoneda(precio));
						this.txtUnidadMedida.setValue(respuesta.data[0].codigo_unidad);
						// this.txtCantidad.setValue(miErpWeb.formatearCantidad(1));
						this.txtDescuento.setValue(miErpWeb.formatearMoneda(0));
						
						this.calcularConcepto(1,precio,0,0);
						// alert(this.agrega_concepto_auto);
						if(this.agregar_concepto_auto==1)
							this.agregarProducto();
						// this.txtCantidad.focus(true,100);
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
			
			var cant = this.txtCantidad.getValue();
			var prec = this.txtCosto.getValue();
			var desc = this.txtDescuento.getValue();
			var impu = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cant,prec,desc,impu);
			
			var id_producto = this.id_producto;
			var existe = false;
			var indexDetalle = 0;
			var cantidad = 0;
			var precio = this.txtCosto.getValue();
			var importe = 0;
			var descuento = 0;
			var subtotal = 0;
			var impuestos = 0;
			var total = 0;
			
			
			
			var detalles = this.gridDetalles.getStore().getRange();
			indexDetalle = detalles.length;
			
			for(var x = 0; x<detalles.length; x++){
				if(detalles[x].data.id_producto == id_producto && detalles[x].data.precio == precio ){
					existe = true;
					indexDetalle = x;
					cantidad = detalles[x].data.cantidad;
					importe = detalles[x].data.importe;
					descuento = detalles[x].data.descuento;
					subtotal = detalles[x].data.importe - detalles[x].data.descuento;
					impuestos = detalles[x].data.impuestos;
					total = detalles[x].data.total;					
				}	
			}
			
			if(existe == false){
				var record = new this.gridDetalles.store.recordType({
					id_producto: this.id_producto,
					descripcion: this.cmbProducto.getRawValue(), 
					unidad_medida: this.txtUnidadMedida.getValue(),
					cantidad: this.txtCantidad.getValue(),
					precio: this.txtCosto.getValue(),
					importe: this.txtImporte.getValue(),
					descuento: this.txtDescuento.getValue(),
					subtotal: this.txtImporte.getValue() - this.txtDescuento.getValue(),
					impuestos: this.txtImpuestos.getValue(),
					total: this.txtTotal.getValue()
				}, Ext.id());
				
				this.gridDetalles.getStore().insert(0,record);
			}else{
				
				var record = this.gridDetalles.getStore().getAt(indexDetalle);
				if(this.edicionDetalle){					
					var can = this.txtCantidad.getValue();
					var imp = this.txtImporte.getValue();
					var des = this.txtDescuento.getValue();
					var sub = (this.txtImporte.getValue() - this.txtDescuento.getValue());
					var imps = this.txtImpuestos.getValue();
					var tot = this.txtTotal.getValue();
				}else{
					var can = cantidad + this.txtCantidad.getValue();
					var imp = importe + this.txtImporte.getValue();
					var des = descuento + this.txtDescuento.getValue();
					var sub = subtotal + (this.txtImporte.getValue() - this.txtDescuento.getValue());
					var imps = impuestos + this.txtImpuestos.getValue();
					var tot = total + this.txtTotal.getValue();
					
				}
				
				
				
				record.set("cantidad",can);
				record.set("importe",imp);
				record.set("descuento",des);
				record.set("subtotal",sub);
				record.set("impuestos",imps);
				record.set("total",tot);
				
			
											
				this.gridDetalles.getStore().commitChanges();				
			}
			this.edicionDetalle=false;
			this.calcularTotales();
			this.frmDetalles.getForm().reset();
			this.txtCantidad.setValue(1);
			this.cmbProducto.setDisabled(false);
			this.cmbProducto.focus(true, 0);
			this.id_producto = 0;
			this.indexDetalle = 0;	
		
	},
	calcularConcepto:function(cantidad,costo,descuento,impuesto){
		var subtotal = 0.000000;
		var total = 0.000000;
		var totalimpuesto = 0.000000;
		var importe = 0.000000;
		var totaldescuento = 0.000000;
		
		importe = cantidad * costo;
		totaldescuento =  descuento;
		subtotal = importe - totaldescuento;
		totalimpuesto = subtotal * (impuesto/100);
		total = subtotal + totalimpuesto;		
		
		this.txtImporte.setValue(miErpWeb.formatearMoneda(importe));
		this.txtImpuestos.setValue(miErpWeb.formatearMoneda(totalimpuesto));
		this.txtTotal.setValue(miErpWeb.formatearMoneda(total));	
	},
	calcularTotales:function(){
			var i=0,c=0;
			var numrecs=this.gridDetalles.store.data.length;
			//var importe=0,descuento=0,subtotal=0,impuestos=0,total=0;
			var importe=0,descuento=0,subtotal=0,impuestos=0,total=0;	
			
			for (i=0; i<numrecs; i++){
				rec=this.gridDetalles.store.getAt(i);		
				
				importe		+=	parseFloat( rec.data.importe );
				descuento	+=	parseFloat( rec.data.descuento );
				subtotal	+=	parseFloat( rec.data.importe - rec.data.descuento );
				impuestos	+=	parseFloat(rec.data.impuestos );
				total		+=	parseFloat( rec.data.total );
				c += parseFloat( rec.data.cantidad );	
			}


			//importe = this.gridDetalles.getStore().sum('importe');
			//descuento = this.gridDetalles.getStore().sum('descuento');
			//impuestos = this.gridDetalles.getStore().sum('impuestos');
			//total = this.gridDetalles.getStore().sum('total');
			//subtotal = importe - descuento
			
			this.lblImporte.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(importe));
			this.lblDescuento.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(descuento + this.descuentoGeneral));
			this.lblSubtotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(subtotal - this.descuentoGeneral));
			this.lblImpuestos.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(impuestos));
			this.lblTotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total - this.descuentoGeneral));

			this.txtTotalImporte.setValue(importe);
			this.txtTotalDescuento.setValue(descuento + this.descuentoGeneral);
			this.txtTotalSubtotal.setValue(subtotal - this.descuentoGeneral);
			this.txtTotalImpuestos.setValue(impuestos);
			this.txtTotalMovimiento.setValue(total - this.descuentoGeneral);
			
			this.lblCantidadProductos.setValue(numrecs);
			this.lblUnidades.setValue(c);
		
	},
	inicializarStores:function(){
		this.gridDetalles.store = new miErpWeb.storePuntoVentaGrid();
		
		this.cmbCliente.store = new miErpWeb.storePuntoVentaClientes();	

		this.cmbAgente.store = new miErpWeb.storePuntoVentaAgentes();	
	},
	inicializarEvents:function(){
		var me = this;
		
		this.frmMain.on('actioncomplete',function(form,action){
			 if (action.result.success){
				 // this.cargarDatos(action.result.data);
				 // alert(action.result.data.id_venta);
				 this.txtIdVenta.setValue(action.result.data.id_venta);
				 this.imprimir();
				 
			 }else{				
				return false;
			}			
		}, this);
		
		this.on('afterrender', function(){			
			this.setConfiguracionInicial();
		}, this);	

		this.txtCantidad.on('blur',function(){
			var cantidad = this.txtCantidad.getValue();
			var precio = this.txtCosto.getValue();
			var descuento = this.txtDescuento.getValue();
			var impuestos = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cantidad,precio,descuento,impuestos);
		}, this);	

		this.txtCantidad.on("keydown", function(cmb, e){
				if(e.getKey()==13){
					this.cmbProducto.focus(false, true);
				}
		}, this);
		
		this.txtCosto.on('blur',function(){
			var cantidad = this.txtCantidad.getValue();
			var precio = this.txtCosto.getValue();
			var descuento = this.txtDescuento.getValue();
			var impuestos = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cantidad,precio,descuento,impuestos);
		}, this);	
		
		this.txtDescuento.on('blur',function(){
			var cantidad = this.txtCantidad.getValue();
			var precio = this.txtCosto.getValue();
			var descuento = this.txtDescuento.getValue();
			var impuestos = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cantidad,precio,descuento,impuestos);
		}, this);
		
		this.cmbVenta.onTriggerClick = function(){
				
				this.busquedaVenta = new formPuntoVentaWinVentas();
				this.busquedaVenta.show();
				
				this.busquedaVenta.on("ventaSeleccionada", function(Id,SerFol){
					
					me.id_venta = Id;
					// me.cmbVenta.setValue(SerFol);
					me.obtenerVenta(Id);
				}, this);
				
		}
		
		this.cmbProducto.onTriggerClick = function(){
				
				this.busquedaProducto = new formPuntoVentaWinProductos();
				this.busquedaProducto.show();
				
				this.busquedaProducto.on("productoSeleccionado", function(Id){
					
					me.id_producto = Id;
					me.cmbProducto.setValue('');
					me.aceptaProducto();
				}, this);
				
		}
		
		this.btnImprimir.on('click', function(){
			this.imprimir();
		}, this);
		
		this.cmbCliente.on('select',function(combo, record, index){
			this.cliente_estilista = record.get('estilista');
		}, this);
		
		this.btnPagar.on('click',function(){
			var numrecs=this.gridDetalles.store.data.length;
				if(numrecs<1)
				{
					Ext.Msg.alert('Aviso', 'Debe introducir detalles.');
					return;
				}	
			this.formasPagosVenta = new formPuntoVentaWinFormaPagos();
				this.formasPagosVenta.totalventa = this.txtTotalMovimiento.getValue();
				this.formasPagosVenta.show();
				
				this.formasPagosVenta.on("formapagoSeleccionada", function(Finalizada,FormasPagos, Importe, Cambio){
					this.formasPago = FormasPagos;
					this.importe = Importe;
					this.cambio = Cambio;
					if(Finalizada)					
						this.hacerSubmit();
				}, this);
		}, this);

		this.btnDescuento.on('click',function(){
			
			this.winDescuento = new winDescuentos();
			this.winDescuento.totalventa = Ext.num(this.txtTotalMovimiento.getValue(),0) + Ext.num(this.descuentoGeneral,0);
			this.winDescuento.show();
			
			this.winDescuento.on("descuentoSeleccionado", function( Importe){
				this.descuentoGeneral = Importe;
				this.calcularTotales();
				
			}, this);
		}, this);	
		
		this.btnCancelar.on('click',function(){
			//this.cancelar();
			this.winCancelaciones = new winCancelaciones();
			this.winCancelaciones.show();
			
			this.winCancelaciones.on("movimientocancelado", function(){
				this.cancelar();				
			}, this);
		}, this);	
		
		this.cmbProducto.on("keydown", function(cmb, e){
				if(e.getKey()==13){
					this.id_producto = 0;
					this.aceptaProducto();
				}
			}, this);
			
		this.btnAgregar.on('click',function(){
			this.agregarProducto();				
		}, this);
		
		this.btnLimpiar.on('click',function(){
			this.limpiar();				
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
					msg: "Est&aacute; seguro que desea borrar la captura?",
					width: 320,
					buttons: Ext.Msg.YESNO,
					fn: function(btn){
						if(btn == "yes"){
							this.gridDetalles.getStore().removeAt(rowIndex);
							this.gridDetalles.getSelectionModel().selectRow(0);
							// this.gridDetalles.getStore().reload();
							this.calcularTotales();
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
				this.txtUnidadMedida.setValue(record.data.unidad_medida);
				this.txtCantidad.setValue(Ext.util.Format.number(record.data.cantidad,'0.0000'));
				this.cmbProducto.setValue(record.data.id_producto);
				this.cmbProducto.setRawValue(record.data.descripcion);
				this.txtCosto.setValue(Ext.util.Format.number(record.data.precio,'0.00'));
				this.txtImporte.setValue(Ext.util.Format.number(record.data.importe,'0.00'));
				this.txtDescuento.setValue(Ext.util.Format.number(record.data.descuento,'0.00'));
				this.txtImpuestos.setValue(Ext.util.Format.number(record.data.impuestos,'0.00'));
				this.txtTotal.setValue(Ext.util.Format.number(record.data.total,'0.00'));
				this.edicionDetalle = true;
				this.cmbProducto.setDisabled(true);
				this.txtCantidad.focus(true, 0);
				
			
			}, this);	
	},
	inicializarRenders:function(){
		var colMod=this.gridDetalles.getColumnModel();
		
		var column=colMod.getColumnById("colCantidad");
		column.renderer=function(val){
			return Ext.util.Format.cantidadConSeparadorDeMiles(val);
		};	
		
		column=colMod.getColumnById("colCosto");
		column.renderer=this.renderMoneda;
		
		column=colMod.getColumnById("colImporte");
		column.renderer=this.renderMoneda;
		
		column=colMod.getColumnById("colDescuento");
		column.renderer=this.renderMoneda;
		
		column=colMod.getColumnById("colImpuestos");
		column.renderer=this.renderMoneda;
		
		column=colMod.getColumnById("colTotal");
		column.renderer=this.renderMoneda;
	},
    inicializarMaps:function(){
		var mapStop = new Ext.KeyMap(document,[
			{
				key:Ext.EventObject.F1,//IMPRIMIR
				ctrl: false,
				fn:function(key,e){
					if(!this.btnPagar.disabled){
						this.btnPagar.fireEvent("click");
					}
					
				},
				stopEvent: true,
				scope: this
			},
			{
				key:Ext.EventObject.F2,//IMPRIMIR
				ctrl: false,
				fn:function(key,e){
					if(!this.btnImprimir.disabled){
						this.btnImprimir.fireEvent("click");
					}
					
				},
				stopEvent: true,
				scope: this
			},
			{
				key:Ext.EventObject.F3,//IMPRIMIR
				ctrl: false,
				fn:function(key,e){
					if(!this.btnLimpiar.disabled){
						this.btnLimpiar.fireEvent("click");
					}
					
				},
				stopEvent: true,
				scope: this
			},
			{
				key:Ext.EventObject.F4,//IMPRIMIR
				ctrl: false,
				fn:function(key,e){
					if(!this.btnCancelar.disabled){
						this.btnCancelar.fireEvent("click");
					}
					
				},
				stopEvent: true,
				scope: this
			},
			{
				key:Ext.EventObject.F6,//IMPRIMIR
				ctrl: false,
				fn:function(key,e){
					if(!this.btnDevolucion.disabled){
						this.btnDevolucion.fireEvent("click");
					}
					
				},
				stopEvent: true,
				scope: this
			},
			{
				key:Ext.EventObject.F7,//IMPRIMIR
				ctrl: false,
				fn:function(key,e){
					if(!this.btnDescuento.disabled){
						this.btnDescuento.fireEvent("click");
					}
					
				},
				stopEvent: true,
				scope: this
			},
			{
				key:Ext.EventObject.P,//IMPRIMIR
				ctrl: true,
				fn:function(key,e){
					if(!this.cmbProducto.disabled){
						this.cmbProducto.onTriggerClick();
					}
					
				},
				stopEvent: true,
				scope: this
			},
			{
				key:Ext.EventObject.O,//IMPRIMIR
				ctrl: true,
				fn:function(key,e){
					if(!this.cmbVenta.disabled){
						this.cmbVenta.onTriggerClick();
					}
					
				},
				stopEvent: true,
				scope: this
			}
			
		]);	
	},
	initComponent: function() {
        formPuntoVenta.superclass.initComponent.call(this);
		/*this.on('afterrender',function(){
			if(mew.localprint == undefined)
			{
				this.createLocalPrint();
			}
			
		},this);*/
		this.inicializarStores();
		this.inicializarEvents();
		this.inicializarRenders();
		this.inicializarMaps();
    },
	renderMoneda:function(val){
		if (val<0){
			return "-$" + Ext.util.Format.monedaConSeparadorDeMiles(val*-1);
		}else{
			return "$" + Ext.util.Format.monedaConSeparadorDeMiles(val);
		}
		
	},
	hacerSubmit:function(){
		// var turno = miErpWeb.Turno[0].id_turno;
		
		// if(turno== 0){
			// Ext.Msg.alert('Aviso', 'No existe un turno abierto.');
			// return;
		// }
		
		if (this.frmMain.getForm().isValid()){
			var conceptos=gridToJson(this.gridDetalles);
			var fecha = this.txtFecha.getValue();
			fecha=fecha.format('Y-m-d'); 
			var params={};
			//params['Movimiento[id_empresa]'] = this.txtIdMovimientoAlmacen.getValue();
			// params['Venta[id_turno]'] = miErpWeb.Turno[0].id_turno;
			params['Venta[id_empresa]'] = miErpWeb.Empresa[0].id_empresa;
			params['Venta[id_sucursal]'] = miErpWeb.Sucursal[0].id_sucursal;
			params['Venta[id_almacen]'] = miErpWeb.Almacen[0].id_almacen;
			params['Venta[id_cliente]'] = this.cmbCliente.getValue();
			params['Venta[id_agente]'] = this.cmbAgente.getValue();
			params['Venta[id_serie]'] = this.id_serie_default;
			params['Venta[serie_venta]'] = this.nombreserie_default;
			params['Venta[folio_venta]'] = this.foliosig_default;
			params['Venta[fecha]'] = fecha; 
			params['Venta[hora]'] =this.txtHora.getValue();
			params['Venta[concepto_venta]'] = this.txtConceptoMovimiento.getValue();
					
			params['Venta[importe]'] = this.txtTotalImporte.getValue();
			params['Venta[descuento]'] = this.txtTotalDescuento.getValue();
			params['Venta[subtotal]'] = this.txtTotalSubtotal.getValue();
			params['Venta[impuestos]'] = this.txtTotalImpuestos.getValue();
			params['Venta[total]'] = this.txtTotalMovimiento.getValue();
			params['Venta[importe_pagado]'] = this.importe;
			params['Venta[cambio]'] = this.cambio;
			params['Conceptos']=conceptos;
			params['FormasPagos']=this.formasPago;
			
			// var id_tipomov = this.cmbTipoMovimiento.getValue();
		    // var store=this.cmbTipoMovimiento.getStore();
		    // var index=store.find('id_tipomovimiento', id_tipomov);
		    	 
		    // var rec=store.getAt(index);
			
			// params['Movimiento[tipo_movimiento]'] = rec.data.tipo_movimiento;
			
			
			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/ventas/save',
				success:function(data, options){
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
	obtenerVenta: function(Id){
		Ext.Ajax.request({
				scope: this,
				url: 'app.php/ventas/obtenerventa',
				params: {
					idVen: Id					
				},
				success: function(data, options){
					var respuesta = Ext.decode(data.responseText);
					
					if(respuesta.success==true){
						// alert(respuesta.data.Venta.id_venta);
						this.cargarDatos(respuesta.data);
						
						} else {
							Ext.Msg.alert('Aviso', 'La venta no existe, verifique por favor.', function(){
							// this.id_producto = 0;	
							// this.cmbProducto.focus(true, 0);
						}, this);
						
					}					
				}
			});
	},
	activarPuntoVenta: function(activo){
		if(activo == 'SI'){
			this.cmbVenta.setDisabled(false);
			this.txtFecha.setDisabled(false);
			this.txtHora.setDisabled(false);
			this.cmbCliente.setDisabled(false);
			this.txtConceptoMovimiento.setDisabled(false);
			this.txtCantidad.setDisabled(false);
			this.cmbProducto.setDisabled(false);
			this.txtUnidadMedida.setDisabled(false);
			this.txtCosto.setDisabled(false);
			this.txtDescuento.setDisabled(false);
			this.txtImpuestos.setDisabled(false);
			this.txtImporte.setDisabled(false);
			this.txtTotal.setDisabled(false);
			this.btnAgregar.setDisabled(false);
			this.gridDetalles.setDisabled(false);
			this.btnImprimir.setDisabled(true);
			this.btnCancelar.setDisabled(true);
			this.btnDevolucion.setDisabled(true);
			this.btnPagar.setDisabled(false);
			this.btnDescuento.setDisabled(false);
			this.cmbAgente.setDisabled(false);
		}else{
			this.cmbVenta.setDisabled(true);
			this.txtFecha.setDisabled(true);
			this.txtHora.setDisabled(true);
			this.cmbCliente.setDisabled(true);
			this.txtConceptoMovimiento.setDisabled(true);
			this.txtCantidad.setDisabled(true);
			this.cmbProducto.setDisabled(true);
			this.txtUnidadMedida.setDisabled(true);
			this.txtCosto.setDisabled(true);
			this.txtDescuento.setDisabled(true);
			this.txtImporte.setDisabled(true);
			this.txtImpuestos.setDisabled(true);
			this.txtTotal.setDisabled(true);
			this.btnAgregar.setDisabled(true);
			this.gridDetalles.setDisabled(true);
			this.btnImprimir.setDisabled(false);
			this.btnCancelar.setDisabled(false);
			this.btnDevolucion.setDisabled(false);
			this.btnPagar.setDisabled(true);
			this.btnDescuento.setDisabled(true);	
			this.cmbAgente.setDisabled(true);
		}
	},
	getParamsImprimir:function(){
		return {
			IDVen:this.txtIdVenta.getValue()
		};
	},
	imprimir:function(){
		/*
			 var imprimir = {
   	ConfigImpresora:[
	{ nombreImpresora: "PDFCreator",
    grafica: false,
    puerto: "",
    codigoCorte: "",
    codigoAperturaCajon: ""}
	
	],
	Extras:[{Extras:[{Nombre:"aaa",Impresora:"PDFCreator"}]}],
    datos: [
        { coordenadas: [80,80], texto: "27,64", fuente: "Arial", codigo: true },
        { coordenadas: [80,80], texto: "27,97,1", fuente: "Arial", codigo: true },
        { coordenadas: [80,80], texto: "27,33,0", fuente: "Arial", codigo: true },
        { coordenadas: [80,80], texto: "January 14, 2002  15:00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "27,100,2", fuente: "Arial", codigo: true},

        { coordenadas: [80,80], texto: "27,97,0", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "27,33,1", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "TM-U210B               $20.00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "TM-U210D               $21.00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "PS-170                 $17.00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "27,33,17", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "TOTAL                  $58.00", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "27,33,0", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "------------------------------", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "PAID                   $60.00", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "CHANGE                 $ 2.00", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "27,100,2", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "29,86,66,0", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "27,112,0,60,120", fuente: "Arial", codigo: true}
    ]
};
Ext.encode(imprimir);
		 printImpresionTicket(this, imprimir);
	
		
		
		
	*/
		
		
		var params=this.getParamsImprimir();
		
		if(this.impresion_ticket==0){
			Ext.Ajax.request({
			params: params,
			   url: 'app.php/ventas/generarreporteventa',
			   success: function(response, opts){
					//Solicita el PDF
					var obj = Ext.decode(response.responseText);
					if (!obj.success){	//Prosegir solo en caso de exito
						return;
					}
					var identificador=obj.data.identificador;
					window.open("app.php/ventas/getpdfventa?identificador="+identificador,'rep_mov',"height=600,width=800");							
				},
			   failure: function(){
					alert("El servidor ha respondido con un mensaje de error");
				}						   
			   
			});
		}
		
		if(this.impresion_ticket==1){
			Ext.Ajax.request({
			params: params,
			   url: 'app.php/ventas/generarreporteventa',
			   success: function(response, opts){
					//Solicita el PDF
					var obj = Ext.decode(response.responseText);
					if (!obj.success){	//Prosegir solo en caso de exito
						return;
					}
					var identificador=obj.data.identificador;
					window.open("app.php/ventas/getpdfventa?identificador="+identificador,'rep_mov',"height=600,width=800");							
				},
			   failure: function(){
					alert("El servidor ha respondido con un mensaje de error");
				}						   
			   
			});
		}
		this.setConfiguracionInicial();
	
	},
	muestraOcultaAgente: function(mostrar){
		if(mostrar){
			this.pnlAgente.setVisible(true);
			this.cmbAgente.allowBlank=false;
			this.cmbAgente.forceSelection=true;
		}else{
			this.pnlAgente.setVisible(false);
			this.cmbAgente.allowBlank=true;
			this.cmbAgente.forceSelection=false;
		}		
	},
	
	/*,
	createLocalPrint:function()
	{
		configlocalprint = {};
			
					
					mew.localprint = new LocalPrint({ 
						id: 'customhandler',
						version: '1.1.4',
						winPath: 'ImpresionLocal/LocalPrint.msi',
						macPath: '',
						debPath: ''
					});
						
					mew.localprint.on('installed',function(){ 
						mew.localprint.getMac(); 
					}, this);
						
					mew.localprint.on('mac', function(response){
						Ext.apply(mew,{
							Terminal : response.hostname,
							DireccionFisica : response.mac
						});
							
					}, this);
				
		
			
		
	}
	*/	
	getParamsCancelar:function(){
		return {
			IDVen:this.txtIdVenta.getValue()
		};
	},
	cancelar:function(){
		Ext.MessageBox.show({
			scope: this,
			title: "Aviso",
			msg: "Est&aacute; seguro que desea cancelar la venta?",
			width: 320,
			buttons: Ext.Msg.YESNO,
			fn: function(btn){
				if(btn == "yes"){
					var params=this.getParamsCancelar();		
					this.el.mask('Cancelando...');
					Ext.Ajax.request({
					scope:this,
					params: params,
					   url: 'app.php/ventas/eliminar',
					   success:function(data, options){
								this.el.unmask();
								this.setConfiguracionInicial();	
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
				}						
			}
		});
		
				
	}
});
Ext.reg('formPuntoVenta', formPuntoVenta);