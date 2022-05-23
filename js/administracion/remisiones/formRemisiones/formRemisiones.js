/*
 * File: formRemisiones.js
 * Date: Mon Apr 17 2017 23:22:08 GMT-0600 (Hora verano, Montañas (México))
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
formRemisiones = Ext.extend(formRemisionesUi, {
	IDModulo:3,
	id_producto:0,
	edicion:false,
	edicionDetalle:false,
	indexDetalle:0,
	aplicado:0,
	foraneo:0,
	credito:0,
	porcentaje_credito:0.00,
	porcentaje_foraneos:0.00,
	importe:0.00,
	descuentoGeneral:0.00,
	getConfiguracionEmpresa: function(){
			
		this.porcentaje_foraneos=0;
		this.porcentaje_credito=0;
		this.importe=0.00;
		this.descuentoGeneral=0.00;
				
		Ext.Ajax.request({
			scope:this,
			params:{
				id_empresa:miErpWeb.Empresa[0].id_empresa
				
			},
		   url: 'app.php/remisiones/obtenerconfiguracionempresa',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				// alert(obj.data[0].agrega_concepto_auto);
				this.porcentaje_credito = obj.data[0].porcentaje_credito;
				this.porcentaje_foraneos = obj.data[0].porcentaje_foraneos;
																
			},
		   failure: function(){
				alert("El servidor ha respondido con un mensaje de error");
			}						   
		   
		});		
	},
	inicializarStores:function(){
		this.cmbSerie.store =  new miErpWeb.storeFormRemisionesSeries();
		this.cmbCondicionPago.store =  new miErpWeb.storeFormRemisionesCondicionesPago();
		var data=new Array(
					{id:'1',nombre:miErpWeb.formatearTexto('CONTADO')},
					{id:'2',nombre:miErpWeb.formatearTexto('CREDITO')}
			);
		this.cmbCondicionPago.store.loadData({data:data});
		this.cmbCondicionPago.setValue('1');
		
		
		this.cmbCliente.store =  new miErpWeb.storeFormRemisionesClientes();		
		
		this.cmbProducto.store =  new miErpWeb.storeFormRemisionesProductos();
		
		this.gridDetalles.store = new miErpWeb.storeFormRemisionesGrid();
		this.cmbAgente.store =  new miErpWeb.storeFormRemisionesAgentes();
		
	},
	inicializarEvents:function(){
		
		this.on('afterrender', function(){			
			this.getConfiguracionEmpresa();
		}, this);	
		
		this.frmMain.on('actioncomplete',function(form,action){
			 if (action.result.success){
				 this.cargarDatos(action.result.data);
				 
			 }else{				
				return false;
			}			
		}, this);	
		
		
		this.cmbAgente.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbCliente.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		
		this.cmbProducto.updateAlways = true;
		
		this.cmbProducto.on("keydown", function(cmb, e){
				if(e.getKey()==13){
					this.id_producto = 0;
					this.aceptaProducto();
				}
			}, this);
		 this.txtConcepto.setValue=function(value){
			value=miErpWeb.formatearTexto(value);
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	//this.cambioDeNombre(value);
			this.fireEvent('cambioDeNombre',value);
		}; 
		this.txtCantidad.on('blur',function(){
			var cantidad = this.txtCantidad.getValue();
			var costo = this.txtCosto.getValue();
			var descuento = this.txtDescuento.getValue();
			var impuestos = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cantidad,costo,descuento,impuestos);
		}, this);	
		
		this.txtCosto.on('blur',function(){
			var cantidad = this.txtCantidad.getValue();
			var costo = this.txtCosto.getValue();
			var descuento = this.txtDescuento.getValue();
			var impuestos = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cantidad,costo,descuento,impuestos);
		}, this);	
		
		this.txtDescuento.on('blur',function(){
			var cantidad = this.txtCantidad.getValue();
			var costo = this.txtCosto.getValue();
			var descuento = this.txtDescuento.getValue();
			var impuestos = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cantidad,costo,descuento,impuestos);
		}, this);
		
		this.btnAgregar.on('click',function(){
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
				this.txtCosto.setValue(Ext.util.Format.number(record.data.costo,'0.00'));
				this.txtImporte.setValue(Ext.util.Format.number(record.data.importe,'0.00'));
				this.txtDescuento.setValue(Ext.util.Format.number(record.data.descuento,'0.00'));
				this.txtImpuestos.setValue(Ext.util.Format.number(record.data.impuestos,'0.00'));
				this.txtTotal.setValue(Ext.util.Format.number(record.data.total,'0.00'));
				this.edicionDetalle = true;
				this.cmbProducto.setDisabled(true);
				this.txtCantidad.focus(true, 0);
				
			
			}, this);
		
		this.txtCantidad.on('specialkey',function(txt,e){
			if (e.getCharCode()==e.ENTER){
				this.txtCosto.focus(true,0);
			}			
		},this);
		
		this.txtCosto.on('specialkey',function(txt,e){
			if (e.getCharCode()==e.ENTER){
				this.txtDescuento.focus(true,0);
			}			
		},this);
		
		this.txtDescuento.on('specialkey',function(txt,e){
			if (e.getCharCode()==e.ENTER){
				this.btnAgregar.focus(true,0);
			}			
		},this);
		
		this.txtCantidad.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};
		
		this.txtCosto.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};
		
		this.txtImporte.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};
		
		this.txtDescuento.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};
		
		this.txtImpuestos.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};
		
		this.txtTotal.setValue=function(value){
			if (value!=''){
				value=miErpWeb.formatearMoneda(value);
			}			
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
		};
		
		this.cmbSerie.on('select',function(combo, record, index){
			this.txtFolio.setValue(record.get('foliosig'));				
		}, this);
		
		this.cmbCliente.on('select',function(combo, record, index){
			this.foraneo = record.get('foraneo');	

			this.calcularTotales();	
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
		
		var me = this;
		this.cmbProducto.onTriggerClick = function(){
				
				this.busquedaProducto = new formRemisionesWinProductos();
				this.busquedaProducto.show();
				
				this.busquedaProducto.on("productoSeleccionado", function(Id){
					
					me.id_producto = Id;
					me.cmbProducto.setValue('');
					me.aceptaProducto();
				}, this);
				
		}
		/*	
		var VentanaSKUS = new VentanaSeries(ventanaConfig);

				VentanaSKUS.on("SKUsSeleccionados", function(seleccionados){
					var record = this.gridDetalles.getSelectionModel().getSelected();
					record.data.SKUs = seleccionados;
				}, this);

				VentanaSKUS.on("VentanaCerrada", function(){
					var record = this.gridDetalles.getSelectionModel().getSelected();
					if(record.data.SKUs=="[]" || record.data.SKUs=="" || record.data.SKUs==undefined)
						if(record.data.Edicion!=1)
							this.gridDetalles.getStore().remove(record);
				}, this);

				VentanaSKUS.show();
		
		*/
		
		
		this.btnGuardar.on('click', function(){
			this.guardar();
		}, this );
		
		this.btnEliminar.on('click',function(){	
			this.eliminar();
			
		},this);
	
		this.btnTicket.on('click', function(){
			this.imprimirTicket();
		}, this);
		
		this.btnPDF.on('click', function(){
			this.imprimirPDF();
		}, this);
		
		this.btnAplicar.on('click', function(){
			this.aplicar();
		}, this );
	
		this.cmbCondicionPago.on('select',function(combo, record, index){
			this.credito = this.cmbCondicionPago.getValue();

			this.calcularTotales();			
		}, this);
	
		this.btnDescuento.on('click',function(){
			
			this.winDescuento = new winDescuentos();
			this.winDescuento.totalventa = Ext.num(this.txtTotalRemision.getValue(),0) + Ext.num(this.descuentoGeneral,0);
			this.winDescuento.show();
			
			this.winDescuento.on("descuentoSeleccionado", function( Importe){
				this.descuentoGeneral = Importe;
				this.calcularTotales();
				
			}, this);
		}, this);	
	
		/*this.btnAuditoria.on('click',function(){
			
			this.winAuditoria = new winAuditoria();
			this.winAuditoria.IdRegistro = Ext.num(this.txtIdRemision.getValue(),0);
			this.winAuditoria.IDModulo = Ext.num(this.IDModulo,0);
			this.winAuditoria.show();
			
			
		}, this);*/	
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
	initComponent: function() {
		formRemisiones.superclass.initComponent.call(this);
		
		this.txtStatus.setValue=function(value){        	
        	Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeStatus',{status:value});
		};
		
		 this.txtIdRemision.setValue=function(value){
			Ext.form.TextField.prototype.setValue.apply(this,arguments);
        	this.fireEvent('cambioDeId',{id:value});
			
        };
		
		this.inicializarStores();
		this.inicializarEvents();
		this.inicializarRenders();
	},
	aceptaProducto:function(){
		if(this.cmbProducto.getValue != "" || this.id_producto > 0 ){
			//this.id_producto = 0;
			Ext.Ajax.request({
				scope: this,
				url: 'app.php/remisiones/obtenerproducto',
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
						this.txtCosto.setValue(miErpWeb.formatearMoneda(respuesta.data[0].precio_compra));
						this.txtUnidadMedida.setValue(respuesta.data[0].codigo_unidad);
						this.txtCantidad.setValue(miErpWeb.formatearCantidad(1));
						this.txtDescuento.setValue(miErpWeb.formatearMoneda(0));
						
						this.calcularConcepto(1,respuesta.data[0].precio_compra,0,0);
						
						this.txtCantidad.focus(true,100);
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
			var cost = this.txtCosto.getValue();
			var desc = this.txtDescuento.getValue();
			var impu = this.txtImpuestos.getValue();
						
			this.calcularConcepto(cant,cost,desc,impu);
			
			var id_producto = this.id_producto;
			var existe = false;
			var indexDetalle = 0;
			var cantidad = 0;
			var costo = this.txtCosto.getValue();
			var importe = 0;
			var descuento = 0;
			var subtotal = 0;
			var impuestos = 0;
			var total = 0;
			
			
			
			var detalles = this.gridDetalles.getStore().getRange();
			indexDetalle = detalles.length;
			
			for(var x = 0; x<detalles.length; x++){
				if(detalles[x].data.id_producto == id_producto && detalles[x].data.costo == costo ){
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
					costo: this.txtCosto.getValue(),
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
			this.calcularTotales();
			this.frmDetalles.getForm().reset();
			this.cmbProducto.setDisabled(false);
			this.cmbProducto.focus(true, 0);
			this.id_producto = 0;
			this.edicionDetalle=false;
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
			var importe=0,descuento=0,subtotal=0,impuestos=0,total=0,comision=0,importecredito=0,importeforaneo=0;	
			
			for (i=0; i<numrecs; i++){
				rec=this.gridDetalles.store.getAt(i);		
				
				importe+=	parseFloat( rec.data.importe );
				descuento+=	parseFloat( rec.data.descuento );
				subtotal+=parseFloat( rec.data.importe - rec.data.descuento );
				impuestos+=	parseFloat(rec.data.impuestos );
				total+=		parseFloat( rec.data.total );
				c += parseFloat( rec.data.cantidad );				
			}
			
			if(this.foraneo == 1){
				importeforaneo = (subtotal - this.descuentoGeneral) * (this.porcentaje_foraneos/100);
				comision = comision + importeforaneo;
			}
			
			if(this.credito == 2){
				importecredito = (subtotal - this.descuentoGeneral) * (this.porcentaje_credito/100);
				comision = comision + importecredito;
			}
						
			total = total + comision;
			
			this.lblImporte.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(importe));
			this.lblDescuento.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(descuento + this.descuentoGeneral));
			this.lblSubtotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(subtotal - this.descuentoGeneral));
			this.lblComision.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(comision));
			this.lblImpuestos.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(impuestos));
			this.lblTotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total - this.descuentoGeneral));

			this.txtTotalImporte.setValue(importe);
			this.txtTotalDescuento.setValue(descuento + this.descuentoGeneral);
			this.txtTotalSubtotal.setValue(subtotal - this.descuentoGeneral);
			this.txtTotalComision.setValue(comision);
			this.txtTotalImpuestos.setValue(impuestos);
			this.txtTotalRemision.setValue(total - this.descuentoGeneral);
			
			this.lblCantidadProductos.setValue(numrecs);
			this.lblUnidades.setValue(c);
	},
	renderMoneda:function(val){
		if (val<0){
			return "-$" + Ext.util.Format.monedaConSeparadorDeMiles(val*-1);
		}else{
			return "$" + Ext.util.Format.monedaConSeparadorDeMiles(val);
		}
		
	},
	cargarDatos:function(data){
		if (data.Remision==undefined ){
			Ext.Msg.show({
				   title:'Error ',
				   msg: 'Error en los datos del movimiento',
				   buttons: Ext.Msg.OK,				   				   
				   icon: Ext.MessageBox.WARNING
				});
			// miErpWeb.tabContainer.remove(this);	
			
			return;
		}
		var remision=data.Remision;
		
		var form=this.frmMain.getForm();		
        // form.setValues(movimiento);
		this.txtIdRemision.setValue(remision.id_remision);
        var fechaMov=remision.fecha;
	
		var dt = Date.parseDate(fechaMov, "d/m/Y H:i:s");
	       
        this.txtFecha.setValue(dt);
        this.txtHora.setValue(dt.format('H:i:s A'));
		this.txtFolio.setValue(remision.folio);
		this.txtConcepto.setValue(remision.concepto);
		
		var importe = remision.importe;
		var descuento = remision.descuento;
		var subtotal = remision.subtotal;
		var comision = remision.comision;
		var impuestos = remision.impuestos;
		var total = remision.total;
		
		this.lblImporte.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(importe));
		this.lblDescuento.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(descuento));
		this.lblSubtotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(subtotal));
		this.lblComision.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(comision));
		this.lblImpuestos.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(impuestos));
		this.lblTotal.setValue("$"+Ext.util.Format.monedaConSeparadorDeMiles(total));

		this.txtTotalImporte.setValue(importe);
		this.txtTotalDescuento.setValue(descuento);
		this.txtTotalSubtotal.setValue(subtotal);
		this.txtTotalComision.setValue(comision);
		this.txtTotalImpuestos.setValue(impuestos);
		this.txtTotalRemision.setValue(total);
		
		if (remision.id_remision==0){
			this.cmbSerie.store.baseParams.id_empresa=remision.id_empresa;		
			this.cmbSerie.store.baseParams.id_sucursal=remision.id_sucursal;	
			this.cmbSerie.store.on('load',this.cargarPrimerFolio,this);	
			this.cmbSerie.store.load();
			
		}else{/*	SI LA FACTURA YA EXISTE EN EL SERVIDOR, SE ESTABLECE UN NUEVO TITULO Y EL ICONO DEL TAB			*/
			var series={
				id_serie:remision.id_serie,
				nombre_serie:remision.serie
			};
			this.cmbSerie.store.loadData({data:series});
			this.cmbSerie.setValue(series.id_serie);
			this.cmbSerie.setDisabled(true);
			
			this.cmbCondicionPago.setValue(remision.condicion_pago);
			// var condicionpago={
				// id_tipomovimiento:remision.con,
				// nombre_movimiento:movimiento.nombre_movimiento,
				// tipo_movimiento:movimiento.tipo_movimiento
			// };
			// this.cmbTipoMovimiento.store.loadData({data:tipos_movimientos});
			
			// var storeTipoMovimiento=this.cmbTipoMovimiento.store;
			// var index=storeTipoMovimiento.find('id_tipomovimiento',movimiento.id_tipomovimiento);
			// var rec=storeTipoMovimiento.getAt(index);
			
			// this.cmbTipoMovimiento.setValue(tipos_movimientos.id_tipomovimiento);
			// this.cmbTipoMovimiento.fireEvent('select',this.cmbTipoMovimiento,rec);
			
			// this.cmbTipoMovimiento.setDisabled(true);
			
			// this.cmbAgente.setDisabled(true);
			// this.cmbCliente.setDisabled(true);
			this.txtFecha.setDisabled(true);
			this.txtHora.setDisabled(true);
			this.txtFolio.setDisabled(true);
			
			this.aplicado = remision.aplicado;
			this.btnImprimir.setDisabled(false);
			this.btnAplicar.setDisabled(false);
			if(remision.aplicado == 0){
				this.btnAplicar.setText('Aplicar');
				this.btnAplicar.setIcon('images/iconos/Activo.png');
				this.btnEliminar.setDisabled(false);
				this.btnGuardar.setDisabled(false);					
			}else{
				this.btnAplicar.setText('Desaplicar');
				this.btnAplicar.setIcon('images/iconos/desaplicar.png');
				this.btnEliminar.setDisabled(true);	
				this.btnGuardar.setDisabled(true);					
				
			} 
				// this.btnAplicar.setDisabled(false);
			
			this.setTitle(remision.id_remision+"-"+remision.concepto);
						
		}
		
		
		
		// ALMACEN DE ORIGEN
			
			// AGENTE
			var agente={
				id_agente:remision.id_agente,
				nombre_agente:remision.nombre_agente
			};
			this.cmbAgente.store.loadData({data:agente});
			this.cmbAgente.setValue(agente.id_agente);
			
			var cliente={
				id_cliente:remision.id_cliente,
				nombre_cliente:remision.nombre_cliente,
				foraneo:remision.foraneo
			};
			this.cmbAgente.store.loadData({data:agente});
			this.cmbAgente.setValue(agente.id_agente);
			
			
			
			this.cmbCliente.store.loadData({data:cliente});
			
			var storeCliente=this.cmbCliente.store;
			var index=storeCliente.find('id_cliente',remision.id_cliente);
			var rec=storeCliente.getAt(index);
			
			this.cmbCliente.setValue(cliente.id_cliente);


						
			this.cmbCondicionPago.fireEvent('select', this.cmbCondicionPago);
			
			if(Ext.num(cliente.id_cliente,0) > 0)
				this.cmbCliente.fireEvent('select',this.cmbCliente,rec);
			// this.cmbTipoMovimiento.el.focus(false);
		
		
		//Cargar detalles
		var detalles=data.Detalles;
        if(detalles!=undefined){			
            this.gridDetalles.store.loadData({
                data:detalles
            });
			
			var i=0,c=0;
			var numrecs=this.gridDetalles.store.data.length;
			//var importe=0,descuento=0,subtotal=0,impuestos=0,total=0;
			var desc=0;	
			
			for (i=0; i<numrecs; i++){
				rec=this.gridDetalles.store.getAt(i);		
				desc +=	parseFloat( rec.data.descuento );
						
			}
			
			if(descuento > 0)
			this.descuentoGeneral = descuento - desc;
			
			
            this.calcularTotales();
        }	

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
			var conceptos=gridToJson(this.gridDetalles);
			var fecha = this.txtFecha.getValue();
			fecha=fecha.format('Y-m-d');   
