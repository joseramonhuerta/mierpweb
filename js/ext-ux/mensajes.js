Ext.ns('miErpWeb.mensajes');
Ext.ns('mew');
miErpWeb.mensajes.mensajeDeEspera="Procesando la petici&oacute;n, por favor espere unos segundos.";
mew.mensajeDeEspera=miErpWeb.mensajes.mensajeDeEspera;

miErpWeb.mensajes.requestError=function(btn,form){
	try{	//EL PRIMER ERROR FUE GENERADO CUANDO EL TAB DEL FORMULARIO FUE CERRADO AUTOMATICAMENTE, AL EXPORTAR UNA ORDEN YA FACTURADA
		var formEl=Ext.get(form.id);
		if (formEl==undefined){
			return;
		}
		var parent=formEl.parent();
		parent=parent.parent();
		
		var cmp=Ext.getCmp(parent.id);
		if (cmp==undefined){
			Ext.Msg.alert("requestError","No se encontro el formulario para notificar el evento de error");
			return;
		}
		cmp.fireEvent('requestError');	
	}catch(e){
	
	}
}

Ext.onReady(function(){
 /*************************************************************************************************************************************
 *
 *              Listeners al resultado de peticiones al servidor, para mostrar los mensajes de notificacion al usuario
 *
 *************************************************************************************************************************************/
    // Listen to all DataProxy beforewrite events
    //

    /*Ext.data.DataProxy.addListener('beforewrite', function(proxy, action) {
      //  App.setAlert(App.STATUS_NOTICE, "Before " + action);
    });

    ////
    // all write events
    //
    Ext.data.DataProxy.addListener('write', function(proxy, action, result, res, rs) {

     //   App.setAlert(true, action + ':' + res.message);
    });*/
    ////
    // all exception events
    //
    Ext.data.DataProxy.addListener('exception', function(proxy, type, action, options, res) {        
        if (res.status==403){
            Ext.Msg.show({
				title:'La sesi&oacute;n ha caducado',
				msg: 'Imposible continuar con el proceso.<br/>Presione aceptar para iniciar sesi&oacute;n.',
				buttons: Ext.Msg.OK,
				minWidth :600,
				fn: function(btn, text){
					window.location = "index.php";
				},
				animEl: 'elId',
				icon: Ext.MessageBox.WARNING
			});
			//Ext.Msg.alert("Error","La sessi&oacute;n ha expirado");
        }
    });
	
	Ext.Ajax.addEvents('failure');
	Ext.Ajax.addEvents('requestError');
	
    Ext.Ajax.addListener('requestcomplete',function(conn, response, options ){
        try{        
        	if (options.noJSON!=undefined)return;  
            var responseData =Ext.util.JSON.decode(response.responseText);                        
            if (responseData.msg!=undefined){
                if (responseData.success){					
					var msg=responseData.msg;
					if (Ext.isObject(msg)){						
						App.setAlert(msg.titulo,msg.mensaje);
					}else{						
						App.setAlert('success',responseData.msg);
					}                    
                }else{
					var msg=responseData.msg;
					var titulo,mensaje;
					var icon=Ext.MessageBox.WARNING;
					if (Ext.isObject(msg)){		
						titulo=msg.titulo;
						mensaje=msg.mensaje;	
						if (msg.icon!=undefined){
							switch(msg.icon){
								case 'WARNING':
									//icon=Ext.MessageBox.WARNING;
								break;
								case 'ERROR':
									icon=Ext.MessageBox.ERROR;
								break;
								case 'INFO':
									icon=Ext.MessageBox.INFO;
								break;
								case 'QUESTION':
									icon=Ext.MessageBox.QUESTION;
								break;
							}
						}
					}else{					
						titulo='Error';
						mensaje=responseData.msg;						
					}
					
					if (!mensaje){
						return;			//Sin mensaje NO hay alerta
					}
					
					Ext.Msg.show({
						title: titulo,
						msg:   mensaje,
						minWidth :600,
						fn: function(btn){							
							if(options.scope==undefined){return;}							
							if(options.scope.form==undefined){return;}							
							miErpWeb.mensajes.requestError(btn,options.scope.form);
						},
						icon:  icon,
						buttons: Ext.Msg.OK
					});					
                }

            }
        }catch($err){

            Ext.Msg.show({
                title:$err,
                buttons: Ext.Msg.OK,
				minWidth :600,
                msg: 'La respuesta del servidor es incomprensible<p>Notifique al Administrador del sistema</p></br>'+$err,
                icon: Ext.MessageBox.ERROR
            });
            if (options.form!=undefined){
                var form=options.scope.form;
                form.fireEvent('failure',{asd:'asd'});
            }
            
        }
    });

    Ext.Ajax.addListener('requestexception',function(conn, response, options ){

        if (response.status==403){
			Ext.Msg.show({
			   title:'Error: La sessi&oacute;n ha caducado',
			   msg: 'Imposible continuar con el proceso.<br/>Presione aceptar para iniciar sessi&oacute;n.',
			   buttons: Ext.Msg.OK,
			   minWidth :600,
			   fn: function(btn, text){
				window.location = "index.php";
				if (btn == 'yes'){
					window.location = "index.php";
				}
			   },
			   animEl: 'elId',
			   icon: Ext.MessageBox.INFO
			});
            //App.setAlert(false, "No tiene permisos para realizar esta accion");
        }else{			
			Ext.Msg.show({
			   title:'Error en la comunicaci&oacute;n',
			   minWidth :600,
			   msg: 'Se perdi&oacute; la conexi&oacute;n con el servidor.',
			   buttons: Ext.Msg.OK,
			   icon: Ext.MessageBox.WARNING
			});			
					
        }
    });
});


