Ext.ns('miErpWeb');
Ext.ns('mew');
Ext.onReady(function(){
    Ext.BLANK_IMAGE_URL = 'images/s.gif';
    Ext.QuickTips.init();

	miErpWeb.cronTask = new Ext.util.DelayedTask(function(){
		miErpWeb.tareaCronometrada();		
	});
	
	miErpWeb.actualizarParametros();	
});

miErpWeb.actualizarParametros=function(){
	if (this.creado==undefined){
				CrearLayout();
			}
      
    this.creado=true;
	
	miErpWeb.tareaCronometrada(); // manda ejecutar por primera vez esta tarea
	
};	

miErpWeb.tareaCronometrada = function(){
		Ext.Ajax.request({ // actualizar los certificados
		url: 'app.php/sistema/verificasesion',
		success: function(){
		}
	});
		
	miErpWeb.cronTask.delay(30000); 
};

CrearLayout=function(){
	MainContainer = new mew.Main({
        renderTo: Ext.getBody()
    }).show();
};	

