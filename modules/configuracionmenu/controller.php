<?php
require_once "modules/configuracionmenu/model.php";
require_once "modules/configuracionmenu/view.php";
require_once "modules/menu/model.php";
require_once "modules/submenu/model.php";
require_once "modules/item/model.php";


class ConfiguracionMenuController {

	function __construct() {
		$this->model = new ConfiguracionMenu();
		$this->view = new ConfiguracionMenuView();
	}

	function agregar() {
		SessionHandler()->check_session();
		$gerencia_collection = Collector()->get('Gerencia');
		$this->view->agregar($gerencia_collection);
	}

	function guardar() {
		SessionHandler()->check_session();
		$this->model->denominacion = filter_input(INPUT_POST, "denominacion");
		$this->model->nivel = filter_input(INPUT_POST, "nivel");
		$this->model->save();
		$arg = $this->model->configuracionmenu_id;
		header("Location: " . URL_APP . "/configuracionmenu/configuracion/{$arg}");
	}

	function configuracion($arg) {
		SessionHandler()->check_session();
		$this->model->configuracionmenu_id = $arg;
		$this->model->get();
		$menu_collection = Collector()->get('Menu');
		$this->view->configuracion($menu_collection, $this->model);
	}

	function guardar_item() {
		SessionHandler()->check_session();
		$configuracionmenu_id = filter_input(INPUT_POST, "configuracionmenu_id");
		$item_collection = filter_input(INPUT_POST, "item", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$submenu_collection = filter_input(INPUT_POST, "submenu", FILTER_DEFAULT, FILTER_REQUIRE_ARRAY);
		$this->model->configuracionmenu_id = $configuracionmenu_id;
		$this->model->get();
		
		if (!is_null($submenu_collection)) {
			foreach ($submenu_collection as $submenu_id) {
				$smm = new SubMenu();
				$smm->submenu_id = $submenu_id;
				$smm->get();
				$this->model->add_submenu($smm);
			}

			$sbcmm = new SubMenuConfiguracionMenu($this->model);
            $sbcmm->save();
		}

		if (!is_null($item_collection)) {
			foreach ($item_collection as $item_id) {
				$im = new Item();
				$im->item_id = $item_id;
				$im->get();
				$this->model->add_item($im);
			}	

			$icmm = new ItemConfiguracionMenu($this->model);
            $icmm->save();
		}

		header("Location: " . URL_APP . "/configuracionmenu/configuracion/{$configuracionmenu_id}");		
	}

	function quitar_submenu($arg) {
		$array_ids = explode("@", $arg);
		$configuracionmenu_id = $array_ids[0];
		$submenu_id = $array_ids[1];
		$this->model->configuracionmenu_id = $configuracionmenu_id;
		$this->model->get();

		$submenu_collection = $this->model->submenu_collection;
		foreach ($submenu_collection as $clave=>$valor) {
			if ($valor->submenu_id == $submenu_id) unset($submenu_collection[$clave]);
		}

		$this->model->submenu_collection = $submenu_collection;
		$smcmm = new SubMenuConfiguracionMenu($this->model);
		$smcmm->save();
		header("Location: " . URL_APP . "/configuracionmenu/configuracion/{$configuracionmenu_id}");		
	}


	function quitar_item($arg) {
		$array_ids = explode("@", $arg);
		$configuracionmenu_id = $array_ids[0];
		$item_id = $array_ids[1];
		$this->model->configuracionmenu_id = $configuracionmenu_id;
		$this->model->get();

		$item_collection = $this->model->item_collection;
		foreach ($item_collection as $clave=>$valor) if ($valor->item_id == $item_id) unset($item_collection[$clave]);
		$this->model->item_collection = $item_collection;
		$icmm = new ItemConfiguracionMenu($this->model);
		$icmm->save();
		header("Location: " . URL_APP . "/configuracionmenu/configuracion/{$configuracionmenu_id}");		
	}

	function ajax_traer_tercer_nivel($arg) {
		SessionHandler()->check_session();
		$where_submenu = "menu = {$arg}";
   		$submenu_collection = CollectorCondition()->get('SubMenu', $where_submenu, 2);
   		$submenu_ids = array();
   		foreach ($submenu_collection as $clave=>$valor)$submenu_ids[] = $valor->submenu_id;
   		$submenu_ids = implode(",", $submenu_ids);
		$where_item = "submenu IN ({$submenu_ids})";
   		$item_collection = CollectorCondition()->get('Item', $where_item, 2);
   		$this->view->mostrar_submenu_ajax($submenu_collection, $item_collection);
	}
}
?>