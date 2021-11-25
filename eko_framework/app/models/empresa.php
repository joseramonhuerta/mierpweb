<?php
class Empresa extends Model{
    var $useTable = 'cat_empresas';
    var $name='Empresa';
    var $primaryKey = 'id_empresa';
    var $specific = true;
    var $camposAfiltrar = array('nombre_fiscal');
    var $impuestos;
    var $certificados;
	
    function setImpuestos($impuestos){
        $this->impuestos=$impuestos;
    }
    
    function setCertificados($certificados){
        $this->certificados=$certificados;
    }
   function getById($IDValue){	
            $query="SELECT IDEmp,manejaInvEmp, ComEmp, PatEmp, MatEmp, FisEmp, TipoEmp, RFCEmp ,CalleEmp, NumExtEmp ,NumIntEmp, ColEmp, LocEmp as localidad, MunEmp, 
            EstEmp, PaisEmp, CPEmp, NomConEmp, MailConEmp, TelConEmp, CelConEmp, StatusEmp, CFDiEmp FROM cat_empresas WHERE IDEmp=$IDValue";
            $arrRes= $this->query($query);
            $datos=array();
            $datos[$this->name]=$arrRes[0];
            return $datos;
    }
	function getRFC($IDEmp){
        $query = "select rfc FROM $this->useTable WHERE id_empresa=$IDEmp;";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        return $resultado['rfc'];
    }
    
	function getEmpresasParaGrid($start, $limit, $filtro,$filtrarActivos=false) {
		$id_user = $_SESSION['Auth']['User']['IDUsu'];
		$admin   = $_SESSION['Auth']['User']['AdminUsu'];
		$privilegios = '';
		
        $filtroSql = $this->filtroToSQL($filtro);

        if ($filtrarActivos) {
            if (strlen($filtroSql) > 0) {
                $filtroSql.=" AND StatusEmp='A' ";
            } else {
                $filtroSql = "WHERE StatusEmp='A' ";
            }
        }
		
		if (!$admin) {
			if (strlen($filtroSql) > 0) {
                $filtroSql.= " AND Origen = 'EMP' ";
            } else {
                $filtroSql = " WHERE Origen = 'EMP' ";
            }
			$privilegios = " LEFT JOIN cat_usuarios_privilegios ON (KEYUsuPriv = '$id_user' AND KEYID = IDEmp) ";
		}
        
        $query = "select count($this->primaryKey) as totalrows  FROM $this->useTable $privilegios $filtroSql;";
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $resultado = mysql_fetch_array($res, MYSQL_ASSOC);
        $totalRows = $resultado['totalrows'];

        $query = " SELECT IDEmp,if (TipoEmp='M',ComEmp,CONCAT_WS(' ',ComEmp,PatEmp,MatEmp)) as  ComEmp,FisEmp,StatusEmp,TipoEmp,RFCEmp,logotipo ";
		$query.= " FROM $this->useTable $privilegios $filtroSql ";
		$query.= " LIMIT $start, $limit; ";
		
		
        $res = mysqlQuery($query);
        if (!$res)
            throw new Exception(mysql_error() . " " . $query);

        $response = ResulsetToExt::resToArray($res);
        $response['totalRows'] = $totalRows;

        return $response;
    }
    
