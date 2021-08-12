<?php
require_once "modules/comprobantepago/model.php";
require_once "modules/comprobantepago/view.php";


class ComprobantePagoController {

	function __construct() {
		$this->model = new ComprobantePago();
		$this->view = new ComprobantePagoView();
	}
}
?>