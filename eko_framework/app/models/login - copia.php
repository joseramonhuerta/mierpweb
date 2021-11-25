<?php

class LoginModel extends Model{
    var $useTable = '';
    var $name='Login';
    var $primaryKey = '';
    var $specific = false;
    var $camposAfiltrar = array('');


    /**
     *  Busca en la base de datos al usuario
     * 

      
     *
     *
     *
     *

      @param  user  el email del usuario
     *

      @param  pass la contraseña del usuario
     *

      @return     boolean
     *

      @see         ver mas...
     */
    
    public function identificar($user, $pass) {        
       $query="SELECT r.id_usuario,if (u.esadmin=0,r.esadmin,u.esadmin ) as esadmin,u.nombre_usuario FROM cat_usuarios_corporativos r
        LEFT JOIN cat_usuarios u ON r.id_usuario=u.id_usuario
        LEFT JOIN cat_corporativos c ON r.id_corporativo=c.id_corporativo
        WHERE r.id_usuario='$user' AND r.pass=AES_ENCRYPT('$pass','asdf');";

        $arrayResult=$this->query($query, DB_MASTER);
		if (sizeof($arrayResult)>0){
			return $arrayResult[0];	
		}else{
			return array();
		}
                 
    }

    public function obtenerCorporativosParaSuper($user,$pass){
         $query = "SELECT corp.* FROM cat_usuarios_corporativos corp WHERE id_usuario='$user' AND pass=AES_ENCRYPT('$pass','asdf')";
         $arrayResult=$this->query($query, DB_MASTER);
         
         if (sizeof($arrayResult)>0){
            $query = "SELECT id_corporativo IDCor,nombre_corporativo NomCor,bd_corporativo DBCor,status StaCor,'$pass' as pass FROM cat_corporativos ORDER BY nombre_corporativo;";  //jrhc->20082013 
            
            $arrayResult=$this->query($query, DB_MASTER);
            return $arrayResult;
         }else{
             throw new Exception('Contraseña incorrecta');
         }
        
    }
    public function getCorporativo($CorId){
        $query="SELECT id_corporativo IDCor, nombre_corporativo NomCor,bd_corporativo DBCor,status StaCor FROM cat_corporativos WHERE id_corporativo=$CorId;";
        $respArray=$this->query($query,DB_MASTER);
        return $respArray[0];
    }
    public function getCorporativoYrol($CorId,$userId){
        $query="SELECT IDCor, NomCor,DBCor,StaCor,AdminUsr FROM cat_corporativos c
        LEFT JOIN cat_usuarios_corporativos uc ON c.IDCor=uc.KEYCor AND KEYUsr='$userId' WHERE IDCor=$CorId;";
        $respArray=$this->query($query,DB_MASTER);
        return $respArray[0];
    }
    
    public function obtenerCorporativosRelacionadosConElUsuario($userId,$pass){
         /*$query = "SELECT corp.* FROM cat_usuarios_corporativos corp_user
             LEFT JOIN cat_corporativos corp ON corp.IDCOr = corp_user.KEYCor
             WHERE KEYUsr='$userId' AND corp.staCor='activo' AND PassUsr=DES_ENCRYPT('$pass');";
         */
                  $query = "SELECT corp_user.id_corporativo,corp.nombre_corporativo,corp.bd_corporativo,corp.status,AES_DECRYPT(corp_user.pass,'asdf') as pass FROM cat_usuarios_corporativos corp_user
             LEFT JOIN cat_corporativos corp ON corp.id_corporativo = corp_user.id_corporativo
             WHERE corp_user.id_usuario='$userId' ORDER BY corp.nombre_corporativo;"; //jrhc->20082013 

         $arrayResult=$this->query($query, DB_MASTER);
         return $arrayResult;
    }

    public function getStatusDelUsuario($DBName,$IDUsr){
         $query="SELECT StatusUsu FROM cat_usuarios WHERE UserUsu='$IDUsr'";
         $resultAsArray=$this->query($query,$DBName);
         return $resultAsArray[0]['StatusUsu'];
    }
    
    public function getStatusDelUsuarioEnElCorporativo($dbCorp,$userId){
        $query="SELECT StatusUsu from cat_usuarios WHERE UserUsu ='$userId';";
        $arrayResult=$this->query($query, $dbCorp);        
        if (sizeof($arrayResult)>0){
            $status=$arrayResult[0]['StatusUsu'];
            if ($status=='A'){
                return true;
            }
        }        
        return false;
    }

    public function obtenerTodasLasEmpresas($db){
        $query="call spConsultaTodasEmpresasSucursales();";
        $empresasYSucursales=$this->query($query,$db);
        return $empresasYSucursales;
    }
    
    public function getUserId($userEmail,$dbName){

        $query = "call loginGetUserId('$userEmail');";	

        $resArray = $this->query($query, $dbName);
		
        if (sizeof($resArray) > 0) {
            $userId = $resArray[0]['IDUsu'];
            return $userId;            
        }
        return 0;
    }

    public function obtenerEmpresasConPermiso($dbName,$userId){
        $query="call loginGetEmpresas($userId,false);";
        $empresasYSucursales=$this->query($query,$dbName);
        return $empresasYSucursales;
    }
/*
    public function getNombreEmpresa($empresaId,$db){
        $query="SELECT ComEmp FROM cat_empresas WHERE IDEmp=$empresaId";
        $resArr=$this->query($query,$db);
        if (sizeof($resArr) > 0 ){
            return $resArr[0]['ComEmp'];
        }
        return "";
    }*/
    public function getEmpresa($empresaId,$db){
        $query="SELECT ComEmp,RFCEmp,ManejaInvEmp FROM cat_empresas WHERE IDEmp=$empresaId";
        $resArr=$this->query($query,$db);
      
        if (sizeof($resArr) > 0 ){
            return $resArr[0];
        }
        return "";
    }

    public function getSucursal($sucId,$db){
        $query = "SELECT NomSuc,RFCEmp FROM cat_sucursales 
            LEFT JOIN cat_empresas ON KEYEmpSuc=IDEmp WHERE IDSuc=$sucId";
        $resArr = $this->query($query,$db);
    
        if (sizeof($resArr) > 0) {
            return $resArr[0];
        }
        return "";
    }
    /*
    public function getNombreSucursal($sucId,$db){
        $query = "SELECT NomSuc FROM cat_sucursales WHERE IDSuc=$sucId";
        $resArr = $this->query($query,$db);
        if (sizeof($resArr) > 0) {
            return $resArr[0]['NomSuc'];
        }
        return "";
    }
	*/
}
?>
