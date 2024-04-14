<?php
require 'init.php';
require 'priviledges.php';

$tempThn = '';
if ($_GET['s'] <> '' && $_GET['s'] <> '0')
  $and .= " AND `status`='{$_GET['s']}'";
$lastD2 = 30;
if (in_array($_GET['m2'], array(1, 3, 5, 7, 8, 10, 12)))
  $lastD2 = 31;
if ($_GET['m2'] == 2 && $_GET['t2'] % 4 == 0)
  $lastD2 = 29;
if ($_GET['m2'] == 2 && $_GET['t2'] % 4 != 0)
  $lastD2 = 28;

switch ($_GET['s']) {
  case '':
  case '0':
    if ($_GET['m1'] <> '')
      $and .= " AND created_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'UNAPPROVED':
  case 'REJECTED':
    $and .= " AND updated_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'APPROVED':
    $and .= " AND approve_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'SUBMITTED':
    $and .= " AND submit_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'SURVEY':
    $and .= " AND survey_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'PAYMENT':
    $and .= " AND payment_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'INVOICE':
    $and .= " AND invoice_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'SETTLED':
    $and .= " AND settlement_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
  case 'CASECLOSED':
    $and .= " AND caseclosed_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
    break;
}
if ($_GET['s'] <> '' && $_GET['s'] <> '0')
  $status = ' ' . $_GET['s'] . ' ';
elseif ($_GET['m1'] <> '')
  $status .= ' ';
if ($_GET['m1'] <> '' && $_GET['m1'] == $_GET['m2'] && $_GET['t1'] == $_GET['t2'])
  $status .= ' ' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'] . ' ';
elseif ($_GET['m1'] <> '')
  $status .= ' ' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'] . ' s.d. ' . $months[$_GET['m2'] - 1] . ' ' . $_GET['t2'] . '';

if ($user->role == 'spvr' || $user->role == 'mgrr') {
  $and .= " AND `user_id`='" . $user->user_id . "' ";
  $and .= " AND kode_region='" . $user->regional . "'";
} elseif ($_GET['r'] != '') {
  $and .= " AND kode_region='" . $_GET['r'] . "'";
}

$SQL = "SELECT kode_region,region,EXTRACT(YEAR FROM tgl_kejadian) thn,cgl.* FROM `cgl` 
WHERE 1 " . $and . " ORDER BY thn DESC ,`updated_at` DESC";
$rescgl = $db->get_results($SQL);

if (is_array($rescgl) && !empty($rescgl)) {
  require_once 'PHPExcel.php';

  $filename = $user->inisial . '-Laporan Klaim CGL ' . $status;
  $objPHPExcel = new PHPExcel();

  $objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', $filename)
    ->setCellValue('A3', 'No.')
    ->setCellValue('B3', "Tahun")
    ->setCellValue('C3', "Regional")
    ->setCellValue('D3', "Nomor Laporan")
    ->setCellValue('E3', "Tgl Kejadian")
    ->setCellValue('F3', "Tgl Diketahui")
    ->setCellValue('G3', "Site ID")
    ->setCellValue('H3', "Site Name")
    ->setCellValue('I3', "Sebab Kerusakan")
    ->setCellValue('J3', "Rincian Kerusakan")
    ->setCellValue('K3', "Estimasi Kerugian")
    ->setCellValue('L3', "Contact Person")
    ->setCellValue('M3', "Status");

  $i = 1;
  $row = 4;
  foreach ($rescgl as $cgl) {
    $objPHPExcel->setActiveSheetIndex(0)
      ->setCellValue('A' . $row, $i . '. ')
      ->setCellValue('B' . $row, $cgl->thn)
      ->setCellValue('C' . $row, $cgl->region)
      ->setCellValue('D' . $row, $cgl->no_laporan)
      ->setCellValue('E' . $row, date("d-m-Y", strtotime($cgl->tgl_kejadian)))
      ->setCellValue('F' . $row, date("d-m-Y", strtotime($cgl->tgl_tuntutan)))
      ->setCellValue('G' . $row, $cgl->st_site_id)
      ->setCellValue('H' . $row, $cgl->st_name)
      ->setCellValue('I' . $row, $cgl->sebab)
      ->setCellValue('J' . $row, $cgl->rincian)
      ->setCellValue('K' . $row, $cgl->estimasi)
      ->setCellValue('L' . $row, $cgl->cp_nama . ' ' . $cgl->cp_telp . ' ' . $cp_hp)
      ->setCellValue('M' . $row, $cgl->status);
    $row++;
    $i++;
  }
}
// Rename sheet
$objPHPExcel->getActiveSheet()->setTitle('CGL');
$objPHPExcel->setActiveSheetIndex(0);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
header('Cache-Control: max-age=0');
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save('php://output');
exit;
?>