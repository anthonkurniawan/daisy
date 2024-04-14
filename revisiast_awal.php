<?php
require 'init.php';
require 'priviledges.php';
include "headerast.php";

$err = array();

$rast = $db->get_row("SELECT * FROM `ast2` WHERE ast_id='".$_GET['revisi']."'");

switch($rast->status)
{
	case 'UNAPPROVED':
		$mode = $user->role=='mgrr'?'approval':'revisi';
		$caption = $user->role=='mgrr'?'Approve Laporan AST':'Submit Revisi AST';
	break;
	case 'REJECTED':
		$mode = $user->role=='spvr'?'revisi':'view';
		$caption = $user->role=='spvr'?'Submit Revisi AST':'';
	break;
	case 'APPROVED':
		$mode = $user->role=='stfp'?'submit':'view';
		$caption = $user->role=='stfp'?'Submit Klaim AST':'';
	break;
	case 'SUBMITTED':
		$mode = $user->role=='spvr'?'survey':'view';
		$caption = $user->role=='spvr'?'Submit Survey AST':'';
	break;
	case 'PAYMENT':
		$mode = $user->role=='spvr'?'invoice':'view';
		$caption = $user->role=='spvr'?'Submit Invoice Klaim AST':'';
	break;
	case 'INVOICE':
		$mode = $user->role=='gmp'?'settlement':'view';
		$caption = $user->role=='spvp'?'Set Klaim AST, SETTLED':'';
	break;
	case 'SETTLED':
	case 'CASECLOSED':
	default:
	$mode = 'view';
	break;
}

//================================================ REVIEW ====================================================================
if($_GET['m']=='review') $mode = 'view'; 


//=============================================== APPROVAL ===================================================================
if($_POST && $mode=='approval') 
{
	if($_POST['isReject']=='1')
	{
		$newStatus = 'REJECTED';
		$set='`reject_at`=NOW()';			
	}else
	{
		$newStatus = 'APPROVED';
		$set='`approve_at`=NOW()';				
	}
	/* ------- INSERT STATUS LOG----------
	$db->query("UPDATE ast2 SET `status`='{$newStatus}',{$set} WHERE ast_id='".$_POST['i']."'");		
	$db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
	('cgl','".$_POST['i']."','".$rcgl->no_laporan."','".$user->user_id."','{$newStatus}',NOW())");	
	*/
	$upd = 1;
	
	//----------------------- send emails (STATUS APPROVAL)
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rast->st_site_id}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	//----- EDIT FROM cgl_vendor ??
		
		$raw 			= file_get_contents('cgl_survey.email.htm');   //-------- EDIT TO "ast_survey.email.htm"
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%', //-- RUBAH PATTERN
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",
						  strtotime($rcgl->tgl_tuntutan)),'['.$r->st_site_id.']'.$r->st_name, $rcgl->st_address, 
						  $r->st_region,$rcgl->st_latitude.'/'.$rcgl->st_longitude, $sebab.'.'.$rcgl->oth_sebab,$rcgl->rincian,$rcgl->cp_nama,
						  $rcgl->cp_telp, $rcgl->cp_hp, $user->nama,$user->posisi,$r2->nama_vendor, $rcgl->vendor_pic,
						  $rcgl->vendor_telp,$rcgl->vendor_hp);
		
		$emailBody = str_replace($pattern, $replaceWith, $raw);
			
		//get recipients		
		$recipients = $db->get_results("SELECT nama,email1 FROM user WHERE `role`='spvp'");
		if(!empty($recipients))
		{
			foreach($recipients as $recipient)
			{
				$to[$recipient->nama]	=	$recipient->email1;
			}	
			
			require 'initMail.php';
			sendMail('Klaim CGL ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($rcgl->tgl_kejadian)).': '.$newStatus,$emailBody,$to,$cc,$bcc);
		}
}		

