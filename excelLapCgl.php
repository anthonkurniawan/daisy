<?php
require 'init.php';
require 'priviledges.php';
require 'excelwriter.inc.php';

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
if($_SESSION['gets']['l']=='1'){
	$filename = $user->inisial.'-REKAPITULASI KLAIM CGL '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'].'.xls';
	$excel=new ExcelWriter($filename);	
	if($excel==false)	
		echo $excel->error;
		
	$fileBody .= $excel->open();	
	
	$fileBody .= $excel->writeRow();
	$fileBody .= $excel->writeCol("REKAPITULASI KLAIM CGL PT TELKOMSEL");
	
	if($_SESSION['gets']['r']<>''){
		$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
		$regional =$reg->region;
	}else $regional ='NASIONAL';
	
	$fileBody .= $excel->writeRow();
	$fileBody .= $excel->writeCol("REGIONAL: {$regional}");
	
	$fileBody .= $excel->writeRow();
	$fileBody .= $excel->writeCol("TAHUN: ".$_SESSION['gets']['t']." (Periode Polis ".$_SESSION['gets']['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'.")");
	
	$fileBody .= $excel->writeRow();
	$fileBody .= $excel->writeCol("REGIONAL: ".($_SESSION['gets']['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000')."");
	
	
	$fileBody .= $excel->writeRow();
	$fileBody .= $excel->writeCol("No.");
	$fileBody .= $excel->writeCol("Nomor Laporan");
	$fileBody .= $excel->writeCol("Site Name");
	$fileBody .= $excel->writeCol("Site ID");
	$fileBody .= $excel->writeCol("Regional");
	$fileBody .= $excel->writeCol("Tanggal Lapor SJU");
	$fileBody .= $excel->writeCol("Tanggal kejadian");
	$fileBody .= $excel->writeCol("Tanggal diketahui");
	$fileBody .= $excel->writeCol("Penyebab Kerugian");
	$fileBody .= $excel->writeCol("Estimasi Kerugian (BoQ)");
	$fileBody .= $excel->writeCol("Nilai ganti rugi (Invoice)");
	$fileBody .= $excel->writeCol("Vendor Pelaksana");
	$fileBody .= $excel->writeCol("Status Klaim");
	$fileBody .= $excel->writeCol("Surat Tuntutan Warga");
	$fileBody .= $excel->writeCol("Dokumen BoQ");
	$fileBody .= $excel->writeCol("Surat Tuntutan Telkomsel");
	
	$i=1;foreach($cgl as $c):
		$fileBody .= $excel->writeRow();
		$fileBody .= $excel->writeCol($i.'. ');
		$fileBody .= $excel->writeCol($c->no_laporan);
		$fileBody .= $excel->writeCol($c->st_name);
		$fileBody .= $excel->writeCol($c->st_site_id);
		$fileBody .= $excel->writeCol($c->region);
		$fileBody .= $excel->writeCol($c->submit_at);
		$fileBody .= $excel->writeCol(($c->tgl_kejadian<>''&&$c->tgl_kejadian<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_kejadian)):''));
		$fileBody .= $excel->writeCol($c->tgl_tuntutan<>''&&$c->tgl_tuntutan<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_tuntutan)):'');
		$fileBody .= $excel->writeCol($c->sebab.' '.$c->rincian);
		$fileBody .= $excel->writeCol($c->estimasi);
		$fileBody .= $excel->writeCol($c->nilai_invoice);
		$fileBody .= $excel->writeCol($c->nama_vendor);
		$fileBody .= $excel->writeCol($c->status);
		$fileBody .= $excel->writeCol($c->file_surat_tuntutan!=''?'ADA':'BELUM ADA');
		$fileBody .= $excel->writeCol($c->file_boq!=''?'ADA':'BELUM ADA');
		$fileBody .= $excel->writeCol($c->file_invoice!=''?'ADA':'BELUM ADA');		
		$i++;
	endforeach;
	$excel->close($fileBody);
}
//unset($_SESSION['gets']);

