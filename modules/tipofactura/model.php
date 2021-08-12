<?php


class TipoFactura extends StandardObject {
	
	function __construct() {
		$this->tipofactura_id = 0;
		$this->afip_id = 0;
		$this->nomenclatura = '';
		$this->denominacion = '';
		$this->plantilla_impresion = '';
		$this->detalle = '';
	}
}
?>