<?php
require_once "modules/reporte/view.php";
require_once "modules/matriculado/model.php";
require_once "modules/matricula/model.php";
require_once "modules/cuentacorrientematriculado/model.php";


class ReporteController {
	function __construct() {
		$this->view = new ReporteView();
	}

	function admin() {
		$desde = date('Y-m') . '-01';
		$hasta = date('Y-m-d');
		$select = "SUM(CASE mf.tipomovimiento WHEN 1 THEN mf.importe ELSE 0 END) AS TOTAL_CAJA, 
				   SUM(CASE mf.tipomovimiento WHEN 2 THEN mf.importe ELSE 0 END) AS TOTAL_BANCO";
		$from = "movimientofinanciero mf";
		$where = "mf.fecha BETWEEN {$desde} AND {$hasta}";
		$total_caja_banco = CollectorCondition()->get('MovimientoFinanciero', NULL, 4, $from, $select);
		$array_datos = array('{total_caja}'=>$total_caja_banco[0]['TOTAL_CAJA'],
							 '{total_banco}'=>$total_caja_banco[0]['TOTAL_BANCO']);
		/*
		$select = "m.matriculado_id AS ID, m.barrio AS BARRIO, pr.denominacion AS PROVINCIA, m.codigopostal AS CODPOSTAL, 
				   CONCAT(m.apellido, ' ', m.nombre) AS MATRICULADO, CONCAT(dt.denominacion, ' ', m.documento) AS DOCUMENTO, 
				   CONCAT(m.barrio, ' - ', m.domicilio) AS DOMICILIO,
				   (SELECT im.valor FROM infocontacto im INNER JOIN infocontactomatriculado icm ON im.infocontacto_id = icm.compositor WHERE 
				   icm.compuesto = m.matriculado_id AND denominacion = 'Celular') AS CELULAR, 
				   (SELECT ic.valor FROM infocontacto ic INNER JOIN infocontactomatriculado icm ON ic.infocontacto_id = icm.compositor WHERE 
				   icm.compuesto = m.matriculado_id AND denominacion = 'Email') AS EMAIL, IF(ma.matricula IS NULL, '-', ma.matricula) AS NUMMATRICULA";
		$from = "matriculado m INNER JOIN provincia pr ON m.provincia = pr.provincia_id INNER JOIN 
				 documentotipo dt ON m.documentotipo = dt.documentotipo_id LEFT JOIN matriculamatriculado mm ON m.matriculado_id = mm.compuesto LEFT JOIN
				 matricula ma ON mm.compositor = ma.matricula_id";
		$matriculado_collection = CollectorCondition()->get('Matriculado', NULL, 4, $from, $select);
		*/
		$this->view->admin($array_datos);
	}

	function operador() {
		/*
		$desde = date('Y-m') . '-01';
		$hasta = date('Y-m-d');
		$select = "SUM(CASE mf.tipomovimiento WHEN 1 THEN mf.importe ELSE 0 END) AS TOTAL_CAJA, 
				   SUM(CASE mf.tipomovimiento WHEN 2 THEN mf.importe ELSE 0 END) AS TOTAL_BANCO";
		$from = "movimientofinanciero mf";
		$where = "mf.fecha BETWEEN {$desde} AND {$hasta}";
		$total_caja_banco = CollectorCondition()->get('MovimientoFinanciero', NULL, 4, $from, $select);
		$array_datos = array('{total_caja}'=>$total_caja_banco[0]['TOTAL_CAJA'],
							 '{total_banco}'=>$total_caja_banco[0]['TOTAL_BANCO']);
		$select = "m.matriculado_id AS ID, m.barrio AS BARRIO, pr.denominacion AS PROVINCIA, m.codigopostal AS CODPOSTAL, 
				   CONCAT(m.apellido, ' ', m.nombre) AS MATRICULADO, CONCAT(dt.denominacion, ' ', m.documento) AS DOCUMENTO, 
				   CONCAT(m.barrio, ' - ', m.domicilio) AS DOMICILIO,
				   (SELECT im.valor FROM infocontacto im INNER JOIN infocontactomatriculado icm ON im.infocontacto_id = icm.compositor WHERE 
				   icm.compuesto = m.matriculado_id AND denominacion = 'Celular') AS CELULAR, 
				   (SELECT ic.valor FROM infocontacto ic INNER JOIN infocontactomatriculado icm ON ic.infocontacto_id = icm.compositor WHERE 
				   icm.compuesto = m.matriculado_id AND denominacion = 'Email') AS EMAIL, IF(ma.matricula IS NULL, '-', ma.matricula) AS NUMMATRICULA";
		$from = "matriculado m INNER JOIN provincia pr ON m.provincia = pr.provincia_id INNER JOIN 
				 documentotipo dt ON m.documentotipo = dt.documentotipo_id LEFT JOIN matriculamatriculado mm ON m.matriculado_id = mm.compuesto LEFT JOIN
				 matricula ma ON mm.compositor = ma.matricula_id";
		$matriculado_collection = CollectorCondition()->get('Matriculado', NULL, 4, $from, $select);
		*/
		$this->view->operador();
	}

