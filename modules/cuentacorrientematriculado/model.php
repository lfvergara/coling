<?php
require_once "modules/conceptopago/model.php";


class CuentaCorrienteMatriculado extends StandardObject {
	
	function __construct(ConceptoPago $conceptopago=NULL) {
		$this->cuentacorrientematriculado_id = 0;
		$this->anio = 0;
		$this->valor_abonado = 0;
		$this->valor_matricula = 0;
		$this->fecha = '';
		$this->hora = '';
		$this->habilitacion = '';
		$this->tomo = '';
		$this->numero_cuota = 0;
		$this->total_cuotas = 0;
		$this->estado = 0;
		$this->movimientotipopago_id = 0;
		$this->resolucion_id = 0;
		$this->matriculado_id = 0;
		$this->matricula_id = 0;
		$this->usuario_id = 0;
		$this->conceptopago = $conceptopago;
	}
}
?>