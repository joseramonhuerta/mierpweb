<?php
class Sucursal extends Model{
    var $useTable = 'cat_sucursales';
    var $name='Sucursal';
    var $primaryKey = 'id_sucursal';
    var $specific = true;
    var $camposAfiltrar = array('nombre_sucursal');
    var $impuestos;
    var $certificados;
    
    function setImpuestos($impuestos){
        $this->impuestos=$impuestos;
    }
    function setCertificados($certificados){
        $this->certificados=$certificados;
    }
	
    function readAll($start, $limit, $filtro,$filtrarActivos=false) {
		$id_user = $_SESSION['Auth']['User']['IDUsu'];
		$admin   = $_SESSION['Auth']['User']['AdminUsu'];
		$privilegios = '';
		
        if ($filtro != '') {
            $filtroSql = $this->filtroToSQL($filtro);
        } else {
            $filtroSql = '';
        }

        if ($filtrarActivos) {
            if (strlen($filtroSql) > 0) {
                $filtroSql.=" AND StatusSuc='A' ";
            } else {
                $filtroSql = "WHERE StatusSuc='A' ";
            }
        }
		
		if (!$admin) {
			if (strlen($filtroSql) > 0) {
                $filtroSql.= " AND Origen = 'SUC' ";
            } else {
                $filtroSql = " WHERE Origen = 'SUC' ";
            }
			$privilegios = " LEFT JOIN cat_usuarios_privilegios ON (KEYUsuPriv = '$id_user' AND KEYID = IDSuc) ";
		}
        
        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable s
                LEFT JOIN cat_empresas e ON e.IDEmp = s.KEYEmpSuc 
                $privilegios $filtroSql ";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = "SELECT s.IDSuc,s.NomSuc,s.StatusSuc,s.NomConSuc,e.ComEmp FROM $this->useTable s
                LEFT JOIN cat_empresas e ON e.IDEmp = s.KEYEmpSuc
                $privilegios $filtroSql limit $start,$limit; ";                
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;

        return $response;
    }
	
    public function setStatus($id,$status){
        $query = "UPDATE $this->useTable SET StatusSuc='$status' WHERE $this->primaryKey=$id";
        $exito=$this->update($query);
        return $exito;
    }
	
    public function guardar($datos){
        $registroNuevo=false;

        $IDUsu=$_SESSION['Auth']['User']['IDUsu'];
		$where='';
        if ($datos[$this->primaryKey]){//UPDATE
            $query="UPDATE $this->useTable SET ";
            $query.="ModUsuario=$IDUsu";    //LOG
            $query.=",ModFecha=now()";
            $where=" WHERE $this->primaryKey = ".$datos[$this->primaryKey];
        }else{  //INSERT
            $query="INSERT INTO $this->useTable SET ";
            $query.="AddUsuario=$IDUsu";    //LOG
            $query.=",AddFecha=now()";  
            $registroNuevo=true;
        }
        
        $query.=",KEYEmpSuc=".$datos['KEYEmpSuc']."";
        $query.=",NomSuc='".$datos['NomSuc']."'";
        $query.=",CalleSuc='".$datos['CalleSuc']."'";
        $query.=",NumIntSuc='".$datos['NumIntSuc']."'";
        $query.=",NumExtSuc='".$datos['NumExtSuc']."'";
        $query.=",LocSuc='".$datos['LocSuc']."'";
        $query.=",ColSuc='".$datos['ColSuc']."'";
        $query.=",MunSuc='".$datos['MunSuc']."'";

        if (!is_numeric($datos['MunSuc'])){
            $query.=",EstSuc='".$datos['nom_est']."'";
        }else{
            $query.=",EstSuc='".$datos['EstSuc']."'";
        }
 
        $query.=",PaisSuc='".$datos['PaisSuc']."'";
        $query.=",CPSuc=".$datos['CPSuc'];
        //DATOS DEL CONTACTO
        $query.=",FaxSuc='".$datos['FaxSuc']."'";
        $query.=",TelSuc='".$datos['TelSuc']."'";
        $query.=",NomConSuc='".$datos['NomConSuc']."'";
        $query.=",MailConSuc='".$datos['MailConSuc']."'";
        $query.=",CelConSuc='".$datos['CelConSuc']."'";
        $query.=",TelConSuc='".$datos['TelConSuc']."'";
        
        
        $query=$query.$where;
		
        $result=mysqlQuery($query);
        if ($result){
            if ($registroNuevo){
                $id=mysql_insert_id();
            }else{
                $id=$datos[$this->primaryKey];
            }
            $this->id=$id;
            $data=$this->getById($id);

            $tasas=$this->guardarTasas($id);
            $this->guardarCertificados();
            return $data;
        }else{
            throw new Exception(mysql_error());
        }

    }
    
