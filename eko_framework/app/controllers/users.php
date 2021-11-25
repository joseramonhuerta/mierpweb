<?php
require ('eko_framework/app/models/user_corp.php');

class Users extends ApplicationController {
    var $uses=array('UserMaster','UserCorp','UsuarioCorporativo');
    var $paginate = array(
                'limit' => 25,                
            );

    public function getUsers() {
    
        try {
            $limit = (empty($_POST['limit'])) ? 20 : $_POST['limit'];
            $start = (empty($_POST['start'])) ?  0 : $_POST['start'];
            $filtro = (empty($_POST['filtro'])) ?  '': $_POST['filtro'];
            
            if ($_POST['filtrarActivos']=='true'){
                $filtrarActivos=true;
            }else{
                $filtrarActivos=false;
            }
            
            $UserCorp=new UserCorp();
            $response = $UserCorp->getUsersGrid($start,$limit,$filtro,$filtrarActivos);
        } catch (Exception $e) {
            $response['totalRows'] = $totalRows;
            $response['succes'] = false;
            $response['msg'] = $e->getMessage();
        }

        echo json_encode($response);
        
    }
    public function setStatus(){
        $IDUsu=$_POST['IDUsu'];
        $StatusUsu=$_POST['StatusUsu'];
        $query="UPDATE cat_usuarios SET StatusUsu='$StatusUsu' WHERE IDUsu=$IDUsu";
        $result=mysqlQuery($query);
        $response=array();
        if (!$result){
            $response['success']=false;
            $response['msg']="error al actualizar el estado del usuario:".mysql_error();
        }else{
            $response['success'] = true;
            $estado='';
            if ($StatusUsu=="I"){
                $estado="Desactivado";
            }else{
                $estado="Activado";
            }
            $response['msg'] =array('titulo'=>'Usuarios','mensaje'=>"El usuario ha sido $estado") ;
        }
        
        echo json_encode($response);
    }
    public function nuevo(){
        
    }
    
