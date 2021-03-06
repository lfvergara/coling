<?php
require_once "modules/configuracion/model.php";
require_once "common/libs/afip.php-master/src/Afip.php";


class FacturaAFIPTool {
    
    public function facturarAFIP($obj_configuracion, $obj_tipofactura, $obj_matriculado, $importe, $cuit, $documentotipo) { 
        $CUIT = $obj_configuracion->cuit;
        $PTO_VENTA = $obj_configuracion->punto_venta;       
        
        $fecha_factura = date('Y-m-d');
        $tipofactura_afip_id = $obj_tipofactura->afip_id;

        if ($cuit == 0) {
            $documentotipo_matriculado = $obj_matriculado->documentotipo->afip_id;
            $documento_matriculado = $obj_matriculado->documento;
        } else {
            $documentotipo_matriculado = $documentotipo;
            $documento_matriculado = $cuit;
        }
            
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $ultima_factura = $afip->ElectronicBilling->GetLastVoucher($PTO_VENTA, $tipofactura_afip_id);
        
        $nueva_factura = array('punto_venta' => $PTO_VENTA, 
                               'nueva_factura' => $ultima_factura + 1, 
                               'tipofactura_afip_id' => $tipofactura_afip_id,
                               'fecha_factura' => $fecha_factura, 
                               'documentotipo_matriculado' => $documentotipo_matriculado, 
                               'documento_matriculado' => $documento_matriculado);
        
        $array_discriminado = $this->prepara_array_discriminado($obj_matriculado, $importe);
        $array_final = array_merge($nueva_factura, $array_discriminado);
        $data = $this->generaArrayData($array_final);
        $res = $afip->ElectronicBilling->CreateVoucher($data);
        $res['NUMFACTURA'] = $nueva_factura['nueva_factura'];
        return $res;
    }

    function prepara_array_discriminado($obj_matriculado, $importe) { 
        $importe_total = $importe;

        // SEPARO EXENTOS Y NO GRAVADOS
        $array_exentos = array();
        $array_nogravados = array();
        
        //DISCRIMINO IVA POR PRODUCTO Y CALCULO DESCUENTO DE FACTURA
        $sum_27 = 0;
        $sum_21 = 0;
        $sum_10_5 = 0;
        $sum_5 = 0;
        $sum_2_5 = 0;
        $sum_0 = 0;
        $sum_baseimp_27 = 0;
        $sum_baseimp_21 = 0;
        $sum_baseimp_10_5 = 0;
        $sum_baseimp_5 = 0;
        $sum_baseimp_2_5 = 0;
        $sum_baseimp_0 = 0;
        $sum_neto = $importe;
        $sum_iva = 0;        

        // DISCRIMINO  EXENTOS Y NO GRAVADOS
        $sum_exentos = 0;
        $sum_nogravados = 0;
        
        // APLICO DESCUENTO DE LA FACTURA
        $neto_final = $sum_neto; 
        $iva_final = $sum_iva; 
        $importe_control = $neto_final + $iva_final + $sum_exentos + $sum_nogravados;
        
        $importes_iva = array(array('{iva}'=>0,'{sum_iva}'=>$sum_0,'{sum_baseimp_iva}'=>$sum_baseimp_0),
                              array('{iva}'=>2.5,'{sum_iva}'=>$sum_2_5,'{sum_baseimp_iva}'=>$sum_baseimp_2_5), 
                              array('{iva}'=>5,'{sum_iva}'=>$sum_5,'{sum_baseimp_iva}'=>$sum_baseimp_5), 
                              array('{iva}'=>10.5,'{sum_iva}'=>$sum_10_5,'{sum_baseimp_iva}'=>$sum_baseimp_10_5), 
                              array('{iva}'=>21,'{sum_iva}'=>$sum_21,'{sum_baseimp_iva}'=>$sum_baseimp_21), 
                              array('{iva}'=>27,'{sum_iva}'=>$sum_27,'{sum_baseimp_iva}'=>$sum_baseimp_27)); 

        $importes_finales = array('importe_neto'=>$neto_final, 
                                  'importe_iva'=>$iva_final, 
                                  'importe_total'=>$importe_total, 
                                  'importe_exento'=>$sum_exentos, 
                                  'importe_nogravado'=>$sum_nogravados, 
                                  'importe_control'=>$importe_control);

        // REDONDEO IMPORTES A DOS DECIMALES
        //foreach ($importes_iva as $clave=>$valor) $importes_iva["{$clave}"] = round($valor,2, PHP_ROUND_HALF_EVEN);
        foreach ($importes_finales as $clave=>$valor) $importes_finales["{$clave}"] = round($valor,2, PHP_ROUND_HALF_EVEN);
        $importes_finales['importe_control'] = $importes_finales['importe_neto'] + $importes_finales['importe_iva'] + $importes_finales['importe_exento'] + $importes_finales['importe_nogravado'];


        // ARMO ARRAY DE ALICUOTAS
        $array_alicuotas = array();
        foreach ($importes_iva as $clave=>$valor) {
            $iva = $valor['{iva}'];
            $costo = $valor['{sum_iva}'];
            $baseimponible = $valor['{sum_baseimp_iva}'];
            
            if ($costo == 0 AND $iva == 0 AND $baseimponible != 0) {
                $array_iva_temp = array(
                    'Id'=>3, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp'=>$baseimponible, // Base imponible
                    'Importe'=>$costo // Importe 
                );
                
                $array_alicuotas[] = $array_iva_temp;
            } 
            
            if ($costo != 0) {
                switch ($iva) {
                    case 2.5:
                        $iva_id = 9;
                        break;
                    case 5:
                        $iva_id = 8;
                        break;
                    case 10.5:
                        $iva_id = 4;
                        break;
                    case 21:
                        $iva_id = 5;
                        break;
                    case 27:
                        $iva_id = 6;
                        break;
                }
                
                $array_iva_temp = array(
                    'Id'=>$iva_id, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp'=>$baseimponible, // Base imponible
                    'Importe'=>$costo // Importe 
                );

                $array_alicuotas[] = $array_iva_temp;
            }
        }

        // FORMO ARRAY FINAL
        $array_final = array_merge($importes_iva,$importes_finales);
        $array_final['array_alicuotas'] = $array_alicuotas;
        return $array_final;
    }

