<?php
require_once 'modules/conceptopago/model.php';


class MovimientoFinanciero extends StandardObject {
	
	function __construct(ConceptoPago $conceptopago=NULL) {
		$this->movimientofinanciero_id = 0;
                $this->denominacion = '';
                $this->numero_movimiento = 0;
                $this->fecha_vencimiento = '';
                $this->importe = 0.00;
                $this->fecha = '';
                $this->hora = '';
                $this->detalle = '';
                $this->tipomovimiento = 0;
        	$this->usuario_id = 0;
                $this->conceptopago = $conceptopago;
	}
}
?>