<?php
require_once "modules/usuariodetalle/model.php";
require_once "modules/configuracionmenu/model.php";


class Usuario extends StandardObject {
	
	function __construct(UsuarioDetalle $detalle=NULL, ConfiguracionMenu $configuracionmenu=NULL) {
		$this->usuario_id = 0;
		$this->denominacion = '';
		$this->nivel = 0;
		$this->equipo = 0;
		$this->actualiza_contrasena = 0;
		$this->usuariodetalle = $detalle;
		$this->configuracionmenu = $configuracionmenu;
	}
}
?>