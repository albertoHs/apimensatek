<?php
////////////////////////////////////////////////////
// CONEXIÓN A MENSATEK POR HTTP/S DESDE PHP
// versión PHP 
// versión API 5.5
// Primera version 5 Mayo 2005
// Última modificación 27 Enero 2017
////////////////////////////////////////////////////


// El puerto por defecto es 3377, se usa para evitar influencia de proxies 
// si no puede utilizar el 3377 por problemas de firewall utilice el puerto 80

/////////////////////////////////////////////////////////////////
// Definiciones necesarias
/////////////////////////////////////////////////////////////////
//PARA CONECTARSE SIN SSL
//define('G_PUERTO',80); //Si tiene un firewall que no deja comunicaciones en el 3377, puede utilizar el puerto 80
//define('G_PUERTO',3377); // si lo desea, puede utilizar el puerto 3377
//define('G_DIR','http://api.mensatek.com');
//PATA CONECTARSE EN FORMA SEGURA SSL
define('G_PUERTO',443); //Si tiene un firewall que no deja comunicaciones en el 3378, puede utilizar el puerto 443
//define('G_PUERTO',3378); // si lo desea, puede utilizar el puerto 3378
define('G_DIR','https://api.mensatek.com');

/////////////////////////////////////////////////////////////////
// Si desea comunicaciones seguras SSL debe tener activada la extensión SSL
// y quitar los comentarios de las dos siguientes sentencias
/////////////////////////////////////////////////////////////////
// define('G_PUERTO',443);  //Si tiene un firewall que no deja comunicaciones en el 3378, puede utilizar el puerto estándar 443
// define('G_PUERTO',3378); //O utilizar, también en conexión segura, el puerto 3378
// define('G_DIR','ssl://apisms.asetecgroup.es');

 
class cMensatek
{
    var $_correo;
    var $_pass;
    var $Res=array();
    var $Creditos=0;
    var $Resultado=0;
    var $idMensaje=0;
    
    // Constructor
    function cMensatek($correo,$pass)
    {
        $this->_correo=$correo;
        $this->_pass=$pass;

    }
    

    //////////////////////////////////////////////////////
    // OBTIENE EL NÚMERO DE CRÉDITOS RESTANTES DEL USUARIO
    // DEVUELVE:
    //  Float en $this->Creditos correspondiente al número de créditos en la cuenta.
    ///////////////////////////////////////////////////////

    function creditos()
    {
       $res=$this->_conecta(array("Cred"=>0),"/v5/creditos.php");
       $this->Creditos=$res["Cred"];
       return $this->Creditos;
    }

    //////////////////////////////////////////////////////
    // ENVÍA MENSAJES A MÓVILES
    // - Valores: Array con todas o alguna de las siguientes variables
    //      Destinatarios: Móvil/Móviles al/a los que se envía el mensaje, de la forma PrefijoTelefono (Ej:346000000 ó para varios destinatarios
    //           346000000;3519760000;443450000) separados por punto y coma ';'
    //      Mensaje: Mensaje que se envía
    //      Remitente: Es el teléfono, nombre de la empresa o persona que envía (aparecerá en el teléfono destinatario como 'Mensaje de : Remitente)
    //            ATENCIÓN: Si es alfanumérico el Máximo es de 11 caracteres.
    //      Fecha: Fecha en la que queda progrmado el envío, el mensaje se enviará en esa fecha. Por defecto "" que significa enviar ahora. Formato: Año-Mes-dia hora:minuto
    //          La referencia horaria es GMT+1 (Zona horaria de España)
    //      Flash: 0=No, 1=Sí
    //      Report: 0=No, 1=Sí  (1=se envía report de entrega al correo electrónico)
    //      Descuento: 0=No, 1=Sí 
    //      EmailReport: Correo electrónico que recibirá el report. Si no se utiliza y se ha seleccionado Report=1, se enviará al correo registrado como usuario en MENSATEK.(ATENCIÓN: Debe ser un correo válido). 
    //                   ::Atención::Si desea que se envíe un correo de report personalizado con su nombre de dominio contacte con el Departamento de Soporte
    //      Descuento: Se hará un 10% de descuento (en créditos) si incluye en el mensaje (MENSATEK.ES)
    //      Ficheros: Ver especificación en el PDF se trata de un array con los ficheros incluidos en el mensaje (en forma de links.
    //              ejemplo: array("0001" => "/tmp/elfichero2.pdf","0002"=>"/tmp/elfichero2.pdf")
    //                       y el mensaje sería algo como : "Hola, te envio la factura (FILE-0001) y el contrato (FILE-0002)"
    // DEVUELVE: Un array
    //  Res: Int
    //      >0 correspondiente al número de mensajes enviados.
    //      -1 Error de autenticación
    //      -2 no hay créditos suficientes.
    //      -3 Error en los datos de la llamada
    //     -50 Intenta enviar un fichero que no encontramos. Asegúrese de escribir todo el path.
    //  Msgid: Int
    //      identificador del mensaje enviado para utilizar en el report
    //  Cred: Float
    //      número de créditos que le restan.
    ///////////////////////////////////////////////////////

