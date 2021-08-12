ob_start();
<?php
require_once "modules/conceptopago/model.php";
require_once "modules/conceptopago/view.php";


class ConceptoPagoController {

	function __construct() {
		ob_end_clean();
		$this->model = new ConceptoPago();
		$this->view = new ConceptoPagoView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$conceptopago_collection = Collector()->get('ConceptoPago');
		$this->view->panel($conceptopago_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->conceptopago_id = $arg;
		$this->model->get();
    	$conceptopago_collection = Collector()->get('ConceptoPago');
    	$this->view->editar($conceptopago_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));
		$this->model->tipo = filter_input(INPUT_POST, 'tipo');
		$this->model->save();
		header("Location: " . URL_APP . "/conceptopago/panel");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$conceptopago_id = filter_input(INPUT_POST, 'conceptopago_id');
		$this->model->conceptopago_id = $conceptopago_id;
		$this->model->get();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));
		$this->model->tipo = filter_input(INPUT_POST, 'tipo');
		$this->model->save();
		header("Location: " . URL_APP . "/conceptopago/panel");
	}
}
?>