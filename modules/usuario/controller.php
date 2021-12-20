<?php
require_once "modules/usuario/model.php";
require_once "modules/usuario/view.php";
require_once "modules/usuariodetalle/controller.php";
require_once "modules/usuariodetalle/model.php";
require_once "modules/configuracionmenu/model.php";


class UsuarioController {

	function __construct() {
		$this->model = new Usuario();
		$this->view = new UsuarioView();
	}

	function login($arg=0) {
		$this->view->login($arg);
	}

	function checkin() {
        SessionHandler()->checkin();
    }

	function checkout() {
        SessionHandler()->checkout();
    }

    function agregar() {
    	SessionHandler()->check_session();
    	SessionHandler()->checkPerfil('3,9');
		$usuario = $_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"];
		$nivel = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		$select_usuario = "u.usuario_id AS USUARIO_ID, u.denominacion AS DENOMINACION, CONCAT(ud.apellido, ' ', ud.nombre) AS USUARIO, 
						   CASE u.nivel WHEN 1 THEN 'Equipo' WHEN 3 THEN 'Admin' WHEN 9 THEN 'Desa' END AS NIVEL";
		$from_usuario = "usuario u INNER JOIN usuariodetalle ud ON u.usuariodetalle = ud.usuariodetalle_id";

		$select_menu = "cm.denominacion AS DENOMINACION, cm.configuracionmenu_id AS CONFIGURACIONMENU_ID";
		$from_menu = "configuracionmenu cm";

		if ($usuario == "desarrollador" AND $nivel == 9) {
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', NULL, 4, $from_menu, $select_menu);
			$usuario_collection = CollectorCondition()->get('Usuario', NULL, 4, $from_usuario, $select_usuario);
		} else {
			$where_menu = "cm.denominacion != 'DESARROLLADOR'";
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', $where_menu, 4, $from_menu, $select_menu);
			$where_usuario = "u.denominacion != 'desarrollador'";
			$usuario_collection = CollectorCondition()->get('Usuario', $where_usuario, 4, $from_usuario, $select_usuario);
		}

		$this->view->agregar($usuario_collection, $configuracionmenu_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$configuracionmenu_id = filter_input(INPUT_POST, "configuracionmenu");
		$cmm = new ConfiguracionMenu();
		$cmm->configuracionmenu_id = $configuracionmenu_id;
		$cmm->get();
		
		$detalle = new UsuarioDetalleController();
        $detalle->guardar();
        $this->model->denominacion = filter_input(INPUT_POST, "denominacion");
        $this->model->nivel = $cmm->nivel;
        $this->model->equipo = 0;
        $this->model->actualiza_contrasena = 1;
        $this->model->configuracionmenu = $configuracionmenu_id;
        $this->model->usuariodetalle = $detalle->model->usuariodetalle_id;        
        $this->model->save();
		header("Location: " . URL_APP . "/usuario/agregar");
	}

	function editar($arg) {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$this->model->usuario_id = $arg;
		$this->model->get();
		
		$usuario = $_SESSION["data-login-" . APP_ABREV]["usuario-denominacion"];
		$nivel = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		$select_usuario = "u.usuario_id AS USUARIO_ID, u.denominacion AS DENOMINACION, CONCAT(ud.apellido, ', ', ud.nombre) AS USUARIO,
						   CASE u.nivel WHEN 1 THEN 'Equipo' WHEN 3 THEN 'Admin' WHEN 9 THEN 'Desa' END AS NIVEL";
		$from_usuario = "usuario u INNER JOIN usuariodetalle ud ON u.usuariodetalle = ud.usuariodetalle_id";
		$select_menu = "cm.denominacion AS DENOMINACION, cm.configuracionmenu_id AS CONFIGURACIONMENU_ID";
		$from_menu = "configuracionmenu cm";

		if ($usuario == "desarrollador" AND $nivel == 9) {
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', NULL, 4, $from_menu, $select_menu);
			$usuario_collection = CollectorCondition()->get('Usuario', NULL, 4, $from_usuario, $select_usuario);
		} else {
			$where_menu = "cm.denominacion != 'DESARROLLADOR'";
			$configuracionmenu_collection = CollectorCondition()->get('ConfiguracionMenu', $where_menu, 4, $from_menu, $select_menu);
			$where_usuario = "u.denominacion != 'desarrollador'";
			$usuario_collection = CollectorCondition()->get('Usuario', $where_usuario, 4, $from_usuario, $select_usuario);
		}

		$this->view->editar($usuario_collection, $configuracionmenu_collection, $this->model);
	}

	function actualizar() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$configuracionmenu_id = filter_input(INPUT_POST, "configuracionmenu");
		$cmm = new ConfiguracionMenu();
		$cmm->configuracionmenu_id = $configuracionmenu_id;
		$cmm->get();

		$detalle = new UsuarioDetalleController();
        $detalle->actualizar();
        $this->model->usuario_id = filter_input(INPUT_POST, "usuario_id");
        $this->model->get();
        $this->model->nivel = $cmm->nivel;
        $this->model->configuracionmenu = $configuracionmenu_id;
		$this->model->save();
		header("Location: " . URL_APP . "/usuario/agregar");
	}

