/*
 * File: formMovimientosAlmacenWinMovimientos.js
 * Date: Sun Jun 11 2017 16:13:12 GMT-0600 (Hora verano, Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be generated the first time you export.
 *
 * You should implement event handling and custom methods in this
 * class.
 */

formMovimientosAlmacenWinMovimientos = Ext.extend(formMovimientosAlmacenWinMovimientosUi, {
	renderPrecio:function(val,x,rec){
		return "$"+Ext.util.Format.monedaConSeparadorDeMiles(val);
	},
	inicializarRenders:function(){
		var colModel=this.gridMovimientos.getColumnModel();
        var columna=colModel.getColumnById('colTotalMovimiento');
        columna.renderer=this.renderPrecio
	},	
   inicializarStores:function(){
			this.gridMovimientos.store=new miErpWeb.storeMovimientosAlmacenWinMovimientos();
	},
	inicializarEvents:function(){
			var dt = new Date();			
			this.txtFechaInicio.setValue(dt);
			this.txtFechaFin.setValue(dt);
			this.gridMovimientos.store.on('beforeload',function(){
				this.gridMovimientos.store.baseParams=this.gridMovimientos.store.baseParams || {};
				this.gridMovimientos.store.baseParams.fechainicio=this.txtFechaInicio.getValue().dateFormat('Y-m-d');
				this.gridMovimientos.store.baseParams.fechafin=this.txtFechaFin.getValue().dateFormat('Y-m-d');
				this.gridMovimientos.store.baseParams.id_empresa=miErpWeb.Empresa[0].id_empresa;
				this.gridMovimientos.store.baseParams.id_sucursal=miErpWeb.Sucursal[0].id_sucursal;
				
			},this);
					
			this.gridMovimientos.store.on('load',function(){
				this.el.unmask();
			},this);
			
			this.btnFiltro.on('click',function(){
				this.gridMovimientos.store.reload();
			},this);
			
			this.gridMovimientos.on("keydown", function(e){
				if(e.getKey()==13){
					this.fireEvent("movimientoSeleccionado", this.gridMovimientos.getSelectionModel().getSelected().data.id_movimiento,this.gridMovimientos.getSelectionModel().getSelected().data.serie_folio);
					this.close();
				}
			}, this);

			this.gridMovimientos.on("celldblclick", function(){
				this.fireEvent("movimientoSeleccionado", this.gridMovimientos.getSelectionModel().getSelected().data.id_movimiento,this.gridMovimientos.getSelectionModel().getSelected().data.serie_folio);
				this.close();
			}, this);

					
	},
	initComponent: function() {
        formMovimientosAlmacenWinMovimientos.superclass.initComponent.call(this);
		this.inicializarStores();
		this.inicializarEvents();
		this.inicializarRenders();
    }
});
Ext.reg('formMovimientosAlmacenWinMovimientos', formMovimientosAlmacenWinMovimientos);
