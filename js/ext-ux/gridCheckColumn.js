/**
 * @author Isaac Peraza/Omar Vidaña y Otro
 * Fecha: 20 Abril de 2009
 * Modificacion:  01 Octurbe de 2009
 * Componente Ext.grid.CheckColumn para agregar al column model
 * Plugin del Ext.grid que contiene la funcionalidad del checkbox de las celdas y del headers.
 */
 




Ext.grid.CheckColumn = function(config){

	this.checkHeader = true;
	
	this.header = '&#160;';
	
	this.width = 20;
	
	this.sortable = false;
	
	this.menuDisabled = true;
	
	//Para que no cambie de tamaño la columna.
	this.fixed = true;
	
	this.renderer = this.renderer.createDelegate(this);
	
	Ext.apply(this, config);
	
	if(!this.id){
		this.id = Ext.id();
	}
};



Ext.grid.CheckColumn.prototype = {
	init : function(grid){
		
		var chk = this;
		Ext.apply(grid,{
			
			onKeyDown : function(e, t){
				if(e.getKey() == 13 || e.getKey() == 32 || (t.className && t.className.indexOf('x-grid3-check-col-td') != -1)){
					var cell = this.getSelectionModel().getSelectedCell();
					if (Ext.get(this.getView().getCell(cell[0], cell[1])).child('div.x-grid3-cc-' + chk.id)) {
						chk.onMouseDown(e, Ext.get(this.getView().getCell(cell[0], cell[1])).child('div.x-grid3-cc-' + chk.id).dom);
					}
				}
				this.fireEvent('keydown', e);
			},
			
			checkAll : function(dataIndex, checkRecords){
				
				var view = this.getView();
				var index = this.colModel.findColumnIndex(dataIndex);
				//Se checa header si no esta checado.
				if (Ext.get(view.getHeaderCell(index)).child('div.x-grid3-check-col')) {
					Ext.get(view.getHeaderCell(index)).child('div.x-grid3-check-col').replaceClass('x-grid3-check-col', 'x-grid3-check-col-on');
				}
				
				//Si checkRecords == true se checa cada una de las columnas no checadas
				if (checkRecords) {
					var record = this.store.getAt(0);
					if (record) {
						this.store.each(function(record){
							if (record.get(dataIndex) == 0) 
								record.set(dataIndex, 1);
						}, this);
					}
				}
			},
			
			unCheckAll : function(dataIndex, unCheckRecords){
				var view = this.getView();
				var index = this.colModel.findColumnIndex(dataIndex);
				//Se descheca header si no esta checado.
				if (Ext.get(view.getHeaderCell(index)).child('div.x-grid3-check-col')) {
					Ext.get(view.getHeaderCell(index)).child('div.x-grid3-check-col').replaceClass('x-grid3-check-col-on', 'x-grid3-check-col');
				}
				
				//Si unCheckRecords == true se descheca cada una de las columnas no deschecadas
				if (unCheckRecords) {
					var record = this.store.getAt(0);
					if (record) {
						this.store.each(function(record){
							if (record.get(dataIndex) == 1) 
								record.set(dataIndex, 0);
						}, this);
					}
				}
			}

		});
		
		this.grid = grid;
		this.grid.on('render', function(){
			
			var view = this.grid.getView();
			
			view.mainBody.on('mousedown', this.onMouseDown, this);

			// Si estara visible el checkHeader
			if (this.checkHeader) {
				Ext.fly(view.innerHd).select('div.x-grid3-hd-'+this.id).addClass('x-grid3-check-col');
				view.mainBody.on('keydown', grid.onKeyDown, grid);
				
				Ext.fly(view.innerHd).on('mousedown', this.onHdMouseDown, this);
				
				this.grid.store.on('beforeload', function(){
					Ext.fly(view.innerHd).select('div.x-grid3-check-col-on').replaceClass('x-grid3-check-col-on', 'x-grid3-check-col');
				}, this);
				
				var mview = view; // LMNT - Para volver a mostrar el check del encabezado ya que lo desaparecia al recargar el grid
				this.grid.store.on('load', function(){
					
					// LMNT - Aqui se vuelve a mostrar el check
					Ext.fly(mview.innerHd).select('div.x-grid3-hd-'+this.id).addClass('x-grid3-check-col');
					
					//Verificar que las columnas check esten checadas si todos su hijos lo estan.
					var store = this.grid.store;
					var colModel = this.grid.colModel
					var index = colModel.getColumnCount();
					var view = this.grid.getView();
					var i = 0;
					
					//Se recorren todas las columnas en busca de columnas tipo check
					while (i < (index - 1)) {
						if (Ext.get(view.getHeaderCell(i)).child('div.x-grid3-check-col')) {
							var dataIndex = colModel.getDataIndex(i);
							if (store.sum(dataIndex) === store.getCount() && store.getCount() > 0) {
								this.grid.checkAll(dataIndex, false);
							}
						}
						i++;
					}
				}, this);
			}
			
			
		}, this);	

	},
	
	onMouseDown : function(e, t){
		if(t.className && t.className.indexOf('x-grid3-cc-'+this.id) != -1){
			e.stopEvent();
			var view = this.grid.getView();
			var row = view.findRowIndex(t);
			var col = view.findCellIndex(t);
			var record = this.grid.store.getAt(row);
			record.set(this.dataIndex, !record.data[this.dataIndex]);
			var sm = this.grid.getSelectionModel();
			if (sm.selectRow) {
				sm.selectRow(row);
			}
			else{
				sm.select(row,col);
			}
		}
	},
	
	onHdMouseDown : function(e, t){
		if(t.className && t.className.indexOf('x-grid3-hd-'+this.id) != -1){
			e.stopEvent();
			var chkValue;
			if(Ext.fly(t).hasClass('x-grid3-check-col')){
				Ext.fly(t).replaceClass('x-grid3-check-col','x-grid3-check-col-on');			
				var chkValue = 1;
			}
			else{
				Ext.fly(t).replaceClass('x-grid3-check-col-on','x-grid3-check-col');
				var chkValue = 0;
			}
			this.grid.store.each(function(r){
				r.set(this.dataIndex,chkValue);
			},this);
		}
	},
	
	renderer : function(v, p, record){
		//if(!this.checkHeader){return "&#160;"};
		
		record.data.idDom = this.id;
		p.css += ' x-grid3-check-col-td';
		
		return '<div class="x-grid3-check-col'+(v?'-on':'')+' x-grid3-cc-'+this.id+'">&#160;</div>';
	}
};
