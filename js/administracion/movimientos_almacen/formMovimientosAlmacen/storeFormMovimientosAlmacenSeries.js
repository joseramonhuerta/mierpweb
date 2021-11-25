Ext.ns('miErpWeb');
miErpWeb.storeFormMovimientosAlmacenSeries = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormMovimientosAlmacenSeries.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormMovimientosAlmacenSeries',
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
            url: 'app.php/movimientosalmacen/obtenerseries'
        }, cfg));
    }
});
Ext.reg('storeFormMovimientosAlmacenSeries', miErpWeb.storeFormMovimientosAlmacenSeries);