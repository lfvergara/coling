<?php
class User {
	static function get_usuariodetalle_id($hash) {
	    $sql = "SELECT 
	    			usuariodetalle_id
	    		FROM 
	    			usuariodetalle 
	    		WHERE 
	    			token = ?";
	    $datos = array($hash);
        $result = execute_query($sql, $datos);
        $usuariodetalle_id = (isset($result[0]) AND is_array($result) AND !empty($result)) ? $result[0]['usuariodetalle_id'] : 0;
		return $usuariodetalle_id;
	}

	static function get_usuario_id($usuariodetalle_id) {
	    $sql = "SELECT 
	    			usuario_id
	    		FROM 
	    			usuario 
	    		WHERE 
	    			usuariodetalle = ?";
	    $datos = array($usuariodetalle_id);
        $result = execute_query($sql, $datos);
        $usuario_id = (isset($result[0]) AND is_array($result) AND !empty($result)) ? $result[0]['usuario_id'] : 0;
		return $usuario_id;
	}

	static function get_equipo_id($usuario_id) {
	    $sql = "SELECT 
	    			equipo_id
	    		FROM 
	    			equipo 
	    		WHERE 
	    			usuario_id = ?";
	    $datos = array($usuario_id);
        $result = execute_query($sql, $datos);
        $equipo_id = (is_array($result) AND !empty($result)) ? $result[0]['equipo_id'] : 0;
		return $equipo_id;
	}
}

function User() {return new User();}
?>