CatTest = {};
CatTest.catalogoTest ={};
CatTest.formMascotas={};
/*******************************************************************************************************
// ----- Grid de Productos -----
********************************************************************************************************/
CatTest.catalogoTest = Ext.extend(Ext.grid.EditorGridPanel, {    
    sm: new Ext.grid.RowSelectionModel({
        singleSelect:true
    }),
    title: 'Catalogo de Mascotas.',
    height: 480,
    frame: false,
    border:false,
    columns: [
    {
        header: 'Clave',
        align: 'center',
        dataIndex: 'IDMascotaMas',
        sortable: true,
        width: 70
    },

    {
        header: 'Nombre',
        align: 'left',
        dataIndex: 'nombreMas',
        sortable: true,
        width: 280
    },

    {
        header: 'Tipo',
        align: 'center',
        dataIndex: 'tipoMas',
        width:50
    },

    {
        header: 'Dueño',
        align: 'left',
        dataIndex: 'ownerMas',
        width:80
    }
    ],
    store: new Ext.data.JsonStore({
        autoDestroy: true,
        idProperty: 'IDMascotaMas',
        root: 'data',
        totalProperty: 'totalRows',
        messageProperty: "msg" ,
        fields: [{
            name: 'IDMascotaMas',
            type:"int"
        }, 'nombreMas', 'tipoMas','ownerMas'],
        proxy:new Ext.data.HttpProxy({
            api: {
                read    : 'app.php/mascotas/getmascotas',
                create  : 'app.php/mascotas/nueva',
                update  : 'app.php/mascotas/actualizar',
                destroy : 'app.php/mascotas/eliminar'
            }
        }),
        writer : new Ext.data.JsonWriter({
            writeAllFields  : true,
            encodeDelete:true
        })
    }),
 

    onRender: function(ct, position) {
        this.store.load();
        CatTest.catalogoTest.superclass.onRender.call(this,ct, position);
    },
    agregar:function(){

        var tab_panel = Ext.getCmp('tabContainer');
                tab_panel.add({
                    title:  'Agregar Mascota',
                    items: [{xtype: 'formMascotas'}],
                    closable: true
                }).show();
    },
    editar:function(){        
        var selModel=this.getSelectionModel();
        var selected=selModel.getSelected();
        if (selected==null){
            Ext.Msg.alert("Debe seleccionar un registro");
            return;
        }
        var IDMascotaMas=selected.data.IDMascotaMas;
        var tab_panel = Ext.getCmp('tabContainer');
        tab=tab_panel.add({
            title:  'Editar Mascota',
            items: [{xtype: 'formMascotas'}],
            closable: true
        });
        tab.show();
        form=tab.items.items[0];
        form.editar(form,IDMascotaMas);
    },
    eliminar:function(){        
        var selModel=this.getSelectionModel();
        selected=selModel.getSelected();
        if (selected==null){
            Ext.Msg.alert("Debe seleccionar un registro");
            return;
        }
        var store=this.getStore();
        store.remove(selected);
        store.save();
    },
    refrescar:function(){
        this.store.load();
    },
    initComponent: function() {
        this.tbar= [
        {
            text:  'Agregar',
            width: 70,            
            iconCls: 'icon-save',
            handler:this.agregar
        },
        {
            text:"Editar",
            width: 70,
            iconCls: 'icon-edit',
            scope:this,
            handler:this.editar
        }/*,
        {
            text:"Refrescar",
            iconCls:'icon-refresh',
            scope:this,
            handler:this.refrescar
        }*/,
        {
            text:"Eliminar",
            iconCls: 'icon-delete',
            scope:this,
            handler:this.eliminar
        }],
    this.bbar= new Ext.PagingToolbar({
            pageSize: 5,
            store: this.store,
            displayInfo: true,
            displayMsg: 'Displaying topics {0} - {1} of {2}',
            emptyMsg: "No topics to display",
            items:[
                '-', {
                pressed: true,
                enableToggle:true,
                text: 'Show Preview',
                cls: 'x-btn-text-icon details',
                toggleHandler: function(btn, pressed){
                    var view = grid.getView();
                    view.showPreview = pressed;
                    view.refresh();
                }
            }]
        });
        CatTest.catalogoTest.superclass.initComponent.call(this);
    }
});

Ext.reg('catMascotas', CatTest.catalogoTest);
/**********************************************************************************************************************************************
 *                          ESTE ES EL FORMULARIO DE EDICION
 ***********************************************************************************************************************************************/
CatTest.formMascotas = Ext.extend(Ext.FormPanel, {
    reader: new Ext.data.JsonReader({
        idProperty: 'IDMascotaMas',
        root: 'data',
        fields: [
        {name: 'IDMascotaMas'},
        {name: 'nombreMas'},
        {name: 'tipoMas'},
        {name: 'ownerMas'},
        ]
    }),
    title: 'Agregar/Editar Productos',    
    height: 480,
    frame: true,
    editar:function(form,id){
        form.getForm();
        form.load({
            url : 'app.php/mascotas/getmascota',
            method: 'POST',
            params: {
                IDMascotaMas: id
            },
            waitMsg : 'Espere por favor'
        });
    },
    hacerSubmit:function(){
        var form=this.getForm();
        form.submit({
            url : 'app.php/mascotas/guardar',
            waitMsg : 'Salvando datos...',
            failure: function (form, action) {
                Ext.MessageBox.show({
                    title: 'Error al salvar los datos',
                    msg: 'Error al salvar los datos.',
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.ERROR
                });
            },
            success: function (form, request) {
                Ext.MessageBox.show({
                    title: 'Datos salvados correctamente',
                    msg: 'Datos salvados correctamente',
                    buttons: Ext.MessageBox.OK,
                    icon: Ext.MessageBox.INFO
                });
                responseData = Ext.util.JSON.decode(request.response.responseText);
                form.load({
                    url : 'app.php/mascotas/getmascota',
                    method: 'POST',
                    params: {
                        IDMascotaMas: responseData.data.IDMascotaMas
                    },
                    waitMsg : 'Espere por favor'
                });
            }
        });
    },
    initComponent: function() {
        this.items= [{
            xtype: 'textfield',
            fieldLabel: 'Clave',
            readOnly:   true,
            name:   'IDMascotaMas'
        },{
            xtype: 'textfield',
            fieldLabel: 'Nombre',
            name:  'nombreMas'
        },{
            xtype: 'textfield',
            fieldLabel: 'Tipo',
            name:  'tipoMas'
        },{
            xtype: 'textfield',
            fieldLabel: 'Dueño',
            name:  'ownerMas'
        }];
        this.buttons=[{
            text: 'Guardar',
            type:'submit',
            iconCls:'icon-save',
            scope: this,
            handler:this.hacerSubmit
        }];    
        CatTest.formMascotas.superclass.initComponent.call(this);    
    }
});

Ext.reg('formMascotas', CatTest.formMascotas);