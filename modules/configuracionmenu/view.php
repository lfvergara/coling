<?php


class ConfiguracionMenuView extends View {
	
	function agregar($gerencia_collection) {
		$gui = file_get_contents("static/modules/menu/agregar_configuracion.html");

		$render = $this->render_regex("SLT_GERENCIA", $gui, $gerencia_collection);
		$template = $this->render_template($render);
		print $template;
	}

	function configuracion($menu_collection, $obj_configuracionmenu) {
		$gui = file_get_contents("static/modules/menu/configuracion.html");
		$gui_prevista_configuracion = file_get_contents("static/modules/menu/prevista_configuracion.html");
		$opt_menu = file_get_contents("static/common/menu/opt_menu_ajax.html");
		
		$submenu_collection = $obj_configuracionmenu->submenu_collection;
		$item_collection = $obj_configuracionmenu->item_collection;
		unset($obj_configuracionmenu->item_collection, $obj_configuracionmenu->submenu_collection);
		switch ($obj_configuracionmenu->nivel) {
			case 1:
				$obj_configuracionmenu->nivel_denominacion = "Operador";
				break;
			case 2:
				$obj_configuracionmenu->nivel_denominacion = "Supervisor";
				break;
			case 3:
				$obj_configuracionmenu->nivel_denominacion = "Administrador";
				break;
			case 9:
				$obj_configuracionmenu->nivel_denominacion = "Desarrollador";
				break;
		}

		foreach ($submenu_collection as $clave=>$valor) {
			$submenu_id = $valor->submenu_id;
			$submenu_collection[$clave]->clavelin = "sm_{$submenu_id}";
			$array_temp = array();
			foreach ($item_collection as $c=>$v) {
				$submenu_temp = $v->submenu->submenu_id;
				$item_id = $v->item_id;
				$item_collection[$c]->clavelin = "i_{$item_id}";
				if ($submenu_id == $submenu_temp) $array_temp[] = $v;
			}

			$submenu_collection[$clave]->item_collection = $array_temp;
		}

		$array_temp_menu_id = array();
		foreach ($submenu_collection as $clave=>$valor) {
			if (!in_array($valor->menu->menu_id, $array_temp_menu_id)) $array_temp_menu_id[] = $valor->menu->menu_id;	
		}

		$menu_collection_temp = array();
		foreach ($array_temp_menu_id as $menu_id) {
			foreach ($menu_collection as $clave=>$valor) {
				if ($valor->menu_id == $menu_id) {
					$valor->submenu_collection = array();
					$menu_id = $valor->menu_id;
					$menu_collection[$clave]->clavelin = "m_{$menu_id}";
					$menu_collection_temp[] = $valor;
				}
			}
		}

		foreach ($menu_collection_temp as $clave=>$valor) {
			$menu_id_temp = $valor->menu_id;
			foreach ($submenu_collection as $submenu) {
				if ($menu_id_temp == $submenu->menu->menu_id) $valor->submenu_collection[] = $submenu;
			}
		}

		$render_menu = '';
        $cod_btn_menu = $this->get_regex('BTN_MENU', $gui_prevista_configuracion);
        foreach ($menu_collection_temp as $dict_menu) {
        	$temp_menu_id = $dict_menu->menu_id;
        	$submenu_collection = $dict_menu->submenu_collection;
        	unset($dict_menu->submenu_collection);
        	$dict_menu = $this->set_dict($dict_menu);
        	$btn_menu = $this->render($dict_menu, $cod_btn_menu);

	        $cod_btn_submenu = file_get_contents("static/modules/menu/prevista_configuracion_submenu.html");
			$render_submenu = '';
	        foreach($submenu_collection as $dict) {
	        	$item_collection = $dict->item_collection;
	        	unset($dict->item_collection);
	            
	        	$dict = $this->set_dict($dict);
	    		$btn_submenu = $this->render($dict, $cod_btn_submenu);
	        	$cod_btn_item = file_get_contents("static/modules/menu/prevista_configuracion_item.html");
	        	$render_item = '';
	    		$item_collection = $this->set_collection_dict($item_collection);
	        	foreach ($item_collection as $clave=>$valor) {
	        		$render_item .= $this->render($valor, $cod_btn_item);
	        	}
	    		
	    		$btn_submenu = str_replace('{btn_item}', $render_item, $btn_submenu);
	            $render_submenu .= $btn_submenu;
	        }

	        $btn_menu = str_replace('{btn_submenu}', $render_submenu, $btn_menu);
            $render_menu .= $btn_menu;
        }
		
        
		$gui_prevista_configuracion = str_replace($cod_btn_menu, $render_menu, $gui_prevista_configuracion);
		$opt_menu = $this->render_regex("OPT_MENU", $opt_menu, $menu_collection);
		$render = str_replace("{opt_menu}", $opt_menu, $gui);
		$render = str_replace("{vista_configuracionmenu}", $gui_prevista_configuracion, $render);
		$obj_configuracionmenu = $this->set_dict($obj_configuracionmenu);
		$render = $this->render($obj_configuracionmenu, $render);
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