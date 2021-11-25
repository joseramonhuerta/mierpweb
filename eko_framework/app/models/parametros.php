<?php
class Parametros extends Model{
    var $name = 'Parametros';  //ESTE VALOR DEBO OBTENERLO DINAMICAMENTE, POR LO PRONTO QUE ASI SEA PARIENTES!!
    var $useTable="cat_parametros";
    var $primaryKey = 'id_par';
    var $specific = true;
	
	var $select=array(
		'id_par','met_cos_par','activo_par','nom_ciu','nom_est','nom_pai','tem_par','tex_par','des_par',
		'dec_mon_par','dec_can_par','reg_pag_par'
	);
    public function getActivo(){
        $query="SELECT * FROM cat_parametros WHERE status='A' LIMIT 0,1";        
        $result=mysqlQuery($query);
        $datos=array();
        $datos[$this->name]=mysql_fetch_array($result,MYSQL_ASSOC);
        return $datos;
    }
    public function obtenerEmpresasConPermiso($usuario,$IDemp){
		// throw new Exception("2");
        $query="SELECT rfc,nombre_fiscal,id_empresa,maneja_inventario,e.logotipo,ifnull(e.logotipo_sucursal,0) as logotipo_sucursal ";
        $query.=" FROM cat_usuarios_privilegios p";
        $query.=" LEFT JOIN cat_empresas e ON e.id_empresa=id_privilegio";
        $query.=" WHERE id_usuario=$usuario AND id_privilegio=$IDemp AND tipo_privilegio=1;";
		// throw new Exception($query);
        $arr=$this->query($query);
        
        if (sizeof($arr)>0){
        	return $arr[0];	
        }else{
        	return array();
        }
    }

    public function getEmpresa($IDemp){
        $query="SELECT id_empresa,nombre_fiscal,logotipo,ifnull(logotipo_sucursal,0) as logotipo_sucursal ";
        $query.=" FROM cat_empresas";
        $query.=" WHERE id_empresa=$IDemp;";

        $arr=$this->query($query);
        if (sizeof($arr)>0){
        	return $arr[0];	
        }else{
        	return array();
        }
        
    }
    public function getSucursalConPermiso($usuario,$IDSuc){
        $query="SELECT RFCEmp,ComEmp,IDEmp,IDSuc,NomSuc";
        $query.=" FROM cat_usuarios_privilegios p";
        $query.=" LEFT JOIN cat_sucursales s ON s.IDSuc=p.keyid";
        $query.=" LEFT JOIN cat_empresas e ON e.IDEmp=s.KEYEmpSuc";
        $query.=" WHERE KEYUsuPriv=$usuario AND KEYID=$IDSuc AND Origen='SUC';";
      //  echo $query;
        $arr=$this->query($query);
        return $arr[0];
    }
    public function getSucursal($IDSuc){
        $query="SELECT s.id_sucursal,s.nombre_sucursal,s.logotipo, ifnull(e.logotipo_sucursal,0) as logotipo_sucursal";
        $query.=" FROM cat_sucursales s";
		 $query.=" inner join cat_empresas e on e.id_empresa = s.id_empresa";
        $query.=" WHERE s.id_sucursal=$IDSuc;";
      //  echo $query;
        $arr=$this->query($query);
         if (sizeof($arr)>0){
        	return $arr[0];	
        }else{
        	return array();
        }
    }
	
	 public function getAlmacenDefault($IDSuc){
       $query="SELECT id_almacen,nombre_almacen";
        $query.=" FROM cat_almacenes";
        $query.=" WHERE id_sucursal=$IDSuc and esdefault = 1 and status = 'A';";
      //  echo $query;
        $arr=$this->query($query);
         if (sizeof($arr)>0){
        	return $arr[0];	
        }else{
			$almacen=array();
        	$almacen['id_almacen']=0;
            $almacen['nombre_almacen']='';
			return $almacen;
        }
    }
	
	 public function getAlmacen($IDAlm){
        $query="SELECT id_almacen,nombre_almacen";
        $query.=" FROM cat_almacenes";
        $query.=" WHERE id_almacen=$IDAlm;";
      //  echo $query;
        $arr=$this->query($query);
        return $arr[0];
    }
	
	// datos de certificado para el cambio de empresa/sucursal
	public function getInfoCertificado($id, $tipo = 'EMP'){
		if ($tipo == 'EMP'){
			$query = "SELECT IDCer,NumSerCer,DATE_FORMAT(FecSolCer,'%Y-%m-%d') AS FecSol,DATE_FORMAT(FecExpCer,'%Y-%m-%d') AS FecExp,";
			$query.= "DATEDIFF(FecExpCer,NOW()) as expira ";
			$query.= "FROM cat_certificados WHERE KEYEmpCer=$id AND DefaultCer=1 AND StatusCer='A';";
		}
		if ($tipo == 'SUC'){
			$query = "SELECT IDCer,NumSerCer,DATE_FORMAT(FecSolCer,'%Y-%m-%d') AS FecSol,DATE_FORMAT(FecExpCer,'%Y-%m-%d') AS FecExp,";
			$query.= "DATEDIFF(FecExpCer,NOW()) as expira ";
			$query.= "FROM cat_certificados_sucursales LEFT JOIN cat_certificados ON(IDCer = KEYCerCerSuc) ";
			$query.= "WHERE KEYSucCerSuc=$id AND DefaultCerSuc=1 AND StatusCer='A';";
		}
		$arr = $this->query($query);
		if (sizeof($arr)==0){
			return array();
		}else{
			return $arr[0];	
		}
		
	}
	
