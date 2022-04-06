<?php
require_once "modules/notacredito/model.php";
require_once "modules/notacredito/view.php";
require_once "modules/comprobantepago/model.php";
require_once "modules/tipofactura/model.php";
require_once "tools/facturaAFIPTool.php";


class NotaCreditoController {

	function __construct() {
		$this->model = new NotaCredito();
		$this->view = new NotaCreditoView();
	}

	function listar() {
    	SessionHandler()->check_session();
    	$periodo_actual = date('Ym');
		$select = "nc.fecha AS FECHA, CONCAT(tifa.nomenclatura, ' ', LPAD(nc.punto_venta, 4, 0), '-', LPAD(nc.numero_factura, 8, 0)) AS NOTCRE, CASE WHEN nc.emitido_afip = 0 THEN CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE e.tipofactura = tf.tipofactura_id), ' ', LPAD(e.punto_venta, 4, 0), '-', LPAD(e.numero_factura, 8, 0)) ELSE CONCAT((SELECT tf.nomenclatura FROM tipofactura tf WHERE eafip.tipofactura = tf.tipofactura_id), ' ', LPAD(eafip.punto_venta, 4, 0), '-', LPAD(eafip.numero_factura, 8, 0)) END AS REFERENCIA, cl.razon_social AS CLIENTE, CONCAT(v.apellido, ' ', v.nombre) AS VENDEDOR, nc.importe_total AS IMPORTETOTAL, nc.notacredito_id AS NOTACREDITO_ID";
		$from = "notacredito nc INNER JOIN egreso e ON nc.egreso_id = e.egreso_id INNER JOIN tipofactura tifa ON nc.tipofactura = tifa.tipofactura_id INNER JOIN cliente cl ON e.cliente = cl.cliente_id INNER JOIN vendedor v ON e.vendedor = v.vendedor_id LEFT JOIN egresoafip eafip ON e.egreso_id = eafip.egreso_id";
		$where = "date_format(nc.fecha, '%Y%m') = {$periodo_actual} ORDER BY e.fecha DESC";
		$notacredito_collection = CollectorCondition()->get('NotaCredito', $where, 4, $from, $select);
		$this->view->listar($notacredito_collection);
	}

	function notacredito_afip() {
		SessionHandler()->check_session();
		
		$cm = new Configuracion();
		$cm->configuracion_id = 1;
		$cm->get();

		$notacredito_id = filter_input(INPUT_POST, 'notacredito_id');
		$this->model->notacredito_id = $notacredito_id;
		$this->model->get();

		$egreso_id = filter_input(INPUT_POST, 'egreso_id');
		$em = new Egreso();
		$em->egreso_id = $egreso_id;
		$em->get();
		$tipofactura_id = $em->tipofactura->tipofactura_id;

		switch ($tipofactura_id) {
			case 1:
				$tiponc_id = 4;
				break;
			case 3:
				$tiponc_id = 5;
				break;
		}

		$tfm = new TipoFactura();
		$tfm->tipofactura_id = $tiponc_id;
		$tfm->get();

		$resultadoAFIP = FacturaAFIPTool()->notaCreditoAFIP($cm, $tfm, $this->model, $em, $notacreditodetalle_collection);
		if (is_array($resultadoAFIP)) {
			$this->model = new NotaCredito();
			$this->model->notacredito_id = $notacredito_id;
			$this->model->get();
			$this->model->punto_venta = $cm->punto_venta;
			$this->model->numero_factura = $resultadoAFIP['NUMFACTURA'];
			$this->model->emitido_afip = 1;
			$this->model->save();
		}

		header("Location: " . URL_APP . "/egreso/consultar/{$egreso_id}");
	}	
}
?>