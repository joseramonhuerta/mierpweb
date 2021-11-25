Ext.ns('miFacturaWeb.CantidadField');

miFacturaWeb.CantidadField = Ext.extend(Ext.form.TextField, {
	allowBlank: false,
	minimo: 0,
	maximo: 1000000,
	
	getErrors: function(){
		var errors = Ext.form.TextField.superclass.getErrors.apply(this, arguments);
		if (this.getValue() < this.minimo || this.getValue() > this.maximo){
			var msg = "El valor debe ser entre "+ this.minimo +" y "+ this.maximo;
			errors.push(msg);
		}
		if (this.allowBlank == false && (this.getValue() == '' || this.getValue() == 0)){
			var msg = "El valor 0 no esta permitido";
			errors.push(msg);
		}
		if (isNaN(this.getValue())){
			var msg = "Numero incorrecto";
			errors.push(msg);
		}
		return errors;
	},
	
	initComponent: function(){
		this.emptyText  = this.emptyText || miFacturaWeb.formatearCantidad(0),
		
		miFacturaWeb.CantidadField.superclass.initComponent.call(this);
	}
});

Ext.reg("cantidadfield", miFacturaWeb.CantidadField);