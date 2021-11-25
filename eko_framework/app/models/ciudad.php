<?php
class CiudadModel extends Model{
    var $name = 'Ciudad';  //ESTE VALOR DEBO OBTENERLO DINAMICAMENTE, POR LO PRONTO QUE ASI SEA PARIENTES!!
    var $useTable="cat_ciudades";
    var $primaryKey = 'id_est';
    var $specific = true;
    
    var $camposAfiltrar = array('nom_ciu','nom_est');
    public function getCiudadEstadoYpais($idCiudad,$idEstado,$idPais){

        $query="SELECT id_ciu,nom_ciu,nom_est,id_est,nom_pai,id_pai FROM $this->useTable
                LEFT JOIN cat_estados ON id_est=key_est_ciu
                LEFT JOIN cat_paises ON id_pai=key_pai_ciu
                WHERE id_ciu='$idCiudad' AND key_est_ciu='$idEstado' AND key_pai_ciu=$idPais";

        $arrayResult=$this->query($query);
        return $arrayResult;
    }
	
	public function find($start, $limit, $filtro){
    //public function find($start, $limit, $filtro, $id_pais=146){
   		if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);			
			//$filtroSql .= ' AND key_pai_ciu='.$id_pais;
        } else {
           // $filtroSql = 'WHERE key_pai_ciu='.$id_pais;
			$filtroSql = '';
        }
        
    	$queryTotal="SELECT COUNT(1) as totalRows
			FROM cat_ciudades
			LEFT OUTER JOIN cat_estados ON (id_est = key_est_ciu AND key_pai_est = key_pai_ciu)
			LEFT OUTER JOIN cat_paises ON (id_pai = key_pai_ciu) 
    		$filtroSql;
			";
    	      
		//
		$arrTotal=$this->query($queryTotal);
		$total=$arrTotal[0]['totalRows'];
		
		$query="SELECT  id_ciu, nom_ciu, id_est, nom_est, id_pai, nom_pai, CONCAT(LCASE(LEFT(nom_pai, 1)),'/',nom_pai,'.png') AS img_pai,
		',V_totalRows,' AS totalrows 
		FROM cat_ciudades 
		LEFT OUTER JOIN cat_estados ON (id_est = key_est_ciu AND key_pai_est = key_pai_ciu) 
		LEFT OUTER JOIN cat_paises ON (id_pai = key_pai_ciu)
		 $filtroSql 
		 ORDER BY nom_ciu LIMIT $start, $limit";
		$arrRes=$this->query($query);
		//throw new Exception($query);
		$response=array(
			'success'=>true,
			'data'=>$arrRes,
			'totalRows'=>$total		
		);
		return $response;
    }
}
?>
