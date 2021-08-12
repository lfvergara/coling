<?php
require_once "modules/documentotipo/model.php";
require_once "modules/documentotipo/view.php";


class DocumentoTipoController {

	function __construct() {
		$this->model = new DocumentoTipo();
		$this->view = new DocumentoTipoView();
	}
}
?>