comportamiento_corporativo={
	mostrarPantallaCorporativo:function( params ){
		this.cardLogin.getLayout().setActiveItem(1);
		
		this.frmLogin.getForm().reset();
		this.frmCorps.getForm().reset();
		this.frmCorps.getForm().clearInvalid();
	
		this.cmbCorporativos.store.loadData({data:params.corporativos});
		this.cmbCorporativos.reset();
		this.cmbEmpresas.reset();
		this.cmbSucursales.reset();
		
		var numCorps=this.cmbCorporativos.store.getCount();
			if ( numCorps > 0 ){ //<--Siempre deberia ser mayor que 0, de lo contrario no permite avanzar
				var firstCorp=this.cmbCorporativos.store.getAt(0);
				var idFirstCorp=firstCorp.data.id_corporativo;
				this.cmbCorporativos.setValue(idFirstCorp);
				this.cmbCorporativos.fireEvent('select',this.cmbCorporativos,firstCorp,0);
			}
              
           this.cmbCorporativos.focus(true);
	

		
	},
	cambiarUsuario:function(params){        
		var formPanel=this.frmLogin;
		formPanel.getForm().reset();
		this.cardLogin.getLayout().setActiveItem(0);
		this.txtUsername.focus();
    },
	seleccionarCorporativo:function(corpId){
		this.dspCorps.el.addClass('pensando');
        var formPanel=this;
        Ext.Ajax.request({
            url: 'app.php/login/seleccionarcorporativo',
            params:{IDCor:corpId},
			scope:this,
            success: function(response){
                formPanel.dspCorps.el.removeClass('pensando');
                // var comboEmps=Ext.ComponentMgr.get('comboEmpresas');
                this.cmbEmpresas.setDisabled(true);
                this.cmbEmpresas.reset();   
				formPanel.cmbCorporativos.focus();				
                 var responseData = Ext.util.JSON.decode(response.responseText);
                 if (responseData.success){
                     if (responseData.siguiente==5){
                          formPanel.btnEntrar.setDisabled(false);
                         return;
                     }
                     if (responseData.data!=undefined){
                        var empresas=responseData.data;
                        var storeEmps=this.cmbEmpresas.store;
                        storeEmps.loadData({data:empresas});                        
                        this.cmbEmpresas.setDisabled(false);

                        /*AHORA SELECCIONO EL PRIMER ELEMENTO DEL COMBO EMPRESAS*/
                        var numEmps=this.cmbEmpresas.store.getCount();                        
                        if (numEmps>0){ //<--Siempre deberia ser >0, de lo contrario no permite avanzar
                            var firstEmp=this.cmbEmpresas.store.getAt(0);                            
                            var idFirstEmp=firstEmp.data.id_empresa;
                            this.cmbEmpresas.setValue(idFirstEmp);
                            this.cmbEmpresas.fireEvent("select",this.cmbEmpresas,firstEmp,0);
							
                        }
                     }
                    
                 }                
            },
           failure: function (form, action) {
               this.dspCorps.el.removeClass('pensando');
               mew.loginPanel.el.unmask();
                if (action.failureType=="client"){
                    App.setAlert("Error", "Revise los campos marcados.");
                    return;
                }
            }           
        });
	
	},
	seleccionarEmpresa:function(empId){
		this.dspEmps.el.addClass('pensando');
        var formPanel=this;
        Ext.Ajax.request({
            url: 'app.php/login/seleccionarempresa',
            params:{IDEmp:empId},
			scope:this,
            success: function(response){
                formPanel.dspEmps.el.removeClass('pensando');
                // var comboEmps=Ext.ComponentMgr.get('comboEmpresas');
                this.cmbSucursales.setDisabled(true);
                this.cmbSucursales.reset();   
				formPanel.cmbSucursales.focus();				
                 var responseData = Ext.util.JSON.decode(response.responseText);
                 if (responseData.success){
                     if (responseData.siguiente==5){
                          formPanel.btnEntrar.setDisabled(false);
                         return;
                     }
                     if (responseData.data!=undefined){
                        var sucursales=responseData.data;
                        var storeSucs=this.cmbSucursales.store;
                        storeSucs.loadData({data:sucursales});                        
                        this.cmbSucursales.setDisabled(false);

                        /*AHORA SELECCIONO EL PRIMER ELEMENTO DEL COMBO EMPRESAS*/
                        var numSucs=this.cmbSucursales.store.getCount();                        
                        if (numSucs>0){ //<--Siempre deberia ser >0, de lo contrario no permite avanzar
                            var firstSuc=this.cmbSucursales.store.getAt(0);                            
                            var idFirstSuc=firstSuc.data.id_sucursal;
							this.cmbSucursales.setValue(idFirstSuc);
                            this.cmbSucursales.fireEvent("select",this.cmbSucursales,firstSuc,0);
							
                        }
                     }
                    
                 }                
            },
           failure: function (form, action) {
               this.dspEmps.el.removeClass('pensando');
               mew.loginPanel.el.unmask();
                if (action.failureType=="client"){
                    App.setAlert("Error", "Revise los campos marcados.");
                    return;
                }
            }           
        });
	
	},
	seleccionarSucursal:function(sucId){
	this.dspSucs.el.addClass('pensando');
        var formPanel=this;
        Ext.Ajax.request({
            url: 'app.php/login/seleccionarsucursal',
            params:{IDSuc:sucId},
			scope:this,
            success: function(response){
                 formPanel.dspSucs.el.removeClass('pensando');
                 var responseData = Ext.util.JSON.decode(response.responseText);
				 formPanel.cmbSucursales.focus(true);	
                 if (responseData.success){
                     if (responseData.siguiente==5){
                          formPanel.btnEntrar.setDisabled(false);
                     }                    
                 }                
            },
           failure: function (form, action) {
               this.dspdspSucsCorps.el.removeClass('pensando');
               mew.loginPanel.el.unmask();
                if (action.failureType=="client"){
                    App.setAlert("Error", "Revise los campos marcados.");
                    return;
                }
            }           
        });
	
	
	},
	entrar:function(params){
			mew.loginPanel.el.mask(miErpWeb.mensajes.mensajeDeEspera);
			var formPanel=this.frmCorps;
		
		   formPanel.getForm().submit({
            url:'app.php/login/enter',
			 scope:this,
            failure:function(){                				
                mew.loginPanel.el.unmask();
            },							
            success:function(form, action){
                 window.location='admin.php';
            }
        });
		
	}
}