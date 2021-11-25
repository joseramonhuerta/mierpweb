var tplCatCiudades = new Ext.XTemplate(
	'<tpl for="."><div class="search-item">',
		'<h3 style="min-width:0;">{nom_ciu}</h3>',
		'<div>',
			'<div style="width:140px; float:left;">{nom_est}</div>',
			'<div style="width:100px; float:right; text-align:right;">',
				'<img src="images/banderas/{[miFacturaWeb.escape(values.img_pai)]}" align="absmiddle"/>&nbsp;{nom_pai:ellipsis(32)}',
			'</div>',
			'<div style="clear:both;"></div>',
		'</div>',
	'</div></tpl>'
);
miFacturaWeb.combo_ciudad = Ext.extend(Ext.form.ComboBox,{
    bubbleEvents:['ciudadSeleccionada'],
    hiddenName:  'ciu_def_par',
    tpl:   tplCatCiudades,
    itemSelector: 'div.search-item',
    displayField: 'nom_ciu',
    valueField:  'id_ciu',
    fieldLabel:  'Ciudad/Estado',
    labelStyle: 'font-weight:bold;',
    queryDelay:  250,
    pageSize:    10,
    listWidth:   300,
    width:       200,
    allowBlank: false,
    forceSelection: true,
    maskRe: /[^\\\/"']/,
    triggerAction:  'all',
    enableKeyEvents: true,
	seleccionarDefault:function(){
		var ciudad={
			id_ciu:miFacturaWeb.parametros.ciudad.id_ciu,
			nom_ciu:miFacturaWeb.parametros.ciudad.nom_ciu,
			nom_est:miFacturaWeb.parametros.ciudad.nom_est,
			id_est:miFacturaWeb.parametros.ciudad.id_est,
			nom_pai:miFacturaWeb.parametros.ciudad.nom_pai,
			id_pai:miFacturaWeb.parametros.ciudad.id_pai
		};

		this.store.loadData({data:ciudad});

		this.setValue(ciudad.id_ciu);

		var estado={
			id:ciudad.id_est,
			nombre:ciudad.nom_est
		};
		var pais={
		  id:ciudad.id_pai,
		  nombre:ciudad.nom_pai
		};
		var params={
			estado:estado,
			pais:pais
		};
		this.fireEvent('ciudadSeleccionada',params);
	},
    initComponent:function(){
        this.hiddenName=this.name;
        this.store= new Ext.data.JsonStore({
            scope: this,
            url:  'app.php/parametros/getcatciudades',
            totalProperty: 'totalRows',
            root: 'data',
            idProperty: 'id_ciu',
            autoDestroy: true,
            //autoLoad: true,
            fields: [{
                name: 'id_ciu',
                type: 'int'
            },{
                name: 'nom_ciu',
                type: 'string'
            },{
                name: 'id_est',
                type: 'int'
            },{
                name: 'nom_est',
                type: 'string'
            },{
                name: 'id_pai',
                type: 'int'
            },{
                name: 'nom_pai'
                ,type: 'string'
            },{
                name: 'img_pai'
           //     ,type: 'string',                
            }]
        });
        if (this.url!=undefined){
            this.store.proxy.api.create.url=this.url;
            this.store.proxy.api.destroy.url=this.url;
            this.store.proxy.api.read.url=this.url;
            this.store.proxy.api.update.url=this.url;
        }else{

        }
        this.listeners= {
            keyup:function(t,e){
                if (e.getKey() == 114 && e['ctrlKey'] && !e['altKey']) {
                    if(this.disabled){
						return;
					}
                    this.expand();
                    this.el.focus();
                    if(this.getRawValue())	this.doQuery(this.getRawValue());
                    else this.doQuery('@ALL001X23');
                    this.el.focus();
                }
            },
            keypress: function(t, e){
                if (e['altKey'] || (e['shiftKey'] && e.getKey() == 34) || e.getKey() == 39) {
                    e.stopEvent();
                }
            },
            blur: function(t){
                if(!t.getValue()){
                    //this.ownerCt.estado_pais.setValue('');
                }
            },
            beforequery: function(qe){
                delete qe.combo.lastQuery;
            },
            render:function(){                
                this.seleccionarDefault();
            },
            select: function(t, record){
                var estado={
                    id:record.get('id_est'),
                    nombre:record.get('nom_est')
                };
                var pais={
                  id:record.get('id_pai'),
                  nombre:record.get('nom_pai')
                };
                var params={
                    estado:estado,
                    pais:pais
                };
                this.fireEvent('ciudadSeleccionada',params);
            }
        }
        
        miFacturaWeb.combo_ciudad.superclass.initComponent.call(this);
    }

});
Ext.reg('comboCiudades', miFacturaWeb.combo_ciudad);
