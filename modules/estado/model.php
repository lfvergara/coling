<?php


class Estado extends StandardObject {
	
	function __construct() {
		$this->estado_id = 0;
		$this->denominacion = '';
		$this->detalle = '';
		$this->perfil = '';
		$this->usuario = '';
	}
}
?>