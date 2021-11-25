<?php
require ('eko_framework/app/models/ciudad.php');       //MODELO
require ('eko_framework/app/models/parametros_model.php');  

class Parametros extends ApplicationController {	
    var $uses=array('Parametros');
	var $model='ParametrosModel';
	
	protected $camposAfiltrar = array('des_par');
	var $components=array(
		'ACL'=>array(
			'allowedActions'=>array('getCatCiudades'))
	);
	
    public function getParametros(){
		$model=$this->getModelObject();
		
		$limit  = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
        $start  = (empty($_POST['start'])) ?  0 : $_POST['start'];
        $filtro = (empty($_POST['query'])) ?  '': $_POST['query'];
        $params['filtros']=array();	
		
		return $model->readAll($start,$limit, $filtro,$params,true);                 
    }
	
    function getParYstore(){
		$id=$_POST['IDPar'];
		//------------------------------------------------
		$model=$this->getModelObject();
		$data=$model->getById($id);
		//------------------------------------------------
		//Para evitar este bloque es necesario modificar el js, la ventaja es un codigo mas unificado en el controlador
		//Este combo es usado para llenar el combo de ubicacion
		//Este codigo tambien existe en la funcion guardar
		$data['combo']=array(
			'success'=>true,
			'id_ciu'=>$data['Parametros']['ciu_def_par'], 
			'nom_ciu'=>$data['Parametros']['nom_ciu'], 
			'id_est'=>$data['Parametros']['est_def_par'],
			'nom_est'=>$data['Parametros']['nom_est'], 
			'id_pai'=>$data['Parametros']['pai_def_par'],
			'nom_pai'=>$data['Parametros']['nom_pai'], 
			'img_pai'=>strtolower($data['Parametros']['nom_pai']).'/'.$data['Parametros']['nom_pai'].'.png'
		);
		//------------------------------------------------
		return array(
			'success'=>true,
			'data'=>$data
		);
		
    }
	
    public function guardar(){
		$params=$this->getParams();
		$model=$this->getModelObject();
		$data=$model->save($params,false);
		$data['parametros']=$data['Parametros'];
		//------------------------------------------------
		//Para evitar este bloque es necesario modificar el js, la ventaja es un codigo mas unificado en el controlador
		//Este combo es usado para llenar el combo de ubicacion
		//Este codigo tambien existe en la funcion getParYstore
		$data['combo']=array(
			'success'=>true,
			'id_ciu'=>$data['Parametros']['ciu_def_par'], 
			'nom_ciu'=>$data['Parametros']['nom_ciu'], 
			'id_est'=>$data['Parametros']['est_def_par'],
			'nom_est'=>$data['Parametros']['nom_est'], 
			'id_pai'=>$data['Parametros']['pai_def_par'],
			'nom_pai'=>$data['Parametros']['nom_pai'], 
			'img_pai'=>strtolower($data['Parametros']['nom_pai']).'/'.$data['Parametros']['nom_pai'].'.png'
		);
		return array(
			'success'=>true,
			'data'=>$data,
			'msg'=>array('titulo'=>'Parámetros','mensaje'=>'Parámetro almacenado de manera correcta')
		);        
    }
	
    public function getRegParametros(){
        $result = array();
        $id = $_POST['id_par'];
        $query = "SELECT * FROM cat_parametros WHERE id_par = $id;";
        $res = mysqlQuery($query);
		echo jsonform($res);        
    }
	
    public function delete(){
        $response = array();
        try{
            $id = $_POST['id_par'];
            $query = "DELETE FROM cat_parametros WHERE id_par = $id ";
            $res = mysqlQuery($query);          //<------------ELIMINO EL REGISTRO
            if(!$res)throw new Exception(mysql_error());
            
            $response['success'] = true;
            $response['msg'] =array('titulo'=>'Parametros','mensaje'=>'Registro de parametros eliminado') ;
            
        }catch(Exception $e){
			$response['success'] = false;
			$response['msg'] = 'No pudo eliminarse el registro. '.$e->getMessage();
        }		
        echo json_encode($response);
        
	}
	
	public function getCatCiudades(){
		$busqueda = (!empty($_POST['query'])) ? $_POST['query'] : '';
		$inicio   = (!empty($_POST['start'])) ? $_POST['start'] : 0;
		$limite   = (!empty($_POST['limit'])) ? $_POST['limit'] : 0;
		
		$ciudadModel=new CiudadModel();
		$response=$ciudadModel->find($inicio,$limite,$busqueda);
		return $response;
		
		$queryTotal="SELECT COUNT(1) as totalRows
			FROM cat_ciudades
			LEFT OUTER JOIN cat_estados ON (id_est = key_est_ciu AND key_pai_est = key_pai_ciu)
			LEFT OUTER JOIN cat_paises ON (id_pai = key_pai_ciu);
			";		
		$model=new Model(); 
		$arrTotal=$model->query($queryTotal);
		$total=$arrTotal[0]['totalRows'];
		
		$query="SELECT  id_ciu, nom_ciu, id_est, nom_est, id_pai, nom_pai, CONCAT(LCASE(LEFT(nom_pai, 1)),'/',nom_pai,'.png') AS img_pai,
		',V_totalRows,' AS totalrows FROM cat_ciudades LEFT OUTER JOIN cat_estados ON (id_est = key_est_ciu AND key_pai_est = key_pai_ciu) LEFT OUTER JOIN cat_paises ON (id_pai = key_pai_ciu)
		 ORDER BY nom_ciu LIMIT $inicio, $limite";
		$arrRes=$model->query($query);
		
		$response=array(
			'success'=>true,
			'data'=>$arrRes,
			'totalRows'=>$total		
		);
		return $response;
		//$query = 'CALL spCatCiudadesConsultar(0, 0, 0, "'.$busqueda.'", '.$inicio.', '.$limite.');';
		//$res = mysqlGridPaginado($query);
		//echo $res;
	}
	
	public function getCiudadActual(){
		$response = array();
		$query = 'CALL spCatCiudadesConsultar('.$_POST['id_ciu'].', '.$_POST['id_est'].', '.$_POST['id_pai'].', "", 0, 0);';
		$res = mysqlQuery($query);
		if ($res){
			echo ResulsetToExt::jsonform($res);
		} else {
			$response['success'] = false;
			$response['data']    = array();
			$response['message'] = "Error al buscar la ciudad";
			echo json_encode($response);
		}
		
	}
	
	public function totalParametros(){
		$res = mysqlQuery("SELECT COUNT(*) AS total FROM cat_parametros;");
		if (mysql_num_rows($res)){
			$row = mysql_fetch_object($res);
			echo $row->total;
		} else {
			echo '0';
		}
	}
}