<?php
require_once "modules/tipofactura/model.php";
require_once "modules/tipofactura/view.php";


class TipoFacturaController {

	function __construct() {
		$this->model = new TipoFactura();
		$this->view = new TipoFacturaView();
	}

	function panel() {
    	SessionHandler()->check_session();		
		$tipofactura_collection = Collector()->get('TipoFactura');
		$this->view->panel($tipofactura_collection);
	}

	function guardar() {
		SessionHandler()->check_session();		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/tipofactura/panel");
	}

	function editar($arg) {
		SessionHandler()->check_session();		
		$this->model->tipofactura_id = $arg;
		$this->model->get();
		$tipofactura_collection = Collector()->get('TipoFactura');
		$this->view->editar($tipofactura_collection, $this->model);
	}
}
?>