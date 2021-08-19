<?php
require_once "modules/resolucion/model.php";
require_once "modules/resolucion/view.php";


class ResolucionController {

	function __construct() {
		$this->model = new Resolucion();
		$this->view = new ResolucionView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	$resolucion_collection = Collector()->get('Resolucion');
    	foreach ($resolucion_collection as $clave=>$valor) {
    		if ($valor->estado == 0) {
    			$resolucion_collection[$clave]->class_estado = 'danger';
    			$resolucion_collection[$clave]->icon_estado = 'times';
    			$resolucion_collection[$clave]->url_estado = 'activar';
    			$resolucion_collection[$clave]->lbl_btn_estado = 'Activar';
			} else {
				$resolucion_collection[$clave]->class_estado = 'success';
    			$resolucion_collection[$clave]->icon_estado = 'check';
    			$resolucion_collection[$clave]->url_estado = 'desactivar';
    			$resolucion_collection[$clave]->lbl_btn_estado = 'Desactivar';
			}
    	}

		$this->view->panel($resolucion_collection);
	}

	function editar($arg) {
		SessionHandler()->check_session();
		$this->model->resolucion_id = $arg;
		$this->model->get();
    	$resolucion_collection = Collector()->get('Resolucion');
    	foreach ($resolucion_collection as $clave=>$valor) {
    		if ($valor->estado == 0) {
    			$resolucion_collection[$clave]->class_estado = 'danger';
    			$resolucion_collection[$clave]->icon_estado = 'times';
    			$resolucion_collection[$clave]->url_estado = 'activar';
    			$resolucion_collection[$clave]->lbl_btn_estado = 'Activar';
			} else {
				$resolucion_collection[$clave]->class_estado = 'success';
    			$resolucion_collection[$clave]->icon_estado = 'check';
    			$resolucion_collection[$clave]->url_estado = 'desactivar';
    			$resolucion_collection[$clave]->lbl_btn_estado = 'Desactivar';
			}
    	}

    	$this->view->editar($resolucion_collection, $this->model);
	}

	function guardar() {
		SessionHandler()->check_session();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));
		$this->model->fecha_desde = filter_input(INPUT_POST, 'fecha_desde');
		$this->model->fecha_hasta = filter_input(INPUT_POST, 'fecha_hasta');
		$this->model->descuento = filter_input(INPUT_POST, 'descuento');
		$this->model->recarga = filter_input(INPUT_POST, 'recarga');
		$this->model->detalle = filter_input(INPUT_POST, 'detalle');
		$this->model->activo = 0;
		$this->model->save();
		header("Location: " . URL_APP . "/resolucion/panel");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$resolucion_id = filter_input(INPUT_POST, 'resolucion_id');
		$this->model->resolucion_id = $resolucion_id;
		$this->model->get();
		$this->model->denominacion = strtoupper(filter_input(INPUT_POST, 'denominacion'));
		$this->model->fecha_desde = filter_input(INPUT_POST, 'fecha_desde');
		$this->model->fecha_hasta = filter_input(INPUT_POST, 'fecha_hasta');
		$this->model->descuento = filter_input(INPUT_POST, 'descuento');
		$this->model->recarga = filter_input(INPUT_POST, 'recarga');
		$this->model->detalle = filter_input(INPUT_POST, 'detalle');
		$this->model->save();
		header("Location: " . URL_APP . "/resolucion/panel");
	}

	function activar($arg) {
		SessionHandler()->check_session();
		$resolucion_id = $arg;
		$this->model->resolucion_id = $resolucion_id;
		$this->model->get();
		$this->model->estado = 1;
		$this->model->save();
		header("Location: " . URL_APP . "/resolucion/panel");
	}

	function desactivar($arg) {
		SessionHandler()->check_session();
		$resolucion_id = $arg;
		$this->model->resolucion_id = $resolucion_id;
		$this->model->get();
		$this->model->estado = 0;
		$this->model->save();
		header("Location: " . URL_APP . "/resolucion/panel");
	}
}
?>