if($_SESSION['gets']['l']=='2'){ 
$filename = $user->inisial.'-DETAIL PROGRESS CGL '.$_SESSION['gets']['r'].'.'.$_SESSION['gets']['m1'].' '.$_SESSION['gets']['t1'].'-'.$_SESSION['gets']['m2'].' '.$_SESSION['gets']['t2'].'.xls';
$excel=new ExcelWriter($filename);
if($excel==false)	
	echo $excel->error;
$fileBody2 .= $excel->open();	

$fileBody2 .= $excel->writeRow();
$fileBody2 .= $excel->writeCol("REPORT DETAIL PROGRESS CGL PT TELKOMSEL");
if($_SESSION['gets']['r']<>''){
	$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
	$regional =$reg->region;
}else $regional ='NASIONAL';
$fileBody2 .= $excel->writeRow();
$fileBody2 .= $excel->writeCol("REGIONAL: {$regional}");

$fileBody2 .= $excel->writeRow();
$fileBody2 .= $excel->writeCol("TAHUN: ".$_SESSION['gets']['t']." (Periode Polis ".$_SESSION['gets']['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'.")");

$fileBody2 .= $excel->writeRow();
$fileBody2 .= $excel->writeCol("REGIONAL: ".($_SESSION['gets']['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000')."");
$fileBody2 .= $excel->writeRow();
$fileBody2 .= $excel->writeCol("No.");
$fileBody2 .= $excel->writeCol("Nomor Laporan");
$fileBody2 .= $excel->writeCol("Site Name");
$fileBody2 .= $excel->writeCol("Site ID");
$fileBody2 .= $excel->writeCol("Regional");
$fileBody2 .= $excel->writeCol("Tanggal Lapor SJU");
$fileBody2 .= $excel->writeCol("Tanggal Kejadian");
$fileBody2 .= $excel->writeCol("Tanggal Diketahui");
$fileBody2 .= $excel->writeCol("Pnyebab Kerugian");
$fileBody2 .= $excel->writeCol("Estimasi Kerugian");
$fileBody2 .= $excel->writeCol("Nilai Ganti Rugi (invoice)");
$fileBody2 .= $excel->writeCol("Vendor Pelaksana");
$fileBody2 .= $excel->writeCol("Status Klaim");
$fileBody2 .= $excel->writeCol("Dokumen BoQ");
$fileBody2 .= $excel->writeCol("Create");
$fileBody2 .= $excel->writeCol("Approve");
$fileBody2 .= $excel->writeCol("Submit");
$fileBody2 .= $excel->writeCol("Survey");
$fileBody2 .= $excel->writeCol("Payment");
$fileBody2 .= $excel->writeCol("Claim Letter");
$fileBody2 .= $excel->writeCol("Settled");
$fileBody2 .= $excel->writeCol("Closed");
	
$i=1;foreach($cgl as $c):
	$fileBody2 .= $excel->writeRow();
	$fileBody2 .= $excel->writeCol($i.'. ');
	$fileBody2 .= $excel->writeCol($c->no_laporan);
	$fileBody2 .= $excel->writeCol($c->st_name);
	$fileBody2 .= $excel->writeCol($c->st_site_id);
	$fileBody2 .= $excel->writeCol($c->region);
	$fileBody2 .= $excel->writeCol($c->submit_at);
	$fileBody2 .= $excel->writeCol(($c->tgl_kejadian<>''&&$c->tgl_kejadian<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_kejadian)):''));
	$fileBody2 .= $excel->writeCol($c->tgl_tuntutan<>''&&$c->tgl_tuntutan<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_tuntutan)):'');
	$fileBody2 .= $excel->writeCol($c->sebab.' '.$c->rincian);
	$fileBody2 .= $excel->writeCol($c->estimasi);
	$fileBody2 .= $excel->writeCol($c->nilai_invoice);
	$fileBody2 .= $excel->writeCol($c->nama_vendor);
	$fileBody2 .= $excel->writeCol($c->status);
	$fileBody2 .= $excel->writeCol($c->file_boq!=''?'ADA':'BELUM ADA');
	$fileBody2 .= $excel->writeCol($c->status);
	$fileBody2 .= $excel->writeCol($c->created_at?date("d/m/Y",strtotime($c->created_at)):'');
	$fileBody2 .= $excel->writeCol($c->approve_at<>''&&$c->approve_at<>'0000-00-00 00:00:00'?date("d/m/Y",strtotime($c->approve_at)):'');
	$fileBody2 .= $excel->writeCol($c->submit_at<>''&&$c->submit_at<>'0000-00-00'?date("d/m/Y",strtotime($c->submit_at)):'');
	$fileBody2 .= $excel->writeCol($c->survey_date<>''&&$c->survey_date<>'0000-00-00'?date("d/m/Y",strtotime($c->survey_date.' 00:00:00')):'');
	$fileBody2 .= $excel->writeCol($c->payment_date<>''&&$c->payment_date<>'0000-00-00'?date("d/m/Y",strtotime($c->payment_date.' 00:00:00')):'');
	$fileBody2 .= $excel->writeCol($c->invoice_date<>''&&$c->invoice_date<>'0000-00-00'?date("d/m/Y",strtotime($c->invoice_date.' 00:00:00')):'');
	$fileBody2 .= $excel->writeCol($c->settled_date<>''&&$c->settled_date<>'0000-00-00'?date("d/m/Y",strtotime($c->settled_date.' 00:00:00')):'');
	$fileBody2 .= $excel->writeCol($c->caseclosed_at<>''&&$c->caseclosed_at<>'0000-00-00'?date("d/m/Y",strtotime($c->caseclosed_at)):'');
	$i++;
