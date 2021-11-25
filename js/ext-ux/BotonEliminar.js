miFacturaWeb.BotonEliminar=Ext.extend(Ext.Button,{    
    text: 'Eliminar',
    bubbleEvents:['pushEliminar'],
    disabled :true,      
	textTitle:'Confirme',
	textMsg:'¿Deseas eliminar el registro seleccionado?',
    initComponent:function(){        
        this.eliminar=function(respuesta){
            if (respuesta!='yes'){
                return;
            }            
            this.fireEvent('pushEliminar');
        };
        this.handler=function(){            
            Ext.Msg.show({
                scope:this,
                title:this.textTitle,
                msg: this.textMsg,
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
