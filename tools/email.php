<?php
require_once "common/libs/PHPMailer/class.phpmailer.php";


class EmailHelper extends View {
        public function envia_comprobante($obj_matriculado, $obj_matricula, $obj_cuentacorrientematriculado, $obj_comprobantepago) { 
                $gui = file_get_contents("static/mail.html");

                $comprobantepago_id = $obj_comprobantepago->comprobantepago_id;
                $infocontacto_collection = $obj_matriculado->infocontacto_collection;
                unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);
                foreach ($infocontacto_collection as $clave=>$valor) {
                        if ($valor->denominacion == 'Email') $correo_destino = $valor->valor;
                }

                if (!empty($correo_destino) AND !is_null($correo_destino) AND $correo_destino != '') {
                        $fecha_desglosada = $this->descomponer_fecha();
                        $origen = "hu.ce.ro@gmail.com";
                        $nombre = "Consejo Profesional de Ingenieria - La Rioja";
                        
                        $obj_matriculado = $this->set_dict($obj_matriculado);
                        $obj_matricula = $this->set_dict($obj_matricula);
                        $obj_cuentacorrientematriculado = $this->set_dict($obj_cuentacorrientematriculado);
                        $obj_comprobantepago = $this->set_dict($obj_comprobantepago);
                        
                        $render = $this->render($fecha_desglosada, $gui);
                        $render = $this->render($obj_matriculado, $render);
                        $render = $this->render($obj_matricula, $render);
                        $render = $this->render($obj_cuentacorrientematriculado, $render);
                        $render = $this->render($obj_comprobantepago, $render);
                        
                        $mail = new PHPMailer();
                        $mail->SMTPDebug = 2;
                        $mail->IsSMTP();
                        $mail->SMTPSecure = 'ssl';
                        $mail->Host = 'smtp.gmail.com';
                        $mail->Port = 465;
                        $mail->SMTPAuth = true;
                        $mail->Username = "hu.ce.ro@gmail.com";
                        $mail->Password = "caribe.1";
                        $mail->From = $origen;
                        $mail->FromName = $nombre;
                        $mail->AddAddress($correo_destino);
                        $mail->IsHTML(true);
                        $mail->Body = utf8_decode($render);
                        $mail->AddAttachment(URL_PRIVATE . "comprobantepago/ComprobantePago-{$comprobantepago_id}.pdf");
                        $mail->Subject = utf8_decode("Comprobante de Pago");
                        $mail->Send();
                }
        }
}
?>