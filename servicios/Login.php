<?php
    $con = mysqli_connect("localhost", "root", "ramon", "erp_master");
    
    $username = $_POST["username"];//"prueba@hotmail.com";//
    $password = $_POST["password"];//"prueba"; //

    $statement = mysqli_prepare($con, "SELECT r.id_usuario,if (u.esadmin=0,r.esadmin,u.esadmin ) as esadmin,u.nombre_usuario FROM cat_usuarios_corporativos r
        LEFT JOIN cat_usuarios u ON r.id_usuario=u.id_usuario
        LEFT JOIN cat_corporativos c ON r.id_corporativo=c.id_corporativo
        WHERE  r.id_corporativo=1 AND r.id_usuario=? AND r.pass=AES_ENCRYPT(?,'asdf')");

    mysqli_stmt_bind_param($statement, "ss", $username, $password);
    mysqli_stmt_execute($statement);
    
    mysqli_stmt_store_result($statement);
    mysqli_stmt_bind_result($statement, $id_usuario, $esadmin, $nombre_usuario);
    
    $response = array();
    $response["success"] = false;  
    
    while(mysqli_stmt_fetch($statement)){
        $response["success"] = true;  
        $response["nombre_usuario"] = $nombre_usuario;
       
    }
    
    echo json_encode($response);
?>