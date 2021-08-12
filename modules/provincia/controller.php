<?php
require_once "modules/provincia/model.php";
require_once "modules/provincia/view.php";


class ProvinciaController {

	function __construct() {
		$this->model = new Provincia();
		$this->view = new ProvinciaView();
	}
}
?>