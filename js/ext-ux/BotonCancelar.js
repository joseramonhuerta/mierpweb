miFacturaWeb.BotonEliminar=Ext.extend(Ext.Button,{    
    text: 'Cancelar',
    bubbleEvents:['pushCancelar'],
    disabled :true,        
    initComponent:function(){        
        this.eliminar=function(respuesta){
            if (respuesta!='yes'){
                return;
            }            
            this.fireEvent('pushCancelar');
        };
        this.handler=function(){            
            Ext.Msg.show({
                scope:this,
                title:'Cancelar?',
                msg: '¿Deseas eliminar el registro seleccionado?',
                buttons: Ext.Msg.YESNO,
                fn: this.eliminar,
                animEl: 'elId',
                icon: Ext.MessageBox.QUESTION
            });
        };
        miFacturaWeb.BotonEliminar.superclass.initComponent.call(this);
    }
});
Ext.reg('botonEliminar',miFacturaWeb.BotonEliminar);
