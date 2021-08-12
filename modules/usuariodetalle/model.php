<?php


class UsuarioDetalle extends StandardObject {
	function __construct() {
		$this->usuariodetalle_id = 0;
		$this->nombre = '';
		$this->apellido = '';
		$this->correoelectronico = '';
		$this->token = '';
	}
}
?>