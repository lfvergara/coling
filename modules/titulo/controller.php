ob_start();
<?php
require_once "modules/titulo/model.php";
require_once "modules/titulo/view.php";


class TituloController {

	function __construct() {
		ob_end_clean();
		$this->model = new Titulo();
		$this->view = new TituloView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$titulo_collection = Collector()->get('Titulo');
		$this->view->panel($titulo_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->titulo_id = $arg;
		$this->model->get();
    	$titulo_collection = Collector()->get('Titulo');
    	$this->view->editar($titulo_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));
		$this->model->valor_matricula = filter_input(INPUT_POST, 'valor_matricula');
		$this->model->save();
		header("Location: " . URL_APP . "/titulo/panel");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$titulo_id = filter_input(INPUT_POST, 'titulo_id');
		$this->model->titulo_id = $titulo_id;
		$this->model->get();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));
		$this->model->valor_matricula = filter_input(INPUT_POST, 'valor_matricula');
		$this->model->save();
		header("Location: " . URL_APP . "/titulo/panel");
	}
}
?>