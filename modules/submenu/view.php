<?php


class SubMenuView extends View {
	
	function editar($obj_submenu, $menu_collection, $submenu_collection, $item_collection) {
		$gui = file_get_contents("static/modules/menu/editar_submenu.html");
		$tbl_menu = file_get_contents("static/common/menu/tbl_menu.html");
		$tbl_menu = $this->render_regex("TBL_MENU", $tbl_menu, $menu_collection);
		$tbl_submenu = file_get_contents("static/common/menu/tbl_submenu.html");
		$tbl_submenu = $this->render_regex("TBL_SUBMENU", $tbl_submenu, $submenu_collection);
		$tbl_item = file_get_contents("static/common/menu/tbl_item.html");
		$tbl_item = $this->render_regex("TBL_ITEM", $tbl_item, $item_collection);
		$slt_menu = file_get_contents("static/common/menu/slt_menu.html");
		$slt_menu = $this->render_regex("SLT_MENU", $slt_menu, $menu_collection);
		$slt_submenu = file_get_contents("static/common/menu/slt_submenu.html");
		$slt_submenu = $this->render_regex("SLT_SUBMENU", $slt_submenu, $submenu_collection);
		
		$obj_submenu = $this->set_dict($obj_submenu);
		$render = $this->render($obj_submenu, $gui);
		$render = str_replace("{tbl_menu}", $tbl_menu, $render);
		$render = str_replace("{slt_menu}", $slt_menu, $render);
		$render = str_replace("{tbl_submenu}", $tbl_submenu, $render);
		$render = str_replace("{slt_submenu}", $slt_submenu, $render);
		$render = str_replace("{tbl_item}", $tbl_item, $render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>