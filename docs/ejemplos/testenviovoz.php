<?php

require_once("mensatek.inc");
// Crear instancia Clase
$Mensatek=new cMensatek("su correo registrado en MENSATEK.COM","Su contraseña");
$variables=array(
"Remitente"=>"349XXXXXXXX",  //Remitente que aparece, ATENCION: Debe ser un número de remitente validado en MENSATEK
//"Destinatarios"=>"34900000000", // Destinatarios del mensaje, si es más de 1 sepárelos por punto y coma
    "Destinatarios"=>"34912345678",
    "Mensaje"=>"Su mensaje de prueba.", //Mensaje, si se envían más de 160 caracteres se enviará en varios mensajes, si quiere añadir pausas inserte en el mensaje
                                        // (PAUSA:1) que hará una pausa de 1 segundo , el 1 puede modificarlo por el número de segundos que desee.
                                        // Puede deletrear un PIN o palabra insertando (SPELL:1234) que deletreará 1234 , cambie 1234 por el número o palabra/frase que desee
"Lenguaje"=>"es-ES:1", //voz española mujer (ver posibilidades en la documentación de la API)
"Reintentos"=>6, //Número de reintentos si no contestamn
"Intervalo"=>30, //Intervalo en minutos entre reintentos
"DetectarContestador"=>0, //Qué hacer si hay un contestador 0, por defecto, espera la señal y deja el mensaje en el contestador.
"TimeZone"=>"Europe/Madrid", //Zona horaria para los datos de FechaLimite, HoraInicioDiaria y HoraFinDiaria
"FechaLimite"=>"2050-01-01 10:00", //Fecha a partir de la cual ya no se intentará entregar (aunque no se hayan terminado los reintentos
"HoraInicioDiaria"=>"10:00",// Hora de inicio de las llamadas cada día. No se realizarán llamadas antes de esta hora. Referido a la zona horaria indicadad
"HoraFinDiaria"=>"22:00",// Hora de fin de las llamadas cada día. No se realizarán llamadas después de esta hora. Referido a la zona horaria indicadada
"Descuento"=>0,  // Si utiliza descuento o no. Descuento=0 es sin descuento. Descuento=1 añadirá (enviado desde MENSATEK.ES) al final del mensaje
"Fecha"=>"2017-09-30 10:20", //Si quiere programar el envío siempre según zona horaria de España.
"Report"=>0,  //Report de entrega. Por defecto es 0. (los reports y registros de llamadas siempre están en su panel de usuario).
"Referencia"=>"MiReferencia", //Opcional: Si quieres incluir una referencia para recibir reports en la siguiente URL
"URLReport"=>"http://www.midominio.com/getreports.php", // Opcional: URL donde recibirás, si lo deseas, los reports de cambios de estado en la llamada.
//"IVR"=>0, //Si hay menú IVR o solicitud de una OTP (one time password) o un PIN
//"MenuIVR"=>"" //Valores del menú si IVT es 1 ó 2

    /**************************
     * EJEMPLO DE ENVIO DE MENÚ IVR
*/
     "IVR"=>1,
    "MenuIVR"=>json_encode(array(
        "Locucion"=>utf8_encode("Pulse 1 para repetir el mensaje, 2 para aceptar la oferta, 3 para hablar con un comercial, 9 para darse de baja de nuestras ofertas"),
        1=>array(
            "Accion"=>1,
            "RepetirMenu"=>1
        ),
        2=>array(
            "Accion"=>2, //Accion 2 es enviar petición a un script de su web, la Accion 3 hace lo mismo pero enviando un correo a donde desee (el correo en el parámetro Valor)
            "Valor"=>"http://www.midominio.com/aceptacion.php",
            "RepetirMenu"=>0,
            "LocucionFinal"=>utf8_encode("Gracias por su interés, le enviaremos su pedido lo antes posible")
        ),
        3=>array(
            "Accion"=>4,
            "Valor"=>"349XXXXXXXXX", //teléfono destino (donde responde el comercial)
            "RepetirMenu"=>0,
        ),
        9=>array(
            "Accion"=>5,
            "RepetirMenu"=>0,
            "LocucionFinal"=>utf8_encode("Le hemos dado de baja de nuestros sistemas, lamentamos su decisión y esperamos que vuelva a contactar con nosotros para volver a recibir nuestras sensacionales ofertas.")
        )
    )),

/*
     **************************/

    /**************************
     *
     * EJEMPLO DE SOLICITUD DE PIN/OTP AL DESTINATARIO
     * Puede haber dos formas de enviar un PIN a un destinatario para validación. 1.- Enviárselo por SMS o voz para que lo introduzca en un formulario de su web o
     * 2.- Mostrar el PIN/OTP en su web, enviar a su correo electrónico (de esta forma valida de una vez el correo electrónico y el número de teléfono) y solicitar que lo
     * introduzca mediante el teclado del teléfono. Éste es un ejemplo del modelo 2. La forma 1 puede realizarse directamente sin menú IVR (simplemente enviando el mensaje con el PIN.

    "IVR"=>2,
    "MenuIVR"=>json_encode(
    array(
    "Locucion"=>utf8_encode("Por favor introduzca el PIN que le hemos enviado a su correo electrónico"),
    "AccionPIN"=>2,
    "ValorAccionPIN"=>"http://www.midominio.com/enviopin.php",
    "LongPIN"=>4,
    "LocucionFinalPIN"=>utf8_encode("Gracias por introducir el PIN, si es correcto habrá validado el proceso.")

    )
    ),

     *
     **************************/
    /**************************
     *
     * EJEMPLO DE SOLICITUD DE PUNTUACIÓN 1 a 5 A TRATO COMERCIAL/TÉCNICO, ETC...

    "Mensaje"=>utf8_encode("Muchas gracias por utilizar nuestros servicios. Nos gustaría conocer su feedback puntuando de 1 a 5 la atención que ha recibido de nuestro agente comercial."),
    "IVR"=>1,
    "MenuIVR"=>json_encode(array(
    "Locucion"=>utf8_encode("Por favor, pulse de 1 a 5 siendo 1 muy malo y 5 excelente la puntuación que le merece la atención de nuestro agente comercial"),
    1=>array(
    "Accion"=>2, //Accion 2 es enviar petición a un script de su web, gestionamos automáticamente desde este script las puntuaciones de nuestros clientes (también podría enviarse a un email)
    "Valor"=>"http://www.midominio.com/puntuacion.php",
    "RepetirMenu"=>0,
    "LocucionFinal"=>utf8_encode("Gracias por su colaboración, lamentamos que sea una puntuación tan baja, intentaremos revisar y verificar lo sucedido")
    ),
    2=>array(
    "Accion"=>2, //Accion 2 es enviar petición a un script de su web, gestionamos automáticamente desde este script las puntuaciones de nuestros clientes (también podría enviarse a un email)
    "Valor"=>"http://www.midominio.com/puntuacion.php",
    "RepetirMenu"=>0,
    "LocucionFinal"=>utf8_encode("Gracias por su colaboración, lamentamos que sea una puntuación tan baja, intentaremos revisar y verificar lo sucedido. Su opinión nos ayuda a mejorar")
    ),
    3=>array(
    "Accion"=>2, //Accion 2 es enviar petición a un script de su web, gestionamos automáticamente desde este script las puntuaciones de nuestros clientes (también podría enviarse a un email)
    "Valor"=>"http://www.midominio.com/puntuacion.php",
    "RepetirMenu"=>0,
    "LocucionFinal"=>utf8_encode("Gracias por su colaboración, Su opinión nos ayuda a mejorar")
    ),
    4=>array(
    "Accion"=>2, //Accion 2 es enviar petición a un script de su web, gestionamos automáticamente desde este script las puntuaciones de nuestros clientes (también podría enviarse a un email)
    "Valor"=>"http://www.midominio.com/puntuacion.php",
    "RepetirMenu"=>0,
    "LocucionFinal"=>utf8_encode("Gracias por su colaboración, Su opinión nos ayuda a mejorar. Estaremos encantados de volver a atenderle e intentaremos llegar a una puntuación de 5")
    ),
     4=>array(
    "Accion"=>2, //Accion 2 es enviar petición a un script de su web, gestionamos automáticamente desde este script las puntuaciones de nuestros clientes (también podría enviarse a un email)
    "Valor"=>"http://www.midominio.com/puntuacion.php",
    "RepetirMenu"=>0,
    "LocucionFinal"=>utf8_encode("Gracias por su colaboración, Su opinión nos ayuda mucho. Estaremos encantados de volver a atenderle e intentaremos seguir obteniendo la máxima puntuación.")
    ),
    )),

     *
     **************************/


    /**************************
     *
     * EJEMPLO DE VOTO POR VARIOS CANDIDATOS (2 en este ejemplo pero pueden ser, lógicamente, más)

    "Mensaje"=>utf8_encode("Te llamamos para que votes por tu candidato favorito"),
    "IVR"=>1,
    "MenuIVR"=>json_encode(array(
    "Locucion"=>utf8_encode("Vota por tu candidato preferido. Pulsa uno para votar por Manuel o 2 para votar por Sonia"),
    1=>array(
    "Accion"=>2, //Accion 2 es enviar petición a un script de su web, gestionamos automáticamente desde este script las puntuaciones de nuestros clientes (también podría enviarse a un email)
    "Valor"=>"http://www.midominio.com/voto.php",
    "RepetirMenu"=>0,
    "LocucionFinal"=>utf8_encode("Gracias por votar por Manuel, tu voto le ayudará mucho. ")
    ),
    2=>array(
    "Accion"=>2, //Accion 2 es enviar petición a un script de su web, gestionamos automáticamente desde este script las puntuaciones de nuestros clientes (también podría enviarse a un email)
    "Valor"=>"http://www.midominio.com/voto.php",
    "RepetirMenu"=>0,
    "LocucionFinal"=>utf8_encode("Gracias por votar por Sonia, tu voto le ayudará mucho.")
    ),

    )),

     *
     **************************/
);


// Ejemplo de envío
$res=$Mensatek->enviarVOZ($variables);
if ($res["Res"]>0)
{
    echo "<br/>Se enviaron ".$res["Res"]." mensajes de voz y le restan ".$res["Cred"]." cr&eacute;ditos (se han utilizado ".$res["CreditosUsados"]." cr&eacute;ditos";
    echo "<br/>El resultado completo es:<pre>".print_r($res,true)."</pre>";

// Ejemplo de obtendión directa de créditos restantes en su cuenta
    echo "<br>Le restan ".$Mensatek->Creditos." cr&eacute;ditos";
}
else echo "<br/>Se ha producido el error ".$res["Res"]." consulte el significado en la documentación de la API";




?>