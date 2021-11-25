/*
 * File: gridAgentes.js
 * Date: Thu Feb 02 2017 22:06:15 GMT-0700 (Hora estándar Montañas (México))
 * 
 * This file was generated by Ext Designer version 1.1.2.
 * http://www.sencha.com/products/designer/
 *
 * This file will be generated the first time you export.
 *
 * You should implement event handling and custom methods in this
 * class.
 */
Ext.ns('miErpWeb');
Ext.ns('mew');
gridAgentes = Ext.extend(gridAgentesUi, {
	configurarToobar:function(){
		if (this.iconMaster!=undefined){
			this.btnAgregar.setIcon('images/iconos/'+this.iconMaster+'_add.png');
			this.btnEditar.setIcon('images/iconos/'+this.iconMaster+'_edit.png');
			this.btnEliminar.setIcon('images/iconos/'+this.iconMaster+'_delete.png');
			
		}
	},
	inicializarStores:function(){
			this.store=new miErpWeb.storeGridAgentes();
			this.bottomToolbar.bindStore(this.store);
			  
			this.cmbStatus.store = new miErpWeb.storeGridAgentesStatus();        
			var data=new Array(
					{id:'A',nombre:miErpWeb.formatearTexto('ACTIVOS')},
					{id:'I',nombre:miErpWeb.formatearTexto('INACTIVOS')},
					{id:'T',nombre:miErpWeb.formatearTexto('TODOS')}
			);
			 this.cmbStatus.store.loadData({data:data});
			 this.cmbStatus.setValue('A');
			 
			 this.bottomToolbar.pageSize=miErpWeb.parametros.registros_pagina;
	},
	inicializarEvents:function(){
			this.store.on('beforeload',function(){
				this.el.mask(mew.mensajeDeEspera);  
				this.store.baseParams=this.store.baseParams || {};
				this.store.baseParams.filtro=this.txtFiltro.getValue();
				this.store.baseParams.filtroStatus=this.cmbStatus.getValue();
			},this);
							
			this.store.on('load',function(){
				this.el.unmask();
			},this);
			
			this.btnAgregar.on('click',function(){
				this.nuevo();
			},this);
			
			this.btnEditar.on('click',function(){
				this.editar();
			},this);
			
			this.btnEliminar.on('click',function(){
				this.eliminar();
			},this);
			
			
			
			this.txtFiltro.on('specialkey',function(comp,e){		
				if (e.getCharCode()==e.ENTER){
					this.bottomToolbar.doRefresh();
				}
			},this);
			
			this.cmbStatus.on('select',function(combo, record, index){	
					this.bottomToolbar.doRefresh();			
			},this);
	},
    initComponent: function() {
		this.columnaStatus="status";
        gridAgentes.superclass.initComponent.call(this);
		this.configurarToobar();
		this.inicializarStores();
		this.inicializarEvents();
	},
	nuevo:function(){
    	var params={            
           xtype:'formAgentes',           
           idValue:0,
           title:'Nuevo Agente',
		   closable: true,
           iconMaster:this.iconMaster ,           
           // icon:"images/iconos/"+this.iconMaster+"_add.png"
       };
    			
       var tab=MainContainer.cargarTab(params);
    },
	editar:function(id){
		if (id==undefined){	//obtener el id del registro seleccionado
    		var  sel=this.getSelectionModel().getSelections();
    		if (sel.length==undefined || sel.length==0){
    			return;
    		}else{
    			id=sel[0].id;    			
    		}
    	}
		
    	var params={            
           xtype:'formAgentes',           
           idValue:id,           
           title:'Editar Agente',
           iconMaster:this.iconMaster,
			closable: true,		   
           icon:"images/iconos/"+this.iconMaster+"_edit.png"
       };
    			
       var tab=MainContainer.cargarTab(params);
       
       tab.on('eliminado',function(id){
    	   var rec=this.store.getById(id);
    	   if (rec==undefined){
    		   return;
    	   }
    	   this.store.remove(rec);
       },this);
    },
	eliminar:function(btn,id){
		switch(btn){	//ESTE SWITCH ES USADO PARA ANALIZAR LO QUE TRATA DE HACER EL USUARIO, LA PRIERA VEZ DEBE ENTRAR A default:
    	case 'no':
    		return;
    	break;
    	case 'yes':
    		this.eliminar('borrar');
    		return;
    		break;
    	case 'borrar':
    		break;		//SALE DEL SWITCH Y SIGUE EJECUTANDOSE LA FUNCI�N
    	case undefined:	//AQUI ENTRA LA PRIMERA VEZ
    	case false:    		
    	default:
    		var me=this;    		
    		Ext.Msg.show({
 			   title:'Confirme por favor',
 			   msg: "¿Desea borrar el Agente?",
 			   buttons: Ext.Msg.YESNO,
 			   fn: function(btn){	    				
    				me.eliminar(btn);
    			},
 			   scope:this,
 			   icon: Ext.MessageBox.QUESTION
 			});
    		return;
		} 
		
		if (id==undefined){	//obtener el id del registro seleccionado
    		var  sel=this.getSelectionModel().getSelections();
    		if (sel.length==undefined || sel.length==0){
    			return;
    		}else{
    			id=sel[0].id;    			
    		}
    	}
		
		this.el.mask(mfw.mensajeDeEspera);
		Ext.Ajax.request({
			params: { id_agente: id },
			scope:this,
		   	url: 'app.php/agentes/eliminar',
		   	success: function(response,options){	
				var respuesta=Ext.decode(response.responseText);
				if (respuesta.success==false){
					this.el.unmask();
					return;
				}
				
				this.bottomToolbar.doRefresh();			
				
		   	},
		   	failure: function(){
		   		this.el.unmask();
		   	}		   
		});
	},
	listeners:{
    	activate:function(){
    		if (this.activado==true){
    			return;
    		}
    		this.activado=true;
    		this.bottomToolbar.doRefresh();
    	},
    	rowdblclick : function( grid ,rowIndex, e ){   
    		var rec=this.store.getAt(rowIndex);
    		this.editar(rec.id);
    	},
    	rowclick : function( grid ,rowIndex, e ){
    		var  sel=this.getSelectionModel().getSelections();
    		if (sel.length==undefined || sel.length==0){
    			this.btnEditar.setDisabled(true);
    		}else{
    			this.btnEditar.setDisabled(false);
    		}
			if (sel.length==undefined || sel.length==0){
				this.btnEliminar.setDisabled(true);
			}else{
				this.btnEliminar.setDisabled(false);
			}
    		
    	}
	}
});
Ext.reg('gridAgentes', gridAgentes);