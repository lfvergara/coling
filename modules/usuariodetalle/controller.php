<?php
require_once "modules/usuariodetalle/model.php";


class UsuarioDetalleController {
	function __construct() {
		$this->model = new UsuarioDetalle();
	}

	function guardar() {
		SessionHandler()->check_session();
		
		$denominacion = filter_input(INPUT_POST, "denominacion");
		$password = $denominacion . "$1";
		$denominacion = hash(ALGORITMO_USER, $denominacion);
		$password = hash(ALGORITMO_PASS, $password);
		$token = hash(ALGORITMO_FINAL, $denominacion . $password);
		$password = hash(ALGORITMO_PASS, "{$denominacion}$1");
		
		$this->model->nombre = filter_input(INPUT_POST, "nombre");
		$this->model->apellido = filter_input(INPUT_POST, "apellido");
		$this->model->correoelectronico = filter_input(INPUT_POST, "correoelectronico");
		$this->model->token = $token;
		$this->model->save();
	}

	function actualizar() {
		SessionHandler()->check_session();
		
		$this->model->usuariodetalle_id = filter_input(INPUT_POST, "usuariodetalle_id");
		$this->model->nombre = filter_input(INPUT_POST, "nombre");
		$this->model->apellido = filter_input(INPUT_POST, "apellido");
		$this->model->correoelectronico = filter_input(INPUT_POST, "correoelectronico");
		$this->model->save();
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		
		$this->model->usuariodetalle_id = $arg;
		$this->model->delete();
	}

	function actualizar_token($usuariodetalle_id) {
		SessionHandler()->check_session();		
		$usuario = $_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"];
		$denominacion = hash(ALGORITMO_USER, $usuario);
		$password = hash(ALGORITMO_PASS, strtolower(filter_input(INPUT_POST, 'token')));
		$token = hash(ALGORITMO_FINAL, $denominacion . $password);
		$this->model->usuariodetalle_id = $usuariodetalle_id;
		$this->model->get();
		$this->model->token = $token;
		$this->model->save();
	}

	function regenerar_token($detalle_id, $usuario) {
		SessionHandler()->check_session();
		
		$denominacion = hash(ALGORITMO_USER, $usuario);
		$password = strtolower($usuario) . "$1";
		$password = hash(ALGORITMO_PASS, $password);
		$token = hash(ALGORITMO_FINAL, $denominacion . $password);
		$this->model->usuariodetalle_id = $detalle_id;
		$this->model->get();
		$this->model->token = $token;
		$this->model->save();
	}
}
?>