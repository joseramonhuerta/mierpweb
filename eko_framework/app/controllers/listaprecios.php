<?php
require ('eko_framework/app/models/lista_precio.php');       //MODELO

class ListaPrecios extends ApplicationController {
    function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
    function obtenerlistaprecios(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $listaprecioModel=new ListaPrecioModel();
            $response = $listaprecioModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }

	function obtenerproducto(){
		try {
			$producto = $_POST['Descripcion'];
			$id_producto = $_POST['ID'];
			
			$query = "SELECT COUNT(id_producto) AS totalrows FROM cat_productos WHERE (descripcion = '$producto' OR codigo = '$producto' OR codigo_barras = '$producto') OR id_producto = $id_producto";
			$res = mysqlQuery($query);
			if (!$res)
			throw new Exception(mysql_error()." ".$query);
				
			$resultado  = mysql_fetch_array($res, MYSQL_ASSOC);
			$total_rows = $resultado['totalrows'];
			
			if ($total_rows > 0){
				$query = " SELECT p.id_producto, p.descripcion, p.codigo_barras, p.codigo, u.codigo_unidad, p.precio_venta, p.valor_puntos FROM cat_productos p";
				$query.= " INNER JOIN cat_unidadesdemedida u on u.id_unidadmedida = p.id_unidadmedida";
				$query.= " WHERE (p.descripcion = '$producto' OR p.codigo = '$producto' OR p.codigo_barras = '$producto') OR p.id_producto = $id_producto";
				$query.= " ORDER BY p.descripcion;";
				$res = mysqlQuery($query);
				if (!$res)  throw new Exception(mysql_error()." ".$query);
					
				$response = ResulsetToExt::resToArray($res);
			}else{
				$response['success']    = false;
			}
				
		} catch (Exception $e) {
			$response['success']    = false;
			$response['msg']       = $e->getMessage();
		}
		
		echo json_encode($response);
		
	}		
	
	function save(){
		$params = $_POST;
		
		$listaprecioModel=new ListaPrecioModel();
		
		$resp = $listaprecioModel->guardar($params);
		
		$response=array();
        $response['success']=true;
        $response['msg'] = array('titulo'=>'Lista Precios','mensaje'=> 'La información de la Lista de Precios ha sido guardada satisfactoriamente') ;            
        $response['data']=$resp;
		
        return $response;
	}

	function obtenerlista(){
		$listaprecioModel=new  ListaPrecioModel();

		$id=$_POST['idLis'];
				
		if($id==0){
			$data=array();
			$data['ListaPrecio']=$listaprecioModel->getInitialInfo();
					
		}else{
			$data=$listaprecioModel->getById($id);
		}
	
		$response=array();
        $response['success']=true;
        $response['data']=$data;
		
        return $response;
		
	}

	function eliminar(){
		$listaprecioModel=new ListaPrecioModel();
		$titulo=$listaprecioModel->name;
		
		if ( empty($_POST['id_listaprecio']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la lista de precios que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_listaprecio'];	
	
		$listaprecioModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La lista de precios no fue eliminado";
		}else{
			$success=true;
			$mensaje="Lista de precios eliminado de la base de datos";
		}	
		$data=array('id_listaprecio'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Lista Precio',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}
	
	function actualizarprecios(){
		$listaprecioModel=new ListaPrecioModel();
		$titulo=$listaprecioModel->name;
		
		if ( empty($_POST['id_listaprecio']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud",'mensaje'=>"Debe proporcionar la referencia a la lista de precios"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_listaprecio'];	
		
		$affected = $listaprecioModel->actualizarPrecios($id);		
		
		if (empty($affected)){
			$success=false;
			$mensaje="La lista de precios no fue actualizada";
		}else{
			$success=true;
			$mensaje="Lista de precios Actualizada";
		}	
		
		return array(
			'success'=>$success,
			'msg'=>array(
					'titulo'=>'Lista Precio',
					'mensaje'=>$mensaje
				),
			'data'=>$affected
		);
		
		//return true;
	}
}
?>