	function descargar_movimiento_mensual($arg) {
		SessionHandler()->check_session();
		require_once "tools/excelreport.php";
		$desde = date('Y-m') . '-01';
		$hasta = date('Y-m-d');
    	$tipomovimiento = $arg;

		
		$select = "mf.movimientofinanciero_id AS MFID, mf.fecha AS FECHA, mf.detalle AS DETALLE, cp.denominacion AS CONCEPTO,
    			   mf.importe AS IMPORTE, u.denominacion AS USUARIO";
		$from = "movimientofinanciero mf INNER JOIN conceptopago cp ON mf.conceptopago = cp.conceptopago_id INNER JOIN 
				 usuario u ON mf.usuario_id = u.usuario_id";
		$where = "mf.tipomovimiento = {$tipomovimiento}";
		$movimientofinanciero_collection = CollectorCondition()->get('MovimientoFinanciero', $where, 4, $from, $select);
		$datos_reporte = CollectorCondition()->get('MovimientoFinanciero', $where, 4, $from, $select);

		switch ($tipomovimiento) {
			case 1:
				$subtitulo = "MOVIMIENTOS POR CAJA";
				break;
			case 2:
				$subtitulo = "MOVIMIENTOS POR BANCO";
				break;
		}

		$array_encabezados = array('FECHA', 'CONCEPTO', 'DETALLE', 'USUARIO', 'IMPORTE');
		$array_exportacion = array();
		$array_exportacion[] = $array_encabezados;
		$sum_importe = 0;
		foreach ($datos_reporte as $clave=>$valor) {
			$sum_importe = $sum_importe + $valor["IMPORTE"];
			$array_temp = array();
			$array_temp = array(
						  $valor["FECHA"]
						, $valor["CONCEPTO"]
						, $valor["DETALLE"]
						, $valor["USUARIO"]
						, $valor["IMPORTE"]);
			$array_exportacion[] = $array_temp;
		}
		
		$array_exportacion[] = array('', '', '', '', '');
		$array_exportacion[] = array('', '', '', 'TOTAL', $sum_importe);
		ExcelReport()->extraer_informe($array_exportacion, $subtitulo);
		exit;
	}