    function enviar($Valores)
    {
       $res=$this->_conecta($Valores,"/v5/enviar.php");
       $this->Creditos=$res["Cred"];
       $this->idMensaje=$res["Msgid"];
       return $res;
    }

    //////////////////////////////////////////////////////
    // ENVÍA MENSAJES MMS A MÓVILES
    // - Valores: Array con todas o alguna de las siguientes variables
    //      Destinatarios: Móvil/Móviles al/a los que se envía el mensaje, de la forma PrefijoTelefono (Ej:346000000 ó para varios destinatarios
    //           346000000;3519760000;443450000) separados por punto y coma ';'
    //      El contenido se envía de una de las siguientes formas:
    //          adjN, iniN, durN (donde N es 1, 2, 3 , 4.... para diferenciar el adjunto)
    //              adjN: URL de descarga del fichero (imagen, vídeo, sonido,...)
    //              iniN: segundo en el que se empieza a mostrar/reproducir el fichero multimedia o imagen
    //              durN: duración en segundos que se mostrará.
    //              Por ejemplo: adj1=http://miservidor/carpeta/dibujo.jpg&ini1=1&dur1=5

    //          o también como archivos adjuntos:
    //          archivos=texto:texto1_del_mms|url1|url2|texto:texto2_del_mms

    //      Asunto: (obligatorio) Es el asunto del MMS (el texto que se verá primero)
    //
    // DEVUELVE: Un array
    //  Res: Int
    //      >0 correspondiente al número de mensajes enviados.
    //      -1 Error de autenticación
    //      -2 no hay créditos suficientes.
    //      -3 Error en los datos de la llamada
    //      -4 teléfono/s no válido/s
    //      -5 Tipo de Contenido no admitido o no existe uno de los archivox
    //      -6 El MMS está vacío (si se envía como SMIL) / No hay parámetro archivos si no se envía como SMIL
    //      -7 No se ha especificado el parámetro Asunto (es obligatorio)
    //      -8 En cada petición debe haber un máximo de 1000 destinatarios
    //      -9 El tamaño del MMS excede el máximo de 300kb
    //     -10 El REemitente no puede estar vacío
    //     -11 Error en los archivos multimedia incluidos
    //
    //  Msgid: Int
    //      identificador del mensaje enviado para utilizar en el report
    //  Cred: Float
    //      número de créditos que le restan.
    ///////////////////////////////////////////////////////

    function enviarMMS($Valores)
    {
        $res=$this->_conecta($Valores,"/v5/mmshttp.php");
        $this->Creditos=$res["Cred"];
        $this->idMensaje=$res["Msgid"];
        return $res;
    }

    //////////////////////////////////////////////////////
    // ENVÍA MENSAJES SMS CERTIFICADOS A MÓVILES
    // - Valores: Array con todas o alguna de las siguientes variables
    //      Destinatarios: Móvil/Móviles al/a los que se envía el mensaje, de la forma PrefijoTelefono (Ej:346000000 ó para varios destinatarios
    //           346000000;3519760000;443450000) separados por punto y coma ';'
    //      Mensaje: Mensaje que se envía
    //      Remitente: Es el teléfono, nombre de la empresa o persona que envía (aparecerá en el teléfono destinatario como 'Mensaje de : Remitente)
    //            ATENCIÓN: Si es alfanumérico el Máximo es de 11 caracteres.
    //      Fecha: Fecha en la que queda progrmado el envío, el mensaje se enviará en esa fecha. Por defecto "" que significa enviar ahora. Formato: Año-Mes-dia hora:minuto
    //          La referencia horaria es GMT+1 (Zona horaria de España

