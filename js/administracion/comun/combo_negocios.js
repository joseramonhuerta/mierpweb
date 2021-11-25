Ext.ns('mfw.cmbNegocios');

mfw.store_negocios= Ext.extend(Ext.data.JsonStore,{
    constructor: function(cfg) {
        cfg = cfg || {};
        mfw.store_negocios.superclass.constructor.call(this, Ext.apply({
            url:'app.php/negocios/find',
            autoDestroy: true,
            idProperty: 'IDConcat',
            fields: ['IDConcat',
            {name:'Nombre',type:'string'},
            {name:'ComEmp',type:'string'},
            ,'Origen','IDSucursal','IDEmpresa',{name:'CFDiEmp',type:'int'}],
            baseParams: {inicial: miFacturaWeb.Empresa.IDEmp},
            autoLoad: true,
            root: 'data'
        }, cfg));
    }    
});
Ext.reg('store_negocios', mfw.store_negocios);

mfw.combo_empresa =  Ext.extend( Ext.form.ComboBox,{
    bubbleEvents:['negocioSeleccionado'],
    xtype:  'combo',
    width:  300,
    hideLabel: true,
    editable:  false,
    mode:   'local',
    triggerAction: 'all',
   autoSelect:true,
    valueField: 'IDConcat',
    displayField: 'Nombre',
    itemSelector: 'div.search-item2',
    initComponent:function(){
        this.store=new mfw.store_negocios();
        this.tpl= new Ext.XTemplate('<tpl for=".">'+
            '<div class="search-item2" style="padding:3px;border:1px dotted white;">'+
            '<div class="name {Origen}" style="font-weight:bold">{[fm.ellipsis( values.Nombre, 40,true) ]}</div>'+
            '<div class="desc {Origen}" style="font-style: italic; font-size:9px ">{[fm.ellipsis( values.ComEmp, 30,true) ]}</div>'+
            '<div class="{[(values.CFDiEmp)?'+"'cfdi':'cfd' ]}"+'"></div>'+
            '</div>'+
            '</tpl>');        
        mfw.combo_empresa.superclass.initComponent.call(this);
        this.store.on('load',this.seleccionarDefault,this);
    },
    seleccionarDefault:function(){        
        var negocios={
            empresa:miFacturaWeb.Empresa,
            sucursal:miFacturaWeb.Sucursal
        };

        this.store.removeListener('load',this.seleccionarDefault);
        if ( miFacturaWeb.Sucursal.IDSuc!=0){
            if (this.store.find('IDSucursal', miFacturaWeb.Sucursal.IDSuc) != -1) {
                this.setValue('SUC-'+miFacturaWeb.Sucursal.IDSuc);
                this.fireEvent('negocioSeleccionado',negocios);
            } else {
             //   alert('Sucursal No encontrado');
            }
        }else{
            if (this.store.find('IDEmpresa', miFacturaWeb.Empresa.IDEmp) != -1) {
                this.setValue('EMP-'+miFacturaWeb.Empresa.IDEmp);
                this.fireEvent('negocioSeleccionado',negocios);
            } else {
              //  alert('Empresa No encontrado');
            }
        }
    },
    listeners: {
        'select': function(combo, rec, index){
            var empresa={
                IDEmp:rec.data.IDEmpresa,
                CFDiEmp:rec.data.CFDiEmp
            };
            var sucursal={
                IDSuc:rec.data.IDSucursal
            };
            var negocio={
                empresa:empresa,
                sucursal:sucursal
            };
            this.fireEvent('negocioSeleccionado',negocio);
        },render:function(){
            
        }
    }
});


Ext.reg('combo_negocios',mfw.combo_empresa);