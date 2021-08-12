<?php
$file = (isset($_GET['f'])) ? $_GET['f'] : '';
$ids = explode("_", $file);

$objeto = $ids[0];
$propiedad = $ids[1];
$url = (isset($ids[2])) ? $ids[2] : "";
$archivo = URL_PRIVATE . "{$objeto}/{$propiedad}/{$url}";

if(file_exists($archivo)) {
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $archivo);
	finfo_close($finfo);
	header("Content-Type: {$mime}");
	$imagen = readfile($archivo);
} elseif ($objeto_id == 0) {
	$archivo = URL_PRIVATE . "common/sin_imagen.jpg";
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $archivo);
	finfo_close($finfo);
	header("Content-Type: $mime");
	$imagen = readfile($archivo);
} else {
	$archivo = URL_PRIVATE . "common/sin_imagen.jpg";
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $archivo);
	finfo_close($finfo);
	header("Content-Type: $mime");
	$imagen = readfile($archivo);
}

?>