    //       VPD: Periodo de validez en días (0 a 20), se intentará la entrega durante VPD días VPH horas y VPM minutos tras lo cual, el mensaje expirará y ya no se intentará la 8 entrega.
    //       VPH: Periodo de validez en horas (0 a 23), se intentará la entrega durante VPD días VPH horas y VPM minutos tras lo cual, el mensaje expirará y ya no se intentará la entrega.
    //       VPM: Periodo de validez en minutos (0 a 59), se intentará la entrega durante VPD días VPH horas y VPM minutos tras lo cual, el mensaje expirará y ya no se intentará la entrega.
    //       Contacto: Nombre de la Empresa o Persona que envía la notificación certificada. El objeto es que el receptor pueda consultar quién le envía la notificación y pueda contactar.
    //       TelContacto: Teléfono de contacto de la Empresa o Persona que envía la notificación certificada. El objeto es que el receptor pueda consultar quién le envía la notificación y pueda contactar.
    //       Report: 0=No, 1=Sí (recibir report por correo electrónico o por peticiones web si se ha solicitado)
    //       EmailReport: Correo electrónico que recibirá el report. Si no se utiliza y se ha seleccionado Report=1, se enviará al correo registrado como usuario en MENSATEK.(ATENCIÓN: Debe ser un correo válido).
    //       Referencia: Parámetro que se utiliza como referencia para el usuario. Si se selecciona recibir el report en una URL, recibirá este parámetro en el resultado del envío.
    // DEVUELVE: Un array
    //  Res: Int
    //      >0 correspondiente al número de mensajes enviados.
    //      -1 Error de autenticación
    //      -2 no hay créditos suficientes.
    //      -3 Error en los datos de la llamada
    //  Msgid: Int
    //      identificador del mensaje enviado para utilizar en el report
    //  Cred: Float
    //      número de créditos que le restan.
    ///////////////////////////////////////////////////////

    function enviarCertificado($Valores)
    {
        $res=$this->_conecta($Valores,"/v5/enviarcert.php");
        $this->Creditos=$res["Cred"];
        $this->idMensaje=$res["Msgid"];
        return $res;
    }

