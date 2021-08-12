<?php
require_once "modules/provincia/model.php";
require_once "modules/documentotipo/model.php";
require_once "modules/infocontacto/model.php";
require_once "modules/matricula/model.php";


class Matriculado extends StandardObject {
	
	function __construct(Provincia $provincia=NULL, DocumentoTipo $documentotipo=NULL) {
		$this->matriculado_id = 0;
        $this->apellido = '';
        $this->nombre = '';
		$this->documento = 0;
        $this->domicilio = '';
        $this->codigopostal = '';
        $this->barrio = '';
        $this->observacion = '';
        $this->provincia = $provincia;
        $this->documentotipo = $documentotipo;
        $this->infocontacto_collection = array();
        $this->matricula_collection = array();
	}

	function add_infocontacto(InfoContacto $infocontacto) {
        $this->infocontacto_collection[] = $infocontacto;
    }

    function add_matricula(Matricula $matricula) {
        $this->matricula_collection[] = $matricula;
    }
}

class InfoContactoMatriculado {
    
    function __construct(Matriculado $matriculado=null) {
        $this->infocontactomatriculado_id = 0;
        $this->compuesto = $matriculado;
        $this->compositor = $matriculado->infocontacto_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM infocontactomatriculado WHERE compuesto=?";
        $datos = array($this->compuesto->matriculado_id);
        $resultados = execute_query($sql, $datos);
        if($resultados){
            foreach($resultados as $array) {
                $obj = new InfoContacto();
                $obj->infocontacto_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_infocontacto($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO infocontactomatriculado (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $infocontacto) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->matriculado_id;
            $datos[] = $infocontacto->infocontacto_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM infocontactomatriculado WHERE compuesto=?";
        $datos = array($this->compuesto->matriculado_id);
        execute_query($sql, $datos);
    }
}

class MatriculaMatriculado {
    
    function __construct(Matriculado $matriculado=null) {
        $this->matriculamatriculado_id = 0;
        $this->compuesto = $matriculado;
        $this->compositor = $matriculado->matricula_collection;
    }

    function get() {
        $sql = "SELECT compositor FROM matriculamatriculado WHERE compuesto=?";
        $datos = array($this->compuesto->matriculado_id);
        $resultados = execute_query($sql, $datos);
        if($resultados){
            foreach($resultados as $array) {
                $obj = new Matricula();
                $obj->matricula_id = $array['compositor'];
                $obj->get();
                $this->compuesto->add_matricula($obj);
            }
        }
    }

    function save() {
        $this->destroy();
        $tuplas = array();
        $datos = array();
        $sql = "INSERT INTO matriculamatriculado (compuesto, compositor)
                VALUES ";
        foreach($this->compositor as $matricula) {
            $tuplas[] = "(?, ?)";
            $datos[] = $this->compuesto->matriculado_id;
            $datos[] = $matricula->matricula_id;
        }
        $sql .= implode(', ', $tuplas);
        execute_query($sql, $datos);
    }

    function destroy() {
        $sql = "DELETE FROM matriculamatriculado WHERE compuesto=?";
        $datos = array($this->compuesto->matriculado_id);
        execute_query($sql, $datos);
    }
}
?>