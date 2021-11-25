Ext.ns('miFacturaWeb');
Ext.ns('miFacturaWeb.gridTasas');
Ext.ns('miFacturaWeb.formTasas');


/*************************************************
//         ----- Grid de Tasas -----         
**************************************************/
miFacturaWeb.gridTasas = Ext.extend(miFacturaWeb.CatalogoGrid, {
    xtypeForm:   'formTasas',    // form de edicion
    tituloNew:   "Nueva tasa",   // Titulo a mostrar en el tab al crear un nuevo registro
    campoAmostrar: "DescTasa", // El contenido de este campo se mostrará en el titulo del tab cuando este en modo de edicion
    campoId:     "IDTasa",  // PK del registro,
	campoActivo: "ActivoTasa",
	verSwitchActivos: true,
	stripeRows:true,
	autoExpandColumn: 'gridTasas_Descripcion',
    columns: [{
        header: 'Descripción',
        align:  'left',
        dataIndex: 'DescTasa',
		id:     'gridTasas_Descripcion',
        sortable: true
	},{
		header: 'Impuesto',
        align:  'left',
        dataIndex: 'DescImp',
        width:  130
	},{
		header: 'Porcentaje',
        align:  'left',
        dataIndex: 'ImpTasa',
		align:  'right',
        width:  80,
		renderer: function(value){return miFacturaWeb.formatearCantidad(value)+" %";}
    }],
    store: new Ext.data.JsonStore({
        idProperty: 'IDTasa',
        root: 'data',
        totalProperty: 'totalRows',
        messageProperty: "msg",
		autoSave: false,
        fields: [
			{name: 'IDTasa',     type: "int"},
			{name: 'DescTasa',   type: "string"},
			{name: 'DescImp',    type: "string"},
			{name: 'ImpTasa',    type: "float"},
			{name: 'ActivoTasa', type: "int", defaltValue: 1}
		],
        proxy:new Ext.data.HttpProxy({
            api: {
                read    : 'app.php/tasas/gettasas',
                create  : 'app.php/tasas/nuevo',
                update  : 'app.php/tasas/actualizar',
                destroy : 'app.php/tasas/eliminar'
            }
        }),
        writer : new Ext.data.JsonWriter({
            writeAllFields  : true,
            encodeDelete:true
        })
    }),

    refrescar:function(){
        this.store.load();
    },
    initComponent: function() {
    	
    	    	
       	this.columnaStatus="ActivoTasa";
       	this.cancelValue='0';
        miFacturaWeb.gridTasas.superclass.initComponent.call(this);
    }
});

Ext.reg('gridTasas', miFacturaWeb.gridTasas);




/*************************************************
//        ----- Formulario de Tasas -----         
**************************************************/

miFacturaWeb.formTasas = Ext.extend(miFacturaWeb.CatalogoForm, {
    //frame: false,
    unstyled :false,
    //border:false,
	bodyStyle:'border-width:1px 0 0 0;padding:8px;',
    cls: 'x-panel-mcs',
    style: 'padding-top:0px;border-width:1px 0 0 0;',
    
	autoScroll: true,
	reader: new Ext.data.JsonReader({
		idProperty: 'IDTasa',
		root: 'data',
		method: 'POST',
		fields: [
			{name: 'IDTasa'},
			{name: 'DescTasa', type:'string'},
			{name: 'KEYImpTasa'},
			{name: 'ImpTasa',  type: 'cantidad'},
			{name: 'ActivoTasa', defaultValue: 1}
        ]
    }),
 
    initComponent: function() {
		this.url    = 'app.php/tasas/guardar';
		this.urlGet = 'app.php/tasas/getregtasas';
		this.urlDel = 'app.php/tasas/delete';
		this.urlAct = 'app.php/tasas/activar';
		this.frame= false;
   // this.unstyled =true;
    //this.border=false;
   // this.cls= 'x-panel-mc';
   // this.style= 'padding-top:0px;';
		this.textFieldId = new Ext.form.TextField({
			xtype: 'textfield',
			fieldLabel: 'ID',
			readOnly: true,
			hidden:   true,
			name:    'IDTasa'
		});
		
		this.textFieldActivo = new Ext.form.TextField({
			xtype: 'textfield',
			fielLabel: 'Activo',
			readOnly: true,
			hidden:   true,
			name:  'ActivoTasa'
		});
		
		this.descripcion=new Ext.form.TextField({
            xtype: 'textfield',
            fieldLabel: 'Descripcion',
			labelStyle: 'font-weight:bold;',
            allowBlank: false,
			width: 300,
            name:  'DescTasa',
			autoCreate: {tag: 'input', maxlength: '50'}
        });
		
        this.items= [
			this.textFieldId,
			this.descripcion
		,{
			xtype:  'combo',
			fieldLabel: 'Impuesto',
			editable:   false,
			labelStyle: 'font-weight:bold;',
			allowBlank: false,
			width:  150,
			hiddenName: 'KEYImpTasa',
			triggerAction: 'all',
			mode:   'remote',
			store:  new Ext.data.JsonStore({
				url:   'app.php/tasas/getimpuestos',
				root:  'data',
				idProperty: 'IDImp',
				autoLoad:   true,
				fields:[
					{name: 'IDImp'},
					{name: 'DescImp', type:'string'}
				]
			}),
			valueField:   'IDImp',
			displayField: 'DescImp'
		},{

			xtype: 'textfield',
            fieldLabel: 'Porcentaje',
			labelStyle: 'font-weight:bold;',
            name:  'ImpTasa',
			style: 'text-align: right;',
			//minValue:   1,
			//maxValue:   99,
			//allowBlank: false,
			allowDecimals: true,
			//decimalPrecision: miFacturaWeb.parametros.dec_can_par,
			//value: miFacturaWeb.formatearCantidad(0),
			vtype: 'moneda',
			width: 80,
			emptyText: miFacturaWeb.formatearCantidad(0),
			getErrors: function(){
				var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
				if (this.getValue() <= 0 || this.getValue() >= 100){
					var msg = "El valor debe ser entre 1 y 99";
					errors.push(msg);
				}
				return errors;
			}
		},
			this.textFieldActivo
		];
		
        miFacturaWeb.formTasas.superclass.initComponent.call(this);
		this.on('actioncomplete',function(form,action){
			var id = action.result.data.IDTasa;
			var titulo = action.result.data.DescTasa;
			this.actualizarTab(id, titulo);
		});
    }
});

Ext.reg('formTasas', miFacturaWeb.formTasas);
