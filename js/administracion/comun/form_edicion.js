Ext.ns('miFacturaWeb.FormEdicion');

miFacturaWeb.FormEdicion = Ext.extend(Ext.FormPanel, {
    frame: false,
    unstyled :true,
    cls:'x-panel-mc', 
    border:false,  
    autoDestroy :true,
    bodyStyle:'padding-left:8px;padding-top:10px;',   
	bubbleEvents:['eliminado'],
	valoresIniciales:new Array(),
    nuevo:function(){
      this.fireEvent('nuevo');
    },
    editar:function(id){
        var parametro={};           
        var campoId=this.reader.meta.idProperty;
        eval("parametro."+campoId+"= id"); //<---Agrego la propiedad dinamicamente        
        this.getForm().load({
            url:this.urlGet,
            method: 'POST',
            params: parametro
        });
    },
    eliminar:function(){
        
        var basicForm=this.getForm();
        if (this.idField==undefined){
			Ext.Msg.alert("Error","No encontr� el campo identificador idField");            
            return;
        }
        
        var ID=this.idField.getValue();     // <<---Seria mejor usar un fieldText llamado idField

        var parametro={};
        var campoId=this.reader.meta.idProperty;

        eval("parametro."+campoId+"= ID"); //<--Agrego la propiedad dinamicamente        
        Ext.Ajax.request({
            params:parametro,
            url: this.urlDel,
            scope:this,
            success: function(response){
                var responseData = Ext.util.JSON.decode(response.responseText);
                if (responseData.success==true){               
                    this.fireEvent('eliminado',responseData);
                }else{                    
                    this.fireEvent('noeliminado');
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
					Ext.Msg.alert("Error en los datos","Revise los campos marcados.");       					                
                    return;
                }
            },
            success: function (form, request) {                
                var responseData;
                try{
                     responseData = Ext.util.JSON.decode(request.response.responseText);
                }catch(err){
					Ext.Msg.alert("Error decodificando la respuesta del servidor","Intente de nuevo o consulte al administrador del sistema.");       					                    
                    return;
                }
				
                var valores=responseData.data;
				
                form.setValues(valores);
                var campoId=this.reader.meta.idProperty;
                
               this.idValue=responseData.data[campoId];			//para evitar abrir dos veces la misma informacion
               form.idValue=responseData.data[campoId];
               
            }
        });
    },  
    initComponent: function() {        
        this.initialConfig.url= this.url;
        this.initialConfig.urlGet= this.urlGet;      
        this.initialConfig.reader= this.reader;    
        this.initialConfig.iconMaster= this.iconMaster;
        this.initialConfig.moduloText= this.moduloText;
        this.initialConfig.campoAmostrar= this.campoAmostrar;       
        this.initialConfig.idValue= this.idValue;
        
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
            
            for(i=0;i<fields.length;i++){                
                if (fields[i]!=undefined ){
                    campo=fields[i];
                    if( campo['name']!=undefined){
                        name=campo['name'];
                        map=(campo['mapping']==undefined)?campo['name']:campo['mapping'];
                        valor=valores[map];
                        
                        if (Ext.isDefined(valores[map])){
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
            
            Ext.form.BasicForm.prototype.setValues.call(this,campos);
        };
        //======================================================================================================
        //              
        var bubbleEvents=['cargado','eliminado','beforeaction','actionfailed','actioncomplete','nuevo','noeliminado'];
        try{
            if (this.bubbleEvents!=undefined){
                this.bubbleEvents=this.bubbleEvents.concat(bubbleEvents);
            }
        }catch(e){
            this.bubbleEvents=bubbleEvents;
        }
        //======================================================================================================
        Ext.applyIf();
        if (this.listeners == undefined) {this.listeners = {};}
        
        Ext.applyIf(this.listeners,{
            beforeaction:function(form){},
            actionfailed:function(form,action){},
            actioncomplete:function(form,action){              
                if (action.type=="load" && action.result.success==true){                 
                    this.fireEvent('cargado');
                }                
                if (action.type=="submit" && action.result.success==true){
                    this.fireEvent('guardado');
                }
            }
        });
        
        miFacturaWeb.FormEdicion.superclass.initComponent.call(this);
    }
	
	
});

