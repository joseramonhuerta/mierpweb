<?php
class ProductoModel extends Model{
    var $useTable = 'cat_productos';
	var $useTableStock = 'cat_productos_stocks';
    var $name='Producto';
    var $primaryKey = 'id_producto';
    var $specific = true;
    var $camposAfiltrar = array('codigo','codigo_barras','descripcion');
		
	function readAll($start, $limit, $filtro, $filtroStatus) {
        
        if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);
        } else {
            $filtroSql = '';
        }
		
		 if (strlen($filtroSql) > 0) {
				if ($filtroStatus=='A')
                $filtroSql.=" AND p.status='A' ";
				if ($filtroStatus=='I')
                $filtroSql.=" AND p.status='I' ";
            }else {
               if ($filtroStatus=='A')
                $filtroSql.="WHERE p.status='A' ";
				if ($filtroStatus=='I')
                $filtroSql.="WHERE p.status='I' ";
            }

        
      

        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable p
        $filtroSql";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT p.id_producto,p.codigo,p.descripcion,p.tipo_producto,p.id_linea,l.nombre_linea,u.descripcion_unidad as unidad_medida,p.precio_venta,p.precio_estilista,p.status, p.valor_puntos FROM $this->useTable p
				LEFT JOIN cat_unidadesdemedida u ON u.id_unidadmedida=p.id_unidadmedida
				LEFT JOIN cat_lineas l ON l.id_linea=p.id_linea
                $filtroSql ORDER BY p.descripcion limit $start,$limit ;";

        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;
		
        return $response;
    }
    
	public function guardar($datos){
        $id_almacen = $_SESSION['Auth']['User']['id_almacen'];
    	$registroNuevo=false;
		 $IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		 
		 $stock_min = (empty($datos['stock_min']) )? 0 : $datos['stock_min'];
		 $stock_max = (empty($datos['stock_max']) )? 0 : $datos['stock_max'];
		 // throw new Exception($stock_max);
	    if (@$datos[$this->primaryKey]){//UPDATE
            $query="UPDATE $this->useTable SET ";
            
            $query.="usermodif=$IDUsu";    //LOG
            $query.=",fechamodif=now()";
            $where=" WHERE $this->primaryKey = ".$datos[$this->primaryKey];
        }else{  //INSERT
            $query="INSERT INTO $this->useTable SET ";
            $query.="usercreador=$IDUsu";    //LOG
            $query.=",fechacreador=now()";
          
            $registroNuevo=true;
			$where='';
        }
        $codigo = $this->EscComillas(strtoupper($datos['codigo']));
		$codigo_barras = $this->EscComillas(strtoupper($datos['codigo_barras']));
		
        $query.=",codigo='".$this->EscComillas(strtoupper($datos['codigo']))."'";
        $query.=",descripcion='".$this->EscComillas($datos['descripcion'])."'";
		$query.=",detalles='".$this->EscComillas($datos['detalles'])."'";
        $query.=",tipo_producto='".$this->EscComillas($datos['tipo_producto'])."'";
        $query.=",codigo_barras='".$this->EscComillas($datos['codigo_barras'])."'";
        
		if (is_numeric($datos['id_unidadmedida'])){	
			$query.=",id_unidadmedida='".$datos['id_unidadmedida']."'";
		}
		
		if (is_numeric($datos['id_linea'])){	
			$query.=",id_linea='".$datos['id_linea']."'";
		}
		
		$query.=",precio_venta=".$datos['precio_venta']."";	
        $query.=",precio_estilista=".$datos['precio_estilista']."";
		$query.=",precio_compra=".$datos['precio_compra']."";
		$query.=",valor_puntos=".$datos['valor_puntos']."";
		
		$query.=",iva=".$datos['iva']."";
		$query.=",ret_iva=".$datos['ret_iva']."";
		$query.=",ret_isr=".$datos['ret_isr']."";
		
		if (empty($datos['id_producto'])){//CUANDO ES UN NUEVO PRODUCTO, EL PRECIO DE COMPRA Y ULTIMO COSTO SON IGUAL AL COSTO DE COMPRA
			$query.=",ultimo_costo=".$datos['precio_compra']."";
			$query.=",costo_promedio=".$datos['precio_compra']."";
		}
        
		$query.=",status='".$this->EscComillas($datos['status'])."'";
		
        /*LOG dEL MOVIMIENTO*/

        $query=$query.$where;
		// throw new Exception($query);
		
        try{
            if ($registroNuevo) {				
				$id = $this->insert($query);
				
				$queryStock="INSERT INTO $this->useTableStock SET ";
				$queryStock.="AddUsuario=$IDUsu";    //LOG
				$queryStock.=",AddFecha=now()";
				
				$queryStock.=",id_almacen='".$id_almacen."'";
				$queryStock.=",id_producto='".$id."'";
				$queryStock.=",stock=0.000000";
				$queryStock.=",stock_min=".$stock_min."";	
				$queryStock.=",stock_max=".$stock_max."";
				
				$this->insert($queryStock);
				
				
            } else {
                $this->update($query);
                $id = $datos[$this->primaryKey];
				
				$stock = "SELECT id_stock FROM cat_productos_stocks WHERE id_almacen = $id_almacen and id_producto = $id";				
				$arr = $this->query($stock);
				$id_stock = $arr[0]['id_stock'];
				 // throw new Exception($id_stock);
				if($id_stock > 0){					
					$queryStock="UPDATE $this->useTableStock SET ";
					$queryStock.="ModUsuario=$IDUsu";    //LOG
					$queryStock.=",ModFecha=now()";
					$queryStock.=",stock_min=".$stock_min."";	
					$queryStock.=",stock_max=".$stock_max."";
					$queryStock.=" WHERE id_stock = ".$id_stock;
					$this->update($queryStock);
					
				} else{
					$queryStock="INSERT INTO $this->useTableStock SET ";
					$queryStock.="AddUsuario=$IDUsu";    //LOG
					$queryStock.=",AddFecha=now()";
					$queryStock.=",id_almacen='".$id_almacen."'";
					$queryStock.=",id_producto='".$id."'";
					$queryStock.=",stock=0.000000";
					$queryStock.=",stock_min=".$stock_min."";	
					$queryStock.=",stock_max=".$stock_max."";
					$this->insert($queryStock);
					
				}
				
				
            }
            $this->id = $id;
            $data = $this->getById($id);
            return $data['Producto'];
        }catch(Exception $e){            
            return false;
        }                    

    }
	
	public function validarProductoDuplicado($datos){
		$registroNuevo=false;
		
		
		 
	    if (!@$datos[$this->primaryKey]){//UPDATE
            
		// }else{
            $registroNuevo=true;
			
        }
		$idpro = @$datos[$this->primaryKey];
        $codigo = $this->EscComillas(strtoupper($datos['codigo']));
		$codigo_barras = $this->EscComillas(strtoupper($datos['codigo_barras']));
		
        
        if ($registroNuevo) {				
			$pro = "SELECT * FROM cat_productos WHERE codigo = '$codigo';";				
			$arr = $this->query($pro);
			if(count($arr) > 0) 
				throw new Exception("El codigo del producto ya esta registrado en el sistema, verifique!!!");
					
			$pro = "SELECT * FROM cat_productos WHERE codigo_barras = '$codigo_barras';";				
			$arr = $this->query($pro);
			if(count($arr) > 0) 
				throw new Exception("El codigo de barras del producto ya esta registrado en el sistema, verifique!!!");				
		}else{
			$pro = "SELECT * FROM cat_productos WHERE codigo = '$codigo' and id_producto != '$idpro';	";				
			$arr = $this->query($pro);
			if(count($arr) > 0) 
				throw new Exception("El codigo del producto ya esta registrado en el sistema, verifique!!!");
					
			$pro = "SELECT * FROM cat_productos WHERE codigo_barras = '$codigo_barras' and id_producto != '$idpro';";				
			$arr = $this->query($pro);
			if(count($arr) > 0) 
				throw new Exception("El codigo de barras del producto ya esta registrado en el sistema, verifique!!!");	
		}						
		
		
	}
	
	public function delete($id){
        return parent::delete($id);
    }

	function getById($id){
		$id_almacen = $_SESSION['Auth']['User']['id_almacen'];
    	$query="SELECT p.id_producto,p.codigo,p.codigo_barras,p.descripcion,p.detalles,p.id_unidadmedida,u.descripcion_unidad,p.precio_venta,p.precio_estilista,p.precio_compra,p.costo_promedio,
		p.ultimo_costo,p.status,p.tipo_producto,p.id_linea,l.nombre_linea,p.iva,p.ret_iva,p.ret_isr,
		IFNULL(s.stock_min,0) as stock_min,	IFNULL(s.stock_max,0) as stock_max, p.valor_puntos
		FROM cat_productos p
		LEFT JOIN cat_unidadesdemedida u ON p.id_unidadmedida=u.id_unidadmedida 
		LEFT JOIN cat_lineas l ON p.id_linea=l.id_linea 
		LEFT JOIN cat_productos_stocks s ON s.id_producto = p.id_producto AND s.id_almacen = $id_almacen
		WHERE p.id_producto=$id ";
    	$arrResult=$this->query($query);
    	
    	return array('Producto'=>$arrResult[0]);
    }
	
	function getProductosMM($id_almacen, $id_linea, $id_producto){
		$query ="CALL spMaximosMinimosProductos($id_almacen, $id_linea, $id_producto);";
		//throw new Exception($query);
		$productos = $this->query($query);
		
		if ( empty($productos) ){
			return array();
		}
			
		$datos=array();
		$datos['Productos']=$productos;
		return $datos;
		
		
	}
	
	public function guardarMaximoMinimos($params){
		try{
			
					
			$Productos=json_decode( stripslashes($params['Productos']), true);
			
			$numConceptos=sizeof($Productos);
			// Throw new Exception($numConceptos);
			if($numConceptos == 0)
				Throw new Exception("No se recibieron detalles.");
				
			
			$this->guardarDetalles($Productos);
			
			
			$response['success']    = true;
			$response['msg']       = array('titulo'=>"Productos",'mensaje'=>"Maximos y minimos guardados correctamente");
			$response['data']    = $data;
		}catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
		return $response;
                     

    }
	
	private function guardarDetalles($productos){
		$IDUsu=$_SESSION['Auth']['User']['IDUsu'];     
		foreach ($productos  as $producto) {
			// throw new Exception("1");
			// throw new Exception($concepto['id_producto']);	
			$queryStock="";
			$id_producto = $producto['id_producto'];
			$id_almacen = $producto['id_almacen'];
			$stock_min = $producto['stock_min'];
			$stock_max = $producto['stock_max'];
			
			
			$stock = "SELECT id_stock FROM cat_productos_stocks WHERE id_almacen = $id_almacen and id_producto = $id_producto";				
				$arr = $this->query($stock);
				$id_stock = $arr[0]['id_stock'];
				 // throw new Exception($id_stock);
				if($id_stock > 0){					
					$queryStock="UPDATE $this->useTableStock SET ";
					$queryStock.="ModUsuario=$IDUsu";    //LOG
					$queryStock.=",ModFecha=now()";
					$queryStock.=",stock_min=".$stock_min."";	
					$queryStock.=",stock_max=".$stock_max."";
					$queryStock.=" WHERE id_stock = ".$id_stock;
					$this->update($queryStock);
					
				} else{
					$queryStock="INSERT INTO $this->useTableStock SET ";
					$queryStock.="AddUsuario=$IDUsu";    //LOG
					$queryStock.=",AddFecha=now()";
					$queryStock.=",id_almacen='".$id_almacen."'";
					$queryStock.=",id_producto='".$id_producto."'";
					$queryStock.=",stock=0.000000";
					$queryStock.=",stock_min=".$stock_min."";	
					$queryStock.=",stock_max=".$stock_max."";
					$this->insert($queryStock);
					
				}			
			
			
		}
	}
}
?>