//============================================ PAYMENT ?? (UNEDITED)------------------------------------------------------------> 
if($_POST && $mode=='payment')  
{
	if($_POST['payment']=='') $err[]="tanggal payment";
	//if($_FILE['foto']['name']=='') $err[]="lampiran foto";
	//if($_FILE['kwi']['name']=='') $err[]="lampiran kwitansi";
	//if($_FILE['sps']['name']=='') $err[]="lampiran SPS";
	//if($_FILE['kro']['name']=='') $err[]="lampiran kronologis";
	//if($_FILES['s_boq']['name']=='') $err[]="lampiran kronologis";
	
	if(strtotime($_POST['payment']) < strtotime($rast->survey_date)) $err[]="Tanggal payment tidak boleh kurang dari tanggal survey";	
	
	if(empty($err)):
		
		$kode_laporan = str_pad($rast->no_laporan,2,"0",STR_PAD_LEFT).'/'.$user->inisial.date('d').'/'.$user->regional.'/AST/'.date("m").'/'.date("y");	
		$prefixFile = str_replace('/','',$kode_laporan);
		if($_FILES['s_boq']['name']<>'') $fboq = $prefixFile.'_boq_'.basename($_FILES['s_boq']['name']);
		$prefixFile2 = str_replace('/','',$rcgl->no_laporan);
		if($_FILES['foto']['name']<>'') $ffoto = $prefixFile2.'_foto_'.basename($_FILES['foto']['name']);
		if($_FILES['kwi']['name']<>'') 	$fkwi = $prefixFile2.'_kwitansi_'.basename($_FILES['kwi']['name']);
		if($_FILES['sps']['name']<>'') 	$fsps = $prefixFile2.'_sps_'.basename($_FILES['sps']['name']);
		if($_FILES['kro']['name']<>'') 	$fkro = $prefixFile2.'_kronologis_'.basename($_FILES['kro']['name']);		
		
		$uploaddir = 'docs/cgl/';
		if($_FILES['s_boq']['name']<>'')
		{ 
		if(!move_uploaded_file($_FILES['s_boq']['tmp_name'], $uploaddir.$fboq))echo 'File Upload Error: '.$_FILES['s_boq']['error'];
		}
		if($_FILES['foto']['name']<>'')
		{
			if(!move_uploaded_file($_FILES['foto']['tmp_name'], $uploaddir.$ffoto))echo 'File Upload Error: '.$_FILES['foto']['error'];
			}
		if($_FILES['kwi']['name']<>'')
		{
			if(!move_uploaded_file($_FILES['kwi']['tmp_name'], $uploaddir.$fkwi))echo 'File Upload Error: '.$_FILES['kwi']['error'];
		}
		if($_FILES['sps']['name']<>'')
		{
			if(!move_uploaded_file($_FILES['sps']['tmp_name'], $uploaddir.$fsps))echo 'File Upload Error: '.$_FILES['sps']['error'];
			}
		if($_FILES['kro']['name']<>'')
		{
			if(!move_uploaded_file($_FILES['kro']['tmp_name'], $uploaddir.$fkro))echo 'File Upload Error: '.$_FILES['kro']['error'];
			}
		
		$SQL = "UPDATE cgl 
			SET 
			`payment_date`='".$_POST['payment']."',`kerugian_survey`='".$_POST['kerugian_survey']."',
			`status`='PAYMENT', 
			".($ffoto?"`foto`='".$ffoto."',":'')."
			".($fkwi?"`kwitansi`='".$fkwi."',":'')."
			".($fsps?"`sps`='".$fsps."',":'')."
			".($fkro?"`kronologis`='".$fkro."',":'')."
			".($fboq?"`file_boq`='".$fboq."',":'')."
			`payment_at`=NOW(),
			`updated_at`=NOW() WHERE `cgl_id`='".$_POST['i']."'";
		$db->query($SQL);
		$db->query("INSERT INTO `status_log`(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) 
			VALUES 
			('cgl','".$rcgl->cgl_id."','".$rcgl->no_laporan."','".$user->user_id."','PAYMENT',NOW())");
		
		//---- SEND EMAIL (STATUS PAYMENT)
		$query = "SELECT st_site_id,st_name,st_region,st_longitude,st_latitude,st_address  FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		
		$query2 = "SELECT * FROM `cgl_vendor` WHERE `id_cglv` = '{$rcgl->id_cglv}'";
		$r2 = $db->get_row($query2);	
		
		$raw 			= file_get_contents('cgl_unapproved.email.htm');
		$pattern 		= array('%%V%%','%%VHP%%','%%VTELP%%','%%VNAMA%%','%%PAYMENTDATE%%','%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%',                                '%%TGLTUNTUTAN%%','%%NAMASITE%%','%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							    '%%ESTIMASI%%','%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%');
		$replaceWith 	= array($r2->nama_vendor,$rcgl->vendor_hp,$rcgl->vendor_telp,$rcgl->vendor_pic,$rcgl->payment_date,$kode_laporan,
								date("Y"),date("l/ j F Y",strtotime($_POST['tgl_kejadian'])),date("l/ j F Y",strtotime($_POST['tgl_tuntutan'])),
								'['.$r->st_site_id.']'.$r->st_name,$r->st_address,$r->st_region,$r->st_latitude.'/'.$r->st_longitude,$sebab,
								$_POST['rincian'],$_POST['estimasi'],$_POST['cp_nama'],$_POST['cp_telp'],$_POST['cp_hp'],
								$user->nama,$user->posisi);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
		$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `regional`='".$user->regional."' AND `role`='gmp'");
		if(!empty($recipients))
		{
			foreach($recipients as $recipient)
			{
				if($recipient->email1<>'' || $recipient->email2<>'')
				{
					$to[$recipient->nama]	=	$recipient->email1;
					if($recipient->email2) $to[$recipient->nama.' 2']	=	$recipient->email2;
				}
				else
				{
					$to[$recipient->nama]	=	$recipient->email1;
				}
			}	
		}
		
		//------------------------ send emails (STATUS SURVEY)
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	
		
		$raw 			= file_get_contents('cgl_survey.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",
								strtotime($rcgl->tgl_tuntutan)),'['.$r->st_site_id.']'.$r->st_name,
							    $rcgl->st_address,$r->st_region,$rcgl->st_latitude.'/'.$rcgl->st_longitude,$sebab.'.'.$rcgl->oth_sebab,
								$rcgl->rincian,$rcgl->cp_nama,$rcgl->cp_telp,$rcgl->cp_hp,$user->nama,$user->posisi,
						        $r2->nama_vendor, $rcgl->vendor_pic,$rcgl->vendor_telp,$rcgl->vendor_hp);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
		$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `regional`='".$user->regional."' AND `role`='mgrr'");
		if(!empty($recipients))
		{
			foreach($recipients as $recipient)
			{
				if($recipient->email2<>'')
				{
					$to[$recipient->nama]	=	$recipient->email1;
					if($recipient->email2) $to[$recipient->nama.' 2']	=	$recipient->email2;
				}
				else
				{
					$to[$recipient->nama]	=	$recipient->email1;
				}
			}	
			
			require 'initMail.php';
			sendMail('Klaim CGL ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($rcgl->tgl_kejadian)).': PAYMENT',$emailBody,$to,$cc,$bcc);
		}
		
		
		$paymentSet = 1;
	endif;
}

//================================================== INVOICE ?? (UNEDITED)=============================================================
if($_POST && $mode=='invoice')  
{
	if($_POST['invoice']=='') $err[]="tanggal invoice";
	if($_POST['besaran_invoice']=='') $err[]="besaran invoice";
	if(!is_numeric($_POST['besaran_invoice'])) $err[]="besaran invoice hanya dapat berupa angka";
	if(strtotime($_POST['invoice']) < strtotime($rcgl->payment_date)) $err[]="Tanggal invoice tidak boleh kurang dari tanggal payment";	
	
	if(empty($err)):	
		$SQL = "UPDATE cgl 
			SET 
			`invoice_date`='".$_POST['invoice']."', 
			`nilai_invoice`='".$_POST['besaran_invoice']."', 
			`status`='INVOICE', 
			`invoice_at`=NOW(),
			`updated_at`=NOW() WHERE `cgl_id`='".$_POST['i']."'";
		$db->query($SQL);
		$db->query("INSERT INTO `status_log` (`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) 
					VALUES ('cgl','".$rcgl->cgl_id."','".$rcgl->no_laporan."','".$user->user_id."','INVOICE',NOW())");
		$invoiceSet = 1;
		
		// send emails
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	
		
		$raw 			= file_get_contents('cgl_survey.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",
						        strtotime($rcgl->tgl_tuntutan)),'['.$r->st_site_id.']'.$r->st_name,$rcgl->st_address,
						        $r->st_region,$rcgl->st_latitude.'/'.$rcgl->st_longitude,$sebab.'.'.$rcgl->oth_sebab,
								$rcgl->rincian,$rcgl->cp_nama,$rcgl->cp_telp,$rcgl->cp_hp,$user->nama,$user->posisi,
						        $r2->nama_vendor, $rcgl->vendor_pic,$rcgl->vendor_telp,$rcgl->vendor_hp);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
		$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `regional`='".$user->regional."' AND `role`='mgrr'");
		
		if(!empty($recipients))
		{
			foreach($recipients as $recipient)
			{
				if($recipient->email2<>'')
				{
					$to[$recipient->nama]	=	$recipient->email1;
					if($recipient->email2) $to[$recipient->nama.' 2']	=	$recipient->email2;
				}
				else
				{
					$to[$recipient->nama]	=	$recipient->email1;
				}
			}	
			require 'initMail.php';
			sendMail('Klaim CGL ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($rcgl->tgl_kejadian)).': INVOICE',$emailBody,$to,$cc,$bcc);
		}
	endif;
}

//=============================================== SURVEY ?? (UNEDITED)==============================================
if($_POST && $mode=='survey')  
{	
	if($_FILES['tuntutan']['name']=='') $err[]="file surat tuntutan";	
	if($_POST['survey_date']=='') $err[]="tanggal survey";	
	if(strtotime($_POST['survey_date']) < strtotime($rcgl->submit_at)) $err[]="Tanggal survey tidak boleh kurang dari tanggal submitted";	
	
	if(empty($err)):	
		$kode_laporan = str_pad($rcgl->no_laporan,2,"0",STR_PAD_LEFT).'/'.$user->inisial.date('d').'/'.$user->regional.'/CGL/'.date("m").'/'.date("y");	
		$prefixFile = str_replace('/','',$kode_laporan);	
		if($_FILES['tuntutan']['name']<>'')
			$filename1 = $prefixFile.'_tuntutan_'.basename($_FILES['tuntutan']['name']);
		if($_FILES['s_boq']['name']<>'')
			$filename2 = $prefixFile.'_boq_'.basename($_FILES['s_boq']['name']);
		$uploaddir = 'docs/cgl/';
		$uploadfile1 = $uploaddir . $filename1;
		$uploadfile2 = $uploaddir . $filename2;
		if($_FILES['tuntutan']['name']<>'')
		{
			if(!move_uploaded_file($_FILES['tuntutan']['tmp_name'], $uploadfile1))echo 'File Upload Error: '.$_FILES['tuntutan']['error'];}
		if($_FILES['s_boq']['name']<>'')
		{
			if(!move_uploaded_file($_FILES['s_boq']['tmp_name'], $uploadfile2))echo 'File Upload Error: '.$_FILES['s_boq']['error'];}	
		
		$SQL = "UPDATE cgl 
			SET 
			".($filename1?"`file_surat_tuntutan`='".$filename1."',":'')."
			".($filename2?"`file_boq`='".$filename2."',":'')."
			`kerugian_survey`='".$_POST['kerugian_survey']."',
			`status`='SURVEY', `survey_at`=NOW(), `survey_date`='".$_POST['survey_date']."' WHERE `cgl_id`='".$_POST['i']."'";
		$db->query($SQL);
		$db->query("INSERT INTO `status_log` (`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) 
					VALUES ('cgl','".$rcgl->cgl_id."','".$rcgl->no_laporan."','".$user->user_id."','SURVEY',NOW())");
		$issurvey = 1;
		
		//----- send emails (STATUS SURVEY )
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	
		
		$raw 			= file_get_contents('cgl_survey.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",
								strtotime($rcgl->tgl_tuntutan)),'['.$r->st_site_id.']'.$r->st_name,$rcgl->st_address,
								$r->st_region,$rcgl->st_latitude.'/'.$rcgl->st_longitude,$sebab.'.'.$rcgl->oth_sebab,
								$rcgl->rincian,$rcgl->cp_nama,$rcgl->cp_telp,$rcgl->cp_hp,$user->nama,$user->posisi,
						        $r2->nama_vendor, $rcgl->vendor_pic,$rcgl->vendor_telp,$rcgl->vendor_hp);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
		$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `regional`='".$user->regional."' AND `role`='mgrr'");
		if(!empty($recipients))
		{
			foreach($recipients as $recipient)
			{
				if($recipient->email2<>'')
				{
					$to[$recipient->nama]	=	$recipient->email1;
					if($recipient->email2) $to[$recipient->nama.' 2']	=	$recipient->email2;
				}
				else
				{
					$to[$recipient->nama]	=	$recipient->email1;
				}
			}	
			
			require 'initMail.php';
			sendMail('Klaim CGL ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($rcgl->tgl_kejadian)).': SURVEY',$emailBody,$to,$cc,$bcc);
		}
	endif;
}

//================================================ REVISI EDITED TO AST ==============================================
if($_POST && $mode=='revisi')
{
$res = $db->get_row("SELECT no_laporan FROM ast2 WHERE inisial='".strtoupper($user->inisial)."' AND DAYOFMONTH(created_at)='".date('j')."' ORDER BY created_at DESC");
	
	if($res->no_laporan<>'')
	{
		$aNoLap = explode('/',$res->no_laporan);
		(int) $no_laporan = $aNoLap[0];
		$no_laporan++;
	}
	else $no_laporan=1;	
	//$kode_laporan = str_pad($no_laporan,2,"0",STR_PAD_LEFT).'/'.$user->inisial.date('d').'/'.$user->regional.'/AST/'.date("m").'/'.date("y");
	$kode_laporan = str_pad($no_laporan,2,"0",STR_PAD_LEFT).'/'.$user->regional.'/AST/'.date("d").'/'.date("m").'/'.date("y");	
	
$prefixFile = str_replace('/','',$kode_laporan);
	if($_FILES['dok1']['name']<>'') {
		                             $d1= $prefixFile.'_1_'.basename($_FILES['dok1']['name']);
									 $d1_tgl="NOW()";} else {$d1_tgl="1";}
	if($_FILES['dok2']['name']<>'') {
		                             $d2= $prefixFile.'_2_'.basename($_FILES['dok2']['name']);
									 $d2_tgl="NOW()";} else {$d2_tgl="1";}
	if($_FILES['dok3']['name']<>'') {
		                             $d3= $prefixFile.'_3_'.basename($_FILES['dok3']['name']);
									 $d3_tgl="NOW()";} else {$d3_tgl="1";}
	if($_FILES['dok4']['name']<>'') {
		                              $d4= $prefixFile.'_4_'.basename($_FILES['dok4']['name']);
	                                 $d4_tgl="NOW()";} else {$d4_tgl="1";}
	if($_FILES['dok5']['name']<>'') {
		                              $d5= $prefixFile.'_5_'.basename($_FILES['dok5']['name']);
	                                  $d5_tgl="NOW()";} else {$d5_tgl="1";}
	if($_FILES['dok6']['name']<>'') {
		                              $d6= $prefixFile.'_6_'.basename($_FILES['dok6']['name']);
	                                  $d6_tgl="NOW()";} else {$d6_tgl="1";}
	if($_FILES['dok7']['name']<>'') {
		                              $d7= $prefixFile.'_7_'.basename($_FILES['dok7']['name']);
	                                  $d7_tgl="NOW()";} else {$d7_tgl="1";}
	if($_FILES['dok8']['name']<>'') {
		                              $d8= $prefixFile.'_8_'.basename($_FILES['dok8']['name']);
	                                  $d8_tgl="NOW()";} else {$d8_tgl="1";}
    if($_FILES['dok9']['name']<>'') {
		                              $d9= $prefixFile.'_9_'.basename($_FILES['dok9']['name']);
	                                  $d9_tgl="NOW()";} else {$d9_tgl="1";}
	if($_FILES['dok10']['name']<>'') {
		                              $d10= $prefixFile.'_10_'.basename($_FILES['dok10']['name']);
	                                  $d10_tgl="NOW()";} else {$d10_tgl="1";}
	
	
	
	switch($_POST['cod'])
	{
		case 'nds':$sebab='Natural Dissaster (Bencana Alam)';break;
		case 'rio':$sebab='Riots/ Strikes, Malicious Damage (Kerusuhan)';break;
		case 'thf':$sebab='Theft (Pencurian)';break;
		case 'lit':$sebab='Lightning (Petir)';break;
		case 'etv':$sebab='Earthquake, Tsunami, Volcano Erruption';break;
		case 'fre':$sebab='Fire (Terbakar/ Kebakaran)';break;
		case 'trp':$sebab='Third Party (Tuntutan Pihak ketiga)';break;
		case 'oth':$sebab='Other Losses (Lainnya..):'.$_POST['oth_sebab'];break;
	}
		
	$SQL = "UPDATE ast2 SET
		`status_claim`='".$_POST['sclaim']."',
		`st_site_id`='".$_POST['site']."',
		`flag_natural_diss`='".($_POST['cod']=='nds'?'1':'0')."',
		`flag_riot`='".($_POST['cod']=='rio'?'1':'0')."',
		`flag_theft`='".($_POST['cod']=='thf'?'1':'0')."',
		`flag_fire`='".($_POST['cod']=='fre'?'1':'0')."',
		`flag_lightning`='".($_POST['cod']=='lit'?'1':'0')."',
		`flag_earthquake_tsunami_volcano`='".($_POST['cod']=='etv'?'1':'0')."',
		`flag_other`='".($_POST['cod']=='oth'?'1':'0')."',
		`flag_third_party`='".($_POST['cod']=='trp'?'1':'0')."',
		".($d1<>''?"`doc1`='{$d1}',":'')."
		".($d2<>''?"`doc2`='{$d2}',":'')."
		".($d3<>''?"`doc3`='{$d3}',":'')."
		".($d4<>''?"`doc4`='{$d4}',":'')."
		".($d5<>''?"`doc5`='{$d5}',":'')."
		".($dk<>''?"`dockhusus`='{$dk}',":'')."
		`status`='UNAPPROVED',
		`updated_at`=NOW() WHERE ast_id='".$_POST['i']."'";
	$db->query($SQL);
	
	//$assetgroups = array();
	//$assetgroups = $_POST['asetgrup'];
	$db->query("DELETE FROM ast_asset_group WHERE ast_id='".$_POST['i']."'");  // hilangkan laporan, ganti yg baru
	$db->query("DELETE FROM ast_asset WHERE ast_id='".$_POST['i']."'");
	
	/*
	foreach($assetgroups as $ag){
		$db->query("INSERT INTO ast_asset_group (`ast_id`,`asset_group_id`) VALUES ('".$_POST['i']."','".$ag."')");		
		$rAset = $db->get_results("SELECT * FROM `asset` WHERE `asset_group_id`='".$ag."'");
		if(!empty($rAset))
		{
			foreach($rAset as $aset){			
				if(isset($_POST['vendor'.$aset->asset_id])&&isset($_POST['merk'.$aset->asset_id])&&isset($_POST['jml'.$aset->asset_id])
				&&$_POST['vendor'.$aset->asset_id]>0&&$_POST['merk'.$aset->asset_id]>0){
					$sqlPrice = "SELECT price_per_unit FROM asset_detail WHERE asset_id='".$aset->asset_id."' AND vendor_id='".$_POST['vendor'.$aset->asset_id]."' AND merk_id='".$_POST['merk'.$aset->asset_id]."'";
					$rPrice = $db->get_row($sqlPrice);
					$price = $rPrice->price_per_unit;
					$subtotal = $_POST['jml'.$aset->asset_id] * $price;				
					$sqlag = "INSERT INTO ast_asset 
					(`ast_id`,`asset_id`,`vendor_id`,`merk_id`,`num_unit`,
					`price_per_unit`,`subtotal`,`created_at`) VALUES 
					('".$_POST['i']."','".$aset->asset_id."','".$_POST['vendor'.$aset->asset_id]."','".$_POST['merk'.$aset->asset_id]."','".$_POST['jml'.$aset->asset_id]."',
					'".$price."','".$subtotal."',NOW())";
					$db->query($sqlag);
				}
			}
		}
	}
	*/
	
	$uploaddir = 'docs/ast/';
	$uploadfile1 = $uploaddir . $filename1;
	$uploadfile2 = $uploaddir . $filename2;
	if($_FILES['tuntutan'])
  {
		if(!move_uploaded_file($_FILES['tuntutan']['tmp_name'], $uploadfile1));//echo 'Error code: '.$_FILES['tuntutan']['error'];
	}
	if($_FILES['s_boq'])
  {
		if(!move_uploaded_file($_FILES['s_boq']['tmp_name'], $uploadfile2));//echo 'Error code: '.$_FILES['s_boq']['error'];
	}
	$insast = 1;
}
//================================================ END EDITED =====================================================
/*		
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$_POST['lokasi']}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$_POST['cglv']}'");	
		
		$raw 			= file_get_contents('cgl_unapproved.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($_POST['tgl_kejadian'])),date("l/ j F Y",strtotime($_POST['tgl_tuntutan'])),'['.$r->st_site_id.']'.$r->st_name,
						$_POST['st_address'],$r->st_region,$_POST['st_latitude'].'/'.$_POST['st_longitude'],$sebab.'.'.$_POST['oth_sebab'],$_POST['rincian'],
						$_POST['cp_nama'],$_POST['cp_telp'],$_POST['cp_hp'],$user->nama,$user->posisi,
						$r2->nama_vendor, $_POST['vendor_pic'],$_POST['vendor_telp'],$_POST['vendor_hp']
		);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
		$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `regional`='".$user->regional."' AND `role`='mgrr'");
		if(!empty($recipients))
    {
			foreach($recipients as $recipient)
      {
				if($recipient->email2<>'')
        {
					$to[$recipient->nama]	=	$recipient->email1;
					if($recipient->email2) $to[$recipient->nama.' 2']	=	$recipient->email2;
				}else{
					$to[$recipient->nama]	=	$recipient->email1;
				}
			}	
			require 'initMail.php';
			sendMail('Klaim CGL ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($_POST['tgl_kejadian'])),$emailBody,$to,$cc,$bcc);
		}
	endif;	
}
*/	

?>
<!----------------- CEKIDOT --------------------------------------------------------------------------------->
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:200px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menu.php" ?>
			</ul>
		</td>
		<td>
			<?php if($invoiceSet==1)
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Invoice Laporan AST berhasil di input!<br /><a href="laporan_ast.php">Kembali ke halaman laporan AST &raquo;</a></p>
			</div>			
			<?php exit(); }
			
			if($paymentSet==1)
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Payment Laporan AST berhasil di input!<br /><a href="laporan_ast.php">Kembali ke halaman laporan AST &raquo;</a></p>
			</div>			
			<?php exit();}
			
			
			if($upd==1)
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Klaim AST berhasil di <?=$newStatus?>! <br /><a href="laporan_ast.php">Kembali ke halaman laporan AST &raquo;</a></p>
			</div>
			<?php exit();}
			
			if($issurvey===1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Survey AST berhasil di submit! <br /><a href="laporan_ast.php">Kembali ke halaman laporan AST &raquo;</a></p>
			</div>
			<?php exit();}
			
			if($insast===1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Revisi laporan AST berhasil di submit! <br /><a href="laporan_ast.php">Kembali ke halaman laporan AST &raquo;</a></p>
			</div>
			<?php }
			else
			{ ?>
            
 <!-------------------------------------------- LAPORAN AST BERDASARKAN STATUS ---------------------------->
			<h3>Revisi Laporan AST [ <?=$rast->no_laporan?> ]</h3>
			
			<?php if($rast->status=='SUBMITTED')
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Proses survey tidak bisa berjalan sebelum melampirkan:??EDIT??<br /> <strong>- Surat Tuntutan</strong>.</p>
			</div>
			<?php 
			} ?>
			
			<?php if(!empty($err))
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Mohon isi / perbaiki data berikut:
					<ul>
					<?php foreach($err as $e):
							?>
							<li><?=ucfirst($e)?></li>
							<?
							endforeach;
					?>
					</ul>
				</p>
			</div>
			<?php 
			} ?>
			
			<form method="post" action="" enctype="multipart/form-data"  autocomplete="off">
			<input type="hidden" name="i" value="<?=$rast->ast_id?>" />
			
