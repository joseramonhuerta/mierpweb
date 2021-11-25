Ext.ns('miErpWeb');

// Ext.util.Format.separarMiles=function(cantidad){
	// var separado=cantidad.split('').reverse().join('').replace(/(?=\d*\.?)(\d{3})/g,'$1,').split('').reverse().join('').replace(/^[\,]/,'');
	// return separador;
// }

Ext.util.Format.cantidadConSeparadorDeMiles=function(cantidad){
	var cant=miErpWeb.formatearCantidad(cantidad);	
	return Ext.util.Format.separarMiles(cant);
};

Ext.util.Format.monedaConSeparadorDeMiles=function(cantidad){
	var cant=miErpWeb.formatearMoneda(cantidad);	
	cant=Ext.util.Format.separarMiles(cant);	
	return cant;
};

Ext.util.Format.separarMiles=function(cantidad){
	cantidad=cantidad.toString();
	var separadas=cantidad.split('.');
	var enteros=separadas[0];
	var decimales=separadas[1];
	// enteros=enteros.split('').reverse().join('').replace(/(?=\d*)(\d{3})/,'$1,').split('').reverse().join('').replace(/^[\,]/,'');
	enteros = Ext.util.Format.number(enteros, "000,000");
	if (decimales){
		// --- decimales=decimales.split('').reverse().join('').replace(/(?=\d*)(\d{3})/,'$1,').split('').reverse().join('').replace(/^[\,]/,'');		
		cantidad=enteros+'.'+decimales;
	}else{
		cantidad=enteros;
	}
	return cantidad;
};

String.prototype.capitalize=function(){
    /*Ademas de capitalizar, te pone en mayuscula las letras despues de los puntos*/
    var valor = this.toLowerCase();
    valor= valor.replace(/(^|\s)([a-z])/g, function (m, p1, p2) {
        return p1 + p2.toUpperCase();
    });


    if (valor.length==0){
        return valor;
    }
    var RegExPattern=/[\.][a-z]/;
    var iEncontrado=0;
    var strIzq='';
    var strDer='';
    longitud=valor.length;
    do{
        iEncontrado=valor.search(RegExPattern);
        if (iEncontrado==-1){
            break;
        }
        iEncontrado+=1;
        strIzq=valor.substring(0, iEncontrado);
        strDer=valor.substring(iEncontrado,longitud);
        valor= strIzq+strDer.capitalize();
    }while(iEncontrado>0);
    return valor;
};

miErpWeb.timestampToJsDate=function(timestamp){

    var fechaHoraArr=timestamp.split(' ');
    
    var fechaArr=fechaHoraArr[0].split('-');
    var horaArr=fechaHoraArr[1].split(':');

    var fecha=new Date(fechaArr[0],fechaArr[1]-1,fechaArr[2],horaArr[0],horaArr[1],horaArr[2]);
    return fecha;
};

function isInt(x) {
   var y=parseInt(x);
   if (isNaN(y)){
        return false;
   }
   return x==y && x.toString()==y.toString();
 }
