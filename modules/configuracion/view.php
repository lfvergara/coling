<?php


class ConfiguracionView extends View {
	
	function panel($obj_configuracion) {
		$gui = file_get_contents("static/modules/configuracion/panel.html");
		
		$obj_configuracion = $this->set_dict($obj_configuracion);
		$render = $this->render($obj_configuracion, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>