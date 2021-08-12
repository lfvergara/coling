<?php


class ObjectParser {

    public function __construct($obj=NULL) {
        if(!is_null($obj)) {
            $this->propiedades = $this->get_clean_properties($obj);
            $this->tabla = $this->get_table_name($obj);
            $this->campos = $this->get_fields();
            $this->string_campos = $this->get_string_fields();
            $this->propiedad_id = $this->get_id($obj);
            $this->valores_sustituciones = $this->get_values();
            $this->sustituciones_string = $this->get_substitution_string();
            $this->insert_string = $this->get_insert_string();
            $this->update_string = $this->get_update_string();
        }
    }

    public function get_table_name($obj) {
        return strtolower(get_class($obj));
    }

    public function get_clean_properties($obj) {
        $properties = get_object_vars($obj);
        foreach($properties as $property=>$value) {
            if(is_array($value)) unset($properties[$property]);
        }
        return $properties;
    }

    public function get_fields() {
        return array_keys($this->propiedades);
    }

    public function get_id($obj) {
        return strtolower(get_class($obj)) . '_id';
    }

    public function get_string_fields() {
        return implode(', ', array_keys($this->propiedades));
    }

    public function get_values() {
        $data = array_values($this->propiedades);
        foreach($data as &$valor) {
            if((gettype($valor) == 'object')) {
                $propiedad = $this->get_id($valor);
                $valor = $valor->$propiedad;
            }
        }
        array_shift($data);
        return $data;
    }

    public function get_insert_string() {
        $string = implode(', ', $this->campos);
        return str_replace("{$this->propiedad_id}, ", "", $string);
    }

    public function get_update_string() {
        $string = implode(' = ?, ', $this->campos) . ' = ? ';
        return str_replace("{$this->propiedad_id} = ?, ", "", $string);
    }

    public function get_substitution_string() {
        return implode(', ', array_fill(0, count($this->campos) - 1, '?'));
    }
}

function ObjectParser($obj=NULL) {return new ObjectParser($obj);}
?>
