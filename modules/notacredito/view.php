<?php


class NotaCreditoView extends View {
	function listar($notacredito_collection) {
		$gui = file_get_contents("static/modules/notacredito/listar.html");
		$tbl_notacredito_array = file_get_contents("static/modules/notacredito/tbl_notacredito_array.html");
		$tbl_notacredito_array = $this->render_regex_dict('TBL_NOTACREDITO', $tbl_notacredito_array, $notacredito_collection);

		$render = str_replace('{tbl_notacredito}', $tbl_notacredito_array, $gui);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}	
}
?>