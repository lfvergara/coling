<?php
require_once "modules/submenu/model.php";


class Item extends StandardObject {
	
	function __construct(SubMenu $submenu=NULL) {
		$this->item_id = 0;
        $this->denominacion = "";
		$this->detalle = "";
		$this->url = "";
		$this->submenu = $submenu;
	}
}
?>