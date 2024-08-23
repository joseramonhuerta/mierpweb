DELIMITER $$

USE `erp_lagranbelleza`$$

DROP PROCEDURE IF EXISTS `spPedidoSugerido`$$

CREATE DEFINER=`mierp`@`%` PROCEDURE `spPedidoSugerido`(V_id_sucursal_origen BIGINT,V_id_sucursal BIGINT,V_FechaInicio DATETIME, V_FechaFin DATETIME, V_id_linea BIGINT,V_id_producto BIGINT, V_productosTop INT, V_tipo INT)
BEGIN
		
	
	DECLARE V_id_almacen_origen BIGINT;
	
	SELECT id_almacen INTO V_id_almacen_origen  FROM cat_almacenes WHERE id_sucursal = V_id_sucursal_origen;
	
	DROP TEMPORARY TABLE IF EXISTS TempVentas;
	
	CREATE TEMPORARY TABLE TempVentas( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,
		id_linea BIGINT,
		cantidad DECIMAL(24,6)
		
	); 
	
	DROP TEMPORARY TABLE IF EXISTS TempVentasTotales;
	
	CREATE TEMPORARY TABLE TempVentasTotales( 
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
		stock DECIMAL(24,6),
		stock_origen DECIMAL(24,6)
	);
	
	DROP TEMPORARY TABLE IF EXISTS TempProductosAlmacenes;	
	
	CREATE TEMPORARY TABLE TempProductosAlmacenes( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT, 
		id_linea BIGINT
	);
	
	DROP TEMPORARY TABLE IF EXISTS Resultado;
	
	CREATE TEMPORARY TABLE Resultado( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,
		id_linea BIGINT,		
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock_min_nvo DECIMAL(24,6),
		stock_max_nvo DECIMAL(24,6),
		stock DECIMAL(24,6),
		ventas DECIMAL(24,6),
		pedido_sugerido DECIMAL(24,6),
		stock_origen DECIMAL(24,6)
	);
	
	DROP TEMPORARY TABLE IF EXISTS ResultadoFinal;
	
	CREATE TEMPORARY TABLE ResultadoFinal( 
		id_producto BIGINT,
		nombre_fiscal VARCHAR(255),
		nombre_sucursal VARCHAR(255),
		nombre_almacen VARCHAR(255),
		descripcion VARCHAR(255),
		codigo_barras VARCHAR(255),
		codigo VARCHAR(255),
		nombre_linea VARCHAR(255),
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),
		stock_min_nvo DECIMAL(24,6),
		stock_max_nvo DECIMAL(24,6),
		stock DECIMAL(24,6),
		ventas DECIMAL(24,6),
		pedido_sugerido DECIMAL(24,6),
		stock_origen DECIMAL(24,6)	
	);
	
	DROP TEMPORARY TABLE IF EXISTS TempProductosAlmacenesStocks;
	
	CREATE TEMPORARY TABLE TempProductosAlmacenesStocks( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,
		id_linea BIGINT,
		stock_min DECIMAL(24,6),
		stock_max DECIMAL(24,6),		
		stock DECIMAL(24,6)
	);	
	
	DROP TEMPORARY TABLE IF EXISTS TempProductosOrigen;
	
	CREATE TEMPORARY TABLE TempProductosOrigen( 
		id_producto BIGINT,
		id_empresa BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT, 
		id_linea BIGINT,		
		stock DECIMAL(24,6)
	);
	
	DROP TEMPORARY TABLE IF EXISTS TempProductosStocksAlmacenOrigen;
	
	CREATE TEMPORARY TABLE TempProductosStocksAlmacenOrigen( 
		id_producto BIGINT,
		id_sucursal BIGINT,
		id_almacen BIGINT,		
		stock DECIMAL(24,6)
	);	
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen,p.id_linea,SUM(vd.cantidad)
	FROM ventas_detalles vd
	INNER JOIN ventas v ON v.id_venta = vd.id_venta
	INNER JOIN cat_productos p ON p.id_producto = vd.id_producto
	WHERE v.fecha_venta BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A' AND v.id_sucursal = V_id_sucursal
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen;	
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen_origen,p.id_linea,SUM(vd.cantidad)
	FROM movimientos_almacen_detalles vd
	INNER JOIN movimientos_almacen v ON v.id_movimiento = vd.id_movimiento
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	INNER JOIN cat_tiposmovimientos tm ON tm.id_tipomovimiento = v.id_tipomovimiento
	WHERE v.fecha_movimiento BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A' AND tm.tipo_movimiento = 4 AND v.id_sucursal = V_id_sucursal	
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen_origen;
	
	INSERT INTO TempVentas(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)		
	SELECT vd.id_producto,v.id_empresa,v.id_sucursal,v.id_almacen,p.id_linea,SUM(vd.cantidad)
	FROM remisiones_detalles vd
	INNER JOIN remisiones v ON v.id_remision = vd.id_remision
	INNER JOIN cat_productos p ON p.`id_producto` = vd.id_producto
	WHERE v.fecha BETWEEN V_FechaInicio AND V_FechaFin AND v.status = 'A' AND v.id_sucursal = V_id_sucursal
	GROUP BY vd.id_producto,v.id_sucursal,v.id_almacen;				
	
		
	IF V_id_linea > 0 THEN
		DELETE FROM TempVentas WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempVentas WHERE id_producto <> V_id_producto;
	END IF;
	
	INSERT INTO TempVentasTotales(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,cantidad)
	SELECT id_producto,id_empresa,id_sucursal,id_almacen,id_linea,SUM(cantidad) FROM TempVentas
	GROUP BY id_producto,id_empresa,id_sucursal,id_almacen,id_linea;
	
	
	INSERT INTO TempProductos(id_producto ,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock)	
	SELECT s.id_producto ,a.id_empresa,a.id_sucursal,s.id_almacen,p.id_linea,s.stock_min,s.stock_max,s.stock
	FROM cat_productos_stocks s
	INNER JOIN cat_productos p ON p.id_producto = s.id_producto
	INNER JOIN cat_almacenes a ON a.id_almacen = s.id_almacen
	INNER JOIN cat_lineas l ON l.id_linea = p.id_linea
	WHERE a.id_sucursal = V_id_sucursal AND p.status = 'A';
	
	IF V_id_linea > 0 THEN
		DELETE FROM TempProductos WHERE id_linea <> V_id_linea;
	END IF;
	
	IF V_id_producto > 0 THEN
		DELETE FROM TempProductos WHERE id_producto <> V_id_producto;
	END IF;
	
	
	INSERT INTO TempProductosAlmacenesStocks(id_producto ,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock)	
	SELECT s.id_producto ,a.id_empresa,a.id_sucursal,s.id_almacen,p.id_linea,s.stock_min,s.stock_max,s.stock
	FROM cat_productos_stocks s
	INNER JOIN TempProductos p ON p.id_producto = s.id_producto
	INNER JOIN cat_almacenes a ON a.id_almacen = s.id_almacen	
	WHERE a.id_sucursal = V_id_sucursal_origen;
	
	DELETE FROM TempProductosAlmacenesStocks WHERE stock < 0;
	
	
	/*
	update TempProductos t1 
	set t1.stock_origen = (
		Select stock from TempProductosAlmacenesStocks t2 where t2.id_producto = t1.id_producto and t2.id_sucursal = V_id_sucursal_origen
	)
	where t1.id_sucursal = V_id_sucursal;	
	*/
		
	/*select * from TempProductos;*/
	/*SELECT * FROM TempProductosAlmacenesStocks;*/		
	
	IF V_tipo = 1 THEN
		INSERT INTO Resultado(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock,ventas,stock_origen)
		SELECT p.id_producto, p.id_empresa, p.id_sucursal, p.id_almacen,p.id_linea,IFNULL(p.stock_min,0),IFNULL(p.stock_max,0),IFNULL(p.stock,0),IFNULL(v.cantidad,0),IFNULL(s.stock,0)
		FROM TempProductos p
		INNER JOIN TempProductosAlmacenesStocks s ON s.id_producto = p.id_producto
		LEFT JOIN TempVentasTotales v ON v.id_producto = p.id_producto;
		
		DELETE FROM Resultado WHERE ventas = 0;
	
	ELSE
		INSERT INTO Resultado(id_producto,id_empresa,id_sucursal,id_almacen,id_linea,stock_min,stock_max,stock,ventas,stock_origen)
		SELECT p.id_producto, p.id_empresa, p.id_sucursal, p.id_almacen,p.id_linea,IFNULL(p.stock_min,0),IFNULL(p.stock_max,0),IFNULL(p.stock,0),IFNULL(v.cantidad,0),IFNULL(s.stock,0)
		FROM TempProductos p
		LEFT JOIN TempProductosAlmacenesStocks s ON s.id_producto = p.id_producto
		LEFT JOIN TempVentasTotales v ON v.id_producto = p.id_producto;	
	
	END IF;
	
	UPDATE Resultado SET stock_min_nvo = ventas WHERE ventas > 0;
	
	UPDATE Resultado SET stock_max_nvo = ventas * 2 WHERE ventas > 0;
	
	UPDATE Resultado SET pedido_sugerido = stock_max - stock
	WHERE stock >= 0 AND stock <= stock_min;
		
	DELETE FROM Resultado WHERE IFNULL(pedido_sugerido,0) < 1;	
	
	DELETE FROM Resultado WHERE IFNULL(stock_origen,0) < 1;	
	
	
	
	
	
	
	IF V_productosTop = 1 THEN
		SELECT r.id_producto,e.nombre_fiscal,su.nombre_sucursal,al.nombre_almacen,p.descripcion,p.codigo_barras,p.codigo,l.nombre_linea,IFNULL(r.stock_min,0) AS stock_min,IFNULL(r.stock_max,0) AS stock_max,IFNULL(r.stock_min_nvo,0) AS stock_min_nvo,IFNULL(r.stock_max_nvo,0) AS stock_max_nvo,IFNULL(r.stock,0) AS stock,IFNULL(r.ventas,0) AS ventas,IFNULL(r.pedido_sugerido,0) AS pedido_sugerido,IFNULL(r.stock_origen,0) AS stock_origen 
		FROM Resultado r
		INNER JOIN cat_productos p ON p.id_producto = r.id_producto
		INNER JOIN cat_lineas l ON l.id_linea = r.id_linea
		INNER JOIN cat_empresas e ON e.id_empresa = r.id_empresa
		INNER JOIN cat_sucursales su ON su.id_sucursal = r.id_sucursal
		INNER JOIN cat_almacenes al ON al.id_almacen = r.id_almacen	
		WHERE pedido_sugerido > 0
		ORDER BY p.descripcion LIMIT 300;
	ELSE
		SELECT r.id_producto,e.nombre_fiscal,su.nombre_sucursal,al.nombre_almacen,p.descripcion,p.codigo_barras,p.codigo,l.nombre_linea,IFNULL(r.stock_min,0) AS stock_min,IFNULL(r.stock_max,0) AS stock_max,IFNULL(r.stock_min_nvo,0) AS stock_min_nvo,IFNULL(r.stock_max_nvo,0) AS stock_max_nvo,IFNULL(r.stock,0) AS stock,IFNULL(r.ventas,0) AS ventas,IFNULL(r.pedido_sugerido,0) AS pedido_sugerido,IFNULL(r.stock_origen,0) AS stock_origen 
		FROM Resultado r
		INNER JOIN cat_productos p ON p.id_producto = r.id_producto
		INNER JOIN cat_lineas l ON l.id_linea = r.id_linea
		INNER JOIN cat_empresas e ON e.id_empresa = r.id_empresa
		INNER JOIN cat_sucursales su ON su.id_sucursal = r.id_sucursal
		INNER JOIN cat_almacenes al ON al.id_almacen = r.id_almacen	
		WHERE pedido_sugerido > 0
		ORDER BY descripcion;
	END IF;
	
	
END$$

DELIMITER ;