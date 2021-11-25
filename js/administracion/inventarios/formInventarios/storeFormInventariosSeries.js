Ext.ns('miErpWeb');
miErpWeb.storeFormInventariosSeries = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormInventariosSeries.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormInventariosSeries',
			idProperty: 'id_serie',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'totalRows',
            fields:[
				{
                    name: 'id_serie'
                },
				{
                    name: 'nombre_serie',
                    type: 'string'
                },
				{
                    name: 'foliosig',
                    type: 'int'
                }
			
			],
            url: 'app.php/inventarios/obtenerseries'
        }, cfg));
    }
});
Ext.reg('storeFormInventariosSeries', miErpWeb.storeFormInventariosSeries);