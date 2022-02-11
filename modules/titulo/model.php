<?php


class Titulo extends StandardObject {
	
	function __construct() {
		$this->titulo_id = 0;
		$this->denominacion = '';
		$this->valor_matricula = 0.00;
	}

	function editar_masivo() {
		$sql = "UPDATE titulo SET valor_matricula = ?";
		$datos = array($this->valor_matricula);
		execute_query($sql, $datos);
	}
}
?>