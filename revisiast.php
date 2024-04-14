<?php
require 'init.php';
require 'priviledges.php';
include "headerast.php";

$rast = $db->get_row("SELECT * FROM `ast2` WHERE no_laporan='".$_GET['revisi']."'");
$rsat = $db->get_row("SELECT item1,quantity,satuan from `ast_detail2` WHERE no_laporan='".$_GET['revisi']."'");
$rcat = $db->get_row("SELECT * from `category` WHERE item1='".$rsat->item1."'");

if($rast->sebab=='nds') { $sebab='Natural Dissaster (Bencana Alam)';}
elseif ($rast->sebab=='thf') { $sebab= 'Theft (Pencurian)';}
elseif ($rast->sebab=='lit') { $sebab= 'Lightning (Petir)';}
elseif ($rast->sebab=="etv") { $sebab= 'Earthquake, Tsunami, Volcano Erruption';}
elseif ($rast->sebab=='fre') { $sebab= 'Fire (Terbakar/ Kebakaran)';}
elseif ($rast->sebab=='trp') { $sebab= 'Third Party (Tuntutan Pihak ketiga)';}
elseif ($rast->sebab=='rio') { $sebab= 'Riots/ Strikes, Malicious Damage (Kerusuhan)';}
else { $sebab= 'Other Losses (Lainnya..)';}

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
		$mode = $user->role=='spvr'?'revisi':'view';
		$caption = $user->role=='spvr'?'Submit Dokumen AST':'';
	break;
	case 'SETTLED':
	case 'CASECLOSED':
	default:
	$mode = 'view';
	break;
}

