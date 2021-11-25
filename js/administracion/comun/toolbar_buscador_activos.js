Ext.ns('miFacturaWeb.users.ToolbarBuscadorActivos');
miFacturaWeb.comun.ToolbarBuscadorActivos=Ext.extend(Ext.Toolbar,{
    bubbleEvents:['pushAgregar','pushEditar','pushVerTodos','pushVerActivos','pushBuscar'],
    getFiltrarActivos:function(){
        return this.filtrar;
    },
    switchVista: function(obj){
        if (obj.text == 'Todos') {
            this.filtrar=false;
            if (this.botonVerActivos.pressed==true){
                this.botonVerActivos.toggle();
            }
        } else {
            this.filtrar=true;
            if (this.botonVerTodos.pressed==true){
                this.botonVerTodos.toggle();
            }
        }
        if (obj.pressed==false){
            obj.toggle();
        }else{
            this.fireEvent('pushBuscar');
        }
    },
    verTodos:function(){
        this.filtrar=false;
        this.fireEvent('pushBuscar');
        this.botonVerActivos.toggle();
    },
    verActivos:function(){
        this.filtrar=true;
        this.fireEvent('pushBuscar');
        this.botonVerTodos.toggle();
    },
    initComponent:function(){
        this.filtrar=true;
     //   this.style='border-top: none;';
        var rutaIcono="images/iconos/";

        this.botonEditar=new Ext.Button({
            text: 'Editar',
            width: 70,
            icon:rutaIcono+this.iconMaster+"_edit.png",
            handler:function(){
                this.ownerCt.fireEvent('pushEditar');
            }
        });

        this.botonAgregar=new Ext.Button({
            text: 'Agregar',
            width: 70,
            icon:rutaIcono+this.iconMaster+"_add.png",
            handler:function(){
                this.ownerCt.fireEvent('pushAgregar');
            }
        });

        this.botonVerTodos=new Ext.Button({
            text: 'Todos',
            enableToggle : true,
            icon:rutaIcono+this.iconMaster+"_todos.png",
            scope:this,
            handler:function(b,e){
              //  b.pressed=false;
                this.switchVista(b);
            }
        });

        this.botonVerActivos=new Ext.Button({
            text: 'Activos',
            enableToggle : true,
            pressed:true,
            icon:rutaIcono+this.iconMaster+"_activos.png",
            scope:this,
            handler:function(b,e){
              //  b.pressed=false;
                this.switchVista(b);
            }/*,
            toggleHandler :function(boton,state){
                state=true;
                return true;
            }*/
        });

         this.filtro=new Ext.form.TextField({
            grid:this,
            width:200,
            emptyText:"Escriba el texto a buscar",
            listeners: {
                specialkey: function(field, e){
                    if (e.getKey() == e.ENTER) {
                        this.ownerCt.fireEvent('pushBuscar');
                    }
                }
            }
        });
        this.style="border-top:none;border-bottom:none;";
        this.items=[
                 this.botonAgregar,this.botonEditar
                ,'-',this.botonVerActivos,this.botonVerTodos,"->",this.filtro,' ',' ',' '
        ];
        miFacturaWeb.comun.ToolbarBuscadorActivos.superclass.initComponent.call(this);
    }
})
Ext.reg('ToolbarBuscadorActivos', miFacturaWeb.comun.ToolbarBuscadorActivos);


