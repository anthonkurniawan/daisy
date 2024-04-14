<?php
require 'init.php';
require 'priviledges.php';

if($_SESSION['gets']['p']=='1') 
	$and .=" AND created_at BETWEEN '".($_SESSION['gets']['t1']-1)."-12-01 00:00:00' AND '".$_SESSION['gets']['t2']."-06-30 23:59:59'";
else
	$and .=" AND created_at BETWEEN '".$_SESSION['gets']['t1']."-08-02 00:00:00' AND '".$_SESSION['gets']['t2']."-12-31 23:59:59'";

if($_SESSION['gets']['r']<>''){
	$and.= " AND kode_region='".$_SESSION['gets']['r']."'";
	$rx = $db->get_row("SELECT * FROM region WHERE kode_region='".$_SESSION['gets']['r']."'");
	$status.= " Regional ".$rx->region."";
} 

switch($_SESSION['gets']['s1']){
	case '1':$order.=" site.st_site_id ASC,";break;
	case '2':$order.=" region ASC ASC,";break;
	case '3':$order.=" submit_at DESC,";break;
	case '4':$order.=" tgl_kejadian DESC,";break;
	case '5':$order.=" tgl_tuntutan DESC,";break;
	case '6':$order.=" st ASC,";break;
}
switch($_SESSION['gets']['s2']){
	case '1':$order.=" site.st_site_id ASC,";break;
	case '2':$order.=" region ASC ASC,";break;
	case '3':$order.=" submit_at DESC,";break;
	case '4':$order.=" tgl_kejadian DESC,";break;
	case '5':$order.=" tgl_tuntutan DESC,";break;
	case '6':$order.=" st ASC,";break;
}

$SQL1 = "SELECT *,
	(if(status='UNAPPROVED',1,
		if(status='APPROVED',2,
			if(status='SUBMITTED',3,
				if(status='SURVEY',4,
					if(status='PAYMENT',5,
						if(status='INVOICE',6,
							if(status='SETTLED',7,
								if(status='CLOSED',8,0)
							)
						)
					)
				)
			)
		)
	)
) as st FROM `cgl` 
JOIN cgl_vendor v ON v.id_cglv=cgl.id_cglv
WHERE 1 ".$and." ORDER BY {$order} cgl_id DESC";
//echo $SQL1;
if($_SESSION['gets']['l']!='3') $cgl = $db->get_results($SQL1);
//$db->debug();
require_once 'PHPExcel.php';
	
if($_SESSION['gets']['l']=='1'){	
	$objPHPExcel = new PHPExcel();
	$filename = $user->inisial.'-REKAPITULASI KLAIM CGL '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'];
	
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'REKAPITULASI KLAIM CGL PT TELKOMSEL')
            ->setCellValue('A2', "REGIONAL: {$regional}")
			->setCellValue('A3', "TAHUN: ".$_SESSION['gets']['t']." (Periode Polis ".$_SESSION['gets']['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'.")")
			->setCellValue('A4', "NO Polis: ".($_SESSION['gets']['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000')."")			
			->setCellValue('A6', "No.")
			->setCellValue('B6', "Nomor Laporan")
			->setCellValue('C6', "Site Name")
			->setCellValue('D6', "Site ID")
			->setCellValue('E6', "Regional")
			->setCellValue('F6', "Tanggal Lapor SJU")
			->setCellValue('G6', "Tanggal kejadian")
			->setCellValue('H6', "Tanggal diketahui")
			->setCellValue('I6', "Penyebab Kerugian")
			->setCellValue('J6', "Estimasi Kerugian (BoQ)")
			->setCellValue('K6', "Nilai ganti rugi (Invoice)")
			->setCellValue('L6', "Vendor Pelaksana")
			->setCellValue('M6', "Status Klaim")
			->setCellValue('N6', "Surat Tuntutan Warga")
			->setCellValue('O6', "Dokumen BoQ")
			->setCellValue('P6', "Surat Tuntutan Telkomsel");
			
	if($_SESSION['gets']['r']<>''){
		$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
		$regional =$reg->region;
	}else $regional ='NASIONAL';
	
	$i=1;
	$row=7;
	foreach($cgl as $c):
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$row, $i.'. ')
		->setCellValue('B'.$row, $c->no_laporan)
		->setCellValue('C'.$row, $c->st_name)
		->setCellValue('D'.$row, $c->st_site_id)
		->setCellValue('E'.$row, $c->region)
		->setCellValue('F'.$row, $c->submit_at)
		->setCellValue('G'.$row, ($c->tgl_kejadian<>''&&$c->tgl_kejadian<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_kejadian)):''))
		->setCellValue('H'.$row, $c->tgl_tuntutan<>''&&$c->tgl_tuntutan<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_tuntutan)):'')
		->setCellValue('I'.$row, $c->sebab.' '.$c->rincian)
		->setCellValue('J'.$row, $c->estimasi)
		->setCellValue('K'.$row, $c->nilai_invoice)
		->setCellValue('L'.$row, $c->nama_vendor)
		->setCellValue('M'.$row, $c->status)
		->setCellValue('N'.$row, $c->file_surat_tuntutan!=''?'ADA':'BELUM ADA')
		->setCellValue('O'.$row, $c->file_boq!=''?'ADA':'BELUM ADA')
		->setCellValue('P'.$row, $c->file_invoice!=''?'ADA':'BELUM ADA');		
		$i++;
		$row++;
	endforeach;
	$objPHPExcel->getActiveSheet()->setTitle('REKAPITULASI KLAIM CGL ');
	$objPHPExcel->setActiveSheetIndex(0);

	
	if($_GET['f']='xlsx'){
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');		
	}else{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		
	}
	$objWriter->save('php://output');	
	exit;
}

