<?php
require_once ('eko_framework/includes/phpmailer/class.phpmailer.php');
require_once ('eko_framework/includes/phpmailer/class.smtp.php');

function enviarCorreo($subject, $body, $to = null, $cc = null, $bcc = null, $html = false, $attachment = null,$enviadoPor,$FromName='')
{
    //$pathMailer=  "phpmailer/mail_config.php";
	//include "mail_config.php";
	
	//Toma de la session la empresa y sucursal logeada luego busca el nombre del contacto en caso de existir este se agrega al email 
		
	if (empty($FromName) ){
		$idEmp=$_SESSION['Auth']['User']['IDEmp'];
		$idSuc=$_SESSION['Auth']['User']['IDSuc'];
		
		$sql="SELECT NomConEmp,NomConSuc FROM cat_empresas
		LEFT JOIN cat_sucursales ON IDSuc=$idSuc
		WHERE IDEmp=$idEmp";
		
		$res=mysql_query($sql);
		$arrNombres = mysql_fetch_array($res);
		
		//echo print_r($arrNombres );
		$FromName= empty($arrNombres['NomConSuc'])? $arrNombres['NomConEmp']: $arrNombres['NomConSuc'];	
	}
	
	
	//obtener los datos de los parametros
	$sql = "SELECT smtp_ser_par, smtp_port_par,smtp_usr_par,smtp_pass_par,smtp_nom_par FROM cat_parametros WHERE activo_par = 'S'";
	$res = mysql_query($sql);
	$Parametros = mysql_fetch_object($res);

	if($Parametros){
		if (trim($Parametros->smtp_ser_par)==''){
			throw new Exception("Por favor configure el servidor SMTP (En el módulo 'Parámetros')");
		}
		
		if (trim($Parametros->smtp_port_par)==''){
			throw new Exception("Por favor configure el puerto SMTP (En el módulo 'Parámetros')");
		}
		
		if (trim($Parametros->smtp_usr_par)==''){
			throw new Exception("Por favor configure el usuario SMTP (En el módulo 'Parámetros')");
		}
		
		if (trim($Parametros->smtp_pass_par)==''){
			throw new Exception("Por favor configure la contraseña SMTP (En el módulo 'Parámetros')");
		}
		
		if(!isset($to)){return false;}
		$mail = new phpmailer(true);
		$mail->PluginDir = "eko_framework/includes/phpmailer/";   
		//$mail->Mailer = $mailer;
		$mail->IsSMTP();		
		$mail->Host      = $Parametros->smtp_ser_par;
		$mail->Port      = $Parametros->smtp_port_par;
		$mail->SMTPAuth  = true;
		$mail->Username  = $Parametros->smtp_usr_par;
		$mail->Password  = $Parametros->smtp_pass_par;
		//$mail->SMTPDebug=true;
		if (empty($enviadoPor)){
			$mail->From      = $Parametros->smtp_usr_par;	//EMAIL	
		}else{
			$mail->From      = $enviadoPor;	//EMAIL
		}
		$FromName= empty($FromName)? $Parametros->smtp_nom_par : $FromName;
		$mail->FromName  =$FromName;	//Nombre del emisor

		if($to != null){
			foreach($to as $itemTo){
				$mail->AddAddress($itemTo);
			}
		}
		
		if($cc != null){
			foreach($cc as $itemCC){
				$mail->AddCC($itemCC);
			}
		}
		
		if($bcc != null){
			foreach($bcc as $itemBCC){
				$mail->AddBCC($itemBCC);
			}
		}
		
		$mail->Subject =utf8_decode($subject);
		$mail->Body = utf8_decode($body);
		if ($attachment != null)
		{
			foreach ($attachment as $itemAtt)
			{
				$mail->AddAttachment($itemAtt['path'], basename($itemAtt['nombre']));
			}
		}
		$mail->IsHTML($html);

		$exito = @$mail->Send();
		$intentos = 1;
		while ((!$exito) && ($intentos < 5))
		{
			sleep(5);
			//echo $mail->ErrorInfo;
			$exito = @$mail->Send();
			$intentos = $intentos+1;
		}
		
		if (!$exito)
		{
			//echo "Problemas enviando correo electr�nico a ".$valor;
			//echo "<br>".$mail->ErrorInfo;			
			throw new Exception("Error al enviar el correo.");
		}
		else
		{
			//echo "Mensaje enviado correctamente";
			return true;
		}
	}else{
		throw new Exception("No hay registros en parametros");
	}
	
}
?>