Ext.onReady(function(){
/**/

 
Ext.override(Ext.form.Field, {
   setFieldLabel : function(text) {
      Ext.fly(this.el.dom.parentNode.previousSibling).update(text);
   }
})

Ext.apply(Ext.form.VTypes, {
    phoneRe : /^\d{10}$/,
    phone: function(val, field) {
        return this.phoneRe.test(val);
    },
    phoneText: 'Debe escribir los 10 digitos: 3 para lada, 7 del numero telefonico".',
    phoneMask: /[\d]/i
});

Ext.apply(Ext.form.VTypes, {

    monedaRe : /^\d*.{1}\d*$/,
    moneda: function(val, field) {
        return this.monedaRe.test(val);
    },
    
    monedaText: 'Debe escribir numeros con decimales'  
});

Ext.data.Types.MAYUSCULAS = {
    convert: function(v, data) {
        return v.toUpperCase();
    },
    sortType: function(v) {        
        return v;  
    },
    type: 'mayusculas'
};


Ext.data.Types.STRING = {
    convert: function(v, data) {
        return miErpWeb.formatearTexto(v);
    },
    sortType: function(v) {
        return v;
    },
    type: 'string',

};

Ext.data.Types.MINUSCULAS = {
    convert: function(v, data) {
        if (v==undefined){
            return '';
        }		
        return v.toLowerCase();
    },
    sortType: function(v) {
        return v;
    },
    type: 'minusculas'
};

Ext.data.Types.MONEDA = {
    convert: function(v, data) {
        if (v==undefined){
            return '';
        }
        return miErpWeb.formatearMoneda(v);
    },
    sortType: function(v) {
        return v;
    },
    type: 'moneda'
};

Ext.data.Types.CANTIDAD = {
    convert: function(v, data) {
        if (v==undefined){
            return '';
        }
        return miErpWeb.formatearCantidad(v);
    },
    sortType: function(v) {
        return v;
    },
    type: 'cantidad'
};

Ext.data.Types.FECHAHORA = {
    convert: function(v, data) {
        return miErpWeb.timestampToJsDate(v);
    },
    sortType: function(v) {
        return v;
    },
    type: 'fechahora'
};


Ext.data.Types.CAPITALIZE = {
    convert: function(v, data) {
        return v.capitalize();
    },
    sortType: function(v) {
        return v;
    },
    type: 'capitalize'
};

Ext.apply(Ext.form.VTypes, {

   mayusculasRe : /^\d*.{1}\d*$/,
    mayusculas: function(val, field) {
        return this.monedaRe.test(val);
    },

    mayusculasText: ''
});

/*************************************************************************************************************************************
*                                                  OVERRRIDE AL READER PARA DARLE FORMATO A LA INFO LEIDA
*************************************************************************************************************************************/

    Ext.override(Ext.data.JsonReader,{
        extractValues : function(data, items, len) {
            
            var f, values = {};
            var j;
            for(j = 0; j < len; j++){
                f = items[j];
                var v = this.ef[j](data);                
                v=miErpWeb.formatear(v,f);
                values[f.name] = f.convert((v !== undefined) ? v : f.defaultValue, data);
            }
            return values;
        }
    });

    miErpWeb.formatear=function(valor,field){
        if (valor!=null & field.formatear!=false){
            var tipo=field.type;                        
           if (tipo==undefined){               
               return valor;
           }

           if (tipo=="minusculas"  || tipo.type=='minusculas'){               
                return  valor.toLowerCase();
            }
           if (tipo=="mayusculas"  || tipo.type=='mayusculas'){                
               return  valor.toUpperCase();
           }
            /*
            if (tipo=="mayusculas"  || tipo.type=='mayusculas'){                
                return  valor.toUpperCase();
            }
            
            if (tipo=="capitalize"  || tipo.type=='capitalize'){                
                return valor.capitalize();
            }*/
            if (tipo=="moneda"  || tipo.type=='moneda'){                
                return  miErpWeb.formatearMoneda(valor);
            }
            if (tipo=="cantidad" || tipo.type=='cantidad'){
                return  miErpWeb.formatearCantidad(valor);
            }
            if ( tipo=='string' || tipo.type=='string'){
                return  miErpWeb.formatearTexto(valor);
            }
            if (tipo=='tel' || tipo.type=='tel'){
                return  miErpWeb.formatearTelefono(valor);
            }
        }
        return valor;
    };

    miErpWeb.formatearCantidad=function(valor){
     
     if (miErpWeb.parametros.dec_can_par!=undefined){
         var decimales=miErpWeb.parametros.dec_can_par;
         var cantidad = parseFloat(valor);
        return cantidad.toFixed(decimales);
     }else{
        return valor;
     }
        
    };
    miErpWeb.formatearMoneda=function(valor){
     //   alert('formatearMoneda'+valor);
     if (miErpWeb.parametros.decimales_cantidad!=undefined){
        var decimales=miErpWeb.parametros.decimales_cantidad;
		var cantidad = parseFloat(valor);
        return cantidad.toFixed(decimales);
     }else{
         return valor;
     }
        
    };
    miErpWeb.formatearTexto=function(valor){
     // alert('formatearTexto'+valor);alert('formatearTexto'+valor);
	  if (valor==null){
		return valor;
	  }
	  valor=valor.toString();
        switch(miErpWeb.UserConfig.forUsu){
            case "1":
				try {
					valor=valor.toUpperCase();
				} catch(e){
					
				}
                break;
            case "2":
                valor=valor.toLowerCase();
                break;
            case "3":
                valor=valor.capitalize();
                break;
            default:
        }
        return valor;
    };
	
	 miErpWeb.formatearCorreo=function(valor){
     // alert('formatearTexto'+valor);alert('formatearTexto'+valor);
	  if (valor==null){
		return valor;
	  }
	   valor=valor.toString();
      
       valor=valor.toLowerCase();
             
        return valor;
    };

    miErpWeb.formatearTelefono=function(num_sf){
            
            if (num_sf==''){
              return num_sf;
            }

            var num_cf='';
            num_cf="("+num_sf.substring(0,3)+") ";
            num_cf+=num_sf.substring(3,6)+"-";
            num_cf+=num_sf.substring(6,10);
            return num_cf;
    };


 /************************************************************************************************************************************
 *                      OVERRIDE AL TREELOADER PARA DARLE FORMATO A LA INFO LEIDA
 *************************************************************************************************************************************/

    Ext.override(Ext.tree.TreeLoader,{
        createNode : function(attr){
            // var text=attr.text;

            // if (text!=null & attr.formatear!=false &  (this.formatear==undefined | this.formatear==true)){

                // switch(miErpWeb.UserConfig.forUsu){
                    // case "1":
                        // text=text.toUpperCase();
                        // break;
                    // case "2":
                        // text=text.toLowerCase();
                        // break;
                    // case "3":
                        // text=text.capitalize();
                        // break;
                    // default:

                // }
                // attr.text=text;

            // }
            // apply baseAttrs, nice idea Corey!
            if(this.baseAttrs){
                Ext.applyIf(attr, this.baseAttrs);
            }
            if(this.applyLoader !== false && !attr.loader){
                attr.loader = this;
            }
            if(Ext.isString(attr.uiProvider)){
                attr.uiProvider = this.uiProviders[attr.uiProvider] || eval(attr.uiProvider);
            }
            if(attr.nodeType){
                return new Ext.tree.TreePanel.nodeTypes[attr.nodeType](attr);
            }else{
                return attr.leaf ?
                new Ext.tree.TreeNode(attr) :
                new Ext.tree.AsyncTreeNode(attr);
            }
        }

    });

});