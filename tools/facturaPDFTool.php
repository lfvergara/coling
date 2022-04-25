<?php
use Dompdf\Dompdf;
require_once 'common/libs/ndompdf/autoload.inc.php';


class FacturaPDF extends View {
	
    public function comprobante_pago($obj_matriculado, $obj_matricula, $obj_cuentacorrientematriculado, $obj_comprobantepago,
                                     $obj_configuracion) { 
        
        
        $gui_html = file_get_contents("static/common/plantilla_comprobante_pago.html");
        unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);

        $cuit_comprobante = $obj_comprobantepago->cuit;
        if ($cuit_comprobante == 0) {
            $denominacion_cliente = $obj_matriculado->apellido . ' ' . $obj_matriculado->nombre;
            $tipodoc_cliente = $obj_matriculado->documentotipo->afip_id;
            $doc_cliente = $obj_matriculado->documento;
            $doc_cliente = $obj_matriculado->domicilio;
        } else {
            $denominacion_cliente = $obj_comprobantepago->razon_social;
            $tipodoc_cliente = 80;
            $doc_cliente = $cuit_comprobante;
            $domicilio_cliente = '-';
        }

        $array_qr = array('fecha_venta'=>$obj_comprobantepago->fecha,
                          'cuit'=>$obj_configuracion->cuit, 
                          'pto_venta'=>$obj_comprobantepago->punto_venta, 
                          'tipofactura'=>$obj_comprobantepago->tipofactura->afip_id, 
                          'numero_factura'=>$obj_comprobantepago->numero_factura, 
                          'total'=>$obj_comprobantepago->subtotal, 
                          'cliente_tipo_doc'=>$tipodoc_cliente, 
                          'cliente_nro_doc'=>$doc_cliente, 
                          'cae'=>$obj_comprobantepago->cae);

        $cod_qr = $this->qrAFIP($array_qr);
        $obj_comprobantepago->cod_qr = $cod_qr;

        $obj_comprobantepago->punto_venta = str_pad($obj_comprobantepago->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_comprobantepago->numero_factura = str_pad($obj_comprobantepago->numero_factura, 8, '0', STR_PAD_LEFT);
        $comprobantepago_id = $obj_comprobantepago->comprobantepago_id;
        
        $obj_matriculado = $this->set_dict($obj_matriculado);
        $obj_matricula = $this->set_dict($obj_matricula);
        $obj_cuentacorrientematriculado = $this->set_dict($obj_cuentacorrientematriculado);
        $obj_comprobantepago = $this->set_dict($obj_comprobantepago);
        $obj_configuracion = $this->set_dict($obj_configuracion);

        $gui_html = str_replace('{denominacion_cliente}', $denominacion_cliente, $gui_html);
        $gui_html = str_replace('{documento_cliente}', $doc_cliente, $gui_html);
        $gui_html = str_replace('{domicilio_cliente}', $domicilio_cliente, $gui_html);
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

    public function comprobante_nc($obj_matriculado, $obj_notacredito, $obj_configuracion) {        
        
        $gui_html = file_get_contents("static/common/plantilla_comprobante_nc.html");
        unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);

        $array_qr = array('fecha_venta'=>$obj_notacredito->fecha,
                          'cuit'=>$obj_configuracion->cuit, 
                          'pto_venta'=>$obj_notacredito->punto_venta, 
                          'tipofactura'=>$obj_notacredito->tipofactura->afip_id, 
                          'numero_factura'=>$obj_notacredito->numero_factura, 
                          'total'=>$obj_notacredito->total, 
                          'cliente_tipo_doc'=>$obj_matriculado->documentotipo->afip_id, 
                          'cliente_nro_doc'=>$obj_matriculado->documento, 
                          'cae'=>$obj_notacredito->cae);

        $cod_qr = $this->qrAFIP($array_qr);
        $obj_notacredito->cod_qr = $cod_qr;

        $obj_notacredito->punto_venta = str_pad($obj_notacredito->punto_venta, 4, '0', STR_PAD_LEFT);
        $obj_notacredito->numero_factura = str_pad($obj_notacredito->numero_factura, 8, '0', STR_PAD_LEFT);
        $comprobantepago_id = $obj_notacredito->comprobantepago_id;
        
        $obj_matriculado = $this->set_dict($obj_matriculado);
        $obj_notacredito = $this->set_dict($obj_notacredito);
        $obj_configuracion = $this->set_dict($obj_configuracion);

        $gui_html = $this->render($obj_matriculado, $gui_html);
        $gui_html = $this->render($obj_notacredito, $gui_html);
        $gui_html = $this->render($obj_configuracion, $gui_html);
        
        $nombre_PDF = "NotaCredito-{$comprobantepago_id}";
        $nombre_PDF1 = "NotaCredito-{$comprobantepago_id}.pdf";
        $directorio = URL_PRIVATE . "notacredito/";
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

    function qrAFIP($array_qr) {
        require_once 'vendor/autoload.php';
        $datos_cmp_base_64 = json_encode([
            "ver" => 1,
            "fecha" => substr($array_qr['fecha_venta'], 0, 10),
            "cuit" => (int) $array_qr['cuit'],
            "ptoVta" => (int) $array_qr['pto_venta'],
            "tipoCmp" => (int) $array_qr['tipofactura'],
            "nroCmp" => (int) $array_qr['numero_factura'],
            "importe" => (float) $array_qr['total'],
            "moneda" => "PES",
            "ctz" => (float) 1,
            "tipoDocRec" => (int) $array_qr['cliente_tipo_doc'],
            "nroDocRec" => (int) $array_qr['cliente_nro_doc'],
            "tipoCodAut" => "E",
            "codAut" => (int) $array_qr['cae']
        ]);
        
        $datos_cmp_base_64 = base64_encode($datos_cmp_base_64);
        $url = 'https://www.afip.gob.ar/fe/qr/';
        $to_qr = $url.'?p='.$datos_cmp_base_64;

        $barcode = new \Com\Tecnick\Barcode\Barcode();
        $bobj = $barcode->getBarcodeObj(
                'QRCODE,H',
                $to_qr,
                -4,
                -4,
                'black',
                array(-2, -2, -2, -2)
            )->setBackgroundColor('white');
        $qr_div = base64_encode($bobj->getPngData());
        return $qr_div;
    }
}
?>