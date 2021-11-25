Ext.ns('miErpWeb');
miErpWeb.storeFormGastosSeries = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.storeFormGastosSeries.superclass.constructor.call(this, Ext.apply({
            storeId: 'storeFormGastosSeries',
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
            url: 'app.php/movimientosbanco/obtenerseriesgastos'
        }, cfg));
    }
});
Ext.reg('storeFormGastosSeries', miErpWeb.storeFormGastosSeries);