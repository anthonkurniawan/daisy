<?php
require 'init.php';
require 'priviledges.php';
include "headercgl.php";

$err = array();
$rcgl = $db->get_row("SELECT * FROM `cgl` WHERE cgl_id='".$_GET['revisi']."'");

switch($rcgl->status){
	case 'UNAPPROVED':
		$mode = $user->role=='mgrr'?'approval':'revisi';
		$caption = $user->role=='mgrr'?'Approve Laporan CGL':'Submit Revisi CGL';
	break;
	case 'REJECTED':
		$mode = $user->role=='spvr'?'revisi':'view';
		$caption = $user->role=='spvr'?'Submit Revisi CGL':'';
	break;
	case 'APPROVED':
		$mode = $user->role=='stfp'?'submit':'view';
		$caption = $user->role=='stfp'?'Submit Klaim CGL':'';
	break;
	case 'SUBMITTED':
		$mode = $user->role=='spvr'?'survey':'view';
		$caption = $user->role=='spvr'?'Submit Survey CGL':'';
	break;
	case 'SURVEY':
		$mode = $user->role=='spvr'?'payment':'view';
		$caption = $user->role=='spvr'?'Set Payment Klaim CGL':'';
	break;
	case 'PAYMENT':
		$mode = $user->role=='spvr'?'invoice':'view';
		$caption = $user->role=='spvr'?'Submit Invoice Klaim CGL':'';
	break;
	case 'INVOICE':
		$mode = $user->role=='gmp'?'settlement':'view';
		$caption = $user->role=='spvp'?'Set Klaim CGL, SETTLED':'';
	break;
	case 'SETTLED':
	case 'CASECLOSED':
	default:
	$mode = 'view';
	break;
}

if($_GET['m']=='review') $mode = 'view';

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
	$db->query("UPDATE cgl SET `status`='{$newStatus}',{$set} WHERE cgl_id='".$_POST['i']."'");		
	$db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) 
	VALUES 
	('cgl','".$_POST['i']."','".$rcgl->no_laporan."','".$user->user_id."','{$newStatus}',NOW())");	
	$upd = 1;
	
	//----------------------- send email
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	
		
		$raw 			= file_get_contents('cgl_approved.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($rcgl->no_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",
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
    
// send sms
$from="DAISY";
$phone=$db->get_row("SELECT phone FROM user WHERE `role`='spvp'");
$text="No.Lap= $rcgl->no_laporan, Side ID= $r->st_site_id, Site Name= $r->st_name, Status= $newStatus";

$user="daisy";
$pass="daisy123";
$to = $phone->phone;

$url = 'http://10.2.224.148:9001/smsgw_acl/submit.jsp';

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $url);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS,  "user=$user&pass=$pass&from=$from&to=$to&text=$text");
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
curl_setopt($curlHandle, CURLOPT_POST, 1);
curl_exec($curlHandle);
curl_close($curlHandle);
//==
}		
//--------------------------------------------------------------------------------------------------------


