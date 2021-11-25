<?php 
	include "Validador.php"; 
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
</head>
<body>
	<?php
		$Validador = new Validador();
		$nombreXML = 'AAA010101AAA_TEST_2_121205';
		$validaciones = $Validador->validarXML($nombreXML.'.xml');
		verArreglo($validaciones);		
		//file_put_contents('cadena'.$nombreXML.'.txt',$validaciones["validaciones"]["cadena_original"]);
		
		/*$cadenaOriginal = $validaciones["validaciones"]["cadena_original"];
		$certificado = 'Certificados/GOGA470501G93.cer';
		$llavePrivada = 'Certificados/GOGA470501G93.key';
		$contra = 'ebsaamdist10joan';
		/*$certificado = 'Certificados/AAA010101AAA.cer';
		$llavePrivada = 'Certificados/AAA010101AAA.key';
		$contra = 'a0123456789';
		
		$sello = crearSelloDigital($certificado,$llavePrivada,$contra,$cadenaOriginal);
		echo $sello;*/

		function verArreglo($array){
			echo "<pre>";
			print_r($array);
			echo "</pre>";
		}
		
		function crearSelloDigital($CerFile,$KeyFile,$PassKey,$CadenaOriginal){
			//verificar que existan los PEM para los archivos CER y KEY del cliente sino crearlos
			//if(!file_exists($Cliente->FileCer)){
			$CerPem = convertirCerAPem($CerFile);
			
			//}
			//if(!file_exists($Cliente->FileKey)){
			$KeyPem = convertirKeyAPem($KeyFile,$PassKey);
			//}        
			$pkeyid = openssl_get_privatekey(file_get_contents($KeyPem));//Obtienes la llave privada
			
			openssl_sign($CadenaOriginal, $crypttext, $pkeyid);//Firmas la cadena original
			openssl_free_key($pkeyid);
			$sello = base64_encode($crypttext); // lo codifica en formato base64    	    	
				
			$pubkeyid = openssl_get_publickey(file_get_contents($CerPem)); 
							
			$ok = openssl_verify($CadenaOriginal, $crypttext,$pubkeyid);		
			if ($ok == 1) {    		
				return $sello;
			}else{    		
				die("Error 118: No se pudo generar el sello digital.");
			}
		}
		
		function convertirCerAPem($cer){			
			shell_exec("openssl x509 -inform DER -outform PEM -in $cer -pubkey -out $cer.pem");			
			return "$cer.pem";
		}
		
		function convertirKeyAPem($key,$contra){
			shell_exec("openssl pkcs8 -inform DER -in $key -passin pass:$contra -out $key.pem");
			return "$key.pem";
		}
	?>
</body>
</html>