ob_start();
<?php
require_once "modules/universidad/model.php";
require_once "modules/universidad/view.php";
require_once "modules/provincia/model.php";


class UniversidadController {

	function __construct() {
		ob_end_clean();
		$this->model = new Universidad();
		$this->view = new UniversidadView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$universidad_collection = Collector()->get('Universidad');
    	$provincia_collection = Collector()->get('Provincia');
		$this->view->panel($universidad_collection, $provincia_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->universidad_id = $arg;
		$this->model->get();
    	$universidad_collection = Collector()->get('Universidad');
    	$provincia_collection = Collector()->get('Provincia');
		$this->view->editar($universidad_collection, $provincia_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));
		$this->model->acronimo = strtoupper(filter_input(INPUT_POST, 'acronimo'));
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->save();
		header("Location: " . URL_APP . "/universidad/panel");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$universidad_id = filter_input(INPUT_POST, 'universidad_id');
		$this->model->universidad_id = $universidad_id;
		$this->model->get();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));	
		$this->model->acronimo = strtoupper(filter_input(INPUT_POST, 'acronimo'));
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->save();
		header("Location: " . URL_APP . "/universidad/panel");
	}
}
?>