Ext.ns('miFacturaWeb.GridBuscador');
/*******************************************************************************************************
                     <-----                 GRID               ----->
********************************************************************************************************/
miFacturaWeb.GridBuscador = Ext.extend(Ext.grid.EditorGridPanel, {
    //xtypeEditor:''                //<---------------Panel de edicion
    //tituloNew:"Nuevo",          //<---------------Titulo a mostrar en el tab al crear un nuevo registro
    //campoAmostrar:"nombre",     //<---------------El contenido de este campo se mostrará en el titulo del tab cuando este en modo de edicion        
    mensajeEditar:'Seleccione el registro a editar, gracias',
   // frame: false,
   // border:false,
   // style:"border-top:none;",
    bodyStyle:'border-width:1px 0 0 0;',
    root: 'data',
    totalProperty: 'totalRows',
    messageProperty: "msg" ,
    textoMenu:'',
	bubbleEvents:['cargarTab'],
	stripeRows:true,
    buscar:function(){
        this.bottomToolbar.doRefresh();
    },
     getFiltro:function(){
        var filtro=this.topToolbar.filtro.getValue();
        return filtro;
    },
    initComponent: function() {    

        this.bbar= new Ext.PagingToolbar({
            pageSize: miFacturaWeb.parametros.reg_pag_par,
            store: this.store,
            displayInfo: true,
            displayMsg: 'Mostrando {0} - {1} de {2}',
            emptyMsg: "No hay registros para mostrar",
            doRefresh:function(){
                this.ownerCt.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
				
                var filtro=this.ownerCt.getFiltro();
				
                this.store.baseParams.filtro=filtro;                
                this.store.grid=this.ownerCt;
                this.store.on('load',function(){
                    this.grid.el.unmask();
                });
                this.store.on('exception',function(misc){
                    this.grid.el.unmask();
                });
                Ext.PagingToolbar.prototype.doRefresh.apply(this);
            }
        });
        
        if (this.listeners == undefined){  this.listeners = {};}
        Ext.applyIf(this.listeners,{
        	activate:function(){
	    		if(this.activado){
	    			return;
	    		}
	    		this.activado=true;
	            this.bottomToolbar.doRefresh();
	        },
            rowdblclick:function(){
                this.editar();
            }
        });
        this.sm= new Ext.grid.RowSelectionModel({
            singleSelect:true
        });
        
        miFacturaWeb.GridBuscador.superclass.initComponent.call(this);
    },
    getSelected:function(){
        var selModel=this.getSelectionModel();
        var selected=selModel.getSelected();
        if (selected==null){
            Ext.Msg.show({
                title:'Aviso',
                buttons: Ext.Msg.OK,
                msg:this.mensajeEditar
            });

            return false;
        }
        return selected;
    },
    agregar:function(){
        var idSelected=0;
        var tituloTab=this.tituloNew;        
        this.crearEditor(tituloTab,"add", idSelected);
    },
    borrar:function(id){
        var campoId=this.getStore().idProperty;
        var index=this.getStore().find(campoId,id);
        var rec=this.store.getAt(index);
        this.getStore().remove(rec);
    },

    editar:function(){

        var selected=this.getSelected();
        if (!selected){return;}        
        var idProperty=this.store.idProperty;
        var idSelected=selected.get(idProperty);
        var tituloTab=selected.get(this.campoAmostrar);
        this.crearEditor(tituloTab,"edit", idSelected);

    },
    //ESTA FUNCION REALIZA OPERACIONES QUE NO LE CORRESPONDEN AL GRID, Estas le corresponden al Tab Container
    crearEditor:function(tituloTab,estado,idSelected){
        var mask = new Ext.LoadMask(this.el, {
            msg:miFacturaWeb.mensajes.mensajeDeEspera
        });
        this.mask=mask;
        mask.show();
        
        var params={
            lanzador:this,
            xtypeEditor:this.xtypeEditor,
            iconMaster:this.iconMaster,
            tituloTab:tituloTab,
            estado:estado,
            idValue:idSelected,
            moduloText:this.moduloText,
            campoAmostrar:this.campoAmostrar

        };
        //miFacturaWeb.tabContainer.cargarTab(params);
        this.fireEvent('cargarTab',params);
        return;

        

    }
});

Ext.reg('GridBuscador', miFacturaWeb.GridBuscador);




