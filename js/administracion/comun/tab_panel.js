Ext.ns('miFacturaWeb.comun.TabPanel');
miFacturaWeb.comun.TabPanel=Ext.extend(Ext.TabPanel, {          
    enableTabScroll: true,
    activeTab:   0,
    frame:true,
    border:true,    
    //autoScroll:true,
    cargarTab:function(params){
        /*-------------------------------------------------------------*/
        if (params.xtypeEditor!=undefined){ //POR COMPATIBILIDAD
            params.xtype=params.xtypeEditor;
        }
        if (params.xtype==undefined){
		
            Ext.Msg.alert.alert('desconozco el Componente que debo crear');
            return;
        }
        /*-------------------BUSCAR EL TAB-----------------------------*/

        var tabItems = this.items;
        var existe = false;
         var tab;
        for (var i=0; i<tabItems.getCount(); i++){
            tab = tabItems.items[i];
            if (tab.xtype == params.xtype && tab.idValue == params.idValue){
                this.setActiveTab(tab);
                existe = true;
                break;
            }
        }
        /*-------------------------------------------------------------*/
        if (!existe){
            var accion=''
            if (params.bullet==undefined){
                accion=(params.idValue==0)? '_add':'_edit';
            }else{
                accion=params.bullet;
            }

            var iconCls= Ext.ux.TDGi.iconMgr.getIcon(params.iconMaster+''+accion);

            tab=this.add(Ext.applyIf(params,{
                title:'loading',
                iconCls:iconCls,
                closable: true
            }));
        }
        /*-------------------------------------------------------------*/
        tab.show();
        tab.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
       if (params.idValue==0){
            tab.nuevo();
        }else{
            tab.editar(params.idValue);
        }
        return false;
    },   
    initComponent:function(){
        miFacturaWeb.comun.TabPanel.superclass.initComponent.call(this);                     
    },
    listeners:{
         mostrarNuevo:function(config){

            var nuevo=this.cargarTab(config);
            if (nuevo){
                nuevo.nuevo();
            }
            return false;
        },
        mostrarEditor:function(config){
            var editor=this.cargarTab(config);
            if (editor){
                editor.editar(config.idValue);
            }
            return false;
        }
    }
});

Ext.reg('comunTabPanel', miFacturaWeb.comun.TabPanel);