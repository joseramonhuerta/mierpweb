<?php

require ('eko_framework/app/models/cliente_categoria.php'); 

class ClientesCategoria extends ApplicationController {
	
	function getModelObject(){
    	if (empty($this->model)) {
    		$this->model=new Model();
    	}
    	return $this->model;
    } 
   
	 function obtenercategorias(){ //<----------------PARA EL GRID

			$limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro']; 
			$filtroStatus = (empty($_POST['filtroStatus'])) ?  'A': $_POST['filtroStatus'];			
		  			
            $categoriaClienteModel=new ClienteCategoriaModel();
            $response = $categoriaClienteModel->readAll($start,$limit,$filtro,$filtroStatus);
      
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
	
	public function cambiarstatus(){
       
	   $idValue=$_POST['id_cliente_categoria'];
   
		
		$statusOld=$_POST['status'];			
		if ($statusOld=='A'){
			$nuevoStatus="I";
		}else if ($statusOld=='I'){
			$nuevoStatus="A";
		}else{
			return array(
				'success'=>false,
				'msg'=>array(
					'titulo'=>'Error en la peticion de cambio de status',
					'mensaje'=>"El estado (<span style='font-weight:bold;'>$statusOld</span>) es desconocido por el sistema."
				)				
			);
		}
		
		
        $query="UPDATE cat_clientes_categorias SET status='$nuevoStatus' WHERE id_cliente_categoria=$idValue";
        $result=mysqlQuery($query);
        $response=array();
		$data=array(
			'id_cliente_categoria'=>$id,
			'status'=>$nuevoStatus
		);
		
        if (!$result){
            $response['success']=false;
            $response['msg']= array(
					'titulo'=>'Categoria de Cliente',
					'mensaje'=>"Error al actualizar el estado de la Categoria de Cliente:".mysql_error()
				);
        }else{
            $response['success'] = true;
            $estado='';
            if ($nuevoStatus=="I"){
                $estado="Desactivada";
            }else{
                $estado="Activada";
            }
            $response['msg'] = array(
					'titulo'=>'Categoria de Cliente',
					'mensaje'=>"La categoria de cliente ha sido $estado"
				);
			
			$response['data'] = $data;
        }
		
        return $response;
	}
	
	function obtenercategoria(){
		$clienteCategoriaModel = new ClienteCategoriaModel();
						
		$id=$_POST['idCat'];
		$datos = $clienteCategoriaModel->getById($id);
		$response['success'] = true;
		$response['data']['ClienteCategoria'] = $datos['ClienteCategoria'];
			
		return $response;			
	}
	
	function guardar(){
       
        $empresa=array();
        $response=array();
    		
		$ClienteCategoria=array(
		'id_cliente_categoria'=>$_POST['id_cliente_categoria'],
		'nombre_categoria'=>$_POST['nombre_categoria'],
		'status'=>$_POST['status']			
		);		
		
		$clienteCategoriaModel=new ClienteCategoriaModel();
		
		$categoriaGuardado=$clienteCategoriaModel->guardar($ClienteCategoria);
		if (!$categoriaGuardado)throw new Exception("Error al guardar los datos de la Categoria de Cliente");
		
		$response['success'] = true;
		$response['msg'] = array('titulo'=>'Categorias de Cliente','mensaje'=> 'La información de la Categoria de Cliente ha sido guardada satisfactoriamente') ;            
		$response['data']['ClienteCategoria']= $categoriaGuardado; 
			
		return $response;
    }

	
	function eliminar(){
		 $clienteCategoriaModel=new ClienteCategoriaModel();
		$titulo=$clienteCategoriaModel->name;
		
		if ( empty($_POST['id_cliente_categoria']) ){
			return array(
				'success'=>false,
				'msg'=>array('titulo'=>"Error en la solicitud de borrado",'mensaje'=>"Debe proporcionar la referencia a la Categoria de Cliente que desea eliminar"),
				'data'=>$data
			);	
		}
		
		$id=$_POST['id_cliente_categoria'];	
	
		$clienteCategoriaModel->delete($id);
		
		$affected=mysql_affected_rows();
		
		if (empty($affected)){
			$success=false;
			$mensaje="La Categoria de Cliente no fue eliminada";
		}else{
			$success=false;
			$mensaje="Categoria de Cliente eliminada de la base de datos";
		}	
		$data=array('id_cliente_categoria'=>$id);
		
		return array(
			'success'=>true,
			'msg'=>array(
					'titulo'=>'Categorias de Cliente',
					'mensaje'=>$mensaje
				),
			'data'=>$data
		);
	}	
	
	/******************/
		
	
	
	function filtroToSQL($filtro,$camposAfiltrar=array()) {
     	 $where = '';
     	 
        if (!empty($filtro)) {
			$filtroArray = explode(" ", $filtro);
	        $condiciones = "";
	        $condicion = "";

	        foreach ($camposAfiltrar as $campo) {
	
	            foreach ($filtroArray as $text) {
	                if (strlen($text) > 0){
						$condicion.="$campo LIKE '%$text%' AND ";	 									
					}
	            }
	
	            if (strlen($condicion) > 0) {
	                $condicion = substr($condicion, 0, strlen($condicion) - 4); //<----LE BORRO LA ULTIMA PARTE "AND ";
	                $condicion = "(" . $condicion . ") OR ";
	                $condiciones.=$condicion;
	                $condicion = "";
	            }
	        }
	       
	        if (strlen($condiciones) > 0) {
	            $condiciones = substr($condiciones, 0, strlen($condiciones) - 3); //<----LE BORRO LA ULTIMA PARTE "or ";
	            $where = "WHERE ($condiciones)";
	        }
        }
        return $where;
    }
}