    /*
     *
     *
     *
     */
    //////////////////////////////////////////////////////
    // ENVÍA MENSAJES DE VOZ A TELÉFONOS FIJOS Y MÓVILES
    // - Valores: Array con todas o alguna de las siguientes variables
    //      Destinatarios: Móvil/Móviles al/a los que se envía el mensaje, de la forma PrefijoTelefono (Ej:346000000 ó para varios destinatarios
    //           346000000;3519760000;443450000) separados por punto y coma ';'
    //      Mensaje: Mensaje que se envía, se convierte en voz y se entrega
    //      Remitente: Es el teléfono que aparece como número llamante. Por seguridad, debe ser un número validado desde los paneles de Mensatek o un número contratado en mensatek. Puede contratar números telefónicos en más de 50 países.
    //            Si no es un número validado o uno contratado en Mensatek no se realizará la llamada.
    //      Fecha: Fecha en la que queda progrmado el envío, el mensaje se enviará en esa fecha. Por defecto "" que significa enviar ahora. Formato: Año-Mes-dia hora:minuto
    //          La referencia horaria es GMT+1 (Zona horaria de España)
    //      Report: 0=No, 1=Sí  (1=se envía report de entrega al correo electrónico)
    //
    //      Descuento: 0=No, 1=Sí (se añadirá 'Enviado desde Mensatek.es)
    //      URLReport: Uri a la que se envviarán los cambios de estado de la llamada. Ver estados de llamada posibles en el PDF de especificaciones
    //      Referencia: Referencia del mensaje de VOZ, la recibirá junto con los parámentros de llamada en la URI especificada en URIReport
    //      Lenguaje: Lenguage y género de la conversión de texto a VOZ (opciones disponibles en el PDF de especificaciones)
    //      TimeZone: Zona Horaria, por defecto Europe/Madrid
    //      Reintentos: Si no contesta, número de reintentos
    //      Intervalo: Tiempo en segundos entre reintentos
    //      FechaLimite: Fecha límite de reintentos. Por defecto un mes posterior a la fecha de envío
    //      HoraInicioDiaria: Hora de inicio de las llamadas cada día referenciado a la zona horaria indicada. Por defecto 10:00 de la mañana
    //      HoraLimiteDiaria: Hora diaria de finalización de las llamadas referenciada a la zona horaria indicada (por defecto 22:00).
    //      DetectarContestador: Acción a realizar si descuelga un contestador automático
    //              0: Esperar Señal y dejar mensaje en el contestador (Por defecto y opción recomendada/más económica)
    //              1: Colgar y reintentar de nuevo las veces indicadas
    //              2: Colgar y reintentar las veces indicadas, si al final, responde un contestador (tras todos los reintentos) se esparará la señal y se dejará el mensaje.
    //      IVR:    0=> No hay menú IVR
    //              1=> Hay menú IVR (debe enviarse en ña variable MenuIVR
    //              2=> Hay solicitud de PIN (EWn MenuIVR debe enviar los parámetros)
    //      MenuIVR: JSON con los siguientes datos
    //          Para IVR=1 (Menú IVR)
    //              Locucion: Locución inicial del Menú (por ejemplo pulse 1 para repetir, 2 para enviar email, 3,....)
    //              DIGITO => (número que lanza esta opción del menú) Escribir directamente el/los dígitos, no DIGITO: dígito (ver ejemplos en testenviovoz.php)
    //                     Accion: Acción a realizar:
    //                                  1=> Repetir Mensaje
    //                                  2=> Enviar a URL (Poner URI en la opción Valor)
    //                                  3=> Enviar a Email (Poner correo en la Opción Valor)
    //                                  4=>Reenviar a número (poner número a reenviar en la opción Valor)
    //                                  5=>Dar de baja, el número destinatario quedará añadido a la lista negra
    //                                  6=>Colgar, colgar la llamada
    //                      Valor: (valor de la acción, sólo necesario para acciones 2,3 y 4)
    //                      Grabar: 0 (No) o 1 (Sí). Sólo válido para Acción: 4 (reenvío de llamada): S grabará la conversación.
    //                      RepetirMenu: 0 (no, se reproducirá la locuciónFinal y se colgará)/ 1 (Sí, se repetirá la locución del Menú y se solicitará otra pulsación).
    //                      LocucionFinal: Si se cuelga (RepetirMenu=0)  antes de colgar se reproducirá esta locución (por ejemplo: Gracias por su confirmación),
    //              DIGITO =>.... otra opción del menú
    //              ....
    //          Para IVR=2 (solicitud de PIN)
    //              Locucion: Locución inicial del Menú (por ejemplo pulse 1 para repetir, 2 para enviar email, 3,....)
    //              AccionPIN: Acción a realizar una vez obtenido el PIN:
    //                      2: Enviar a URL (valor de la URL en ValorAccionPIN)
    //                      3: Enviar a Email (Valor de Email en ValorAccionPIN).
    //              ValorAccionPIN: URI o Email para llevar a cabo la acción indicada en AccionPIN.
    //              LongPIN: Longitud en dígitos del PIN a solicitar, (por defecto 4)
    //              LocucionFinalPIN: Locución final una vez obtenido el PIN (se reproduce y se cuelga).
    //
    // DEVUELVE: Un array
    //  Res: Int
    //      >0 correspondiente al número de mensajes enviados.
    //      -1 Error de autenticación
    //      -2 no hay créditos suficientes.
    //      -3 Error en los datos de la llamada
    //      -19 Remitente no validado en Mensatek
    //      -20 Lenguaje/género no válido
    //  Msgid: Int
    //      identificador del mensaje enviado para utilizar en el report
    //  Cred: Float
    //      número de créditos que le restan.
    ///////////////////////////////////////////////////////

    function enviarVOZ($Valores)
    {
        $res=$this->_conecta($Valores,"/v5/enviarvoz.php");
        $this->Creditos=$res["Cred"];
        $this->idMensaje=$res["Msgid"];
        return $res;
    }
    
    //////////////////////////////////////////////////////
    // REPORT DE ENVÍO
    // MsgId: Identificador de mensaje devuelto por la función de envío.
    // DEVUELVE:
    //  - Entero con el Número de reports
    //  - Carga Array en $this->Res con n valores (tantos como teléfonos de destino) del tipo 
    //         $this->Res[n]["Fecha"] Fecha/Hora de envío
    //         $this->Res[n]["Movil"] Móvil destino
    //         $this->Res[n]["Tiempo"] Tiempo (en segundos) que tardó en entregarse el mensaje al móvil (normalmente entre 2 s 20 segundos si el móvil está encendido).
    //         $this->Res[n]["Resultado"] String con el resultado del envío (entregado, móvil erróneo, etc...). Se compone de:
    //
    ///////////////////////////////////////////////////////

