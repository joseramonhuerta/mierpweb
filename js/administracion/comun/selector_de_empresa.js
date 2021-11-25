//--------------------------------------------------------------------------
// var tpl='<tpl for="."><div class="datos">'+
// '<div class="rfc">{RFCEmp}</div>'+
// '<div class="empresa">{[fm.ellipsis(values.ComEmp, 30,true)]}</div>'+'<div class="trigger x-tool"></div>'+
// '<div class="sucursal">{[fm.ellipsis(values.NomSuc, 30,true)]}</div>'+
// '</div>'+
// '<div style:"clear:both"></div></tpl>';

var tpl = '<tpl for=".">'+
             '<div class="datos">'+
			    '<div><span class="empresa">{[fm.ellipsis(values.ComEmp, 38,true)]}</span>&nbsp;&nbsp;<span class="rfc">({RFCEmp})</span></div>'+
				'<div>'+
				   '<div style="float:left;width:220px">'+
				      '<div class="sucursal">Sucursal {[fm.ellipsis(values.NomSuc, 28,true)]}</div>'+
					  '<div class="link-cambiar" style="padding-top:2px" onclick="seleccionComboEmpresas()"><u>Cambiar de Empresa/Sucursal</u></div>'+
				   '</div>'+
				   '<div style="float:left;width:260px">'+
				      '<table>'+
					     '<tr>'+
						    '<td width="92px">Certificado usado:</td>'+
							'<td width="130px" style="text-align:right"><span class="num-certif"></span></td>'+
							'<td width="12px"><img src="images/iconos/sem_verde_A.gif" class="status-certificado" /></td>'+
						 '</tr>'+
						 '<tr style="height:16px">'+
						    '<td class="link-cambiar link-folios"><u>Folios vigentes:</u></td>'+
							'<td  style="text-align:right" class="info-folios">'+
							   // En esta linea se escriben numero de serie, folio activo y folio final
							   '<span class="num-serie"></span><span class="folio-activo"></span><span class="folio-final"></span>'+
							'</td>'+
							'<td><img src="images/iconos/sem_verde_A.gif" class="status-folios" /></td>'+
						 '</tr>'+
					  '</table>'+
				   '</div>'+
				   '<div style="clear:both"></div>'+
				'</div>'+
			 '</div>'+
          '</tpl>';


miFacturaWeb.selectorDeEmpresa=new Ext.Panel({
    id: 'selectorDeEmpresa',
    width: 500,
	
    cls:'x-tree-node',
    unstyled:true,
    tpl : new Ext.XTemplate(tpl)
});



miFacturaWeb.comboEmps=new Ext.form.ComboBox({
    cls	:'noCombo',
    defaultAutoCreate : {tag: "span"},
    triggerClass:'x',
    fieldClass:'x',
	
    style:'position:fixed;top:56px;left:211px;height:12px;',
    id:"comboSelectorDeEmpresa",
    borders:false,
    width:170,
	listWidth: 280,
    hideTrigger:true,
    allowBlank:true,
    labelStyle: 'padding-right:20px;',
    lazyInit:false,
    submitValue:true,
    autoScroll :true,
    forceSelection :false,
    minChars:0,
    editable:false,
	triggerAction :	'all',
    mode: 'remote',
    valueField: 'IDConcat',
    displayField: 'NombreSucursal',
    itemSelector: 'div.search-item2',
    tpl: new Ext.XTemplate('<tpl for=".">',
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
    '</tpl>'),
    store: new Ext.data.JsonStore({        
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
    }),
    actualizarEnServidor:function(empresaId,sucursalId){        
        Ext.Ajax.request({
            params:{empresaId:empresaId,sucursalId:sucursalId},
            url: 'app.php/sistema/seleccionarempresa',
            scope:this,
            success: function(response){
                var responseData = Ext.util.JSON.decode(response.responseText);
                miFacturaWeb.Empresa=responseData.data.Empresa;
                miFacturaWeb.Sucursal=responseData.data.Sucursal;
            }
        });
    },
    listeners:{
        select:function(combo, record, index ){
            miFacturaWeb.Empresa.IDEmp=record.data.IDEmpresa;
            miFacturaWeb.Empresa.ComEmp=record.data.Nombre;
            if (record.data.IDSucursal==0||record.data.IDSucursal==""){
                miFacturaWeb.Empresa.ComEmp=record.data.Nombre;
            }else{
                miFacturaWeb.Empresa.ComEmp=record.data.ComEmp;
            }
            miFacturaWeb.Sucursal.IDSuc=record.data.IDSucursal;
            miFacturaWeb.Sucursal.NomSuc=record.data.NombreSucursal;


            var nombreEmpresa=Ext.util.Format.ellipsis(miFacturaWeb.Empresa.ComEmp.capitalize(),35,true);
            var nombreSucursal=(miFacturaWeb.Sucursal.NomSuc=='')?'Matriz':miFacturaWeb.Sucursal.NomSuc.capitalize();
            nombreSucursal=Ext.util.Format.ellipsis(nombreSucursal,35,true);
            
            var empresa={
                RFCEmp:record.data.RFCEmp.toUpperCase(),
                ComEmp:nombreEmpresa,
                NomSuc:nombreSucursal
            };
            miFacturaWeb.selectorDeEmpresa.tpl.overwrite('selectorDeEmpresa',empresa);
			miFacturaWeb.tareaCronometrada();
            this.actualizarEnServidor(miFacturaWeb.Empresa.IDEmp,record.data.IDSucursal);
			var negocio={
				empresa:miFacturaWeb.Empresa,
				sucursal:miFacturaWeb.Sucursal
			};
			miFacturaWeb.comboEmps.fireEvent('negocioSeleccionado',negocio);
			
			// abrir el catalogo de folios
			miFacturaWeb.selectorDeEmpresa.el.select(".link-folios").on('click',function(){
				var params = {
					xtype: 'foliosBuscador',
					idValue: 0,
					title: 'Folios',
					iconCls: Ext.ux.TDGi.iconMgr.getIcon('folios'),
					iconMaster: 'folios'
				};
				miFacturaWeb.tabContainer.cargarTab(params);
			});
			
			
        },
		beforequery: function(queryEv){
			queryEv.combo.expand();
			queryEv.combo.store.load();
			return false;
		},
        render: function(){ // render
            // this.el.on('mousedown',function(){
                // miFacturaWeb.selectorDeEmpresa.el.addClass('selector-over');
                // this.el.addClass('selector-over');
            // },this);
            // this.el.on('mouseout',function(){
                // miFacturaWeb.selectorDeEmpresa.el.removeClass('selector-over');
            // },this);
        }
    },
    initComponent:function(){
        Ext.form.ComboBox.prototype.initComponent.call(this);
		STORE=this.store;
    }
});
