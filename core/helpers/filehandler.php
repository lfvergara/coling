<?php

/**
 * Herramientas para manipular archivos.
 */
class FileHandler {

  static function save_file($archivo, $nombre_carpeta, $nombre_archivo) {
    $directorio = FILES_PATH . $nombre_carpeta;
    if(!file_exists($directorio)) mkdir($directorio);
    $ruta_archivo = FILES_PATH . $nombre_carpeta . "/" . $nombre_archivo;
    move_uploaded_file($archivo['tmp_name'], $ruta_archivo);
  }

  static function get_file($archivo) {
    $archivo = FILES_PATH.$archivo;
    if(file_exists($archivo)) {
      $finfo = finfo_open(FILEINFO_MIME_TYPE);
      $mime = finfo_file($finfo, $archivo);
      finfo_close($finfo);
      header("Content-Type: $mime");
      readfile($archivo);
    }
  }

  static function check_file($carpeta, $archivo) {
    $ruta_archivo = FILES_PATH . $carpeta . "/" . $archivo;
    if(file_exists($ruta_archivo)) {
      return true;
    } else {
      return false;
    }
  }
  
  static function delete_files($carpeta) {
    $ruta_directorio = FILES_PATH . $carpeta . "/";
    $files = scandir($ruta_directorio);
    foreach($files as $f) {
      if (is_file($ruta_directorio.$f)) unlink($ruta_directorio . $f);
    }
  }
}
?>