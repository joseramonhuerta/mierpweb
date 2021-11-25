Ext.ns('miErpWeb');
miErpWeb.storeFormAbonosSeries = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormAbonosSeries.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormAbonosSeries',
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
            url: 'app.php/abonos/obtenerseries'
        }, cfg));
    }
});
Ext.reg('storeFormAbonosSeries', miErpWeb.storeFormAbonosSeries);