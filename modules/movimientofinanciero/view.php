<?php


class MovimientoFinancieroView extends View {
	function caja($movimientofinanciero_collection, $conceptopago_collection) {
		$gui = file_get_contents("static/modules/movimientofinanciero/caja.html");
		$gui_slt_conceptopago = file_get_contents("static/common/slt_conceptopago.html");
		$gui_tbl_movimientofinanciero = file_get_contents("static/modules/movimientofinanciero/tbl_caja.html");
		
		foreach ($conceptopago_collection as $clave=>$valor) {
			if ($valor->tipo != 3) unset($conceptopago_collection[$clave]);
		}

		$gui_slt_conceptopago = $this->render_regex('SLT_CONCEPTOPAGO', $gui_slt_conceptopago, $conceptopago_collection);
		$gui_tbl_movimientofinanciero = $this->render_regex_dict('TBL_MOVIMIENTOFINANCIERO', $gui_tbl_movimientofinanciero, $movimientofinanciero_collection);
		$render = str_replace('{tbl_movimientofinanciero}', $gui_tbl_movimientofinanciero, $gui);
		$render = str_replace('{slt_conceptopago}', $gui_slt_conceptopago, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function banco($movimientofinanciero_collection, $conceptopago_collection) {
		$gui = file_get_contents("static/modules/movimientofinanciero/banco.html");
		$gui_slt_conceptopago = file_get_contents("static/common/slt_conceptopago.html");
		$gui_tbl_movimientofinanciero = file_get_contents("static/modules/movimientofinanciero/tbl_banco.html");
		
		foreach ($conceptopago_collection as $clave=>$valor) {
			if ($valor->tipo != 3) unset($conceptopago_collection[$clave]);
		}

		$gui_slt_conceptopago = $this->render_regex('SLT_CONCEPTOPAGO', $gui_slt_conceptopago, $conceptopago_collection);
		$gui_tbl_movimientofinanciero = $this->render_regex_dict('TBL_MOVIMIENTOFINANCIERO', $gui_tbl_movimientofinanciero, $movimientofinanciero_collection);
		$render = str_replace('{tbl_movimientofinanciero}', $gui_tbl_movimientofinanciero, $gui);
		$render = str_replace('{slt_conceptopago}', $gui_slt_conceptopago, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar_caja($movimientofinanciero_collection, $conceptopago_collection, $obj_movimientofinanciero) {
		$gui = file_get_contents("static/modules/movimientofinanciero/editar_caja.html");
		$gui_slt_conceptopago = file_get_contents("static/common/slt_conceptopago.html");
		$gui_tbl_movimientofinanciero = file_get_contents("static/modules/movimientofinanciero/tbl_caja.html");

		foreach ($conceptopago_collection as $clave=>$valor) {
			if ($valor->tipo != 3) unset($conceptopago_collection[$clave]);
		}
		
		$obj_movimientofinanciero = $this->set_dict($obj_movimientofinanciero);
		$gui_slt_conceptopago = $this->render_regex('SLT_CONCEPTOPAGO', $gui_slt_conceptopago, $conceptopago_collection);
		$gui_tbl_movimientofinanciero = $this->render_regex_dict('TBL_MOVIMIENTOFINANCIERO', $gui_tbl_movimientofinanciero, $movimientofinanciero_collection);
		$render = str_replace('{tbl_movimientofinanciero}', $gui_tbl_movimientofinanciero, $gui);
		$render = str_replace('{slt_conceptopago}', $gui_slt_conceptopago, $render);
		$render = $this->render($obj_movimientofinanciero, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function editar_banco($movimientofinanciero_collection, $conceptopago_collection, $obj_movimientofinanciero) {
		$gui = file_get_contents("static/modules/movimientofinanciero/editar_banco.html");
		$gui_slt_conceptopago = file_get_contents("static/common/slt_conceptopago.html");
		$gui_tbl_movimientofinanciero = file_get_contents("static/modules/movimientofinanciero/tbl_banco.html");

		foreach ($conceptopago_collection as $clave=>$valor) {
			if ($valor->tipo != 3) unset($conceptopago_collection[$clave]);
		}
		
		switch ($obj_movimientofinanciero->denominacion) {
			case 'Cheque':
				$array_tipopago = array("{display-numero_cheque}"=>'inline-block',
										"{display-fecha_vencimiento}"=>'inline-block',
										"{display-numero_transferencia}"=>'none',
										"{slt_cheque_selected}"=>'selected',
										"{slt_transferencia_selected}"=>'',
										"{slt_efectivo_selected}"=>'');
				break;
			case 'Transferencia':
				$array_tipopago = array("{display-numero_cheque}"=>'none',
										"{display-fecha_vencimiento}"=>'none',
										"{display-numero_transferencia}"=>'inline-block',
										"{slt_cheque_selected}"=>'',
										"{slt_transferencia_selected}"=>'selected',
										"{slt_efectivo_selected}"=>'');
				break;
			case 'Efectivo':
				$array_tipopago = array("{display-numero_cheque}"=>'none',
										"{display-fecha_vencimiento}"=>'none',
										"{display-numero_transferencia}"=>'none',
										"{slt_cheque_selected}"=>'',
										"{slt_transferencia_selected}"=>'',
										"{slt_efectivo_selected}"=>'selected');
				break;
		}

		$obj_movimientofinanciero = $this->set_dict($obj_movimientofinanciero);
		$gui_slt_conceptopago = $this->render_regex('SLT_CONCEPTOPAGO', $gui_slt_conceptopago, $conceptopago_collection);
		$gui_tbl_movimientofinanciero = $this->render_regex_dict('TBL_MOVIMIENTOFINANCIERO', $gui_tbl_movimientofinanciero, $movimientofinanciero_collection);
		$render = str_replace('{tbl_movimientofinanciero}', $gui_tbl_movimientofinanciero, $gui);
		$render = str_replace('{slt_conceptopago}', $gui_slt_conceptopago, $render);
		$render = $this->render($obj_movimientofinanciero, $render);
		$render = $this->render($array_tipopago, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_movimientofinanciero) {
		$gui = file_get_contents("static/modules/movimientofinanciero/consultar.html");
		$obj_movimientofinanciero = $this->set_dict($obj_movimientofinanciero);
		$render = $this->render($obj_movimientofinanciero, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>