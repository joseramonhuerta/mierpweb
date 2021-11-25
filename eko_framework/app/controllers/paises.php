<?php
require ('eko_framework/app/models/pais.php');

class Paises extends ApplicationController {
    var $components=array(
		'ACL'=>array(
			'allowedActions'=>array('readAll'))
	);
    function readAll(){
        try {
            //$limit = $_POST['limit'];
            //$start = $_POST['start'];
            $query=(isset($_POST['query'])) ? $_POST['query'] : '';
            $limit=300;
            $start=0;

            $paisModel = new PaisModel();
            $response = $paisModel->readAll($start, $limit,$query);
        } catch (Exception $e) {
            $response['succes'] = false;
            $response['msg'] = $e->getMessage();
        }
        return $response; //RETURN PARA COMPRIMIR LA RESPUESTA CON GZIP
    }
}
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

?>
