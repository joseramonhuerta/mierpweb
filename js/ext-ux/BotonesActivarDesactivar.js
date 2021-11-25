//CAMBIAR NOMBRE A BOTONES STATUS
miFacturaWeb.BotonesActivarDesactivar = Ext.extend(Ext.ButtonGroup, {
    title: '',
    columns: 2,
    frame:false,
    verComoActivo:function(){
         this.botonVerActivos.toggle(true);
         this.botonVerTodos.toggle(false);
    },
    verComoInactivo:function(){
        this.botonVerActivos.toggle(false);
         this.botonVerTodos.toggle(true);
    },
    initComponent: function() {
        var rutaIcono="images/iconos/";
        
        this.botonVerActivos=new Ext.Button({
            text: 'Activar',
            enableToggle : true,
            pressed:true,
            icon:rutaIcono+this.iconMaster+"_green.png",            
            bubbleEvents:['pushActivar'],
            handler:function(b,e){
                if (b.pressed==true){                    
                    this.fireEvent('pushActivar');
                     b.toggle(false); 
                }else{
                     b.toggle(true);
                }                
            }
        });

        this.botonVerTodos=new Ext.Button({
            text: 'Desactivar',
            enableToggle : true,
            
            icon:rutaIcono+this.iconMaster+"_red.png",            
            bubbleEvents:['pushDesactivar'],
            handler:function(b,e){
                if (b.pressed==true){                    
                    this.fireEvent('pushDesactivar');
                    b.toggle(false);  
                }else{
                     b.toggle(true);
                }                
            }            
        });
        this.items = [
            this.botonVerTodos,
            this.botonVerActivos
        ];
        miFacturaWeb.BotonesActivarDesactivar.superclass.initComponent.call(this);
    }
});