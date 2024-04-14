<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
include "headerast.php";

$rast = $db->get_row("SELECT * FROM `ast2` WHERE no_laporan='".$_GET['i']."'");

if($rast->sebab=='nds') { $sebab='Natural Dissaster (Bencana Alam)';}
elseif ($rast->sebab=='thf') { $sebab= 'Theft (Pencurian)';}
elseif ($rast->sebab=='lit') { $sebab= 'Lightning (Petir)';}
elseif ($rast->sebab=="etv") { $sebab= 'Earthquake, Tsunami, Volcano Erruption';}
elseif ($rast->sebab=='fre') { $sebab= 'Fire (Terbakar/ Kebakaran)';}
elseif ($rast->sebab=='trp') { $sebab= 'Third Party (Tuntutan Pihak ketiga)';}
elseif ($rast->sebab=='rio') { $sebab= 'Riots/ Strikes, Malicious Damage (Kerusuhan)';}
else { $sebab= 'Other Losses (Lainnya..)';}

 function tgl($tgl)
		{
			if($tgl <> '0000-00-00'){ echo date('l/ j F Y',strtotime($tgl)); }
			else {echo "-";}
		}								
//---------------------------------------------- # 2.JIKA STATUS "APPROVED"
//if($_POST && $rcgl->status=='UNAPPROVED')  --- EDITED---- 
if($_POST && $rast->status=='APPROVED') 
{
	if($_POST['isReject']=='1')
	{
     $err = array();
     if($_POST['note_rej']=='') $err[]="Catatan Untuk Rejected";	
    
     if(empty($err)):
		$newStatus = 'REJECTED';
		$set='`reject_at`=NOW()';		
    $db->query("UPDATE ast2 SET `status`='{$newStatus}',{$set},`note_rej`='".$_POST['note_rej']."',`pic_status`='2' WHERE no_laporan='".$_POST['i']."'");
    
	   	
	$upd = 1;   
  endif;
    }  
	elseif ($_POST['isUnder']=='2')
	{    
	  $newStatus = 'UNDER DEDUCTIBLE';
		$db->query("UPDATE ast2 SET `status`='{$newStatus}',`status_progress`='2' WHERE no_laporan='".$_POST['i']."'");
    $upd = 1;
	}
	else
	{
		$newStatus = 'SUBMITTED';  
		$set='`submit_at`=NOW()';		
		$db->query("UPDATE ast2 SET `status`='{$newStatus}',{$set},`status_progress`='1',`estimasi`='".$_POST['estimasi']."',`estimasi_at`=NOW() 
                WHERE no_laporan='".$_POST['i']."'");	


//-- send emails
		$query = "SELECT * FROM `site` WHERE `st_site_id` = '{$rast->st_site_id}'";
		$r = $db->get_row($query);	
		//$r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");	
		
		$raw 			= file_get_contents('ast_submitted.email.html');
		$pattern 		= array('%%NODOKUMEN%%','%%TAHUN%%','%%TGLKEJADIAN%%',': %%TGLLAPOR%%','%%NAMASITE%%',
							'%%ALAMATSITE%%','%%REGIONAL%%','%%LONGLAT%%','%%SEBAB%%','%%RINCIAN%%',
							'%%CPNAMA%%','%%CPTELP%%','%%CPHP%%','%%NAMA%%','%%JABATAN%%');
		$replaceWith 	= array($kode_laporan,date("Y"),date("l/ j F Y",strtotime($rast->tgl_kejadian)),date("l/ j F Y",strtotime($rast->approve_at)),'['.$r->st_site_id.']'.$r->st_name,
						$rast->st_address,$r->st_region,$r->st_latitude.'/'.$r->st_longitude,$sebab,$rast->rincian,
						$rast->pic_region,$rast->telp,$rast->hp,$user->nama,$user->posisi);
		$emailBody = str_replace($pattern, $replaceWith, $raw);
		

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
			
			require 'initMail.php';
			sendMail('Klaim AST ['.$r->st_site_id.']'.$r->st_name.' '.date("d/m/Y",strtotime($rast->tgl_kejadian)).': SURVEY',$emailBody,$to,$cc,$bcc);
		}
		//=================================================== SMS=========================================================		
		$upd = 1;
		
	}
  
    $rinci = array();
	$rinci = $_POST['rinci'];
	
	if(!empty($rinci)){
	foreach($rinci as $ri) {
	$db->query("UPDATE ast_detail2 SET `price`='".$_POST['price'.$ri]."',`currency`='".$_POST['currency'.$ri]."',
             `jumlah`='".$_POST['jumlah'.$ri]."' WHERE no_laporan='".$_POST['i']."' and id='$ri' ");		    }
	        }
          
	/*
	$db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
	('ast','".$_POST['i']."','".$rsat->no_laporan."','".$user->user_id."','{$newStatus}',NOW())");	
	*/
	
}

//------------------------------------------------------- # 3.JIKA STATUS CLOSED
if($_POST['isCc']=='1')       // KIRIM EMAIL???
{
	$newStatus = "CLOSED";
	$set = "caseclosed_at=NOW(), caseclosed_by='".$user->user_id."'";

	$db->query("UPDATE ast SET `status`='{$newStatus}',{$set} WHERE ast_id='".$_POST['i']."'");	
/*
	$db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
	('ast','".$_POST['i']."','".$rast->no_laporan."','".$user->user_id."','{$newStatus}',NOW())");	
*/	
	$upd = 1;
}

//-------------------------------------------- STATUS SUBMITTED
if($_POST && $rast->status=='SUBMITTED')
{ 

$kode_laporan = $rast->no_laporan;	
$prefixFile = str_replace('/','',$kode_laporan);

	if($_FILES['doc_tun']['name']<>'') { 
		                                 $d_tun= $prefixFile.'_1_'.basename($_FILES['doc_tun']['name']);
									     $d_tun_tgl="NOW()";
										 $db->query("UPDATE ast2 SET `doc_tun_file`='".$d_tun."',`doc_tun`=".$d_tun_tgl." WHERE no_laporan='".$_POST['i']."'");}
	
	if($_FILES['doc_po']['name']<>'') {
		                                  $d_po= $prefixFile.'_4_'.basename($_FILES['doc_po']['name']);
	                                      $d_po_tgl="NOW()";
										  $db->query("UPDATE ast2 SET `doc_po_file`='".$d_po."',`doc_po`=".$d_po_tgl." WHERE no_laporan='".$_POST['i']."'");}
										  
   if($_FILES['doc_konf1']['name']<>'') { 
		                                 $d_konf1= $prefixFile.'_konf1_'.basename($_FILES['doc_konf1']['name']);
										 $db->query("UPDATE ast2 SET `doc_konf1_file`='".$d_konf1."' WHERE no_laporan='".$_POST['i']."'");}
										 
   if($_FILES['doc_konf2']['name']<>'') { 
		                                 $d_konf2= $prefixFile.'_konf2_'.basename($_FILES['doc_konf2']['name']);
										 $db->query("UPDATE ast2 SET `doc_konf2_file`='".$d_konf2."' WHERE no_laporan='".$_POST['i']."'");}
   if($_FILES['doc_konf3']['name']<>'') { 
		                                 $d_konf3= $prefixFile.'_konf3_'.basename($_FILES['doc_konf3']['name']);
										 $db->query("UPDATE ast2 SET `doc_konf3_file`='".$d_konf3."' WHERE no_laporan='".$_POST['i']."'");}
										   
	$uploaddir = 'docs/ast/';
    $uploadfile1 = $uploaddir . $d_tun;
    $uploadfile2 = $uploaddir . $d_po;
	
	if($_FILES['doc_tun']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_tun']['tmp_name'], $uploadfile1))echo 'File Upload Error: '.$_FILES['doc_tun']['error'];}
	if($_FILES['doc_po']['name']<>'')
	    	{  if(!move_uploaded_file($_FILES['doc_po']['tmp_name'], $uploadfile2))echo 'File Upload Error: '.$_FILES['doc_po']['error'];}
  


   if(!is_numeric($_POST['estimasi'])) $err[]="Nilai estimasi kerugian hanya dapat berupa angka"; 
   if(!is_numeric($_POST['pro_adj1'])) $err[]="Nilai Propose Adjusment hanya dapat berupa angka"; 


   if ($_POST['estimasi']<>'')
       { $db->query("UPDATE ast2 SET `estimasi`='".$_POST['estimasi']."' WHERE no_laporan='".$_POST['i']."'"); }
   if ($_POST['tgl_estimasi']<>'') 
       { $db->query("UPDATE ast2 SET `estimasi_at`='".$_POST['tgl_estimasi']."' WHERE no_laporan='".$_POST['i']."'"); }
   
   if ($_POST['pro_adj1']<>'')
       { $db->query("UPDATE ast2 SET `pro_adj1`='".$_POST['pro_adj1']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_proadj1']<>'')
       { $db->query("UPDATE ast2 SET `tgl_proadj1`='".$_POST['tgl_proadj1']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['pro_adj2']<>'')
       { $db->query("UPDATE ast2 SET `pro_adj2`='".$_POST['pro_adj2']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_proadj2']<>'')
       { $db->query("UPDATE ast2 SET `tgl_proadj2`='".$_POST['tgl_proadj2']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['pro_adj3']<>'')
       { $db->query("UPDATE ast2 SET `pro_adj3`='".$_POST['pro_adj3']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_proadj3']<>'')
       { $db->query("UPDATE ast2 SET `tgl_proadj3`='".$_POST['tgl_proadj3']."' WHERE no_laporan='".$_POST['i']."'");}
	   
   if ($_POST['kon_adj1']<>'')
       { $db->query("UPDATE ast2 SET `kon_adj1`='".$_POST['kon_adj1']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_konadj1']<>'')
       { $db->query("UPDATE ast2 SET `tgl_konadj1`='".$_POST['tgl_konadj1']."' WHERE no_laporan='".$_POST['i']."'");}
	   
   if ($_POST['kon_adj2']<>'')
       { $db->query("UPDATE ast2 SET `kon_adj2`='".$_POST['kon_adj2']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_konadj2']<>'')
       { $db->query("UPDATE ast2 SET `tgl_konadj2`='".$_POST['tgl_konadj2']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['kon_adj3']<>'')
       { $db->query("UPDATE ast2 SET `kon_adj3`='".$_POST['kon_adj3']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_konadj3']<>'')
       { $db->query("UPDATE ast2 SET `tgl_konadj3`='".$_POST['tgl_konadj3']."' WHERE no_laporan='".$_POST['i']."'");}   
	
	   
   if ($_POST['settled']<>'')
       { $db->query("UPDATE ast2 SET `settled`='".$_POST['settled']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_settled']<>'')
       { $db->query("UPDATE ast2 SET `tgl_settled`='".$_POST['tgl_settled']."' WHERE no_laporan='".$_POST['i']."'");}
	   
   if ($_POST['budget']<>'')
       { $db->query("UPDATE ast2 SET `budget`='".$_POST['budget']."' WHERE no_laporan='".$_POST['i']."'");}
   if ($_POST['tgl_budget']<>'')
       { $db->query("UPDATE ast2 SET `tgl_budget`='".$_POST['tgl_budget']."' WHERE no_laporan='".$_POST['i']."'");}
	   
   if ($_POST['note']<>'')
       { $db->query("UPDATE ast2 SET `note`='".$_POST['note']."' WHERE no_laporan='".$_POST['i']."'");}

    $rinci = array();
	$rinci = $_POST['rinci'];
	
	if(!empty($rinci)){
	foreach($rinci as $ri) {
	$db->query("UPDATE ast_detail2 SET `price`='".$_POST['price'.$ri]."',`currency`='".$_POST['currency'.$ri]."',
             `jumlah`='".$_POST['jumlah'.$ri]."' WHERE no_laporan='".$_POST['i']."' and id='$ri' ");		    }
	        }
   //if($_FILES['doc_tun']['name']<>'')
     //  { $db->query("UPDATE ast2 SET `doc_tun_file`='".$d_tun."',`doc_tun`=".$d_tun_tgl." WHERE no_laporan='".$_POST['i']."'");}
   
    //if($_FILES['doc_po']['name']<>'')
      // { $db->query("UPDATE ast2 SET `doc_po_file`='".$d_po."',`doc_po`=".$d_po_tgl." WHERE no_laporan='".$_POST['i']."'");}
   
   
$upd = 2;
}

//---------------------------------------------- JIKA STATUS ( isCc==1 )
/*
if($_POST['isCc']=='1')       // KIRIM EMAIL???
{
	$newStatus = "CLOSED";
	$set = "caseclosed_at=NOW(), caseclosed_by='".$user->user_id."'";

	$db->query("UPDATE cgl SET `status`='{$newStatus}',{$set} WHERE cgl_id='".$_POST['i']."'");	

	$db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
	('cgl','".$_POST['i']."','".$rcgl->no_laporan."','".$user->user_id."','{$newStatus}',NOW())");	
	$upd = 1;
}	
*/


?>

<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:140px">
			<?php include "menusuper.php" ?>			
		</td>
		<td>
			<?php if($upd==1)
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Laporan AST telah di <?=$newStatus?>!!</p>
			</div>
			<?php }
			elseif ($upd==2)
		     	{    ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Nilai Estimasi Kerugian telah di submit!!</p> </div
    ><?php }

			else
			{ ?>			
            <h3>Detail Laporan AST</h3>
            <?php if(!empty($err))
			{ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Mohon isi / perbaiki data berikut:
					<ul>
					<?php foreach($err as $e):
							?>
							<li><?=ucfirst($e)?></li>
							<?
							endforeach; ?>
				    	</ul>
			      	</p>
			       </div>
			     <?php 	} 
			?>
            
            <form method="post" action="" name="" enctype="multipart/form-data"  autocomplete="off">
            <input type="hidden" name="i" value="<?=$rast->no_laporan?>" />  <!------- VAR= i ------------>
			
			
			<table width="100%"> 
				<tr class="odd">
					<td colspan="2">Nomor Laporan</td>
					<td colspan="2"><?=$rast->no_laporan?></td>
				</tr>
                <tr class="even">
					<td colspan="2">Hari, tanggal kejadian</td>
					<td colspan="2">
						<?=date("l, d F Y",strtotime($rast->tgl_kejadian)) ?>						
					</td>
				</tr>
                
                <tr class="odd">
					<td colspan="2">Hari, tanggal Lapor SJU</td>
					<td colspan="2">
                    <?php if($rast->submit_at==''||$rast->submit_at=='0000-00-00') {echo "-";} else { echo date("l, d F Y",strtotime($rast->submit_at));} ?>
					</td>
				</tr>
				<tr class="even">
					<td colspan="2">Tempat/ Lokasi Kerugian</td>
					<td colspan="2"><?=$rast->st_site_id?></td>
				</tr>
				<tr class="odd">
					<td colspan="2">Status Claim</td>
					<td colspan="2"><?=$rast->status_claim=='total'?'Total Loss':'Partial Loss'?></td>
				</tr>
				
        <tr class="even">
					<td colspan="2">Cause of Damage</td>
					<td colspan="2"><?php echo $sebab; ?> </td>
				</tr>
				
        <tr class="odd">
					<td colspan="2">Rincian Kerusakan</td>
					<td colspan="2">
					<?php 
						$ast_detail=$db->get_results ("SELECT * from ast_detail2 WHERE no_laporan='".$_GET['i']."'");
						//if(!empty($ast_detail)):?>
							<table width="100%"> 
								<tr>
									<th>Item</th>
									<th>Merk</th>
									<th>Type</th>
									<th>Quantity</th>
									<th>Satuan</th>
                                    <th>Tarikan</th>
                                    <td>&nbsp;</td>
                                    <?php if( $rast->status=='APPROVED'|| $rast->status=='SUBMITTED'&& $user->role=='spvp') { ?>
                                    <th>Currency</th>
                                    <th>Price/item</th>
                                    <th>Jumlah</th>
                                    <?php } ?>
					            </tr>
                    
							    <?php $c=1;foreach($ast_detail as $d){ ?>
					           <tr class="<?= $i++;
					               $c%2==0?'odd':'even'?>">
									<td nowrap="nowrap"><?=$d->item1?></td>
									<td nowrap="nowrap"><?=$d->merk?></td>
									<td nowrap="nowrap"><?=$d->type?></td>
									<td align="center">
									<?=$d->quantity?>
                                    <input name="quantity<?=$i?>" type="hidden" id="quan" value="<?=$d->quantity?>" />
                                    </td>
									<td align="center"><?=$d->satuan?></td>
                                    <td align="center"><?=$d->tarikan?></td>
                                    <td>&nbsp;</td>
                                    <?php if( $rast->status=='APPROVED'|| $rast->status=='SUBMITTED'&& $user->role=='spvp') { ?>
                                    <td align="center">
                                    <input name="rinci[]" type="hidden" value="<?=$i?>" /> 
                                       <select  name="currency<?=$i?>" id="curr" value="">
                        					<option <?=($d->currency=='idr'?'selected="selected"':'')?> value="idr">IDR</option>
											<option <?=($d->currency=='usd'?'selected="selected"':'')?> value="usd">USD</option>
                				            <option <?=($d->currency=='eur'?'selected="selected"':'')?> value="eur">EUR</option>
                                      </select>
                                    </td>  
                                    <td align="center"> <input name="price<?=$i?>" type="text" size="10" value="<?=$d->price?>"/> </td> 
                                    <td align="center"> <input type="text" name="jumlah<?=$i?>" value="<?=$d->jumlah?>" size="10" /> </td> 
                                      <?php } ?> 
					           </tr>
							<?php $c++;}?>
                            <?php if( $rast->status=='APPROVED'|| $rast->status=='SUBMITTED'&& $user->role=='spvp') { ?>
                               <tr>
                                    <td colspan="8">&nbsp;</td>
                                    <td align="right"><strong>Total :</strong></td>
                                    <td align="center"><input type="text"  name="total" value="<?=$rast->estimasi?>" size="10" /></td>
                               </tr> 
                               <?php } ?>
							</table>
                            							
						<?php //endif;?> 
                        <input name="count" type="hidden" id="count" value="<?=$i?>" />
					</td>
		        </tr>
        
                <tr>
                      <td colspan="2">Nilai Estimasi</td>
			          <?php if( $rast->status=='APPROVED'|| $rast->status=='SUBMITTED'&& $user->role=='spvp') { ?>
                      <td colspan="1">
				      	<input type="text" name="estimasi" value="<?=$rast->estimasi?>" class="narr" />
				<!--	<div class="keterangan"><strong>*) Besaran nilai Estimasi adalah sejumlah total Nilai Estimasi Dari total kerugian asset.</div> -->              <input type="submit" value="Ok" />
			         </td>
                      <?php } 
					  else {?>  
                      <td colspan=""> <?=number_format($rast->estimasi)?></td>
                      <?php } ?>
                             
              <!-- <td> Tanggal </td>
               <td> 
                    <input type="text" name="tgl_estimasi_show"  value="<?php tgl($rast->estimasi_at); ?>" id="tgl_estimasi_show" class="narr" />
                    <input type="hidden" name="tgl_estimasi" id="tgl_estimasi"  value="<?=$_POST['tgl_estimasi']?>" />
				</td>-->
             </tr>  
             
        <tr class="even">
		   <td colspan="2">Nilai Deductible</td>
           <td colspan="2"><?=number_format($rast->deduct)?></td>
        </tr>    
        
        <tr class="odd">
		   <td colspan="4">&nbsp;</td>
        </tr>          
				
		<tr class="even">
		   <td colspan="3">Dokumen 1
			   <div class="keterangan">Berita acara kerusakan atau kehilangan Aktiva Tetap (Rincian Objek yang Mengalami Kerugian)</div>
		   </td>
		   <td colspan="1"><a href="docs/ast/<?=$rast->doc_hil_file?>"><?=$rast->doc_hil_file?></a></td>
		</tr>
        
		<tr class="even">
		   <td colspan="3">Dokumen 2
			  <div class="keterangan">Kronologi kejadian/ kerugian</div></td>
		   <td><a href="docs/ast/<?=$rast->doc_kro_file?>"><?=$rast->doc_kro_file?></a></td>
		</tr>
				
		<tr class="even">
			<td colspan="3">Dokumen 3
				<div class="keterangan">Foto Objek Kerugian</div>
			</td>
			<td><a href="docs/ast/<?=$rast->doc_fo_file?>"><?=$rast->doc_fo_file;?></a></td>
		</tr>
              
        <tr class="even">
			<td colspan="3">Dokumen 4
				<div class="keterangan">Dokumen Rincian Kerugian</div>
			</td>
			<td><a href="docs/ast/<?=$rast->doc_rinci_file?>"><?=$rast->doc_rinci_file?></a></td>
		</tr>

        <?php if($rast->sebab=='thf' || $rast->sebab=='rio' || $rast->sebab=='trp'){ ?> 
        <tr class="even">
			<td colspan="3">Dokumen 
				<div class="keterangan">Dokumen Khusus (BA Kepolisian)</div>
			</td>
			<td><a href="docs/ast/<?=$rast->doc_pol_file?>"><?=$rast->doc_pol_file?></a></td>
		</tr>
          <?php } 
		   if($rast->sebab=='fre') {  ?>
        <tr class="even">
			<td colspan="3">Dokumen 
					<div class="keterangan">Dokumen Khusus (Surat PMK)</div>
			</td>
			<td><a href="docs/ast/<?=$rast->doc_pmk_file?>"><?=$rast->doc_pmk_file?></a></td>
		</tr>
            <?php } 
			   if($rast->sebab=='lit' || $rast->sebab=='nds' || $rast->sebab=='etv') {
			?>
        <tr class="even">
			<td colspan="3">Dokumen 
				<div class="keterangan">Dokumen Khusus (Surat BMKG)</div>
			</td>
			<td><a href="docs/ast/<?=$rast->doc_bmkg_file?>"><?=$rast->doc_bmkg_file?></a></td>
		</tr>
        <?php } ?>
        <tr>
          <td colspan="4">&nbsp;</td>
        </tr>
                         
                <!--------------------------- DOKUMENT OLEH HO ---------------------------------------------------------->
                 <?php if( $rast->status=='SUBMITTED'&& $user->role=='spvp') { ?>
        <tr>
			<td colspan="3"><div>Dokumen </div>Surat tuntutan/ pengajuan klaim dari tertanggung
                <div class="keterangan"><?=$rast->doc_tun_file==''?'-belum ada-':$rast->doc_tun_file?></div>
            </td>
			<td><input type="file" name="doc_tun" /></td>
		</tr>
        <tr class="even">
			<td colspan="3"><div>Dokumen</div>PO/ Kontrak/ Price list/ Kwitansi perbaikan/ pembelian perangkat/ Dokumen lain yang menjelaskan nilai kerugian 
                <div class="keterangan"><?=$rast->doc_po_file==''?'-belum ada-':$rast->doc_po_file?></div>
            </td>
			<td><input type="file" name="doc_po" /></td>
		</tr>     
            <?php } ?>
				
		<tr class="odd">
			<td colspan="1">Dibuat pada</td>
			<td colspan="3"><?=date("j F Y, H:i",strtotime($rast->created_at))?></td>
		</tr>
				
        <tr class="odd">
			<td colspan="1">Diupdate pada</td>
			<td colspan="3"><?=date("j F Y, H:i",strtotime($rast->updated_at))?></td>
		</tr>
       <!-- <tr><td colspan="4">&nbsp;</td></tr>-->
            <?php if($rast->status=='SUBMITTED')
			     {?>
        <tr class="" valign="top">
             <td colspan="4" align="center"> 
               <table border="0" cellpadding="10" cellspacing="0" width="100%"> 
                  <tr>
                   <td colspan="">
          
                  <table width="" border="0" cellpadding="0" cellspacing="10" class="even">
                      <td nowrap="nowrap">Proposed Adjustment 1</td>
			          <td colspan="0">
					    <input type="text" name="pro_adj1" value="<?=$rast->pro_adj1?>" class="narr" />
			          </td>
                      <td width="20">&nbsp;</td>
                      <td> Tanggal</td>
                <td>
                    <input type="text" name="tgl_proadj1_show"  value="<?php tgl($rast->tgl_proadj1); ?>" id="tgl_proadj1_show" class="narr" />
                    <input type="hidden" name="tgl_proadj1" id="tgl_proadj1"  value="<?=$_POST['tgl_proadj1']?>" />
				</td>
                <td><input onclick="isSET.value=1" type="submit" value="Ok" /></td>
             </tr>
             <tr>
               <td>Konfirmasi Proposed Adjustment 1</td>
			   <td colspan="0"><input type="text" name="kon_adj1" value="<?=$rast->kon_adj1?>" class="narr" />			     <!--	<div class="keterangan"><strong>*).</div> -->
			   </td>
               <td>&nbsp;</td>
               <td> Tanggal</td>
               <td>
                    <input type="text" name="tgl_konadj1_show"  value="<?php tgl($rast->tgl_konadj1);?>" id="tgl_konadj1_show" class="narr" />
                    <input type="hidden" name="tgl_konadj1" id="tgl_konadj1"  value="<?=$_POST['tgl_konadj1']?>" />
				</td>
                <td>&nbsp;</td>
             </tr>
             <tr>
                <td colspan="2"><div>Dokumen </div>Konfirmasi Proposed Adjustment 1
                <div class="keterangan"><?=$rast->doc_konf1_file==''?'-belum ada-':$rast->doc_konf1_file?></div></td>
                <td colspan="2">&nbsp;</td>
                <td><input type="file" name="doc_konf1" /></td>
                <td><input onclick="isSET.value=1" type="submit" value="Ok" /> </td>
		     </tr>
             </table> </td> </tr>
             
             <tr><td>
             <table width="" border="0" cellpadding="0" cellspacing="10" class="even">
             <tr>
               <td>Proposed Adjustment 2</td>
			   <td colspan="0">
					<input type="text" name="pro_adj2" value="<?=$rast->pro_adj2?>" class="narr" />
				<!--	<div class="keterangan"><strong>*).</div> -->
			   </td>
               <td width="20">&nbsp;</td>
               <td> Tanggal</td>
               <td>
                    <input type="text" name="tgl_proadj2_show"  value="<?php tgl($rast->tgl_proadj2);?>" id="tgl_proadj2_show" class="narr" />
                    <input type="hidden" name="tgl_proadj2" id="tgl_proadj2"  value="<?=$_POST['tgl_proadj2']?>" />
				</td>
                <td>
               
                <input onclick="isSET.value=1" type="submit" value="Ok" />  
                </td>
             </tr>
             
             <tr>
               <td>Konfirmasi Proposed Adjustment 2</td>
			   <td colspan="0">
					<input type="text" name="kon_adj2" value="<?=$rast->kon_adj2?>" class="narr" />
					<!--	<div class="keterangan"><strong>*).</div> -->
			   </td>
               <td>&nbsp;</td>
               <td> Tanggal</td>
               <td>
                    <input type="text" name="tgl_konadj2_show"  value="<?php tgl($rast->tgl_konadj2);?>" id="tgl_konadj2_show" class="narr" />
                    <input type="hidden" name="tgl_konadj2" id="tgl_konadj2"  value="<?=$_POST['tgl_konadj2']?>" />
				</td>
                <td>&nbsp;</td>
             </tr>
             <tr>
                <td colspan="2"><div>Dokumen </div>Konfirmasi Proposed Adjustment 2
               <div class="keterangan"><?=$rast->doc_konf2_file==''?'-belum ada-':$rast->doc_konf2_file?></div></td>
                <td colspan="2">&nbsp;</td>
                <td><input type="file" name="doc_konf2" /></td>
                <td><input onclick="isSET.value=1" type="submit" value="Ok" /> </td>
		     </tr>
             </table> </td> </tr>
             
             <tr><td>
            
             <table width="" border="0" cellpadding="0" cellspacing="10" class="even">
             <tr>
               <td>Proposed Adjustment 3</td>
			   <td colspan="0">
					<input type="text" name="pro_adj3" value="<?=$rast->pro_adj3?>" class="narr" />
					<!--	<div class="keterangan"><strong>*).</div> -->
			   </td>
               <td width="20">&nbsp;</td>
               <td> Tanggal</td>
               <td>
                    <input type="text" name="tgl_proadj3_show"  value="<?php tgl($rast->tgl_proadj3);?>" id="tgl_proadj3_show" class="narr" />
                    <input type="hidden" name="tgl_proadj3" id="tgl_proadj3"  value="<?=$_POST['tgl_proadj3']?>" />
				</td>
                <td>
               
                <input onclick="isSET.value=1" type="submit" value="Ok" />  
                </td>
             </tr>
             
             <tr>
               <td>Konfirmasi Proposed Adjustment 3</td>
			   <td colspan="0">
                <input type="text" name="kon_adj3" value="<?=$rast->kon_adj3?>" class="narr" />
               <!--	<div class="keterangan"><strong>*).</div> -->
			   </td>
               <td>&nbsp;</td>
               <td> Tanggal</td>
               <td>
                    <input type="text" name="tgl_konadj3_show"  value="<?php tgl($rast->tgl_konadj3);?>" id="tgl_konadj3_show" class="narr" />
                    <input type="hidden" name="tgl_konadj3" id="tgl_konadj3"  value="<?=$_POST['tgl_konadj3']?>" />
				</td>
                <td>&nbsp; </td>
             </tr>
             <tr>
                <td colspan="2"><div>Dokumen </div>Konfirmasi Proposed Adjustment 3
                <div class="keterangan"><?=$rast->doc_konf3_file==''?'-belum ada-':$rast->doc_konf3_file?></div></td>
                <td colspan="2">&nbsp;</td>
                <td><input type="file" name="doc_konf3" /></td>
                <td><input onclick="isSET.value=1" type="submit" value="Ok" /> </td>
		     </tr>
             </table> 
             </td> </tr>
             
             <tr><td><table width="" border="0" cellpadding="0" cellspacing="10" class="even">
               <tr>
                 <td>Setlled</td>
                 <td colspan="0"><input type="text" name="settled" value="<?=$rast->settled?>" class="narr" />
                   <!--	<div class="keterangan"><strong>*).</div> --></td>
                 <td>&nbsp;</td>
                 <td> Tanggal</td>
                 <td><input type="text" name="tgl_settled_show"   value="<?php tgl($rast->tgl_settled);?>" id="tgl_settled_show" class="narr" />
                   <input type="hidden" name="tgl_settled" id="tgl_settled"  value="<?=$_POST['tgl_settled']?>" /></td>
                 <td><input onclick="isSET.value=1" type="submit" value="Ok" /></td>
               </tr>
               <tr>
                 <td>Budget</td>
                 <td colspan="0"><input type="text" name="budget" value="<?=$rast->budget?>" class="narr" />
                   <!--	<div class="keterangan"><strong>*).</div> --></td>
                 <td>&nbsp;</td>
                 <td> Tanggal</td>
                 <td><input type="text" name="tgl_budget_show"  value="<?php tgl($rast->tgl_budget);?>" id="tgl_budget_show" class="narr" />
                   <input type="hidden" name="tgl_budget" id="tgl_budget"  value="<?=$_POST['tgl_budget']?>" /></td>
                 <td><input onclick="isSET.value=1" type="submit" value="Ok" /></td>
               </tr>
               <tr>
                 <td colspan="5">&nbsp;</td>
               </tr>
               <tr>
                 <td colspan="1">&nbsp;</td>
                 <td colspan="4"><legend> Note : </legend>
                   <textarea name="note" cols="79" rows="5"><?=$rast->note?>
                   </textarea></td>
               </tr>
             </table></td> </tr>
            
            </table>
       
        </td>         
		</tr>
                <?php } ?>
				
        <tr class="even">
        	<td>&nbsp;</td>
					<td colspan="2"> 
					<?php if($user->role=='gmp' && $rast->status<>'CLOSED')
					{ ?>
					<div style="margin:10px;background:#fcc;padding:5px;text-align:center;">
						<input type="hidden" value="0" name="isCc" /> 
                        <input onclick="if(confirm('Case Closed Laporan AST <?=$rast->no_laporan?>'))isCc.value=1;else return false;" type="submit" value="Set CASE CLOSED" />
					</div>
					<?php 
					} ?>
					
						<input style="cursor:pointer;" type="button" onclick="document.location.href='lap_ast.php'" value="Kembali" />
                        <!--------------------UNEDITED #onclick="document.location.href='lap_cgl.php'-------------------->
                        
						<input style="cursor:pointer;" type="button" value="Print" onclick="window.open ('printDetailAST.php?ast=<?=$rast->ast_id?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
					</td>
					
                    <!--<td colspan="1"> -->
                    <td align=""  style="display:block" class="hit2" nowrap="nowrap" >  				
						<?php //if(in_array($rast->status, array('SUBMITTED','UNDER DEDUCTIBLE','PAYMENT','SETTLEMENT','CLOSED','REJECTED'))) echo 'Current status: '.$rast->status.'<br />';
						echo 'Current status: '.$rast->status.'<br />'; 
						?> 
						
						
						<?php if($rast->status=='APPROVED')
						{ ?>
				<!--	<input type="hidden" value="0" name="isReject" /> 
                    <input onclick="isReject.value=1" type="submit" value="REJECTED" /> -->
                     <button name="reject2" type="button" onclick="rejectIt()" id="reject"  value=0>REJECT Laporan AST</button>
					
                    <input type="hidden" value="0" name="isUnder" /> 
                    <input onclick="isUnder.value=2" type="submit" value="Under Deductible" />
                    
                    <input type="submit" value="Submit SJU" />
						<?php 
						}?>
						
						<?php if($rast->status=='INVOICE')
						{ ?>					
					<input type="submit" value="Set Klaim : SETTLED" />
						<?php }?>						
					</td>
				</tr>	
                <tr>
                    <td colspan="3">&nbsp; </td>
                    <td  style="display:none" class="hit"> Note : <br /> <textarea name="note_rej" cols="50" rows="3"></textarea> </td>
                </tr>	
                <tr>
                    <td colspan="3">&nbsp; </td>
                    <td  align="" class="hit"  style="display:none">  
                        <button name="cancel2" type="button" onclick="cancel()" id=""  value=1>Cancel</button>
                        <input type="hidden" value="0" name="isReject" />
                        <input name="reject" onclick="isReject.value=1" type="submit" value="REJECT Laporan AST" />
                    </td>
                </tr>	
                			
			</table>
			</form>
			<?php 
			//} ?>
		</td>			
	</tr>
</table>
  <?php } ?>
  <script type="text/javascript">
  var count=document.getElementById('count').value
  //$("select[name*=currency]").change(function()
  $("input[name*=price]").blur(function()
			{	//alert('test');
			var name = $(this).attr("name");
			//var index = name.substr(-1);
			var index;
			
			if(name.length > 6) 
				index = name.substr(-2);
				//alert(index);
			else
				index = name.substr(-1);
			//alert (index);
			
            var val = $(this, "option:selected").val();
			var kurs = prompt("Masukan Nilai Kurs saat ini", "Nilai Kurs saat ini");
			//var quan = document.getElementById('quan').value
			var quan = $("input[name*=quantity"+index+"]").val();  
			var price = $("input[name*=price"+index+"]").val();
			//var total= $("input[name*=total]").val();
			
			var jumlah=(kurs*price)*quan;
		    var res=0;
			res=jumlah+ parseFloat($("input[name=total]").val())
		
			$("input[name=jumlah"+index+"]").val(jumlah);
			$("input[name=total]").val(res);
			$("input[name=estimasi]").val(res);
			
			//alert("index:"+index) 
			//alert("quantity:"+quan)    
			//alert("price:"+price) 
			//alert("kurs:"+kurs)      
			//alert("kurs:"+jumlah) 
			
		    });
  
  $("input[name*=price2"+count+"]").blur(function()								 
			{		
			 //alert("test")
			/*	var index = name.substr(-1);
				var nilai = parseFloat($(this).val());
				nilai = isNaN(nilai) ? 0 : nilai;
			    alert(index)
			*/	
			//alert(hasil)
			var i=0;
			var jml=0;
             for (i=1;i<=count;i++)
             {
				//alert(i)
				var jml=jml+ parseFloat($("input[name=price"+i+"]").val())
              }  
			  //alert("jumlah="+jml)
			 $("input[name=estimasi]").val(jml);
			
		});
</script>

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