if($_POST && $mode=='payment')  //--------------------- PAYMENT
{
	if($_POST['payment']=='') $err[]="tanggal payment";

	if(strtotime($_POST['payment']) < strtotime($rcgl->survey_date)) $err[]="Tanggal payment tidak boleh kurang dari tanggal survey";	
	
	if(empty($err)):
		
		$kode_laporan = str_pad($rcgl->no_laporan,2,"0",STR_PAD_LEFT).'/'.$user->inisial.date('d').'/'.$user->regional.'/CGL/'.date("m").'/'.date("y");	
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
		
		$query = "SELECT st_site_id,st_name,st_region,st_longitude,st_latitude,st_address  FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		
		$query2 = "SELECT * FROM `cgl_vendor` WHERE `id_cglv` = '{$rcgl->id_cglv}'";
		$r2 = $db->get_row($query2);	
		
		$raw 			= file_get_contents('cgl_payment.email.htm');
		$pattern 		= array('%%V%%','%%VHP%%','%%VTELP%%','%%VNAMA%%','%%PAYMENTDATE%%','%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%',
							    '%%NAMASITE%%','%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%','%%ESTIMASI%%','%%CPNAMA%%','%%CPTELP%%',
                  '%%CPHP%%','%%NAMA%%','%%JABATAN%%');
		$replaceWith 	= array($r2->nama_vendor,$rcgl->vendor_hp,$rcgl->vendor_telp,$rcgl->vendor_pic,$rcgl->payment_date,$rcgl->no_laporan,
								date("Y"),date("l/ j F Y",strtotime($_POST['tgl_kejadian'])),date("l/ j F Y",strtotime($_POST['tgl_tuntutan'])),
								'['.$r->st_site_id.']'.$r->st_name,$r->st_address,$r->st_region,$r->st_latitude.'/'.$r->st_longitude,$sebab.'.'.$rcgl->oth_sebab,
								$_POST['rincian'],$_POST['estimasi'],$_POST['cp_nama'],$_POST['cp_telp'],$_POST['cp_hp'],
								$user->nama,$user->posisi);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
	$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `regional`='".$user->regional."'");
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
		
		// send emails
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	
		
		$raw 			= file_get_contents('cgl_survey.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%',
    '%%NAMASITE%%','%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",strtotime($rcgl->tgl_tuntutan)),
    '['.$r->st_site_id.']'.$r->st_name,$rcgl->st_address,$r->st_region,$rcgl->st_latitude.'/'.$rcgl->st_longitude,
    $sebab.'.'.$rcgl->oth_sebab,$rcgl->rincian,
    $rcgl->cp_nama,$rcgl->cp_telp,$rcgl->cp_hp,$user->nama,$user->posisi,
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
		 //======================= SMS ======================================
$from="DAISY";
$phone=$db->get_row("SELECT phone FROM user WHERE `role`='mgrr'");
$text="No.Lap= $rcgl->no_laporan, Side ID= $r->st_site_id, Site Name= $r->st_name, Status= $newStatus";

$user="daisy";
$pass="daisy123";
$to = $phone->phone;

$url = 'http://10.2.224.148:9001/smsgw_acl/submit.jsp';

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $url);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS,  "user=$user&pass=$pass&from=$from&to=$to&text=$text");
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
curl_setopt($curlHandle, CURLOPT_POST, 1);
curl_exec($curlHandle);
curl_close($curlHandle);
//=========================================================================================
		
		$paymentSet = 1;
	endif;
}

if($_POST && $mode=='invoice')  //------------------------ INVOICE
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
		$replaceWith 	= array($rcgl->no_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",
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
  
  		 //======================= SMS ======================================
$from="DAISY";
$phone=$db->get_row("SELECT phone FROM user WHERE `role`='mgrr'");
$text="No.Lap= $rcgl->no_laporan, Side ID= $r->st_site_id, Site Name= $r->st_name, Status= $newStatus";

$user="daisy";
$pass="daisy123";
$to = $phone->phone;

$url = 'http://10.2.224.148:9001/smsgw_acl/submit.jsp';

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $url);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS,  "user=$user&pass=$pass&from=$from&to=$to&text=$text");
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
curl_setopt($curlHandle, CURLOPT_POST, 1);
curl_exec($curlHandle);
curl_close($curlHandle);
//=========================================================================================
}


if($_POST && $mode=='survey')  //---------------------- SURVEY
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
		
		// send emails
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	
		
		$raw 			= file_get_contents('cgl_survey.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($rcgl->no_laporan,date("Y"),date("l/ j F Y",strtotime($rcgl->tgl_kejadian)),date("l/ j F Y",
								strtotime($rcgl->tgl_tuntutan)),'['.$r->st_site_id.']'.$r->st_name,$rcgl->st_address,
								$r->st_region,$rcgl->st_latitude.'/'.$rcgl->st_longitude,$sebab.'.'.$rcgl->oth_sebab,
								$rcgl->rincian,$rcgl->cp_nama,$rcgl->cp_telp,$rcgl->cp_hp,$user->nama,$user->posisi,
						        $r2->nama_vendor, $rcgl->vendor_pic,$rcgl->vendor_telp,$rcgl->vendor_hp);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
		$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `role`='spvp'");
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
  		 //======================= SMS ======================================
$from="DAISY";
$phone=$db->get_row("SELECT phone FROM user WHERE `role`='mgrr'");
$text="No.Lap= $rcgl->no_laporan, Side ID= $r->st_site_id, Site Name= $r->st_name, Status= $newStatus";

