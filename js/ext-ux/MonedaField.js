Ext.ns('miFacturaWeb.MonedaField');

miFacturaWeb.MonedaField = Ext.extend(Ext.form.TextField, {
    allowBlank: false,
    minimo: 0,
    maximo: 90000000,
    bubbleEvents:['change','specialkey','blur'],
	style:'text-align: right;',
	value:0,
    getErrors: function(){
        var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
        var msg='';
        
        if (this.getValue() < this.minimo || this.getValue() > this.maximo){		
            msg = "El valor debe ser entre "+ this.minimo +" y "+ Ext.util.Format.monedaConSeparadorDeMiles(this.maximo);			
            errors.push(msg);
        }

        if (this.allowBlank == false && (this.getValue() == '')){
			
            msg = "Debe establecer un valor para este campo";
            errors.push(msg);
        }

        if (isNaN(this.getValue())){		
            msg = "Numero incorrecto";
            errors.push(msg);
        }
        
        return errors;
    },
    setMaximo:function(maximo){
         this.maximo=maximo;
        var maxlength = this.maximo.toString().length;        
        if (this.el!=undefined){
            this.el.dom.maxlength=18;
        }else{
            this.autoCreate.maxlength=18;
        }
        
    },
    initComponent: function(){
       
        var maximo = this.maximo.toString().length;
		
		if (this.autoCreate==undefined){
			this.autoCreate={
				tag:        'input',
				maxlength:  18 
			}
		};
                
       // this.emptyText  = this.emptyText || miFacturaWeb.formatearMoneda(0),
        miFacturaWeb.MonedaField.superclass.initComponent.call(this);
    }
});

Ext.reg("monedafield", miFacturaWeb.MonedaField);