	/*
	function agregar() {
    	SessionHandler()->check_session();	
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$this->view->agregar($provincia_collection, $documentotipo_collection);
	}

	function consultar($arg) {
		SessionHandler()->check_session();
		$this->model->matriculado_id = $arg;
		$this->model->get();
		$this->view->consultar($this->model);
	}

	function editar($arg) {
		SessionHandler()->check_session();	
		$this->model->matriculado_id = $arg;
		$this->model->get();
		$provincia_collection = Collector()->get('Provincia');
		$documentotipo_collection = Collector()->get('DocumentoTipo');
		$this->view->editar($provincia_collection, $documentotipo_collection, $this->model);
	}

	function matriculas($arg) {
		SessionHandler()->check_session();	
		$matriculado_id = $arg;
		$this->model->matriculado_id = $arg;
		$this->model->get();
		$matricula_collection = $this->model->matricula_collection;

		$anio = date('Y');
		$fecha = date('Y-m-d');
		if (is_array($matricula_collection) AND !empty($matricula_collection)) {
			foreach ($matricula_collection as $clave=>$valor) {
				$matricula_id = $valor->matricula_id;
				$estado_collection = $valor->estado_collection;
				if (empty($estado_collection)) {
					
					$select = "COUNT(anio) AS CANT";
					$from = "cuentacorrientematriculado ccm";
					$where = "ccm.matriculado_id = {$matriculado_id} AND ccm.matricula_id = {$matricula_id} AND ccm.anio = {$anio}";
					$group_by = "ccm.anio";
					$habilitacion_anual_flag = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select, $group_by);
					$habilitacion_anual_flag = (is_array($habilitacion_anual_flag) AND !empty($habilitacion_anual_flag)) ? $habilitacion_anual_flag[0]["CANT"] : 0;
					if ($habilitacion_anual_flag > 0) {
						$select = "ccm.fecha AS FECHA";
						$from = "cuentacorrientematriculado ccm";
						$where = "ccm.matriculado_id = {$matriculado_id} AND ccm.matricula_id = {$matricula_id} AND ccm.anio = {$anio} AND ccm.estado = 0";
						$group_by = "ccm.anio ORDER BY ccm.fecha ASC LIMIT 1";
						$cuentacorriente_fecha_vencida = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select, $group_by);
						$cuentacorriente_fecha_vencida = (is_array($cuentacorriente_fecha_vencida) AND !empty($cuentacorriente_fecha_vencida)) ? $cuentacorriente_fecha_vencida[0]["FECHA"] : 0;
						if ($cuentacorriente_fecha_vencida == 0) {
							$btn_arg_estado = 0;
							$btn_class_estado = 'danger';
							$btn_icon_estado = 'arrow-down';
							$lbl_estado = 'ACTIVA';
							$lbl_class_estado = 'success';
							$btn_class_estado = 'success';
							$btn_display_consultar_estado = 'none';
							$btn_consultar_tipo_inactivo = 0;
						} else {
							if ($cuentacorriente_fecha_vencida > $fecha) {
								$btn_arg_estado = 0;
								$btn_class_estado = 'danger';
								$btn_icon_estado = 'arrow-down';
								$lbl_estado = 'ACTIVA';
								$lbl_class_estado = 'success';
								$btn_display_consultar_estado = 'none';
								$btn_consultar_tipo_inactivo = 0;
							} else {
								$btn_arg_estado = 1;
								$btn_class_estado = 'success';
								$btn_icon_estado = 'arrow-up';
								$lbl_estado = 'INACTIVA';
								$lbl_class_estado = 'danger';
								$btn_display_consultar_estado = 'inline-block';
								// CUOTA  VENCIDA
								$btn_consultar_tipo_inactivo = 1;
							}
						}
					} else {
						$btn_arg_estado = 1;
						$btn_class_estado = 'success';
						$btn_icon_estado = 'arrow-up';
						$lbl_estado = 'INACTIVA';
						$lbl_class_estado = 'danger';
						$btn_display_consultar_estado = 'inline-block';
						// SIN HABILITACIÓN
						$btn_consultar_tipo_inactivo = 2;
					}

				} else {
					$ultimo_estado = end($estado_collection);
					$estado = $ultimo_estado->denominacion;
					$perfil = $ultimo_estado->perfil;

					if ($estado == 'INACTIVA' AND $perfil == 'admin') {
						$btn_arg_estado = 1;
						$btn_class_estado = 'success';
						$btn_icon_estado = 'arrow-up';
						$lbl_estado = 'INACTIVA';
						$lbl_class_estado = 'danger';
						$btn_display_consultar_estado = 'inline-block';
						// INHABILITADA POR ADMINISTRADOR
						$btn_consultar_tipo_inactivo = 3;
					} else {
						$select = "COUNT(anio) AS CANT";
						$from = "cuentacorrientematriculado ccm";
						$where = "ccm.matriculado_id = {$matriculado_id} AND ccm.matricula_id = {$matricula_id} AND ccm.anio = {$anio}";
						$group_by = "ccm.anio";
						$habilitacion_anual_flag = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select, $group_by);
						$habilitacion_anual_flag = (is_array($habilitacion_anual_flag) AND !empty($habilitacion_anual_flag)) ? $habilitacion_anual_flag[0]["CANT"] : 0;

						if ($habilitacion_anual_flag > 0) {
							$select = "ccm.fecha AS FECHA";
							$from = "cuentacorrientematriculado ccm";
							$where = "ccm.matriculado_id = {$matriculado_id} AND ccm.matricula_id = {$matricula_id} AND ccm.anio = {$anio} AND ccm.estado = 0";
							$group_by = "ccm.anio ORDER BY ccm.fecha ASC LIMIT 1";
							$cuentacorriente_fecha_vencida = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select, $group_by);
							$cuentacorriente_fecha_vencida = (is_array($cuentacorriente_fecha_vencida) AND !empty($cuentacorriente_fecha_vencida)) ? $cuentacorriente_fecha_vencida[0]["FECHA"] : 0;

							if ($cuentacorriente_fecha_vencida == 0) {
								$btn_arg_estado = 0;
								$btn_class_estado = 'danger';
								$btn_icon_estado = 'arrow-down';
								$lbl_estado = 'ACTIVA';
								$lbl_class_estado = 'success';
								$btn_display_consultar_estado = 'none';
								$btn_consultar_tipo_inactivo = 0;
							} else {
								if ($cuentacorriente_fecha_vencida > $fecha) {
									$btn_arg_estado = 0;
									$btn_class_estado = 'danger';
									$btn_icon_estado = 'arrow-down';
									$lbl_estado = 'ACTIVA';
									$lbl_class_estado = 'success';
									$btn_display_consultar_estado = 'none';
									$btn_consultar_tipo_inactivo = 0;
								} else {
									$btn_arg_estado = 1;
									$btn_class_estado = 'success';
									$btn_icon_estado = 'arrow-up';
									$lbl_estado = 'INACTIVA';
									$lbl_class_estado = 'danger';
									$btn_display_consultar_estado = 'inline-block';
									// CUOTA  VENCIDA
									$btn_consultar_tipo_inactivo = 1;
								}
							}
						} else {
							$btn_arg_estado = 1;
							$btn_class_estado = 'success';
							$btn_icon_estado = 'arrow-up';
							$lbl_estado = 'INACTIVA';
							$lbl_class_estado = 'danger';
							$btn_display_consultar_estado = 'inline-block';
							// SIN HABILITACIÓN
							$btn_consultar_tipo_inactivo = 2;
						}
					}
				}

				$matricula_collection[$clave]->btn_arg_estado = $btn_arg_estado;
				$matricula_collection[$clave]->btn_class_estado = $btn_class_estado;
				$matricula_collection[$clave]->btn_icon_estado = $btn_icon_estado;
				$matricula_collection[$clave]->lbl_estado = $lbl_estado;
				$matricula_collection[$clave]->lbl_class_estado = $lbl_class_estado;
				$matricula_collection[$clave]->btn_display_consultar_estado = $btn_display_consultar_estado;
				$matricula_collection[$clave]->btn_consultar_tipo_inactivo = $btn_consultar_tipo_inactivo;

				unset($matricula_collection[$clave]->estado_collection);
			}	
		} 

		unset($this->model->matricula_collection);
		$this->model->matricula_collection = $matricula_collection;
		
		$universidad_collection = Collector()->get('Universidad');
		$titulo_collection = Collector()->get('Titulo');
		$conceptopago_collection = Collector()->get('ConceptoPago');

		$select = "r.resolucion_id AS RESID, r.denominacion AS RESOLUCION";
		$from = "resolucion r";
		$where = "r.estado = 1 AND '{$fecha}' BETWEEN r.fecha_desde AND r.fecha_hasta";
		$resolucion_collection = CollectorCondition()->get('Resolucion', $where, 4, $from, $select);

		$select = "CONCAT(ccm.numero_cuota, '/', ccm.total_cuotas) AS CUOTAS, ccm.fecha AS FECHA, ccm.anio AS PERIODO, 
				   mtp.denominacion AS TIPOPAGO, ccm.valor_abonado AS VALOR, ccm.cuentacorrientematriculado_id AS CCMID, 
				   ccm.matriculado_id AS MATRICULADOID, ccm.matricula_id AS MATRICULAID, cp.denominacion AS CONCEPTO";
		$from = "cuentacorrientematriculado ccm INNER JOIN movimientotipopago mtp ON ccm.movimientotipopago_id = mtp.movimientotipopago_id INNER JOIN
				 conceptopago cp ON ccm.conceptopago = cp.conceptopago_id";
		$where = "ccm.matriculado_id = {$matriculado_id} AND cp.tipo IN (2,4) AND ccm.estado = 1 ORDER BY ccm.anio DESC";
		$movimientosmatriculado_collection = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select);

		$select = "CONCAT(ccm.numero_cuota, '/', ccm.total_cuotas) AS CUOTAS, ccm.fecha AS FECHA, ccm.anio AS PERIODO, 
				   mtp.denominacion AS TIPOPAGO, ccm.valor_abonado AS VALOR, ccm.cuentacorrientematriculado_id AS CCMID, 
				   ccm.matriculado_id AS MATRICULADOID, ccm.matricula_id AS MATRICULAID, cp.denominacion AS CONCEPTO";
		$from = "cuentacorrientematriculado ccm INNER JOIN movimientotipopago mtp ON ccm.movimientotipopago_id = mtp.movimientotipopago_id INNER JOIN
				 conceptopago cp ON ccm.conceptopago = cp.conceptopago_id";
		$where = "ccm.matriculado_id = {$matriculado_id} AND cp.tipo IN (1) AND ccm.estado = 0 ORDER BY ccm.anio DESC";
		$deudamatriculado_collection = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select);

		if (empty($matricula_collection)) {
			$gui_array = array("{btn_agregar_matricula_display}"=>'none',
							   "{div_agregar_matricula_display}"=>'inline-block',
							   "{btn_abonar_otros_display}"=>'inline-block',
							   "{div_abonar_otros_display}"=>'none',
							   "{div_movimientos_display}"=>'none',
							   "{btn_movimientos_display}"=>'none',
							   "{div_deuda_display}"=>'none',
							   "{btn_deuda_display}"=>'none');
		} else {
			$gui_array = array("{btn_agregar_matricula_display}"=>'inline-block',
							   "{div_agregar_matricula_display}"=>'none',
							   "{btn_abonar_otros_display}"=>'none',
							   "{div_abonar_otros_display}"=>'inline-block',
							   "{div_movimientos_display}"=>'none',
							   "{btn_movimientos_display}"=>'inline-block',
							   "{div_deuda_display}"=>'none',
						   	   "{btn_deuda_display}"=>'inline-block');
		}
		
		$this->view->matriculas($universidad_collection, $titulo_collection, $conceptopago_collection, $resolucion_collection,
								$movimientosmatriculado_collection, $deudamatriculado_collection, $this->model, $gui_array);
	}
	
	function buscar() {
		SessionHandler()->check_session();
		$buscar = filter_input(INPUT_POST, 'buscar');
		$select = "m.matriculado_id AS matriculado_ID, m.barrio AS BARRIO, pr.denominacion AS PROVINCIA, m.codigopostal AS CODPOSTAL, 
				   CONCAT(dt.denominacion, ' ', m.documento) AS DOCUMENTO";
		$from = "matriculado m INNER JOIN provincia pr ON m.provincia = pr.provincia_id INNER JOIN 
				 documentotipo dt ON m.documentotipo = dt.documentotipo_id";
		$where = "m.denominacion LIKE '%{$buscar}%' OR m.documento LIKE '%{$buscar}%'";
		$matriculado_collection = CollectorCondition()->get('Matriculado', $where, 4, $from, $select);
		$this->view->listar($matriculado_collection);
	}

	function gestionar_matricula($arg) {
		$ids = explode('@', $arg);
		$matriculado_id = $ids[0];
		$matricula_id = $ids[1];

		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();
		
		$select = "CONCAT(ccm.numero_cuota, '/', ccm.total_cuotas) AS CUOTAS, CASE ccm.estado WHEN 0 THEN CONCAT('Venc. ', ccm.fecha) ELSE CONCAT('Pago ', ccm.fecha) END AS FECHA, 
				   ccm.anio AS PERIODO, mtp.denominacion AS TIPOPAGO, ccm.valor_abonado AS VALOR, ccm.cuentacorrientematriculado_id AS CCMID, ccm.tomo AS TOMO, 
				   ccm.matriculado_id AS MATRICULADOID, ccm.matricula_id AS MATRICULAID, cp.denominacion AS CONCEPTO, ccm.habilitacion AS HABILITACION,
				   CASE ccm.estado WHEN 1 THEN 'none' ELSE 'inline-block' END AS BTNABONAR, CASE ccm.estado WHEN 1 THEN 'inline-block' ELSE 'none' END AS BTNANULAR,
				   CASE ccm.estado WHEN 1 THEN 'none' ELSE 'inline-block' END AS BTNBORRAR";
		$from = "cuentacorrientematriculado ccm INNER JOIN movimientotipopago mtp ON ccm.movimientotipopago_id = mtp.movimientotipopago_id INNER JOIN
				 conceptopago cp ON ccm.conceptopago = cp.conceptopago_id";
		$where = "ccm.matricula_id = {$matricula_id} AND ccm.matriculado_id = {$matriculado_id} AND ccm.conceptopago IN (1,2,3) ORDER BY ccm.anio DESC";
		$cuentacorrientematriculado_collection = CollectorCondition()->get('Resolucion', $where, 4, $from, $select);

		$this->view->gestionar_matricula($this->model, $mm, $cuentacorrientematriculado_collection);
	}
	*/
}
?>