    function generaArrayData($array_final) { 

        $punto_venta = $array_final['punto_venta'];
        $numero_factura = $array_final['nueva_factura'];
        $tipofactura = $array_final['tipofactura_afip_id'];
        $fecha_factura = date('Ymd');
        $importe_total = $array_final['importe_total'];
        $documentotipo_matriculado = $array_final['documentotipo_matriculado'];
        $documento_matriculado = $array_final['documento_matriculado'];
        $importe_nogravado = $array_final['importe_nogravado'];
        $importe_exento = $array_final['importe_exento'];
        $importe_neto = $array_final['importe_neto'];
        $importe_iva = $array_final['importe_iva'];
        $array_alicuotas = $array_final['array_alicuotas'];
        
        $data = array(
            'CantReg'   => 1,  // Cantidad de comprobantes a registrar
            'PtoVta'    => $punto_venta,  // Punto de venta
            'CbteTipo'  => $tipofactura,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto'  => 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo'   => $documentotipo_matriculado, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro'    => $documento_matriculado,  // N??mero de documento del comprador (0 consumidor final)
            'CbteDesde' => $numero_factura,  // N??mero de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta' => $numero_factura,  // N??mero de comprobante o numero del ??ltimo comprobante en caso de ser mas de uno
            'CbteFch'   => intval($fecha_factura), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal'  => $importe_total, // Importe total del comprobante
            'ImpTotConc'=> $importe_nogravado,   // Importe neto no gravado
            'ImpNeto'   => $importe_neto, // Importe neto gravado
            'ImpOpEx'   => $importe_exento,   // Importe exento de IVA
            'ImpIVA'    => $importe_iva,  //Importe total de IVA
            'ImpTrib'   => 0,   //Importe total de tributos
            'MonId'     => 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz'  => 1,     // Cotizaci??n de la moneda usada (1 para pesos argentinos) 
            /*'Iva'       => $array_alicuotas, */
        );

        return $data;
    }

