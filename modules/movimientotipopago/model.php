<?php


class MovimientoTipoPago extends StandardObject {
	
	function __construct() {
		$this->movimientotipopago_id = 0;
		$this->denominacion = '';
		$this->numero_movimiento = 0;
		$this->fecha_vencimiento = '';
		$this->estado = '';
	}
}
?>