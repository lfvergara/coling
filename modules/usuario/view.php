<?php


class UsuarioView extends View {
	
	function login($arg) {
		$template = $this->render_login();
		if ($arg == "mError")  {
			$gui_mError = file_get_contents("static/modules/usuario/mError.html");	
			$template = str_replace("{gui_mError}", $gui_mError, $template);
		} else {
			$template = str_replace("{gui_mError}", "", $template);
		}
		print $template;
	}

	function agregar($usuario_collection, $configuracionmenu_collection) {
		$gui = file_get_contents("static/modules/usuario/agregar.html");
		$slt_configuracionmenu = file_get_contents("static/modules/usuario/slt_configuracionmenu.html");
		$slt_configuracionmenu = $this->render_regex_dict('SLT_CONFIGURACIONMENU', $slt_configuracionmenu, $configuracionmenu_collection);

		$render = $this->render_regex_dict('TBL_USUARIO', $gui, $usuario_collection);
		$render = str_replace('{slt_configuracionmenu}', $slt_configuracionmenu, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($usuario_collection, $configuracionmenu_collection, $usuario) {
		$gui = file_get_contents("static/modules/usuario/editar.html");
		$slt_configuracionmenu = file_get_contents("static/modules/usuario/slt_configuracionmenu.html");
		$slt_configuracionmenu = $this->render_regex_dict('SLT_CONFIGURACIONMENU', $slt_configuracionmenu, $configuracionmenu_collection);
		
		$usuario_nivel = $usuario->nivel;
		unset($usuario->configuracionmenu->submenu_collection, $usuario->configuracionmenu->item_collection);
		$usuario = $this->set_dict($usuario);
		
		$render = $this->render_regex_dict('TBL_USUARIO', $gui, $usuario_collection);
		$render = str_replace('{slt_configuracionmenu}', $slt_configuracionmenu, $render);
		$render = $this->render($usuario, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;	
	}

	function perfil() {
		$gui = file_get_contents("static/modules/usuario/perfil.html");
		$actualiza_contrasena = $_SESSION["data-login-" . APP_ABREV]["usuario-actualiza_contrasena"];
		if ($actualiza_contrasena == 1) {
			$gui_alerta = file_get_contents("static/modules/usuario/alerta_cambia_contrasena.html");
		} else {
			$gui_alerta = "";
		}

		$dict_perfil = array(
			"{usuario-usuario_id}"=>$_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"],
			"{usuario-denominacion}"=>$_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"],
			"{usuario-nombre}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-nombre"],
			"{usuario-apellido}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-apellido"],
			"{usuario-nivel}"=>$_SESSION["data-login-" . APP_ABREV]["nivel-denominacion"],
			"{usuariodetalle-correoelectronico}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-correoelectronico"]);
		$render = $this->render($dict_perfil, $gui);
		$render = str_replace('{gui_alerta}', $gui_alerta, $render);
		$template = $this->render_template($render);
		print $template;
	}

	function admin() {
		$gui = file_get_contents("static/modules/usuario/admin.html");
		$dict_perfil = array(
			"{usuario-usuario_id}"=>$_SESSION["data-login-" . APP_ABREV]["usuario-usuario_id"],
			"{usuario-denominacion}"=>$_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"],
			"{usuario-nombre}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-nombre"],
			"{usuario-apellido}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-apellido"],
			"{usuario-nivel}"=>$_SESSION["data-login-" . APP_ABREV]["nivel-denominacion"],
			"{usuariodetalle-correoelectronico}"=>$_SESSION["data-login-" . APP_ABREV]["usuariodetalle-correoelectronico"]);
		$render = $this->render($dict_perfil, $gui);
		$template = $this->render_template($render);
		print $template;
	}

	function administrador() {
		$gui = file_get_contents("static/modules/usuario/administrador.html");
		$template = $this->render_template($gui);
		print $template;
	}
}
?>