<?php


class MatriculadoView extends View {
	function panel() {
		$gui = file_get_contents("static/modules/matriculado/panel.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}

	function listar($matriculado_collection) {
		$gui = file_get_contents("static/modules/matriculado/listar.html");
		$tbl_matriculado_array = file_get_contents("static/modules/matriculado/tbl_matriculado_array.html");

		$tbl_matriculado_array = $this->render_regex_dict('TBL_MATRICULADO', $tbl_matriculado_array, $matriculado_collection);
		$render = str_replace('{tbl_matriculado}', $tbl_matriculado_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($provincia_collection, $documentotipo_collection) {
		$gui = file_get_contents("static/modules/matriculado/agregar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($provincia_collection, $documentotipo_collection, $obj_matriculado) {
		$gui = file_get_contents("static/modules/matriculado/editar.html");
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_documentotipo = file_get_contents("static/common/slt_documentotipo.html");
		$gui_lst_input_infocontacto = file_get_contents("static/modules/matriculado/lst_input_infocontacto.html");
		
		$infocontacto_collection = $obj_matriculado->infocontacto_collection;
		$gui_lst_input_infocontacto = $this->render_regex('LST_INPUT_INFOCONTACTO', $gui_lst_input_infocontacto, $infocontacto_collection);
		unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);
		$obj_matriculado = $this->set_dict($obj_matriculado);
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		$gui_slt_documentotipo = $this->render_regex('SLT_DOCUMENTOTIPO', $gui_slt_documentotipo, $documentotipo_collection);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $gui);
		$render = str_replace('{slt_documentotipo}', $gui_slt_documentotipo, $render);
		$render = str_replace('{lst_input_infocontacto}', $gui_lst_input_infocontacto, $render);
		$render = $this->render($obj_matriculado, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function matriculas($universidad_collection, $titulo_collection, $conceptopago_collection, $resolucion_collection,
						$movimientosmatriculado_collection, $deudamatriculado_collection, $obj_matriculado, $gui_array) {
		$gui = file_get_contents("static/modules/matriculado/matriculas.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$gui_slt_universidad = file_get_contents("static/common/slt_universidad.html");
		$gui_slt_universidad = $this->render_regex('SLT_UNIVERSIDAD', $gui_slt_universidad, $universidad_collection);
		$gui_slt_titulo = file_get_contents("static/common/slt_titulo.html");
		$gui_slt_titulo = $this->render_regex('SLT_TITULO', $gui_slt_titulo, $titulo_collection);
		$gui_slt_matricula = file_get_contents("static/common/slt_matricula.html");
		$gui_slt_conceptopago = file_get_contents("static/common/slt_conceptopago.html");
		$gui_slt_resolucion = file_get_contents("static/common/slt_resolucion_array.html");
		$gui_tbl_movimientos_matriculado = file_get_contents("static/modules/matriculado/tbl_movimientos_matriculado.html");

		$usu_perfil = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		if ($usu_perfil > 1) {
			$gui_tbl_matricula = file_get_contents("static/modules/matriculado/tbl_matricula.html");
		} else {
			$gui_tbl_matricula = file_get_contents("static/modules/matriculado/tbl_matricula_operador.html");
		}

		foreach ($conceptopago_collection as $clave=>$valor) {
			if ($valor->tipo != 4) unset($conceptopago_collection[$clave]);
		}

		if ($obj_matriculado->documentotipo->denominacion == 'CUIL' OR $obj_matriculado->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_matriculado->documento, 0, 2);
			$cuil2 = substr($obj_matriculado->documento, 2, 8);
			$cuil3 = substr($obj_matriculado->documento, 10);
			$obj_matriculado->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		$infocontacto_collection = $obj_matriculado->infocontacto_collection;
		$matricula_collection = $obj_matriculado->matricula_collection;
		unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);
		$obj_matriculado = $this->set_dict($obj_matriculado);
		
		$gui_slt_resolucion = $this->render_regex_dict('SLT_RESOLUCION', $gui_slt_resolucion, $resolucion_collection);
		$gui_slt_conceptopago = $this->render_regex('SLT_CONCEPTOPAGO', $gui_slt_conceptopago, $conceptopago_collection);
		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$gui_tbl_matricula = $this->render_regex('TBL_MATRICULA', $gui_tbl_matricula, $matricula_collection);
		$gui_slt_matricula = $this->render_regex('SLT_MATRICULA', $gui_slt_matricula, $matricula_collection);
		$gui_tbl_deuda_matriculado = $this->render_regex_dict('TBL_CUENTACORRIENTEMATRICULADO', $gui_tbl_movimientos_matriculado, $deudamatriculado_collection);
		$gui_tbl_movimientos_matriculado = $this->render_regex_dict('TBL_CUENTACORRIENTEMATRICULADO', $gui_tbl_movimientos_matriculado, $movimientosmatriculado_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = str_replace('{slt_universidad}', $gui_slt_universidad, $render);
		$render = str_replace('{slt_titulo}', $gui_slt_titulo, $render);
		$render = str_replace('{tbl_matricula}', $gui_tbl_matricula, $render);
		$render = str_replace('{slt_conceptopago}', $gui_slt_conceptopago, $render);
		$render = str_replace('{slt_resolucion}', $gui_slt_resolucion, $render);
		$render = str_replace('{slt_matricula}', $gui_slt_matricula, $render);
		$render = str_replace('{tbl_movimientos}', $gui_tbl_movimientos_matriculado, $render);
		$render = str_replace('{tbl_deuda}', $gui_tbl_deuda_matriculado, $render);
		$render = $this->render($obj_matriculado, $render);
		$render = $this->render($gui_array, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_matriculado) {
		$gui = file_get_contents("static/modules/matriculado/consultar.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");
		$gui_tbl_matricula = file_get_contents("static/modules/matriculado/tbl_small_matricula.html");
		if ($obj_matriculado->documentotipo->denominacion == 'CUIL' OR $obj_matriculado->documentotipo->denominacion == 'CUIT') {
			$cuil1 = substr($obj_matriculado->documento, 0, 2);
			$cuil2 = substr($obj_matriculado->documento, 2, 8);
			$cuil3 = substr($obj_matriculado->documento, 10);
			$obj_matriculado->documento = "{$cuil1}-{$cuil2}-{$cuil3}";
		}

		$infocontacto_collection = $obj_matriculado->infocontacto_collection;
		$matricula_collection = $obj_matriculado->matricula_collection;
		unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);
		$obj_matriculado = $this->set_dict($obj_matriculado);

		foreach ($matricula_collection as $clave=>$valor) {
			if (empty($valor->estado_collection)) {
				$estado_matricula = 'INACTIVA';
			} else {
				$ultimo_estado = end($valor->estado_collection);
				$estado_matricula = $ultimo_estado->denominacion;
			}

			unset($matricula_collection[$clave]->estado_collection);
			$class_estado = ($estado_matricula == 'ACTIVA') ? 'success' : 'danger';
			$matricula_collection[$clave]->estado_denominacion = $estado_matricula;
			$matricula_collection[$clave]->class_estado = $class_estado;
		}

		$gui_tbl_matricula = $this->render_regex('TBL_MATRICULA', $gui_tbl_matricula, $matricula_collection);
		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = str_replace('{tbl_matricula}', $gui_tbl_matricula, $render);
		$render = $this->render($obj_matriculado, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function editar_matricula_ajax($universidad_collection, $titulo_collection, $obj_matricula, $matriculado_id) {
		$gui = file_get_contents("static/modules/matriculado/editar_matricula.html");
		$gui_slt_universidad = file_get_contents("static/common/slt_universidad.html");
		$gui_slt_universidad = $this->render_regex('SLT_UNIVERSIDAD', $gui_slt_universidad, $universidad_collection);
		$gui_slt_titulo = file_get_contents("static/common/slt_titulo.html");
		$gui_slt_titulo = $this->render_regex('SLT_TITULO', $gui_slt_titulo, $titulo_collection);

		$obj_matricula = $this->set_dict($obj_matricula);
		$render = str_replace('{slt_universidad}', $gui_slt_universidad, $gui);
		$render = str_replace('{slt_titulo}', $gui_slt_titulo, $render);
		$render = $this->render($obj_matricula, $render);
		$render = str_replace('{matriculado-matriculado_id}', $matriculado_id, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;	
	}

	function agregar_matricula_ajax($universidad_collection, $titulo_collection, $matriculado_id) {
		$gui = file_get_contents("static/modules/matriculado/agregar_matricula.html");
		$gui_slt_universidad = file_get_contents("static/common/slt_universidad.html");
		$gui_slt_universidad = $this->render_regex('SLT_UNIVERSIDAD', $gui_slt_universidad, $universidad_collection);
		$gui_slt_titulo = file_get_contents("static/common/slt_titulo.html");
		$gui_slt_titulo = $this->render_regex('SLT_TITULO', $gui_slt_titulo, $titulo_collection);

		$render = str_replace('{slt_universidad}', $gui_slt_universidad, $gui);
		$render = str_replace('{slt_titulo}', $gui_slt_titulo, $render);
		$render = str_replace('{matriculado-matriculado_id}', $matriculado_id, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;	
	}

	function gestionar_matricula($obj_matriculado, $obj_matricula, $cuentacorrientematriculado_collection) {
		$gui = file_get_contents("static/modules/matriculado/gestionar_matricula.html");
		$gui_lst_infocontacto = file_get_contents("static/common/lst_infocontacto.html");

		$usu_perfil = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		if ($usu_perfil > 1) {
			$gui_tbl_cuentacorrientematriculado = file_get_contents("static/modules/matriculado/tbl_cuentacorrientematriculado_array.html");
		} else {
			$gui_tbl_cuentacorrientematriculado = file_get_contents("static/modules/matriculado/tbl_cuentacorrientematriculado_operador_array.html");
		}

		$infocontacto_collection = $obj_matriculado->infocontacto_collection;
		unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection, $obj_matricula->estado_collection);
		$obj_matriculado = $this->set_dict($obj_matriculado);
		$obj_matricula = $this->set_dict($obj_matricula);
		
		$gui_lst_infocontacto = $this->render_regex('LST_INFOCONTACTO', $gui_lst_infocontacto, $infocontacto_collection);
		$gui_tbl_cuentacorrientematriculado = $this->render_regex_dict('TBL_CUENTACORRIENTEMATRICULADO', $gui_tbl_cuentacorrientematriculado, $cuentacorrientematriculado_collection);
		
		$render = str_replace('{lst_infocontacto}', $gui_lst_infocontacto, $gui);
		$render = str_replace('{tbl_cuentacorrientematriculado}', $gui_tbl_cuentacorrientematriculado, $render);
		$render = $this->render($obj_matriculado, $render);
		$render = $this->render($obj_matricula, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function traer_form_abonar_ajax($obj_matriculado, $obj_matricula, $obj_cuentacorrientematriculado, $resolucion_collection, 
									$conceptopago_collection) {
		$gui = file_get_contents("static/modules/matriculado/formulario_abonar_ajax.html");
		$gui_slt_resolucion = file_get_contents("static/common/slt_resolucion_array.html");
		$gui_slt_conceptopago = file_get_contents("static/common/slt_conceptopago.html");

		foreach ($conceptopago_collection as $clave=>$valor) {
			if ($valor->tipo != 2) unset($conceptopago_collection[$clave]);
		}

		unset($obj_matriculado->infocontacto_collection, $obj_matriculado->matricula_collection);
		$obj_matriculado = $this->set_dict($obj_matriculado);
		$obj_matricula = $this->set_dict($obj_matricula);
		$obj_cuentacorrientematriculado = $this->set_dict($obj_cuentacorrientematriculado);
		$gui_slt_resolucion = $this->render_regex_dict('SLT_RESOLUCION', $gui_slt_resolucion, $resolucion_collection);
		$gui_slt_conceptopago = $this->render_regex('SLT_CONCEPTOPAGO', $gui_slt_conceptopago, $conceptopago_collection);		
		
		$render = str_replace('{slt_conceptopago}', $gui_slt_conceptopago, $gui);
		$render = str_replace('{slt_resolucion}', $gui_slt_resolucion, $render);
		$render = $this->render($obj_matriculado, $render);
		$render = $this->render($obj_matricula, $render);
		$render = $this->render($obj_cuentacorrientematriculado, $render);
		$render = str_replace('{url_app}', URL_APP, $render);
		print $render;
	}
}
?>