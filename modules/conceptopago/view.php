<?php


class ConceptoPagoView extends View {
	function panel($conceptopago_collection) {
		$gui = file_get_contents("static/modules/conceptopago/panel.html");
		$gui_tbl_conceptopago = file_get_contents("static/modules/conceptopago/tbl_conceptopago.html");
		$gui_tbl_conceptopago = $this->render_regex('TBL_CONCEPTOPAGO', $gui_tbl_conceptopago, $conceptopago_collection);

		$render = str_replace('{tbl_conceptopago}', $gui_tbl_conceptopago, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($conceptopago_collection, $obj_conceptopago) {
		$gui = file_get_contents("static/modules/conceptopago/editar.html");
		$gui_tbl_conceptopago = file_get_contents("static/modules/conceptopago/tbl_conceptopago.html");
		$gui_tbl_conceptopago = $this->render_regex('TBL_CONCEPTOPAGO', $gui_tbl_conceptopago, $conceptopago_collection);
		
		$obj_conceptopago = $this->set_dict($obj_conceptopago);
		$render = str_replace('{tbl_conceptopago}', $gui_tbl_conceptopago, $gui);
		$render = $this->render($obj_conceptopago, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_conceptopago) {
		$gui = file_get_contents("static/modules/conceptopago/consultar.html");
		$obj_conceptopago = $this->set_dict($obj_conceptopago);
		$render = $this->render($obj_conceptopago, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>