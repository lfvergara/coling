<?php


class TituloView extends View {
	function panel($titulo_collection) {
		$gui = file_get_contents("static/modules/titulo/panel.html");
		$gui_tbl_titulo = file_get_contents("static/modules/titulo/tbl_titulo.html");
		$gui_tbl_titulo = $this->render_regex('TBL_TITULO', $gui_tbl_titulo, $titulo_collection);

		$render = str_replace('{tbl_titulo}', $gui_tbl_titulo, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
	
	function editar($titulo_collection, $obj_titulo) {
		$gui = file_get_contents("static/modules/titulo/editar.html");
		$gui_tbl_titulo = file_get_contents("static/modules/titulo/tbl_titulo.html");
		$gui_tbl_titulo = $this->render_regex('TBL_TITULO', $gui_tbl_titulo, $titulo_collection);
		
		$obj_titulo = $this->set_dict($obj_titulo);
		$render = str_replace('{tbl_titulo}', $gui_tbl_titulo, $gui);
		$render = $this->render($obj_titulo, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function consultar($obj_titulo) {
		$gui = file_get_contents("static/modules/titulo/consultar.html");
		$obj_titulo = $this->set_dict($obj_titulo);
		$render = $this->render($obj_titulo, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}
}
?>