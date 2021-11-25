ekoGrid={
	initEkoGrid:function(){
		
		this.store.on('beforeload',function(){
			this.el.mask(mew.mensajeDeEspera);
		},this);
		
		this.store.on('load',function(){
			if (this.el!=undefined){
				this.el.unmask();
			}			
		},this);
		
		this.store.on('loadexception',function(){
			if (this.el!=undefined){
				this.el.unmask();
			}			
		},this);
		
		
		this.configurarView();
	},
	configurarView:function(){
		if (this.columnaStatus==undefined){
			return;
		}
		this.cancelValue=this.cancelValue || 'I';
		this.view = new Ext.grid.GridView({
			scope: this,
			getRowClass: function(row, index){				
				return (row.data[this.grid.columnaStatus] == this.grid.cancelValue) ? 'columna-inactiva' :'';
			},
			onRowOver : function(e, target) {
				var row = this.findRowIndex(target);
		        var rec=this.grid.store.getAt(row);				
				var clase='';				
				if (rec==undefined)return;
				
				if (rec.data[this.grid.columnaStatus]==this.grid.cancelValue){
					clase='columna-inactiva-over';
				}else{
					clase=this.rowOverCls;
				}
		        if (row !== false) {
		            this.addRowClass(row, clase);
		        }
		    },
		    onRowOut : function(e, target) {
		        var row = this.findRowIndex(target);		        

		        if (row !== false && !e.within(this.getRow(row), true)) {
		        	this.removeRowClass(row, 'columna-inactiva-over');
		            this.removeRowClass(row, this.rowOverCls);
		        }
		    },
		    onRowSelect : function(row) {
				var rec=this.grid.store.getAt(row );
		        var clase='';
				if (rec.data[this.grid.columnaStatus]==this.grid.cancelValue){
					clase='columna-inactiva-sel';					
				}else{					
					clase=this.selectedRowClass;
				}				
		        this.addRowClass(row, clase);
		    },
		    onRowDeselect : function(row) {
		    	this.removeRowClass(row, 'columna-inactiva-sel');	            
		        this.removeRowClass(row, this.selectedRowClass);
		    }
		}); 
	}
};
ekoGrid.onRender = function(ct, position){
    Ext.grid.GridPanel.superclass.onRender.apply(this, arguments);

    var c = this.getGridEl();

    this.el.addClass('x-grid-panel');

    this.mon(c, {
        scope: this,
        mousedown: this.onMouseDown,
        click: this.onClick,
        dblclick: this.onDblClick,
        contextmenu: this.onContextMenu
    });

    this.relayEvents(c, ['mousedown','mouseup','mouseover','mouseout','keypress', 'keydown']);
    
    this.initEkoGrid();//<--------------------------
    
    
    var view = this.getView();
    view.init(this);
    view.render();
    this.getSelectionModel().init(this);

		
};