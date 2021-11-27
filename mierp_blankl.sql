/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 5.6.51-cll-lve : Database - erp_blank
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`erp_blank` /*!40100 DEFAULT CHARACTER SET latin1 */;

/*Table structure for table `cat_agentes` */

DROP TABLE IF EXISTS `cat_agentes`;

CREATE TABLE `cat_agentes` (
  `id_agente` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_agente` varchar(200) NOT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_agente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_almacenes` */

DROP TABLE IF EXISTS `cat_almacenes`;

CREATE TABLE `cat_almacenes` (
  `id_almacen` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `codigo_almacen` varchar(20) DEFAULT NULL,
  `nombre_almacen` varchar(255) DEFAULT NULL,
  `tipo_almacen` tinyint(1) DEFAULT '1' COMMENT '1= Almacena y vende, 2= solo vende, 3= Solo almacena, 4=Produccion, 5=Traslado / en ruta',
  `status` varchar(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `esdefault` tinyint(1) DEFAULT '0' COMMENT '0=No, 1=Si',
  PRIMARY KEY (`id_almacen`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_certificados` */

DROP TABLE IF EXISTS `cat_certificados`;

CREATE TABLE `cat_certificados` (
  `id_certificado` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `archivo_certificado` varchar(100) NOT NULL,
  `archivo_llave` varbinary(100) DEFAULT NULL,
  `numero_certificado` varchar(20) DEFAULT NULL,
  `fecha_solicitud` datetime DEFAULT NULL,
  `fecha_vencimiento` datetime DEFAULT NULL,
  `rfc_certificado` varchar(15) DEFAULT NULL,
  `razonsocial_certificado` varchar(100) DEFAULT NULL,
  `pass_certificado` blob,
  `pem_certificado` blob,
  `pem_llave` blob,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `esdefault` tinyint(1) DEFAULT '0' COMMENT '0=No, 1=Si',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_certificado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_chequeras` */

DROP TABLE IF EXISTS `cat_chequeras`;

CREATE TABLE `cat_chequeras` (
  `id_chequera` bigint(20) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_chequera`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_ciudades` */

DROP TABLE IF EXISTS `cat_ciudades`;

CREATE TABLE `cat_ciudades` (
  `id_ciu` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID de la ciudad',
  `nom_ciu` varchar(60) DEFAULT NULL COMMENT 'Nombre de la ciudad',
  `key_est_ciu` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Estado',
  `key_pai_ciu` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'País',
  `uso_ciu` bigint(20) unsigned DEFAULT '0' COMMENT 'Indice de uso de la ciudad',
  PRIMARY KEY (`id_ciu`,`key_est_ciu`,`key_pai_ciu`)
) ENGINE=MyISAM AUTO_INCREMENT=4049 DEFAULT CHARSET=utf8;

/*Table structure for table `cat_clientes` */

DROP TABLE IF EXISTS `cat_clientes`;

CREATE TABLE `cat_clientes` (
  `id_cliente` bigint(20) NOT NULL AUTO_INCREMENT,
  `rfc_cliente` varchar(15) DEFAULT NULL,
  `nombre_comercial` varchar(255) DEFAULT NULL,
  `nombre_fiscal` varchar(255) DEFAULT NULL,
  `tipo_cliente` char(1) DEFAULT NULL,
  `calle` varchar(255) DEFAULT NULL,
  `numext` varchar(50) DEFAULT NULL,
  `numint` varchar(50) DEFAULT NULL,
  `colonia` varchar(100) DEFAULT NULL,
  `cp` varchar(10) DEFAULT NULL,
  `localidad` varchar(200) DEFAULT NULL,
  `id_ciu` int(11) DEFAULT NULL,
  `id_est` int(11) DEFAULT NULL,
  `id_pai` int(11) DEFAULT NULL,
  `nombre_contacto` varchar(200) DEFAULT NULL,
  `email_contacto` varchar(100) DEFAULT NULL,
  `telefono_contacto` varchar(20) DEFAULT NULL,
  `estilista` tinyint(4) DEFAULT '0',
  `celular_contacto` varchar(20) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `foraneo` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`id_cliente`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_conceptos` */

DROP TABLE IF EXISTS `cat_conceptos`;

CREATE TABLE `cat_conceptos` (
  `id_concepto` bigint(20) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(255) DEFAULT NULL,
  `tipo` tinyint(1) DEFAULT NULL COMMENT '1=Ingresos,2=Gastos',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_concepto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_denominaciones` */

DROP TABLE IF EXISTS `cat_denominaciones`;

CREATE TABLE `cat_denominaciones` (
  `id_denominacion` bigint(20) NOT NULL AUTO_INCREMENT,
  `denominacion` decimal(8,2) DEFAULT '0.00',
  PRIMARY KEY (`id_denominacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_empleados` */

DROP TABLE IF EXISTS `cat_empleados`;

CREATE TABLE `cat_empleados` (
  `id_empleado` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo_empleado` varchar(20) NOT NULL,
  `nombre_empleado` varchar(200) NOT NULL,
  `celular` varchar(20) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_empleado`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_empresas` */

DROP TABLE IF EXISTS `cat_empresas`;

CREATE TABLE `cat_empresas` (
  `id_empresa` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) DEFAULT NULL,
  `apepaterno` varchar(50) DEFAULT NULL,
  `apematerno` varchar(50) DEFAULT NULL,
  `nombre_comercial` varchar(250) DEFAULT NULL,
  `nombre_fiscal` varchar(250) DEFAULT NULL,
  `tipo_empresa` enum('F','M') NOT NULL DEFAULT 'M',
  `rfc` varchar(15) DEFAULT NULL,
  `maneja_inventario` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0=No, 1=Si',
  `logotipo` varchar(255) DEFAULT NULL,
  `calle` varchar(200) DEFAULT NULL,
  `numext` varchar(50) DEFAULT NULL,
  `numint` varchar(50) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `colonia` varchar(200) DEFAULT NULL,
  `localidad` varchar(255) DEFAULT NULL,
  `id_ciu` bigint(20) DEFAULT NULL,
  `id_est` bigint(20) DEFAULT NULL,
  `id_pai` bigint(20) DEFAULT NULL,
  `regimen_fiscal` varchar(255) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `status` varchar(1) DEFAULT 'A' COMMENT 'A=Activo,I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `logotipo_sucursal` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_empresa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_estados` */

DROP TABLE IF EXISTS `cat_estados`;

CREATE TABLE `cat_estados` (
  `id_est` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID del estado',
  `nom_est` varchar(60) DEFAULT NULL COMMENT 'Nombre del estado',
  `key_pai_est` smallint(5) unsigned NOT NULL DEFAULT '0' COMMENT 'País',
  `uso_est` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Indice de uso del estado',
  PRIMARY KEY (`id_est`,`key_pai_est`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8;

/*Table structure for table `cat_formaspagos` */

DROP TABLE IF EXISTS `cat_formaspagos`;

CREATE TABLE `cat_formaspagos` (
  `id_formapago` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_formapago` varchar(100) DEFAULT NULL,
  `tipo_formapago` tinyint(4) DEFAULT NULL COMMENT '1=Efectivo,2=Tarjeta Debito,3=Tarjeta Credito,4=Transferencia,5=Cheque',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_formapago`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_horarios` */

DROP TABLE IF EXISTS `cat_horarios`;

CREATE TABLE `cat_horarios` (
  `id_horario` bigint(20) NOT NULL AUTO_INCREMENT,
  `hora_inicio` datetime NOT NULL,
  `hora_fin` datetime NOT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_horario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_impuestos` */

DROP TABLE IF EXISTS `cat_impuestos`;

CREATE TABLE `cat_impuestos` (
  `id_impuesto` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_impuesto` varchar(200) DEFAULT NULL,
  `porcentaje` decimal(14,6) DEFAULT NULL,
  `tipo_impuesto` tinyint(4) DEFAULT NULL COMMENT '1=IVA,2=Ret IVA,3=Ret ISR',
  PRIMARY KEY (`id_impuesto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_lineas` */

DROP TABLE IF EXISTS `cat_lineas`;

CREATE TABLE `cat_lineas` (
  `id_linea` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_linea` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_linea`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_modulos` */

DROP TABLE IF EXISTS `cat_modulos`;

CREATE TABLE `cat_modulos` (
  `id_modulo` mediumint(9) NOT NULL AUTO_INCREMENT COMMENT 'ID del Modulo',
  `descripcion` varchar(50) NOT NULL COMMENT 'Descripcion del Modulo',
  `id_padre` mediumint(9) DEFAULT '0',
  `orden` int(11) DEFAULT '0',
  `newWin` char(20) DEFAULT NULL,
  `newTab` char(50) DEFAULT NULL,
  `icono` char(50) DEFAULT NULL,
  `controller` char(100) DEFAULT NULL COMMENT 'Controlador',
  PRIMARY KEY (`id_modulo`)
) ENGINE=MyISAM AUTO_INCREMENT=23012 DEFAULT CHARSET=utf8 COMMENT='Catalogo de Modulos de Acceso';

/*Table structure for table `cat_paises` */

DROP TABLE IF EXISTS `cat_paises`;

CREATE TABLE `cat_paises` (
  `id_pai` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Código',
  `id_iso_pai` smallint(6) NOT NULL,
  `id_iso2_pai` char(2) NOT NULL,
  `id_iso3_pai` char(3) NOT NULL,
  `nom_pai` varchar(80) NOT NULL COMMENT 'Nombre del país',
  `uso_pai` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT 'Contador de uso',
  PRIMARY KEY (`id_pai`)
) ENGINE=MyISAM AUTO_INCREMENT=241 DEFAULT CHARSET=utf8;

/*Table structure for table `cat_parametros` */

DROP TABLE IF EXISTS `cat_parametros`;

CREATE TABLE `cat_parametros` (
  `id_parametro` int(11) NOT NULL AUTO_INCREMENT,
  `descripcion` varchar(100) DEFAULT NULL,
  `ciudad_default` int(11) DEFAULT NULL,
  `estado_default` int(11) DEFAULT NULL,
  `pais_default` int(11) DEFAULT NULL,
  `decimales_moneda` tinyint(1) DEFAULT '0',
  `decimales_cantidad` tinyint(1) DEFAULT '0',
  `registros_pagina` mediumint(6) DEFAULT '0',
  `tipo_texto` char(1) DEFAULT '1',
  `metodo_costeo` tinyint(4) DEFAULT NULL,
  `smtp_servidor` varchar(100) DEFAULT NULL,
  `smtp_puerto` varchar(100) DEFAULT NULL,
  `smtp_usuario` varchar(100) DEFAULT NULL,
  `smtp_pass` varchar(100) DEFAULT NULL,
  `status` char(1) NOT NULL DEFAULT 'I' COMMENT 'A=Activo,I=Inactivo',
  `id_serie_entrada` bigint(20) DEFAULT NULL COMMENT 'Serie para entradas de ajuste inventario',
  `id_serie_salida` bigint(20) DEFAULT NULL COMMENT 'Serie para salidas de ajuste de inventarios',
  PRIMARY KEY (`id_parametro`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Table structure for table `cat_parametros_empresas` */

DROP TABLE IF EXISTS `cat_parametros_empresas`;

CREATE TABLE `cat_parametros_empresas` (
  `id_parametro_empresa` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `porcentaje_credito` decimal(18,6) DEFAULT '0.000000',
  `porcentaje_foraneos` decimal(18,6) DEFAULT '0.000000',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_parametro_empresa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_parametros_ventas` */

DROP TABLE IF EXISTS `cat_parametros_ventas`;

CREATE TABLE `cat_parametros_ventas` (
  `id_parametro_venta` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) DEFAULT NULL,
  `id_serie` bigint(20) DEFAULT NULL,
  `id_cliente` bigint(20) DEFAULT NULL,
  `agrega_concepto_auto` tinyint(1) DEFAULT '0',
  `impresion_ticket` tinyint(1) DEFAULT '0',
  `id_serie_eaju` bigint(20) DEFAULT NULL,
  `id_serie_saju` bigint(20) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `mostrar_agente` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_parametro_venta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_productos` */

DROP TABLE IF EXISTS `cat_productos`;

CREATE TABLE `cat_productos` (
  `id_producto` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) DEFAULT NULL,
  `codigo_barras` varchar(20) DEFAULT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `detalles` text,
  `id_linea` bigint(20) DEFAULT NULL,
  `id_unidadmedida` bigint(20) DEFAULT NULL,
  `id_proveedor` bigint(20) DEFAULT NULL,
  `precio_compra` decimal(14,6) DEFAULT '0.000000',
  `precio_venta` decimal(14,6) DEFAULT '0.000000',
  `precio_estilista` decimal(14,6) DEFAULT '0.000000',
  `metodo_costeo` tinyint(1) DEFAULT '0',
  `ultimo_costo` decimal(14,6) DEFAULT '0.000000',
  `costo_promedio` decimal(14,6) DEFAULT '0.000000',
  `tipo_producto` char(1) DEFAULT 'P' COMMENT 'P=Producto, S=Servicio',
  `stock_minimo` decimal(14,6) DEFAULT '0.000000',
  `stock_marximo` decimal(14,6) DEFAULT '0.000000',
  `stock` decimal(14,6) DEFAULT '0.000000',
  `iva` tinyint(1) DEFAULT '0',
  `ret_isr` tinyint(1) DEFAULT '0',
  `ret_iva` tinyint(1) DEFAULT '0',
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_producto`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_productos_stocks` */

DROP TABLE IF EXISTS `cat_productos_stocks`;

CREATE TABLE `cat_productos_stocks` (
  `id_stock` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_almacen` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `stock_min` decimal(14,6) DEFAULT '0.000000',
  `stock_max` decimal(14,6) DEFAULT '0.000000',
  `stock` decimal(14,6) DEFAULT '0.000000',
  `stock_aviso` decimal(14,6) DEFAULT '0.000000',
  `AddUsuario` bigint(20) DEFAULT NULL,
  `AddFecha` datetime DEFAULT NULL,
  `ModUsuario` bigint(20) DEFAULT NULL,
  `ModFecha` datetime DEFAULT NULL,
  PRIMARY KEY (`id_stock`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_proveedores` */

DROP TABLE IF EXISTS `cat_proveedores`;

CREATE TABLE `cat_proveedores` (
  `id_proveedor` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_proveedor` varchar(255) DEFAULT NULL,
  `status` char(1) DEFAULT 'A',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_proveedor`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_series` */

DROP TABLE IF EXISTS `cat_series`;

CREATE TABLE `cat_series` (
  `id_serie` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `nombre_serie` varchar(20) NOT NULL,
  `folioinicio` int(11) NOT NULL,
  `foliofin` int(11) NOT NULL,
  `foliosig` int(11) DEFAULT NULL,
  `tipo_serie` tinyint(1) NOT NULL COMMENT '0=Factura,1=NOTAS DE CREDITO,2=COMPRAS,3=VENTAS,4=INVENTARIO,5=NOMINA,6=REMISIONES, 7=ENTRADAS, 8=SALIDAS, 9=ABONOS,10=MOVIMIENTOS BANCOS',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_serie`,`id_empresa`,`id_sucursal`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_sucursales` */

DROP TABLE IF EXISTS `cat_sucursales`;

CREATE TABLE `cat_sucursales` (
  `id_sucursal` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `nombre_sucursal` varchar(250) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `calle` varchar(200) DEFAULT NULL,
  `numext` varchar(50) DEFAULT NULL,
  `numint` varchar(50) DEFAULT NULL,
  `colonia` varchar(200) DEFAULT NULL,
  `cp` varchar(5) DEFAULT NULL,
  `localidad` varchar(200) DEFAULT NULL,
  `ciudad` varchar(200) DEFAULT NULL,
  `estado` varchar(200) DEFAULT NULL,
  `pais` varchar(200) DEFAULT NULL,
  `logotipo` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_sucursal`,`id_empresa`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_tiposmovimientos` */

DROP TABLE IF EXISTS `cat_tiposmovimientos`;

CREATE TABLE `cat_tiposmovimientos` (
  `id_tipomovimiento` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo_movimiento` varchar(10) NOT NULL,
  `nombre_movimiento` varchar(50) NOT NULL,
  `tipo_movimiento` tinyint(1) NOT NULL,
  `genera_recosteo` tinyint(1) NOT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_tipomovimiento`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

/*Table structure for table `cat_unidadesdemedida` */

DROP TABLE IF EXISTS `cat_unidadesdemedida`;

CREATE TABLE `cat_unidadesdemedida` (
  `id_unidadmedida` bigint(20) NOT NULL AUTO_INCREMENT,
  `codigo_unidad` varchar(20) DEFAULT NULL,
  `descripcion_unidad` varchar(100) DEFAULT NULL,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo, I=Inactivo',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_unidadmedida`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_usuarios` */

DROP TABLE IF EXISTS `cat_usuarios`;

CREATE TABLE `cat_usuarios` (
  `id_usuario` bigint(20) NOT NULL AUTO_INCREMENT,
  `nombre_usuario` varchar(150) DEFAULT NULL,
  `usuario` varchar(100) DEFAULT NULL,
  `pass` blob,
  `status` char(1) DEFAULT 'A' COMMENT 'A=Activo,I=Inactivo',
  `esadmin` tinyint(1) DEFAULT '0',
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `puede_eliminar` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id_usuario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cat_usuarios_privilegios` */

DROP TABLE IF EXISTS `cat_usuarios_privilegios`;

CREATE TABLE `cat_usuarios_privilegios` (
  `id_usuario_privilegio` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_usuario` bigint(20) NOT NULL,
  `id_privilegio` bigint(20) NOT NULL,
  `tipo_privilegio` tinyint(4) NOT NULL COMMENT '1=Empresa,2=Sucursal,3=Almacen,4=Modulo',
  PRIMARY KEY (`id_usuario_privilegio`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `checadas` */

DROP TABLE IF EXISTS `checadas`;

CREATE TABLE `checadas` (
  `id_checada` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empleado` bigint(20) NOT NULL,
  `fecha_hora` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_checada`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `citas` */

DROP TABLE IF EXISTS `citas`;

CREATE TABLE `citas` (
  `id_cita` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_cliente` bigint(20) NOT NULL,
  `id_agente` bigint(20) DEFAULT NULL,
  `fecha` date DEFAULT NULL,
  `id_horario` bigint(20) NOT NULL,
  `observaciones` varchar(200) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cita`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cortes` */

DROP TABLE IF EXISTS `cortes`;

CREATE TABLE `cortes` (
  `id_corte` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_turno` bigint(20) NOT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `fecha_corte` datetime DEFAULT NULL,
  `total_liquidado` decimal(18,2) DEFAULT '0.00',
  `total_retenido` decimal(18,2) DEFAULT '0.00',
  `total_corte` decimal(18,2) DEFAULT '0.00',
  `total_turno` decimal(18,2) DEFAULT '0.00',
  `total_ventas` decimal(18,2) DEFAULT '0.00',
  `total_depositos` decimal(18,2) DEFAULT '0.00',
  `total_retiros` decimal(18,2) DEFAULT '0.00',
  `diferencia_corte` decimal(18,2) DEFAULT '0.00',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_corte`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cortes_liquidaciones` */

DROP TABLE IF EXISTS `cortes_liquidaciones`;

CREATE TABLE `cortes_liquidaciones` (
  `id_corte_liquidacion` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_corte` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `id_denominacion` bigint(20) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_corte_liquidacion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cortes_retenciones` */

DROP TABLE IF EXISTS `cortes_retenciones`;

CREATE TABLE `cortes_retenciones` (
  `id_corte_retencion` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_corte` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `id_denominacion` bigint(20) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_corte_retencion`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cxc` */

DROP TABLE IF EXISTS `cxc`;

CREATE TABLE `cxc` (
  `id_cxc` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_remision` bigint(20) DEFAULT NULL,
  `id_cliente` bigint(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `total` decimal(24,6) DEFAULT '0.000000',
  `abonos` decimal(24,6) DEFAULT '0.000000',
  `saldo` decimal(24,6) DEFAULT '0.000000',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cxc`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `cxc_abonos` */

DROP TABLE IF EXISTS `cxc_abonos`;

CREATE TABLE `cxc_abonos` (
  `id_cxc_abono` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_cxc` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `concepto` varchar(100) DEFAULT NULL,
  `observacion` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_cxc_abono`,`id_cxc`,`id_serie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `inventarios` */

DROP TABLE IF EXISTS `inventarios`;

CREATE TABLE `inventarios` (
  `id_inventario` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie_inventario` varchar(20) DEFAULT NULL,
  `folio_inventario` int(11) DEFAULT NULL,
  `fecha_inventario` datetime DEFAULT NULL,
  `concepto_inventario` varchar(200) DEFAULT NULL,
  `aplicado` tinyint(1) DEFAULT '0',
  `fecha_aplica` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_inventario`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `inventarios_detalles` */

DROP TABLE IF EXISTS `inventarios_detalles`;

CREATE TABLE `inventarios_detalles` (
  `id_inventario_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_inventario` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `stock` decimal(24,6) DEFAULT '0.000000',
  `conteo` decimal(24,6) DEFAULT '0.000000',
  `diferencia` decimal(24,6) DEFAULT '0.000000',
  PRIMARY KEY (`id_inventario_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `movimientos_almacen` */

DROP TABLE IF EXISTS `movimientos_almacen`;

CREATE TABLE `movimientos_almacen` (
  `id_movimiento` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_tipomovimiento` bigint(20) DEFAULT NULL,
  `id_almacen_origen` bigint(20) DEFAULT NULL,
  `id_almacen_destino` bigint(20) DEFAULT NULL,
  `id_serie` bigint(20) DEFAULT NULL,
  `id_agente` bigint(20) DEFAULT NULL,
  `id_inventario` bigint(20) DEFAULT NULL,
  `serie_movimiento` varchar(20) DEFAULT NULL,
  `folio_movimiento` int(11) DEFAULT NULL,
  `fecha_movimiento` datetime DEFAULT NULL,
  `concepto_movimiento` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_movimiento`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `movimientos_almacen_detalles` */

DROP TABLE IF EXISTS `movimientos_almacen_detalles`;

CREATE TABLE `movimientos_almacen_detalles` (
  `id_movimiento_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_movimiento` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `cantidad` decimal(24,6) DEFAULT NULL,
  `costo` decimal(24,6) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `movimientos_bancos` */

DROP TABLE IF EXISTS `movimientos_bancos`;

CREATE TABLE `movimientos_bancos` (
  `id_movimiento_banco` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `id_concepto` bigint(20) NOT NULL,
  `id_chequera` bigint(20) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `observaciones` varchar(200) DEFAULT NULL,
  `tipo_movimiento` tinyint(1) DEFAULT NULL COMMENT '1=Ingreso,2=egreso',
  `tipo_origen` tinyint(4) DEFAULT NULL COMMENT '1=Efectivo,2=Bancos',
  `importe` decimal(18,6) DEFAULT NULL,
  `origen` tinyint(1) DEFAULT '1' COMMENT '1=Movimientos Bancos,2=Gastos',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_banco`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `movimientos_caja` */

DROP TABLE IF EXISTS `movimientos_caja`;

CREATE TABLE `movimientos_caja` (
  `id_movimiento_caja` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_turno` bigint(20) NOT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `tipo` tinyint(1) DEFAULT NULL COMMENT '1=Deposito,2=Retiro',
  `total` decimal(18,2) DEFAULT '0.00',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_movimiento_caja`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `remisiones` */

DROP TABLE IF EXISTS `remisiones`;

CREATE TABLE `remisiones` (
  `id_remision` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) NOT NULL,
  `id_cliente` bigint(20) NOT NULL,
  `id_agente` bigint(20) NOT NULL,
  `id_serie` bigint(20) NOT NULL,
  `serie` varchar(20) DEFAULT NULL,
  `folio` int(11) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL,
  `condicion_pago` tinyint(1) DEFAULT NULL COMMENT '1=Contado, 2=Credito',
  `concepto` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `comision` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  `aplicado` tinyint(1) DEFAULT '0',
  `fecha_aplica` datetime DEFAULT NULL,
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  PRIMARY KEY (`id_remision`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `remisiones_detalles` */

DROP TABLE IF EXISTS `remisiones_detalles`;

CREATE TABLE `remisiones_detalles` (
  `id_remision_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_remision` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `cantidad` decimal(24,6) DEFAULT NULL,
  `costo` decimal(24,6) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_remision_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `turnos` */

DROP TABLE IF EXISTS `turnos`;

CREATE TABLE `turnos` (
  `id_turno` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_caja` bigint(20) DEFAULT NULL,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_corte` bigint(20) DEFAULT NULL,
  `consecutivo` int(11) DEFAULT NULL,
  `concepto` varchar(200) DEFAULT NULL,
  `fechainicio` datetime NOT NULL,
  `fechafin` datetime DEFAULT NULL,
  `total_turno` decimal(18,2) DEFAULT '0.00',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `usercierre` bigint(20) DEFAULT NULL,
  `fechacierre` datetime DEFAULT NULL,
  PRIMARY KEY (`id_turno`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `turnos_detalles` */

DROP TABLE IF EXISTS `turnos_detalles`;

CREATE TABLE `turnos_detalles` (
  `id_turno_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_turno` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `id_denominacion` bigint(20) NOT NULL,
  `cantidad` decimal(18,2) NOT NULL DEFAULT '0.00',
  `total` decimal(18,2) NOT NULL DEFAULT '0.00',
  PRIMARY KEY (`id_turno_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `ventas` */

DROP TABLE IF EXISTS `ventas`;

CREATE TABLE `ventas` (
  `id_venta` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_empresa` bigint(20) NOT NULL,
  `id_sucursal` bigint(20) NOT NULL,
  `id_almacen` bigint(20) DEFAULT NULL,
  `id_serie` bigint(20) DEFAULT NULL,
  `id_cliente` bigint(20) DEFAULT NULL,
  `serie_venta` varchar(20) DEFAULT NULL,
  `folio_venta` int(11) DEFAULT NULL,
  `fecha_venta` datetime DEFAULT NULL,
  `concepto_venta` varchar(100) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  `pago` decimal(24,6) DEFAULT '0.000000',
  `cambio` decimal(24,6) DEFAULT '0.000000',
  `status` char(1) DEFAULT NULL,
  `usercreador` bigint(20) DEFAULT NULL,
  `fechacreador` datetime DEFAULT NULL,
  `usermodif` bigint(20) DEFAULT NULL,
  `fechamodif` datetime DEFAULT NULL,
  `id_turno` bigint(20) DEFAULT NULL,
  `id_agente` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`id_venta`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `ventas_detalles` */

DROP TABLE IF EXISTS `ventas_detalles`;

CREATE TABLE `ventas_detalles` (
  `id_venta_detalle` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(20) NOT NULL,
  `id_producto` bigint(20) NOT NULL,
  `cantidad` decimal(24,6) DEFAULT NULL,
  `precio` decimal(24,6) DEFAULT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  `descuento` decimal(24,6) DEFAULT NULL,
  `subtotal` decimal(24,6) DEFAULT NULL,
  `impuestos` decimal(24,6) DEFAULT NULL,
  `total` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_venta_detalle`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Table structure for table `ventas_formaspagos` */

DROP TABLE IF EXISTS `ventas_formaspagos`;

CREATE TABLE `ventas_formaspagos` (
  `id_venta_formapago` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_venta` bigint(20) NOT NULL,
  `id_formapago` bigint(20) NOT NULL,
  `importe` decimal(24,6) DEFAULT NULL,
  PRIMARY KEY (`id_venta_formapago`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/* Procedure structure for procedure `get_menus_del_usuario` */

/*!50003 DROP PROCEDURE IF EXISTS  `get_menus_del_usuario` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `get_menus_del_usuario`(V_User INTEGER(11) , V_nodo INTEGER(11),V_todos  BOOLEAN)
BEGIN
IF V_todos=FALSE THEN
	/*
	SELECT id_modulo as id, descripcion as text,newWin,newTab,icono,
            if ((select count(id_modulo) as numero from cat_modulos where id_padre=id)=0, true, '') as leaf,concat(concat('images/iconos/',icono),'.png') as icon,icono 
            FROM
            cat_usuarios_privilegios priv
            LEFT JOIN cat_modulos mods ON priv.Origen='MOD' AND priv.KEYId=mods.IDMod
            WHERE KEYUsuPriv=V_User AND KEYPadMod=V_nodo ORDER by orden;  
            */  
            SELECT id_modulo AS id, descripcion AS TEXT, newWin, newTab,CONCAT(CONCAT('images/iconos/',icono),'.png') AS icon,icono AS iconMaster,
                IF ((SELECT COUNT(id_modulo) AS numero FROM cat_modulos WHERE id_padre=id)=0, TRUE, '') AS leaf,icono 
                FROM cat_usuarios_privilegios priv
                LEFT JOIN cat_modulos mods ON priv.tipo_privilegio=4 AND priv.id_privilegio=mods.id_modulo
                WHERE id_usuario=V_User AND id_padre=V_nodo ORDER BY orden;
ELSE
	SELECT id_modulo AS id, descripcion AS TEXT, newWin, newTab,CONCAT(CONCAT('images/iconos/',icono),'.png') AS icon,icono AS iconMaster,
                IF ((SELECT COUNT(id_modulo) AS numero FROM cat_modulos WHERE id_padre=id)=0, TRUE, '') AS leaf,icono FROM cat_modulos
                WHERE id_padre=V_nodo ORDER BY orden;
END IF;
END */$$
DELIMITER ;

/* Procedure structure for procedure `loginGetEmpresas` */

/*!50003 DROP PROCEDURE IF EXISTS  `loginGetEmpresas` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `loginGetEmpresas`(V_usuario INTEGER(11),v_esAdmin BOOLEAN)
BEGIN
IF v_esAdmin=FALSE THEN
SELECT e.id_empresa,e.nombre_fiscal,e.maneja_inventario
FROM cat_usuarios_privilegios up
INNER JOIN cat_empresas  e ON (up.id_privilegio = e.id_empresa AND up.tipo_privilegio = 1)
WHERE up.id_usuario = V_usuario AND up.tipo_privilegio = 1
AND e.STATUS='A'
ORDER BY e.id_empresa;
ELSE
	SELECT id_empresa,nombre_fiscal,maneja_inventario
	FROM cat_empresas;
	
END IF;
END */$$
DELIMITER ;

/* Procedure structure for procedure `loginGetSucursales` */

/*!50003 DROP PROCEDURE IF EXISTS  `loginGetSucursales` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `loginGetSucursales`(V_usuario INTEGER(11),v_esAdmin BOOLEAN,V_id_empresa INTEGER(11))
BEGIN
IF v_esAdmin=FALSE THEN
SELECT s.id_sucursal,s.nombre_sucursal
FROM cat_usuarios_privilegios up
INNER JOIN cat_sucursales  s ON (up.id_privilegio =s.id_sucursal AND up.tipo_privilegio = 2)
inner join cat_empresas e ON (e.id_empresa = s.id_empresa)
WHERE up.id_usuario = V_usuario AND up.tipo_privilegio = 2 and s.id_empresa = V_id_empresa
AND s.STATUS='A'
ORDER BY s.id_sucursal;
ELSE
	SELECT id_sucursal,nombre_sucursal
	fROM cat_sucursales s
	INNER JOIN catempresas e on (e.id_empresa = s.id_empresa)
	where s.id_empresa = V_id_empresa;
END IF;
END */$$
DELIMITER ;

/* Procedure structure for procedure `loginGetUserId` */

/*!50003 DROP PROCEDURE IF EXISTS  `loginGetUserId` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `loginGetUserId`(V_User VARCHAR(80))
BEGIN
		SELECT id_usuario IDUsu,esadmin AdminUsu FROM cat_usuarios WHERE usuario=V_User;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spConsultaTodasEmpresas` */

/*!50003 DROP PROCEDURE IF EXISTS  `spConsultaTodasEmpresas` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spConsultaTodasEmpresas`()
BEGIN
		SELECT id_empresa,nombre_fiscal,maneja_inventario
		FROM cat_empresas ORDER BY id_empresa;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spConsultaTodasEmpresasSucursales` */

/*!50003 DROP PROCEDURE IF EXISTS  `spConsultaTodasEmpresasSucursales` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spConsultaTodasEmpresasSucursales`()
BEGIN
		DROP TABLE IF EXISTS todas_empresas_tmp;
		
		CREATE TEMPORARY TABLE todas_empresas_tmp(
			Origen VARCHAR(10),
			CFDiEMP TINYINT(1),
			IDConcat VARCHAR(10),
			Nombre VARCHAR(250),
			IDEmpresa INT(11),
			ComEmp VARCHAR(250),
			IDSucursal INT(11),
			NombreSucursal VARCHAR(250),
			bandera TINYINT(1),
			RFCEmp VARCHAR(15)			
		);
		INSERT INTO todas_empresas_tmp SELECT 'EMP' AS Origen,1,
			CONCAT('EMP-', e.id_empresa) AS IDConcat,
			UPPER(e.nombre_comercial) AS Nombre, 
			e.id_empresa AS IDEmpresa, 
			'MATRIZ' AS ComEmp, 	
			0 AS IDSucursal, 
			IF(ISNULL(s.id_sucursal), 0, 'MATRIZ') AS NombreSucursal,			
			IF(ISNULL(e.id_empresa), 0, 1) AS bandera,
			rfc RFCEmp
		FROM cat_empresas e 
		LEFT JOIN cat_sucursales s ON s.id_empresa=e.id_empresa 
		GROUP BY e.id_empresa HAVING bandera!=0;
		
		INSERT INTO todas_empresas_tmp SELECT IF(ISNULL(id_sucursal),'EMP','SUC') AS Origen,1 as CFDiEmp,
			IF(ISNULL(id_sucursal),CONCAT('EMP-', s.id_empresa),CONCAT('SUC-', id_sucursal)) AS IDConcat,
			IF(ISNULL(id_sucursal), UPPER(nombre_comercial), UPPER(nombre_sucursal)) AS Nombre, 
			s.id_empresa AS IDEmpresa, 
			IF(ISNULL(id_sucursal), 'MATRIZ', (SELECT UPPER(nombre_comercial) FROM cat_empresas e2 WHERE e2.id_empresa = s.id_empresa)) AS ComEmp, 	
			IF(ISNULL(id_sucursal), 0, id_sucursal) AS IDSucursal, 
			IF(ISNULL(id_sucursal), 'MATRIZ', UPPER(nombre_sucursal)) AS NombreSucursal,
			0 AS bandera,
			rfc RFCEmp
		FROM cat_empresas e 
		LEFT JOIN cat_sucursales s ON s.id_empresa=e.id_empresa;
    
		
    
    
		SELECT Origen,CFDiEmp,IDConcat,Nombre ,IDEmpresa,ComEmp,IDSucursal,NombreSucursal,RFCEmp
		FROM todas_empresas_tmp ORDER BY IDEmpresa,IDSucursal;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spConsultaTodasSucursales` */

/*!50003 DROP PROCEDURE IF EXISTS  `spConsultaTodasSucursales` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spConsultaTodasSucursales`(V_id_empresa INTEGER(11))
BEGIN
		SELECT id_sucursal,nombre_sucursal
		FROM cat_sucursales 
		WHERE id_empresa = V_id_empresa		
		ORDER BY id_sucursal;
    END */$$
DELIMITER ;

/* Procedure structure for procedure `spMaximosMinimosProductos` */

/*!50003 DROP PROCEDURE IF EXISTS  `spMaximosMinimosProductos` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spMaximosMinimosProductos`(V_id_almacen BIGINT,V_id_linea BIGINT,V_id_producto BIGINT)
BEGIN
		
	DROP TEMPORARY TABLE IF EXISTS TempProductos;
	
	CREATE TEMPORARY TABLE TempProductos( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT, 
		id_linea BIGINT,
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock DECIMAL(24,6)
	);
	
	
	/*
	INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
	SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
	FROM cat_productos pp 
	LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
	where pp.tipo_producto = 'P';
	*/
	
	IF V_id_producto > 0 THEN
		INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
		SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
		FROM cat_productos pp 
		LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
		WHERE pp.tipo_producto = 'P' and pp.id_producto = V_id_producto;
	END if;
	
	
	IF V_id_linea > 0 and V_id_producto = 0 THEN
		INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
		SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
		FROM cat_productos pp 
		LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
		WHERE pp.tipo_producto = 'P' AND pp.id_linea = V_id_linea;
	END if;
	
	IF V_id_linea = 0 AND V_id_producto = 0 THEN
		INSERT INTO TempProductos(id_producto ,id_almacen,id_linea,stock_min,stock_max,stock)	
		SELECT pp.id_producto,s.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) 
		FROM cat_productos pp 
		LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND s.id_almacen = V_id_almacen
		WHERE pp.tipo_producto = 'P';
		
	end if;
	
	
	
	SELECT ps.id_producto ,p.codigo,p.descripcion,V_id_almacen AS id_almacen,ps.id_linea,ps.stock_min,ps.stock_max,ps.stock,0 modificado FROM TempProductos ps
	INNER JOIN cat_productos p ON p.id_producto = ps.id_producto;
	
	
END */$$
DELIMITER ;

/* Procedure structure for procedure `spPedidoSugerido` */

/*!50003 DROP PROCEDURE IF EXISTS  `spPedidoSugerido` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spPedidoSugerido`(V_id_sucursal_origen BIGINT,V_id_sucursal BIGINT,V_FechaInicio DATETIME, V_FechaFin DATETIME, V_id_linea BIGINT,V_id_producto BIGINT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS TempVentas;
	
	CREATE TEMPORARY TABLE TempVentas( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,
		id_linea BIGINT,
		cantidad DECIMAL(24,6)
		
	); 	
	
	DROP TEMPORARY TABLE IF EXISTS TempProductos;
	
	CREATE TEMPORARY TABLE TempProductos( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT, 
		id_linea BIGINT,
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock DECIMAL(24,6)
	);
	
	DROP TEMPORARY TABLE IF EXISTS Resultado;
	
	CREATE TEMPORARY TABLE Resultado( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,
		id_linea BIGINT,
		/*nombre_sucursal VARCHAR(255),
		descripcion VARCHAR(255),
		codigo_barras VARCHAR(255),
		codigo VARCHAR(255),
		cantidad DECIMAL(24,6),*/
		/*nombre_linea VARCHAR(255),*/
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock_min_nvo DECIMAL(24,6),
		stock_max_nvo DECIMAL(24,6),
		stock DECIMAL(24,6),
		ventas DECIMAL(24,6),
		pedido_sugerido DECIMAL(24,6)
	);
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen,p.id_linea,SUM(vd.cantidad)
	FROM ventas_detalles vd
	INNER JOIN ventas v ON v.id_venta = vd.id_venta
	INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
	WHERE v.fecha_venta BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen;	
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen_origen,p.id_linea,SUM(vd.cantidad)
	FROM movimientos_almacen_detalles vd
	INNER JOIN movimientos_almacen v ON v.id_movimiento = vd.id_movimiento
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	INNER JOIN cat_tiposmovimientos tm ON tm.id_tipomovimiento = v.id_tipomovimiento
	WHERE v.fecha_movimiento BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A' AND tm.tipo_movimiento = 4	
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen_origen;
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen,p.id_linea,SUM(vd.cantidad)
	FROM remisiones_detalles vd
	INNER JOIN remisiones v ON v.id_remision = vd.id_remision
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	WHERE v.fecha BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen;	
	
	/*IF V_id_empresa > 0 THEN
		DELETE FROM TempVentas WHERE id_empresa <> V_id_empresa;
	END IF;*/
		
	IF V_id_sucursal > 0 THEN
		DELETE FROM TempVentas WHERE id_sucursal <> V_id_sucursal;
	END IF;
	
	IF V_id_linea > 0 THEN
		DELETE FROM TempVentas WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempVentas WHERE id_producto <> V_id_producto;
	END IF;
	
	IF V_id_sucursal_origen = 2 then 
		DELETE FROM TempVentas WHERE id_linea in (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	end if;
	
	IF V_id_sucursal_origen = 3 THEN 
		DELETE FROM TempVentas WHERE id_linea not IN (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	END IF;
	
	
	INSERT INTO TempProductos(id_producto ,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock)	
	SELECT pp.id_producto,pp.id_empresa,pp.id_sucursal,pp.id_almacen,pp.id_linea,IFNULL(s.stock_min,0),IFNULL(s.stock_max,0),IFNULL(s.stock,0) FROM(
	SELECT p.id_producto,a.id_empresa,a.id_sucursal,a.id_almacen,p.id_linea
	FROM cat_productos p, cat_almacenes a) pp 
	LEFT JOIN cat_productos_stocks s ON s.id_producto = pp.id_producto AND pp.id_almacen = s.id_almacen;
	
	/*IF V_id_empresa > 0 THEN
		DELETE FROM TempProductos WHERE id_empresa <> V_id_empresa;
	END IF;*/
		
	IF V_id_sucursal > 0 THEN
		DELETE FROM TempProductos WHERE id_sucursal <> V_id_sucursal;
	END IF;
	
	IF V_id_linea > 0 THEN
		DELETE FROM TempProductos WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempProductos WHERE id_producto <> V_id_producto;
	END IF;
	
	
	IF V_id_sucursal_origen = 2 THEN 
		DELETE FROM TempProductos WHERE id_linea IN (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	END IF;
	
	IF V_id_sucursal_origen = 3 THEN 
		DELETE FROM TempProductos WHERE id_linea NOT IN (51/*ADARA*/,163/*ALBELLA*/,45/*APPLE*/,30/*ARI*/,50/*BISSU*/,161/*CANAY COSMETICOS*/,
		150/*COLORE*/,146/*COLORI*/,164/*COLORTONE*/,29/*CRISTALEX*/,44/*CURTIS*/,145/*DE LA FUENTE*/,14/*DEVIBELL*/,
		120/*DISAVA*/,63/*DUO*/,26/*EROS NAILS*/,20/*ESTEFANIA*/,114/*G&K*/,59/*IMPORTACIONES GUADALAJARA*/,
		88/*INTMEX*/,37/*JAVIER MINA*/,40/*JDENIS*/,46/*JLASH*/,72/*JORDANA*/,52/*JULIA SANCHEZ*/,167/*L.A. COLORS*/,
		53/*LAKREEM*/,76/*MAXHI*/,95/*NAIL PRO*/,165/*NIN ACCESORIOS*/,47/*PACHS*/,
		112/*NIYA NAIL*/,136/*NUNN CARE*/,9/*ORGANIC NAIL*/,28/*OUTLET NAILS*/,60/*PERFECT NAIL*/,93/*PINK UP*/,153/*SANTILLAN*/,
		122/*SECRET COSMETICS*/,158/*STAR ONE*/,42 /*Caroline*/, 31 /*globusman*/);
	END IF;	
	
	INSERT INTO Resultado(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock,ventas)
	SELECT t1.id_producto,t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.id_linea,MAX(stock_min),MAX(stock_max),MAX(stock),SUM(t1.cantidad)
	FROM(	
		SELECT p.id_producto,p.id_empresa,p.id_sucursal,p.id_almacen,p.id_linea,stock_min,stock_max,stock,IFNULL(cantidad,0) AS cantidad FROM TempProductos p
		LEFT JOIN TempVentas v ON v.id_producto = p.id_producto AND v.id_empresa = p.id_empresa AND v.id_sucursal = p.id_sucursal
		AND v.id_almacen = p.id_almacen
	) t1		
	GROUP BY t1.id_producto,t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.id_linea;	
	
	
	UPDATE Resultado SET stock_min_nvo = ventas WHERE ventas > 0;
	
	UPDATE Resultado SET stock_max_nvo = ventas * 2 WHERE ventas > 0;
	
	UPDATE Resultado SET pedido_sugerido = stock_max - stock
	WHERE stock >= 0 AND stock <= stock_min;
		
	DELETE FROM Resultado WHERE pedido_sugerido < 1;
	
	/*DELETE FROM Resultado WHERE stock <= 0;*/
		
	SELECT t1.id_producto,t1.nombre_fiscal,t1.nombre_sucursal,t1.nombre_almacen,t1.descripcion,t1.codigo_barras,t1.codigo,t1.nombre_linea,MAX(t1.stock_min) AS stock_min,MAX(t1.stock_max) AS stock_max,MAX(t1.stock_min_nvo) AS stock_min_nvo,MAX(t1.stock_max_nvo) AS stock_max_nvo,MAX(t1.stock) AS stock,SUM(t1.ventas) AS ventas,MAX(t1.pedido_sugerido) AS pedido_sugerido
	FROM (	
	SELECT r.id_producto,r.id_empresa,r.id_sucursal,r.id_almacen,r.id_linea,e.nombre_fiscal,su.nombre_sucursal,al.nombre_almacen,p.descripcion,p.codigo_barras,p.codigo,l.nombre_linea,IFNULL(r.stock_min,0) AS stock_min,IFNULL(r.stock_max,0) AS stock_max,IFNULL(r.stock_min_nvo,0) AS stock_min_nvo,IFNULL(r.stock_max_nvo,0) AS stock_max_nvo,IFNULL(r.stock,0) AS stock,IFNULL(r.ventas,0) AS ventas,IFNULL(r.pedido_sugerido,0) AS pedido_sugerido FROM Resultado r
	INNER JOIN cat_productos p ON p.id_producto = r.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = r.id_linea
	INNER JOIN cat_empresas e ON e.id_empresa = r.id_empresa
	INNER JOIN cat_sucursales su ON su.id_sucursal = r.id_sucursal
	INNER JOIN cat_almacenes al ON al.id_almacen = r.id_almacen
	) t1
	Where t1.pedido_sugerido > 0
	GROUP BY t1.id_producto,t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.id_linea,t1.nombre_fiscal,t1.nombre_sucursal,t1.nombre_almacen,t1.descripcion,t1.codigo_barras,t1.codigo,t1.nombre_linea
	ORDER BY t1.id_empresa,t1.id_sucursal,t1.id_almacen,t1.codigo;
	
	
	
END */$$
DELIMITER ;

/* Procedure structure for procedure `spReporteMovimientosBancos` */

/*!50003 DROP PROCEDURE IF EXISTS  `spReporteMovimientosBancos` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spReporteMovimientosBancos`(V_id_empresa BIGINT,V_id_sucursal BIGINT,V_FechaInicio DATETIME, V_FechaFin DATETIME, V_id_concepto BIGINT,V_id_producto BIGINT)
BEGIN
	Declare  V_Ingresos DECIMAL(24,6);
	DECLARE V_Egresos DECIMAL(24,6);
	DECLARE V_SaldoAnterior DECIMAL(24,6);
	
	DROP TEMPORARY TABLE IF EXISTS Movimientos;
	
	CREATE TEMPORARY TABLE Movimientos( 
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_concepto BIGINT,
		id_chequera BIGINT,
		fecha DATETIME,
		serie VARCHAR(20),
		folio int(20),
		observaciones VARCHAR(200),
		tipo_movimiento TINYINT,
		tipo_origen TINYINT,
		importe DECIMAL(24,6)
		
	);
	
	DROP TEMPORARY TABLE IF EXISTS Resultado;
	
	CREATE TEMPORARY TABLE Resultado( 
		id_movimiento_banco BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_concepto BIGINT,
		id_chequera BIGINT,
		fecha datetime,
		serie VARCHAR(20),
		folio INT(20),
		observaciones VARCHAR(200),
		nombre_concepto VARCHAR(255),
		tipo_movimiento tinyint,
		tipo_origen TINYINT,
		saldoanterior DECIMAL(24,6),
		ingresos DECIMAL(24,6),
		egresos DECIMAL(24,6),
		saldo DECIMAL(24,6),
		tipo TINYINT
	);
	
	
	/*
	IF V_id_producto > 0 THEN
		DELETE FROM TempProductos WHERE id_producto <> V_id_producto;
	END IF;
	*/
	
	insert into Movimientos(serie,folio,id_empresa,id_sucursal,id_concepto,id_chequera,fecha,observaciones,tipo_movimiento,tipo_origen,importe)
	Select m.serie,m.folio,m.id_empresa,m.id_sucursal,m.id_concepto,m.id_chequera,m.fecha,m.observaciones,m.tipo_movimiento,m.tipo_origen,m.importe
	FROM movimientos_bancos	m	
	WHERE m.fecha BETWEEN V_FechaInicio AND V_FechaFin AND m.status = 'A';
	
	IF V_id_sucursal > 0 THEN
		DELETE FROM Movimientos WHERE id_sucursal <> V_id_sucursal;
	END IF;	
	
	IF V_id_concepto > 0 THEN
		DELETE FROM Movimientos WHERE id_concepto <> V_id_concepto;
	END IF;	
	
	set V_Ingresos = (
	SELECT IFNULL(SUM(importe),0)
	FROM movimientos_bancos WHERE tipo_movimiento = 1 AND fecha < V_FechaInicio);
	
	SET V_Egresos = (
	SELECT ifnull(SUM(importe),0)
	FROM movimientos_bancos WHERE tipo_movimiento = 2 AND fecha < V_FechaInicio);
	
	set V_SaldoAnterior = V_Ingresos - V_Egresos;
	
	insert into Resultado(fecha,nombre_concepto,saldoanterior,ingresos,egresos,saldo,tipo)
	values (V_FechaInicio,'SALDO ANTERIOR',V_SaldoAnterior,0,0,V_SaldoAnterior,1);
		
	INSERT INTO Resultado(serie,folio,id_empresa,id_sucursal,id_concepto,nombre_concepto,id_chequera,fecha,observaciones,tipo_movimiento,tipo_origen,saldoanterior,ingresos,egresos,saldo,tipo)
	SELECT serie,folio,id_empresa,id_sucursal,m.id_concepto,c.descripcion,id_chequera,fecha,observaciones,tipo_movimiento,tipo_origen,V_SaldoAnterior,CASE tipo_movimiento WHEN 1 THEN importe ELSE 0 END AS ingresos,
	CASE tipo_movimiento WHEN 2 THEN importe ELSE 0 END AS egresos,0,2 
	FROM Movimientos m
	LEFT JOIN cat_conceptos c ON c.id_concepto = m.id_concepto
	GROUP BY id_empresa,id_sucursal,id_concepto,id_chequera,fecha,tipo_movimiento,tipo_origen
	ORDER BY fecha;	
	
	
	
	/*
	update usuario nu
	set nu.bicikilometros=(select sum(be.km)
	from biker_rutasusuario nra, biker_etapa be
	where be.codigo = nra.codigoruta and
	      nra.codigousuario = nu.codigo) 
	
*/
	
	Select DATE_FORMAT(r.fecha,'%d/%m/%Y') as fecha,nombre_concepto,ifnull(r.observaciones,'') as observaciones,IFNULL(s.nombre_sucursal,'') as nombre_sucursal,ifnull(ch.descripcion,'') as nombre_chequera,case r.tipo_origen when 1 then 'EFECTIVO' WHEN 2 THEN 'BANCOS' else '' END AS tipo_origen,r.saldoanterior,r.ingresos,r.egresos,r.saldo,r.tipo 
	FROM Resultado r		
	LEFT join cat_sucursales s on s.id_sucursal = r.id_sucursal
	left join cat_chequeras ch on ch.id_chequera = r.id_chequera	
	ORDER BY r.fecha ASC;	
	
	
	
	
END */$$
DELIMITER ;

/* Procedure structure for procedure `spReporteVentas` */

/*!50003 DROP PROCEDURE IF EXISTS  `spReporteVentas` */;

DELIMITER $$

/*!50003 CREATE DEFINER=`mierp`@`%` PROCEDURE `spReporteVentas`(V_id_empresa BIGINT,V_id_sucursal BIGINT,V_FechaInicio DATETIME, V_FechaFin DATETIME, V_id_linea BIGINT,V_id_producto BIGINT, V_Agrupado TINYINT)
BEGIN
	DROP TEMPORARY TABLE IF EXISTS TempVentas;
	
	CREATE TEMPORARY TABLE TempVentas( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_linea BIGINT,
		nombre_sucursal VARCHAR(255),
		descripcion VARCHAR(255),
		codigo_barras VARCHAR(255),
		codigo VARCHAR(255),
		cantidad DECIMAL(24,6),
		nombre_linea VARCHAR(255),
		stock DECIMAL(24,6),
		precio DECIMAL(24,6),
		subtotal DECIMAL(24,6)
	); 	
	DROP TEMPORARY TABLE IF EXISTS TempResultado;	
	
	CREATE TEMPORARY TABLE TempResultado( 
		id_producto BIGINT,
		id_linea BIGINT,
		nombre_sucursal VARCHAR(255),
		descripcion VARCHAR(255),
		codigo_barras VARCHAR(255),
		codigo VARCHAR(255),
		ventas DECIMAL(24,6),
		nombre_linea VARCHAR(255),
		stock DECIMAL(24,6),
		precio DECIMAL(24,6),
		subtotal DECIMAL(24,6)
	); 	
	
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,p.id_linea,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS 
	stock, MAX(vd.precio) AS precio, SUM(vd.total) AS subtotal
	FROM ventas_detalles vd
	INNER JOIN ventas v ON v.id_venta = vd.id_venta
	INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
	INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
	LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.id_almacen
	WHERE v.fecha_venta BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_sucursal,v.id_almacen,su.nombre_sucursal;
	/*
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,p.id_linea,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS stock,
	MAX(vd.costo) AS precio, SUM(vd.total) AS subtotal
	FROM movimientos_almacen_detalles vd
	INNER JOIN movimientos_almacen v ON v.id_movimiento = vd.id_movimiento
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
	INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
	INNER JOIN cat_tiposmovimientos tm ON tm.id_tipomovimiento = v.id_tipomovimiento
	LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen_origen`
	WHERE v.fecha_movimiento BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A' AND tm.tipo_movimiento = 4
	GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_sucursal,v.id_almacen_origen,su.nombre_sucursal;
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,p.id_linea,su.nombre_sucursal,p.descripcion,p.codigo_barras,p.codigo,SUM(vd.cantidad) AS cantidad,l.nombre_linea,SUM(DISTINCT IFNULL(s.stock,0)) AS stock,
	MAX(vd.costo) AS precio, SUM(vd.total) AS subtotal
	FROM remisiones_detalles vd
	INNER JOIN remisiones v ON v.id_remision = vd.id_remision
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
	INNER JOIN cat_sucursales su ON su.id_sucursal = v.id_sucursal
	LEFT JOIN cat_productos_stocks s ON s.id_producto = vd.id_producto AND s.id_almacen = v.`id_almacen`
	WHERE v.fecha BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A'
	GROUP BY vd.id_producto,p.codigo,p.descripcion,v.id_sucursal,v.id_almacen,su.nombre_sucursal;			
	*/
	IF V_id_empresa > 0 THEN
		DELETE FROM TempVentas WHERE id_empresa <> V_id_empresa;
	END IF;
		
	IF V_id_sucursal > 0 THEN
		DELETE FROM TempVentas WHERE id_sucursal <> V_id_sucursal;
	END IF;
	
	IF V_id_linea > 0 THEN
		DELETE FROM TempVentas WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempVentas WHERE id_producto <> V_id_producto;
	END IF;
	
	INSERT INTO TempResultado(id_producto,id_linea,nombre_sucursal,descripcion,codigo_barras,codigo,ventas,nombre_linea,stock,precio,subtotal)
	SELECT t1.id_producto,t1.id_sucursal,t1.nombre_sucursal,t1.descripcion,t1.codigo_barras,t1.codigo,SUM(t1.cantidad) AS ventas,t1.nombre_linea,SUM(DISTINCT t1.stock) AS stock,MAX(t1.precio) AS precio, SUM(t1.subtotal) AS subtotal
	FROM  (
		SELECT id_producto,id_sucursal,nombre_sucursal,descripcion,codigo_barras,codigo,cantidad,nombre_linea,stock,precio,subtotal FROM TempVentas
		) t1
		GROUP BY t1.id_producto,t1.codigo,t1.descripcion,t1.id_sucursal,t1.nombre_sucursal     
		ORDER BY t1.descripcion,t1.nombre_sucursal;
	
	
		
	IF V_Agrupado = 1 THEN
		SET @PivotQuery = NULL;
		SELECT
		  GROUP_CONCAT( DISTINCT
		    CONCAT(
		      ' sum(IF(nombre_sucursal = ''',
		      nombre_sucursal,
		      ''', stock, 0)) AS ''',
		      t.nombre_sucursal,''''
		    )
		  ) INTO @PivotQuery
		FROM
		  (SELECT
		     nombre_sucursal
		   FROM     
		     TempResultado
		) t;
		SET @PivotQuery = CONCAT('SELECT t1.codigo as CODIGO,t1.descripcion as DESCRIPCION,t1.nombre_linea as NOMBRE_LINEA,t1.codigo_barras AS CODIGO_BARRAS,t1.codigo AS CODIGO,SUM(t1.ventas) as VENTAS,', @PivotQuery, 
			' FROM TempResultado t1 group by t1.descripcion,t1.codigo_barras,t1.codigo,t1.nombre_linea');
		PREPARE statement FROM @PivotQuery;
		EXECUTE statement;
		DEALLOCATE PREPARE statement;
	END IF;
	
	IF V_Agrupado = 0 THEN
		SELECT t1.id_producto,t1.nombre_sucursal,t1.descripcion,t1.codigo_barras,t1.codigo,t1.ventas,t1.nombre_linea,
		t1.stock, t1.precio, t1.subtotal 
		FROM TempResultado t1
		ORDER BY t1.descripcion,t1.nombre_sucursal;
	END IF;	
	
	
END */$$
DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
