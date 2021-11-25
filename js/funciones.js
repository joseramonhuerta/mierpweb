
function opcGridFacturacion(opc){
	// asignar a la variable global, el valor del icono al que se le dio click
	mew.Fact.actioncolumn = opc;
}

function seleccionComboEmpresas(){
    // link para cambiar de empresa/sucursal en la parte superior (para IE ya que no funcionaba con el componente Ext)
    miErpWeb.comboEmps.store.load();
    miErpWeb.comboEmps.focus();
    miErpWeb.comboEmps.expand();
}

function onChat(){
	//var sURL = 'http://mail.upccorporate.com:9090/webchat/userinfo.jsp?chatID=UxwfQc2NaS&workgroup=upctechnologies@workgroup.upccorporate.com';
	if (mew.chatActivo==true){
		var sURL = 'http://mail.upccorporate.com:9090/webchat/userinfo.jsp?chatID=UxwfQc2NaS&workgroup=mi_factura@workgroup.mail.upccorporate.com';
		window.open(sURL, 'onChatTechnologies','toolbar=0,status=1,menubar=0,resizable=0,scrollbars=0,width=500,height=330');
	}	
}

function gridToJson(grid){	//<--Esta funcion deberia estar dentro del grid
    var store=grid.store;
    var numRecs=store.data.length;
    var datos=new Array();
	var i;
    for (i=0; i<numRecs; i++){
        datos[i]=store.data.items[i].data;
    }         
    return Ext.util.JSON.encode( datos );  //<------Datos como Json
}

function GeneraImpresionTicket(scope, config, callback){
	/*Ext.Ajax.request({
		url: String.format('{0}/{1}',config.url || 'Compartir.asmx',config.Method || 'GeneraTicket'),
		headers: { 'Content-Type': 'application/json;charset=utf-8' },
		jsonData: {
			ID_Registro: config.ID_Registro,
			Codigo: config.Codigo,
			tipoImpresion: config.TipoImpresion,
			extraParams: config.extraParams || {}
		},
		scope: scope,
		success: function (response, opts) {
			var resp = Ext.decode(response.responseText).d;
			
			if(resp.Success){
				if(callback){
					callback(resp);
				}else{
	                printImpresionTicket(this, resp);
				}
			}else{
				SuccessFalse(resp);
				if(this.maskForm && this.maskForm.hide)
					this.maskForm.hide();
				else if(this.quitaMascara)
					this.quitaMascara();
			}
		}
		*/
		 var imprimir = {
    nombreImpresora: "EPSON TM-T20",
    grafica: false,
    puerto: "",
    codigoCorte: "",
    codigoAperturaCajon: "",
    datos: [
        { coordenadas: [80,80], texto: "27,64", fuente: "Arial", codigo: true },
        { coordenadas: [80,80], texto: "27,97,1", fuente: "Arial", codigo: true },
        { coordenadas: [80,80], texto: "27,33,0", fuente: "Arial", codigo: true },
        { coordenadas: [80,80], texto: "January 14, 2002  15:00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "27,100,2", fuente: "Arial", codigo: true},

        { coordenadas: [80,80], texto: "27,97,0", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "27,33,1", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "TM-U210B               $20.00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "TM-U210D               $21.00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "PS-170                 $17.00", fuente: "Arial", codigo: false},
        { coordenadas: [80,80], texto: "27,33,17", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "TOTAL                  $58.00", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "27,33,0", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "------------------------------", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "PAID                   $60.00", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "CHANGE                 $ 2.00", fuente: "Arial", codigo: false },
        { coordenadas: [80,80], texto: "27,100,2", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "29,86,66,0", fuente: "Arial", codigo: true},
        { coordenadas: [80,80], texto: "27,112,0,60,120", fuente: "Arial", codigo: true}
    ]
};
Ext.encode(imprimir);
		 printImpresionTicket(this, imprimir);
	// });
}

function createLocalPrint(){ 
	$.getScript("localprint.js", function(){
		mew.localprint = new LocalPrint({ 
			id: 'customhandler', 
			version: '1.1.1',
			port: (8080*1),
			winPath: 'ImpresionLocal/LocalPrint.msi'//,
			//macPath: mew.HandlerMacPath,
			//debPath: mew.HandlerDebPath
		});
			
		mew.localprint.on('installed',function(){ 
			mew.localprint.getMac(); 
		}, this);
			
		mew.localprint.on('mac', function(response){
			Ext.apply(mew,{
				Terminal : response.hostname,
				DireccionFisica : response.mac
			});
				
		}, this);			
	});
}

function printImpresionTicket(scope, resp){
	 if(mew.localprint != undefined && Ext.isFunction(mew.localprint.directPrint)){			
		var datos = document.location.href;
		datos = datos.substring(0, datos.lastIndexOf("/"));
		
		var config = Ext.apply(resp.ConfigImpresora,{
			codigoCorte : "",
			codigoAperturaCajon : "",
			datos: String.format("{0}/tmp/{1}", datos, encodeURIComponent(resp.ConfigImpresora.datos)),
		});
		
		var cfg = config;//Ext.encode(config);
		//applet.dom.imprimir(cfg);
        mew.localprint.directPrint(cfg);		
		
		if(!Ext.isEmpty(resp.Extras)){			
			//this.showErroresTickets(resp.Extras);
			
			if(Ext.isArray(resp.Extras.Extras)){
				Ext.each(resp.Extras.Extras, function(item,index,allItems){
					
					
					var file = config.datos = String.format("{0}/tmp/{1}",datos,encodeURIComponent(Ext.isString(item) ? item : item.Nombre));
					
					/*if(Ext.isEmpty(item.Impresora)){
						ImprimirEnPantalla(file);
					}else{*/
						config = Ext.apply(config,item.Impresora);
						config.datos = file;
						cfg = config;//Ext.encode(config);
						//applet.dom.imprimir(cfg);
                        mew.localprint.directPrint(cfg);
					//}					
				},this);
			}
		}		
	}/*else{
		ImprimirEnPantalla(resp);
	}*/
}