;   
			var params={};
			//params['Movimiento[id_empresa]'] = this.txtIdMovimientoAlmacen.getValue();
			params['Remision[id_empresa]'] = miErpWeb.Empresa[0].id_empresa;
			params['Remision[id_sucursal]'] = miErpWeb.Sucursal[0].id_sucursal;
			params['Remision[id_almacen]'] = miErpWeb.Almacen[0].id_almacen;
			params['Remision[id_remision]'] = this.txtIdRemision.getValue();
			params['Remision[id_serie]'] = this.cmbSerie.getValue();
			params['Remision[nombre_serie]'] = this.cmbSerie.getRawValue();
			params['Remision[folio]'] = this.txtFolio.getValue();
			params['Remision[fecha]'] = fecha; 
			params['Remision[hora]'] =this.txtHora.getValue();
			params['Remision[concepto]'] = this.txtConcepto.getValue();
			params['Remision[condicion_pago]'] = this.cmbCondicionPago.getValue();	
			params['Remision[id_cliente]'] = this.cmbCliente.getValue();			
			params['Remision[id_agente]'] = this.cmbAgente.getValue();
			params['Remision[importe]'] = this.txtTotalImporte.getValue();
			params['Remision[descuento]'] = this.txtTotalDescuento.getValue();
			params['Remision[subtotal]'] = this.txtTotalSubtotal.getValue();
			params['Remision[comision]'] = this.txtTotalComision.getValue();
			params['Remision[impuestos]'] = this.txtTotalImpuestos.getValue();
			params['Remision[total]'] = this.txtTotalRemision.getValue();
			params['Remision[status]'] = this.txtStatus.getValue();

			params['Conceptos']=conceptos;
			
			this.el.mask('Guardando...');
			this.frmMain.getForm().submit({
				params:params,
				scope:this,
				url:'app.php/remisiones/save',
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
					}
				});
				
			
		}else{
			return;
			
		}	
		
		
	},
	getParamsImprimir:function(){
		return {
			IDRem:this.txtIdRemision.getValue()
		};
	},
	imprimirPDF:function(){
		var params=this.getParamsImprimir();			
		Ext.Ajax.request({
		params: params,
		   url: 'app.php/remisiones/generarreporteremision',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				var identificador=obj.data.identificador;
				window.open("app.php/remisiones/getpdfremision?identificador="+identificador,'rep_rem',"height=600,width=800");							
			},
		   failure: function(){
				alert("El servidor ha respondido con un mensaje de error");
			}						   
		   
		});
	},
	imprimirTicket:function(){
		var params=this.getParamsImprimir();			
		Ext.Ajax.request({
		params: params,
		   url: 'app.php/remisiones/generarreporteremisionticket',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				var identificador=obj.data.identificador;
				window.open("app.php/remisiones/getpdfremisionticket?identificador="+identificador,'rep_rem',"height=600,width=800");							
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
			params: { id_remision: this.txtIdRemision.getValue() },
			scope:this,
		   	url: 'app.php/remisiones/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.fireEvent('eliminado',options.params.id_remision);
				MainContainer.tabContainer.remove(this);
		   	},
		   	failure: function(){
		   		this.el.unmask();
		   	}		   
		});
	},
	aplicar:function(btn){
		switch(btn){	//ESTE SWITCH ES USADO PARA ANALIZAR LO QUE TRATA DE HACER EL USUARIO, LA PRIERA VEZ DEBE ENTRAR A default:
    	case 'no':
    		return;
    	break;
    	case 'yes':
    		this.aplicar('aplicar');
    		return;
    		break;
    	case 'aplicar':
    		break;		//SALE DEL SWITCH Y SIGUE EJECUTANDOSE LA FUNCI�N
    	case undefined:	//AQUI ENTRA LA PRIMERA VEZ
    	case false:    		
    	default:
    		var me=this;    		
    		Ext.Msg.show({
 			   title:'Confirme por favor',
 			   msg: "¿Desea aplicar/desaplicar la remision?",
 			   buttons: Ext.Msg.YESNO,
 			   fn: function(btn){	    				
    				me.aplicar(btn);
    			},
 			   scope:this,
 			   icon: Ext.MessageBox.QUESTION
 			});
    		return;
		} 	
		this.el.mask(mew.mensajeDeEspera);
		Ext.Ajax.request({
			params: { id_remision: this.txtIdRemision.getValue(),aplicado: this.aplicado },
			scope:this,
		   	url: 'app.php/remisiones/aplicar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}					
				
				var remision=respuesta.data.Remision;
				
				this.aplicado = remision.aplicado;
				this.btnImprimir.setDisabled(false);
				this.btnAplicar.setDisabled(false);
				if(remision.aplicado == 0){
					this.btnAplicar.setText('Aplicar');
					this.btnAplicar.setIcon('images/iconos/Activo.png');
					this.btnEliminar.setDisabled(false);
					this.btnGuardar.setDisabled(false);					
				}else{
					this.btnAplicar.setText('Desaplicar');
					this.btnAplicar.setIcon('images/iconos/desaplicar.png');
					this.btnEliminar.setDisabled(true);	
					this.btnGuardar.setDisabled(true);					
					
				}
				this.el.unmask();	
				
		   	},
		   	failure: function(){
		   		this.el.unmask();
		   	}		   
		});
		// this.el.unmask();
	},
	listeners:{
    	activate:function(){
			
    		if (this.activado==true){
    			return;
    		}
    		this.activado=true;
    	
			if (this.idValue!=undefined && this.idValue!=0){
    			this.txtIdRemision.setValue(this.idValue);
				//this.el.mask(mfw.mensajeDeEspera);    			
    		}
                          
			this.frmMain.load({
				params:{idRem:this.idValue,
						id_empresa:miErpWeb.Empresa[0].id_empresa,
						id_sucursal:miErpWeb.Sucursal[0].id_sucursal,
						id_almacen:miErpWeb.Almacen[0].id_almacen				
				},
				url:'app.php/remisiones/obtenerremision'
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
Ext.reg('formRemisiones', formRemisiones);