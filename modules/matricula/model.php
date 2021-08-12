<?php
require_once "modules/titulo/model.php";
require_once "modules/universidad/model.php";
require_once "modules/estado/model.php";


class Matricula extends StandardObject {
	
	function __construct(Titulo $titulo=NULL, Universidad $universidad=NULL) {
		$this->matricula_id = 0;
                $this->matricula = '';
                $this->fecha_egreso = '';
                $this->fecha_inscripcion = '';
        	$this->estado = 0;
                $this->titulo = $titulo;
                $this->universidad = $universidad;
                $this->estado_collection = array();
	}

        function add_estado(Estado $estado) {
                $this->estado_collection[] = $estado;
        }
}

class EstadoMatricula {
    
        function __construct(Matricula $matricula=null) {
                $this->estadomatricula_id = 0;
                $this->compuesto = $matricula;
                $this->compositor = $matricula->estado_collection;
        }

        function get() {
                $sql = "SELECT compositor FROM estadomatricula WHERE compuesto=?";
                $datos = array($this->compuesto->matricula_id);
                $resultados = execute_query($sql, $datos);
                if($resultados){
                    foreach($resultados as $array) {
                        $obj = new Estado();
                        $obj->estado_id = $array['compositor'];
                        $obj->get();
                        $this->compuesto->add_estado($obj);
                    }
                }
        }

        function save() {
                $this->destroy();
                $tuplas = array();
                $datos = array();
                $sql = "INSERT INTO estadomatricula (compuesto, compositor)
                        VALUES ";
                foreach($this->compositor as $estado) {
                    $tuplas[] = "(?, ?)";
                    $datos[] = $this->compuesto->matricula_id;
                    $datos[] = $estado->estado_id;
                }
                $sql .= implode(', ', $tuplas);
                execute_query($sql, $datos);
        }

        function destroy() {
                $sql = "DELETE FROM estadomatricula WHERE compuesto=?";
                $datos = array($this->compuesto->matricula_id);
                execute_query($sql, $datos);
        }
}
?>