endforeach;
	$excel->close($fileBody2);
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

$excel=new ExcelWriter($filename);
if($excel==false)	
	echo $excel->error;
$fileBody = '';
$fileBody .= $excel->open();	

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("SUMMARY REPORT KLAIM CGL PT TELKOMSEL");
if($_SESSION['gets']['r']<>''){
	$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_SESSION['gets']['r']}'");
	$regional =$reg->region;
}else $regional ='NASIONAL';
$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("REGIONAL: {$regional}");

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("TAHUN: ".$_SESSION['gets']['t']." (Periode Polis ".$_SESSION['gets']['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'.")");

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("REGIONAL: ".($_SESSION['gets']['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000')."");


$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Status");
$fileBody .= $excel->writeCol("Bulan");
$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol(" ");
foreach($months as $m):
		$fileBody .= $excel->writeCol($m);
endforeach;

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Created");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['UNAPPROVED'][$i]);
} 


$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Approve");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['APPROVED'][$i]);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Submitted (Total Klaim)");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['SUBMITTED'][$i]);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Outstanding");
for($i=1;$i<=12;$i++){
	$outs= $summ['SURVEY'][$i]+$summ['PAYMENT'][$i]+$summ['INVOICE'][$i];
	$fileBody .= $excel->writeCol((int)$outs);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Survey");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['SURVEY'][$i]);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Payment");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['PAYMENT'][$i]);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Invoice");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['INOVICE'][$i]);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Settled");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['SETTLED'][$i]);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Closed Case");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol((int)$summ['CLOSED'][$i]);
} 

$fileBody .= $excel->writeRow();
$fileBody .= $excel->writeCol("Total");
for($i=1;$i<=12;$i++){
	$fileBody .= $excel->writeCol($summ['CLOSED'][$i]+$summ['UNAPPROVED'][$i]+$summ['APPROVED'][$i]+$summ['SUBMITTED'][$i]+$summ['SURVEY'][$i]+$summ['PAYMENT'][$i]+$summ['INVOICE'][$i]+$summ['SETTLED'][$i]);
}


	$excel->close($fileBody);
} 
?>