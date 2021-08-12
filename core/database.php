<?php
function execute_query($sql, $datos=array()) {
     $conexion = "mysql:host=". DB_HOST .";dbname=". DB_NAME .";charset=utf8";
     $opciones = array(PDO::ATTR_PERSISTENT=>true);
     $conn = new PDO($conexion, DB_USER, DB_PASS, $opciones);
     $query = $conn->prepare($sql);
     foreach($datos as $i=>$dato) $query->bindParam($i+1, $datos[$i]);
     $query->execute();
     $id_ingresado = $conn->lastInsertId();
     $registros_leidos = $query->fetchAll(PDO::FETCH_ASSOC);
     return ($registros_leidos) ? $registros_leidos : $id_ingresado;
}
?>