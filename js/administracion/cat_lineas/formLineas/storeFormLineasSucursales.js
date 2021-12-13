
Ext.ns('miErpWeb');
miErpWeb.storeFormLineasSucursales = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormLineasSucursales.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormLineasSucursales',
			//autoDestroy: true,
            idProperty: 'id_linea',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_sucursal'
                },
				{
                    name: 'nombre_sucursal',
                    type: 'string'
                },
				{
                    name: 'id_empresa'
                },
				{
                    name: 'nombre_empresa',
                    type: 'string'
                }	
			],
            url: 'app.php/lineas/obtenersucursales'
        }, cfg));
    }
});
Ext.reg('storeFormLineasSucursales', miErpWeb.storeFormLineasSucursales);
