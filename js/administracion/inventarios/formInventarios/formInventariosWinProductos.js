/*
 * File: formInventariosWinProductos.js
 * Date: Sat May 27 2017 12:00:49 GMT-0600 (Hora verano, Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be generated the first time you export.
 *
 * You should implement event handling and custom methods in this
 * class.
 */

formInventariosWinProductos = Ext.extend(formInventariosWinProductosUi, {
	inicializarStores:function(){
			this.gridProductos.store=new miErpWeb.storeFormInventariosWinProductos();
	},
	inicializarEvents:function(){
			this.gridProductos.store.on('beforeload',function(){
				this.gridProductos.store.baseParams=this.gridProductos.store.baseParams || {};
				this.gridProductos.store.baseParams.filtro=this.txtBusqueda.getValue();
				
			},this);
					
			this.gridProductos.store.on('load',function(){
				this.el.unmask();
			},this);
			
			this.btnFiltro.on('click',function(){
				this.gridProductos.store.reload();
			},this);
			
			this.gridProductos.on("keydown", function(e){
				if(e.getKey()==13){
					this.fireEvent("productoSeleccionado", this.gridProductos.getSelectionModel().getSelected().data.id_producto);
					this.close();
				}
			}, this);

			this.gridProductos.on("celldblclick", function(){
				this.fireEvent("productoSeleccionado", this.gridProductos.getSelectionModel().getSelected().data.id_producto);
				this.close();
			}, this);

					
	},
	initComponent: function() {
        formInventariosWinProductos.superclass.initComponent.call(this);
		this.inicializarStores();
		this.inicializarEvents();
    }
});
Ext.reg('formInventariosWinProductos', formInventariosWinProductos);