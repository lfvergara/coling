<?php
require_once "modules/provincia/model.php";


class Universidad extends StandardObject {
	
	function __construct(Provincia $provincia=NULL) {
		$this->universidad_id = 0;
		$this->denominacion = '';
		$this->acronimo = '';
		$this->provincia = $provincia;
	}
}
?>