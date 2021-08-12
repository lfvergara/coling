<?php


class Resolucion extends StandardObject {
	
	function __construct() {
		$this->resolucion_id = 0;
                $this->denominacion = '';
                $this->fecha_desde = '';
                $this->fecha_hasta = '';
                $this->descuento = 0.00;
                $this->recarga = 0.00;
                $this->detalle = '';
        	$this->estado = 0;
	}
}
?>