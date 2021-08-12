<?php
$file = (isset($_GET['f'])) ? $_GET['f'] : '';
$accion = (isset($_GET['a'])) ? $_GET['a'] : '';
$ids = explode("_", $file);

$objeto = $ids[0];
$propiedad = (isset($ids[1])) ? $ids[1] : "";
$url = (isset($ids[2])) ? $ids[2] : "";
$archivo = URL_PRIVATE . "{$objeto}/{$propiedad}/{$url}";

if(file_exists($archivo)) {
	switch ($accion) {
		case 'v':
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$mime = finfo_file($finfo, $archivo);
			finfo_close($finfo);
			header("Content-Type: {$mime}");
			ob_end_clean();
		    $imagen = readfile($archivo);
			break;
		case 'd':
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
		    $mime = finfo_file($finfo, "{$archivo}.sql.gz");
		    finfo_close($finfo);
		    header("Content-Type: {$mime}");
		    ob_end_clean();
		    readfile($archivo);
			exit;
			break;
	}
} else {
	$sin_imagen = URL_PRIVATE . "common/sin_imagen";
	$finfo = finfo_open(FILEINFO_MIME_TYPE);
	$mime = finfo_file($finfo, $sin_imagen);
	finfo_close($finfo);
	header("Content-Type: {$mime}");
	ob_end_clean();
    $imagen = readfile($sin_imagen);
}
?>