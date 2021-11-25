Ext.ns('miFacturaWeb.FormEdicion');

miFacturaWeb.comun.FormEdicion = Ext.extend(Ext.FormPanel, {
    autoScroll:true,      
    autoDestroy :true,
    bodyStyle:'padding-left:8px;padding-top:8px;',  
    tituloParaNuevo:"Registro nuevo",
    descripcion:'FormularioGenerico',
    frame: false,
    unstyled :true,
     border:false,
    xtype:'generico',
    valoresIniciales:new Array(),
    cambiarTitulo:function(){
        //CAMBIAR EL TITULO AL TAB
        var form=this.getForm();
        var valores=form.getValues();
        var campoAmostrar=this.campoAmostrar;

        var titulo=valores[campoAmostrar];
        this.setTitle(Ext.util.Format.ellipsis(titulo,25,true), Ext.ux.TDGi.iconMgr.getIcon(this.iconMaster+"_edit"));        
        this.removeClass(this.xtype+"-"+0);

        if (this.moduloText!=undefined){    //POR COMPATIBILIDAD            
            this.removeClass(this.moduloText+"-form-"+0);
        }
        
        this.addClass(this.xtype+"-"+form.idValue);
        if (this.topToolbar!=undefined){
            if (this.topToolbar.botonAgregar!=undefined){
                this.topToolbar.botonAgregar.setIcon("images/iconos/"+this.iconMaster+"_edit.png");
            }
        }     
    },
    getValoresIniciales:function(){        
        var fields=this.getForm().reader.meta.fields;
        var campo;
        var campos=new Array();
        var name;
        var valores=this.getForm().getValues();
        
        for(i=0;i<fields.length;i++){

            if (fields[i]!=undefined ){
                campo=fields[i];
                if( campo['name']!=undefined){
                    name=campo['name'];
                    map=campo['name'];
        
                    valor=valores[map];
                    valor=miFacturaWeb.formatear(valor,campo);
                    campos[i]={
                        id:name,
                        value:valor
                    };
                }
            }
        }

        return campos;
    },
    nuevo:function(){

        this.valoresIniciales=this.getValoresIniciales();
        this.setTitle(this.tituloParaNuevo,Ext.ux.TDGi.iconMgr.getIcon(this.iconMaster+"_add"));
        this.fireEvent('nuevo');
        this.el.unmask();
    },
    editar:function(id){
        var parametro={};
        if (this.reader==undefined){
            parametro.idValue=id;
        }else{
            var campoId=this.reader.meta.idProperty;
            eval("parametro."+campoId+"= id"); //<---Agrego la propiedad dinamicamente
        }
        this.getForm().load({
            url:this.urlGet,
            method: 'POST',
            params: parametro
        });
    },

    eliminar:function(){

        var basicForm=this.getForm();
        if (this.idField==undefined){
			Ext.Msg.alert("Error grave","No encontré el campo identificador idField");       					            
            return;
        }

        var ID=this.idField.getValue();
        
        var parametro={};
        if (this.reader==undefined){
            parametro.idValue=ID;
        }else{
            var campoId=this.reader.meta.idProperty;
            eval("parametro."+campoId+"= ID"); //<---Agrego la propiedad dinamicamente
        }
        Ext.Ajax.request({
            params:parametro,
            url: this.urlDel,
            scope:this,
            success: function(response){
                var responseData = Ext.util.JSON.decode(response.responseText);
                if (responseData.success==true){
                    if (this.buscador!=undefined){
                        var params={};
                        params.buscador=this.buscador;
                        params.idValue=ID;
                        this.fireEvent('regEliminado',params);
                    }else{
                        this.fireEvent('eliminado',responseData);
                    }
                     
                    
                    this.fireEvent('cerrar',this);
                    

                    
                    
                }
            }
        });
    },



    hacerSubmit:function(){
        var form=this.getForm();
        form.submit({
            scope:this,
            failure: function (form, action) {
                if (action.failureType=="client"){
					Ext.Msg.alert("Error en los datos","Revise los campos marcados'");                                 
                    this.fireEvent('failure');
                    return;
                }
                
            },
            success: function (form, request) {
                var responseData;
                try{
                    responseData = Ext.util.JSON.decode(request.response.responseText);
                }catch(err){
					Ext.Msg.alert("Error decodificando la respuesta","Intente de nuevo o consulte al administrador del sistema");       					
                    return;
                }

                var valores=responseData.data;
  
                form.setValues(valores);
                var campoId=this.reader.meta.idProperty;

                this.idValue=responseData.data[campoId];
                form.idValue=responseData.data[campoId];

            }
        });
    },
   guardarValoresIniciales: function(valores){
        this.valoresIniciales=valores;
   },
   getInicial:function(id){       
       var iniciales=this.valoresIniciales;
       var i;
                        
       for (i=0;i<iniciales.length;i++){
           inicial=iniciales[i];
               if (inicial.id==id){
                   return inicial;
               }
      }
      return false;

   },
   revisarCambiosPendientes:function(){
        var valores=this.getForm().getValues();
        
        var pendientes=false;
        var inicial;
        var valor;
        for (i in valores){
            try{
            	inicial=this.getInicial(i);
            }catch(e){
                return false;
            }
            if (!inicial){                
                break;                                
            }

            valor=valores[i];

            if (valor!=inicial.value){   
                return true;
            }


        }
        return pendientes;



   },
   respuestaDeMsgCambios:function(respuesta){
        if (respuesta=='yes'){
            this.fireEvent('cerrar',this);
            //this.getForm().submit();
        }
   },
   revisarCambios:function(){
     var pendientes=this.revisarCambiosPendientes();
            if (pendientes){
                Ext.Msg.show({
                        scope:this,
                        title:'Cambios sin guardar',
                        msg: '¿Descartar cambios?. Presione No y luego guarde sus cambios',
                        buttons: Ext.Msg.YESNOCANCEL,
                        fn: this.respuestaDeMsgCambios,
                        animEl: 'elId',
                        icon: Ext.MessageBox.WARNING
                    });
                    return false;
            }
   },
    initComponent: function() {
        this.initialConfig.url= this.url;
        this.initialConfig.urlGet= this.urlGet;
        this.initialConfig.reader= this.reader;
        this.initialConfig.idValue= this.idValue;
        this.initialConfig.formPanel=this;

        this.initialConfig.setValues=function(valores){
            if (this.reader==undefined){
                Ext.form.BasicForm.prototype.setValues.call(this,valores);
                return;
            }            
            var fields=this.reader.meta.fields;
            var campo;
            var campos=new Array();
            var name;
            var indice=0;

            var campoId=this.reader.meta.idProperty;

            for(i=0;i<fields.length;i++){
                if (fields[i]!=undefined ){
                    campo=fields[i];
                    if( campo['name']!=undefined){
                        name=campo['name'];

                        
                        map=(campo['mapping']==undefined)?campo['name']:campo['mapping'];
                        if (Ext.isDefined(valores[map])){
                            
                            if (campo['mapping']==campoId){                                                                
                                this.idValue=valores[map];  //USADO PARA NO ABRIR DOS VECES EL MISMO TAB 
                            }                            
                            
                            valor=valores[map];
                            valor=miFacturaWeb.formatear(valor,campo);
                            campos[indice]={
                                id:name,
                                value:valor
                            };
                            indice++;
                        }                        
                    }
                }
            }            
            this.formPanel.guardarValoresIniciales(campos);
                                  
            Ext.form.BasicForm.prototype.setValues.call(this,campos);
        };
        //======================================================================================================
        //
        var bubbleEvents=['cargado','eliminado','beforeaction','actionfailed','actioncomplete','nuevo','cambioDeContenido','cerrar','regEliminado','failure'];
        try{
            if (this.bubbleEvents!=undefined){
                this.bubbleEvents=this.bubbleEvents.concat(bubbleEvents);                
            }
        }catch(e){
            this.bubbleEvents=bubbleEvents;
        }
        //======================================================================================================
        //Ext.applyIf();
        if (this.listeners == undefined) {
            this.listeners = {};
		}

    Ext.applyIf(this.listeners,{
        beforeaction:function(form){},
        actionfailed:function(form,action){            
            this.fireEvent('failure');
            //return false;
        },
        actioncomplete:function(form,action){
            var params={};
            if (action.type=="load" && action.result.success==true){
                this.fireEvent('cargado');
                this.cambiarTitulo();
            }else if (action.type=="submit" && action.result.success==true){
                this.fireEvent('guardado');
                this.cambiarTitulo();
            }
        }
    });

    this.addListener('beforeclose',this.revisarCambios);
    this.addListener('cambiarId',function(id){
        this.idValue=id;
    },this);
    miFacturaWeb.comun.FormEdicion.superclass.initComponent.call(this);
}
});

