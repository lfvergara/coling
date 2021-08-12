<?php
require_once "modules/backup/model.php";
require_once "modules/backup/view.php";


class BackupController {

	function __construct() {
		$this->model = new Backup();
		$this->view = new BackupView();
	}

	function panel() {
    	SessionHandler()->check_session();
		$backup_collection = Collector()->get('Backup');
		$this->view->panel($backup_collection);
	}

	function descargar_backup() {
		require_once "core/helpers/file.php";
	}

	function guardar() {
		SessionHandler()->check_session();
		$dbhost = DB_HOST;
		$dbname = DB_NAME;
		$dbuser = DB_USER;
		$dbpass = DB_PASS;

		$denominacion = filter_input(INPUT_POST, "denominacion");
		$this->model->denominacion = $denominacion;
		$this->model->usuario = $_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"];
		$this->model->fecha = date("Y-m-d");
		$this->model->hora = date("H:i:s");
		$this->model->detalle = filter_input(INPUT_POST, "detalle");
		$this->model->save();
				 
		$target = URL_PRIVATE . "backup/bd/";
		shell_exec("mysqldump --skip-comments --routines -h '{$dbhost}' -u '{$dbuser}' -p'{$dbpass}' '{$dbname}' | gzip -c > {$target}/{$denominacion}.sql.gz"); 
		header("Location: " . URL_APP . "/backup/panel");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		$this->model->backup_id = $arg;
		$this->model->get();
		$denominacion = $this->model->denominacion;
		$target = URL_PRIVATE . "backup/bd/{$denominacion}.sql.gz";
		unlink($target);
		$this->model->delete();
		header("Location: " . URL_APP . "/backup/panel");
	}
}
?>