$user="daisy";
$pass="daisy123";
$to = $phone->phone;

$url = 'http://10.2.224.148:9001/smsgw_acl/submit.jsp';

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $url);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS,  "user=$user&pass=$pass&from=$from&to=$to&text=$text");
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
curl_setopt($curlHandle, CURLOPT_POST, 1);
curl_exec($curlHandle);
curl_close($curlHandle);
//=========================================================================================
}

if($_POST && $mode=='revisi')  //-------------------- REVISI
{
	if($_POST['tgl_kejadian']=='') $err[]="tanggal kejadian";
	if($_POST['tgl_tuntutan']=='') $err[]="tanggal diketahui Telkomsel";
	if($_POST['lokasi']=='') $err[]="lokasi kejadian";
	if($_POST['sebab']=='') $err[]="penyebab kerugian";
	if($_POST['rincian']=='') $err[]="rincian kerugian";
	//if($_POST['st_address']=='') $err[]="alamat site";
	//if($_POST['st_longitude']=='') $err[]="Posisi longitude site";
	//if($_POST['st_latitude']=='') $err[]="Posisi latitude site";	
	//if($_POST['estimasi']=='') $err[]="estimasi kerugian";
	//if(!is_numeric($_POST['estimasi'])) $err[]="data estimasi kerugian hanya dapat berupa angka";
	if($_POST['cp_nama']=='') $err[]="nama contact person";
	if($_POST['cp_telp']=='') $err[]="telepon  contact person";
	if($_POST['cp_hp']=='') $err[]="nomor hp contact person";
	if($_POST['vendor_pic']=='') $err[]="nama pic vendor";
	if($_POST['vendor_telp']=='') $err[]="telepon pic vendor";
	//if($_POST['vendor_hp']=='') $err[]="nomor hp pic vendor";
	
	if(empty($err)):	
		switch($_POST['sebab'])
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
		
		$SQL = "UPDATE cgl 
			SET 
			".($_POST['tgl_kejadian']<>''?"`tgl_kejadian`='".$_POST['tgl_kejadian']."',":'')."
			".($_POST['tgl_tuntutan']<>''?"`tgl_tuntutan`='".$_POST['tgl_tuntutan']."',":'')."
			`st_site_id`='".$_POST['lokasi']."',
			`sebab`='".$sebab."',
			`rincian`='".$_POST['rincian']."',
			`estimasi`='".$_POST['estimasi']."',
			`cp_nama`='".$_POST['cp_nama']."',
			`cp_telp`='".$_POST['cp_telp']."',
			`cp_hp`='".$_POST['cp_hp']."',
			`vendor_pic`='".$_POST['vendor_pic']."',
			`vendor_telp`='".$_POST['vendor_telp']."',
			`vendor_hp`='".$_POST['vendor_hp']."',
			`status`='UNAPPROVED',
			`id_cglv`='".$_POST['cglv']."',
			`updated_at`=NOW()
			WHERE cgl_id='".$_POST['i']."'";
		
		if($rcgl->status=='REJECTED') 
		$db->query("INSERT INTO `status_log` (`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
					('cgl','".$rcgl->cgl_id."','".$rcgl->no_laporan."','".$user->user_id."','UNAPPROVED',NOW())");
		
		$db->query($SQL);	
		$inscgl = 1;
		
		$query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$_POST['lokasi']}'";
		$r = $db->get_row($query);	
		$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$_POST['cglv']}'");	
		
		$raw 			= file_get_contents('cgl_unapproved.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($rcgl->no_laporan,date("Y"),date("l/ j F Y",strtotime($_POST['tgl_kejadian'])),date("l/ j F Y",strtotime($_POST['tgl_tuntutan'])),'['.$r->st_site_id.']'.$r->st_name,
						$_POST['st_address'],$r->st_region,$_POST['st_latitude'].'/'.$_POST['st_longitude'],$sebab.'.'.$_POST['oth_sebab'],$_POST['rincian'],
						$_POST['cp_nama'],$_POST['cp_telp'],$_POST['cp_hp'],$user->nama,$user->posisi,
						$r2->nama_vendor, $_POST['vendor_pic'],$_POST['vendor_telp'],$_POST['vendor_hp']
		);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		
		//get recipients
		$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE `regional`='".$user->regional."' AND `role`='mgrr'");
		if(!empty($recipients))
    {
			foreach($recipients as $recipient){
				if($recipient->email2<>''){
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
  
  		 //======================= SMS ======================================
$from="DAISY";
$phone=$db->get_row("SELECT phone FROM user WHERE `role`='mgrr'");
$text="No.Lap= $rcgl->no_laporan, Side ID= $r->st_site_id, Site Name= $r->st_name, Status= $newStatus";

$user="daisy";
$pass="daisy123";
$to = $phone->phone;

$url = 'http://10.2.224.148:9001/smsgw_acl/submit.jsp';

$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $url);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS,  "user=$user&pass=$pass&from=$from&to=$to&text=$text");
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
curl_setopt($curlHandle, CURLOPT_POST, 1);
curl_exec($curlHandle);
curl_close($curlHandle);
//=========================================================================================
}
	
