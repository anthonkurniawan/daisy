<?php
require 'init.php';
require 'priviledges.php';
$err = array();
if($_POST)
{	
	if($_POST['tgl_kejadian']=='') $err[]="tanggal kejadian";
	if($_POST['tgl_tuntutan']=='') $err[]="tanggal diketahui Telkomsel";
	if(strtotime($_POST['tgl_tuntutan']) < strtotime($_POST['tgl_kejadian'])) $err[]="Tanggal diketahui Telkomsel (tanggal_tuntutan) tidak boleh kurang dari tanggal kejadian";	
	if(strtotime($_POST['tgl_tuntutan']) < strtotime("-21 day")) $err[]="Tanggal diketahui Telkomsel (tanggal_tuntutan) tidak boleh lebih dari 21 hari dari hari ini";	
	if($_POST['cp_nama']=='') $err[]="nama contact person";
	if($_POST['cp_telp']=='') $err[]="telepon  contact person";
	if($_POST['cp_hp']=='') $err[]="nomor hp contact person";
	if($_POST['cglv']=='') $err[]="vendor pelaksana";
	if($_POST['vendor_pic']=='') $err[]="nama pic vendor";
	if($_POST['vendor_telp']=='') $err[]="telepon pic vendor";
	if(empty($err)){
		$res = $db->getRow("SELECT no_laporan FROM cgl ORDER BY created_at DESC");
		if($res->no_laporan<>''){
			$aNoLap = explode('/',$res->no_laporan);
			(int) $no_laporan = $aNoLap[0];
			$no_laporan++;
		}else $no_laporan=1;	
		
		/*
		Kode Laporan: laporan: [Nomor]/[Regional]/[Kode Klaim]/[Tanggal]/[Bulan]/[Tahun] 
		[Nomor] : 4 digit 
		[Regional] : 4 digit (Rxxx) 
		[Kode Klaim] : 3 digit (CGL / AST) 
		[Tanggal] : 2 digit (01 � 31) 
		[Bulan] : 3 digit (Jan � Dec) 
		[Tahun] : 2 digit (xx)
		*/
		$kode_laporan = str_pad($no_laporan,2,"0",STR_PAD_LEFT).'/'.$user['regional'].'/CGL/'.date("d").'/'.date("m").'/'.date("y");
		$query = "SELECT st_site_id,st_name,kode_region,st_region FROM `site` WHERE `st_site_id` = '{$_POST['lokasi']}'";
		$r = $db->getRow($query);	
		
		switch($_POST['sebab']){
			case 'lit':$sebab='CGL Imbas Petir';break;
			case 'oth':$sebab='Other Losses (Lainnya..)';break;
		}
		$SQL = "INSERT INTO cgl (
			`no_laporan`,`inisial`,`user_id`,
			`tgl_kejadian`,`tgl_tuntutan`,`st_site_id`,`st_name`,`sebab`,`rincian`,
			`cp_nama`,`cp_telp`,`cp_hp`,`vendor_pic`,`vendor_telp`,`vendor_hp`,
			`created_at`,`updated_at`,`status`,`id_cglv`,
			`kode_region`,`region`,
			`st_address`,`st_longitude`,`st_latitude`,`other_sebab`) VALUES (
			'".$kode_laporan."','".$user['inisial']."','".$user['user_id']."',
			'".$_POST['tgl_kejadian']."','".$_POST['tgl_tuntutan']."','".$_POST['lokasi']."','".$r->st_name."','".$sebab."','".$_POST['rincian']."',
			'".$_POST['cp_nama']."','".$_POST['cp_telp']."','".$_POST['cp_hp']."','".$_POST['vendor_pic']."','".$_POST['vendor_telp']."',		
			'".$_POST['vendor_hp']."',NOW(),NOW(),'UNAPPROVED','".$_POST['cglv']."',
			'".$r->kode_region."','".$r->st_region."',
			'".$_POST['st_address']."','".$_POST['st_longitude']."','".$_POST['st_latitude']."','".$_POST['oth_sebab']."')";
		$db->query($SQL);
		// $cglid = mysql_insert_id();
		$db->query("INSERT INTO `status_log` (`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES ('cgl','".$cglid."','".$kode_laporan."','".$user['user_id']."','UNAPPROVED',NOW())");
		$r2 = $db->getRow("SELECT * FROM cgl_vendor WHERE id_cglv='{$_POST['cglv']}'");	
		
		//SEND EMAIL
		$raw 			= file_get_contents('cgl_unapproved.email.htm');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%','%%TGLDIKETAHUI%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%',
							'%%VENDOR%%','%%PICNAMA%%','%%PICTELP%%','%%PICHP%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($_POST['tgl_kejadian'])),date("l/ j F Y",strtotime($_POST['tgl_tuntutan'])),'['.$r->st_site_id.']'.$r->st_name,
						$_POST['st_address'],$r->st_region,$_POST['st_latitude'].'/'.$_POST['st_longitude'],$sebab.'.'.$_POST['oth_sebab'],$_POST['rincian'],
						$_POST['cp_nama'],$_POST['cp_telp'],$_POST['cp_hp'],$user['nama'],$user['posisi'],
						$r2->nama_vendor, $_POST['vendor_pic'],$_POST['vendor_telp'],$_POST['vendor_hp']
		);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
	
		//get recipients
		$recipients = $db->getArray("SELECT nama,email1 FROM user WHERE `regional`='".$user['regional']."' AND `role`='mgrr'");
		                               
		if(!empty($recipients))
		{
			foreach($recipients as $recipient){
				$to[$recipient->nama]	=	$recipient->email1;
			}	
			require 'initMail.php';
			// sendMail('Klaim CGL ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($_POST['tgl_kejadian'])),$emailBody,$to,$cc,$bcc);
		}
		$inscgl = 1;
	$from="Admin DAISY";
	$phone=$db->getRow("SELECT phone FROM user WHERE `regional`='".$user['regional']."' AND `role`='mgrr'");
	$text1="No.Lap= ".$text->no_laporan.", Side ID= ".$text->st_site_id.", Site Name= ".$text->st_name.", Status= ".$text->status;

