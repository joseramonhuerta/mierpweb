/*
 * File: main.js
 * Date: Wed Mar 09 2016 19:14:18 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be generated the first time you export.
 *
 * You should implement event handling and custom methods in this
 * class.
 */
Ext.ns('mew');
mew.mainLogin = Ext.extend(mainUi, {
    initComponent: function() {
        mew.mainLogin.superclass.initComponent.call(this);
		
		Ext.apply(this,comportamiento_login);	
		Ext.apply(this,comportamiento_corporativo);
		
		this.cardLogin.on("afterrender",function(){
			this.cardLogin.getLayout().setActiveItem(0);
		},this);
		
		this.txtUsername.on('afterrender',function(){
			this.txtUsername.focus( true );
		},this);
		
		this.configurarPantallaLogin();
		this.configurarPantallaCorporativo();			
			
    },
	
	configurarPantallaLogin:function(){
				
		this.btnIdentificar.on("click",function(){
				this.identificar();
		},this);
		
		this.btnSalir.on("click",function(){
				this.salir();
		},this);
		
		this.txtPass.on('keypress',function(textfield, e){
			if (e.getCharCode()==e.ENTER){
				this.identificar();
			}
		},this);
		
		this.txtUsername.on('keypress',function(textfield, e){
			if (e.getCharCode()==e.ENTER){
				this.txtPass.focus( true );
			}
		},this);
		
	},
	configurarPantallaCorporativo:function(){
		
		this.btnCambiar.on("click",function(){
				this.cambiarUsuario();
		},this);
		
		this.btnSalir2.on("click",function(){
				this.salir();
		},this);
		
		this.cmbCorporativos.addListener('beforequery',function(qe){
			delete qe.combo.lastQuery; 	//PARA QUE SIEMPRE REALICE LA CONSULTA AL SERVIDOR
		},this);
		
		this.cmbCorporativos.on('select',function(combo, record, index){			
			this.seleccionarCorporativo(record.data.id_corporativo);
		},this);

		this.cmbEmpresas.on('select',function(combo, record, index){			
			this.seleccionarEmpresa(record.data.id_empresa);
		},this);

		this.cmbSucursales.on('select',function(combo, record, index){			
			this.seleccionarSucursal(record.data.id_sucursal);
		},this);
		
		this.btnEntrar.on("click",function(){
				this.entrar();
		},this);
		
		this.cmbCorporativos.on("beforequery",function(queryEvent){
				 queryEvent.query=""; 
		},this);
		
		this.cmbEmpresas.on("beforequery",function(queryEvent){
				 queryEvent.query=""; 
		},this);
		
		this.cmbSucursales.on("beforequery",function(queryEvent){
				 queryEvent.query=""; 
		},this);
	}
});
Ext.reg('mainLogin',mew.mainLogin);