	public function getInfoFolios($id_emp, $id_suc = 0){
	// throw new Exception("La contraseña actual es incorrecta");
		$suc_condic = ($id_suc) ? "AND KEYSucFol = $id_suc" : ""; // condicion para la sucursal
		$cad = "SELECT IDFol,SerieFol,InicialFol,FinalFol,(SigFol - 1) AS folio_actual ,KEYSucFol,IF(SigFol<=FinalFol,0,1) AS terminados 
		        FROM cat_folios WHERE KEYEmpFol = $id_emp ".$suc_condic." AND StatusFol = 'A' AND PredetFol=1 
				ORDER BY KEYSucFol,terminados,FinalFol;";
		$arr = $this->query($cad);
		if (sizeof($arr)==0){
			$cad = "SELECT IDFol,SerieFol,InicialFol,FinalFol,(SigFol - 1) AS folio_actual ,KEYSucFol,IF(SigFol<=FinalFol,0,1) AS terminados 
		        FROM cat_folios WHERE KEYEmpFol = $id_emp ".$suc_condic." AND StatusFol = 'A' 
				ORDER BY KEYSucFol,terminados,FinalFol;";
			$arr = $this->query($cad);
		}
		if (count($arr) > 0){
			$response=array();
			if ($arr[0]['terminados']){
				$response['imagen']  = 'sem_rojo';
				
			} else {  // si los folios no se han terminado, determinar el total de facturas de los ultimos 30 dias
				$cad  = "SELECT COUNT(*) AS facturados FROM facturacion 
				         WHERE FechaTimbrado>DATE_SUB(NOW(),INTERVAL 30 DAY) AND IDEmp=$id_emp and SerFol='".$arr[0]['SerieFol']."'";
				$res  = $this->query($cad);
				$f30d = $res[0]['facturados'];  // total facturas en los ultimos 30 dias
				if (($arr[0]['FinalFol'] - $arr[0]['folio_actual']) > $f30d) { // �Ajustan los folios para otros 30 dias?
					$response['imagen']  = 'sem_verde';
				} else {
					$response['imagen']  = 'sem_ama';
				}
			}			
			$response['serie'] = $arr[0]['SerieFol'];
			$response['folio'] = $arr[0]['folio_actual'];
			$response['final'] = $arr[0]['FinalFol'];
		}else{
			$response=array();
		}
		return $response;
	}
	
	public function getInfoFoliosNuevo($rfc){
		
		$cad = "SELECT Anio, Mes, Saldo FROM facturacion_edocta WHERE RFC = '$rfc' ORDER BY Folio DESC LIMIT 1;";
		$arr = $this->query($cad, 'db_mifactura');
		$saldo = 0;
		$nuevoAnio = 2013;
		$consumidosAlaFecha = 0;
		$oldMes = 0;
		$nuevoMes = 0;
		// throw new Exception('cualquier cosa');
		if (count($arr) > 0)
		{	
			$nuevoAnio = $arr[0]['Anio'];
			$nuevoMes = $arr[0]['Mes'];
			$oldMes = $nuevoMes;
			$saldo = $arr[0]['Saldo'];
			if($nuevoMes == 12){
				$nuevoAnio = $nuevoAnio +1;
				$nuevoMes = 01;
			}else{
				$nuevoMes = $nuevoMes +1;
			}
			
			$fecha = $nuevoAnio.'-'.str_pad($nuevoMes, 2, 0, STR_PAD_LEFT).'-01 00:00:00';
			
			$cad = "SELECT SUM(total) as Total FROM (
			SELECT COUNT(*) AS total FROM facturacion WHERE FechaTimbrado > '$fecha' AND NOT ISNULL(FechaTimbrado)
			UNION
			SELECT COUNT(*) AS total FROM facturacion WHERE (FechaTimbrado > '$fecha' AND NOT ISNULL(FechaTimbrado)) AND (Status = 'C' AND NOT ISNULL(FecCan))
			) dte;";
			
			$arr2 = $this->query($cad);
			if(count($arr2) > 0){
				$consumidosAlaFecha = $arr2[0]['Total'];
			}
		}
		
		$cad = "SELECT YEAR(NOW()) Anio, MONTH(NOW()) Mes";
		
		$arrFechaHoy = $this->query($cad);
		$anioActual =  $arrFechaHoy[0]['Anio'];
		$mesActual =  $arrFechaHoy[0]['Mes'];
		
		$fechaActualOld= $anioActual.'-'.$mesActual.'-01 00:00:00';
		
		if($mesActual == 1 || str_pad($mesActual, 2, 0, STR_PAD_LEFT) == 01){
			$anioActual = $anioActual - 1;
			$mesActual = 12;
		}else{
			$mesActual = $mesActual = $mesActual -1;
		}
		$fechaActualNew = $anioActual.'-'.$mesActual.'-01 00:00:00';
		
		$cad = "SELECT SUM(total) as Total FROM (
			SELECT COUNT(*) AS total FROM facturacion WHERE FechaTimbrado > '$fechaActualNew' AND NOT ISNULL(FechaTimbrado) 
					AND FechaTimbrado < '$fechaActualOld'
			UNION
			SELECT COUNT(*) AS total FROM facturacion WHERE (FechaTimbrado > '$fechaActualNew' AND NOT ISNULL(FechaTimbrado)) AND (Status = 'C' AND NOT ISNULL(FecCan)) 
					AND FechaTimbrado < '$fechaActualOld'
			) dte;";
		
		$arrSumaTotalVR = $this->query($cad);
		$sumaTotalVR = $arrSumaTotalVR[0]['Total'];
		
		
		$response=array();			
		$response['Mes'] = $oldMes;
		$response['NuevoMes'] = $nuevoMes;
		$response['ConsumidosAlaFecha'] = $consumidosAlaFecha;
		$response['Saldo'] = $saldo;
		$response['SumaTotalVR'] = $sumaTotalVR;
		
		return $response;
	}
	
}

?>
