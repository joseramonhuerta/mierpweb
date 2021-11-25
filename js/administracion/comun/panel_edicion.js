Ext.ns('miFacturaWeb.PanelEdicion');

miFacturaWeb.PanelEdicion=Ext.extend(Ext.Panel,{
    //frame:true,
    border:false,
	//cls:'x-panel-mc',
   // style:'padding-top:0;',
   bodyStyle:'border-width:1px 0 0 0;',
    autoScroll:true,

    closable: true,


    cambiarTitulo:function(){
         //CAMBIAR EL TITULO AL TAB         
        var valores=this.formEditor.getForm().getValues();
        var campoAmostrar=this.campoAmostrar;

        var titulo=valores[campoAmostrar];
        this.setTitle(Ext.util.Format.ellipsis(titulo,25,true), Ext.ux.TDGi.iconMgr.getIcon(this.iconMaster+"_edit"));

		if (this.formEditor.idValue!=undefined){
			this.idValue=this.formEditor.idValue;
		}else{
			var basicForm=this.formEditor.getForm();
			if (basicForm.idValue!=undefined){
				this.idForm=basicForm.idValue;
			}
		}
		
        var form=this.formEditor.getForm();
      //  var selectorOld=form.selector;
		
       // this.removeClass(selectorOld); //<--QUITÉ EL CSS

        if (this.topToolbar!=undefined){
            if (this.topToolbar.botonAgregar!=undefined){
                this.topToolbar.botonAgregar.setIcon("images/iconos/"+this.iconMaster+"_edit.png");
            }

        }
      //  var selectorNuevo=form.moduloText+'-form-'+form.idValue;
      //  form.selector=selectorNuevo;                //<--GUARDO LA REFERENCIA AL CSS
      //  this.addClass(selectorNuevo);                  //<-- CSS ACTUALIZADO

    },
    initComponent:function(){
       // this.addClass('x-panel-mc');
        var bubbleEvents=['editorListo'];

      //  this.width=900;
        if (this.tbar==undefined & this.xtypeToolbar==undefined){
            this.tbar={
                xtype:'BasicToolbar',
                iconMaster:this.iconMaster,
                iconoBotonSave:this.iconoBotonSave,
                panelEdicion:this,
                listeners:{
                    pushAgregar:function(){
                        this.panelEdicion.formEditor.hacerSubmit();
                    },
                    pushGuardar:function(){
                        this.panelEdicion.formEditor.hacerSubmit();
                    },
                    pushEliminar:function(){                        
                        this.panelEdicion.formEditor.eliminar();
                    }                    
                }
            };
        }
        
        
        this.addListener('render',function(){            

         /*   var mask = new Ext.LoadMask(this.el, {
                msg:'espere por favor...'
            });
            this.mask=mask;*/
          //  this.el.mask(miFacturaWeb.mensajes.mensajeDeEspera);
        },this);

        try{
            if (this.bubbleEvents!=undefined){
                this.bubbleEvents=this.bubbleEvents.concat(bubbleEvents);
            }
        }catch(e){
            this.bubbleEvents=bubbleEvents;
        }
        
        miFacturaWeb.PanelEdicion.superclass.initComponent.call(this);
    }
});


//Ext.reg('PanelEdicion', miFacturaWeb.PanelEdicion);