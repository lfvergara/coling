<?php
require_once "modules/infocontacto/model.php";
require_once "modules/infocontacto/view.php";


class InfoContactoController {

	function __construct() {
		$this->model = new InfoContacto();
		$this->view = new InfoContactoView();
	}
}
?>