    function report($MsgId)
    {
        $res=$this->_conecta(array("idM"=>$MsgId),"/v5/report.php");
        $this->Res=$res["Informe"];
        return count($this->Res);

    }

    function reportCertificado($MsgId)
    {
        $res=$this->_conecta(array("idM"=>$MsgId),"/v5/reportcert.php");
        $this->Res=$res["Informe"];
        return count($this->Res);

    }
    
    //////////////////////////////////////////////////////
    // SUBVENCIONAR CRÉDITOS A OTRA CUENTA DE USUARIO
    // CorreoDestino: Correo del usuario destino de los créditos.
    // Creditos: Número de crédtos a añadir al usuario  
    // DEVUELVE:
    //  - Si >0 Entero con el Número de créditos efectivamente añadidos al usuario o error
    //  - Si <0  
    //   -1 Errror de usuario
    //   -2 No hay suficientes créditos
    //   -3 Correo de destino no existe
    //   -4 Créditos <0
    ///////////////////////////////////////////////////////

    function subvencionar($CorreoDestino,$Creditos)       
    {
        $res=$this->_conecta(array("CorreoDest"=>$CorreoDestino,"Creditos"=>$Creditos),"/v5/subvencionar.php");
        
        return $res["Res"];
    }
    
    //////////////////////////////////////////////////////
    // CARGAR FICHERO (REOMENDADO PDF POR COMPATIBILIDAD DE LOS MÓVILES AUNQUE TAMBIÉN PUEDE SER WORD O EXCEL)
    // Valores: Array con las siguiente variables:
    // Nombre: Es el nombre que se le dará al fichero con extensión. Por ejemplo fichero,pdf . No debe contener más que caracteres alfanuméricos y la extensión, un máximo de 15 caractees sin contar la extensión.
    // Fichero: dichero a cargar, es el fichero con el path , por ejemplo ./fichero.pdf
    // Tipo: Es el tipo de carga. Recomendado FILES que sería enviar el fichero como si se hiciese desde un formulario, BASE64 es paracuando no se puede enviar de esa forma. En esta librería forzamos FILES
    // DEVUELVE
    // array con
    // Sobrescrito: 0 si no existía 1 si existía y el fichero se ha sobrescrito
    // Nomnbre: Nombre final del fichero cargado (elimina caracteres no alfanuméricos)
    // Res:
    //   1: Todo correcto
    //  - Si <0
    //   -1 Errror de usuario
    //   -3: Sólo las cuentas con saldo activo pueden cargar ficheros
    //-10: Ha excedido su capacidad de almacenamiento de ficheros, contacte con soporte para solicitar más espacio
    //-11: Formato de fichero no admitido, se admiten PDF, excel y word, se recomienda PDF por máxima compatibilidad en los teléfonos
    //-12: Ha añadido un fichero en el mensaje indicando que lo especificaría en BASE64 pero no ha enviado el contenido  en BASE64
    //-13: Ha añadido un fichero en el mensaje indicando que lo enviaría en en formato FORM-MULTIPART y no ha llegado ningún fichero
    //-14: Formato de imagen no admitida , puede incluir GIF, PNG y JPG
    //-15: Debe especificar Tipo FILES o BASE64
    ///////////////////////////////////////////////////////
    
    function cargaFichero($Valores)
    {
        if (!isset($Valores["Fichero"])||strlen($Valores["Fichero"])<3) return -13;
        $ext = strtolower(substr($Valores["Fichero"], strrpos($Valores["Fichero"], '.')+1));
        if ($ext!="pdf"&&$ext!="doc"&&$ext!="docx"&&$ext!="xls"&&$ext!="xlsx") return -11;

        if (!function_exists('curl_file_create'))   $Valores["Fichero"]='@'.realpath($Valores["Fichero"]).";filename:".basename($Valores["Fichero"]);
        else $Valores["Fichero"]=curl_file_create(realpath($Valores["Fichero"]),'',basename($Valores["Fichero"]));
        
        $Valores["Tipo"]="FILES";
        
        $res=$this->_conecta($Valores,"/v5/cargarfichero.php");
        
        return $res["Res"];
    }
    
    //////////////////////////////////////////////////////
    // LISTAR FICHEROS EN LA CUENTA
    // DEVUELVE
    // Array con los ficheros en caso de éxito
    // -1: Eerror de autenticación
    // -11: Debe especificar un fichero correcto
    ///////////////////////////////////////////////////////
    
