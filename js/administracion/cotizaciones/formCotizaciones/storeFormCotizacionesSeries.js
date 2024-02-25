Ext.ns('miErpWeb');
miErpWeb.storeFormCotizacionesSeries = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormCotizacionesSeries.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormCotizacionesSeries',
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
            url: 'app.php/cotizaciones/obtenerseries'
        }, cfg));
    }
});
Ext.reg('storeFormCotizacionesSeries', miErpWeb.storeFormCotizacionesSeries);