<!------------------------------------------------- REPORT AST --------------------------------------------->        
<table id="fast">
				
                <tr class="even">
					<td><strong>Hari / tanggal kejadian</strong></td>
					<td>
						<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
						<?php if($mode=='revisi')
						{ ?>
						<input type="text" name="tgl_kejadian_show" id="tgl_kejadian_show" value="<?=date('l/ j F Y',
						 strtotime($rast->tgl_kejadian))?>" class="narr" />
                        <input type="hidden" name="tgl_kejadian" id="tgl_kejadian" value="<?=$rast->tgl_kejadian?>" />
						<div class="keterangan">[ctrl+panah]:untuk pindah tanggal, [pageUp/pageDown]:untuk pindah bulan, [Enter]:accept</div>
						<?php 
						}
						else
						{ ?>
						<?=date("l, d F Y",strtotime($rast->tgl_kejadian)) ?>
						<?php 
						} ?>
					</td>
				</tr>
                
				<tr class="even">
					<td>Site ID</td>
					<td>
					<?php if($mode=='revisi')
					{ ?>
							<select name="site" id="lokasite">
							<option value="">-Pilih Site ID-</option>
							<?php $resSite = $db->get_results("SELECT st_site_id,st_name FROM `site` WHERE kode_region='".$user->regional."' GROUP BY st_site_id ORDER BY st_site_id ASC"); 
							foreach($resSite as $site): ?>
							<option <?=($site->st_site_id==$rast->st_site_id?'selected="selected"':'')?> value="<?=$site->st_site_id?>"><?=$site->st_site_id?> / <?=$site->st_name?></option>
							<?php endforeach;
							?>							
						</select>
                        
                        <tr class="odd" valign="top">
					<td>Site Detail</td>
					<td><div id="siteDetail"></div></td>
				</tr>				
					<?php 
					}					
					else
					{
					$r = $db->get_row("SELECT * FROM `site` WHERE st_site_id='".$rast->st_site_id."'"); 
					//if($mode=='view') echo $rast->st_site_id.' / '.$r->st_name;	
					echo $rast->st_site_id.' / '.$r->st_name;}					
					?>	
					</td>
				</tr>
                
                <tr valign="top" class="even">
					<td><strong>Contact person</strong></td>
					<td>
						<table>
							<tr>
								<td>Nama</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi')
								{ ?>
								<input type="text" style="width:220px" name="cp_nama" value="<?=$rast->pic_region?>" /></td>
								<?php 
								}
								else
								{ ?>
								<?=$rast->pic_region ?>
								<?php 
								} ?>
							</tr>
							<tr>
								<td>No telepon/Fax</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi'){ ?>
								<input type="text" style="width:220px" name="cp_telp" value="<?=$rast->telp?>" />
								<?php 
								}
								else
								{ ?>
								<?=$rast->telp ?>
								<?php 
								} ?>
								</td>
							</tr>
                            <tr>
								<td>No HP</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi'){ ?>
								<input type="text" style="width:220px" name="cp_telp" value="<?=$rast->hp?>" />
								<?php 
								}
								else
								{ ?>
								<?=$rast->hp ?>
								<?php 
								} ?>
								</td>
							</tr>
	
                                
				<tr class="odd" valign="top">
					<td><strong>Status Claim</strong></td>
					<td>
						<?php if($mode=='revisi')
						{ ?>
						<input type="radio" name="sclaim" <?=($rast->status_claim=='total'?'checked="1"':'')?> value="total" id="total" /> <label for="total">Totally lost</label><br />
						<input type="radio" name="sclaim" <?=($rast->status_claim=='partial'?'checked="1"':'')?> value="partial" id="partial" /> <label for="partial">Partial lost</label>
					<?php 
					} 
					else
					{ ?>
                    <?=$rast->status_claim=='total'?'Total Loss':'Partial Loss'?>
          <?php } ?>          
                    </td>
				</tr>
                
			  
              
              <tr class="odd" valign="top">	
	          <td colspan="4"><div id="merk_type"></div></td>
 
