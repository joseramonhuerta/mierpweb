/*
 * File: formReporteExistencia.js
 * Date: Sun Jun 18 2017 23:47:22 GMT-0600 (Hora verano, Montañas (México))
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
formReporteExistencia = Ext.extend(formReporteExistenciaUi, {
	inicializarStores: function(){
		this.cmbAlmacen.store =  new miErpWeb.storeFormReporteExistenciaAlmacenes();
		
		this.cmbLinea.store =  new miErpWeb.storeFormReporteExistenciaLineas();
	},
	inicializarEvents: function(){
		var me = this;
		this.cmbAlmacen.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbLinea.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbAlmacen.store.on('beforeload',function(){
			this.cmbAlmacen.store.baseParams.id_empresa=miErpWeb.Empresa[0].id_empresa;
			this.cmbAlmacen.store.baseParams.id_sucursal=miErpWeb.Sucursal[0].id_sucursal;
		},this);
		
		this.btnPDF.on('click', function(){
			this.imprimir();
		}, this);
		
		this.btnExcel.on('click', function(){
			this.imprimirExcel();
		}, this);
		
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
		
	},
    initComponent: function() {
        formReporteExistencia.superclass.initComponent.call(this);
		this.inicializarStores();
		this.inicializarEvents();
    },
	getParamsImprimir:function(){
		return {
			IDAlm:this.cmbAlmacen.getValue(),
			IDLin:this.cmbLinea.getValue()
		};
	},
	imprimir:function(){
		if (!this.getForm().isValid()) {	//<---Si hay errores informa al usuario
			 return false;
		}

		var params=this.getParamsImprimir();			
		Ext.Ajax.request({
		params: params,
		   url: 'app.php/productos/generarreporteexistencia',
		   success: function(response, opts){
				//Solicita el PDF
				var obj = Ext.decode(response.responseText);
				if (!obj.success){	//Prosegir solo en caso de exito
					return;
				}
				var identificador=obj.data.identificador;
				window.open("app.php/productos/getpdfexistencia?identificador="+identificador,'rep_mov',"height=600,width=800");							
			},
		   failure: function(){
				alert("El servidor ha respondido con un mensaje de error");
			}						   
		   
		});
	},
	imprimirExcel:function(){
		if (!this.getForm().isValid()) {	//<---Si hay errores informa al usuario
			 return false;
		}
		var params=this.getParamsImprimir();				
		
		location.href = "app.php/productos/generarreporteexistenciaexcel?IDAlm="+params.IDAlm+"&IDLin="+params.IDLin;
		
		//window.open("app.php/ventas/getpdfventasproductos?identificador="+identificador,'rep_mov',"height=600,width=800");
	}
});
Ext.reg('formReporteExistencia', formReporteExistencia);