    public function getContra(){        
        $IDUsu=$_POST['usuario'];
        if ($IDUsu==''){
            $response['success']= false;
            $response['msg']='No se recibio el identificador del usuario';            
        }else{
            
            $logedUserId=$_SESSION['Auth']['User']['IDUsu'];
            $logedType=$_SESSION['Auth']['User']['AdminUsu'];
            $super=$_SESSION['Auth']['User']['super'];
            $verContra=false;
            $filtro='';
            if ($logedUserId==$IDUsu || $super==true){
                $verContra=true;

            }else if($logedType==1 ){
                $filtro=" AND AdminUsu=0";
                $verContra=true;
            }

            $query = "SELECT AES_DECRYPT(passUsu,'asdf') as passUsu FROM cat_usuarios WHERE IDUsu=$IDUsu $filtro";
            $res = mysqlQuery($query);
            $contra = mysql_fetch_array($res, MYSQL_ASSOC);
            $response['success']= true;
            $response['data']= $contra;
        }
        
        echo json_encode($response);
    }
    function validarEmailUnico(){
        $ema=$_POST['email'];
        
        $query="SELECT COUNT(IDUsu) FROM cat_usuarios WHERE UserUsu='$ema'";
        $res=mysqlQuery($query);
        $datos=mysql_fetch_array($res);

        $disponible=($datos[0]>0)?false:true;
        
        
            $response['success']=$disponible;
        
        echo json_encode($response);
        
    }
    public function guardar(){
        //LOS QUERYS DE ESTA FUNCION DEBERIN ESTAR DENTRO DE DOS PROCEDIMIENTOS ALMACENADOS, UNO PARA LA BDD cfd_master y otro para la bdd cfd_pacifico

        $pass = $_POST['PassUsu'];        
        //-------------------------------------------------------------------------------------------
        try{
        //-------------------------------------------------------------------------------------------
        $IDCor = $_SESSION['Auth']['User']['IDCor'];
        $usuario=$_SESSION['Auth']['User']['IDUsu'];
		
        if($_POST['IDUsu']==''){ //REGISTRO NUEVO
			// echo "Nuevo";exit;
            //SI EL EMAIL YA EXISTE, SOLO REGISTRO LA FECHA DE LA MODIFICACIOn
            $UserUsu=$_POST['UserUsu'];
            $query="SELECT COUNT(IDUsr) cont FROM cat_usuarios WHERE IDUsr='$UserUsu'";
            $res=mysqlQuery($query,DB_MASTER);
            if (!$res)throw new Exception("Error al iniciar el guardado");
            $cont=mysql_fetch_array($res,MYSQL_ASSOC);
            
            if ($cont['cont']==0){                
                $query = "INSERT INTO cat_usuarios (IDUsr,ModFecha) VALUES ('";
                    $query.=$_POST['UserUsu'] . "',";
                                        
                    $query.="now())";

                    $res = mysqlQuery($query, DB_MASTER);
                    if (!$res)throw new Exception("no se guardo el usuario en el master: " );
            }else{			
                $query="UPDATE cat_usuarios set ModFecha=now() WHERE IDUsr='$UserUsu';";				
                $res=mysqlQuery($query,DB_MASTER);

                 if (!$res)throw new Exception("Error al actualizar la cuenta.".$query);
            }
            


            

            $query="INSERT INTO cat_usuarios_corporativos (KEYUsr,KeyCor,PassUsr,AdminUsr) VALUES ('";
            $query.=$_POST['UserUsu']."',$IDCor,AES_ENCRYPT('$pass','asdf'),".$_POST['AdminUsu'].");";
                                    
            $res=mysqlQuery($query,DB_MASTER);
            if (!$res)throw new Exception("no se guardo la relacion del usuario en el master: ");


            $query="INSERT INTO cat_usuarios SET ";
                $query.="UserUsu='".$_POST['UserUsu']."'";                
                $query.=",NomUsu='".$_POST['NomUsu']."'";
                $query.=",PassUsu=AES_ENCRYPT('$pass','asdf')";
                $query.=",TelUsu='".$_POST['TelUsu']."'";
                $query.=",CelUsu='".$_POST['CelUsu']."'";
                $query.=",StatusUsu='".$_POST['StatusUsu']."'";
                $query.=",AdminUsu=".$_POST['AdminUsu']."";
                $query.=",AddUsuario=$usuario";
                $query.=",AddFecha=now()";
                $query.=",ModUsuario=$usuario";
                $query.=",ModFecha=now()";
                $query.=",temUsu='".$_POST['temUsu']."'";
                $query.=",forUsu='".$_POST['forUsu']."';";

            $res=mysqlQuery($query);
            
            if (!$res)throw new Exception("no se guardo al usuario:");
            
            $id=mysql_insert_id();

        }else{ //REGISTRO EXISTENTE            
			
            $IDUsu = $_POST['IDUsu'];
            $id=$IDUsu ;
           
            $query="UPDATE cat_usuarios SET ";                
                $query.="NomUsu='".$_POST['NomUsu']."'";
                if ($_POST['PassUsu']!=''){
                    $query.=",PassUsu=AES_ENCRYPT('$pass','asdf')";
                }                
                $query.=",TelUsu='".$_POST['TelUsu']."'";
                $query.=",CelUsu='".$_POST['CelUsu']."'";
                $query.=",StatusUsu='".$_POST['StatusUsu']."'";
                $query.=",AdminUsu=".$_POST['AdminUsu']."";                                
                $query.=",ModUsuario=$usuario";
                $query.=",ModFecha=now()";
                $query.=",temUsu='".$_POST['temUsu']."'";
                $query.=",forUsu='".$_POST['forUsu']."' WHERE IDUsu=$IDUsu";
			
            $res=mysqlQuery($query);
            if (!$res)throw new Exception("no se actualizo el usuario : ".$query);
            
            $query="SELECT UserUsu FROM cat_usuarios WHERE IDUsu=$IDUsu";
            
            $res=mysqlQuery($query);
            if (!$res)throw new Exception("no encontre el email del usuario: ");
            $email=mysql_fetch_array($res,MYSQL_ASSOC);
          

            $corp=$_SESSION['Auth']['User']['IDCor'];
            $query="UPDATE cat_usuarios_corporativos SET ";
            $query.="AdminUsr=".$_POST['AdminUsu'].",";
            /*
            if ($_POST['PassUsu']!=''){
                $query.="PassUsr= AES_ENCRYPT('$pass'),";
            }*/
            $query.="ModFecha=now() WHERE KEYUsr='".$email['UserUsu']."' AND KEYCor=$corp";
			
            $res=mysqlQuery($query,DB_MASTER);
            if (!$res)throw new Exception("no se actualizo el usuario en el Master: ".mysql_error());

            if ($_POST['PassUsu']!=''){
                $user=$email['UserUsu'];
                

                $query="UPDATE cat_usuarios_corporativos set PassUsr= AES_ENCRYPT('$pass','asdf'),AdminUsr=".$_POST['AdminUsu']." WHERE KEYUsr='$user' AND KEYCor=$corp";
                
                $res=mysqlQuery($query,DB_MASTER);
                if (!$res)throw new Exception("no se actualizó la contraseña!");
            }

            
            
        }
        
        }catch (Exception $e){
            $response['success']=false;
            $response['msg']=$e->getMessage();
            echo json_encode($response);
            exit;
        }
       // $id = $this->UserCorp->id;

        
        
        //$datos=$this->UserCorp->read();;


        try{
            $query = "SELECT * FROM cat_usuarios WHERE IDUsu=$id";
            $res = mysqlQuery($query);
            $datos['UserCorp'] = mysql_fetch_array($res, MYSQL_ASSOC);
			
            $query = "DELETE FROM cat_usuarios_privilegios WHERE KEYUsuPriv=" . $id;
            $res = mysqlQuery($query);

            /*****************************************************************************************************************
             *                  AHORA SE ALMACENAN LOS PERMISOS A EMPRESAS Y SUCURSALES
             ***************************************************************************************************************** */

            $nodos = json_decode(stripslashes($_POST['privEmpSuc']), true);

            $user = array();

            $values = '';
			
			
            foreach ($nodos as $nodo) {
                $params = explode('-', $nodo['nodo']);
                $origen = $params[0];
                $key = $params[1];
                $values .= ",($id,$key ,'$origen')";
            }
            if (strlen($values) > 0) {
                $values = substr($values, 1);
                $query = "INSERT INTO cat_usuarios_privilegios (KEYUsuPriv,KEYId,Origen ) VALUES $values";
				
                $res = mysqlQuery($query);
                if (!$res
                    )throw new Exception(mysql_error() . $query);
            }
			//----------------------------------------------------------------------------
            $nodos = json_decode(stripslashes($_POST['privMods']), true);
            $values = '';
            foreach ($nodos as $nodo) {
                $params = explode('-', $nodo['nodo']);
                $origen = $params[0];
                $key = $params[1];
                $values .= ",($id,$key ,'$origen')";
            }
            if (strlen($values) > 0) {
                $values = substr($values, 1);
                $query = "INSERT INTO cat_usuarios_privilegios (KEYUsuPriv,KEYId,Origen ) VALUES $values";

                $res = mysqlQuery($query);
                if (!$res
                    )throw new Exception(msyql_error() . $query);
            }
			//----------------------------------------------------------------------------
			$nodos = json_decode(stripslashes($_POST['almacenes']), true);
            $values = '';
            foreach ($nodos as $nodo) {
                $idAlmacen = $nodo['IDAlm'];
               
                $values .= ",($id,$idAlmacen ,'ALM')";
            }
            if (strlen($values) > 0) {
                $values = substr($values, 1);
                $query = "INSERT INTO cat_usuarios_privilegios (KEYUsuPriv,KEYId,Origen ) VALUES $values";

                $res = mysqlQuery($query);
                if (!$res
                    )throw new Exception(msyql_error() . $query);
            }
            //-----------------------------------------------------------------------------
			 $sqlAlmacenes="SELECT IDAlm idAlm,KEYEmpAlm fk_empresa,KEYSucAlm fk_sucursal,DesAlm nomAlm,if (KEYID is NULL,0,1) as permiso 
			FROM cat_almacenes
			LEFT JOIN cat_usuarios_privilegios ON KEYUsuPriv=$usuario AND KEYID=IDAlm AND Origen='ALM';";
			$model=new Model();
			$arrAlmacenes=$model->select($sqlAlmacenes);
			
			$response['almacenes']=$arrAlmacenes;
            $datos['UserCorp']['PassUsu'] = '';
            $datos['UserCorp']['passwordConfirm'] = '';
            $response['data'] = $datos['UserCorp'];
            $response['success'] = true;
            $response['msg'] =array('titulo'=>'Usuarios','mensaje'=>"Información del Usuario Almacenada Satisfactoriamente") ;
        }catch(Exception $e){
            $response['data'] = array();
            $response['success'] = false;
            $response['msg'] = $e->getMessage();
        }

        echo json_encode($response);
        
    }
    public function getUser(){        
        $IDUsu=(isset($_POST['IDUsu']))? $_POST['IDUsu'] : 0;

        $UserCorp=new UserCorp();        
        $user=$UserCorp->getById($IDUsu);              
        
        $sqlAlmacenes="SELECT IDAlm idAlm,KEYEmpAlm fk_empresa,KEYSucAlm fk_sucursal,DesAlm nomAlm,if (KEYID is NULL,0,1) as permiso 
		FROM cat_almacenes
		LEFT JOIN cat_usuarios_privilegios ON KEYUsuPriv=$IDUsu AND KEYID=IDAlm AND Origen='ALM';";
        
        $arrAlmacenes=$UserCorp->select($sqlAlmacenes);
        
        $response=array();
        $response['success']=true;
        $response['data']=$user['UserCorp'];
        $response['almacenes']=$arrAlmacenes;
        echo json_encode($response);
    }
    
