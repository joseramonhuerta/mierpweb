
Ext.ns('miFacturaWeb.comun.Toolbar');
miFacturaWeb.comun.Toolbar=Ext.extend(Ext.Toolbar,{
    style:'border: none;border-left:none;',
    bubbleEvents:['pushAgregar','pushEliminar','pushGuardar'],
    editorListo:function(){
        this.botonEliminar.setDisabled(false);
    },

    eliminar:function(respuesta){
            if (respuesta!='yes'){
                return;
            }
            this.fireEvent('pushEliminar');
    },
    initComponent:function(){
        
        var rutaIcono="images/iconos/";
        this.botonEliminar=new Ext.Button({
            text: 'Eliminar',
            //type:'submit',
            icon:rutaIcono+this.iconMaster+"_delete.png",
            disabled :true,
            scope:this,
            handler:function(){
                    Ext.Msg.show({
                        scope:this,
                        title:'¿Eliminar registro?',
                        msg: '¿Deseas eliminar el registro seleccionado?',
                        buttons: Ext.Msg.YESNO,
                        fn: this.eliminar,
                        animEl: 'elId',
                        icon: Ext.MessageBox.QUESTION
                    });
            }
        });

        this.botonAgregar=new Ext.Button({
            text: 'Guardar',
            type:'submit',
            icon:rutaIcono+this.iconMaster+"_add.png",
            handler:function(){
                var toolbar=this.ownerCt;
                //toolbar.fireEvent('pushAgregar');
                toolbar.fireEvent('pushGuardar');
            }
        });

        this.items=[
                this.botonAgregar,
                this.botonEliminar              
            ];
       miFacturaWeb.comun.Toolbar.superclass.initComponent.call(this);
    }
});
Ext.reg('BasicToolbar', miFacturaWeb.comun.Toolbar);