//unset($_SESSION['gets']);

if($_SESSION['gets']['l']=='2'){ 
	$objPHPExcel = new PHPExcel();
	$filename = $user->inisial.'-DETAIL PROGRESS CGL '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'];
	
	if($_SESSION['gets']['r']<>''){
		$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
		$regional =$reg->region;
	}else $regional ='NASIONAL';
	
	$objPHPExcel->setActiveSheetIndex(0)
            ->setCellValue('A1', 'REPORT DETAIL PROGRESS CGL PT TELKOMSEL')
			->setCellValue('A2', "REGIONAL: {$regional}")
			->setCellValue('A3', "TAHUN: ".$_SESSION['gets']['t']." (Periode Polis ".$_SESSION['gets']['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'.")")
			->setCellValue('A4', "NO. POLIS: ".($_SESSION['gets']['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000')."")
			
			->setCellValue('A6', "No.")
			->setCellValue('B6', "Nomor Laporan")
			->setCellValue('C6', "Site Name")
			->setCellValue('D6', "Site ID")
			->setCellValue('E6', "Regional")
			->setCellValue('F6', "Tanggal Lapor SJU")
			->setCellValue('G6', "Tanggal Kejadian")
			->setCellValue('H6', "Tanggal Diketahui")
			->setCellValue('I6', "Penyebab Kerugian")
			->setCellValue('J6', "Estimasi Kerugian")
			->setCellValue('K6', "Nilai Ganti Rugi (invoice)")
			->setCellValue('L6', "Vendor Pelaksana")
			->setCellValue('M6', "Status Klaim")
			->setCellValue('N6', "Dokumen BoQ")
			->setCellValue('O6', "Create")
			->setCellValue('P6', "Approve")
			->setCellValue('Q6', "Submit")
			->setCellValue('R6', "Survey")
			->setCellValue('S6', "Payment")
			->setCellValue('T6', "Claim Letter")
			->setCellValue('U6', "Settled")
			->setCellValue('V6', "Closed");
		
	$i=1;
	$row = 7;
	foreach($cgl as $c):
		$objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A'.$row, $i.'. ')
		->setCellValue('B'.$row, $c->no_laporan)
		->setCellValue('C'.$row, $c->st_name)
		->setCellValue('D'.$row, $c->st_site_id)
		->setCellValue('E'.$row, $c->region)
		->setCellValue('F'.$row, $c->submit_at)
		->setCellValue('G'.$row, ($c->tgl_kejadian<>''&&$c->tgl_kejadian<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_kejadian)):''))
		->setCellValue('H'.$row, $c->tgl_tuntutan<>''&&$c->tgl_tuntutan<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_tuntutan)):'')
		->setCellValue('I'.$row, $c->sebab.' '.$c->rincian)
		->setCellValue('J'.$row, $c->estimasi)
		->setCellValue('K'.$row, $c->nilai_invoice)
		->setCellValue('L'.$row, $c->nama_vendor)
		->setCellValue('M'.$row, $c->status)
		->setCellValue('N'.$row, $c->file_boq!=''?'ADA':'BELUM ADA')		
		->setCellValue('O'.$row, $c->created_at?date("d/m/Y",strtotime($c->created_at)):'')
		->setCellValue('P'.$row, $c->approve_at<>''&&$c->approve_at<>'0000-00-00 00:00:00'?date("d/m/Y",strtotime($c->approve_at)):'')
		->setCellValue('Q'.$row, $c->submit_at<>''&&$c->submit_at<>'0000-00-00'?date("d/m/Y",strtotime($c->submit_at)):'')
		->setCellValue('R'.$row, $c->survey_date<>''&&$c->survey_date<>'0000-00-00'?date("d/m/Y",strtotime($c->survey_date.' 00:00:00')):'')
		->setCellValue('S'.$row, $c->payment_date<>''&&$c->payment_date<>'0000-00-00'?date("d/m/Y",strtotime($c->payment_date.' 00:00:00')):'')
		->setCellValue('T'.$row, $c->invoice_date<>''&&$c->invoice_date<>'0000-00-00'?date("d/m/Y",strtotime($c->invoice_date.' 00:00:00')):'')
		->setCellValue('U'.$row, $c->settled_date<>''&&$c->settled_date<>'0000-00-00'?date("d/m/Y",strtotime($c->settled_date.' 00:00:00')):'')
		->setCellValue('V'.$row, $c->caseclosed_at<>''&&$c->caseclosed_at<>'0000-00-00'?date("d/m/Y",strtotime($c->caseclosed_at)):'');
		$i++;$row++;
	endforeach;
	$objPHPExcel->getActiveSheet()->setTitle('REPORT DETAIL PROGRESS CGL');
	$objPHPExcel->setActiveSheetIndex(0);

	if($_GET['f']='xlsx'){
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');		
	}else{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		
	}
	$objWriter->save('php://output');	
	exit;
} 

