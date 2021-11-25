/*
 * File: formReporteVentasProductos.js
 * Date: Thu Oct 12 2017 00:34:41 GMT-0600 (Hora verano, Montañas (México))
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
formReporteVentasProductos = Ext.extend(formReporteVentasProductosUi, {
    inicializarStores: function(){
		this.cmbLinea.store =  new miErpWeb.storeFormReporteVentasProductosLineas();
		this.cmbSucursal.store =  new miErpWeb.storeFormReporteVentasProductosSucursales();
		
		this.cmbSucursal.store.load();
		this.cmbLinea.store.load();
	},
	inicializarEvents: function(){
		var me = this;
		var dt = new Date();			
		this.txtFechaInicio.setValue(dt);
		this.txtFechaFin.setValue(dt);	
		
		this.cmbLinea.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbSucursal.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.btnEjecutar.on('click', function(){
			this.imprimir();
		}, this);
		
		this.cmbSucursal.onTriggerClick = function(a, e){
			if(e){
				if(e.getAttribute('class').indexOf('x-form-clear-trigger') > -1){
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}
					if(!Ext.isEmpty(me.cmbSucursal.getValue())){
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
		
		this.cmbLinea.onTriggerClick = function(a, e){
			if(e){
				if(e.getAttribute('class').indexOf('x-form-clear-trigger') > -1){
					if(this.isExpanded()){
						this.collapse();
						this.el.focus();
					}
					if(!Ext.isEmpty(me.cmbLinea.getValue())){
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
		
		this.cmbSucursal.store.on('load', function(){
			this.cmbSucursal.setValue(Ext.num(miErpWeb.Sucursal[0].id_sucursal,0));	
		}, this);
	},
    inizializaTpls:function(){
		this.cmbSucursal.tpl = new Ext.XTemplate(
			'<tpl for=".">'+
				'<div class="x-combo-list-item">'+
					'<div><b>{nombre_sucursal}</b></div>'+
					'<div><i>{nombre_empresa}</i></div>'+
				'</div>'+
			'</tpl>'
		);
		
		
	},
	initComponent: function() {
        formReporteVentasProductos.superclass.initComponent.call(this);
		this.inicializarStores();
		this.inicializarEvents();
		this.inizializaTpls();
    },
	getParamsImprimir:function(){
		return {
			IDEmp:miErpWeb.Empresa[0].id_empresa,
			IDSuc:Ext.num(this.cmbSucursal.getValue(),0),
			FechaIni:this.txtFechaInicio.getValue().dateFormat('Y-m-d'),
			FechaFin:this.txtFechaFin.getValue().dateFormat('Y-m-d'),
			IDLin:Ext.num(this.cmbLinea.getValue(),0)
		};
		
		
				
	},
	imprimir:function(){
		if (!this.getForm().isValid()) {	//<---Si hay errores informa al usuario
			 return false;
		}
		var params=this.getParamsImprimir();			
		Ext.Ajax.request({
		params: params,
		   url: 'app.php/ventas/generarreporteventasproductos',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				var identificador=obj.data.identificador;
				window.open("app.php/ventas/getpdfventasproductos?identificador="+identificador,'rep_mov',"height=600,width=800");							
			},
		   failure: function(){
				alert("El servidor ha respondido con un mensaje de error");
			}						   
		   
		});
	}
});
Ext.reg('formReporteVentasProductos', formReporteVentasProductos);