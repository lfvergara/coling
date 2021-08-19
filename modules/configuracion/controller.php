<?php
require_once "modules/configuracion/model.php";
require_once "modules/configuracion/view.php";


class ConfiguracionController {

	function __construct() {
		$this->model = new Configuracion();
		$this->view = new ConfiguracionView();
	}

	function panel() {
    	SessionHandler()->check_session();
    	SessionHandler()->checkPerfil('3,9');
    	require_once "core/helpers/file.php";
    	$this->model->configuracion_id = 1;
    	$this->model->get();    	
    	$this->view->panel($this->model);
	}

	function ver_archivo() {
    	SessionHandler()->check_session();
    	SessionHandler()->checkPerfil('3,9');
    	require_once "core/helpers/file.php";
	}

	function definir_entidad() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$this->model->configuracion_id = filter_input(INPUT_POST, "configuracion_id");
		$this->model->get();
		$this->model->entidad = filter_input(INPUT_POST, "entidad");
		$this->model->save();
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function definir_logo() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$directorio = URL_PRIVATE . "configuracion/logo/";		
		$archivo = $_FILES["logo"]["tmp_name"];
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$mime = $finfo->file($archivo);
		$formato = explode("/", $mime);
		$mimes_permitidos = array("image/jpeg");		
		$name = "logo";
		if(in_array($mime, $mimes_permitidos)) move_uploaded_file($archivo, "{$directorio}/{$name}"); 
		header("Location: " . URL_APP . "/configuracion/panel");
	}

	function guardar() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
        $this->model->save();
		header("Location: " . URL_APP . "/configuracion/panel");
	}
}
?>