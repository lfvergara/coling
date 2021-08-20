<?php
require_once "modules/matriculado/model.php";
require_once "modules/matriculado/view.php";
require_once "modules/documentotipo/model.php";
require_once "modules/provincia/model.php";
require_once "modules/universidad/model.php";
require_once "modules/titulo/model.php";
require_once "modules/matricula/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/resolucion/model.php";
require_once "modules/movimientotipopago/model.php";
require_once "modules/cuentacorrientematriculado/model.php";
require_once "modules/configuracion/model.php";
require_once "modules/comprobantepago/model.php";
require_once "modules/tipofactura/model.php";
require_once "modules/usuario/model.php";


class MatriculadoController {
	function __construct() {
		$this->model = new Matriculado();
		$this->view = new MatriculadoView();
	}

	function panel() {
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
		$this->view->listar($matriculado_collection);
	}

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

	function guardar() {
		SessionHandler()->check_session();
		$this->model->apellido = strtoupper(filter_input(INPUT_POST, 'apellido'));
		$this->model->nombre = strtoupper(filter_input(INPUT_POST, 'nombre'));
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->barrio = strtoupper(filter_input(INPUT_POST, 'barrio'));
		$this->model->domicilio = strtoupper(filter_input(INPUT_POST, 'domicilio'));
		$this->model->observacion = strtoupper(filter_input(INPUT_POST, 'observacion'));
		$this->model->save();
		$matriculado_id = $this->model->matriculado_id;

		$this->model = new Matriculado();
		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				$icm = new InfoContacto();
				$icm->denominacion = $clave;
				$icm->valor = $valor;
				$icm->save();
				$infocontacto_id = $icm->infocontacto_id;
				
				$icm = new InfoContacto();
				$icm->infocontacto_id = $infocontacto_id;
				$icm->get();

				$this->model->add_infocontacto($icm);
			}

