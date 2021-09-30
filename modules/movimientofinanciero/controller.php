<?php
require_once "modules/movimientofinanciero/model.php";
require_once "modules/movimientofinanciero/view.php";
require_once "modules/cuentacorrientematriculado/model.php";
require_once "core/helpers/file.php";


class MovimientoFinancieroController {

	function __construct() {
		$this->model = new MovimientoFinanciero();
		$this->view = new MovimientoFinancieroView();
	}

	function caja() {
    	SessionHandler()->check_session();
    	$select = "mf.movimientofinanciero_id AS MFID, mf.fecha AS FECHA, mf.detalle AS DETALLE, cp.denominacion AS CONCEPTO,
    			   mf.importe AS IMPORTE, u.denominacion AS USUARIO";
		$from = "movimientofinanciero mf INNER JOIN conceptopago cp ON mf.conceptopago = cp.conceptopago_id INNER JOIN 
				 usuario u ON mf.usuario_id = u.usuario_id";
		$where = "mf.tipomovimiento = 1";
		$movimientofinanciero_collection = CollectorCondition()->get('MovimientoFinanciero', $where, 4, $from, $select);
    	$conceptopago_collection = Collector()->get('ConceptoPago');

		$this->view->caja($movimientofinanciero_collection, $conceptopago_collection);
	}

	function editar_caja($arg) {
		SessionHandler()->check_session();
		$this->model->movimientofinanciero_id = $arg;
		$this->model->get();
		
		$select = "mf.movimientofinanciero_id AS MFID, mf.fecha AS FECHA, mf.detalle AS DETALLE, cp.denominacion AS CONCEPTO,
    			   mf.importe AS IMPORTE, u.denominacion AS USUARIO";
		$from = "movimientofinanciero mf INNER JOIN conceptopago cp ON mf.conceptopago = cp.conceptopago_id INNER JOIN 
				 usuario u ON mf.usuario_id = u.usuario_id";
		$where = "mf.tipomovimiento = 1";
		$movimientofinanciero_collection = CollectorCondition()->get('MovimientoFinanciero', $where, 4, $from, $select);

    	$conceptopago_collection = Collector()->get('ConceptoPago');
    	$this->view->editar_caja($movimientofinanciero_collection, $conceptopago_collection, $this->model);
	}

	function editar_banco($arg) {
		SessionHandler()->check_session();
		$this->model->movimientofinanciero_id = $arg;
		$this->model->get();
		
		$select = "mf.movimientofinanciero_id AS MFID, mf.fecha AS FECHA, mf.detalle AS DETALLE, cp.denominacion AS CONCEPTO,
    			   mf.importe AS IMPORTE, u.denominacion AS USUARIO, mf.denominacion AS DENOMINACION";
		$from = "movimientofinanciero mf INNER JOIN conceptopago cp ON mf.conceptopago = cp.conceptopago_id INNER JOIN 
				 usuario u ON mf.usuario_id = u.usuario_id";
		$where = "mf.tipomovimiento = 2";
		$movimientofinanciero_collection = CollectorCondition()->get('MovimientoFinanciero', $where, 4, $from, $select);

    	$conceptopago_collection = Collector()->get('ConceptoPago');
    	$this->view->editar_banco($movimientofinanciero_collection, $conceptopago_collection, $this->model);
	}