    public function notaCreditoAFIP($obj_configuracion, $obj_notacredito, $obj_matriculado, $numero_recibo, $cuit, $documentotipo) { 
        $CUIT = $obj_configuracion->cuit;
        $PTO_VENTA = $obj_configuracion->punto_venta;

        if ($cuit == 0) {
            $documentotipo_matriculado = $obj_matriculado->documentotipo->afip_id;
            $documento_matriculado = $obj_matriculado->documento;
        } else {
            $documentotipo_matriculado = $documentotipo;
            $documento_matriculado = $cuit;
        }
        
        $importe = $obj_notacredito->total;
        $fecha_recibo = $obj_notacredito->fecha;
        $tipofactura_afip_id = $obj_notacredito->tipofactura->afip_id;
        //$documentotipo_matriculado = $obj_matriculado->documentotipo->afip_id;
        //$documento_matriculado = $obj_matriculado->documento;
            
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $ultima_factura = $afip->ElectronicBilling->GetLastVoucher($PTO_VENTA,$tipofactura_afip_id);
        
        $nueva_factura = array('punto_venta'=>$obj_configuracion->punto_venta, 'nueva_factura'=>$ultima_factura + 1, 'tipofactura_afip_id'=>$tipofactura_afip_id, 'fecha_factura'=>$fecha_recibo, 'documentotipo_matriculado'=>$documentotipo_matriculado, 'documento_matriculado'=>$documento_matriculado, 'cuit_emisor'=>$CUIT);
        
        $array_discriminado = $this->prepara_array_discriminado_nc($importe);
        $array_final = array_merge($nueva_factura, $array_discriminado);
        $data = $this->generaArrayDataNC($array_final, $numero_recibo);
        
        $res = $afip->ElectronicBilling->CreateVoucher($data);
        $res['NUMFACTURA'] = $nueva_factura['nueva_factura'];
        return $res;
    }

    function prepara_array_discriminado_nc($importe) { 
        $importe_total = $importe;

        // SEPARO EXENTOS Y NO GRAVADOS
        $array_exentos = array();
        $array_nogravados = array();
        
        //DISCRIMINO IVA POR PRODUCTO Y CALCULO DESCUENTO DE FACTURA
        $sum_27 = 0;
        $sum_21 = 0;
        $sum_10_5 = 0;
        $sum_5 = 0;
        $sum_2_5 = 0;
        $sum_0 = 0;
        $sum_baseimp_27 = 0;
        $sum_baseimp_21 = 0;
        $sum_baseimp_10_5 = 0;
        $sum_baseimp_5 = 0;
        $sum_baseimp_2_5 = 0;
        $sum_baseimp_0 = 0;
        $sum_neto = $importe;
        $sum_iva = 0;        

        // DISCRIMINO  EXENTOS Y NO GRAVADOS
        $sum_exentos = 0;
        $sum_nogravados = 0;
        
        // APLICO DESCUENTO DE LA FACTURA
        $neto_final = $sum_neto; 
        $iva_final = $sum_iva; 
        $importe_control = $neto_final + $iva_final + $sum_exentos + $sum_nogravados;
        
        $importes_iva = array(array('{iva}'=>0,'{sum_iva}'=>$sum_0,'{sum_baseimp_iva}'=>$sum_baseimp_0),
                              array('{iva}'=>2.5,'{sum_iva}'=>$sum_2_5,'{sum_baseimp_iva}'=>$sum_baseimp_2_5), 
                              array('{iva}'=>5,'{sum_iva}'=>$sum_5,'{sum_baseimp_iva}'=>$sum_baseimp_5), 
                              array('{iva}'=>10.5,'{sum_iva}'=>$sum_10_5,'{sum_baseimp_iva}'=>$sum_baseimp_10_5), 
                              array('{iva}'=>21,'{sum_iva}'=>$sum_21,'{sum_baseimp_iva}'=>$sum_baseimp_21), 
                              array('{iva}'=>27,'{sum_iva}'=>$sum_27,'{sum_baseimp_iva}'=>$sum_baseimp_27)); 

        $importes_finales = array('importe_neto'=>$neto_final, 
                                  'importe_iva'=>$iva_final, 
                                  'importe_total'=>$importe_total, 
                                  'importe_exento'=>$sum_exentos, 
                                  'importe_nogravado'=>$sum_nogravados, 
                                  'importe_control'=>$importe_control);

        // REDONDEO IMPORTES A DOS DECIMALES
        //foreach ($importes_iva as $clave=>$valor) $importes_iva["{$clave}"] = round($valor,2, PHP_ROUND_HALF_EVEN);
        foreach ($importes_finales as $clave=>$valor) $importes_finales["{$clave}"] = round($valor,2, PHP_ROUND_HALF_EVEN);
        $importes_finales['importe_control'] = $importes_finales['importe_neto'] + $importes_finales['importe_iva'] + $importes_finales['importe_exento'] + $importes_finales['importe_nogravado'];


        // ARMO ARRAY DE ALICUOTAS
        $array_alicuotas = array();
        foreach ($importes_iva as $clave=>$valor) {
            $iva = $valor['{iva}'];
            $costo = $valor['{sum_iva}'];
            $baseimponible = $valor['{sum_baseimp_iva}'];
            
            if ($costo == 0 AND $iva == 0 AND $baseimponible != 0) {
                $array_iva_temp = array(
                    'Id'=>3, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp'=>$baseimponible, // Base imponible
                    'Importe'=>$costo // Importe 
                );
                
                $array_alicuotas[] = $array_iva_temp;
            } 
            
            if ($costo != 0) {
                switch ($iva) {
                    case 2.5:
                        $iva_id = 9;
                        break;
                    case 5:
                        $iva_id = 8;
                        break;
                    case 10.5:
                        $iva_id = 4;
                        break;
                    case 21:
                        $iva_id = 5;
                        break;
                    case 27:
                        $iva_id = 6;
                        break;
                }
                
                $array_iva_temp = array(
                    'Id'=>$iva_id, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp'=>$baseimponible, // Base imponible
                    'Importe'=>$costo // Importe 
                );

                $array_alicuotas[] = $array_iva_temp;
            }
        }

        // FORMO ARRAY FINAL
        $array_final = array_merge($importes_iva,$importes_finales);
        $array_final['array_alicuotas'] = $array_alicuotas;
        return $array_final;
    }

