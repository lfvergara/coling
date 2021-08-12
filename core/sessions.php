    <?php
require_once 'core/helpers/user.php';
require_once 'modules/usuario/model.php';
require_once 'modules/configuracionmenu/model.php';


class SessionBaseHandler {
    function checkin() {  
        $user = hash(ALGORITMO_USER, $_POST['usuario']);
        $clave = hash(ALGORITMO_PASS, $_POST['contrasena']);
        $hash = hash(ALGORITMO_FINAL, $user . $clave);
        $usuariodetalle_id = User::get_usuariodetalle_id($hash);
        
        if ($usuariodetalle_id != 0) {
            $usuario_id = User::get_usuario_id($usuariodetalle_id);
            if ($usuario_id != 0) {
                $um = new Usuario();
                $um->usuario_id = $usuario_id;
                $um->get();
                
                $nav_menu_home = ($um->nivel > 1) ? 'admin' : 'operador';
                $nivel_denominacion = ($um->nivel == 1) ? "Operador" : "";
                $nivel_denominacion = ($um->nivel == 3) ? "Admin" : $nivel_denominacion;
                $nivel_denominacion = ($um->nivel == 9) ? "Desa" : $nivel_denominacion;
                $data_login = array(
                    "usuario-usuario_id"=>$um->usuario_id,
                    "usuario-denominacion"=>$um->denominacion,
                    "usuario-nivel"=>$um->nivel,
                    "usuario-actualiza_contrasena"=>$um->actualiza_contrasena,
                    "nivel-denominacion"=>$nivel_denominacion,
                    "usuariodetalle-nombre"=>$um->usuariodetalle->nombre,
                    "usuariodetalle-apellido"=>$um->usuariodetalle->apellido,
                    "usuariodetalle-correoelectronico"=>$um->usuariodetalle->correoelectronico,
                    "nav_home-url"=>"/usuario/{$nav_menu_home}",
                    "usuario-configuracionmenu"=>$um->configuracionmenu->configuracionmenu_id);
                
                $_SESSION["data-login-" . APP_ABREV] = $data_login;
                $_SESSION['login' . APP_ABREV] = true;
                if ($um->actualiza_contrasena == 1) {
                    $redirect = URL_APP . "/usuario/perfil";
                } else {
                    if ($um->nivel > 1) {
                        $redirect = URL_APP . "/usuario/admin";
                    } else {
                        $redirect = URL_APP . "/usuario/operador";
                    }
                }
                
            }
        } else {
            $_SESSION['login' . APP_ABREV] = false;
            $redirect = URL_APP . LOGIN_URI . "/mError";
        }

        header("Location: $redirect");
    }

    function check_session() {
        if($_SESSION['login' . APP_ABREV] !== true) {
            $this->checkout();
        }
    }

    function check_actualiza_contrasena() {
        $actualiza_contrasena = $_SESSION["data-login-" . APP_ABREV]["usuario-actualiza_contrasena"];
        if ($actualiza_contrasena == 1) {
            $redirect = URL_APP . "/usuario/perfil";
            header("Location: $redirect");
        }
    }

    function check_panel($usr_nivel) {
        switch ($usr_nivel) {
            case 1:
                $panel = "operador";
                break;
            case 2:
                $panel = "analista";
                break;
            case 3:
                $panel = "administrador";
                break;
            case 9:
                $panel = "administrador";
                break;
        }

        return $panel;
    }

    function check_admin_level() {
        $level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"]; 
        if ($level != 9) {
            $this->checkout();
        }
    }

    function check_level() {
        $level = $_SESSION["data-login-" . APP_ABREV]["usuario-nivel"]; 
        if ($level > 1 ) {
            $_SESSION['login' . APP_ABREV] = true;
        } else {
            $this->checkout();
        }
    }

    function checkPerfil($usr_niveles) {
        $session_usr_nivel = $_SESSION["data-login-" . APP_ABREV]['usuario-nivel'];
        $niveles = explode(',', $usr_niveles);
        if ($session_usr_nivel == 1) {
             $this->checkout();
        } else {
          if(in_array(0, $niveles)) return false;
          
          if(3 == $session_usr_nivel || 9 == $session_usr_nivel) {
            return false;
          } else {
            if(in_array($session_usr_nivel, $niveles)) {
              return false;
            } else {
               $this->checkout();
            }
          }
        }
    }

    function checkout() {
        $_SESSION[] = array();
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"], 
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
        $_SESSION['login' . APP_ABREV] = false;
        header("Location:" . URL_APP . LOGIN_URI);
    }
}

function SessionHandler() { return new SessionBaseHandler();}