$user="daisy";
$pass="daisy123";
//$from = $_POST['from'];
$to = $phone->phone;
$text = $text1;

// TARGET URL
// http://10.2.224.148:9001/smsgw_acl/submit.jsp?user=daisy&pass=daisy123&from=<from>&to=<628xxxxxx>&text=<msg><http://10.2.224.148:9001/smsgw_acl/submit.jsp?user=daisy&pass=daisy123&from=%3cfrom%3e&to=%3c628xxxxxx%3e&text=%3cmsg%3e>
// TEST1
//$url = "http://10.2.224.148:9001/smsgw_acl/submit.jsp?user=daisy&pass=daisy123&from=<from>&to=<628xxxxxx>&text=<msg><http://10.2.224.148:9001/smsgw_acl/submit.jsp?user=daisy&pass=daisy123&from=%3cfrom%3e&to=%3c628xxxxxx%3e&text=%3cmsg%3e>";
// TEST2
$url = "http://10.2.224.148:9001/smsgw_acl/submit.jsp";


$curlHandle = curl_init();
curl_setopt($curlHandle, CURLOPT_URL, $url);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS, "data1=".$nilai1."&amp;data2=".$nilai2."&amp;data3=".$nilai3);
curl_setopt($curlHandle, CURLOPT_POSTFIELDS, "user=".$user."&amp;pass=".$pass."&amp;from=".$from."&amp;to=".$to."&amp;text=".$text);
curl_setopt($curlHandle, CURLOPT_HEADER, 0);
curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
curl_setopt($curlHandle, CURLOPT_POST, 1);
curl_exec($curlHandle);
curl_close($curlHandle);
?>
<!--
<form action="http://10.2.224.148:9001/smsgw_acl/submit.jsp?user=daisy&pass=daisy123&from=<from>&to=<628xxxxxx>&text=<msg>method="post" enctype="application/x-www-form-urlencoded" target="_self">
form: <input name="form" type="text" value="  />
No.tlp: <input name="to" type="text" value="<082110874855>" />
msg: <input name="text" type="text" value="<TEST>" />
<input type="submit" />
</form>
-->
	<?php
	}
}