    public function delete(){

        $response = array();
        try{
            //$IDUsu = $_POST['IDUsu'];
            $IDUsu =$_POST['IDUsu'];
            //$this->UserCorp->id=$IDUsu ;
            $query="SELECT UserUsu FROM cat_usuarios WHERE IDUsu=$IDUsu";
            $res=mysqlQuery($query);
            if (!$res)throw new Exception("no encontre el email del usuario: ");
            $email=mysql_fetch_array($res,MYSQL_ASSOC);
            $UserUsu=$email['UserUsu'];
            //-------------------------------------------------------------------------------------------
            $IDCor = $_SESSION['Auth']['User']['IDCor'];
            $query = "DELETE FROM cat_usuarios_corporativos WHERE KEYUsr='$UserUsu' AND KEYCor=$IDCor";
            $res = mysqlQuery($query,DB_MASTER);          //<------------- ELIMINO LA RELACION CON EL CORPORATIVO
            if(!$res)throw new Exception(mysql_error().$query.$_SESSION['Auth']['User']['IDCor']);
            //-------------------------------------------------------------------------------------------
            //SI EL USUARIO NO TIENE MAS RELACIONES, SE BORRA DEL SISTEMA
            $query="SELECT COUNT(KEYUsr) count FROM cat_usuarios_corporativos WHERE KEYUsr='$UserUsu'";
            
            $res=mysqlQuery($query,DB_MASTER);
            if (!$res)throw new Exception("Error al buscar las relaciones de la cuenta");
            $cont=mysql_fetch_array($res,MYSQL_ASSOC);
         
            if ($cont['count']==0){

                $query = "DELETE FROM cat_usuarios WHERE IDUsr='$UserUsu'";
                $res = mysqlQuery($query, DB_MASTER);        // <---------------ELIMINO AL USUARIO DEL MASTER
                if (!$res)throw new Exception(mysql_error());
            }           
            
            $query = "DELETE FROM cat_usuarios WHERE IDUsu=$IDUsu";
            $res = mysqlQuery($query);          //<------------ELIMINO AL USUARIO DEL CORPORATIVO
            if(!$res)throw new Exception(mysql_error());            

            $query="DELETE FROM cat_usuarios_privilegios WHERE KEYUsuPriv=$IDUsu";
            $res = mysqlQuery($query);          //<------------ELIMINO LOS PERMISOS DEL USUARIO
            if(!$res)throw new Exception(mysql_error());
            
            $response['success'] = true;
            $response['msg'] =array('titulo'=>'Usuarios','mensaje'=>'Usuario eliminado');
            $response['data']['IDUsu']=$IDUsu;
        }catch(Exception $e){
            $response['success'] = false;
             $response['msg'] = 'No pudo eliminarse el usuario. '.$e->getMessage();
        }

        
        echo json_encode($response);
   }
    public function getEmpSucTree(){
      if (!isset($_POST['node']))throw new Exception("El servidor no recibió el identificador del nodo");
        $node=$_POST['node'];

        $params = explode('-', $node);
        $tipo = $params[0];
        $key = $params[1];


        /***************************************
         * ESTO ESTA MAL, EL ID DEL USUARIO DEBE SER DEL QUE SE ESTA EDITANDO
         * AQUI ESTO PASANDO EL ID DEL USUARIO LOGEADO
        ****************************************/
        $IDUsu=$_POST['IDUsu'];


        if ($IDUsu == '') $IDUsu=0; //ESTOY SUPONIENDO QUE NUNCA HABRA UN USUARIO CON ID=0;
        if ($tipo=='xnode'){
            
                $query = "SELECT concat('emp-',IDEmp) as id,ComEmp as text,
                if (KEYId IS NULL,0,1 ) as checked,0 as fk_sucursal,IDEmp as fk_empresa
                FROM cat_empresas e
                LEFT JOIN cat_usuarios_privilegios p ON p.Origen='EMP' AND p.KEYId=IDEmp AND KEYUsuPriv=$IDUsu";                        
        }else if ($tipo=='emp'){              

                $query = "SELECT concat('suc-',IDSuc) as id,NomSuc as text,
                if (KEYId IS NULL,0,1 ) as checked,IDSuc as fk_sucursal,KEYEmpSuc as fk_empresa
                FROM cat_sucursales s
                LEFT JOIN cat_usuarios_privilegios p ON p.Origen='SUC' AND p.KEYId=IDSuc AND KEYUsuPriv=$IDUsu
                WHERE KEYEmpSuc=$key";
            
            
         
        }else{
            return;
        }
      //
       
        $result = mysqlQuery($query);
    
        $arr = array();
        
        if (!$result )echo mysql_error();

        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            if ($tipo=='emp'){
                $row['leaf']=true;
            }else{
                $row['leaf']=false;
            }
            ($row['checked']=='1')?$row['checked']=true:$row['checked']=false;
            $arr[] = $row;
        }
        
