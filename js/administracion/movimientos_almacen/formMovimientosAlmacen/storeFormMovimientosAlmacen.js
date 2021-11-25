Ext.ns('miErpWeb');
miErpWeb.comboSerie = Ext.extend(Ext.data.JsonStore, {
    constructor: function(cfg) {
        cfg = cfg || {};
        miErpWeb.comboSerie.superclass.constructor.call(this, Ext.apply({
            storeId: 'comboSerie',
			idProperty: 'id_serie',
			messageProperty: 'msg',
            root: 'data',
			totalProperty: 'id_serie',
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
Ext.reg('storeFormMovimientosAlmacen', miErpWeb.storeFormMovimientosAlmacen);