    function listaFicheros()
    {
        
        $res=$this->_conecta(array(),"/v5/listarficheros.php");
        
        if ($res["Res"]==1) return($res["Ficheros"]);
        else return $res["Res"];
    }
    
    //////////////////////////////////////////////////////
    // BORRA FICHEROS EN LA CUENTA
    // Valores: Array con las siguiente variables:
    // Fichero: Fichero a borrar
    // DEVUELVE
    // 1: Borrado con éxito
    // -1: Eerror de autenticación
    // -2: No existe el fichhero
    ///////////////////////////////////////////////////////
    
    function borraFichero($Valores)
    {
        
        $ext = strtolower(substr($Valores["Fichero"], strrpos($Valores["Fichero"], '.')+1));
        if ($ext!="pdf"&&$ext!="doc"&&$ext!="docx"&&$ext!="xls"&&$ext!="xlsx") return -11;
        
        
        $res=$this->_conecta($Valores,"/v5/borrarfichero.php");
    
        return $res["Res"];
    }
    

    // Funciones internas
    function _conecta($args,$dir)
    {
        
        $args["Correo"]=$this->_correo;
        $args["Passwd"]=$this->_pass;
        $args["Resp"]="JSON";
        
        // Aseguramos que se envía en utf8
        foreach($args as $vr=>$vl) $args[$vr]=utf8_encode($vl);
    
        

        if (function_exists("curl_init"))
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,G_DIR.$dir);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //PONER A true SI SE QUIERE VER LAS TRAZAS DE CONEXIÓN SÓLO DURANTE DESARROLLO
            curl_setopt($ch, CURLOPT_VERBOSE, false);
            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_STDERR, fopen('php://output', 'w+'));
            curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
            curl_setopt($ch,CURLOPT_POST, 1);
            //curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $args);
            curl_setopt($ch, CURLOPT_PORT, G_PUERTO);

            // Puede poner esta línea a continuación o descargar el certificado de nuestra web y utilizar curl_setopt($ch, CURLOPT_CAINFO, getcwd() . "nuestrocertificadoAPI.pem"); si lo desea

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);

            curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            

            $sub=curl_exec ($ch);
            if ($sub === FALSE) {
                printf("cUrl error (#%d): %s<br>\n", curl_errno($ch),
                    htmlspecialchars(curl_error($ch)));
            }
            // Quitar el siguiente comentario para sacar por pantalla resultado directo devuelto.
            //echo "<pre>".$sub."</pre>";


            curl_close ($ch);

            $p=strpos($sub,"{");
            $sub=substr($sub,$p,strrpos($sub,"}")+1-$p);
            
            $return=json_decode($sub,true);
            
        }
        else
        {
            // Eliminar esta línea si se desea conectar por sockets aunque es recomendable utilizar curl.
            echo "\nATENCION: Intentando conectarse por sockets, debe considerar activar curl en su instalaci&oacute;n de PHP\n";
            if (G_PUERTO==443||G_PUERTO==3378)
                $fp = fsockopen ("ssl://api.mensatek.com", G_PUERTO, $errno, $errstr, 30);
            else $fp = fsockopen ("api.mensatek.com", G_PUERTO, $errno, $errstr, 30);
            if (!$fp) echo "Su sistema no permite trabajar con sockets, active la funcionalidad de sockets en PHP para utilizar la librería\n";
            else
            {

                $content = "PET=POST&".http_build_query($args)."&SEG=SSL2048";

                $string="POST ".$dir;
                fputs($fp, $string."  HTTP/1.1\r\n");
                fputs($fp, "Host: api.mensatek.com\r\n");

                fwrite($fp, "Content-Type: application/x-www-form-urlencoded\r\n");
                fwrite($fp, "Content-Length: ".strlen($content)."\r\n");
                fputs($fp, "Connection: close\r\n\r\n");
                fwrite($fp, "\r\n");
                fwrite($fp, $content);
                $sub="";
                while (!feof($fp)) $sub.=fgets($fp, 128);
                fclose($fp);
                // Quitar este comentario para imprimir en pantalla las traszas de la comunicación
                //echo "<pre>".$sub."</pre>";
                $p=strpos($sub,"{");
                $val=substr($sub,$p,strrpos($sub,"}")+1-$p);

                $return=json_decode($val,true);

            }

        } 
        return $return;
    }
    

}
?>