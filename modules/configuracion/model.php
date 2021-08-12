<?php


class Configuracion extends StandardObject {
	
	function __construct() {
		$this->configuracion_id = 0;
		$this->razon_social = '';
		$this->domicilio_comercial = '';
		$this->telefono = '';
		$this->cuit = 0;
		$this->ingresos_brutos = '';
		$this->fecha_inicio_actividad = '';
		$this->punto_venta = 0;
		$this->tipofactura = 0;
		$this->remitente = '';
		$this->correoelectronico = '';
		$this->host = '';
	}
}
?>