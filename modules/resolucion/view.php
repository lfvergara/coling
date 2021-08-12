<?php


class ResolucionView extends View {
	function panel($resolucion_collection) {
		$gui = file_get_contents("static/modules/resolucion/panel.html");
		$gui_tbl_resolucion = file_get_contents("static/modules/resolucion/tbl_resolucion.html");
		$gui_tbl_resolucion = $this->render_regex('TBL_RESOLUCION', $gui_tbl_resolucion, $resolucion_collection);
		
		$render = str_replace('{tbl_resolucion}', $gui_tbl_resolucion, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($resolucion_collection, $obj_resolucion) {
		$gui = file_get_contents("static/modules/resolucion/editar.html");
		$gui_tbl_resolucion = file_get_contents("static/modules/resolucion/tbl_resolucion.html");
		$gui_tbl_resolucion = $this->render_regex('TBL_RESOLUCION', $gui_tbl_resolucion, $resolucion_collection);
		
		$obj_resolucion = $this->set_dict($obj_resolucion);
		$render = str_replace('{tbl_resolucion}', $gui_tbl_resolucion, $gui);
		$render = $this->render($obj_resolucion, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_resolucion) {
		$gui = file_get_contents("static/modules/resolucion/consultar.html");
		$obj_resolucion = $this->set_dict($obj_resolucion);
		$render = $this->render($obj_resolucion, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>