<?php


class UniversidadView extends View {
	function panel($universidad_collection, $provincia_collection) {
		$gui = file_get_contents("static/modules/universidad/panel.html");
		$gui_tbl_universidad = file_get_contents("static/modules/universidad/tbl_universidad.html");
		$gui_tbl_universidad = $this->render_regex('TBL_UNIVERSIDAD', $gui_tbl_universidad, $universidad_collection);
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);

		$render = str_replace('{tbl_universidad}', $gui_tbl_universidad, $gui);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($universidad_collection, $provincia_collection, $obj_universidad) {
		$gui = file_get_contents("static/modules/universidad/editar.html");
		$gui_tbl_universidad = file_get_contents("static/modules/universidad/tbl_universidad.html");
		$gui_tbl_universidad = $this->render_regex('TBL_UNIVERSIDAD', $gui_tbl_universidad, $universidad_collection);
		$gui_slt_provincia = file_get_contents("static/common/slt_provincia.html");
		$gui_slt_provincia = $this->render_regex('SLT_PROVINCIA', $gui_slt_provincia, $provincia_collection);
		
		$obj_universidad = $this->set_dict($obj_universidad);
		$render = str_replace('{tbl_universidad}', $gui_tbl_universidad, $gui);
		$render = str_replace('{slt_provincia}', $gui_slt_provincia, $render);
		$render = $this->render($obj_universidad, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_universidad) {
		$gui = file_get_contents("static/modules/universidad/consultar.html");
		$obj_universidad = $this->set_dict($obj_universidad);
		$render = $this->render($obj_universidad, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>