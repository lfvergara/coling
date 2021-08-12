<?php
require_once 'common/libs/domPDF/dompdf_config.inc.php';


class FacturaPDF extends View {
	
    public function comprobante_pago($obj_matriculado, $obj_matricula, $obj_cuentacorrientematriculado, $obj_comprobantepago,
                                     $obj_configuracion) { 
        
        
        $gui_html = file_get_contents("static/common/plantilla_comprobante_pago.html");
        unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);

        $obj_comprobantepago->punto_venta = str_pad($obj_comprobantepago->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_comprobantepago->numero_factura = str_pad($obj_comprobantepago->numero_factura, 8, '0', STR_PAD_LEFT);
        $comprobantepago_id = $obj_comprobantepago->comprobantepago_id;
        
        $obj_matriculado = $this->set_dict($obj_matriculado);
        $obj_matricula = $this->set_dict($obj_matricula);
        $obj_cuentacorrientematriculado = $this->set_dict($obj_cuentacorrientematriculado);
        $obj_comprobantepago = $this->set_dict($obj_comprobantepago);
        $obj_configuracion = $this->set_dict($obj_configuracion);

        $gui_html = $this->render($obj_matriculado, $gui_html);
        $gui_html = $this->render($obj_matricula, $gui_html);
        $gui_html = $this->render($obj_cuentacorrientematriculado, $gui_html);
        $gui_html = $this->render($obj_comprobantepago, $gui_html);
        $gui_html = $this->render($obj_configuracion, $gui_html);
        
        $nombre_PDF = "ComprobantePago-{$comprobantepago_id}";
        $nombre_PDF1 = "ComprobantePago-{$comprobantepago_id}.pdf";
        $directorio = URL_PRIVATE . "comprobantepago/";
        if(!file_exists($directorio)) {
            mkdir($directorio);
            chmod($directorio, 0777);
        }

        $output = $directorio . $nombre_PDF;
        $output1 = $directorio . $nombre_PDF1;
        $mipdf = new DOMPDF();
        $mipdf->set_paper("A4", "portrait");
        $mipdf->load_html($gui_html);
        $mipdf->render(); 
        $pdfoutput = $mipdf->output(); 
        $filename = $output; 
        $fp = fopen($output, "a"); 
        fwrite($fp, $pdfoutput); 
        fclose($fp);

        $fp = fopen($output1, "a"); 
        fwrite($fp, $pdfoutput); 
        fclose($fp);
    }
}
?>