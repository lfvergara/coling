<?php
require_once "modules/estado/model.php";
require_once "modules/estado/view.php";


class EstadoController {

	function __construct() {
		$this->model = new Estado();
		$this->view = new EstadoView();
	}
}
?>