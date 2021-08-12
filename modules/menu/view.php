<?php


class MenuView extends View {
	function panel($configuracionmenu_collection) {
		$gui = file_get_contents("static/modules/menu/listar_menu.html");
		$gui_configuracionmenu = file_get_contents("static/common/menu/tbl_configuracionmenu.html");
		
		foreach ($configuracionmenu_collection as $clave=>$valor) unset($valor->submenu_collection, $valor->item_collection);	
		$gui_configuracionmenu = $this->render_regex("TBL_CONFIGURACIONMENU", $gui_configuracionmenu, $configuracionmenu_collection);
		
		$render = str_replace("{tbl_configuracionmenu}", $gui_configuracionmenu, $gui);
		$template = $this->render_template($render);
		print $template;
	}

	function configuracion($menu_collection, $submenu_collection, $item_collection) {
		$gui = file_get_contents("static/modules/menu/configuracion.html");

		$txt_menu = "";
		foreach ($menu_collection as $clave=>$valor) {
			$menu_id = $valor->menu_id;
			$denominacion = $valor->denominacion;
			$txt_menu .= "{$menu_id}@{$denominacion},";
		}

		$txt_menu = substr($txt_menu, 0, -1);
		$render = str_replace("{menu-menu_denominacion_id}", $txt_menu, $gui);
		$template = $this->render_template($render);
		print $template;
	}

	function configurar_segundo_nivel_menu($array_menus) {
		$gui = file_get_contents("static/modules/menu/configuracion_segundo_nivel.html");
		$opt_menu = file_get_contents("static/common/menu/opt_menu_ajax.html");

		$menu_collection = array();
		foreach ($array_menus as $clave=>$valor) {
			$array = explode("@", $valor);
			$array_temp = array("{menu-menu_id}"=>$array[0],
						  		"{menu-denominacion}"=>$array[1]);
			$menu_collection[] = $array_temp;
		}
		
		$opt_menu = $this->render_regex_dict("OPT_MENU", $opt_menu, $menu_collection);
		$render = str_replace("{opt_menu}", $opt_menu, $gui);
		$template = $this->render_template($render);
		print $template;
	}

	function agregar($menu_collection, $submenu_collection, $item_collection) {
		$gui = file_get_contents("static/modules/menu/agregar.html");
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

		$render = str_replace("{tbl_menu}", $tbl_menu, $gui);
		$render = str_replace("{slt_menu}", $slt_menu, $render);
		$render = str_replace("{tbl_submenu}", $tbl_submenu, $render);
		$render = str_replace("{slt_submenu}", $slt_submenu, $render);
		$render = str_replace("{tbl_item}", $tbl_item, $render);
		$template = $this->render_template($render);
		print $template;
	}

	function editar($obj_menu, $menu_collection, $submenu_collection, $item_collection) {
		$gui = file_get_contents("static/modules/menu/editar.html");
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

		$obj_menu = $this->set_dict($obj_menu);
		$render = $this->render($obj_menu, $gui);
		$render = str_replace("{tbl_menu}", $tbl_menu, $render);
		$render = str_replace("{slt_menu}", $slt_menu, $render);
		$render = str_replace("{tbl_submenu}", $tbl_submenu, $render);
		$render = str_replace("{slt_submenu}", $slt_submenu, $render);
		$render = str_replace("{tbl_item}", $tbl_item, $render);
		$template = $this->render_template($render);
		print $template;
	}

	function mostrar_submenu_ajax($submenu_collection, $item_collection) {
		$gui = file_get_contents("static/common/menu/chk_menu_segundo_nivel.html");
		foreach ($submenu_collection as $clave=>$valor) {
			$submenu_id = $valor->submenu_id;
			$array_temp = array();
			foreach ($item_collection as $c=>$v) {
				$submenu_temp = $v->submenu->submenu_id;
				if ($submenu_id == $submenu_temp) $array_temp[] = $v;
			}

			$submenu_collection[$clave]->item_collection = $array_temp;
		}

		$render_final = '';
		$render_submenu = '';
        $cod_chk_submenu = $this->get_regex('CHK_SUBMENU', $gui);
        foreach($submenu_collection as $dict) {
        	$item_collection = $dict->item_collection;
        	unset($dict->item_collection);
            
        	$dict = $this->set_dict($dict);
    		$chk_submenu = $this->render($dict, $cod_chk_submenu);
        	$cod_chk_item = $this->get_regex('CHK_ITEM', $chk_submenu);
        	$render_item = '';
    		$item_collection = $this->set_collection_dict($item_collection);
        	foreach ($item_collection as $clave=>$valor) {
        		$render_item .= $this->render($valor, $cod_chk_item);
        	}

    		
    		$chk_submenu = str_replace($cod_chk_item, $render_item, $chk_submenu);
            $render_submenu .= $chk_submenu;
        }
            
		print $render_submenu;		
	}
}
?>