			$iccm = new InfoContactoMatriculado($this->model);
			$iccm->save();
		}
	
		header("Location: " . URL_APP . "/matriculado/editar/{$matriculado_id}");
	}

	function guardar_matricula() {
		SessionHandler()->check_session();
		$matriculado_id = filter_input(INPUT_POST, 'matriculado_id');
		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$mm = new Matricula();
		$mm->matricula = filter_input(INPUT_POST, 'matricula');
		$mm->fecha_egreso = filter_input(INPUT_POST, 'fecha_egreso');
		$mm->fecha_inscripcion = filter_input(INPUT_POST, 'fecha_inscripcion');
		$mm->titulo = filter_input(INPUT_POST, 'titulo');
		$mm->universidad = filter_input(INPUT_POST, 'universidad');
		$mm->save();
		$matricula_id = $mm->matricula_id;

		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();

		$this->model->add_matricula($mm);
		$mmm = new MatriculaMatriculado($this->model);
		$mmm->save();
		
		header("Location: " . URL_APP . "/matriculado/matriculas/{$matriculado_id}");
	}

	function actualizar() {
		SessionHandler()->check_session();
		$matriculado_id = filter_input(INPUT_POST, 'matriculado_id');
		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();
		$this->model->apellido = filter_input(INPUT_POST, 'apellido');
		$this->model->nombre = filter_input(INPUT_POST, 'nombre');
		$this->model->documento = filter_input(INPUT_POST, 'documento');
		$this->model->documentotipo = filter_input(INPUT_POST, 'documentotipo');
		$this->model->provincia = filter_input(INPUT_POST, 'provincia');
		$this->model->codigopostal = filter_input(INPUT_POST, 'codigopostal');
		$this->model->barrio = filter_input(INPUT_POST, 'barrio');
		$this->model->domicilio = filter_input(INPUT_POST, 'domicilio');
		$this->model->observacion = filter_input(INPUT_POST, 'observacion');
		$this->model->save();
		
		$this->model = new Matriculado();
		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$array_infocontacto = $_POST['infocontacto'];
		if (!empty($array_infocontacto)) {
			foreach ($array_infocontacto as $clave=>$valor) {
				$icm = new InfoContacto();
				$icm->infocontacto_id = $clave;
				$icm->get();
				$icm->valor = $valor;
				$icm->save();
			}
		}
	
		header("Location: " . URL_APP . "/matriculado/editar/{$matriculado_id}");
	}

	function actualizar_matricula() {
		SessionHandler()->check_session();
		$matriculado_id = filter_input(INPUT_POST, 'matriculado_id');
		$matricula_id = filter_input(INPUT_POST, 'matricula_id');
		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();
		$mm->matricula = filter_input(INPUT_POST, 'matricula');
		$mm->fecha_egreso = filter_input(INPUT_POST, 'fecha_egreso');
		$mm->fecha_inscripcion = filter_input(INPUT_POST, 'fecha_inscripcion');
		$mm->titulo = filter_input(INPUT_POST, 'titulo');
		$mm->universidad = filter_input(INPUT_POST, 'universidad');
		$mm->save();
		
		header("Location: " . URL_APP . "/matriculado/matriculas/{$matriculado_id}");
	}

	function eliminar_matricula($arg) {
		SessionHandler()->check_session();
		$datos = explode('@', $arg);
		$matriculado_id = $datos[0];
		$matricula_id = $datos[1];

		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->delete();
		header("Location: " . URL_APP . "/matriculado/matriculas/{$matriculado_id}");
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

	function verifica_documento_ajax($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "matriculado m";
		$where = "m.documento = {$arg}";
		$flag = CollectorCondition()->get('Matriculado', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}

	function verifica_matricula_ajax($arg) {
		$select = "COUNT(*) AS DUPLICADO";
		$from = "matricula m";
		$where = "m.matricula = '{$arg}'";
		$flag = CollectorCondition()->get('Matricula', $where, 4, $from, $select);
		print $flag[0]["DUPLICADO"];
	}

	function editar_matricula_ajax($arg) {
		$ids = explode('@', $arg);
		$matriculado_id = $ids[0];
		$matricula_id = $ids[1];

		$universidad_collection = Collector()->get('Universidad');
		$titulo_collection = Collector()->get('Titulo');

		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();
		$this->view->editar_matricula_ajax($universidad_collection, $titulo_collection, $mm, $matriculado_id);
	}

	function agregar_matricula_ajax($arg) {
		$matriculado_id = $arg;
		$universidad_collection = Collector()->get('Universidad');
		$titulo_collection = Collector()->get('Titulo');
		$this->view->agregar_matricula_ajax($universidad_collection, $titulo_collection, $matriculado_id);
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

	function traer_valor_resolucion_ajax($arg) {
		$resolucion_id = $arg;
		$rm = new Resolucion();
		$rm->resolucion_id = $resolucion_id;
		$rm->get();
		
		$descuento = $rm->descuento;
		$recarga = $rm->recarga;

		if ($descuento == 0 AND $recarga == 0) {
			$valor = 0;
			$accion = 0;
		} elseif ($descuento == 0) {
			$valor = $recarga;
			$accion = 1;
		} elseif ($recarga == 0) {
			$valor = $descuento;
			$accion = 2;
		}

		print "{$accion}@{$valor}";
	}

	function ingresar_pago() {
		require_once "tools/facturaPDFTool.php";
		require_once "tools/email.php";
		
		$movimientotipopago_id = filter_input(INPUT_POST, 'tipopago');
		switch ($movimientotipopago_id) {
			case 1:
				$movimientotipopago_denominacion = 'Efectivo';
				$numero_movimiento = 0;
				$fecha_vencimiento = NULL;
				break;
			case 2:
				$movimientotipopago_denominacion = 'Cheque';
				$numero_movimiento = filter_input(INPUT_POST, 'numero_cheque');
				$fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
				break;
			case 3:
				$movimientotipopago_denominacion = 'Transferencia';
				$numero_movimiento = filter_input(INPUT_POST, 'numero_transferencia');
				$fecha_vencimiento = NULL;
				break;
		}

		$matriculado_id = filter_input(INPUT_POST, 'matriculado_id');
		$matricula_id = filter_input(INPUT_POST, 'matricula_id');
		$cuentacorrientematriculado_id = filter_input(INPUT_POST, 'cuentacorrientematriculado_id');
		$valor_abonado = filter_input(INPUT_POST, 'valor_abonado');

		$ccmm = new CuentaCorrienteMatriculado();
		$ccmm->cuentacorrientematriculado_id = filter_input(INPUT_POST, 'cuentacorrientematriculado_id');
		$ccmm->get();
		$movimientotipopago_id = $ccmm->movimientotipopago_id;
		
		$ccmm->valor_abonado = filter_input(INPUT_POST, 'valor_abonado');
		$ccmm->fecha = date('Y-m-d');
		$ccmm->hora = date('H-i-s');
		$ccmm->conceptopago = filter_input(INPUT_POST, 'conceptopago');
		$ccmm->resolucion_id = filter_input(INPUT_POST, 'resolucion');
		$ccmm->estado = 1;
		$ccmm->save();

		$mtpm = new MovimientoTipoPago();
		$mtpm->movimientotipopago_id = $movimientotipopago_id;
		$mtpm->get();
		$mtpm->denominacion = $movimientotipopago_denominacion;
		$mtpm->numero_movimiento = $numero_movimiento;
		$mtpm->fecha_vencimiento = $fecha_vencimiento;
		$mtpm->estado = 1;
		$mtpm->save();

		$cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();
        $tipofactura_id = $cm->tipofactura; 

		if ($tipofactura_id != 2) {
			/*
			$tipofactura_afip = $tfm->afip_id;
			$siguiente_factura = FacturaAFIPTool()->traerSiguienteFacturaAFIP($tipofactura_afip);
			*/
		} else {
			$select = "(MAX(cp.numero_factura) + 1 ) AS SIGUIENTE_NUMERO ";
			$from = "comprobantepago cp";
			$where = "cp.tipofactura = 2";
			$groupby = "cp.tipofactura";
			$nuevo_numero = CollectorCondition()->get('ComprobantePago', $where, 4, $from, $select, $groupby);
			$nuevo_numero = (!is_array($nuevo_numero)) ? 1 : $nuevo_numero[0]['SIGUIENTE_NUMERO'];
		}

		$cpm = new ComprobantePago();
		$cpm->punto_venta = $punto_venta;
		$cpm->numero_factura = $nuevo_numero;
		$cpm->fecha = date('Y-m-d');
		$cpm->hora = date('H:i:s');
		$cpm->subtotal = $valor_abonado;
		$cpm->importe_total = $valor_abonado;
		$cpm->emitido = 1;
		$cpm->cuentacorrientematriculado_id = $cuentacorrientematriculado_id;
		$cpm->tipofactura = $tipofactura_id;
		$cpm->save();
		$comprobantepago_id = $cpm->comprobantepago_id;

		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();
		
		$ccmm = new CuentaCorrienteMatriculado();
		$ccmm->cuentacorrientematriculado_id = $cuentacorrientematriculado_id;
		$ccmm->get();

		$cpm = new ComprobantePago();
		$cpm->comprobantepago_id = $comprobantepago_id;
		$cpm->get();

		$facturaPDFHelper = new FacturaPDF();
		$facturaPDFHelper->comprobante_pago($this->model, $mm, $ccmm, $cpm, $cm);
		
		$this->model = new Matriculado();
		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$emailHelper = new EmailHelper();
		$emailHelper->envia_comprobante($this->model, $mm, $ccmm, $cpm);

		header("Location: " . URL_APP . "/matriculado/gestionar_matricula/{$matriculado_id}@{$matricula_id}");
	}

	function ingresar_pago_otros() {
		require_once "tools/facturaAFIPTool.php";
		require_once "tools/facturaPDFTool.php";
		require_once "tools/email.php";

		$movimientotipopago_id = filter_input(INPUT_POST, 'tipopago');
		switch ($movimientotipopago_id) {
			case 1:
				$movimientotipopago_denominacion = 'Efectivo';
				$numero_movimiento = 0;
				$fecha_vencimiento = NULL;
				break;
			case 2:
				$movimientotipopago_denominacion = 'Cheque';
				$numero_movimiento = filter_input(INPUT_POST, 'numero_cheque');
				$fecha_vencimiento = filter_input(INPUT_POST, 'fecha_vencimiento');
				break;
			case 3:
				$movimientotipopago_denominacion = 'Transferencia';
				$numero_movimiento = filter_input(INPUT_POST, 'numero_transferencia');
				$fecha_vencimiento = NULL;
				break;
		}

		$matriculado_id = filter_input(INPUT_POST, 'matriculado_id');
		$matricula_id = filter_input(INPUT_POST, 'matricula_id');
		$valor_abonado = filter_input(INPUT_POST, 'importe');
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$conceptopago = filter_input(INPUT_POST, 'conceptopago');
		
		/* ---------------------------------------------------------------------------------------- */
		$cm = new Configuracion();
        $cm->configuracion_id = 1;
        $cm->get();
        $punto_venta = $cm->punto_venta;
        
		$tipofactura_id = 8; // Para recibos tipo C - CONSUMIDOR FINAL
		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tipofactura_id;
		$tfm->get();

		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$resultadoAFIP = FacturaAFIPTool()->facturarAFIP($cm, $tfm, $this->model, $valor_abonado);
		print_r($resultadoAFIP);exit;
		if (is_array($resultadoAFIP)) {
			$mtpm = new MovimientoTipoPago();
			$mtpm->denominacion = $movimientotipopago_denominacion;
			$mtpm->numero_movimiento = $numero_movimiento;
			$mtpm->fecha_vencimiento = $fecha_vencimiento;
			$mtpm->estado = 1;
			$mtpm->save();
			$movimientotipopago_id = $mtpm->movimientotipopago_id;

			$ccmm = new CuentaCorrienteMatriculado();
			$ccmm->anio = date('Y');
			$ccmm->valor_abonado = $valor_abonado;
			$ccmm->valor_matricula = $valor_abonado;
			$ccmm->fecha = date('Y-m-d');
			$ccmm->hora = date('H:i:s');
			$ccmm->habilitacion = 0;
			$ccmm->tomo = 0;
			$ccmm->numero_cuota = 1;
			$ccmm->total_cuotas = 1;
			$ccmm->estado = 1;
			$ccmm->movimientotipopago_id = $movimientotipopago_id;
			$ccmm->resolucion_id = 0;
			$ccmm->matriculado_id = $matriculado_id;
			$ccmm->matricula_id = $matricula_id;
			$ccmm->usuario_id = $usuario_id;
			$ccmm->conceptopago = $conceptopago;
			$ccmm->save();
			$cuentacorrientematriculado_id = $ccmm->cuentacorrientematriculado_id;

			$cpm = new ComprobantePago();
			$cpm->punto_venta = $punto_venta;
			$cpm->numero_factura = $resultadoAFIP['NUMFACTURA'];
			$cpm->cae = $resultadoAFIP['CAE'];
			$cpm->cae_vencimiento = $resultadoAFIP['CAEFchVto'];
			$cpm->fecha = date('Y-m-d');
			$cpm->hora = date('H:i:s');
			$cpm->subtotal = $valor_abonado;
			$cpm->importe_total = $valor_abonado;
			$cpm->emitido = 1;
			$cpm->cuentacorrientematriculado_id = $cuentacorrientematriculado_id;
			$cpm->tipofactura = $tipofactura_id;
			$cpm->save();
			$comprobantepago_id = $cpm->comprobantepago_id;

			$this->model->matriculado_id = $matriculado_id;
			$this->model->get();

			$mm = new Matricula();
			$mm->matricula_id = $matricula_id;
			$mm->get();
			
			$ccmm = new CuentaCorrienteMatriculado();
			$ccmm->cuentacorrientematriculado_id = $cuentacorrientematriculado_id;
			$ccmm->get();

			$cpm = new ComprobantePago();
			$cpm->comprobantepago_id = $comprobantepago_id;
			$cpm->get();

			$facturaPDFHelper = new FacturaPDF();
			$facturaPDFHelper->comprobante_pago($this->model, $mm, $ccmm, $cpm, $cm);
			
			$this->model = new Matriculado();
			$this->model->matriculado_id = $matriculado_id;
			$this->model->get();

			$emailHelper = new EmailHelper();
			$emailHelper->envia_comprobante($this->model, $mm, $ccmm, $cpm);
		}
		
		header("Location: " . URL_APP . "/matriculado/matriculas/{$matriculado_id}");
	}

	function guardar_habilitacion() {
		$matriculado_id = filter_input(INPUT_POST, 'matriculado_id');
		$matricula_id = filter_input(INPUT_POST, 'matricula_id');
		$total_cuotas = filter_input(INPUT_POST, 'total_cuotas');
		$habilitacion = filter_input(INPUT_POST, 'habilitacion');
		$tomo = filter_input(INPUT_POST, 'tomo');
		$valor_matricula = filter_input(INPUT_POST, 'valor_matricula');
		$anio = filter_input(INPUT_POST, 'anio');
		$interes = filter_input(INPUT_POST, 'interes');
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		if ($total_cuotas > 1) {
			$array_cuotas = $_POST["cuota"];
			$array_vencimientos = $_POST["vencimiento"];
			foreach ($array_cuotas as $clave=>$valor) {
				$mtpm = new MovimientoTipoPago();
				$mtpm->denominacion = "PENDIENTE";
				$mtpm->numero_movimiento = 0;
				$mtpm->fecha_vencimiento = NULL;
				$mtpm->estado = 0;
				$mtpm->save();
				$movimientotipopago_id = $mtpm->movimientotipopago_id;

				$ccmm = new CuentaCorrienteMatriculado();
				$ccmm->anio = $anio;
				$ccmm->valor_abonado = $valor;
				$ccmm->valor_matricula = $valor_matricula;
				$ccmm->fecha = $array_vencimientos[$clave];
				$ccmm->hora = date('H:i:s');
				$ccmm->habilitacion = $habilitacion;
				$ccmm->tomo = $tomo;
				$ccmm->numero_cuota = $clave;
				$ccmm->total_cuotas = $total_cuotas;
				$ccmm->estado = 0;
				$ccmm->conceptopago = 3;
				$ccmm->movimientotipopago_id = $movimientotipopago_id;
				$ccmm->resolucion_id = 0;
				$ccmm->matriculado_id = $matriculado_id;
				$ccmm->matricula_id = $matricula_id;
				$ccmm->usuario_id = $usuario_id;
				$ccmm->save();	
			}
		} else {
			$mtpm = new MovimientoTipoPago();
			$mtpm->denominacion = "PENDIENTE";
			$mtpm->numero_movimiento = 0;
			$mtpm->fecha_vencimiento = date('Y-m-d');
			$mtpm->estado = 0;
			$mtpm->save();
			$movimientotipopago_id = $mtpm->movimientotipopago_id;

			$ccmm = new CuentaCorrienteMatriculado();
			$ccmm->anio = $anio;
			$ccmm->valor_abonado = $valor_matricula;
			$ccmm->valor_matricula = $valor_matricula;
			$ccmm->fecha = date('Y-m-d');
			$ccmm->hora = date('H:i:s');
			$ccmm->habilitacion = $habilitacion;
			$ccmm->tomo = $tomo;
			$ccmm->numero_cuota = 1;
			$ccmm->total_cuotas = 1;
			$ccmm->estado = 0;
			$ccmm->conceptopago = 3;
			$ccmm->movimientotipopago_id = $movimientotipopago_id;
			$ccmm->resolucion_id = 0;
			$ccmm->matriculado_id = $matriculado_id;
			$ccmm->matricula_id = $matricula_id;
			$ccmm->usuario_id = $usuario_id;
			//print_r($ccmm);exit;
			$ccmm->save();	
		}

		header("Location: " . URL_APP . "/matriculado/gestionar_matricula/{$matriculado_id}@{$matricula_id}");
	}

	function cambiar_estado_matricula() {
		$matriculado_id = filter_input(INPUT_POST, 'matriculado_id');
		$matricula_id = filter_input(INPUT_POST, 'matricula_id');
		$estado = filter_input(INPUT_POST, 'estado_id');
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];
		$estado_denominacion = ($estado == 1) ? 'ACTIVA' : 'INACTIVA';

		$um = new Usuario();
		$um->usuario_id = $usuario_id;
		$um->get();
		$usuario = $um->denominacion;

		$em = new Estado();
		$em->denominacion = $estado_denominacion;
		$em->detalle = filter_input(INPUT_POST, 'detalle');
		$em->perfil = 'admin';
		$em->usuario = $usuario;
		$em->save();
		$estado_id = $em->estado_id;

		$em = new Estado();
		$em->estado_id = $estado_id;
		$em->get();
		
		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();
		$mm->add_estado($em);

		$emm = new EstadoMatricula($mm);
		$emm->save();

		header("Location: " . URL_APP . "/matriculado/matriculas/{$matriculado_id}");
	}

	function eliminar_pago_matricula($arg) {
		$ids = explode("@", $arg);
		$matriculado_id = $ids[0];
		$matricula_id = $ids[1];
		$cuentacorrientematriculado_id = $ids[2];

		$ccmm = new CuentaCorrienteMatriculado();
		$ccmm->cuentacorrientematriculado_id = $cuentacorrientematriculado_id;
		$ccmm->get();
		$movimientotipopago_id = $ccmm->movimientotipopago_id;
		$ccmm->delete();

		$mtpm = new MovimientoTipoPago();
		$mtpm->movimientotipopago_id = $movimientotipopago_id;
		$mtpm->delete();
		
		header("Location: " . URL_APP . "/matriculado/gestionar_matricula/{$matriculado_id}@{$matricula_id}");
	}

	function anular_pago_matricula($arg) {
		$ids = explode("@", $arg);
		$matriculado_id = $ids[0];
		$matricula_id = $ids[1];
		$cuentacorrientematriculado_id = $ids[2];
		$usuario_id = $_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"];

		$ccmm = new CuentaCorrienteMatriculado();
		$ccmm->cuentacorrientematriculado_id = $cuentacorrientematriculado_id;
		$ccmm->get();
		$movimientotipopago_id = $ccmm->movimientotipopago_id;
		$ccmm->estado = 0;
		$ccmm->resolucion_id = 0;
		$ccmm->usuario_id = $usuario_id;
		$ccmm->conceptopago = 3;
		$ccmm->save();

		$mtpm = new MovimientoTipoPago();
		$mtpm->movimientotipopago_id = $movimientotipopago_id;
		$mtpm->get();
		$mtpm->denominacion = 'PENDIENTE';
		$mtpm->numero_movimiento = 0;
		$mtpm->fecha_vencimiento = NULL;
		$mtpm->estado = 0;
		$mtpm->save();
		
		header("Location: " . URL_APP . "/matriculado/gestionar_matricula/{$matriculado_id}@{$matricula_id}");
	}

	function traer_form_abonar_ajax($arg) {
		$ids = explode("@", $arg);
		$matriculado_id = $ids[0];
		$matricula_id = $ids[1];
		$cuentacorrientematriculado_id = $ids[2];
		$fecha = date('Y-m-d');

		$this->model->matriculado_id = $matriculado_id;
		$this->model->get();

		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();

		$ccmm = new CuentaCorrienteMatriculado();
		$ccmm->cuentacorrientematriculado_id = $cuentacorrientematriculado_id;
		$ccmm->get();
		
		$select = "r.resolucion_id AS RESID, r.denominacion AS RESOLUCION";
		$from = "resolucion r";
		$where = "r.estado = 1 AND '{$fecha}' BETWEEN r.fecha_desde AND r.fecha_hasta";
		$resolucion_collection = CollectorCondition()->get('Resolucion', $where, 4, $from, $select);
		$conceptopago_collection = Collector()->get('ConceptoPago');

		$this->view->traer_form_abonar_ajax($this->model, $mm, $ccmm, $resolucion_collection, $conceptopago_collection);
	}

	function verifica_habilitacion_ajax($arg) {
		$ids = explode("@", $arg);
		$matriculado_id = $ids[0];
		$matricula_id = $ids[1];
		$anio = $ids[2];

		$select = "COUNT(anio) AS CANT";
		$from = "cuentacorrientematriculado ccm";
		$where = "ccm.matriculado_id = {$matriculado_id} AND ccm.matricula_id = {$matricula_id} AND ccm.anio = {$anio}";
		$group_by = "ccm.anio";
		$habilitacion_anual_flag = CollectorCondition()->get('CuentaCorrienteMatriculado', $where, 4, $from, $select, $group_by);
		if (is_array($habilitacion_anual_flag) AND !empty($habilitacion_anual_flag)) {
			$habilitacion_anual_flag = $habilitacion_anual_flag[0]["CANT"];
		} else {
			$habilitacion_anual_flag = 0;
		}
		
		print $habilitacion_anual_flag;
	}

	function traer_inhabilitacion_admin_ajax($arg) {
		$matricula_id = $arg;

		$mm = new Matricula();
		$mm->matricula_id = $matricula_id;
		$mm->get();
		$estado_collection = $mm->estado_collection;
		$ultimo_estado = end($estado_collection);
		$usuario = $ultimo_estado->usuario;
		$perfil = $ultimo_estado->perfil;
		$detalle = $ultimo_estado->detalle;

		print "{$usuario}@{$perfil}@{$detalle}";
	}

	function traer_tipos_facturas() {
		require_once "common/libs/desa_afip.php-master/src/Afip.php";
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$CUIT = $cm->cuit;
        $afip = new Afip(array('CUIT' => $CUIT, 'production' => false));
        $voucher_types = $afip->ElectronicBilling->GetVoucherTypes();
        print_r($voucher_types);exit;
        return $voucher_types;
	}
}
?>