<?

require_once("mensatek.inc");
// Crear instancia Clase
$Mensatek=new cMensatek("su correo registrado en MENSATEK.COM","Su contraseña");
$variables=array(
"Fichero"=>"./ejemplopdf.pdf", //array con los ficheros incluidos en el mensaje (que se enviarán como links)
"Nombre"=>"Renombrado.pdf"
    
);


// Ejemplo de carga del fichero
$res=$Mensatek->cargaFichero($variables);

echo "<br>Resultado de la carga de fichero:".$res;


// Ahora listo los ficheros que tengo (no hace falta pero para ver funcionamiento).
$res=$Mensatek->listaFicheros();
echo "<br>Resultado de listado de ficheros:<pre>".print_r($res,true)."</pre>";

//Puedo recorrer el array generado: foreach ($res as $i=>$Nombre) echo "<br/>Fichero:".$Nombre;

// Por último, envío el mensaje con el fichero
$variables=array(
    "Remitente"=>"LaEmpresa",  //Remitente que aparece, puede ser número de móvil o texto (hasta 11 caracteres)
    "Destinatarios"=>"34600000004", // Destinatarios del mensaje, si es más de 1 sepárelos por punto y coma
    "Mensaje"=>"Estimado Sr.Gonzalez, le notificamos la deuda pendiente con nuestra empresa por valor de ...... Como referencia, le enviamos, a continuación, el contrato suscrito entre las partes el 22/12/2016 (File:Renombrado.pdf)", //Mensaje, si se envían más de 160 caracteres se enviará en varios mensajes
    "Links"=>1, //Indicamos al sistema que hemos incluido ficheros, imágenes, links cortos, etc...
    "Fecha"=>"2017-01-10 10:00", // Fecha en la que se entregará el mensaje.
    "Contacto"=>"Empresa que envia el mensaje",
    "TelContacto"=>"91234567",
    "CifContacto"=>"B01234567"
);

$res=$Mensatek->enviarCertificado($variables);
if ($res["Res"]>0)
    echo "<br>Se enviaron ".$res["Res"]." mensajes y le restan ".$Mensatek->Creditos." cr&eacute;ditos";
else echo "<br/>El resultado de la petici&oacute;n es:<pre>".print_r($res,true)."</pre>";


?>