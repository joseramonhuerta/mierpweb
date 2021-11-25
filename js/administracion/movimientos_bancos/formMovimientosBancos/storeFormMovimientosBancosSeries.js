Ext.ns('miErpWeb');
miErpWeb.storeFormMovimientosBancosSeries = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormMovimientosBancosSeries.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormMovimientosBancosSeries',
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
            url: 'app.php/movimientosbanco/obtenerseries'
        }, cfg));
    }
});
Ext.reg('storeFormMovimientosBancosSeries', miErpWeb.storeFormMovimientosBancosSeries);