//APPROVAL
if($_POST && $mode=='approval') 
{
//-- send emails
	$query = "SELECT * FROM `site` WHERE `st_site_id` = '{$rast->st_site_id}'";
	$r = $db->get_row($query);	
	$raw 	   = file_get_contents('ast_submitted.email.html');
	$pattern   = array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%',': %%TGLLAPOR%%','%%NAMASITE%%',
					   '%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
					   '%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%');
	$replaceWith = array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($rast->tgl_kejadian)),date("l/ j F Y",strtotime($rast->approve_at)),'['.$r->st_site_id.']'.$r->st_name,
						$rast->st_address,$r->st_region,$r->st_latitude.'/'.$r->st_longitude,$sebab,$rast->rincian,
						$rast->pic_region,$rast->telp,$rast->hp,$user->nama,$user->posisi);
	$emailBody = str_replace($pattern, $replaceWith, $raw);
		
	//get recipients
	$recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE (`regional`='".$user->regional."' AND `role`='mgrr') OR role='spvp'");
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
    }
      
	if($_POST['isReject']=='1')
	{
     $err = array();
     if($_POST['note_rej']=='') $err[]="Catatan Untuk Rejected";	
    
     if(empty($err)):
		$newStatus = 'REJECTED';
		$set='`reject_at`=NOW()';		
    $db->query("UPDATE ast2 SET `status`='".$newStatus."',{$set},`note_rej`='".$_POST['note_rej']."',`pic_status`='2' WHERE ast_id='".$_POST['i']."'");	
    
	  require 'initMail.php';
		sendMail('Klaim AST ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($rast->tgl_kejadian)).': REJECTED',$emailBody,$to,$cc,$bcc);		
	  $upd = 1; 
    endif;
  }  
  else
	{
	  $newStatus = 'APPROVED';
	  $set='`approve_at`=NOW()';	
    $db->query("UPDATE ast2 SET `status`='".$newStatus."',{$set} WHERE ast_id='".$_POST['i']."'");	
     
	  require 'initMail.php';
	  sendMail('Klaim AST ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($rast->tgl_kejadian)).': APPROVED',$emailBody,$to,$cc,$bcc);	
    $upd = 1; 	   		
	}	  
}		

//================================================ REVISI EDITED TO AST ==============================================
if($_POST && $mode=='revisi')
{
$kode_laporan = $rast->no_laporan;			
$prefixFile = str_replace('/','',$kode_laporan);
	if($_FILES['doc_tun']['name']<>'') { 
		                                  $d_tun= $prefixFile.'_1_'.basename($_FILES['doc_tun']['name']);
									      $d_tun_tgl="NOW()";} else {$d_tun_tgl="1";}
	if($_FILES['doc_hil']['name']<>'') {
		                                  $d_hil= $prefixFile.'_2_'.basename($_FILES['doc_hil']['name']);
									      $d_hil_tgl="NOW()";} else {$d_hil_tgl="1";}
	if($_FILES['doc_kro']['name']<>'') {
		                                  $d_kro= $prefixFile.'_3_'.basename($_FILES['doc_kro']['name']);
								          $d_kro_tgl="NOW()";} else {$d_kro_tgl="1";}
	if($_FILES['doc_po']['name']<>'') {
		                                 $d_po= $prefixFile.'_4_'.basename($_FILES['doc_po']['name']);
	                                     $d_po_tgl="NOW()";} else {$d_po_tgl="1";}
	if($_FILES['doc_fo']['name']<>'') {
		                                 $d_fo= $prefixFile.'_5_'.basename($_FILES['doc_fo']['name']);
	                                    $d_fo_tgl="NOW()";} else {$d_fo_tgl="1";}
	if($_FILES['doc_rinci']['name']<>'') {
		                                    $d_rinci= $prefixFile.'_6_'.basename($_FILES['doc_rinci']['name']);
	                                     $d_rinci_tgl="NOW()";} else {$d_rinci_tgl="1";}
	if($_FILES['doc_lap']['name']<>'') {
		                                  $d_lap= $prefixFile.'_7_'.basename($_FILES['doc_lap']['name']);
	                                     $d_lap_tgl="NOW()";} else {$d_lap_tgl="1";}
	if($_FILES['doc_pol']['name']<>'') {
		                                  $d_pol= $prefixFile.'_8_'.basename($_FILES['doc_pol']['name']);
	                                     $d_pol_tgl="NOW()";} else {$d_pol_tgl="1";}
    if($_FILES['doc_pmk']['name']<>'') {
		                                  $d_pmk= $prefixFile.'_9_'.basename($_FILES['doc_pmk']['name']);
	                                     $d_pmk_tgl="NOW()";} else {$d_pmk_tgl="1";}
	if($_FILES['doc_bmkg']['name']<>'') {
		                                  $d_bmkg= $prefixFile.'_10_'.basename($_FILES['doc_bmkg']['name']);
	                                    $d_bmkg_tgl="NOW()";} else {$d_bmkg_tgl="1";}
	$uploaddir = 'docs/ast/';
	$uploadfile1 = $uploaddir . $d_tun;
	$uploadfile2 = $uploaddir . $d_hil;
	$uploadfile3 = $uploaddir . $d_kro;
	$uploadfile4 = $uploaddir . $d_po;
	$uploadfile5 = $uploaddir . $d_fo;
	$uploadfile6 = $uploaddir . $d_rinci;
	$uploadfile7 = $uploaddir . $d_lap;
	$uploadfile8 = $uploaddir . $d_pol;
	$uploadfile9 = $uploaddir . $d_pmk;
	$uploadfile10 = $uploaddir . $d_bmkg;
	
	if($_FILES['doc_tun']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_tun']['tmp_name'], $uploadfile1))echo 'File Upload Error: '.$_FILES['doc_tun']['error'];}
	if($_FILES['doc_hil']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_hil']['tmp_name'], $uploadfile2))echo 'File Upload Error: '.$_FILES['doc_hil']['error'];}
	if($_FILES['doc_kro']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_kro']['tmp_name'], $uploadfile3))echo 'File Upload Error: '.$_FILES['doc_kro']['error'];}
	if($_FILES['doc_po']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_po']['tmp_name'], $uploadfile4))echo 'File Upload Error: '.$_FILES['doc_po']['error'];}
	if($_FILES['doc_fo']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_fo']['tmp_name'], $uploadfile5))echo 'File Upload Error: '.$_FILES['doc_fo']['error'];}
    if($_FILES['doc_rinci']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_rinci']['tmp_name'], $uploadfile6))echo 'File Upload Error: '.$_FILES['doc_rinci']['error'];}
	if($_FILES['doc_lap']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_lap']['tmp_name'], $uploadfile7))echo 'File Upload Error: '.$_FILES['doc_lap']['error'];}
	if($_FILES['doc_pol']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_pol']['tmp_name'], $uploadfile8))echo 'File Upload Error: '.$_FILES['doc_pol']['error'];}
	if($_FILES['doc_pmk']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_pmk']['tmp_name'], $uploadfile9))echo 'File Upload Error: '.$_FILES['doc_pmk']['error'];}
	if($_FILES['doc_bmkg']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_bmkg']['tmp_name'], $uploadfile10))echo 'File Upload Error: '.$_FILES['doc_bmkg']['error'];}
	
	switch($_POST['cod'])
	{
		case 'nds':$sebab='Natural Dissaster (Bencana Alam)'; $deduct='140000000'; break;
		case 'rio':$sebab='Riots/ Strikes, Malicious Damage (Kerusuhan)'; $deduct='140000000'; break;
		case 'thf':$sebab='Theft (Pencurian)'; $deduct='100000000'; break;
		case 'lit':$sebab='Lightning (Petir)'; $deduct='140000000'; break;
		case 'etv':$sebab='Earthquake, Tsunami, Volcano Erruption'; $deduct='?'; break;
		case 'fre':$sebab='Fire (Terbakar/ Kebakaran)'; $deduct='50000000'; break;
		case 'trp':$sebab='Third Party (Tuntutan Pihak ketiga)'; $deduct='20000000'; break;
		case 'oth':$sebab='Other Losses (Lainnya..)'; $deduct='75000000'; break;
	}
	
	if($rast->status=='REJECTED')
	{ $db->query("UPDATE ast2 SET `status`='UNAPPROVED',`note_rej`='',`pic_status`='0' "); }
	
	$SQL = "UPDATE ast2 SET       
	  `tgl_kejadian`='".$_POST['tgl_kejadian']."',
		`status_claim`='".$_POST['sclaim']."',
		`st_site_id`='".$_POST['site']."',
		`pic_region`='".$_POST['cp_nama']."',
		`telp`='".$_POST['cp_telp']."',
		`hp`='".$_POST['cp_hp']."',
		`sebab`='".$_POST['cod']."',`updated_at`=NOW()
        ".($d_tun<>''?",`doc_tun_file`='{$d_tun}',`doc_tun`=$d_tun_tgl":'')." 
		".($d_hil<>''?",`doc_hil_file`='{$d_hil}',`doc_hil`=$d_hil_tgl":'')." 
		".($d_kro<>''?",`doc_kro_file`='{$d_kro}',`doc_kro`=$d_kro_tgl":'')." 
		".($d_po<>''?",`doc_po_file`='{$d_po}',`doc_po`=$d_po_tgl":'')." 
		".($d_fo<>''?",`doc_fo_file`='{$d_fo}',`doc_fo`=$d_fo_tgl":'')." 
		".($d_rinci<>''?",`doc_rinci_file`='{$d_rinci}',`doc_rinci`=$d_rinci_tgl":'')." 
		".($d_lap<>''?",`doc_lap_file`='{$d_lap}',`doc_lap`=$d_lap_tgl":'')." 
		".($d_pol<>''?",`doc_pol_file`='{$d_pol}',`doc_pol`=$d_pol_tgl":'')." 
		".($d_pmk<>''?",`doc_pmk_file`='{$d_pmk}',`doc_pmk`=$d_pmk_tgl":'')." 
		".($d_bmkg<>''?",`doc_bmkg_file`='{$d_bmkg}',`doc_bmkg`=$d_bmkg_tgl":'')." WHERE `ast_id`= '".$_POST['i']."' "; 

	$db->query($SQL);
	$insast = 1;
}
	
?>

 
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
       <td style="width:200px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menu.php" ?>
			</ul>
	   </td>
    
	   <td>	
			<?php if($upd==1)
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p>Klaim AST berhasil di <?=$newStatus?>! <br /><a href="laporan_ast.php">Kembali ke halaman laporan AST &raquo;</a></p>
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
			
			<?php if($rast->status=='SUBMITTED' && $user->role=='spvr')
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:400px;margin:30px auto;"> 
				<p><strong><center>Masukan Dokumen-dokumen pendukung Disesuaikan </br>dengan "Cause of Damage"</center></strong></p>
			</div>
			<?php 
			} ?>
			
            <?php if($rast->status=='REJECTED' && $user->role=='spvr')
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding:0.7em;width:500px;margin:30px auto;"> 
				<fieldset> <legend> Catatan : </legend>
                <p><strong><center><?= $rast->note_rej ?></center></strong></p>
			    </fieldset>
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
			      
			<table id="fast" width="100%" border="0">
                <tr class="even" valign="top">
					<td width="43%">Hari / tanggal kejadian</td>
					<td colspan="2">
						<?php if($mode=='revisi')
						{ ?>
					    	<input type="text" name="tgl_kejadian_show" id="tgl_kejadian_show" value="<?=date('l/ j F Y',strtotime($rast->tgl_kejadian))?>" />
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
                
				<tr class="odd">
					<td>Site ID</td>
                    <td colspan="2">
					  <?php if($mode=='revisi')
					   { ?>
							<select name="site" id="lokasite">
							  <option value="">-Pilih Site ID-</option>
							  <?php $resSite = $db->get_results("SELECT st_site_id,st_name FROM `site` WHERE kode_region='".$user->regional."' GROUP BY st_site_id ORDER BY st_site_id ASC"); 
							   foreach($resSite as $site): ?>
							  <option <?=($site->st_site_id==$rast->st_site_id?'selected="selected"':'')?> value="<?=$site->st_site_id?>"><?=$site->st_site_id?> / <?=$site->st_name?></option>
							  <?php endforeach;?>							
						    </select>
                    </td>
				</tr>
                        
                <tr class="odd" valign="top">
					<td>Site Detail</td>
					<td colspan="2"> <div id="siteDetailAST"></div>
					<?php 
					}					
					else
					{
					$r = $db->get_row("SELECT * FROM `site` WHERE st_site_id='".$rast->st_site_id."'"); 
					echo $rast->st_site_id.' / '.$r->st_name;}					
					?>	
					</td>
				</tr>
                
                <tr><td colspan="3">&nbsp;</td></tr>
                <tr valign="top"  class="odd">
					<td>Contact person</td>
					<td colspan="2">
				     <table>
						<tr class="even">
							<td>Nama</td>
							<td>: 
								<?php if($mode=='revisi')
								{ ?>
								<input type="text" style="width:220px" name="cp_nama" value="<?=$rast->pic_region?>" />
							</td>
								<?php 
								}
								else
								{ ?>
								<?=$rast->pic_region ?>
								<?php 
								} ?>
						</tr>
						<tr class="odd">
							<td>No telepon/Fax</td>
							<td>: 
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
                        <tr class="even">
							<td>No HP</td>
							<td>: 
								<?php if($mode=='revisi'){ ?>
								<input type="text" style="width:220px" name="cp_hp" value="<?=$rast->hp?>" />
								<?php 
								}
								else
								{ ?>
								<?=$rast->hp ?>
								<?php 
								} ?>
							</td>
						</tr>
                     </table>
                    </td>
                </tr>
              
                <tr><td colspan="3">&nbsp;</td></tr>
				
                <?php if($mode=='revisi')
				{ ?>
                <tr class="odd" valign="top">
		            <td>Status Claim</td> 
                    <td colspan="2">
					    <input type="radio" name="sclaim" <?=($rast->status_claim=='total'?'checked="1"':'')?> value="total" id="total" /> 
                        <label for="total">Totally lost</label><br />
					    <input type="radio" name="sclaim" <?=($rast->status_claim=='partial'?'checked="1"':'')?> value="partial" id="partial" /> 
                        <label for="partial">Partial lost</label>
                    </td>
			    </tr>
				
                  <script type="text/javascript" language="javascript">
                     function toogleAssetCtgor() 
                      {
		               var rdsub = document.getElementById("c<?=$row->asset_group_id?>");
		               var rdTotal = document.getElementById("total");
		               var rdPartial = document.getElementById("partial");

		                if (rdTotal.checked) 
						 {
		                  document.getElementById("divAssetCtgor1").style.display = "block";
		                  document.getElementById("divAssetCtgor2").style.display = "none";
		                 }
		                else
		                 {
			              document.getElementById("divAssetCtgor1").style.display = "none";
	  	                  document.getElementById("divAssetCtgor2").style.display = "block";
		                 }
                       }
                  </script>
        
                <tr> 
				    <td colspan="3"> 
						<strong>Asset Category</strong><br />
                        <div class="subtabel" id="divAssetCtgor1">  <div id="total_lost"></div>	  </div>          
                        <div class="subtabel" id="divAssetCtgor2"> <div id="partial_lost"></div>  </div>
					</td>
			    </tr>

          <?php } 
				else
			    { ?>
                <tr class="odd" valign="top">
		            <td>Status Claim</td> 
                    <td colspan="2"><?=$rast->status_claim=='total'?'Total Loss':'Partial Loss'?></td>
			    </tr>    
          <?php } ?>   

          <?php if($mode=='revisi')
			   { ?> 
                <tr>           
		            <td colspan="1"> <strong>Rincian Kerusakan</strong> </td>
                    <td colspan="2" width="57%"> <?php //echo "test  :  ".$_GET['itemId']."<BR/>"; ?>
                      <select name="cat" id="group_cat" onchange="toogleAssetCtgor2()">
						<option value="">-Category-</option>							
                        <?php $res_cat = $db->get_results("SELECT group_name,asset_group_id FROM category GROUP BY group_name ORDER BY group_name ASC");
							 foreach($res_cat as $cat): ?>                   
	                    <option <?=($cat->asset_group_id==$rcat->asset_group_id?'selected="selected"':'')?> value="<?=$cat->asset_group_id?>"> <?=$cat->group_name?> </option>
				            <?php endforeach; ?>				
					   </select>
                    </td>    
                 </tr>
                <tr>
                    <td colspan="3"><div id="cat1"></div></td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td colspan="2"> <div id="merk_type1"> </div></td>
                </tr>
            <?php } 
              //else
			if($mode=='revisi'||'view')
			    {?>
                <tr class="odd">
				    <td>Rincian Kerusakan</td>
				    <td>
			          <?php 
						$ast_detail=$db->get_results ("SELECT * from ast_detail2 WHERE no_laporan='".$_GET['revisi']."'");
						if(!empty($ast_detail)):?>
							<table width="100%" border="0" cellpadding="5" cellspacing="0"> 
								<tr class="even">
									<th>Item</th>
									<th>Merk</th>
									<th>Type</th>
									<th>Quantity</th>
									<th>Satuan</th>
                                    <th>Tarikan</th>
				                </tr>
						
                        <?php $c=1;foreach($ast_detail as $d){ ?>
			        	        <tr class="<?=$c%2==0?'odd':'odd'?>">
									<td nowrap="nowrap"><?=$d->item1?></td>
									<td nowrap="nowrap"><?=$d->merk?></td>
									<td nowrap="nowrap"><?=$d->type?></td>
									<td align="center"><?=$d->quantity?></td>
									<td align="center"><?=$d->satuan?></td>
                                    <td align="center"><?=$d->tarikan?></td>
					             </tr>
						<?php $c++;}?>
							</table>							
					   <?php endif;?>
					</td>
				</tr>
	      <?php } ?>
                  
		  	    <tr class="even" valign="top">
					<td><strong>Cause of damage</strong></td>
				    <td colspan="2"> 
						<?php if($mode=='revisi')
						{ ?>        
                        <select name="cod">
                          <option value=""> -Sebab- </option>
                          <option value="thf" <?=($rast->sebab=="thf"?'selected="selected"':'') ?>>Theft (Pencurian) </option>
              	          <option value="lit" <?=($rast->sebab=="lit"?'selected="selected"':'') ?>>Lightning (Petir) </option>
             	          <option value="fre" <?=($rast->sebab=="fre"?'selected="selected"':'') ?>>Fire (Terbakar/ Kebakaran) </option>
                          <option value="nds" <?=($rast->sebab=="nds"?'selected="selected"':'') ?>>Natural Dissaster (Bencana Alam)</option>
                          <option value="rio" <?=($rast->sebab=="rio"?'selected="selected"':'') ?>>Riots/ Strikes, Malicious Damage (Kerusuhan) </option>
                          <option value="trp" <?=($rast->sebab=="trp"?'selected="selected"':'') ?>>Third Party (Tuntutan Pihak ketiga) </option>
                          <option value="etv" <?=($rast->sebab=="etv"?'selected="selected"':'') ?>>Earthquake, Tsunami, Volcano Erruption </option>
                          <option value="oth" <?=($rast->sebab=="oth"?'selected="selected"':'') ?>>Other Losses (Lainnya..) </option>
                        </select>			
						<?php 	}
						else
						{ 
						echo $sebab; } ?>
					</td>
				</tr>
                <tr>
					<td colspan="3">&nbsp;</td>
				</tr>	
                
				<?php
				if($mode=='revisi')
				{ ?>
                <tr class="odd" valign="top">
					<th>Dokumen-dokumen</th>
					
                    <td colspan="2"> 
						<table width="100%" border="0" cellpadding="0" cellspacing="0">
							<tr class="even">
								<td><div>Dokumen #1</div>Berita acara kerusakan atau kehilangan Aktiva Tetap (Rincian Objek yang Mengalami Kerugian)
                                    <div class="keterangan"><?=$rast->doc_hil_file==''?'-belum ada-':$rast->doc_hil_file?></div>
                                </td>
								<td><input type="file" name="doc_hil" /></td>
							</tr>
							<tr class="odd">
								<td><div>Dokumen #2</div>Kronologi kejadian/ kerugian
                                    <div class="keterangan"><?=$rast->doc_kro_file==''?'-belum ada-':$rast->doc_kro_file?></div>
                                </td>
								<td><input type="file" name="doc_kro" /></td>
							</tr>
                            <tr class="even">
								<td><div>Dokumen #3</div>Foto Objek Kerugian
                                    <div class="keterangan"><?=$rast->doc_fo_file==''?'-belum ada-':$rast->doc_fo_file?></div>
                                </td>
								<td><input type="file" name="doc_fo" /></td>
							</tr>
                            <tr class="odd">
								<td><div>Dokumen #4 </div>Rincian Kerugian
                                    <div class="keterangan"><?=$rast->doc_rinci_file==''?'-belum ada-':$rast->doc_rinci_file?></div>
								</td>
								<td><input type="file" name="doc_rinci" /></td>
							</tr>
                            <tr> <td>&nbsp;</td></tr>
        
                            <?php if($rast->sebab=='thf'||$rast->sebab=='rio')
					        { ?>
                            <tr class="even">
						  	    <td>Dokumen Khusus (BA Kepolisian)
								    <div class="keterangan">Disesuaikan dengan "Cause of Damage"</div>
                                    <div class="keterangan"><?=$rast->doc_pol_file==''?'-belum ada-':$rast->doc_pol_file?></div>
								</td>
								<td><input type="file" name="doc_pol" /></td>
							</tr>
                            <?php } 
					
          		            if($rast->sebab=='fre') {
							?>
                            <tr class="even">
								<td>Dokumen Khusus (Surat PMK)
						    		<div class="keterangan">Disesuaikan dengan "Cause of Damage"</div>
                                    <div class="keterangan"><?=$rast->doc_pmk_file==''?'-belum ada-':$rast->doc_pmk_file?></div>
								</td>
								<td><input type="file" name="doc_pmk" /></td>
                            </tr>
                            <?php } 
							 if($rast->sebab=='lit' || $rast->sebab=='nds' || $rast->sebab=='etv') 
                             {  ?>
                            <tr class="even">
								<td>Dokumen Khusus (Surat BMKG)
							    	<div class="keterangan">Disesuaikan dengan "Cause of Damage"</div>
                                    <div class="keterangan"><?=$rast->doc_bmkg_file==''?'-belum ada-':$rast->doc_bmkg_file?></div>
								</td>
								<td><input type="file" name="doc_bmkg" /></td>
                            </tr>
                            <? } ?>
						</table>
					</td>
				</tr>
		       <?php } 
               else
               {?>
                <tr>
                    <td colspan="3"><strong> Dokumen - dokumen :</strong></td>
                </tr>
                <tr class="even">
			        <td>Dokumen 1 <div class="keterangan">Surat tuntutan/ pengajuan klaim dari tertanggung</div></td>
			        <td colspan="2"> <a href="docs/ast/<?=$rast->doc_tun_file?>"><?=$rast->doc_tun_file?></a> </td>
		        </tr>
		        <tr class="even">
			        <td>Dokumen 2
					   <div class="keterangan">Berita acara kerusakan atau kehilangan Aktiva Tetap (Rincian Objek yang Mengalami Kerugian)</div>
			        </td>
			        <td colspan="2"><a href="docs/ast/<?=$rast->doc_hil_file?>"><?=$rast->doc_hil_file?></a></td>
		        </tr>
		        <tr class="even">
			        <td>Dokumen 3
					   <div class="keterangan">Kronologi kejadian/ kerugian</div></td>
			        <td colspan="2"><a href="docs/ast/<?=$rast->doc_kro_file?>"><?=$rast->doc_kro_file?></a></td>
		        </tr>
		        <tr class="even">
			        <td>Dokumen 4
			           <div class="keterangan">PO/ Kontrak/ Price list/ Kwitansi perbaikan/ pembelian perangkat/ Dokumen lain yang menjelaskan nilai kerugian </div>
			        </td>
			        <td colspan="2"><a href="docs/ast/<?=$rast->doc_po_file?>"><?=$rast->doc_po_file?></a></td>
		        </tr>
		        <tr class="even">
			        <td>Dokumen 5
					   <div class="keterangan">Foto Objek Kerugian</div>
			        </td>
			        <td colspan="2"><a href="docs/ast/<?=$rast->doc_fo_file?>"><?=$rast->doc_fo_file?></a></td>
		        </tr>
                <tr class="even">
			        <td>Dokumen Khusus (BA Kepolisian)</td>
			        <td colspan="2"><a href="docs/ast/<?=$rast->doc_pol_file?>"><?=$rast->doc_pol_file?></a></td>
		        </tr>
                <tr class="even">
			        <td>Dokumen Khusus (Surat PMK)</td>
			        <td colspan="2"><a href="docs/ast/<?=$rast->doc_pmk_file?>"><?=$rast->doc_pmk_file?></a></td>
		        </tr>
                <tr class="even">
			        <td>Dokumen Khusus (Surat BMKG)</td>
			        <td colspan="2"><a href="docs/ast/<?=$rast->doc_bmkg_file?>"><?=$rast->doc_bmkg_file?></a></td>
		        </tr>
                
                <tr><td colspan="3">&nbsp;</td></tr>				
		        <tr class="even">
			        <td>Dibuat pada</td>
			        <td colspan="2"><?=date("j F Y, H:i",strtotime($rast->created_at))?></td>
		        </tr>
		        <tr class="even">
			        <td>Diupdate pada</td>
			        <td colspan="2"><?=date("j F Y, H:i",strtotime($rast->updated_at))?></td>
	            </tr>
                <?php } ?>
				                
		        <tr class='odd'> 
		            <td td colspan=3> <hr size="5" noshade /></td> 
		        </tr>
                <tr class="even">
			        <td colspan="1" align="left">
			            <input style="cursor:pointer;" type="button" onclick="document.location.href='laporan_ast.php'" value="Kembali" />
                        <input name="print" style="cursor:pointer;" type="button" value="Print" onclick="window.open ('printDetailAST.php?ast=<?=$rast->ast_id?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
			        </td>			
                    <td align=""  style="display:block" class="hit2" nowrap="nowrap" >     
                    <?php 
					if($caption!='')
		            {  ?>					
                      <input name="" type="submit" value="<?=$caption?>" />
                    <?php if($mode=='approval') 
		              { ?>
                       <button name="reject2" type="button" onclick="rejectIt()" id="reject"  value=0>REJECT Laporan AST</button>
		            <?php } 
					} 
					echo 'Current status: '.$rast->status;
					?>
	                </td>
	            </tr>
                <tr>
                    <td>&nbsp; </td>
                    <td  style="display:none" class="hit"> Note :<br /> <textarea name="note_rej" cols="50" rows="3"></textarea> </td>
                </tr>	
                <tr>
                    <td>&nbsp; </td>
                    <td align="" class="hit"  style="display:none">  
                        <button name="cancel2" type="button" onclick="cancel()" id=""  value=1>Cancel</button>
                        <input type="hidden" value="0" name="isReject" />
                        <input name="reject" onclick="isReject.value=1" type="submit" value="REJECT Laporan AST" />
                    </td>
                </tr>			
            </table>     	
	        </form>
            <?php } ?>			
        </td>
    </tr>
    <tr><td>&nbsp;</td></tr>
</table>

<script type="text/javascript">
	function rejectIt(){
	var but = $("button[name*=reject2]").val();
	//alert(but);
	if (but==0){ $(".hit").fadeIn('slow'); }
	if (but==0){ $(".hit2").hide(); }
	}
 
	function cancel(){
	var but2 = $("button[name*=cancel2]").val();
	//alert(but2);
	if (but2==1){ $(".hit").hide('slow'); }	
	if (but2==1){ $(".hit2").fadeIn(); }
	}
</script>  
<?php include "footer.php"?>


