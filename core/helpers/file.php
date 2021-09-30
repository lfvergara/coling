<?php
$file = filter_input(INPUT_GET, "f");
 
if(!is_null($file)) {
	$archivo = URL_PRIVATE . "{$file}";
	if(file_exists($archivo)) {
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$mime = finfo_file($finfo, $archivo);
		finfo_close($finfo);
		header("Content-Type: $mime");
		readfile($archivo);
	}
}
?>