    public function guardar($datos){        
        $registroNuevo=false;
		$IDEmp=$datos[$this->primaryKey];
		$where='';
        if ($datos[$this->primaryKey]){//UPDATE
			$res = mysqlQuery("SELECT 1 FROM cat_empresas WHERE RFCEmp = '".$datos['RFCEmp']."' AND $this->primaryKey != ".$datos[$this->primaryKey].";");
			if (mysql_num_rows($res) >= 1){
				throw new Exception("RFC de la empresa ya existe");
			}
            $query="UPDATE $this->useTable SET ";
            $where=" WHERE $this->primaryKey = ".$datos[$this->primaryKey];
        }else{  //INSERT
			$res = mysqlQuery("SELECT 1 FROM cat_empresas WHERE RFCEmp = '".$datos['RFCEmp']."';");
			if (mysql_num_rows($res) >= 1){
				throw new Exception("RFC de la empresa ya existe");
			}
            $query="INSERT INTO $this->useTable SET ";
            $registroNuevo=true;            
            $query.=" CFDiEmp='".$datos['CFDiEmp']."',";
        }
        
        $query.=" TipoEmp='".$datos['TipoEmp']."'";
        $query.=",StatusEmp='".$datos['StatusEmp']."'";
        $query.=",ComEmp='". $datos['ComEmp']."'";
        $query.=",FisEmp='".$datos['FisEmp']."'";
        $query.=",CalleEmp='".$datos['CalleEmp']."'";
        $query.=",ColEmp='".$datos['ColEmp']."'";   
        $query.=",RFCEmp='".strtoupper($datos['RFCEmp'])."'";
        $query.=",PatEmp='".$datos['PatEmp']."'";
        $query.=",MatEmp='".$datos['MatEmp']."'";
        $query.=",PaisEmp=".$datos['PaisEmp'];
        $query.=",LocEmp='".$datos['localidad']."'";
        
        $query.=",EstEmp=".$datos['EstEmp'];
        $query.=",MunEmp=".$datos['MunEmp'];
		$query.=",CPEmp='".$datos['CPEmp']."'";
        //DATOS DEL CONTACTO
        $query.=",NomConEmp='".$datos['NomConEmp']."'";
        $query.=",MailConEmp='".$datos['MailConEmp']."'";
        $query.=",TelConEmp='".$datos['TelConEmp']."'";
        $query.=",CelConEmp='".$datos['CelConEmp']."'";               
        $query.=",NumExtEmp='".$datos['NumExtEmp']."'";
        $query.=",NumIntEmp='".$datos['NumIntEmp']."'";
        $query.=",manejaInvEmp='".$datos['manejaInvEmp']."'";
        
        $query=$query.$where;

      
            if ($registroNuevo){
                
               $result= $this->insert($query);
                $id=mysql_insert_id();
            }else{
            	//-------------------------------------------------------------------------------------------------------------------------------------
            	//	Si la empresa tiene certificados no  puede modificar el RFC porque ya no coincidirán
            	//	Antes de guardar, verifico si la empresa tiene certificados, en cuyo caso no podrá modificar el RFC hasta eliminar los certificados
            	$queryRFC="SELECT RFCEmp FROM cat_empresas WHERE IDEmp=$IDEmp";
            	$arrRFC=$this->query($queryRFC);
            	if (sizeof($arrRFC)==0){
            		throw new Exception("No se ha encontrado la empresa");
            	}
            	$rfc_old=strtoupper($arrRFC[0]['RFCEmp']);
            	$rfc_new=strtoupper($datos['RFCEmp']);
            	if ($rfc_new!=$rfc_old){
	            	$queryCerts="SELECT IDCer FROM cat_certificados WHERE KEYEmpCer=$IDEmp";
	            	$arrCerts=$this->query($queryCerts);	            	
	            	if (sizeof($arrCerts)>0){
	            		throw new Exception("Para cambiar el RFC, debe eliminar los certificados a esta empresa");
	            		
	            	}	
            	}	
            	
            	//-------------------------------------------------------------------------------------------------------------------------------------
                $result=$this->update($query);
                $id=$datos[$this->primaryKey];
            }
            $this->id=$id;
            $data=$this->getById($id);
			
            $tasas=$this->guardarTasas($id);
            $this->guardarCertificados();
			$this->guardarRegimens();
			
			
			#si la empresa seleccionada y la almacenada es la misma, se actualiza en la sesion lo relativo a inventarios
			if ( $_SESSION['Auth']['User']['IDEmp']==$id){
				$_SESSION['Auth']['User']['ManejaInvEmp'] = $data['Empresa']['manejaInvEmp'];
			}
			
            return $data;
        

    }
	private function guardarRegimens(){
		$regimens=$this->regimens;
		$regimenModel=new RegimenFiscalModel();		
		foreach($regimens as $regimen){
			
			$regimen['Regimen_EmpReg']=$regimen['Regimen'];
			
			unset($regimen['Regimen']);
			$regimen['KEY_Emp_EmpReg']=$this->id;
			
			$regimenModel->save($regimen);
		}
		
		if (isset($this->regimensEliminados)){
			foreach($this->regimensEliminados as $regimen){			
				$id=$regimen['ID_EmpReg'];			
				$regimenModel->delete($id);
			}
		}
		
		
	}
	
