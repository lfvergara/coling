<?php
require_once "modules/submenu/model.php";
require_once "modules/item/model.php";


class ConfiguracionMenu extends StandardObject {
    
    function __construct() {
        $this->configuracionmenu_id = 0;
        $this->denominacion = "";
        $this->nivel = 0;
        $this->submenu_collection = array();
        $this->item_collection = array();
    }

    function add_submenu(SubMenu $submenu) {
        $this->submenu_collection[] = $submenu;
    }

    function add_item(Item $item) {
        $this->item_collection[] = $item;
    }
}

class SubMenuConfiguracionMenu {
    
    function __construct(ConfiguracionMenu $configuracionmenu=null) {
        $this->submenuconfiguracionmenu_id = 0;
        $this->compuesto = $configuracionmenu;
        $this->compositor = $configuracionmenu->submenu_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM submenuconfiguracionmenu WHERE compuesto=?";
        $datos = array($this->compuesto->configuracionmenu_id);
        $resultados = execute_query($sql, $datos);
        if($resultados){
            foreach($resultados as $array) {
                $obj = new SubMenu();
                $obj->submenu_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_submenu($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO submenuconfiguracionmenu (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $submenu) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->configuracionmenu_id;
            $datos[] = $submenu->submenu_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM submenuconfiguracionmenu WHERE compuesto=?";
        $datos = array($this->compuesto->configuracionmenu_id);
        execute_query($sql, $datos);
    }
}

class ItemConfiguracionMenu {
    
    function __construct(ConfiguracionMenu $configuracionmenu=null) {
        $this->itemconfiguracionmenu_id = 0;
        $this->compuesto = $configuracionmenu;
        $this->compositor = $configuracionmenu->item_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM itemconfiguracionmenu WHERE compuesto=?";
        $datos = array($this->compuesto->configuracionmenu_id);
        $resultados = execute_query($sql, $datos);
        if($resultados){
            if (!is_array($resultados)) {
                $resultados = array();
            }
            
            foreach($resultados as $array) {
                $obj = new Item();
                $obj->item_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_item($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO itemconfiguracionmenu (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $item) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->configuracionmenu_id;
            $datos[] = $item->item_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM itemconfiguracionmenu WHERE compuesto=?";
        $datos = array($this->compuesto->configuracionmenu_id);
        execute_query($sql, $datos);
    }
}
?>