	private function guardarTasas($empresaId){
		//  echo print_r($this->impuestos);
        $tasas=$this->impuestos;
        $updates='';
        $inserts='';
        $updates=array();
        $deletes='';
        foreach($tasas as $tasa){
            if ($tasa['IDTaR']!=''){
                if ($tasa['KEYTasaTaR']!=''){
                    $update="UPDATE cat_tasas_relaciones SET KEYTasaTaR =".$tasa['KEYTasaTaR']." WHERE IDTaR=".$tasa['IDTaR'].';';
                    $updates[]=$update;
                }else{
                    if ($deletes==''){
                        $deletes="DELETE FROM cat_tasas_relaciones WHERE IDTaR=".$tasa['IDTaR'];
                    }else{
                        $deletes.=" OR IDTaR=".$tasa['IDTaR'];
                    }
                                       ;
                }
            }else{
                if ($tasa['KEYTasaTaR']!=''){
                    $inserts.="('S',".$tasa['KEYTasaTaR'];
                    $inserts.=",".$tasa['IDImp'];
                    $inserts.=", $empresaId),";
                }
            }
        }

        if (sizeof($updates)>0){
            foreach($updates as $query){
                $res=mysqlQuery($query);
                if (!$res)throw new Exception("Error al actualizar las tasas");
            }
        }

        if ($inserts!=''){
            $inserts = substr($inserts, 0, strlen($inserts) - 1); //<----LE BORRO LA ULTIMA COMA ",";
            $queryInsert="INSERT INTO cat_tasas_relaciones (OrigenTar,KEYTasaTaR,KEYImpTaR,KEYOrigenTaR) VALUES $inserts;";

            $res=mysqlQuery($queryInsert);
            if (!$res)throw new Exception("Error al guardar las tasas para la sucursal:". mysql_error());
        }

        if ($deletes!=''){
            $res=$this->queryDelete($deletes);
        }
    }
    private function guardarCertificados(){
        $IDCer=0;
        $certificados=$this->certificados;
        foreach($certificados as $cert){
            if ($cert['DefaultCerSuc']==1){
                $IDCer=$cert['IDCer'];
                break;
            }
        }

        $query="DELETE FROM cat_certificados_sucursales WHERE KEYSucCerSuc=$this->id";
        $this->update($query);

        if ($IDCer!=0){
            $query="SELECT FecSolCer FROM cat_certificados WHERE IDCer=$IDCer AND FecSolCer<now() AND now()<FecExpCer AND StatusCer='A';";
            $arrResult=$this->query($query);
            if (sizeof($arrResult)>0){

                $query="INSERT INTO cat_certificados_sucursales";
                $query.=" SET DefaultCerSuc=1,KeySucCerSuc=$this->id,KeyCerCerSuc=$IDCer";
                $this->insert($query);
            }else{
                throw new Exception("No puedo actualizarse la informacion del certificado ");
            }
        }
    }
    public function getTasas($idParam=null){
        if ($idParam==null){
            if ($this->id!=null){
                $idParam=$this->id;
            }
        }
         $query = "select IDImp,DescImp,ActivoImp,IDTaR,KEYTasaTaR,ActivoTasa FROM cat_impuestos i
            LEFT JOIN cat_tasas_relaciones r ON r.KEYImpTaR=i.IDImp AND OrigenTaR='S' AND KEYOrigenTar=$idParam
            LEFT JOIN cat_tasas t ON t.IDTasa=r.KEYTasaTaR;";
        $resImpuestos = mysqlQuery($query);
        if (!$resImpuestos)throw new Exception(mysql_error());
        $impuestos = array();
        while ($obj = @mysql_fetch_object($resImpuestos)) {
            $impuestos[] = $obj;
        }
        return $impuestos;
    }
    
    function getById($IDValue) {
        $query = "SELECT s.*,ComEmp FROM $this->useTable s
                LEFT JOIN cat_empresas e ON e.IDEmp = s.KEYEmpSuc
                WHERE $this->primaryKey=$IDValue";
        $result = mysqlQuery($query);
        
        $datos = array();
        
        
        $datos[$this->name] = mysql_fetch_array($result, MYSQL_ASSOC);

        $this->id=$datos[$this->name]['IDSuc'];
        return $datos;
    }
   
    public function delete($id){
        $queryTotSucs="SELECT count(IDFac) as totFacts FROM  facturacion WHERE IDSuc=$id";
        $result = $this->query($queryTotSucs);
        if ($result[0]['totFacts']>0 ){
            throw new Exception("<br/> La Sucursal no puede borrarse porque tiene facturas asignadas");
        }
        if (parent::delete($id)){
            //Tambien borro las referencias de la tabla de PRIVILEGIOS
            $query="DELETE FROM cat_usuarios_privilegios WHERE Origen='SUC' AND KEYID=$id";
            $result=mysqlQuery($query);
            return $result;
        }
        return false;
    }
    public function getCertificados($empresaId){
        $query="SELECT IDCer,NumSerCer,StatusCer,
         FecSolCer,
         FecExpCer,
         ifnull(KEYCerCerSuc,0) as KEYCerCerSuc,ifnull(KeyCerCerSuc,0) as KeyCerCerSuc,
        ifnull(DefaultCerSuc,0) as DefaultCerSuc
        FROM cat_certificados  c
        LEFT JOIN cat_certificados_sucursales cs ON c.IDCer=cs.KEYCerCerSuc  AND cs.KEYSucCerSuc=$this->id
        WHERE KEYEmpCer=$empresaId";
        $arrResult=$this->query($query);
        return $arrResult;
    }

	 public function obtenerTodasLasSucursales($empresaId){
			$query="call spConsultaTodasSucursales($empresaId);";
			$sucursales=$this->query($query);
			return $sucursales;
		}

	public function obtenerSucursalesConPermiso($userId,$IDEmp){
			 // throw new Exception($IDEmp);
			$query="
			SELECT s.id_sucursal,s.nombre_sucursal
			FROM cat_usuarios_privilegios up
			INNER JOIN cat_sucursales  s ON (up.id_privilegio =s.id_sucursal AND up.tipo_privilegio = 2)
			INNER JOIN cat_empresas e ON (e.id_empresa = s.id_empresa)
			WHERE up.id_usuario = '$userId' AND up.tipo_privilegio = 2 AND s.id_empresa = '$IDEmp'
			AND s.STATUS='A'
			ORDER BY s.id_sucursal;";      
			$sucursales=$this->query($query);
			return $sucursales;
		}	
	
}
?>