</tr>
                
				<tr valign="top"  class="even">
					<td><strong>Cause of damage</strong></td>
					<td>
						<?php if($mode=='revisi')
						{ ?>
						<input type="radio" checked="<?=$rast->flag_natural_diss?>" name="cod" value="nds" id="nds" /> <label for="nds">Natural Dissaster (Bencana Alam)</label>
						<div class="keterangan">Dok. Khusus: Surat Keterangan dari BMG</div>
						<input type="radio" checked="<?=$rast->flag_riot?>" name="cod" value="rio" id="rio" /> <label for="rio">Riots/ Strikes, Malicious Damage (Kerusuhan)</label>
						<div class="keterangan">Surat Pernyataan dari Pemerintah atau Kepolisian setempat baik yang dipublikasikan maupun tidak</div>
						<input type="radio" checked="<?=$rast->flag_theft?>" name="cod" value="thf" id="thf" /> <label for="thf">Theft (Pencurian)</label>
						<div class="keterangan">Dok. Khusus: Surat Keterangan dari Pejabat atau instansi yang berwenang (dari Kepolisian)</div>
						<input type="radio" checked="<?=$rast->flag_lightning?>" name="cod" value="lit" id="lit" /> <label for="lit">Lightning (Petir)</label>
						<div class="keterangan">Dok. Khusus: Surat Keterangan dari BMG</div>							
						<input type="radio" checked="<?=$rast->flag_earthquake_tsunami_volcano?>" name="cod" value="etv" id="etv" /> <label for="etv">Earthquake, Tsunami, Volcano Erruption</label>
						<div class="keterangan">Dok. Khusus: Surat Keterangan dari BMG</div>
						<input type="radio" checked="<?=$rast->flag_other?>" name="cod" value="oth" id="oth" /> <label for="oth">Other Losses (Lainnya..)</label>
						<div class="keterangan">Dok. Khusus: Surat Keterangan dari Manajer Telkomsel</div>
						<input type="radio" checked="<?=$rast->flag_fire?>" name="cod" value="fre" id="fre" /> <label for="fre">Fire (Terbakar/ Kebakaran)</label>
						<div class="keterangan">Dok. Khusus: Surat Keterangan PMK atau Surat Keterangan dari Manajer Telkomsel</div>
						<input type="radio" checked="<?=$rast->flag_third_party?>" name="cod" value="trp" id="trp" /> <label for="trp">Third Party (Tuntutan Pihak ketiga)</label>
						<div class="keterangan">Dok. Khusus: Surat tuntutan dari pihak ketiga</div>				
						<?php 
						}
						else
						{ ?> <!--------------- EDIT INI!!!--------------------------------------------->
						<?=$rast->sebab=='nds'?'Natural Dissaster (Bencana Alam)':''?>
						<?=$rast->sebab=='riot'?'Riots/ Strikes, Malicious Damage (Kerusuhan)':''?>
						<?=$rast->sebab=='thf'?'Theft (Pencurian)':''?>
						<?=$rast->sebab=='lit'?'Lightning (Petir)':''?>
						<?=$rast->sebab=='etve'?'Earthquake, Tsunami, Volcano Erruption':''?>
						<?=$rast->sebab=='fire'?'Fire (Terbakar/ Kebakaran)':''?>
						<?=$rast->sebab=='3p'?'Third Party (Tuntutan Pihak ketiga)':''?>
						<?=$rast->sebab=='oth'?'Other Losses (Lainnya..)':''?>
						<?php 
						} ?>
					</td>
				</tr>
                
                <!-------------------------- STATUS 'SURVEY' 'INVOICE' AND 'PAYMENT' ??? ------------------------>
			
				<!----------------------------------------------------------------------------------------------->
                 
                 
                <tr>  
		      <td colspan="2"> 
			  <strong>Rincian Kerusakan</strong><br />
               <?php if($mode=='revisi')
						{ ?>
              
           <select name="item" id="item_merktype" onchange="toogleAssetCtgor2()">
							<option value="">-Item-</option>							
                            <?php $res_item = $db->get_results("SELECT distinct(item1) FROM ast_detail2 GROUP BY item1 ORDER BY item1 ASC"); 
							      foreach($res_item as $item): ?>  
                                  
	    <option value="<?=$item->item1?>" <?=($item->item1==$_POST['item']?'selected="selected"':'')?>> <?=$item->item1?> </option>
                               
							<?php endforeach;
							?>				
					</select>
                    <?php }
					else
						{ ?>
						<?php echo $rast->merk ."</br>";
						echo $rast->type ."</br>";
						?>
						<?php } ?>
              </td>
              </tr>
		
                
                <tr class="odd"><td colspan="2">&nbsp </td> </tr>
				
                <tr class="even" valign="top">
                <?php if ($mode=='revisi')
				{ ?>
					<th>Dokumen-dokumen</th>
					<td> 
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr class="even" valign="top">
								<td><div>Dokumen #1</div>Surat tuntutan/ pengajuan klaim dari tertanggung
								<div class="keterangan"><?=$rast->doc1==''?'-belum ada-':$rast->doc1?></div>
								</td>
								<td><input type="file" name="dok1" /></td>
							</tr>
							<tr class="odd">
								<td><div>Dokumen #2</div>Berita acara kerusakan atau kehilangan Aktiva Tetap (Rincian Objek yang Mengalami Kerugian)
								<div class="keterangan"><?=$rast->doc2==''?'-belum ada-':$rast->doc2?></div></td>
								<td><input type="file" name="dok2" /></td>
							</tr>
							<tr class="even">
								<td><div>Dokumen #3</div>Kronologi kejadian/ kerugian
								<div class="keterangan"><?=$rast->doc3==''?'-belum ada-':$rast->doc3?></div></td>
								<td><input type="file" name="dok3" /></td>
							</tr>
							<tr class="odd">
								<td><div>Dokumen #4</div>PO/ Kontrak/ Price list/ Kwitansi perbaikan/ pembelian perangkat/ Dokumen lain yang menjelaskan nilai kerugian 
								<div class="keterangan"><?=$rast->doc4==''?'-belum ada-':$rast->doc4?></div></td>
								<td><input type="file" name="dok4" /></td>
							</tr>
                            <tr class="even">
								<td><div>Dokumen #5</div>Foto Objek Kerugian
								<div class="keterangan"><?=$rast->doc5==''?'-belum ada-':$rast->doc5?></div></td>
								<td><input type="file" name="dok5" /></td>
							</tr>
                            <tr class="odd">
								<td>Dokumen Khusus
								<div class="keterangan"><?=$rast->dock==''?'-belum ada-':$rast->dock?></div></td>
								<td><input type="file" name="dokk" /></td>
							</tr>
						</table>
					</td>
				</tr>
                <?php
				} 
				else 
				{?>
                <tr>
                <td><strong> Dokumen - dokumen :</strong></td></tr>
                <tr class="even">
					<td>Dokumen 1
					<div class="keterangan">Surat tuntutan/ pengajuan klaim dari tertanggung</div></td>
					<td><a href="docs/ast/<?=$rast->doc1?>"><?=$rast->doc1?></a></td>
				</tr>
				<tr class="even">
					<td>Dokumen 2
					<div class="keterangan">Berita acara kerusakan atau kehilangan Aktiva Tetap (Rincian Objek yang Mengalami Kerugian)</div>
					</td>
					<td><a href="docs/ast/<?=$rast->doc2?>"><?=$rast->doc2?></a></td>
				</tr>
				<tr class="even">
					<td>Dokumen 3
					<div class="keterangan">Kronologi kejadian/ kerugian</div></td>
					<td><a href="docs/ast/<?=$rast->doc3?>"><?=$rast->doc3?></a></td>
				</tr>
				<tr class="even">
					<td>Dokumen 4
					<div class="keterangan">PO/ Kontrak/ Price list/ Kwitansi perbaikan/ pembelian perangkat/ Dokumen lain yang menjelaskan nilai kerugian </div>
					</td>
					<td><a href="docs/ast/<?=$rast->doc4?>"><?=$rast->doc4?></a></td>
				</tr>
				<tr class="even">
					<td>Dokumen 5
					<div class="keterangan">Foto Objek Kerugian</div>
					</td>
					<td><a href="docs/ast/<?=$rast->doc5?>"><?=$rast->doc5?></a></td>
				</tr>
				<tr class="even">
					<td>Dokumen Khusus</td>
					<td><a href="docs/ast/<?=$rast->dockhusus?>"><?=$rast->dockhusus?></a></td>
				</tr>
				
				<tr class="even">
					<td>Dibuat pada</td>
					<td><?=date("j F Y, H:i",strtotime($rast->created_at))?></td>
				</tr>
				<tr class="even">
					<td>Diupdate pada</td>
					<td><?=date("j F Y, H:i",strtotime($rast->updated_at))?></td>
				</tr>
                <?php } ?>

                