	function actualizar_token() {
		SessionHandler()->check_session();
		$usuario_id = $_POST["usuario_id"];
		$this->model->usuario_id = $_POST["usuario_id"];
		$this->model->get();
		$usuariodetalle_id = $this->model->usuariodetalle->usuariodetalle_id;
		$udc = new UsuarioDetalleController();
        $udc->actualizar_token($usuariodetalle_id);

		$this->model = new Usuario();
		$this->model->usuario_id = $usuario_id;
		$this->model->get();
		$this->model->actualiza_contrasena = 0;
		$this->model->save();

        $_SESSION["data-login-" . APP_ABREV]["usuario-actualiza_contrasena"] = 0;

		header("Location: " . URL_APP . "/usuario/perfil");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$this->model->usuario_id = $arg;
		$this->model->get();
		$usuariodetalle_id = $this->model->usuariodetalle->usuariodetalle_id;
		$udc = new UsuarioDetalleController();
		$udc->eliminar($usuariodetalle_id);
		$this->model->delete();
		header("Location: " . URL_APP . "/usuario/agregar");
	}
	
	function regenerar_token($arg) {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$usuario_id = $arg;
		$this->model->usuario_id = $usuario_id;
		$this->model->get();
		$detalle = new UsuarioDetalleController();
        $detalle->regenerar_token($this->model->usuariodetalle->usuariodetalle_id, $this->model->denominacion);

        $this->model = new Usuario();
        $this->model->usuario_id = $usuario_id;
		$this->model->get();
		$this->model->actualiza_contrasena = 1;
		$this->model->save();

		header("location:" . URL_APP . "/usuario/agregar");
	}

	function panel() {
		SessionHandler()->check_session();
		$nivel = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"];
		if ($nivel > 1) {
            header("location:" . URL_APP . "/movimientofinanciero/diario");
        } else {
            header("location:" . URL_APP . "/reporte/operador");
        }
	}

	function perfil() {
		SessionHandler()->check_session();
		$this->view->perfil();
	}

	function admin() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		header("location:" . URL_APP . "/reporte/admin");
	}

	function operador() {
		SessionHandler()->check_session();
		header("location:" . URL_APP . "/reporte/operador");
	}

	function informar_clave() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$usuario_collection = Collector()->get("Usuario");
		$usuario_temp = array();
		foreach ($usuario_collection as $clave=>$valor) {
			$array_temp = array();
			$array_temp = array("{usuario-nombre}"=>$valor->usuariodetalle->nombre,
								"{usuario-usuario}"=>$valor->denominacion,
								"{usuario-contraseña}"=>$valor->denominacion . "$1",
								"{usuario_correo}"=>$valor->usuariodetalle->correoelectronico);
			$usuario_temp[] = $array_temp;
			
		}

		$emailHelper = new EmailUsuario();
		$emailHelper->envia_email_usuario($usuario_temp);
		
	}

	function blanqueo_masivo() {
		SessionHandler()->check_session();
		SessionHandler()->checkPerfil('3,9');
		$select = 'u.usuario_id AS ID';
		$from = 'usuario u';
		$usuario_collection = CollectorCondition()->get("Usuario", NULL, 4, $from, $select);
		foreach ($usuario_collection as $clave=>$valor) {
			$usuario_id = $valor['ID'];
			$um = new Usuario();
			$um->usuario_id = $usuario_id;
			$um->get();
			$usuario = $um->denominacion;
			$usuariodetalle_id = $um->usuariodetalle->usuariodetalle_id;

			$denominacion = hash(ALGORITMO_USER, $usuario);
			$password = $usuario . "$1";
			$password = hash(ALGORITMO_PASS, $password);
			$token = hash(ALGORITMO_FINAL, $denominacion . $password);

			$udm = new UsuarioDetalle();
			$udm->usuariodetalle_id = $usuariodetalle_id;
			$udm->get();
			$udm->token = $token;
			$udm->save();
		}
		
		print_r("Éxito!");
		exit;
	}

}
?>