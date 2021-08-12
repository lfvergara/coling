<?php
require_once "common/libs/PHPMailer/class.phpmailer.php";


class EmailUsuario extends View {
        public function envia_email_usuario($array_datos) {
                $origen = "qManagement@tonka.com.ar";
                $fecha_desglosada = $this->descomponer_fecha();
                $nombre = "TonKa: Credenciales de acceso a qManagement";
                
                foreach ($array_datos as $clave=>$valor) {
                        $correo = $valor["usuario_correo"];

                        $gui = file_get_contents("static/mail_usuario.html");
                        $render = $this->render($valor, $gui);
                        $render = $this->render($fecha_desglosada, $render);
                        
                        $mail = new PHPMailer();
                        $mail->From = $origen;
                        $mail->FromName = $nombre;
                        $mail->AddAddress($correo);
                        $mail->IsHTML(true);
                        $mail->Subject = utf8_decode("Asignación de riesgo en qManagement");
                        $mail->Body = utf8_decode($render);
                        $mail->IsSMTP();
                        $mail->Host = '172.18.1.29';
                        $mail->Send();
                
                }                 

                print "ÉXITO";
        }
}
?>