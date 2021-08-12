<?php
require_once "modules/movimientotipopago/model.php";
require_once "modules/movimientotipopago/view.php";


class MovimientoTipoPagoController {

	function __construct() {
		$this->model = new MovimientoTipoPago();
		$this->view = new MovimientoTipoPagoView();
	}
}
?>