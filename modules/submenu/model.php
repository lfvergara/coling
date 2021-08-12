<?php
require_once "modules/menu/model.php";


class SubMenu extends StandardObject {
	
	function __construct(Menu $menu=NULL) {
		$this->submenu_id = 0;
		$this->denominacion = "";
        $this->icon = "";
		$this->url = "";
		$this->detalle = "";
        $this->menu = $menu;
	}
}
?>