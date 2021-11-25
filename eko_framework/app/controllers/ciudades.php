<?php
//require ('eko_framework/app/models/pais.php');
require ('eko_framework/app/models/ciudad.php');
class Ciudades extends ApplicationController {
     var $components = array('Auth'=>array(
        'allowedActions'=>array('obtenerCiudades')
        ));
    function obtenerCiudades(){
        $pais=$inicio = (!empty($_POST['pais'])) ? $_POST['pais'] : 146;
        $busqueda = (!empty($_POST['query'])) ? $_POST['query'] : '';
        $inicio = (!empty($_POST['start'])) ? $_POST['start'] : 0;
        $limite = (!empty($_POST['limit'])) ? $_POST['limit'] : 20;


        //if ($pais!=146 && $pais!= 73){
		if ($pais!=146 && $pais!= 73){
            $response['success']=true;
            $response['data']=array();
            $response['totalrows']=0;
            return json_encode($response);
        }
        
        $ciudadModel=new CiudadModel();
		//$response=$ciudadModel->find($inicio,$limite,$busqueda,$pais);
		$response=$ciudadModel->find($inicio,$limite,$busqueda);
		return $response;

        
        $queryTotal="SELECT COUNT(1) as totalRows
			FROM cat_ciudades
			LEFT OUTER JOIN cat_estados ON (id_est = key_est_ciu AND key_pai_est = key_pai_ciu)
			LEFT OUTER JOIN cat_paises ON (id_pai = key_pai_ciu);
			";
        /*
         *
         * -- CONCAT(LCASE(LEFT(nom_pai, 1)),'/',nom_pai,'.png')
			
			SET @sql = CONCAT(@sql, ' ORDER BY nom_ciu');
			SET @sql = CONCAT(@sql, ' ', CONCAT(' LIMIT ',V_start,', ',V_limit));
         **/        
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
        
        
        //return json_encode($response); //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
}


?>
