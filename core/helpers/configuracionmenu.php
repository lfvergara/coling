<?php
require_once "modules/configuracionmenu/model.php";
require_once "modules/menu/model.php";


class HelperMenu {
	static function traer_configuracionmenu($configuracionmenu_id) {
	    $cmm = new ConfiguracionMenu();
	    $cmm->configuracionmenu_id = $configuracionmenu_id;
	    $cmm->get();
	 	return $cmm;
	}

	static function traer_menu_collection() {
	    $menu_collection = Collector()->get('Menu');
	 	return $menu_collection;
	}
}

function HelperMenu() {return new HelperMenu();}
?>