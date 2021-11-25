Ext.ns('miErpWeb');
miErpWeb.storeFormRemisionesSeries = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormRemisionesSeries.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormRemisionesSeries',
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
            url: 'app.php/remisiones/obtenerseries'
        }, cfg));
    }
});
Ext.reg('storeFormRemisionesSeries', miErpWeb.storeFormRemisionesSeries);