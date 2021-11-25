<?php
class UserCorp extends Model{
    var $name='UserCorp';  //ESTE VALOR DEBO OBTENERLO DINAMICAMENTE, POR LO PRONTO QUE ASI SEA PARIENTES!!
    var $useTable = 'cat_usuarios';
    var $primaryKey = 'IDUsu';
    var $specific = true;

    var $camposAfiltrar = array('UserUsu','NomUsu');
    function getUsersGrid($start, $limit, $filtro,$filtrarActivos=false) {

        $filtroSql = $this->filtroToSQL($filtro);
        if ($filtrarActivos){
            if (strlen($filtroSql )>0){                
                $filtroSql.=" AND StatusUsu='A' ";
            }else{
                $filtroSql="WHERE StatusUsu='A' ";
            }
        }
        
        $query = "select count(IDUsu) as totalrows  FROM cat_usuarios $filtroSql";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT * FROM cat_usuarios $filtroSql limit $start,$limit";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;

        return $response;
    }
    function getById($id){

        $query = "SELECT IDUsu,NomUsu,UserUsu,'' as PassUsu,'' as passwordConfirm,TelUsu,CelUsu,AdminUsu,forUsu,temUsu,StatusUsu FROM cat_usuarios WHERE IDUsu=$id;";
        $res = mysqlQuery($query);
        
        $datos['UserCorp']=mysql_fetch_array($res,MYSQL_ASSOC);
        return $datos;
    }

    function getArbolDeModulosPermitidos($IDUsu){
                        
            
                $query = "SELECT concat('mod-',IDMod) as id,DescMod as text,IDMod,
                if (KEYId IS NULL,0,1 ) as checked,
                if ((select count(IDMod) as numero from cat_modulos where KeyPadMod=IDMod)=0, true, '') as leaf
                FROM cat_modulos m
                LEFT JOIN cat_usuarios_privilegios p ON p.Origen='MOD' AND p.KEYId=IDMod AND KEYUsuPriv=$IDUsu
                WHERE KEYPadMod=0";
            
            $result = mysqlQuery($query);
            
            $arr = array();
            if (!$result)echo mysql_error();

            while ($row = mysql_fetch_object($result)) {
                $hijo=array();
                $cont=0;
                ($row->checked == '1') ? $row->checked = true : $row->checked = false;
                if ($row->leaf==true){
                    $hijo['id']="hijo-".$cont;
                    $hijo['text']="hijo-".$cont;
                    $hijo['leaf']='';
                    $row->children=$hijo;
                    $cont++;
                }
                $arr[] = $row;
            }
           
            return $arr;
            

    }
    
}

?>
