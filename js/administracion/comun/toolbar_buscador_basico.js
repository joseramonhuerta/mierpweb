Ext.ns('miFacturaWeb.ToolbarBuscadorBasico');
miFacturaWeb.ToolbarBuscadorBasico=Ext.extend(Ext.Toolbar,{
    bubbleEvents:['pushAgregar','pushEditar','pushBuscar'],
    /*getFiltrarActivos:function(){
        return this.botonFiltroActivos.getFiltrarActivos();
    },*/
    
    initComponent:function(){
        this.style='border-top: none;';
        var rutaIcono="images/iconos/";
       
        this.botonEditar=new Ext.Button({
            text: 'Editar',
            width: 70,
            icon:rutaIcono+this.iconMaster+"_edit.png",            
            handler:function(){                
                this.ownerCt.fireEvent('pushEditar');
            }
        });        

        this.botonAgregar=new Ext.Button({
            text: 'Agregar',
            width: 70,
            icon:rutaIcono+this.iconMaster+"_add.png",
            handler:function(){                               
                this.ownerCt.fireEvent('pushAgregar');
            }
        });

         this.filtro=new Ext.form.TextField({
            grid:this,
            width:200,
            emptyText:"Escriba el texto a buscar",
            listeners: {
                specialkey: function(field, e){
                    if (e.getKey() == e.ENTER) {
                        this.ownerCt.fireEvent('pushBuscar');
                    }
                }
            }
        });
        this.style="border-top:none;border-bottom:none;";
        this.items=[
                 this.botonAgregar,this.botonEditar
                ,"->",this.filtro,' ',' ',' '
        ];
        miFacturaWeb.ToolbarBuscadorBasico.superclass.initComponent.call(this);
    }
})
Ext.reg('ToolbarBuscadorBasico', miFacturaWeb.ToolbarBuscadorBasico);