	function banco() {
    	SessionHandler()->check_session();
    	$select = "mf.movimientofinanciero_id AS MFID, mf.fecha AS FECHA, mf.detalle AS DETALLE, cp.denominacion AS CONCEPTO,
    			   mf.importe AS IMPORTE, u.denominacion AS USUARIO, mf.denominacion AS DENOMINACION";
		$from = "movimientofinanciero mf INNER JOIN conceptopago cp ON mf.conceptopago = cp.conceptopago_id INNER JOIN 
				 usuario u ON mf.usuario_id = u.usuario_id";
		$where = "mf.tipomovimiento = 2";
		$movimientofinanciero_collection = CollectorCondition()->get('MovimientoFinanciero', $where, 4, $from, $select);
    	$conceptopago_collection = Collector()->get('ConceptoPago');

		$this->view->banco($movimientofinanciero_collection, $conceptopago_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		
		$tipomovimiento = filter_input(INPUT_POST, 'tipomovimiento');
		$movimientotipopago_id = filter_input(INPUT_POST, 'tipopago');
		switch ($movimientotipopago_id) {
			case 1:
				$numero_movimiento = 0;
				break;
			case 2:
				$numero_movimiento = filter_input(INPUT_POST, 'numero_cheque');
				break;
			case 3:
				$numero_movimiento = filter_input(INPUT_POST, 'numero_transferencia');
				break;
			default:
				$numero_movimiento = 0;
				break;
		}


		$this->model->denominacion = filter_input(INPUT_POST, 'denominacion');
		$this->model->numero_movimiento = $numero_movimiento;
		$this->model->fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
		$this->model->importe = filter_input(INPUT_POST, 'importe');
		$this->model->fecha = date('Y-m-d');
		$this->model->hora = date('H:i:s');
		$this->model->detalle = filter_input(INPUT_POST, 'detalle');
		$this->model->tipomovimiento = $tipomovimiento;
		$this->model->usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$this->model->conceptopago = filter_input(INPUT_POST, 'conceptopago');
		$this->model->save();

		switch ($tipomovimiento) {
			case 1:
				header("Location: " . URL_APP . "/movimientofinanciero/caja");
				break;
			case 2:
				header("Location: " . URL_APP . "/movimientofinanciero/banco");
				break;
			default:
				header("Location: " . URL_APP . "/movimientofinanciero/panel");
				break;
		}
	}

	function actualizar() {
		SessionHandler()->check_session();

		$movimientofinanciero_id = filter_input(INPUT_POST, 'movimientofinanciero_id');
		$this->model->movimientofinanciero_id = $movimientofinanciero_id;
		$this->model->get();
		$tipomovimiento = $this->model->tipomovimiento;
		
		$movimientotipopago_id = filter_input(INPUT_POST, 'tipopago');
		switch ($movimientotipopago_id) {
			case 1:
				$numero_movimiento = 0;
				$fecha_vencimiento = NULL;
				break;
			case 2:
				$numero_movimiento = filter_input(INPUT_POST, 'numero_cheque');
				$fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
				break;
			case 3:
				$numero_movimiento = filter_input(INPUT_POST, 'numero_transferencia');
				$fecha_vencimiento = NULL;
				break;
			default:
				$numero_movimiento = 0;
				$fecha_vencimiento = NULL;
				break;
		}

		$this->model->denominacion = filter_input(INPUT_POST, 'denominacion');
		$this->model->numero_movimiento = $numero_movimiento;
		$this->model->fecha_vencimiento = $fecha_vencimiento; 
		$this->model->importe = filter_input(INPUT_POST, 'importe');
		$this->model->fecha = date('Y-m-d');
		$this->model->hora = date('H:i:s');
		$this->model->detalle = filter_input(INPUT_POST, 'detalle');
		$this->model->usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$this->model->conceptopago = filter_input(INPUT_POST, 'conceptopago');
		$this->model->save();

		switch ($tipomovimiento) {
			case 1:
				header("Location: " . URL_APP . "/movimientofinanciero/caja");
				break;
			case 2:
				header("Location: " . URL_APP . "/movimientofinanciero/banco");
				break;
			default:
				header("Location: " . URL_APP . "/movimientofinanciero/panel");
				break;
		}
	}

	function diario() {
		SessionHandler()->check_session();
		$fecha_sys = date('Y-m-d');

		$select = "CONCAT(ccm.numero_cuota, '/', ccm.total_cuotas) AS CUOTAS, ccm.fecha AS FECHA, ccm.anio AS PERIODO, 
				   mtp.denominacion AS TIPOPAGO, ccm.valor_abonado AS VALOR, ccm.cuentacorrientematriculado_id AS CCMID, 
				   ccm.matriculado_id AS MATRICULADOID, ccm.matricula_id AS MATRICULAID, cp.denominacion AS CONCEPTO, CONCAT(m.apellido, ' ', m.nombre) AS MATRICULADO";
		$from = "cuentacorrientematriculado ccm INNER JOIN movimientotipopago mtp ON ccm.movimientotipopago_id = mtp.movimientotipopago_id INNER JOIN conceptopago cp ON ccm.conceptopago = cp.conceptopago_id INNER JOIN matriculado m ON ccm.matriculado_id = m.matriculado_id";
		$where = "cp.tipo IN (2,4) AND ccm.estado = 1 ORDER BY ccm.anio DESC";
		//$where = "cp.tipo IN (2,4) AND ccm.estado = 1 AND ccm.fecha = '{$fecha_sys}' ORDER BY ccm.anio DESC";
		$movimientosmatriculado_collection = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select);
		$movimientosmatriculado_collection = (is_array($movimientosmatriculado_collection) AND !empty($movimientosmatriculado_collection)) ? $movimientosmatriculado_collection : array();

		$caja_total = 0;
		foreach ($movimientosmatriculado_collection as $clave=>$valor) {
			$importe = $valor['VALOR'];
			$caja_total = $caja_total + $importe;
		}

		$titulo = 'Caja del día de la fecha';
		$this->view->diario($movimientosmatriculado_collection, $caja_total, $titulo);
	}

	function filtro_diario() {
		SessionHandler()->check_session();
		$fecha_desde = filter_input(INPUT_POST, 'desde');
		$fecha_hasta = filter_input(INPUT_POST, 'hasta');

		$select = "CONCAT(ccm.numero_cuota, '/', ccm.total_cuotas) AS CUOTAS, ccm.fecha AS FECHA, ccm.anio AS PERIODO, 
				   mtp.denominacion AS TIPOPAGO, ccm.valor_abonado AS VALOR, ccm.cuentacorrientematriculado_id AS CCMID, 
				   ccm.matriculado_id AS MATRICULADOID, ccm.matricula_id AS MATRICULAID, cp.denominacion AS CONCEPTO, CONCAT(m.apellido, ' ', m.nombre) AS MATRICULADO";
		$from = "cuentacorrientematriculado ccm INNER JOIN movimientotipopago mtp ON ccm.movimientotipopago_id = mtp.movimientotipopago_id INNER JOIN conceptopago cp ON ccm.conceptopago = cp.conceptopago_id INNER JOIN matriculado m ON ccm.matriculado_id = m.matriculado_id";
		$where = "cp.tipo IN (2,4) AND ccm.estado = 1 AND ccm.fecha BETWEEN '{$fecha_desde}' AND '{$fecha_hasta}' ORDER BY ccm.anio DESC";
		$movimientosmatriculado_collection = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select);
		$movimientosmatriculado_collection = (is_array($movimientosmatriculado_collection) AND !empty($movimientosmatriculado_collection)) ? $movimientosmatriculado_collection : array();

		$caja_total = 0;
		foreach ($movimientosmatriculado_collection as $clave=>$valor) {
			$importe = $valor['VALOR'];
			$caja_total = $caja_total + $importe;
		}

		$titulo = "Caja desde {$fecha_desde} hasta {$fecha_hasta}";
		$this->view->diario($movimientosmatriculado_collection, $caja_total, $titulo);
	}

	function ver_archivo(){
		SessionHandler()->check_session();
		require_once "core/helpers/files.php";
	}
}
?>