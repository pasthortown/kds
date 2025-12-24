<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/Exception.php';
require 'PHPMailer/PHPMailer.php';
require 'PHPMailer/SMTP.php';


if (isset($_POST["enviarMail"])) {


//     send.rst_id = $("#txtRestaurante").val();
//    send.host = configuracionSMTP.host;
//    send.puerto = configuracionSMTP.puerto;
//    send.rst_id = configuracionSMTP.correo;
//    send.password = configuracionSMTP.password;
//    send.nombreUsuario = configuracionSMTP.nombreUsuario;
//    send.htmlCorreo = htmlCorreo;
//    send.emailDestino = emailDestino;


    $rst_id = $_POST["rst_id"];
    $host = $_POST["host"];
    $puerto = $_POST["puerto"];
    $correo = $_POST["correo"];
    $password = $_POST["password"];
    $nombreUsuario = $_POST["nombreUsuario"];
    $htmlCorreo = $_POST["htmlCorreo"];
    $emailDestino = $_POST["emailDestino"];
    $asunto = $_POST["asunto"];

    $mail = new PHPMailer(true);
    try {
        //Server settings
        $mail->SMTPDebug = 0; // SMTP::DEBUG_SERVER;                       
        $mail->isSMTP();                                            
        $mail->Host = $host; //'smtp.live.com';                    
        $mail->SMTPAuth = true;                                   
        $mail->Username = $correo;                     
        $mail->Password = $password;                            
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         
        $mail->Port = $puerto; //25;    
        $mail->CharSet = 'UTF-8';                                 
        //Recipients
        $mail->setFrom($correo, $nombreUsuario); // 'Mychael castro KFC'); //quien  envuia envia.
        $mail->addAddress($emailDestino, $emailDestino);      
        
// Content
        $mail->isHTML(true);                                 

        $mail->Subject = $asunto;
        $mail->Body = $htmlCorreo;

        $mail->send();

        $array = [
            "message" => 'Mensaje enviado.',
            "statusCode" => 200,
        ];
        print (json_encode($array));
    } catch (Exception $e) {

        $array = [
            "message" => $mail->ErrorInfo,
            "statusCode" => 500,
        ];
        print (json_encode($array));
    }
}
    