<?php
/*
 * Devuelve las rutas de los archivos javascript para el proyecto
 *  var $modoProduccion:
 *  indica si los archivos son para modo produccion o para el modo de desarrollo.
 * ----Produccion:
 *  A la ruta del JS le agrega '../' al inicio
 *  Te da la ruta para ext-all en lugar de ext-all-debug
 *
 *---- Desarrollo:
 *  te da la ruta de ext-all-debug en lugar de ext-all
 */
function getJsFiles($modoProduccion=true){
    $arrayFiles=Array();
	$arrayFiles[]="js/funciones.js";	  
		  
      $arrayFiles[]="js/administracion/comun/encoder.js";	  
      $arrayFiles[]="js/FusionWidgets_Enterprise/JSClass/FusionCharts.js";
      
    #----------------------FIREBUG------------------------------------
     
    if (!$modoProduccion){
 //       $arrayFiles[]="https://getfirebug.com/firebug-lite.js";
    }
    #----------------------LIBRERIAS EXTJS------------------------------------
    $arrayFiles[]="js/ext-3.4.0/adapter/ext/ext-base.js";    
    if ($modoProduccion){
        //$arrayFiles[]="js/ext-3.4.0/ext-all.js";      
        $arrayFiles[]="js/ext-3.4.0/ext-all-debug.js";  
    }else{        
        $arrayFiles[]="js/ext-3.4.0/ext-all-debug.js";
    }

    $arrayFiles[]="js/ext-3.4.0/src/locale/ext-lang-es.js";     //<---Idioma al español
	$arrayFiles[]="js/ext-ux/fixed_opera_vtype_bug.js";
    #----------------------MENSAJES EN LA BARRA SUPERIOR------------------------------------#
      $arrayFiles[]="js/app.js";
    #----------------------CREAR ESTILOS AL VUELO-------------------------------------------#	
    $arrayFiles[]="js/ext-ux/Ext.ux.TDGi.js";
    #----------------------        FORMATOS      -------------------------------------------#
    $arrayFiles[]="js/ext-ux/formatos.js";
    #----------------------        MENSAJES      -------------------------------------------#
    $arrayFiles[]="js/ext-ux/mensajes.js";
    #---------------------- TIPOS DE CANTIDAD Y MONEDA--------------------------------------#
    $arrayFiles[]="js/ext-ux/CantidadField.js";
    $arrayFiles[]="js/ext-ux/MonedaField.js";
    #----------------------         FILE UPLOADER           --------------------------------------#
   $arrayFiles[]="js/ext-ux/fileUploadField.js";
	// $arrayFiles[]="js/ext-ux/fileUploadField2.js";
    #----------------------         BOTON ELIMINAR           --------------------------------------#
    $arrayFiles[]="js/ext-ux/BotonEliminar.js";
    $arrayFiles[]="js/ext-ux/BotonesActivarDesactivar.js";
    #----------------------         CHECK COLUMN           --------------------------------------#
    $arrayFiles[]="js/ext-ux/gridCheckColumn.js";
    #----------Grid add column-----------------#
    $arrayFiles[]="js/ext-ux/grid_addcolumn.js";
    #----------SUPERBOX-----------------#
   // $arrayFiles[]="js/ext-ux/SuperBoxSelect/SuperBoxSelect.js";
   // <script type = "text/javascript" src= "js/jquery/jquery-1.4.1.js"> </script>
     #----------JQUERY 1.4.1-----------------#
    $arrayFiles[]="js/jquery/jquery-1.4.1.js";
    #----------------------         EN EL SERVIDOR DE TECHNOLOGIES NO SE MIRAN LAS BANDERAS    --------------------------------------#
    if ($modoProduccion){
        $arrayFiles[]="js/ext-ux/escape.js";
    }else{
        $arrayFiles[]="js/ext-ux/noescape.js";
    }
    #----------------------         COMUN           --------------------------------------#
    
    $arrayFiles[]="js/lib/eko_grid.js";
    $arrayFiles[]="js/administracion/comun/combo_negocios.js";
    $arrayFiles[]="js/ext-ux/BotonesStatus.js";
    $arrayFiles[]="js/administracion/comun/RFC_TextField.js";
    $arrayFiles[]="js/administracion/comun/grid_buscador.js";
    $arrayFiles[]="js/administracion/comun/comun_grid_buscador.js";
    $arrayFiles[]="js/administracion/comun/comun_form_edicion.js";
    $arrayFiles[]="js/administracion/comun/form_edicion.js";
    $arrayFiles[]="js/administracion/comun/panel_edicion.js";
    $arrayFiles[]="js/administracion/comun/basic_toolbar.js";
    $arrayFiles[]="js/administracion/comun/toolbar_buscador_basico.js";
    $arrayFiles[]="js/administracion/comun/toolbar_buscador_activos.js";
    $arrayFiles[]="js/administracion/comun/combo_ciudades.js";
    $arrayFiles[]="js/administracion/comun/tab_panel.js";
	
	//-----------------          ventana descuento general    ---------------------//
	$arrayFiles[]="js/administracion/comun/winDescuentos/winDescuentos.ui.js";
	$arrayFiles[]="js/administracion/comun/winDescuentos/winDescuentos.js";	
	
	//-----------------          Autorizacion Cancelacion Movimientos    ---------------------//
	$arrayFiles[]="js/administracion/comun/winAutorizacionCancelacion/winCancelaciones.ui.js";
	$arrayFiles[]="js/administracion/comun/winAutorizacionCancelacion/winCancelaciones.js";	

	//-----------------          ventana busqueda productos    ---------------------//
	$arrayFiles[]="js/administracion/comun/winBuscadorProductos/winBuscadorProductos.ui.js";
	$arrayFiles[]="js/administracion/comun/winBuscadorProductos/winBuscadorProductos.js";
	$arrayFiles[]="js/administracion/comun/winBuscadorProductos/winBuscadorProductosStore.js";		
	
		//------------------       Grid Catalogo de Clientes     ------------------//
	$arrayFiles[]="js/administracion/cat_clientes/gridClientes/gridClientes.ui.js";
	$arrayFiles[]="js/administracion/cat_clientes/gridClientes/gridClientes.js";
	$arrayFiles[]="js/administracion/cat_clientes/gridClientes/storeGridClientes.js";
	$arrayFiles[]="js/administracion/cat_clientes/gridClientes/storeGridClientesStatus.js";
	
		//------------------       Form Catalogo de Clientes     ------------------//
	$arrayFiles[]="js/administracion/cat_clientes/formClientes/formClientes.ui.js";
	$arrayFiles[]="js/administracion/cat_clientes/formClientes/formClientes.js";
	$arrayFiles[]="js/administracion/cat_clientes/formClientes/storeTipoCliente.js";
	$arrayFiles[]="js/administracion/cat_clientes/formClientes/storeEstilista.js";
	$arrayFiles[]="js/administracion/cat_clientes/formClientes/storeForaneo.js";
	$arrayFiles[]="js/administracion/cat_clientes/formClientes/storeClientesCiudades.js";
	$arrayFiles[]="js/administracion/cat_clientes/formClientes/storeClientesListaPrecio.js";		

		//------------------       Grid Catalogo de Productos     ------------------//
	$arrayFiles[]="js/administracion/cat_productos/gridProductos/gridProductos.ui.js";
	$arrayFiles[]="js/administracion/cat_productos/gridProductos/gridProductos.js";
	$arrayFiles[]="js/administracion/cat_productos/gridProductos/storeGridProductos.js";
	$arrayFiles[]="js/administracion/cat_productos/gridProductos/storeGridProductosStatus.js";
	
		//------------------       Form Catalogo de Productos     ------------------//
	$arrayFiles[]="js/administracion/cat_productos/formProductos/formProductos.ui.js";
	$arrayFiles[]="js/administracion/cat_productos/formProductos/formProductos.js";
	$arrayFiles[]="js/administracion/cat_productos/formProductos/storeTipoProducto.js";
	$arrayFiles[]="js/administracion/cat_productos/formProductos/storeProductosUnidades.js";
	$arrayFiles[]="js/administracion/cat_productos/formProductos/storeProductosLineas.js";
	$arrayFiles[]="js/administracion/cat_productos/formProductos/storeProductosImpuestos.js";
	
		//------------------       Grid Catalogo de Series     ------------------//
	$arrayFiles[]="js/administracion/cat_series/gridSeries/gridSeries.ui.js";
	$arrayFiles[]="js/administracion/cat_series/gridSeries/gridSeries.js";
	$arrayFiles[]="js/administracion/cat_series/gridSeries/storeGridSeries.js";
	$arrayFiles[]="js/administracion/cat_series/gridSeries/storeGridSeriesStatus.js";
	
			//------------------       Form Catalogo de Series     ------------------//
	$arrayFiles[]="js/administracion/cat_series/formSeries/formSeries.ui.js";
	$arrayFiles[]="js/administracion/cat_series/formSeries/formSeries.js";
	$arrayFiles[]="js/administracion/cat_series/formSeries/storeTipoSerie.js";
	
	//------------------       Grid Catalogo de Agentes     ------------------//
	$arrayFiles[]="js/administracion/cat_agentes/gridAgentes/gridAgentes.ui.js";
	$arrayFiles[]="js/administracion/cat_agentes/gridAgentes/gridAgentes.js";
	$arrayFiles[]="js/administracion/cat_agentes/gridAgentes/storeGridAgentes.js";
	$arrayFiles[]="js/administracion/cat_agentes/gridAgentes/storeGridAgentesStatus.js";
	
				//------------------       Form Catalogo de Agentes     ------------------//
	$arrayFiles[]="js/administracion/cat_agentes/formAgentes/formAgentes.ui.js";
	$arrayFiles[]="js/administracion/cat_agentes/formAgentes/formAgentes.js";
	
	//------------------       Grid Catalogo de Lineas     ------------------//
	$arrayFiles[]="js/administracion/cat_lineas/gridLineas/gridLineas.ui.js";
	$arrayFiles[]="js/administracion/cat_lineas/gridLineas/gridLineas.js";
	$arrayFiles[]="js/administracion/cat_lineas/gridLineas/storeGridLineas.js";
	$arrayFiles[]="js/administracion/cat_lineas/gridLineas/storeGridLineasStatus.js";
	
				//------------------       Form Catalogo de Lineas     ------------------//
	$arrayFiles[]="js/administracion/cat_lineas/formLineas/formLineas.ui.js";
	$arrayFiles[]="js/administracion/cat_lineas/formLineas/formLineas.js";
	$arrayFiles[]="js/administracion/cat_lineas/formLineas/storeFormLineasSucursales.js";
	
	//------------------       Grid Catalogo de Unidades de Medida    ------------------//
	$arrayFiles[]="js/administracion/cat_unidadesmedidas/gridUnidadesMedidas/gridUnidadesMedidas.ui.js";
	$arrayFiles[]="js/administracion/cat_unidadesmedidas/gridUnidadesMedidas/gridUnidadesMedidas.js";
	$arrayFiles[]="js/administracion/cat_unidadesmedidas/gridUnidadesMedidas/storeGridUnidadesMedidas.js";
	$arrayFiles[]="js/administracion/cat_unidadesmedidas/gridUnidadesMedidas/storeGridUnidadesMedidasStatus.js";
	
				//------------------       Form Catalogo de Unidades de Medida     ------------------//
	$arrayFiles[]="js/administracion/cat_unidadesmedidas/formUnidadesMedidas/formUnidadesMedidas.ui.js";
	$arrayFiles[]="js/administracion/cat_unidadesmedidas/formUnidadesMedidas/formUnidadesMedidas.js";
	
	
			//------------------       Grid Catalogo de Certificados     ------------------//
	$arrayFiles[]="js/administracion/cat_certificados/gridCertificados/gridCertificados.ui.js";
	$arrayFiles[]="js/administracion/cat_certificados/gridCertificados/gridCertificados.js";
	$arrayFiles[]="js/administracion/cat_certificados/gridCertificados/storeGridCertificados.js";
	$arrayFiles[]="js/administracion/cat_certificados/gridCertificados/storeGridCertificadosStatus.js";
	
			//------------------       Form Catalogo de Certificados     ------------------//
	$arrayFiles[]="js/administracion/cat_certificados/formCertificados/formCertificados.ui.js";
	$arrayFiles[]="js/administracion/cat_certificados/formCertificados/formCertificados.js";
	// $arrayFiles[]="js/administracion/cat_productos/formCertificados/storeTipoProducto.js";
	// $arrayFiles[]="js/administracion/cat_productos/formCertificados/storeProductosUnidades.js";
	// $arrayFiles[]="js/administracion/cat_productos/formCertificados/storeProductosLineas.js";
	// $arrayFiles[]="js/administracion/cat_productos/formCertificados/storeProductosImpuestos.js";
	
			//------------------       Grid Movimientos de Almacen     ------------------//
	$arrayFiles[]="js/administracion/movimientos_almacen/gridMovimientosAlmacen/gridMovimientosAlmacen.ui.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/gridMovimientosAlmacen/gridMovimientosAlmacen.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/gridMovimientosAlmacen/storeGridMovimientosAlmacen.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/gridMovimientosAlmacen/storeGridMovimientosAlmacenStatus.js";

			//------------------       Form Movimientos de Almacen     ------------------//
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/formMovimientosAlmacen.ui.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/formMovimientosAlmacen.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeFormMovimientosAlmacenSeries.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeFormMovimientosAlmacenGrid.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeFormMovimientosAlmacenTiposMovimientos.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeFormMovimientosAlmacenAlmacenes.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeFormMovimientosAlmacenProductos.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/formMovimientosAlmacenWinProductos.ui.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/formMovimientosAlmacenWinProductos.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeFormMovimientosAlmacenWinProductos.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/formMovimientosAlmacenWinMovimientos.ui.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/formMovimientosAlmacenWinMovimientos.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeMovimientosAlmacenWinMovimientos.js";
	$arrayFiles[]="js/administracion/movimientos_almacen/formMovimientosAlmacen/storeFormMovimientosAlmacenAgentes.js";	
		
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVenta.ui.js";
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVenta.js";
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaGrid.js";
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaProductos.js";
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVentaWinProductos.ui.js";
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVentaWinProductos.js";
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaWinProductos.js";	
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVentaWinVentas.ui.js";
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVentaWinVentas.js";
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaWinVentas.js";		
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVentaWinFormaPagos.ui.js";
	$arrayFiles[]="js/administracion/puntodeventa/formPuntoVentaWinFormaPagos.js";
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaWinFormaPagosCombo.js";	
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaWinFormaPagosGrid.js";	
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaClientes.js";	
	$arrayFiles[]="js/administracion/puntodeventa/storePuntoVentaAgentes.js";
	$arrayFiles[]="js/localprint.js";	
	
	
	$arrayFiles[]="js/administracion/reportes/formReporteExistencia/formReporteExistencia.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteExistencia/formReporteExistencia.js";
	$arrayFiles[]="js/administracion/reportes/formReporteExistencia/storeFormReporteExistenciaAlmacenes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteExistencia/storeFormReporteExistenciaLineas.js";
	
	$arrayFiles[]="js/administracion/reportes/formReporteVentas/formReporteVentas.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentas/formReporteVentas.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentas/storeFormReporteVentasClientes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentas/storeFormReporteVentasSucursales.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentas/storeFormReporteVentasAgentes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductos/formReporteVentasProductos.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductos/formReporteVentasProductos.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductos/storeFormReporteVentasProductosLineas.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductos/storeFormReporteVentasProductosSucursales.js";
	
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosCostos/formReporteVentasProductosCostos.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosCostos/formReporteVentasProductosCostos.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosCostos/storeFormReporteVentasProductosCostosLineas.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosCostos/storeFormReporteVentasProductosCostosSucursales.js";
	
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosExcel/formReporteVentasProductosExcel.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosExcel/formReporteVentasProductosExcel.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosExcel/storeFormReporteVentasProductosExcelLineas.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosExcel/storeFormReporteVentasProductosExcelSucursales.js";
		
	$arrayFiles[]="js/administracion/reportes/formReporteCarteraClientes/formReporteCarteraClientes.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteCarteraClientes/formReporteCarteraClientes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteCarteraClientes/storeFormReporteCarteraClientes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteCarteraClientes/storeFormReporteCarteraClientesAgentes.js";
	
	$arrayFiles[]="js/administracion/reportes/formReporteAbonosClientes/formReporteAbonosClientes.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteAbonosClientes/formReporteAbonosClientes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteAbonosClientes/storeFormReporteAbonosClientes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteAbonosClientes/storeFormReporteAbonosClientesAgentes.js";

	$arrayFiles[]="js/administracion/reportes/formReporteVentasClientes/formReporteVentasClientes.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasClientes/formReporteVentasClientes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasClientes/storeFormReporteVentasClientesClientes.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasClientes/storeFormReporteVentasClientesAgentes.js";
	
	$arrayFiles[]="js/administracion/reportes/formReportePedidoSugerido/formReportePedidoSugerido.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReportePedidoSugerido/formReportePedidoSugerido.js";
	$arrayFiles[]="js/administracion/reportes/formReportePedidoSugerido/storeFormReportePedidoSugeridoLineas.js";
	$arrayFiles[]="js/administracion/reportes/formReportePedidoSugerido/storeFormReportePedidoSugeridoSucursales.js";
	$arrayFiles[]="js/administracion/reportes/formReportePedidoSugerido/storeReportePedidoSugeridoTipo.js";

	$arrayFiles[]="js/administracion/reportes/formReporteMovimientosBancos/formReporteMovimientosBancos.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteMovimientosBancos/formReporteMovimientosBancos.js";
	$arrayFiles[]="js/administracion/reportes/formReporteMovimientosBancos/storeFormReporteMovimientosBancosConceptos.js";
	$arrayFiles[]="js/administracion/reportes/formReporteMovimientosBancos/storeFormReporteMovimientosBancosSucursales.js";
	
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosGlobal/formReporteVentasProductosGlobal.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosGlobal/formReporteVentasProductosGlobal.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosGlobal/storeFormReporteVentasProductosGlobalLineas.js";
	$arrayFiles[]="js/administracion/reportes/formReporteVentasProductosGlobal/storeFormReporteVentasProductosGlobalSucursales.js";
	
	$arrayFiles[]="js/administracion/reportes/formReporteChecadas/formReporteChecadas.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteChecadas/formReporteChecadas.js";
	$arrayFiles[]="js/administracion/reportes/formReporteChecadas/storeFormReporteChecadasEmpleados.js";

	$arrayFiles[]="js/administracion/reportes/formReporteFlujoEfectivo/formReporteFlujoEfectivo.ui.js";
	$arrayFiles[]="js/administracion/reportes/formReporteFlujoEfectivo/formReporteFlujoEfectivo.js";
	$arrayFiles[]="js/administracion/reportes/formReporteFlujoEfectivo/storeFormReporteFlujoEfectivoEmpresas.js";
	$arrayFiles[]="js/administracion/reportes/formReporteFlujoEfectivo/storeFormReporteFlujoEfectivoSucursales.js";
	
	
	//------------------       Grid Turnos     ------------------//
	$arrayFiles[]="js/administracion/turnos/gridTurnos/gridTurnos.ui.js";
	$arrayFiles[]="js/administracion/turnos/gridTurnos/gridTurnos.js";
	$arrayFiles[]="js/administracion/turnos/gridTurnos/storeGridTurnos.js";
	$arrayFiles[]="js/administracion/turnos/gridTurnos/storeGridTurnosStatus.js";
	
	//------------------       Form Turnos     ------------------//
	$arrayFiles[]="js/administracion/turnos/formTurnos/formTurnos.ui.js";
	$arrayFiles[]="js/administracion/turnos/formTurnos/formTurnos.js";
	$arrayFiles[]="js/administracion/turnos/formTurnos/storeFormTurnosDenominaciones.js";
	$arrayFiles[]="js/administracion/turnos/formTurnos/storeFormTurnosFormasPagos.js";
	$arrayFiles[]="js/administracion/turnos/formTurnos/storeFormTurnosGrid.js";
	
	//------------------       Grid Cortes     ------------------//
	$arrayFiles[]="js/administracion/cortes/gridCortes/gridCortes.ui.js";
	$arrayFiles[]="js/administracion/cortes/gridCortes/gridCortes.js";
	$arrayFiles[]="js/administracion/cortes/gridCortes/storeGridCortes.js";
	$arrayFiles[]="js/administracion/cortes/gridCortes/storeGridCortesStatus.js";
	
		//------------------       Form Cortes     ------------------//
	$arrayFiles[]="js/administracion/cortes/formCortes/formCortes.ui.js";
	$arrayFiles[]="js/administracion/cortes/formCortes/formCortes.js";
	$arrayFiles[]="js/administracion/cortes/formCortes/storeFormCortesDenominaciones.js";
	$arrayFiles[]="js/administracion/cortes/formCortes/storeFormCortesFormasPagos.js";
	$arrayFiles[]="js/administracion/cortes/formCortes/storeFormCortesGrid.js";
	$arrayFiles[]="js/administracion/cortes/formCortes/storeFormCortesDenominacionesRetencion.js";
	$arrayFiles[]="js/administracion/cortes/formCortes/storeFormCortesFormasPagosRetencion.js";
	$arrayFiles[]="js/administracion/cortes/formCortes/storeFormCortesGridRetencion.js";
	
	//------------------       Grid Movimientos de Caja     ------------------//
	$arrayFiles[]="js/administracion/movimientos_caja/gridMovimientosCaja/gridMovimientosCaja.ui.js";
	$arrayFiles[]="js/administracion/movimientos_caja/gridMovimientosCaja/gridMovimientosCaja.js";
	$arrayFiles[]="js/administracion/movimientos_caja/gridMovimientosCaja/storeGridMovimientosCaja.js";
	$arrayFiles[]="js/administracion/movimientos_caja/gridMovimientosCaja/storeGridMovimientosCajaStatus.js";
	
	//------------------       Form Movimientos de Caja     ------------------//
	$arrayFiles[]="js/administracion/movimientos_caja/formMovimientosCaja/formMovimientosCaja.ui.js";
	$arrayFiles[]="js/administracion/movimientos_caja/formMovimientosCaja/formMovimientosCaja.js";
	$arrayFiles[]="js/administracion/movimientos_caja/formMovimientosCaja/storeFormMovimientosCajaTipos.js";

			//------------------       Grid Inventarios     ------------------//
	$arrayFiles[]="js/administracion/inventarios/gridInventarios/gridInventarios.ui.js";
	$arrayFiles[]="js/administracion/inventarios/gridInventarios/gridInventarios.js";
	$arrayFiles[]="js/administracion/inventarios/gridInventarios/storeGridInventarios.js";
	$arrayFiles[]="js/administracion/inventarios/gridInventarios/storeGridInventariosStatus.js";	
	
	//------------------       Form Inventarios     ------------------//
	$arrayFiles[]="js/administracion/inventarios/formInventarios/formInventarios.ui.js";
	$arrayFiles[]="js/administracion/inventarios/formInventarios/formInventarios.js";
	$arrayFiles[]="js/administracion/inventarios/formInventarios/storeFormInventariosSeries.js";
	$arrayFiles[]="js/administracion/inventarios/formInventarios/storeFormInventariosGrid.js";
	$arrayFiles[]="js/administracion/inventarios/formInventarios/storeFormInventariosProductos.js";
	$arrayFiles[]="js/administracion/inventarios/formInventarios/formInventariosWinProductos.ui.js";
	$arrayFiles[]="js/administracion/inventarios/formInventarios/formInventariosWinProductos.js";
	$arrayFiles[]="js/administracion/inventarios/formInventarios/storeFormInventariosWinProductos.js";

	//------------------       Grid Remisiones     ------------------//
	$arrayFiles[]="js/administracion/remisiones/gridRemisiones/gridRemisiones.ui.js";
	$arrayFiles[]="js/administracion/remisiones/gridRemisiones/gridRemisiones.js";
	$arrayFiles[]="js/administracion/remisiones/gridRemisiones/storeGridRemisiones.js";
	$arrayFiles[]="js/administracion/remisiones/gridRemisiones/storeGridRemisionesStatus.js";

			//------------------       Form Remisiones     ------------------//
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/formRemisiones.ui.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/formRemisiones.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/storeFormRemisionesSeries.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/storeFormRemisionesGrid.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/storeFormRemisionesProductos.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/formRemisionesWinProductos.ui.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/formRemisionesWinProductos.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/storeFormRemisionesWinProductos.js";
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/storeFormRemisionesAgentes.js";	
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/storeFormRemisionesClientes.js";			
	$arrayFiles[]="js/administracion/remisiones/formRemisiones/storeFormRemisionesCondicionesPago.js";

	//------------------       Grid Abonos     ------------------//
	$arrayFiles[]="js/administracion/abonos/gridAbonos/gridAbonos.ui.js";
	$arrayFiles[]="js/administracion/abonos/gridAbonos/gridAbonos.js";
	$arrayFiles[]="js/administracion/abonos/gridAbonos/storeGridAbonos.js";
	$arrayFiles[]="js/administracion/abonos/gridAbonos/storeGridAbonosStatus.js";
	
		//------------------       Form Abonos     ------------------//
	$arrayFiles[]="js/administracion/abonos/formAbonos/formAbonos.ui.js";
	$arrayFiles[]="js/administracion/abonos/formAbonos/formAbonos.js";
	$arrayFiles[]="js/administracion/abonos/formAbonos/storeFormAbonosSeries.js";
	$arrayFiles[]="js/administracion/abonos/formAbonos/storeFormAbonosRemisiones.js";	
	$arrayFiles[]="js/administracion/abonos/formAbonos/storeFormAbonosClientes.js";			

			//------------------       Grid Movimientos Bancos     ------------------//
	$arrayFiles[]="js/administracion/movimientos_bancos/gridMovimientosBancos/gridMovimientosBancos.ui.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/gridMovimientosBancos/gridMovimientosBancos.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/gridMovimientosBancos/storeGridMovimientosBancos.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/gridMovimientosBancos/storeGridMovimientosBancosStatus.js";	
	
		//------------------       Form Movimientos Bancos     ------------------//
	$arrayFiles[]="js/administracion/movimientos_bancos/formMovimientosBancos/formMovimientosBancos.ui.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/formMovimientosBancos/formMovimientosBancos.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/formMovimientosBancos/storeFormMovimientosBancosChequeras.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/formMovimientosBancos/storeFormMovimientosBancosConceptos.js";	
	$arrayFiles[]="js/administracion/movimientos_bancos/formMovimientosBancos/storeFormMovimientosBancosSeries.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/formMovimientosBancos/storeFormMovimientosBancosTiposMovimientos.js";
	$arrayFiles[]="js/administracion/movimientos_bancos/formMovimientosBancos/storeFormMovimientosBancosTiposOrigen.js";	
	
		//------------------       Grid Movimientos Gastos     ------------------//
	$arrayFiles[]="js/administracion/gastos/gridGastos/gridGastos.ui.js";
	$arrayFiles[]="js/administracion/gastos/gridGastos/gridGastos.js";
	$arrayFiles[]="js/administracion/gastos/gridGastos/storeGridGastos.js";
	$arrayFiles[]="js/administracion/gastos/gridGastos/storeGridGastosStatus.js";	
	
		//------------------       Form Movimientos Gastos     ------------------//
	$arrayFiles[]="js/administracion/gastos/formGastos/formGastos.ui.js";
	$arrayFiles[]="js/administracion/gastos/formGastos/formGastos.js";
	$arrayFiles[]="js/administracion/gastos/formGastos/storeFormGastosChequeras.js";
	$arrayFiles[]="js/administracion/gastos/formGastos/storeFormGastosConceptos.js";	
	$arrayFiles[]="js/administracion/gastos/formGastos/storeFormGastosSeries.js";
	$arrayFiles[]="js/administracion/gastos/formGastos/storeFormGastosTiposMovimientos.js";
	$arrayFiles[]="js/administracion/gastos/formGastos/storeFormGastosTiposOrigen.js";	
	
		//------------------       Grid Catalogo de Conceptos     ------------------//
	$arrayFiles[]="js/administracion/cat_conceptos/gridConceptos/gridConceptos.ui.js";
	$arrayFiles[]="js/administracion/cat_conceptos/gridConceptos/gridConceptos.js";
	$arrayFiles[]="js/administracion/cat_conceptos/gridConceptos/storeGridConceptos.js";
	$arrayFiles[]="js/administracion/cat_conceptos/gridConceptos/storeGridConceptosStatus.js";

			//------------------       Form Catalogo de Conceptos     ------------------//
	$arrayFiles[]="js/administracion/cat_conceptos/formConceptos/formConceptos.ui.js";
	$arrayFiles[]="js/administracion/cat_conceptos/formConceptos/formConceptos.js";
	$arrayFiles[]="js/administracion/cat_conceptos/formConceptos/storeFormConceptosTipo.js";
	
		//------------------       Grid Catalogo de Chequeras     ------------------//
	$arrayFiles[]="js/administracion/cat_chequeras/gridChequeras/gridChequeras.ui.js";
	$arrayFiles[]="js/administracion/cat_chequeras/gridChequeras/gridChequeras.js";
	$arrayFiles[]="js/administracion/cat_chequeras/gridChequeras/storeGridChequeras.js";
	$arrayFiles[]="js/administracion/cat_chequeras/gridChequeras/storeGridChequerasStatus.js";
	
			//------------------       Form Catalogo de Chequeras     ------------------//
	$arrayFiles[]="js/administracion/cat_chequeras/formChequeras/formChequeras.ui.js";
	$arrayFiles[]="js/administracion/cat_chequeras/formChequeras/formChequeras.js";
	
			//------------------       Form Maximos y Minimos     ------------------//
	$arrayFiles[]="js/administracion/maximos_minimos/formMaximosMinimos/formMaximosMinimos.ui.js";
	$arrayFiles[]="js/administracion/maximos_minimos/formMaximosMinimos/formMaximosMinimos.js";
	$arrayFiles[]="js/administracion/maximos_minimos/formMaximosMinimos/storeFormMaximosMinimosAlmacenes.js";
	$arrayFiles[]="js/administracion/maximos_minimos/formMaximosMinimos/storeFormMaximosMinimosGrid.js";
	$arrayFiles[]="js/administracion/maximos_minimos/formMaximosMinimos/storeFormMaximosMinimosLineas.js";
	$arrayFiles[]="js/administracion/maximos_minimos/formMaximosMinimos/storeFormMaximosMinimosProductos.js";
	
		//------------------       Grid Catalogo de Empleados     ------------------//
	$arrayFiles[]="js/administracion/cat_empleados/gridEmpleados/gridEmpleados.ui.js";
	$arrayFiles[]="js/administracion/cat_empleados/gridEmpleados/gridEmpleados.js";
	$arrayFiles[]="js/administracion/cat_empleados/gridEmpleados/storeGridEmpleados.js";
	$arrayFiles[]="js/administracion/cat_empleados/gridEmpleados/storeGridEmpleadosStatus.js";	

	
			//------------------       Form Catalogo de Empleados     ------------------//
	$arrayFiles[]="js/administracion/cat_empleados/formEmpleados/formEmpleados.ui.js";
	$arrayFiles[]="js/administracion/cat_empleados/formEmpleados/formEmpleados.js";
	
		//------------------       Grid Citas     ------------------//
	$arrayFiles[]="js/administracion/citas/gridCitas/gridCitas.ui.js";
	$arrayFiles[]="js/administracion/citas/gridCitas/gridCitas.js";
	$arrayFiles[]="js/administracion/citas/gridCitas/storeGridCitas.js";
	$arrayFiles[]="js/administracion/citas/gridCitas/storeGridCitasStatus.js";	
	
		//------------------       Form Citas     ------------------//
	$arrayFiles[]="js/administracion/citas/formCitas/formCitas.ui.js";
	$arrayFiles[]="js/administracion/citas/formCitas/formCitas.js";
	$arrayFiles[]="js/administracion/citas/formCitas/storeFormCitasAgentes.js";
	$arrayFiles[]="js/administracion/citas/formCitas/storeFormCitasClientes.js";	
	$arrayFiles[]="js/administracion/citas/formCitas/storeFormCitasHorarios.js";	
	
	
		//------------------       Grid Catalogo de Horarios     ------------------//
	$arrayFiles[]="js/administracion/cat_horarios/gridHorarios/gridHorarios.ui.js";
	$arrayFiles[]="js/administracion/cat_horarios/gridHorarios/gridHorarios.js";
	$arrayFiles[]="js/administracion/cat_horarios/gridHorarios/storeGridHorarios.js";
	$arrayFiles[]="js/administracion/cat_horarios/gridHorarios/storeGridHorariosStatus.js";	

	
			//------------------       Form Catalogo de Horarios     ------------------//
	$arrayFiles[]="js/administracion/cat_horarios/formHorarios/formHorarios.ui.js";
	$arrayFiles[]="js/administracion/cat_horarios/formHorarios/formHorarios.js";
	
			//------------------       Form Checador     ------------------//
	$arrayFiles[]="js/administracion/checador/formChecador.ui.js";
	$arrayFiles[]="js/administracion/checador/formChecador.js";

	//------------------       Grid Catalogo de Listas de Precios     ------------------//
	$arrayFiles[]="js/administracion/cat_listaprecios/gridListaPrecios/gridListaPrecios.ui.js";
	$arrayFiles[]="js/administracion/cat_listaprecios/gridListaPrecios/gridListaPrecios.js";
	$arrayFiles[]="js/administracion/cat_listaprecios/gridListaPrecios/storeGridListaPrecios.js";
	$arrayFiles[]="js/administracion/cat_listaprecios/gridListaPrecios/storeGridListaPreciosStatus.js";

	//------------------       Form Catalogo de Listas de Precios     ------------------//
	$arrayFiles[]="js/administracion/cat_listaprecios/formListaPrecios/formListaPrecios.ui.js";
	$arrayFiles[]="js/administracion/cat_listaprecios/formListaPrecios/formListaPrecios.js";
	$arrayFiles[]="js/administracion/cat_listaprecios/formListaPrecios/storeFormListaPreciosGrid.js";
	$arrayFiles[]="js/administracion/cat_listaprecios/formListaPrecios/storeFormListaPreciosProductos.js";
	
	//------------------          Main		     ------------------//
	$arrayFiles[]="js/administracion/main/Main.ui.js";
	$arrayFiles[]="js/administracion/main/Main.js";
	$arrayFiles[]="js/administracion/main/mainStoreEmpresas.js";
	$arrayFiles[]="js/administracion/main/mainStoreSucursales.js";
	$arrayFiles[]="js/administracion/main/mainStoreAlmacenes.js";

	//------------------       Grid Cotizaciones     ------------------//
	$arrayFiles[]="js/administracion/cotizaciones/gridCotizaciones/gridCotizaciones.ui.js";
	$arrayFiles[]="js/administracion/cotizaciones/gridCotizaciones/gridCotizaciones.js";
	$arrayFiles[]="js/administracion/cotizaciones/gridCotizaciones/storeGridCotizaciones.js";
	$arrayFiles[]="js/administracion/cotizaciones/gridCotizaciones/storeGridCotizacionesStatus.js";

	//------------------       Form Cotizaciones     ------------------//
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/formCotizaciones.ui.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/formCotizaciones.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/storeFormCotizacionesSeries.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/storeFormCotizacionesGrid.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/storeFormCotizacionesProductos.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/formCotizacionesWinProductos.ui.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/formCotizacionesWinProductos.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/storeFormCotizacionesWinProductos.js";
	$arrayFiles[]="js/administracion/cotizaciones/formCotizaciones/storeFormCotizacionesClientes.js";		

	$arrayFiles[]="js/administracion/inicio.js";
    if ($modoProduccion){	
        for($i=0 ; $i<sizeof($arrayFiles) ;$i++){
            $arrayFiles[$i]='../'.$arrayFiles[$i]; //POR LA RUTA DEL SCRIPT DEL COMPRESOR
        }
    }
    return $arrayFiles;
}
?>