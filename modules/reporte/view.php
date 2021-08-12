<?php


class ReporteView extends View {
	function admin($array_datos) {
		$gui = file_get_contents("static/modules/reporte/admin.html");
		$render = $this->render($array_datos, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function operador() {
		$gui = file_get_contents("static/modules/reporte/operador.html");
		$render = $this->render_breadcrumb($gui);
		$template = $this->render_template($render);
		print $template;
	}	
}
?>