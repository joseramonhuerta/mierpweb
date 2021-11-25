//  comboEmps
miFacturaWeb.cmbSelectorDeEmpresa=Ext.extend(Ext.form.ComboBox,{
    //cls	:'',
    defaultAutoCreate : {tag: "u"},
  //  borders:false,
   // width:170,
	listWidth: 300,
    hideTrigger:true,
    allowBlank:true,
	//cls:'sucursal',
	cls:'link-cambiar',
	style:'font-weight:bold;font-family: arial,tahoma,helvetica,sans-serif;',
    //labelStyle: 'padding-right:20px;',
    lazyInit:false,
    submitValue:true,
    autoScroll :true,
    forceSelection :false,
    minChars:0,
    hideLabel:true,
    editable:false,
	triggerAction :	'all',
    mode: 'remote',
    valueField: 'IDConcat',
    displayField: 'NombreSucursal',
    itemSelector: 'div.search-item2',
    initComponent:function(){
    	miFacturaWeb.cmbSelectorDeEmpresa.superclass.initComponent.call(this);
    	this.tpl= new Ext.XTemplate('<tpl for=".">',
    	        '<div class="search-item2" style="padding:3px;border:1px dotted white;">',
    				'<div style="float:left;width:225px">',
    					'<div class="name {Origen}" style="font-weight:bold">{[fm.ellipsis( values.Nombre, 40,true) ]}</div>',
    					'<div class="desc {Origen}" style="font-style: italic; font-size:9px ">{[fm.ellipsis( values.ComEmp, 40,true) ]}</div>',
    				'</div>',
    				'<div style="float:left;width:32px">',
    					'<tpl if="CFDiEmp==1"><img src="images/iconos/CFDI.Logo.Chico.png" /></tpl>',
    					'<tpl if="CFDiEmp==0"><img src="images/iconos/CFD.Logo.Chico.png" /></tpl>',
    				'</div>',
    				'<div style="clear:both"></div>',
    			'</div>',
    	    '</tpl>');
    	   this.store= new Ext.data.JsonStore({        
    	        autoDestroy: true,
    	        url:'app.php/sistema/findempsucs',        
    	        root: 'data',
    	        idProperty: 'IDConcat',
    	        fields: ['IDConcat','CFDiEmp',
    	            {name:'Nombre',type:'minusculas',convert:function(val){
    	                return val.capitalize();
    	            }},
    	            {name:'ComEmp',type:'capital',convert:function(val){
    	                return val.capitalize();
    	            }},
    	            'Origen',
    	            {name:'NombreSucursal',type:'capital',convert:function(val){
    	                return val.capitalize();
    	            }}
    	            ,'IDSuc','IDEmpresa','IDSucursal',
    	            {name:'RFCEmp',type:'capital'}]
    	    });
    },
    //El negocio seleccionado es enviado al servidor para que se actualize la session
    actualizarEnServidor:function(empresaId,sucursalId){        
        Ext.Ajax.request({
            params:{empresaId:empresaId,sucursalId:sucursalId},
            url: 'app.php/sistema/seleccionarempresa',
            scope:this,
            success: function(response){
                var responseData = Ext.util.JSON.decode(response.responseText);
                miFacturaWeb.Empresa=responseData.data.Empresa;
                miFacturaWeb.Sucursal=responseData.data.Sucursal;
				if (responseData.data.CertInfo){
					miFacturaWeb.infoCertificado(responseData.data.CertInfo);
				}
            }
        });
    },
    listeners:{    	
        select:function(combo, record, index ){
    	
            miFacturaWeb.Empresa.IDEmp=record.data.IDEmpresa;
            miFacturaWeb.Empresa.CFDiEmp=record.data.CFDiEmp;
            miFacturaWeb.Empresa.RFCEmp=record.data.RFCEmp;
           // miFacturaWeb.Empresa.ComEmp=record.data.Nombre;
            if (record.data.IDSucursal==0||record.data.IDSucursal==""){
                miFacturaWeb.Empresa.ComEmp=record.data.Nombre;
                this.el.dom.innerHTML="Matriz";
            }else{
            	this.el.dom.innerHTML=Ext.util.Format.ellipsis("Sucursal: "+record.data.NombreSucursal, 28,true);
                miFacturaWeb.Empresa.ComEmp=record.data.ComEmp;
            }
            miFacturaWeb.Sucursal.IDSuc=record.data.IDSucursal;
            miFacturaWeb.Sucursal.NomSuc=record.data.NombreSucursal;

			miFacturaWeb.tareaCronometrada();
            this.actualizarEnServidor(miFacturaWeb.Empresa.IDEmp,record.data.IDSucursal);
			var negocio={
				empresa:miFacturaWeb.Empresa,
				sucursal:miFacturaWeb.Sucursal
			};

			this.fireEvent('negocioSeleccionado',negocio);

        },
		beforequery: function(queryEv){
			queryEv.combo.expand();
			queryEv.combo.store.load();
			return false;
		},
        render: function(){ // render		
			this.el.dom.innerHTML="Seleccione empresa o sucursal";
			if (miFacturaWeb.Sucursal.IDSuc==0 && miFacturaWeb.Empresa.IDEmp!=0){
				this.el.dom.innerHTML="Matriz";
			}else if (miFacturaWeb.Sucursal.IDSuc!=0) {				
				this.el.dom.innerHTML=Ext.util.Format.ellipsis("Sucursal: "+miFacturaWeb.formatearTexto(miFacturaWeb.Sucursal.NomSuc), 28,true);
			}
			
        }
    }
});
Ext.reg('cmbSelectorDeEmpresa', miFacturaWeb.cmbSelectorDeEmpresa);

