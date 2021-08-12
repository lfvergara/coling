<?php
require_once "modules/cuentacorrientematriculado/model.php";
require_once "modules/cuentacorrientematriculado/view.php";


class CuentaCorrienteMatriculadoController {

	function __construct() {
		$this->model = new CuentaCorrienteMatriculado();
		$this->view = new CuentaCorrienteMatriculadoView();
	}
}
?>