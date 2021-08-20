<?php
require_once "modules/tipofactura/model.php";


class ComprobantePago extends StandardObject {
	
	function __construct(TipoFactura $tipofactura=NULL) {
		$this->comprobantepago_id = 0;
		$this->punto_venta = 0;
		$this->numero_factura = 0;
		$this->cae = 0;
		$this->cae_vencimiento = '';
		$this->fecha = '';
		$this->hora = '';
		$this->subtotal = 0.00;
		$this->importe_total = 0.00;
		$this->emitido = 0;
		$this->cuentacorrientematriculado_id = 0;
		$this->tipofactura = $tipofactura;
	}
}
?>