    function generaArrayDataNC($array_final, $numero_recibo) { 
        $cuit_emisor = $array_final['cuit_emisor'];
        $punto_venta = $array_final['punto_venta'];
        $numero_factura = $array_final['nueva_factura'];
        $tipofactura = 13;
        $fecha_factura = date('Ymd');
        $importe_total = $array_final['importe_total'];
        $documentotipo_matriculado = $array_final['documentotipo_matriculado'];
        $documento_matriculado = $array_final['documento_matriculado'];
        $importe_nogravado = $array_final['importe_nogravado'];
        $importe_exento = $array_final['importe_exento'];
        $importe_neto = $array_final['importe_neto'];
        $importe_iva = $array_final['importe_iva'];
        $array_alicuotas = $array_final['array_alicuotas'];
        
        $data = array(
            'CantReg'   => 1,  // Cantidad de comprobantes a registrar
            'PtoVta'    => $punto_venta,  // Punto de venta
            'CbteTipo'  => $tipofactura,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto'  => 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo'   => $documentotipo_matriculado, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro'    => $documento_matriculado,  // N??mero de documento del comprador (0 consumidor final)
            'CbteDesde'     => $numero_factura,  // N??mero de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta'     => $numero_factura,  // N??mero de comprobante o numero del ??ltimo comprobante en caso de ser mas de uno
            'CbteFch'   => intval($fecha_factura), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal'  => $importe_total, // Importe total del comprobante
            'ImpTotConc'    => $importe_nogravado,   // Importe neto no gravado
            'ImpNeto'   => $importe_neto, // Importe neto gravado
            'ImpOpEx'   => $importe_exento,   // Importe exento de IVA
            'ImpIVA'    => $importe_iva,  //Importe total de IVA
            'ImpTrib'   => 0,   //Importe total de tributos
            'MonId'     => 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz'  => 1,     // Cotizaci??n de la moneda usada (1 para pesos argentinos) 
            'CbtesAsoc' => array( // (Opcional) Comprobantes asociados
                                array(
                                    'Tipo'      => 15, // Tipo de comprobante (ver tipos disponibles) 
                                    'PtoVta'    => $punto_venta, // Punto de venta
                                    'Nro'       => $numero_recibo, // Numero de comprobante
                                    'Cuit'      => $cuit_emisor // (Opcional) Cuit del emisor del comprobante
                                )
            )
        );

        return $data;
    }

    public function preparaFacturaAFIP($obj_tipofactura, $obj_matriculado, $egresodetalle_collection) { 
        $cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();

        $CUIT = $cm->cuit;
        $PTO_VENTA = $cm->punto_venta;
        
        $array_discriminado = $this->prepara_array_discriminado($obj_matriculado, $egresodetalle_collection);
        $tipofactura_afip_id = $obj_matriculado->tipofactura->afip_id;
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $ultima_factura = $afip->ElectronicBilling->GetLastVoucher($PTO_VENTA,$tipofactura_afip_id);
        
        $nueva_factura = array('punto_venta'=>$cm->punto_venta, 'nueva_factura'=>$ultima_factura + 1);
        $array_final = array_merge($nueva_factura, $array_discriminado);
        return $array_final;
    }