?>
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:250px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menu.php" ?>
			</ul>
		</td>
		<td>
			<?php if($invoiceSet==1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Invoice Laporan CGL berhasil di input!<br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL &raquo;</a></p>
			</div>			
			<?php exit(); }
			if($paymentSet==1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Payment Laporan CGL berhasil di input!<br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL &raquo;</a></p>
			</div>			
			<?php exit();}
			
			
			if($upd==1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Klaim CGL berhasil di <?=$newStatus?>! <br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL &raquo;</a></p>
			</div>
			<?php exit();}
			
			if($issurvey===1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Survey CGL berhasil di submit! <br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL &raquo;</a></p>
			</div>
			<?php exit();}
			
			if($inscgl===1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Revisi laporan CGL berhasil di submit! <br /><a href="laporan_cgl.php">Kembali ke halaman laporan CGL &raquo;</a></p>
			</div>
			<?php }else{ ?>
			<h3>Revisi Laporan CGL [ <?=$rcgl->no_laporan?> ]</h3>
			<?php if($rcgl->status=='SUBMITTED')
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Proses survey tidak bisa berjalan sebelum melampirkan:<br /> <strong>- Surat Tuntutan</strong>.</p>
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
			<input type="hidden" name="i" value="<?=$rcgl->cgl_id?>" />
				
			<table id="fcgl">
				<tr class="even">
					<td><strong>Hari / tanggal kejadian</strong></td>
					<td>
						<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
						<?php if($mode=='revisi')
						{ ?>
						<input type="text" name="tgl_kejadian_show" id="tgl_kejadian_show" value="<?=date('l/ j F Y',
						 strtotime($rcgl->tgl_kejadian))?>" class="narr" />
                        <input type="hidden" name="tgl_kejadian" id="tgl_kejadian" value="<?=$rcgl->tgl_kejadian?>" />
						<div class="keterangan">[ctrl+panah]:untuk pindah tanggal, [pageUp/pageDown]:untuk pindah bulan, [Enter]:accept</div>
						<?php 
						}
						else
						{ ?>
						<?=date("l, d F Y",strtotime($rcgl->tgl_kejadian)) ?>
						<?php 
						} ?>
					</td>
				</tr><tr class="odd">
					<td><strong>Hari / tanggal diketahui Telkomsel</strong></td>
					<td>
					<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
					<?php if($mode=='revisi')
					{ ?>
					<input type="text" name="tgl_tuntutan_show" id="tgl_tuntutan_show" class="narr" value="<?=date('l/ j F Y',                     strtotime($rcgl->tgl_tuntutan))?>"  />
                    <input type="hidden" name="tgl_tuntutan" id="tgl_tuntutan" value="<?=$rcgl->tgl_kejadian?>" />
					<div class="keterangan">[ctrl+panah]:untuk pindah tanggal, [pageUp/pageDown]:untuk pindah bulan, [Enter]:accept</div>
					<?php 
					}
					else
					{ ?>
					<?=date("l, d F Y",strtotime($rcgl->tgl_tuntutan)) ?>
					<?php } ?>
					</td>
				</tr>
				<tr class="even">
					<td><strong>Tempat / lokasi kerugian</strong></td>
					<td>
					<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
					<?php if($mode=='revisi')
					{ ?>
							<select name="lokasi" id="lokasite">
							<option value="">-Pilih Site ID-</option>
							<?php $resSite = $db->get_results("SELECT st_site_id,st_name FROM `site` WHERE kode_region='".$user->regional."' GROUP BY st_site_id ORDER BY st_site_id ASC"); 
							foreach($resSite as $site): ?>
							<option <?=($site->st_site_id==$rcgl->st_site_id?'selected="selected"':'')?> value="<?=$site->st_site_id?>"><?=$site->st_site_id?> / <?=$site->st_name?></option>
							<?php endforeach;
							?>							
							<script>
								$("#lokasite").ready(function(){
									$('#siteDetail').load('./siteDetailCGL.php?a=<?=$rcgl->st_address?>&lat=<?=$rcgl->st_latitude?>&long=<?=$rcgl->st_longitude?>&siteId=<?=$rcgl->st_site_id?>&c=<?=$rcgl->catatan ?>');
								})
                             </script>
						</select>
					
					<?php 
					}					
					
					$r = $db->get_row("SELECT * FROM `site` WHERE st_site_id='".$rcgl->st_site_id."'"); 
					if($mode=='view') echo $rcgl->st_site_id.' / '.$r->st_name;							
					?>	
					</td>
				</tr>
				<tr class="odd" valign="top">
					<td><strong>Detail lokasi kerugian</strong></td>
					<td><div id="siteDetail">
					
