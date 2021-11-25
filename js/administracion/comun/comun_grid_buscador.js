/*
 * Zesar Oktavio
 * Sabado 9 de abril del 2011
 *
 */

Ext.ns('miFacturaWeb.comun.gridBuscador');
miFacturaWeb.comun.gridBuscador = Ext.extend(Ext.grid.EditorGridPanel, {
   // border:false,
   // frame:false,
    root: 'data',    
    totalProperty: 'totalRows',
    messageProperty: "msg" ,
    bodyStyle:'border-width:1px 0 0 0;',
    stripeRows:true,
    mensajeEditar:'Seleccione un registro para editarlo, gracias',
    buscar:function(){
        this.bottomToolbar.doRefresh();
        return false;
    },
     getFiltro:function(){
        var filtro=this.topToolbar.filtro.getValue();
        return filtro;
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
        var params={};        
        params.xtype=this.xtypeEditor;        
        params.descripcion=this.descripcion;      
        params.idValue=idSelected;
        params.iconMaster=this.iconMaster;
        params.moduloText=this.moduloText;  //<---------------OLD
        params.buscador=this;
        params.bullet='add';       
        this.fireEvent('mostrarNuevo',params);
        return false;
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
      //   var tituloTab=selected.get(this.campoAmostrar);
         var params={};
         params.xtype=this.xtypeEditor;        
         params.iconMaster=this.iconMaster;
         params.idValue=idSelected;         
         params.bullet='edit';
         params.buscador=this;
         this.fireEvent('mostrarEditor',params);  //<----fireEvent('editar',params)
         return false;
     },
     
    initComponent: function() {
        var bubbleEvents=['pushAgregar','mostrarEditor','mostrarNuevo'];
        if (this.sm==undefined){
            this.sm= new Ext.grid.RowSelectionModel({
                singleSelect:true
            });
        }
        
        if (this.tbar==undefined){
            this.tbar={
                xtype:'ToolbarBuscadorBasico',
                iconMaster:this.iconMaster                               
            };
        }
        
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

        try{
            if (this.bubbleEvents!=undefined){
                this.bubbleEvents=this.bubbleEvents.concat(bubbleEvents);
            }
        }catch(e){
            this.bubbleEvents=bubbleEvents;
        }
        
        if (this.listeners == undefined){this.listeners = {};}
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

         /*              COMPORTAMIENTO              */
        this.on('pushAgregar',this.agregar);

        this.on('pushEditar',this.editar);

        this.on('pushBuscar',function(){
         //  alert("Buscar en grid Comun");
        },this);
        
        miFacturaWeb.comun.gridBuscador.superclass.initComponent.call(this);
    }
    
});

Ext.reg('comunGridBuscador', miFacturaWeb.comun.gridBuscador);