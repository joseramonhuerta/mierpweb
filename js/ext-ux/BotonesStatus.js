miFacturaWeb.BotonesStatus={};
miFacturaWeb.BotonesStatus = Ext.extend(Ext.ButtonGroup, {
    /*  Pareja de toogle buttons  donde solo uno puede estar precionado a la vez
     *  */
    title: '',
    columns: 2,
    frame:false,
    bubbleEvents:['pushVerde','pushRojo'],
    presionarVerde:function(){
         this.botonRojo.toggle(false);
         this.botonVerde.toggle(true);        
    },
    presionarRojo:function(){
        this.botonRojo.toggle(true);
         this.botonVerde.toggle(false);
    },
	setRedText:function(redText){
		this.botonRojo.text=redText;
	},
	setGreenText:function(greenText){
		this.botonVerde.text=greenText;
	},
	setIcons:function(iconMaster){
        this.iconMaster=iconMaster;
        var rutaIcons="images/iconos/";		
        this.botonRojo.setIcon(rutaIcons+this.iconMaster+"_red.png");        
        this.botonVerde.setIcon(rutaIcons+this.iconMaster+"_green.png");                    
    },
    listeners:{
        click:function(b){
            if (b.pressed==true){
                b.toggle(false);
                if (b.itemId=="verde"){
                    this.fireEvent('pushVerde');
                 //   this.verComoActivo();
                }else{
                   // this.verComoInactivo();
                    this.fireEvent('pushRojo');
                }                                   
           }else{
                b.toggle(true);
          }
          return false;
        }
    },
    initComponent: function() {
        var rutaIcono="images/iconos/";               
        /*SE ESTABLECE EL TEXTO QUE APARECERA EN EL BOTON*/
        var verdeText=(!this.verdeText)?"Activo":this.verdeText;
        var rojoText=(!this.rojoText)?"Inactivo":this.rojoText;      
        if (this.iconMaster==undefined){
        	this.iconMaster='cog';
        }
        this.items = [{
            text:verdeText,
            itemId:'verde',
            ref:'botonVerde',
            enableToggle : true,
            pressed:true,
            icon:rutaIcono+this.iconMaster+"_green.png",
            bubbleEvents:['click']
        },
        {
            text: rojoText,
            itemId:'rojo',
            ref:'botonRojo',
            enableToggle : true,
            icon:rutaIcono+this.iconMaster+"_red.png",
            bubbleEvents:['click']
        }         
        ];
        miFacturaWeb.BotonesStatus.superclass.initComponent.call(this);
    }
});
Ext.reg('botonesStatus',miFacturaWeb.BotonesStatus);