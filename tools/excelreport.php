//ob_start();
<?php
require_once "common/libs/PHPExcel/PHPExcel.php";


class ExcelReport extends View {
  public $estilo_titulo = "";
  public $estilo_titulo_reporte = "";
  public $estilo_titulo_columnas = "";
  public $estilo_informacion = "";
  public $abecedario = array("A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
                             "AA","AB","AC","AD","AE","AF","AG","AH","AI","AJ","AK","AL","AM","AN","AO","AP","AQ","AR","AS","AT","AU",
                             "AV","AW","AX","AY","AZ");

  function extraer_informe($array, $subtitulo) {
    //print_r($subtitulo);exit;

    date_default_timezone_set('America/Mexico_City');
    if (PHP_SAPI == 'cli') die('Este archivo solo se puede ver desde un navegador web');

    $objPHPExcel = new PHPExcel();
    $objPHPExcel->getProperties()->setCreator("CPI") 
                                 ->setLastModifiedBy("CPI") 
                                 ->setTitle("Informes CPI")
                                 ->setSubject("Informes CPI")
                                 ->setDescription("Informes CPI")
                                 ->setKeywords("informes CPI")
                                 ->setCategory("Informes CPI");
    
    $tituloReporte = "Reportes CPI";
    $tituloWeb = $tituloReporte;
    $titulosColumnas = array_shift($array);
    $cantidadColumnas = count($titulosColumnas);
    $cantidadColumnas = $cantidadColumnas - 1;
    $ultimaLetraPosicion = "";
    $this->estilo();
    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) {
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue("{$valor}3",  $titulosColumnas[$clave]);
        $ultimaLetraPosicion = $valor;
      }
    }

    $objPHPExcel->setActiveSheetIndex(0)
                ->mergeCells("A1:{$ultimaLetraPosicion}1")
                ->mergeCells("A2:{$ultimaLetraPosicion}2");
    $objPHPExcel->setActiveSheetIndex(0)
                ->setCellValue('A1', $tituloReporte)
                ->setCellValue('A2', $subtitulo);
    $l = 4;
    foreach ($array as $registro) {
      foreach ($registro as $clave=>$valor) {
        $posicion = $this->abecedario[$clave].$l; 
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue($posicion, $registro[$clave]);
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn("{$clave}")->setAutoSize(true);
      }
      $l++;
    }

    $celdas_titulos = "A3:{$ultimaLetraPosicion}3";
    $celdas_informacion = "A4:{$ultimaLetraPosicion}".($l-1);
    $objPHPExcel->getActiveSheet()->getStyle('A1')->applyFromArray($this->estilo_titulo);
    $objPHPExcel->getActiveSheet()->getStyle('A2')->applyFromArray($this->estilo_subtitulo);
    $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(50);
    $objPHPExcel->getActiveSheet()->getRowDimension(2)->setRowHeight(30);
    
    foreach ($this->abecedario as $clave=>$valor) {
      if ($clave <= $cantidadColumnas) {
        $objPHPExcel->getActiveSheet()->getColumnDimensionByColumn("{$clave}")->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getStyle("{$valor}3")->applyFromArray($this->estilo_titulo_columnas);
      }
    }
    
    $objPHPExcel->getActiveSheet()->setSharedStyle($this->estilo_informacion, "{$celdas_informacion}");
    $objPHPExcel->getActiveSheet()->setTitle("InfCPI");
    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet(0)->freezePaneByColumnAndRow(0,4);

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="infCPI.xls"');
    header('Cache-Control: max-age=0');
	
    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    file_put_contents('depuracion.txt', ob_get_contents());
    ob_end_clean();
    $objWriter->save('php://output');

  }

  /* ESTILO DE EXCEL */
  function estilo() {
    
    $this->estilo_titulo = array(
                            'font'=>array(
                                'name'=>'Verdana',
                                'bold'=>true,
                                'italic'=>false,
                                'strike'=>false,
                                'size'=>18,
                                'color'=>array('rgb'=>'FFFFFF')),
                            'fill'=>array(
                                'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                'color'=>array('rgb' => '2e588b')),
                            'alignment'=>array(
                                'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                'rotation'=>0,
                                'wrap'=>TRUE) );
    
    $this->estilo_subtitulo = array(
                                    'font'=>array(
                                        'name'=>'Verdana',
                                        'size'=>12,
                                        'bold'=>true,
                                        'italic'=>false,
                                        'strike'=>false,
                                        'color'=>array('rgb' => 'FFFFFF')),
                                    'fill'=>array(
                                        'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                        'color'=>array('rgb' => '2c5b94')), 
                                    'alignment'=>array(
                                        'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                        'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                        'rotation'=>0,
                                        'wrap'=>TRUE) );
    
    $this->estilo_titulo_columnas = array(
                                      'font'=>array(
                                          'name'=>'Verdana',
                                          'bold'=>true,                          
                                          'color'=>array('rgb'=>'FFFFFF')),
                                      'fill'=>array(
                                          'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                          'rotation'=>90,
                                          'color'=>array('rgb' => '6992c3')),
                                      'borders'=>array(
                                          'top'=>array(
                                              'style'=>PHPExcel_Style_Border::BORDER_MEDIUM,
                                              'color'=>array('rgb' => 'FFFFFF')),
                                          'bottom'=>array(
                                              'style'=>PHPExcel_Style_Border::BORDER_MEDIUM,
                                              'color'=>array('rgb' => 'FFFFFF'))),
                                      'alignment' =>  array(
                                          'horizontal'=>PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
                                          'vertical'=>PHPExcel_Style_Alignment::VERTICAL_CENTER,
                                          'wrap'=>TRUE) );

    $this->estilo_informacion = new PHPExcel_Style();
    $this->estilo_informacion->applyFromArray(
                            array(
                              'font'=>array(
                                  'name'=>'Tahoma',               
                                  'size'=>10,               
                                  'color'=>array('rgb'=>'000000')),
                              'fill'=>array(
                                  'type'=>PHPExcel_Style_Fill::FILL_SOLID,
                                  'color'=>array('rgb'=>'DDDDDD')),
                              'borders'=>array(
                                  'allborders'=>array(
                                      'style'=>PHPExcel_Style_Border::BORDER_THIN,
                                      'color'=>array('rgb' => 'FFFFFF')))) );
  }
}
function ExcelReport() {return new ExcelReport();} 
?>