<!----------------------------------------------- SURVEY ??? UNEDITED ----------------------------------------------->
				<?php if($mode=='survey')
				{ ?>
					<tr class="odd">
						<td>Tanggal Survey						</td>
						<td>
							<input type="text" name="survey_date_show" id="survey_date_show" style="width:200px" value="<?=$_POST['survey_date_show']?>" />
							<input type="hidden" name="survey_date" id="survey_date" value="<?=$_POST['survey_date']?>" />
							<div class="keterangan"><strong>Diisi tanggal perintah survey</strong></div>
						</td>
					</tr>
				<?php 
				} ?>
                
				<?php if($rcgl->file_surat_tuntutan=='' && $mode=='survey')
				{ ?>
					<tr class="odd">
						<td>File Surat Tuntutan</td>
						<td><input type="file" name="tuntutan" />
						<div class="keterangan"><strong>*) Lampirkan file surat tuntutan</strong></div>
						</td>
					</tr>
				<?php 
				} ?>
				<?php //if($rcgl->status=='SURVEY' || $rcgl->status=='SUBMITTED'){ ?>				
				
<!----------------------------------------------- PAYMENT ??? UNEDITED ----------------------------------------------->
				<?php if($mode=='payment')
				{ ?>
					<tr class="even">
						<td>Nilai BoQ yang telah disetujui<br />oleh Telkomsel</td>
						<td><input type="text" name="kerugian_survey"></td>
					</tr>
				<?php 
				} ?>
				
				<?php if(($mode=='payment') && $rcgl->file_boq=='')
				{ ?>
					<tr class="odd">
						<td>File BoQ</td>
						<td><input type="file" name="s_boq" />
						<div class="keterangan"><strong>*) Lampirkan BoQ yang telah disetujui Telkomsel </strong></div></td>
					</tr>
				<?php 
				} ?>
                
				<?php //if($rcgl->status=='SURVEY'){ ?>
				<?php if($mode=='payment')
				{ ?>
					<tr class="even">
						<td>Tanggal Payment</td>
						<td>
						<input type="text" id="payment_show" name="payment_show" class="narr" value="<?=$_POST['payment_show']?>" />
						<input type="hidden" name="payment" id="payment" value="<?=$_POST['payment']?>" />
						<div class="keterangan"><strong>*) Diisi tanggal payment kepada warga</strong></div>
						</td>
					</tr>
					<tr class="odd">
						<td>Lampiran</td>
						<td>
							<table>
								<tr>
									<td>Foto</td>
									<td><input type="file" name="foto" /></td>
								</tr>
								<tr>
									<td>Kwitansi</td>
									<td><input type="file" name="kwi" /></td>
								</tr>
								<tr>
									<td>SPS</td>
									<td><input type="file" name="sps" /></td>
								</tr>
								<tr>
									<td>Kronologis</td>
									<td><input type="file" name="kro" /></td>
								</tr>
							</table>
						</td>
					</tr>				
				<?php 
				} ?>
                