if($_SESSION['gets']['l']=='3'){ 
	$SQL3  = "SELECT COUNT(1) jml, `status`,EXTRACT(MONTH FROM `created_at`) bln FROM cgl 
			JOIN cgl_vendor v ON v.id_cglv=cgl.id_cglv
			WHERE 1 ".$and." 
			GROUP BY `status`, bln";
	$res3 = $db->get_results($SQL3);
	//$db->debug();

	$summ = array();
	foreach($res3 as $r3){
		$summ[$r3->status][$r3->bln] = $r3->jml>0?$r3->jml:'0';
	}
	$filename = $user->inisial.'-SUMMARY REPORT KLAIM CGL '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'].'.xls';

	$objPHPExcel = new PHPExcel();

	if($_SESSION['gets']['r']<>''){
		$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
		$regional =$reg->region;
	}else $regional ='NASIONAL';


	$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A1', 'SUMMARY REPORT KLAIM CGL PT TELKOMSEL')
				->setCellValue('A2', "REGIONAL: {$regional}")
				->setCellValue('A3', "TAHUN: ".$_SESSION['gets']['t']." (Periode Polis ".$_SESSION['gets']['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'.")")
				->setCellValue('A4', "NO. POLIS: ".($_SESSION['gets']['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000')."")
				->setCellValue('A6','Status')
				->setCellValue('B6','Bulan');
				
	$r = 66;//B
	foreach($months as $m):
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'7',$m);
			$r++;
	endforeach;

	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A8',"Created");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A9',"Approve");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A10',"Submitted (Total Klaim)");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A11',"Outstanding");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A12',"Survey");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A13',"Payment");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A14',"Invoice");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A15',"Settled");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A16',"Closed Case");
	$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A17',"Total");
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'8',(int)$summ['UNAPPROVED'][$i]);
		$r++;
	} 

	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'9',(int)$summ['APPROVED'][$i]);
		$r++;
	} 

	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'10',(int)$summ['SUBMITTED'][$i]);
		$r++;
	}

	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$outs= $summ['SURVEY'][$i]+$summ['PAYMENT'][$i]+$summ['INVOICE'][$i];
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'11',(int)$outs);
		$r++;
	}
	  
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'12',(int)$summ['SURVEY'][$i]);
		$r++;
	}
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'13',(int)$summ['PAYMENT'][$i]);
		$r++;
	}
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'14',(int)$summ['INOVICE'][$i]);
		$r++;
	}
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'15',(int)$summ['SETTLED'][$i]);
		$r++;
	}
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'16',(int)$summ['CLOSED'][$i]);
		$r++;
	}
	$r = 66;//B
	for($i=1;$i<=12;$i++){
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($r).'17',$summ['CLOSED'][$i]+$summ['UNAPPROVED'][$i]+$summ['APPROVED'][$i]+$summ['SUBMITTED'][$i]+$summ['SURVEY'][$i]+$summ['PAYMENT'][$i]+$summ['INVOICE'][$i]+$summ['SETTLED'][$i]);
		$r++;
	}
	
	$objPHPExcel->getActiveSheet()->setTitle('SUMMARY REPORT KLAIM CGL');
	$objPHPExcel->setActiveSheetIndex(0);

	if($_GET['f']='xlsx'){
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="'.$filename.'.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');		
	}else{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="'.$filename.'.xls"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');		
	}
	$objWriter->save('php://output');	
	exit;
} 
?>