miFacturaWeb.cmbSelectorDeAlmacen=Ext.extend(Ext.form.ComboBox,{

    defaultAutoCreate : {tag: "u"},
    borders:false,
    width:170,
	cls:'link-cambiar',
	//style:'font-size:11px;',
	listWidth: 280,
    hideTrigger:true,
    allowBlank:true,
    lazyInit:false,
    submitValue:true,
    autoScroll :true,
    forceSelection :false,
    minChars:0,
    hideLabel:true,
    editable:false,
	triggerAction :	'all',
    mode: 'remote',
    valueField: 'IDAlmacen',
    displayField: 'NombreAlmacen',
    itemSelector: 'div.search-item2',
    //itemSelector: 'div.search-item2',
    tpl: new Ext.XTemplate('<tpl for=".">',
	        '<div class="search-item2" style="padding:3px;border:1px dotted white;">',
				'<div style="float:left;width:225px">',
					'<div class="name {Origen}" style="font-weight:bold;margin-bottom:3px;">{[fm.ellipsis( values.NombreAlmacen, 40,true) ]}</div>',					
				'</div>',
				
				'<div style="clear:both"></div>',
			'</div>',
	    '</tpl>'),
    initComponent:function(){
    	miFacturaWeb.cmbSelectorDeAlmacen.superclass.initComponent.call(this);

    	   this.store= new Ext.data.JsonStore({        
    	        autoDestroy: true,
    	        url:'app.php/sistema/findalmacen',        
    	        root: 'data',
    	        idProperty: 'IDAlmacen',
    	        fields: ['IDAlmacen',
    	            {name:'NombreAlmacen',convert:function(val){
    	                return val.capitalize();
    	            }},
    	            ,"negocio"    	            
    	       ]
    	    });
    },
    
    actualizarEnServidor:function(almacenId,nombre){        
        Ext.Ajax.request({
            params:{IDAlmacen:almacenId,nombre:nombre},
            url: 'app.php/sistema/seleccionaralmacen',
            scope:this,
            success: function(response){
                var responseData = Ext.util.JSON.decode(response.responseText);				
				if ( undefined==responseData.data ) return;
				miFacturaWeb.almacen=responseData.data;				
            }
        });
    },
    listeners:{    	
        select:function(combo, record, index ){
    		
    		this.el.dom.innerHTML=Ext.util.Format.ellipsis(this.el.dom.innerHTML=record.data.NombreAlmacen, 31,true);
    		this.actualizarEnServidor(record.data.IDAlmacen,record.data.NombreAlmacen);
			this.fireEvent('almacenSeleccionado',record.data);
			
			
        },
		beforequery: function(queryEv){
			queryEv.combo.expand();
			queryEv.combo.store.load();
			return false;
		},
        render: function(){ // render
			
			this.el.dom.innerHTML="Seleccione un almacén";
			if (miFacturaWeb.almacen!=undefined){
				var data={
						idAlmacen:miFacturaWeb.almacen.IDAlmacen,
						nomAlm:miFacturaWeb.almacen.nombre
				};				
				
				this.store.loadData({data:data});
				this.setValue(data.idAlmacen);
				
				var nomAlm=Ext.util.Format.ellipsis(data.nomAlm,31,true);				
				this.el.dom.innerHTML=nomAlm;
			}
			
			
		

        }
    }
});
Ext.reg('cmbSelectorDeAlmacen', miFacturaWeb.cmbSelectorDeAlmacen);


//;