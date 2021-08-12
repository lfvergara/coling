<?php
require_once "modules/matricula/model.php";
require_once "modules/matricula/view.php";


class MatriculaController {

	function __construct() {
		$this->model = new Matricula();
		$this->view = new MatriculaView();
	}
}
?>