<!----------------------------------------------INVOICE ??? UNEDITED ---------------------------------------------->									
				<?php //if($rcgl->status=='PAYMENT'){ ?>
				<?php if($mode=='invoice')
				{ ?>
					<tr class="odd">
						<td>Tanggal Invoice
						</td>
						<td>
						<input type="text" name="invoice_show" id="invoice_show" value="<?=$_POST['invoice_show']?>" class="narr" />
						<input type="hidden" name="invoice" id="invoice" value="<?=$_POST['invoice']?>" />
						<div class="keterangan"><strong>*) Diisi tanggal pengiriman Invoice ke perusahaan Asuransi</strong></div>
						</td>
					</tr>
					<tr class="even" valign="top">
						<td>Nilai Invoice</td>
						<td>
						<input type="text" name="besaran_invoice" value="<?=$_POST['besaran_invoice']?>" class="narr" />
						<div class="keterangan"><strong>*) Besaran nilai invoice adalah sejumlah total Nilai Invoice yang ditagihkan oleh vendor ke perusahaan asuransi.</div>
						</td>
					</tr>
				<?php 
				} ?>
<!----------------------------------------------------------------------------------------------------------------->                
			<tr class='odd'> <td td colspan=2> <hr size="5" noshade /></td> </tr>
            <tr colspan="2" class="even">
					<td>&nbsp;</td>
					<td style="text-align:right">
						<?php if($_GET['m']=='review')
						{ ?>
							<input type="button" onclick="document.location.href='revisiast.php?revisi=<?=$_GET['revisi']?>'" value="Edit Kembali" />
							<input type="button" onclick="document.location.href='user.php'" value="Submit Laporan AST" />
						<?php					
						}
						else
						{ ?>
						<input style="cursor:pointer;" type="button" onclick="document.location.href='laporan_ast.php'" value="Kembali" />
						<input style="cursor:pointer;" type="button" value="Print" onclick="window.open ('printDetailAST.php?ast=<?=$rast->ast_id?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
						<?php if($caption!='')
						{ ?>
                        
                        <!-----------------------------------------------EDIT----------------------------------------------------------->
						<?php if($mode=='approval') 
						{ ?>
							<input type="hidden" value="0" name="isReject" /> 
                            <input onclick="isReject.value=1" type="submit" value="REJECT Laporan AST" /> 
						<?php 
						} ?>
						<!-------------------------------------------------------------------------------------------------------------->
                        	<input type="submit" value="<?=$caption?>" />
						<?php } ?>
						<?php } ?>
					</td>
				</tr>				
			</table>
			</form>
			<?php 
			} ?>
		</td>
	</tr>
</table>
<?php include "footer.php"?>