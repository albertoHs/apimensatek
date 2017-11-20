<?php
require_once("mensatek.inc");
// Crear instancia Clase
$Mensatek=new cMensatek("su correo registrado en MENSATEK.COM","Su contraseña");
$variables=array(
"Remitente"=>"Remitente",  //Remitente que aparece, puede ser número de móvil, fijo (formato internacional +346XXXXXXX) o texto (hasta 11 caracteres)
"Destinatarios"=>"34600000000", // Destinatarios del mensaje, si es más de 1 sepárelos por punto y coma
"Mensaje"=>"Su mensaje de prueba.", //Mensaje, si se envían más de 160 caracteres se enviará en varios mensajes
"Report"=>0,  //Report de entrega al correo electrónico por defecto
"Descuento"=>0,  // Si utiliza descuento o no. Descuento=0 es sin descuento. Descuento=2 añadirá (MENSATEK.ES) al final del mensaje, Descuento=1 pondrá MENSATEK.ES como remitente
"Fecha"=>"2015-09-30 10:20" //Si quiere programar el envío
);


// Ejemplo de envío
$res=$Mensatek->enviar($variables);
if ($res["Res"]>0)
{
    echo "<br/>Se enviaron ".$res["Res"]." mensajes y le restan ".$res["Cred"]." cr&eacute;ditos (se han utilizado ".$res["CreditosUsados"]." cr&eacute;ditos";
    echo "<br/>El resultado completo es:<pre>".print_r($res,true)."</pre>";

// Ejemplo de obtendión directa de créditos restantes en su cuenta
    echo "<br>Le restan ".$Mensatek->Creditos." cr&eacute;ditos";
}
else echo "<br>Se ha producido el error ".$res["Res"]." consulte el significado en la documentación de la API";




?>