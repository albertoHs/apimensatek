<?

require_once("mensatek.inc");
// Crear instancia Clase
$Mensatek=new cMensatek("su correo registrado en MENSATEK.COM","Su contraseña");

$variables=array(
"Remitente"=>"Remitente",  //Remitente que aparece, puede ser número de móvil o texto (hasta 11 caracteres)
"Destinatarios"=>"3460000000", // Destinatarios del mensaje, si es más de 1 sepárelos por punto y coma
"Mensaje"=>"Su mensaje de prueba.", //Mensaje, si se envían más de 160 caracteres se enviará en varios mensajes
"Fecha"=>"2017-01-15 10:00", // Fecha en la que se entregará el mensaje.
);


// Ejemplo de envío
$res=$Mensatek->enviar($variables);
if ($res["Res"]>0)
    echo "<br>Se enviaron ".$res["Res"]." mensajes y le restan ".$Mensatek->Creditos." cr&eacute;ditos";
else echo "<br/>El resultado de la petici&oacute;n es:<pre>".print_r($res,true)."</pre>";



// Ejemplo de obtención de reports de envío
/*
echo "<br>N&uacute;mero de reports en el mensaje:".$Mensatek->report($res["Msgid"]);
foreach ($Mensatek->Res as $res) echo "<br>Mensaje enviado en ".$res["Fecha"]." al tel&eacute;fono ".$res["Movil"]." lleg&oacute; en ".$res["Tiempo"]." segundos";
*/


?>