    private function guardarCertificados(){
		/*if (sizeof($this->certificados)==0){
				return;
		}
		*/
        $IDCer=0;
        $certificados=$this->certificados;
        foreach($certificados as $cert){
            if ($cert['DefaultCer']==1){
                $IDCer=$cert['IDCer'];                
                break;
            }
        }
        
        $query="UPDATE cat_certificados SET DefaultCer=0 WHERE KEYEmpCer=$this->id";
        $this->update($query);

        if ($IDCer!=0){
            $query="SELECT FecSolCer FROM cat_certificados WHERE IDCer=$IDCer AND FecSolCer<now() AND now()<FecExpCer AND StatusCer='A';";            
            $arrResult=$this->query($query);            
            if (sizeof($arrResult)>0){
                  
                $query="UPDATE cat_certificados SET DefaultCer=1 WHERE IDCer=$IDCer";
                $this->update($query);
            }else{
                throw new Exception("No se encontró un certificado en ese rango de fechas");
            }

        }
        //REVISAR expDate
        //REVISAR STATUS
    }
    private function guardarTasas($empresaId){
      //  echo print_r($this->impuestos);
        $tasas=$this->impuestos;
        $updates='';
        $inserts='';
        $deletes='';
        $updates=array();
      
        
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
                    $inserts.="('E',".$tasa['KEYTasaTaR'];
                    $inserts.=",".$tasa['IDImp'];
                    $inserts.=", $empresaId),";
                }                
            }            
        }
        
        if (sizeof($updates)>0){
            foreach($updates as $query){
                $res=mysqlQuery($query);
                if (!$res)throw new Exception("Error al actualizar las tasas".$query);
            }            
        }
        
        if ($inserts!=''){
            $inserts = substr($inserts, 0, strlen($inserts) - 1); //<----LE BORRO LA ULTIMA COMA ",";
            $queryInsert="INSERT INTO cat_tasas_relaciones (OrigenTar,KEYTasaTaR,KEYImpTaR,KEYOrigenTaR) VALUES $inserts;";
            
            $res=mysqlQuery($queryInsert);
            if (!$res)throw new Exception("Error al guardar las tasas para la empresa:". mysql_error());
        }


        if ($deletes!=''){
            $res=$this->queryDelete($deletes);
        }
       

    }

    public function getTasas($idParam=null){
        if ($idParam==null){
            if ($this->id!=null){
                $idParam=$this->id;
            }
        }
         $query = "select IDImp,DescImp,ActivoImp,IDTaR,KEYTasaTaR FROM cat_impuestos i
            LEFT JOIN cat_tasas_relaciones r ON r.KEYImpTaR=i.IDImp AND OrigenTaR='E' AND KEYOrigenTar=$idParam
            LEFT JOIN cat_tasas t ON t.IDTasa=r.KEYTasaTaR;";
        $resImpuestos = mysqlQuery($query);
        if (!$resImpuestos)throw new Exception(mysql_error());
        $impuestos = array();
        while ($obj = @mysql_fetch_object($resImpuestos)) {
            $impuestos[] = $obj;
        }
        return $impuestos;
    }
	
    public function delete($id){
        //REVISAR SI TIENE SUCURSALES ASIGNADAS
        $queryTotSucs="SELECT count(IDSuc) as totSucs FROM  cat_sucursales WHERE KEYEmpSuc=$id";
        $result = $this->query($queryTotSucs);
        if ($result[0]['totSucs']>0 ){
            throw new Exception("<br/> La empresa no puede borrarse porque tiene sucursales asignadas");
        }

        $queryTotFacts="SELECT count(IDFac) as totFacts FROM  facturacion WHERE IDEmp=$id";
        $result = $this->query($queryTotFacts);
        if ($result[0]['totFacts']>0 ){
            throw new Exception("<br/> La empresa no puede borrarse porque tiene facturas asignadas");
        }
        
        if (parent::delete($id)){
            //Tambien borro las referencias de la tabla de PRIVILEGIOS
            $query="DELETE FROM cat_usuarios_privilegios WHERE Origen='EMP' AND KEYID=$id;";
            $result=mysqlQuery($query);
            return $result;
        }
        return false;
    }

    public function obtenerEmpresasConPermiso($userId){
        $query="
		SELECT e.id_empresa,e.nombre_fiscal,e.maneja_inventario,e.logotipo
		FROM cat_usuarios_privilegios up
		INNER JOIN cat_empresas  e ON (up.id_privilegio = e.id_empresa AND up.tipo_privilegio = 1)
		WHERE up.id_usuario = $userId AND up.tipo_privilegio = 1
		AND e.STATUS='A'
		ORDER BY e.id_empresa;";      
        $empresasYSucursales=$this->query($query);
		return $empresasYSucursales;
    }
    public function obtenerTodasLasEmpresas(){
        /*$query="SELECT 'EMP' AS Origen,IDEmp,IDEmp as IDEmpresa,ComEmp, 0 as IDSucursal,
            CONCAT_WS('-', 'EMP', IDEmp) AS IDConcat, UPPER(ComEmp) AS Nombre,RFCEmp,
            IDEmp AS IDEmpresa, 'MATRIZ' AS ComEmp, 0 AS IDSucursal, 'MATRIZ' AS NombreSucursal
            FROM cat_empresas;";*/
        $query="call spConsultaTodasEmpresas();";
        $empresas=$this->query($query);
        return $empresas;
    }

    public function getCertificados($empresaId){
        $query="SELECT IDCer,NumSerCer,StatusCer,DefaultCer,FecSolCer,FecExpCer
        FROM cat_certificados WHERE KEYEmpCer=$empresaId";
        $arrResult=$this->query($query);
        return $arrResult;
    }
    public function cambiarModoDeFacturacion($IDEmp){
    	//---------------------------------------------------------------
    	//				Obtengo el modo de facturación actual
    	//---------------------------------------------------------------
    	$query="SELECT CFDiEmp FROM cat_empresas WHERE IDEmp=$IDEmp";
    	$arrCFDiEmp=$this->query($query);
    	$CFDiEmpActual=$arrCFDiEmp[0]['CFDiEmp'];
    	
    	//---------------------------------------------------------------
    	//				Reviso las reglas que se deben cumplir
    	//---------------------------------------------------------------
    	switch($CFDiEmpActual){
    		case 0:
    			$nuevoModo=1;
    			break;
    		case 1:
    			$nuevoModo=0;
    			break;
    		default:
    			throw new Exception("modo de facturación actual desconocido");
    	}
    	//-------------------------------------------------------------------
    	//						Se actualiza el estado
    	//-------------------------------------------------------------------
    	$queryUpdate="UPDATE cat_empresas set CFDiEmp=$nuevoModo WHERE IDEmp=$IDEmp";
    	$this->update($queryUpdate);
    	return $nuevoModo;
    }

}
?>
