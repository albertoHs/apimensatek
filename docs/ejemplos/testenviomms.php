<?php
require_once("mensatek.inc");
// Crear instancia Clase

$Mensatek=new cMensatek("su correo registrado en MENSATEK.COM","Su contraseña");
$variables=array(
    "Remitente"=>"MiEmpresa", //Número o nombre de la empresa/persona que envía.
    "Asunto"=>"Este es el asunto",  //Asunto del mensaje (obligatorio)
    "Destinatarios"=>"34601234567", // Destinatarios del mensaje, si es más de 1 sepárelos por punto y coma
    "SMIL"=>1, //0 formato SMIL, 1 formato enviar como adjuntos
    "adj1"=>"http://www.mensatek.com/pix/logo.jpg",
    "ini1"=>1, //Empieza en el segundo 1(no tiene sentido si SMIL=0)
    "dur1"=>5,//Duracion 5 segundos (no tiene sentido si SMIL=0)
    "adj2"=>"texto:Este es el texto de la diapositiva segunda",
    "ini2"=>5,//Empieza a mostrarse en el segundo 5 (no tiene sentido si SMIL=0)
    "dur2"=>5,//Duracion 5 segundos (no tiene sentido si SMIL=0)
    "adj3"=>"http://www.mensatek.com/pix/exito.jpg",
    "ini3"=>10,//Empieza a mostrarse en el segundo 5 (no tiene sentido si SMIL=0)
    "dur3"=>5,//Duracion 5 segundos (no tiene sentido si SMIL=0)
    "adj4"=>"texto:Y éste es el texto que vamos a enviar en nuestra última diapositiva",
    "ini4"=>15,//Empieza a mostrarse en el segundo 5 (no tiene sentido si SMIL=0)
    "dur4"=>5 //Duracion 5 segundos (no tiene sentido si SMIL=0)
);


// Ejemplo de envío
$res=$Mensatek->enviarMMS($variables);
if ($res["Res"]>0)
{
    echo "<br/>Se enviaron ".$res["Res"]." MMS y le restan ".$res["Cred"]." cr&eacute;ditos (se han utilizado ".$res["CreditosUsados"]." cr&eacute;ditos";
    echo "<br/>El resultado completo es:<pre>".print_r($res,true)."</pre>";

// Ejemplo de obtendión directa de créditos restantes en su cuenta
    echo "<br>Le restan ".$Mensatek->Creditos." cr&eacute;ditos";
}
else echo "<br>Se ha producido el error ".$res["Res"]." consulte el significado en la documentación de la API";




?>