<table width="100%">
	<tr class="odd">
		<td>Site ID</td>
		<td><?=$r->st_site_id?></td>
	</tr>
	<tr class="even">
		<td>Site Name</td>
		<td><?=$r->st_name?></td>
	</tr>
	<tr class="odd">
		<td>Region</td>
		<td><?=$r->st_region?></td>
	</tr>
	<tr class="even">
		<td nowrap="nowrap">Longitude</td>
		<td><?=$rcgl->st_longitude?></td>
	</tr>
	<tr class="odd">
		<td nowrap="nowrap">Latitude</td>
		<td><?=$rcgl->st_latitude?></td>
	</tr>
	<tr valign="top" class="even">
		<td>Alamat Site</td>
		<td><?=$rcgl->st_address?></td>
	</tr>
</table>
					</div></td>
				</tr>
				<tr class="even" valign="top">
					<td><strong>Penyebab kerugian</strong></td>
					<td>
						<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
						<?php if($mode=='revisi')
						{ ?>
						<select name="sebab" onchange="cekOth(this.form)">
							<option <?=("lit"==$_POST['sebab']?'selected="selected"':'')?> value="lit"> CGL Imbas Petir</option>
							<option <?=("oth"==$_POST['sebab']?'selected="selected"':'')?> value="oth"> Other Losses (Lainnya..)</option>
						</select>
					<br />
                    <input type="text" name="oth_sebab" value="<?=$rcgl->other_sebab?>" /> *
						<div class="keterangan">*) Diisi jika penyebab kerugian: Lainnya.. </div>
						<?php 
						}
						else
						{ ?>
						<?=$rcgl->sebab?>
						<?php } ?>
					</td>
				</tr>
				<tr valign="top"  class="odd">
					<td><strong>Rincian kerusakan</strong></td>
					<td>
						<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
						<?php if($mode=='revisi')
						{ ?>
						<textarea name="rincian" style="width:320px;"><?=$rcgl->rincian?></textarea>				
						<?php 
						}
						else
						{ ?>
						<?=$rcgl->rincian?>
						<?php } ?>
					</td>
				</tr>
				<?php if($rcgl->status=='SURVEY' || $rcgl->status=='INVOICE' || $rcgl->status=='PAYMENT' )
				{ ?>
				<tr valign="top"  class="even">
					<td><strong>Estimasi kerugian</strong></td>
					<td>
						<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
						<?php if($mode=='revisi'){ ?>
						<input type="text" name="estimasi" value="<?=$rcgl->kerugian_survey?>" />				
						<?php }else{ ?>
						<?=$rcgl->kerugian_survey ?>
						<?php 
						} ?>
					</td>
				</tr>
				<?php 
				} ?>
				
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
								<input type="text" style="width:220px" name="cp_nama" value="<?=$rcgl->cp_nama?>" /></td>
								<?php 
								}
								else
								{ ?>
								<?=$rcgl->cp_nama ?>
								<?php 
								} ?>
							</tr>
							<tr>
								<td>No telepon</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi'){ ?>
								<input type="text" style="width:220px" name="cp_telp" value="<?=$rcgl->cp_telp?>" />
								<?php 
								}
								else
								{ ?>
								<?=$rcgl->cp_telp ?>
								<?php 
								} ?>
								</td>
							</tr>
							<tr>
								<td>No HP</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi')
								{ ?>
								<input type="text" style="width:220px" name="cp_hp" value="<?=$rcgl->cp_hp?>" />
								<?php 
								}
								else
								{ ?>
								<?=$rcgl->cp_hp ?>
								<?php 
								} ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr valign="top"  class="odd">
					<td><strong>Vendor Pelaksana</strong></td>
					<td>
						<table>
							<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
							<?php if($mode=='revisi')
							{ ?>
							<select name="cglv" id="cglv">
								<?php $res= $db->get_results("SELECT * FROM `cgl_vendor` WHERE kode_regional='".$user->regional."' ORDER BY nama_vendor ASC"); 
								foreach($res as $r): ?>
								<option <?=($r->id_cglv==$rcgl->id_cglv?'selected="selected"':'')?> value="<?=$r->id_cglv?>"><?=$r->nama_vendor ?></option>
								<?php endforeach;
								?>							
							</select>
							<?php 
							}
							else
							{ 
								$resVendor = $db->get_row("SELECT * FROM cgl_vendor WHERE `id_cglv`='".$rcgl->id_cglv."'"); ?>
								<strong><?=$resVendor->nama_vendor;?></strong>
							<?php 
							} ?>
							
                            <tr>
								<td>Nama</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi')
								{ ?>
								<input type="text" style="width:220px" name="vendor_pic" value="<?=$rcgl->vendor_pic?>" /></td>
								<?php }else{ ?>
								<?=$rcgl->vendor_pic ?>
								<?php } ?>
							</tr>
							<tr>
								<td>No telepon</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi')
								{ ?>
								<input type="text" style="width:220px" name="vendor_telp" value="<?=$rcgl->vendor_telp?>" />
								<?php 
								}
								else
								{ ?>
								<?=$rcgl->vendor_telp ?>
								<?php 
								} ?>
								</td>
							</tr>
							<tr>
								<td>No HP</td>
								<td>: 
								<?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){ ?>
								<?php if($mode=='revisi')
								{ ?>
								<input type="text" style="width:220px" name="vendor_hp" value="<?=$rcgl->vendor_hp?>" />
								<?php 
								}
								else
								{ ?>
								<?=$rcgl->vendor_hp ?>
								<?php } ?>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				
				<?php //if($rcgl->status=='SUBMITTED'){ ?>
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
				<?php } ?>
				<?php //if($rcgl->file_surat_tuntutan=='' && $rcgl->status=='SUBMITTED'){ ?>
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
				<?php if($mode=='payment')
				{ ?>
					<tr class="even">
						<td>Nilai BoQ yang telah disetujui<br />oleh Telkomsel</td>
						<td><input type="text" name="kerugian_survey"></td>
					</tr>
				<?php 
				} ?>
				
				<?php //if($rcgl->file_boq=='' &&  ($rcgl->status=='SURVEY'||$rcgl->status=='SUBMITTED')){ ?>
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
			<tr colspan="2" class="even">
					<td>&nbsp;</td>
					<td style="text-align:right">
						<?php if($_GET['m']=='review')
						{ ?>
							<input type="button" onclick="document.location.href='revisicgl.php?revisi=<?=$_GET['revisi']?>'" value="Edit Kembali" />
							<input type="button" onclick="document.location.href='user.php'" value="Submit Laporan CGL" />
						<?php					
						}
						else
						{ ?>
						<input style="cursor:pointer;" type="button" onclick="document.location.href='laporan_cgl.php'" value="Kembali" />
						<input style="cursor:pointer;" type="button" value="Print" onclick="window.open ('printDetailCGL.php?cgl=<?=$rcgl->cgl_id?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
						<?php if($caption!='')
						{ ?>
                        
                        <!-----------------------------------------------EDIT----------------------------------------------------------->
						<?php if($mode=='approval') 
						{ ?>
							<input type="hidden" value="0" name="isReject" /> 
                            <input onclick="isReject.value=1" type="submit" value="REJECT Laporan CGL" /> 
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