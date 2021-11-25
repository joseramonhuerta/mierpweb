miFacturaWeb.RFCTextField=Ext.extend (Ext.form.TextField,{         //LO EXTENDI PARA APLICARLE VALIDACIONES VARIABLES CON EL TIPO DE CLIENTE
    tipo        :'F',
   // maskRe:/[a-z0-9]/i,
    labelStyle:'font-weight:bold;',
    width:115,
    setTipo:function(tipo){
        this.tipo=tipo;        
        if (this.tipo=="G"){            
            this.setReadOnly(true);
            this.setValue('XAXX010101000');
        }else if (this.tipo=="E"){            
            this.setReadOnly(true);
            this.setValue('XEXX010101000');
        }else{
            this.setReadOnly(false);
        }
        this.validate();
    },
    initComponent:function(){
        this.autoCreate  = {
            tag: 'input',
            maxlength: '13'
        };
        this.getErrors=function(){
            var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
			var valor=this.getValue();
			

            switch(this.tipo){
                case 'M':
                    if (valor.length!=12){
                        errors.push('Debe medir 12 caracteres el RFC para personas morales, escribió '+this.getValue().length);
                    }else{
						var patt=/^[a-zA-Z0-9&]{3}(\d{6})([A-Za-z0-9]{3})+$/i;
						var result=patt.test(valor);
						if (!result){							
							var mensaje='Revise el formato del RFC para personas morales: <br/> <br/>'
							mensaje+='3 letras, 6 dígitos numéricos y 3 dígitos alfanuméricos.';	
							mensaje+='<br/>Ejemplo: <label style="font-weight:bold;">MOR010101HOM</label>';
							errors.push(mensaje);
						}
					}
                    break;
                case 'F':
                    if (valor.length!=13){
                        errors.push('Debe medir 13 caracteres el RFC para personas Fisicas, escribió '+this.getValue().length);
                    }else{
						var patt=/^[a-zA-Z0-9&]{4}(\d{6})([A-Za-z0-9]{3})+$/i;
						var result=patt.test(valor);
						if (!result){
							var mensaje='Revise el formato del RFC  para personas fisicas: <br/> <br/>'
							mensaje+='4 letras, 6 dígitos numéricos y 3 dígitos alfanuméricos.';	
							mensaje+='<br/>Ejemplo: <label style="font-weight:bold;">FISI010101HOM</label>';
							errors.push(mensaje);
						}
					}
                    break;
                case 'G':
                    
                    if (valor!='XAXX010101000'){
                        errors.push('Debe escribir XAXX010101000, escribió '+this.getValue());
                    }
                    break;
				case 'E':
                    
                    if (valor!='XEXX010101000'){
                        errors.push('Debe escribir XEXX010101000, escribió '+this.getValue());
                    }
                    break;
                default:
                   // Tipo desconocido;
            }
            return errors;
        };
    }
});
Ext.reg('RFCTextField',miFacturaWeb.RFCTextField);