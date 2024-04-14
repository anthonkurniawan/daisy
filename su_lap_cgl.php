<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
$_SESSION['gets'] = $_GET; 
?>
<table width="<?=in_array($_GET['l'],array(1,2))?'1800':'1000'?>" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto;" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:200px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menusuper.php" ?>
			</ul>
		</td>
		<td>
			<h3>Membuat Laporan Klaim CGL</h3>
			<form method="get" action="" style="border:1px solid #ccc;padding:3px;background:#efefef">
				<table>
					<tr>
						<td>Laporan:</td>
						<td>
							<script>
							function cekOth(obj){
								if(obj.l.value=='3'){
									obj.s1.disabled=true;
									obj.s2.disabled=true;
								}else{
									obj.s1.disabled=false;
									obj.s2.disabled=false;
								}
							}
							</script>
							<select name="l"  onchange="cekOth(this.form)">
								<option value="1" <?=($_GET['l']=='1'?'selected="selected"':'')?>>Rekapitulasi Klaim CGL</option>
								<option value="2" <?=($_GET['l']=='2'?'selected="selected"':'')?>>Detail Progress CGL</option>
								<option value="3" <?=($_GET['l']=='3'?'selected="selected"':'')?>>Summary Report Klaim CGL</option>
							</select>
						</td>
					</tr><tr></tr>
					<tr>
						<td>Regional:</td>
						<td>
							<select name="r">
								<option <?=($_GET['r']==$r->kode_region?'selected="selected"':'')?> value="">ALL (NASIONAL)</option>
								<?php
									$res = $db->get_results("SELECT * FROM `region` ORDER BY kode_region ASC");
									foreach($res as $r){ ?>
									<option <?=($_GET['r']==$r->kode_region?'selected="selected"':'')?> value="<?=$r->kode_region?>"><?=$r->region?></option>
									<?php }
								?>
							</select>
						</td>
					</tr><tr></tr>
					<tr>
						<td>Periode: </td>
						<td>
							<select name="p">
								<option <?=($_GET['p']=='1'?'selected="selected"':'')?> value="1">31/Des/10 - 30/Jun/12 [No. Polis: 202.718.300.10.00020/000/000]</option>
								<option <?=($_GET['p']=='2'?'selected="selected"':'')?> value="2">2/Ags/10 - 31/Des/10 [No. Polis: 202.718.300.10.00012/000/000]</option>
							</select>							
						</td>
					</tr><tr></tr>
					<tr>
						<td>Bulan: </td>
						<td>
							<select name="m1">
								<?php for($i=1;$i<=12;$i++):?>
								<option value="<?=$i?>" <?=($_GET['m1']==$i?'selected="selected"':'')?>><?=$months[$i-1]?></option>
								<?php endfor; ?>
								</select> 
		
							<select name="t1">
								<?php for($i=2003;$i<=2012;$i++){ ?>
								<option value="<?=$i?>" <?=($_GET['t1']==$i ||($_GET['t1']==''&&date("Y")==$i) ?'selected="selected"':'')?>><?=$i?></option>
								<?php } ?>
							</select>
							-					
							<select name="m2">
								<?php for($i=1;$i<=12;$i++):?>
								<option value="<?=$i?>" <?=($_GET['m2']==$i?'selected="selected"':'')?>><?=$months[$i-1]?></option>
								<?php endfor; ?>
							</select> 
							
							<select name="t2">
								<?php for($i=2003;$i<=2012;$i++){ ?>
								<option value="<?=$i?>" <?=($_GET['t1']==$i ||($_GET['t1']==''&&date("Y")==$i) ?'selected="selected"':'')?>><?=$i?></option>
								<?php } ?>
							</select>
							
						</td>
					</tr><tr></tr>
					<tr>
						<td>Urut berdasarkan:</td>						
						<td>
							#1 <select name="s1">
								<option value="" <?=($_GET['s1']==''?'selected="selected"':'')?>>No. Laporan</option>
								<option value="1" <?=($_GET['s1']=='1'?'selected="selected"':'')?>>Site ID</option>
								<option value="2" <?=($_GET['s1']=='2'?'selected="selected"':'')?>>Regional</option>
								<option value="3" <?=($_GET['s1']=='3'?'selected="selected"':'')?>>Tanggal Lapor SJU</option>
								<option value="4" <?=($_GET['s1']=='4'?'selected="selected"':'')?>>Tanggal Kejadian</option>
								<option value="5" <?=($_GET['s1']=='5'?'selected="selected"':'')?>>Tanggal Diketahui</option>
								<option value="6" <?=($_GET['s1']=='6'?'selected="selected"':'')?>>Status Klaim</option>
							</select>
							#2 <select name="s2">
								<option value="" <?=($_GET['s2']==''?'selected="selected"':'')?>>No. Laporan</option>
								<option value="1" <?=($_GET['s2']=='1'?'selected="selected"':'')?>>Site ID</option>
								<option value="2" <?=($_GET['s2']=='2'?'selected="selected"':'')?>>Regional</option>
								<option value="3" <?=($_GET['s2']=='3'?'selected="selected"':'')?>>Tanggal Lapor SJU</option>
								<option value="4" <?=($_GET['s2']=='4'?'selected="selected"':'')?>>Tanggal Kejadian</option>
								<option value="5" <?=($_GET['s2']=='5'?'selected="selected"':'')?>>Tanggal Diketahui</option>
								<option value="6" <?=($_GET['s2']=='6'?'selected="selected"':'')?>>Status Klaim</option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="2"><input type="submit" value="Create" /> 
							<?php if(isset($_GET['l']) && $_GET['l']){ ?>
							<input type="button" value="Print" onclick="window.open ('printReportCGL.php?l=<?=$_GET['l']?>&r=<?=$_GET['r']?>&p=<?=$_GET['p']?>&t1=<?=$_GET['t1']?>&t2=<?=$_GET['t2']?>&no=<?=$_GET['no']?>&s1=<?=$_GET['s1']?>&s2=<?=$_GET['s2']?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
							<input type="button" value="Export to Excel" onclick="window.open('excelLapCgl.php','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=300,height=100');" />
							<input type="button" value="Export ke Excel 2003 (.xls)" onclick="window.open ('su_lap_cgl_excel.php?f=xls','daisy<?=rand()?>','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
							<input type="button" value="Export ke Excel 2007 (.xlsx)" onclick="window.open ('su_lap_cgl_excel.php?f=xlsx','daisy<?=rand()?>','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
							<?php } ?>
						</td>
					</tr>
				</table>
			</form> 			
			<?php
				if($_GET['p']=='1') 
					$and .=" AND created_at BETWEEN '".($_GET['t1']-1)."-12-01 00:00:00' AND '".$_GET['t2']."-06-30 23:59:59'";
				else
					$and .=" AND created_at BETWEEN '".$_GET['t1']."-08-02 00:00:00' AND '".$_GET['t2']."-12-31 23:59:59'";
				
 				if($_GET['r']<>''){
					$and.= " AND kode_region='".$_GET['r']."'";
					$rx = $db->get_row("SELECT * FROM region WHERE kode_region='".$_GET['r']."'");
					$status.= " Regional <strong>".$rx->region."</strong>";
				} 
				
				switch($_GET['s1']){
					case '1':$order.=" site.st_site_id ASC,";break;
					case '2':$order.=" region ASC ASC,";break;
					case '3':$order.=" submit_at DESC,";break;
					case '4':$order.=" tgl_kejadian DESC,";break;
					case '5':$order.=" tgl_tuntutan DESC,";break;
					case '6':$order.=" st ASC,";break;
				}
				switch($_GET['s2']){
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
				if($_GET['l']!='3') $cgl = $db->get_results($SQL1);
			?>
			<?php if($_GET['l']=='1'){ ?>
			<br /><strong>REKAPITULASI KLAIM CGL PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>''){
					$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
					echo $reg->region;
				}else echo 'NASIONAL';
			?><br />
			TAHUN: <?=$_GET['t']?> (periode Polis <?=$_GET['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'?>)<br />
			NO. POLIS: <?=$_GET['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000'?></strong><br />
			<table cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
				<tr>
					<th nowrap="nowrap"  rowspan="2">No.</th>
					<th nowrap="nowrap"  rowspan="2">Nomor Laporan</th>
					<th nowrap="nowrap"  rowspan="2">Site Name</th>
					<th nowrap="nowrap"  rowspan="2">Site ID</th>
					<th nowrap="nowrap"  rowspan="2">Regional</th>
					<th nowrap="nowrap"  rowspan="2">Tanggal Lapor SJU</th>
					<th nowrap="nowrap"  rowspan="2">Tanggal kejadian</th>
					<th nowrap="nowrap"  rowspan="2">Tanggal diketahui</th>
					<th nowrap="nowrap"  rowspan="2">Penyebab Kerugian</th>
					<th nowrap="nowrap"  rowspan="2">Estimasi Kerugian (BoQ)</th>
					<th nowrap="nowrap"  rowspan="2">Nilai ganti rugi (Invoice)</th>
					<th nowrap="nowrap"  rowspan="2">Vendor Pelaksana</th>
					<th nowrap="nowrap"  rowspan="2">Status Klaim</th>
					<th nowrap="nowrap"  colspan="3">Dokumen Pendukung</th>
				</tr>
				<tr>
					<th nowrap="nowrap" >Surat<br />Tuntutan Warga</th>
					<th nowrap="nowrap" >Dokumen<br />BoQ</th>
					<th nowrap="nowrap" >Surat<br />Tuntutan Telkomsel</th>
				</tr>
				<?php 				
				$i=1;foreach($cgl as $c):?>
				<tr class="<?=$i%2==0?'odd':'even'?>">
					<td><?=$i?>.</td>
					<td nowrap="nowrap"><?=$c->no_laporan?></td>
					<td><?=$c->st_name?></td>
					<td><?=$c->st_site_id?></td>
					<td><?=$c->region?></td>
					<td><?=$c->submit_at?date("d/m/Y",strtotime($c->submit_at)):''?></td>
					<td><?=($c->tgl_kejadian<>''&&$c->tgl_kejadian<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_kejadian)):'')?></td>
					<td><?=($c->tgl_tuntutan<>''&&$c->tgl_tuntutan<>'0000-00-00'?date("d/m/Y",strtotime($c->tgl_tuntutan)):'')?></td>
					<td><?=$c->sebab?> <?=$c->rincian?></td>
					<td><?=$c->estimasi?></td>
					<td><?=$c->nilai_invoice?></td>
					<td><?=$c->nama_vendor?></td>
					<td><?=$c->status?></td>
					<td><?=$c->file_surat_tuntutan!=''?'ADA':'BELUM ADA'?></td>
					<td><?=$c->file_boq!=''?'ADA':'BELUM ADA'?></td>
					<td><?=$c->file_invoice!=''?'ADA':'BELUM ADA'?></td>
				</tr>
				<?php $i++;endforeach;?>
			</table>
			<?php } ?>
            
			<?php if($_GET['l']=='2'){ ?>
			<br /><strong>REPORT DETAIL PROGRESS CGL PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>''){
					$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
					echo $reg->region;
				}else echo 'NASIONAL';
			?><br />
			TAHUN: <?=$_GET['t']?> (periode Polis <?=$_GET['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'?>)<br />
			NO. POLIS: <?=$_GET['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000'?></strong><br />
			<table width="100%" cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
				<tr>
					<th nowrap="nowrap"  rowspan="2">No.</th>
					<th nowrap="nowrap"  rowspan="2">Nomor Laporan</th>
					<th nowrap="nowrap"  rowspan="2">Site Name</th>
					<th nowrap="nowrap"  rowspan="2">Site ID</th>
					<th nowrap="nowrap"  rowspan="2">Regional</th>
					<th nowrap="nowrap"  rowspan="2">Tanggal Lapor SJU</th>
					<th nowrap="nowrap"  rowspan="2">Tanggal kejadian</th>
					<th nowrap="nowrap"  rowspan="2">Tanggal diketahui</th>
					<th nowrap="nowrap"  rowspan="2">Penyebab Kerugian</th>
					<th nowrap="nowrap"  rowspan="2">Estimasi Kerugian (BoQ)</th>
					<th nowrap="nowrap"  rowspan="2">Nilai ganti rugi (Invoice)</th>
					<th nowrap="nowrap"  rowspan="2">Vendor Pelaksana</th>
					<th nowrap="nowrap"  rowspan="2">Status Klaim</th>
					<th nowrap="nowrap"  rowspan="2">Dokumen BoQ</th>
					<th nowrap="nowrap"  colspan="8">Timeline Progress</th>
				</tr>
				<tr>
					<th nowrap="nowrap" >Create</th>
					<th nowrap="nowrap" >Approve</th>
					<th nowrap="nowrap" >Submit</th>
					<th nowrap="nowrap" >Survey</th>
					<th nowrap="nowrap" >Payment</th>
					<th nowrap="nowrap" >Claim Letter</th>
					<th nowrap="nowrap" >Settled</th>
					<th nowrap="nowrap" >Closed</th>
				</tr>
				<?php $i=1;foreach($cgl as $c):?>
				<tr class="<?=$i%2==0?'odd':'even'?>">
					<td><?=$i?>.</td>
					<td nowrap="nowrap"><?=$c->no_laporan?></td>
					<td><?=$c->st_name?></td>
					<td><?=$c->st_site_id?></td>
					<td><?=$c->region?></td>
					<td><?=$c->submit_at?></td>
					<td><?=date("d/m/Y",strtotime($c->tgl_kejadian))?></td>
					<td><?=date("d/m/Y",strtotime($c->tgl_tuntutan))?></td>
					<td><?=$c->sebab?> <?=$c->rincian?></td>
					<td><?=$c->estimasi?></td>
					<td><?=$c->nilai_invoice?></td>
					<td><?=$c->nama_vendor?></td>					
					<td><?=$c->status?></td>
					<td><?=$c->file_boq<>''?'ADA':'BELUM ADA'?></td>
					<td><?=$c->created_at?date("d/m/Y",strtotime($c->created_at)):''?></td>
					<td><?=$c->approve_at<>''&&$c->approve_at<>'0000-00-00 00:00:00'?date("d/m/Y",strtotime($c->approve_at)):''?></td>
					<td><?=$c->submit_at<>''&&$c->submit_at<>'0000-00-00'?date("d/m/Y",strtotime($c->submit_at)):''?></td>
					<td><?=$c->survey_date<>''&&$c->survey_date<>'0000-00-00'?date("d/m/Y",strtotime($c->survey_date.' 00:00:00')):''?></td>
					<td><?=$c->payment_date<>''&&$c->payment_date<>'0000-00-00'?date("d/m/Y",strtotime($c->payment_date.' 00:00:00')):''?></td>
					<td><?=$c->invoice_date<>''&&$c->invoice_date<>'0000-00-00'?date("d/m/Y",strtotime($c->invoice_date.' 00:00:00')):''?></td>
					<td><?=$c->settled_date<>''&&$c->settled_date<>'0000-00-00'?date("d/m/Y",strtotime($c->settled_date.' 00:00:00')):''?></td>
					<td><?=$c->caseclosed_at<>''&&$c->caseclosed_at<>'0000-00-00'?date("d/m/Y",strtotime($c->caseclosed_at)):''?></td>
				</tr>
				<?php $i++;endforeach;?>
			</table>
			<?php } ?>
            
            
			<?php if($_GET['l']=='3'){ 
			$SQL3  = "SELECT COUNT(1) jml, `status`,EXTRACT(MONTH FROM `created_at`) bln FROM cgl 
					JOIN cgl_vendor v ON v.id_cglv=cgl.id_cglv
					WHERE 1 ".$and." 
					GROUP BY `status`, bln";
			$res3 = $db->get_results($SQL3);
			$summ = array();
			foreach($res3 as $r3){
				$summ[$r3->status][$r3->bln] = $r3->jml>0?$r3->jml:'0';
			}
			?>
			<br /><strong>SUMMARY REPORT KLAIM CGL PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>''){
					$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
					echo $reg->region;
				}else echo 'NASIONAL';
			?><br />
			TAHUN: <?=$_GET['t']?> (periode Polis <?=$_GET['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'?>)<br />
			NO. POLIS: <?=$_GET['p']=='1'?'202.718.300.10.00020/000/000':'202.718.300.10.00012/000/000'?></strong><br />
			<table cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
				<tr>
					<th rowspan="2">Status</th>
					<th colspan="12">Bulan</th>
				</tr>
				<tr>
					<?php foreach($months as $m): ?>
					<th style="width:40px;"><?=$m?></th>
					<?php endforeach; ?>
				</tr>
				<tr class="odd">
					<td>Created</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['UNAPPROVED'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="even">
					<td>Approve</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['APPROVED'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="odd">
					<td>Submitted (Total Klaim)</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['SUBMITTED'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="even">
					<td>Outstanding</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?php $outs= $summ['SURVEY'][$i]+$summ['PAYMENT'][$i]+$summ['INVOICE'][$i]; echo (int)$outs ?></td>
					<?php } ?>
				</tr>
				<tr class="odd">
					<td style="padding-left:50px">Survey</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['SURVEY'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="even">
					<td style="padding-left:50px">Payment</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['PAYMENT'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="odd">
					<td style="padding-left:50px">Invoice</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['INVOICE'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="even">
					<td>Settled</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['SETTLED'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="odd">
					<td>Closed Case</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['CLOSED'][$i] ?></td>
					<?php } ?>
				</tr>
				<tr class="even">
					<td>Total</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=$summ['CLOSED'][$i]+$summ['UNAPPROVED'][$i]+$summ['APPROVED'][$i]+$summ['SUBMITTED'][$i]+$summ['SURVEY'][$i]+$summ['PAYMENT'][$i]+$summ['INVOICE'][$i]+$summ['SETTLED'][$i] ?></td>
					<?php } ?>
				</tr>
			</table>
			<?php } ?>
			</td>
	</tr>
</table>
<?php include "footer.php"?>