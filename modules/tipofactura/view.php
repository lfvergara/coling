<?php


class TipoFacturaView extends View {
	
	function panel($tipofactura_collection) {
		$gui = file_get_contents("static/modules/tipofactura/panel.html");

		$render = $this->render_regex('TBL_TIPOFACTURA', $gui, $tipofactura_collection);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($tipofactura_collection, $obj_tipofactura) {
		$gui = file_get_contents("static/modules/tipofactura/editar.html");
		$obj_tipofactura = $this->set_dict($obj_tipofactura);
		$render = $this->render_regex('TBL_TIPOFACTURA', $gui, $tipofactura_collection);
		$render = $this->render($obj_tipofactura, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>