        echo json_encode($arr);
        
    }
     public function getModsTree() {
  
         if (!isset($_POST['node']))throw new Exception("El servidor no recibió el identificador del nodo");
        $node = $_POST['node'];

        $params = explode('-', $node);
        $tipo = $params[0];
        $key = $params[1];

        $IDUsu = $_POST['IDUsu'];

        /*$UserCorp=new UserCorp();
        $arboles=$UserCorp->getArbolDeModulosPermitidos($IDUsu);
        echo json_encode($arboles);
        return;*/

        if ($IDUsu == '')$IDUsu = 0; //ESTOY SUPONIENDO QUE NUNCA HABRA UN USUARIO CON ID=0;
            if ($tipo == 'xnode') {

            $query = "SELECT concat('mod-',m.IDMod) as id,m.DescMod as text,CONCAT(CONCAT('images/iconos/',icono),'.png') as icon,";
            $query.="if ((select count(IDMod) as numero from cat_modulos where KeyPadMod=m.IDMod)=0, true, '') as leaf,";
            $query .="if (KEYId IS NULL,0,1 ) as checked ";
            $query .="FROM cat_modulos m ";
            $query .=" LEFT JOIN cat_usuarios_privilegios p ON p.Origen='MOD' AND p.KEYId=m.IDMod AND KEYUsuPriv=$IDUsu";
            $query .=" WHERE KEYPadMod=0";

        } else if ($tipo == 'mod') {

            $query = "SELECT concat('mod-',m.IDMod) as id,m.DescMod as text,CONCAT(CONCAT('images/iconos/',icono),'.png') as icon,";
            $query.="if ((select count(IDMod) as numero from cat_modulos where KeyPadMod=m.IDMod)=0, true, '') as leaf,";
                $query.= "if (KEYId IS NULL,0,1 ) as checked ";
                $query.= " FROM cat_modulos m ";
                $query.= " LEFT JOIN cat_usuarios_privilegios p ON p.Origen='MOD' AND p.KEYId=m.IDMod AND KEYUsuPriv=$IDUsu";
                $query.= " WHERE KEYPadMod=$key";

        } else {
            return;
        }
        

        $result = mysqlQuery($query);

        $arr = array();

        if (!$result){
            echo mysql_error().$query;
            exit;
        }
        
        while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
            
            if ($row['leaf']==1){
                $row['leaf']=true;
            }else{
                $row['leaf']=false;
            }
            ($row['checked'] == '1') ? $row['checked'] = true : $row['checked'] = false;            
            $arr[] = $row;
        }
        
        
        echo json_encode($arr);
    }
}


