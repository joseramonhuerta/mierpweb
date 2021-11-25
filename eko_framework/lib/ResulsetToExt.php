<?php
class ResulsetToExt{
    public static function jsonGrid($res){
        $response=array();
        if (!$res) {
            $response['totalRows']=0;
            $response['data']=array();
            $response['success']=false;
            $response['msg']="Error al ejecutar query: " . utf8_encode(mysql_errno() . " : "  . mysql_error());            
        }else{
            $rows = @mysql_num_rows($res);
            $response['totalRows'] = $rows ;
            $response['success'] = true;
            if ($rows == 0) {               
                $response['data'] = array();                
            } else {
                $arr = array();
                while ($obj = @mysql_fetch_object($res)) {$arr[] = $obj;}
                $response['data'] = $arr;
            }
        }
        return json_encode($response);        
    }
    public static function jsonform($res) {
        $response=array();
        if (!$res) {
            $response['success']=false;
            $response['msg']="Error al ejecutar query";
            $response['data']=array();            
        }else{
            $rows = @mysql_num_rows($res);
            if ($rows == 0) {
                $response['success']=false;
                $response['data']=array();            
                $response['msg']="No se encontro el registro en la base de datos";                
            } else {
                $response['success']=true;                
                $arr = array();
                $arr = @mysql_fetch_object($res);
                if ($arr) {
                    $response['data']=$arr;                    
                } else {
                    $response['data']=array();
                    $response['msg']=$arr['mensaje'];
                    $response['success']=false;
                }
            }
        }
        return json_encode($response);        
    }
    public static function resToArray($res){
        $response = array();
        if (!$res) {
            $response['totalRows'] = 0;
            $response['data'] = array();
            $response['success'] = false;
            $response['msg'] = "Error al ejecutar query: " . utf8_encode(mysql_errno() . " : " . mysql_error());
        } else {
            $rows = @mysql_num_rows($res);
            $response['totalRows'] = $rows;
            $response['success'] = true;
            if ($rows == 0) {
                $response['data'] = array();
            } else {
                $arr = array();
                while ($obj = @mysql_fetch_object($res)) {
                    $arr[] = $obj;
                }
                $response['data'] = $arr;
            }
        }
        return $response;
    }
}

?>