    public function preparaFacturaAFIPNC($obj_tipofactura, $obj_notacredito, $notacreditodetalle_collection) { 
        $cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();
        $CUIT = $cm->cuit;
        $PTO_VENTA = $cm->punto_venta;
        
        $array_discriminado = $this->prepara_array_discriminado_nc($obj_notacredito, $notacreditodetalle_collection);
        $tipofactura_afip_id = $obj_notacredito->tipofactura->afip_id;
        
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $ultima_factura = $afip->ElectronicBilling->GetLastVoucher($PTO_VENTA,$tipofactura_afip_id);

        $nueva_factura = array('punto_venta'=>$cm->punto_venta, 'nueva_factura'=>$ultima_factura + 1);
        $array_final = array_merge($nueva_factura, $array_discriminado);
        return $array_final;
    }

    function BKprepara_array_discriminado($obj_matriculado, $egresodetalle_collection) { 
        $importe_total = $obj_matriculado->importe_total;
        $descuento_factura = $obj_matriculado->descuento;
        $valor_descuento_factura = $descuento_factura / 100;
        
        // SEPARO EXENTOS Y NO GRAVADOS
        $array_exentos = array();
        $array_nogravados = array();
        foreach ($egresodetalle_collection as $clave=>$valor) {
            $exento = $valor['EXENTO'];
            $nogravado = $valor['NOGRAVADO'];
            if ($exento == 1) {
                $array_exentos[] = $valor;
                unset($egresodetalle_collection[$clave]);
            }

            if ($nogravado == 1) {
                $array_nogravados[] = $valor;
                unset($egresodetalle_collection[$clave]);
            }
        }
        
        // DISCRIMINO  EXENTOS Y NO GRAVADOS
        $sum_neto = 0;
        if (!empty($egresodetalle_collection)) {
            foreach ($egresodetalle_collection as $clave=>$valor) {
                $descuento = $valor['DESCUENTO'] / 100;
                $unitario = $valor['COSTO'];
                $cantidad = $valor['CANTIDAD'];
                $subtotal = $cantidad * $unitario;
                $neto = $subtotal - ($subtotal * $descuento);
                $sum_neto = $sum_neto + $neto;
            }
        }

        $sum_exentos = 0;
        if (!empty($array_exentos)) {
            foreach ($array_exentos as $clave=>$valor) {
                $descuento = $valor['DESCUENTO'] / 100;
                $unitario = $valor['COSTO'];
                $cantidad = $valor['CANTIDAD'];
                $subtotal = $cantidad * $unitario;
                $neto = $subtotal - ($subtotal * $descuento);
                $sum_exentos = $sum_exentos + $neto;
            }
        }

        $sum_nogravados = 0;
        if (!empty($array_nogravados)) {
            foreach ($array_nogravados as $clave=>$valor) {
                $descuento = $valor['DESCUENTO'] / 100;
                $unitario = $valor['COSTO'];
                $cantidad = $valor['CANTIDAD'];
                $subtotal = $cantidad * $unitario;
                $neto = $subtotal - ($subtotal * $descuento);
                $sum_nogravados = $sum_nogravados + $neto;
            }
        }

        // APLICO DESCUENTO DE LA FACTURA
        $subtotal_final = $sum_neto - ($valor_descuento_factura * $sum_neto); 

        $neto_final = $subtotal_final / 1.21;
        $iva_final = $subtotal_final - $neto_final;
        $importe_control = $neto_final + $iva_final + $sum_exentos + $sum_nogravados;
        

        $importes_iva = array('iva_21'=>$iva_final);
        $importes_finales = array('importe_neto'=>$neto_final, 'importe_iva'=>$iva_final, 'importe_total'=>$importe_total, 
                                  'importe_exento'=>$sum_exentos, 'importe_nogravado'=>$sum_nogravados, 'importe_control'=>$importe_control);
        
        // REDONDEO IMPORTES A DOS DECIMALES
        foreach ($importes_iva as $clave=>$valor) $importes_iva["{$clave}"] = round($valor,2, PHP_ROUND_HALF_EVEN);
        foreach ($importes_finales as $clave=>$valor) $importes_finales["{$clave}"] = round($valor,2, PHP_ROUND_HALF_EVEN);
        
        $array_alicuota = array(
            'Id'=>5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
            'BaseImp'=>$importes_finales['importe_neto'], // Base imponible
            'Importe'=>$importes_finales['importe_iva'] // Importe 
        );

        // FORMO ARRAY FINAL
        $array_final = array_merge($importes_iva,$importes_finales);
        $array_final['array_alicuotas'] = $array_alicuota;
        // CONTROL
        //print_r($array_final);
        //print("<hr>");
        //print_r($importes_finales);
        //exit;
        
        return $array_final;
    }

