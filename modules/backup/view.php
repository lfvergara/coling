<?php


class BackupView extends View {
	
	function panel($backup_collection) {
		$gui = file_get_contents("static/modules/backup/panel.html");

		$token = substr(md5(rand()),0,8);
		$denominacion = DB_NAME . "-" . date("YmdHis");

		$render = $this->render_regex('TBL_BACKUP', $gui, $backup_collection);
		$render = str_replace('{backup-denominacion}', $denominacion, $render);
		$render = $this->render_breadcrumb($render);
		$template = $this->render_template($render);
		print $template;
	}
}
?>