include "headercgl.php";
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
			<?php if($inscgl===1){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Laporan awal CGL berhasil di submit!<br />Nomor laporan: <?=$kode_laporan?></p>
			</div>
			<?php }else{ ?>
				<h3><center>Laporan Awal CGL</center></h3>
				<?php if(!empty($err)){ ?>
					<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
					<p>Mohon isi/ perbaiki data berikut:
					<ul>
					<?php foreach($err as $e){  echo "<li>".ucfirst($e)."</li>"; } ?>
					</ul>
					</p>
					</div>
				<?php } ?>
            
			<table id="fcgl" width="100%">
             <form method="post" action="" enctype="multipart/form-data"  autocomplete="off">
                <tr class="even">
					<td>Hari / tanggal kejadian</td>
					<td>
                    <input type="text" name="tgl_kejadian_show"  value="<?=$_POST['tgl_kejadian_show']?>" id="tgl_kejadian_show" class="narr" />
                    <input type="hidden" name="tgl_kejadian" id="tgl_kejadian"  value="<?=$_POST['tgl_kejadian']?>" />
					</td>
				</tr>
                
                <tr class="odd">
					<td>Hari / tanggal diketahui Telkomsel</td>
					<td><input type="text" name="tgl_tuntutan_show" value="<?=$_POST['tgl_tuntutan_show']?>" id="tgl_tuntutan_show" class="narr" />
                    <input type="hidden" name="tgl_tuntutan" id="tgl_tuntutan" value=""  value="<?=$_POST['tgl_tuntutan']?>" />
					</td>
				</tr>            
                <tr class="even">
					<td>Tempat / lokasi kerugian</td>
					<td>                 
<select name="lokasi" id="lokasite">                                    
	<option value="">-Pilih Site ID-</option>
	  <?php $resSite = $db->getArray("SELECT st_name,st_site_id FROM `site` WHERE kode_region='".$user['regional']."' GROUP BY st_site_id ORDER BY st_site_id ASC"); 
		 foreach($resSite as $site){ ?> 
        <option value="<?=$site->st_site_id?>" <?=($site->st_site_id==$_POST['lokasi']?'selected="selected"':'')?>> <?=$site->st_site_id?> / <?=$site->st_name?> </option>    <!-- $_POST['lokasi'] = LOKASI SITE --->
		 <?php } ?>                                                         
</select> 
				</tr>
              
				<tr class="" valign="top">
					<td>Detail lokasi kerugian</td>
					<td>
						<div id="siteDetail">
						<?php if($_POST['lokasi']<>'')
						{
							$_GET['siteId']=$_POST['lokasi'];
							include 'siteDetailCGL.php';
						} ?>
						</div>
					</td>
				</tr>
				<tr class="even" valign="top">
					<td><strong>Penyebab kerugian</strong></td>
					<td>
						<script>
							function cekOth(obj){
								if(obj.sebab.value=='oth')
									obj.oth_sebab.disabled=false;
								else
									obj.oth_sebab.disabled=true;
							}
						</script>
						<select name="sebab" onchange="cekOth(this.form)">
							<option <?=("lit"==$_POST['sebab']?'selected="selected"':'')?> value="lit"> CGL Imbas Petir</option>
							<option <?=("oth"==$_POST['sebab']?'selected="selected"':'')?> value="oth"> Other Losses (Lainnya..)</option>
						</select>
					<br /><input type="text" disabled="disabled" name="oth_sebab" value="<?=$_POST['oth_sebab']?>" /> *
						<div class="keterangan">*) Diisi jika penyebab kerugian: Lainnya.. </div>
					</td>
				</tr>
				<tr valign="top"  class="odd">
					<td><strong>Rincian kerusakan</strong></td>
					<td>
						<input type="text" name="rincian" value="<?=$_POST['rincian']?$_POST['rincian']:'Rincian kerusakan warga'?>" style="width:220px" value="Kerugian harta benda warga">				
					</td>
				</tr>
                
				<tr valign="top"  class="odd">
					<td><strong>Contact person</strong></td>
					<td>
						<table>
							<tr class="even">
								<td>Nama</td>
								<td>: <input type="text" style="width:220px" name="cp_nama" value="<?=$_POST['cp_nama']?>" /></td>
							</tr>
							<tr class="odd">
								<td>No telepon</td>
								<td>: <input type="text" style="width:220px" name="cp_telp" value="<?=$_POST['cp_telp']?>" /></td>
							</tr>
							<tr class="even">
								<td>No HP</td>
								<td>: <input type="text" style="width:220px" name="cp_hp" value="<?=$_POST['cp_hp']?>" /></td>
							</tr>
                            
						</table>
					</td>				</tr>

				<tr valign="top" class="odd">
					<td><strong>Vendor Pelaksana</strong></td>
					<td>
						<table>
						<select name="cglv" id="cglv">
							<option value="">-Pilih Vendor Pelaksana-</option>
							<?php $res= $db->getArray("SELECT * FROM `cgl_vendor` WHERE kode_regional='".$user['regional']."' ORDER BY nama_vendor ASC"); 
							foreach($res as $r){ ?>
							<option <?=($r->id_cglv==$_POST['cglv']?'selected="selected"':'')?>  value="<?=$r->id_cglv?>"><?=$r->nama_vendor ?></option>
							<?php } ?>							
						</select>
							<tr class="even">
								<td>PIC</td>
								<td>: <input type="text" style="width:220px" name="vendor_pic" value="<?=$_POST['vendor_pic']?>" /></td>
							</tr>
							<tr class="odd">
								<td>No. telepon</td>
								<td>: <input type="text" style="width:220px" name="vendor_telp" value="<?=$_POST['vendor_telp']?>" /></td>
							<tr class="odd">
								<td>No. HP</td>
								<td>: <input type="text" style="width:220px" name="vendor_hp" value="<?=$_POST['vendor_hp']?>" /></td>
							</tr>
						</table>
					</td>
				</tr>
				<tr class="even">
					<td>&nbsp;</td>
					<td style="text-align:right"><input type="submit" value="Submit laporan CGL" /></td>
				</tr>				
			</table>
			</form>
			<?php } ?>
		</td>
	</tr>
</table>
<?php include "footer.php"; ?>
