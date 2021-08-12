<?php
require_once "modules/submenu/model.php";
require_once "modules/submenu/view.php";
require_once "modules/menu/model.php";
require_once "modules/item/model.php";


class SubMenuController {

	function __construct() {
		$this->model = new SubMenu();
		$this->view = new SubMenuView();
	}


	function editar($arg) {
		SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();

		$this->model->submenu_id = $arg;
		$this->model->get();
		$menu_collection = Collector()->get('Menu');
		$submenu_collection = Collector()->get('SubMenu');
		$item_collection = Collector()->get('Item');
		$this->view->editar($this->model, $menu_collection, $submenu_collection, $item_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();
		
		foreach ($_POST as $clave=>$valor) $this->model->$clave = $valor;
		$this->model->save();
		header("Location: " . URL_APP . "/menu/agregar");
	}

	function eliminar($arg) {
		SessionHandler()->check_session();
		//SessionHandler()->check_admin_level();

		$this->model->submenu_id = $arg;
		$this->model->delete();
		header("Location: " . URL_APP . "/menu/agregar");	
	}
}
?>