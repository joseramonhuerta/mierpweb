<?php

require_once ('eko_framework/lib/model.php');

class MenusManager extends ApplicationController{
    var $uses=array();
    var $components = array(	
		'ACL'=>array(
			'allowedActions'=>array('getMenus','getLinks')
		)
	);
	
    public function getMenus(){
        try{

            if (!isset($_POST['node']))throw new Exception("El servidor no recibió el identificador del nodo");

            $node=$_POST['node'];
            
            if (!is_numeric($node))$node=0;
            

            $usuario=$_SESSION['Auth']['User'] ['IDUsu'];
			// throw new Exception($_SESSION['Auth'] ['User']['AdminUsu']);
				
            if ($_SESSION['Auth'] ['User']['AdminUsu']==1 || $_SESSION['Auth'] ['User']['super']==true){
                $consulta="CALL get_menus_del_usuario(0,$node,true);";
            }else{
                $consulta="CALL get_menus_del_usuario($usuario,$node,false);";
            }

            $result=mysqlQuery($consulta);

            if (!$result)throw new Exception(mysql_error());
            $arr=array();            

            while($row =  mysql_fetch_array($result,MYSQL_ASSOC)){
                $link['id']   =$row['id'];
				$link['text'] = $row['TEXT'];
				$link['newWin'] =  $row['newWin'];
				$link['newTab']  =  $row['newTab'];
				$link['icon'] =  $row['icon'];
				$link['iconMaster'] =  $row['iconMaster'];
				$link['leaf'] =  $row['leaf'];
				$link['icono'] = $row['icono'];
				$arr[] = $link;
            }
			
			
			
            return $arr;
			
			
            
            
        }catch(Exception $e){
            $response['success']=false;
            $response['msg']=$e->getMessage();
             return $response;
        }
       
        
    }
	
	public function getLinks(){
		$model = new Model;
		$padre = $_POST['padre'];
		$nodos = array();
		
		if ($_POST['node'] == 'root'){
			$nodos[] = array('id'=>'G','text'=>'Ligas de interés general','leaf'=>false,'url'=>'','cls'=>'nodos-ligas-padre','iconCls'=>'node-sin-icono');
			// revisar si hay ligas del cliente
			$ligas = $model->query('select COUNT(*) AS num from cat_links');
			if ($ligas[0]['num']>0){
				$nodos[] = array('id'=>'P','text'=>'Vínculos del Corporativo','leaf'=>false,'url'=>'','cls'=>'nodos-ligas-padre','iconCls'=>'node-sin-icono');
			}
		} else {
			$database = ($padre == 'G') ? 'cfd_master' : DB_NAME;
			$id_nodo  = (substr($_POST['node'],1) == '') ? 0 : substr($_POST['node'],1);
			$data  = $model->query("SELECT * FROM cat_links WHERE KEYPadLink = $id_nodo ORDER BY OrdenLink;", $database);
			foreach($data as $row){
				$link['id']   = $padre.$row['IDLink'];
				$link['text'] = $row['DescLink'];
				$link['leaf'] = ($row['PadreLink']=='N') ? true : false;
				$link['url']  = $row['UrlLink'];
				$link['icon'] = ($row['IconoLink']) ? 'images/links/'.$row['IconoLink'] : '';
				$link['qtip'] = $row['TooltipLink'];
				$nodos[] = $link;
			}
		}
		return $nodos;
	}
}

?>
