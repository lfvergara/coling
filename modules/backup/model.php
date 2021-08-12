<?php


class Backup extends StandardObject {
	
	function __construct() {
		$this->backup_id = 0;
		$this->denominacion = '';
        $this->usuario = '';
        $this->fecha = '';
        $this->hora = '';
        $this->detalle = '';
	}
}
?>