    function traerSiguienteFacturaAFIP($tipofactura_afip_id) {
        $cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();

        $CUIT = $cm->cuit;
        $PTO_VENTA = $cm->punto_venta;
        
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $ultima_factura = $afip->ElectronicBilling->GetLastVoucher($PTO_VENTA,$tipofactura_afip_id);
        $nuevo_numero = $ultima_factura + 1;
        $nuevo_comprobante = str_pad($cm->punto_venta, 4, '0', STR_PAD_LEFT) . "-";
        $nuevo_comprobante .= str_pad($nuevo_numero, 8, '0', STR_PAD_LEFT);
        return $nuevo_comprobante;
    }

    function traerDatosFacturaAFIP() {
        $cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();

        $punto_venta = $cm->punto_venta;
        $CUIT = $cm->cuit;
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $info_factura = $afip->ElectronicBilling->GetVoucherInfo($punto_venta,3,6); //Devuelve la informaci??n del comprobante 1 para el punto de venta 1 y el tipo de comprobante 6 (Factura B)->GetAliquotTypes();
        print_r($info_factura);exit;
        return $info_factura;
    }

    function traerTiposAlicuotasAFIP() {
        $cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();

        $CUIT = $cm->cuit;
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $alicuotas = $afip->ElectronicBilling->GetAliquotTypes();
        return $alicuotas;
    }

    function traerTiposFacturasAFIP() {
        $cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();

        $CUIT = $cm->cuit;
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => true));
        $voucher_types = $afip->ElectronicBilling->GetVoucherTypes();
        return $voucher_types;
    }

    function arrayFacturaA($obj_matriculado) { 

        $punto_venta = $obj_matriculado->punto_venta;
        $numero_factura = $obj_matriculado->numero_factura;
        $tipo_factura = $obj_matriculado->tipofactura->afip_id;
        $fecha_factura = str_replace('-', '', $obj_matriculado->fecha);
        $importe_total = $obj_matriculado->importe_total;
        //$importe_total = $obj_matriculado->importe_total;
        print_r($tipo_factura);exit;



        $data = array(
            'CantReg'   => 1,  // Cantidad de comprobantes a registrar
            'PtoVta'    => $punto_venta,  // Punto de venta
            'CbteTipo'  => $tipo_factura,  // Tipo de comprobante (ver tipos disponibles) 
            'Concepto'  => 1,  // Concepto del Comprobante: (1)Productos, (2)Servicios, (3)Productos y Servicios
            'DocTipo'   => 99, // Tipo de documento del comprador (99 consumidor final, ver tipos disponibles)
            'DocNro'    => 0,  // N??mero de documento del comprador (0 consumidor final)
            'CbteDesde'     => $numero_factura,  // N??mero de comprobante o numero del primer comprobante en caso de ser mas de uno
            'CbteHasta'     => $numero_factura,  // N??mero de comprobante o numero del ??ltimo comprobante en caso de ser mas de uno
            'CbteFch'   => intval($fecha_factura), // (Opcional) Fecha del comprobante (yyyymmdd) o fecha actual si es nulo
            'ImpTotal'  => $importe_total, // Importe total del comprobante
            'ImpTotConc'    => 0,   // Importe neto no gravado
            'ImpNeto'   => 100, // Importe neto gravado
            'ImpOpEx'   => 0,   // Importe exento de IVA
            'ImpIVA'    => 21,  //Importe total de IVA
            'ImpTrib'   => 0,   //Importe total de tributos
            'MonId'     => 'PES', //Tipo de moneda usada en el comprobante (ver tipos disponibles)('PES' para pesos argentinos) 
            'MonCotiz'  => 1,     // Cotizaci??n de la moneda usada (1 para pesos argentinos)  
            'Iva'       => array( // (Opcional) Al??cuotas asociadas al comprobante
                array(
                    'Id'        => 5, // Id del tipo de IVA (5 para 21%)(ver tipos disponibles) 
                    'BaseImp'   => 100, // Base imponible
                    'Importe'   => 21 // Importe 
                )
            ), 
        );

        print_r($data);exit;

    }
}

function FacturaAFIPTool() { return new FacturaAFIPTool(); }
?>