<?php
require 'init.php';
include "headerPrint.php";
require 'priviledges.php';?>
<div style="margin:2px;padding:3px;background:#fff;border:1px solid #ccc">
	
	<?php
		$tempThn = '';
		if($_GET['s']<>'' && $_GET['s']<>'0' ) $and .=" AND `status`='{$_GET['s']}'";
		$lastD2 = 30;
		if(in_array($_GET['m2'],array(1,3,5,7,8,10,12))) $lastD2 = 31;
		if($_GET['m2']==2 && $_GET['t2']%4==0) $lastD2 = 29;
		if($_GET['m2']==2 && $_GET['t2']%4!=0) $lastD2 = 28;
		
		switch($_GET['s']){
			case '':
			case '0':
				if($_GET['m1']<>'') $and .=" AND created_at BETWEEN '".$_GET['t1']."-".str_pad($_GET['m1'],2,'0',STR_PAD_LEFT)."-01 00:00:00' AND '".$_GET['t2']."-".str_pad($_GET['m2'],2,'0',STR_PAD_LEFT)."-".$lastD2." 23:59:59'";
			break;
			case 'UNAPPROVED':
			case 'REJECTED':
				$and .=" AND updated_at BETWEEN '".$_GET['t1']."-".str_pad($_GET['m1'],2,'0',STR_PAD_LEFT)."-01 00:00:00' AND '".$_GET['t2']."-".str_pad($_GET['m2'],2,'0',STR_PAD_LEFT)."-".$lastD2." 23:59:59'";
			break;
			case 'APPROVED':
				$and .=" AND approve_at BETWEEN '".$_GET['t1']."-".str_pad($_GET['m1'],2,'0',STR_PAD_LEFT)."-01 00:00:00' AND '".$_GET['t2']."-".str_pad($_GET['m2'],2,'0',STR_PAD_LEFT)."-".$lastD2." 23:59:59'";
			break;
			case 'SUBMITTED':
				$and .=" AND submit_at BETWEEN '".$_GET['t1']."-".str_pad($_GET['m1'],2,'0',STR_PAD_LEFT)."-01 00:00:00' AND '".$_GET['t2']."-".str_pad($_GET['m2'],2,'0',STR_PAD_LEFT)."-".$lastD2." 23:59:59'";
			break;
			case 'SETTLED':
				$and .=" AND settlement_date BETWEEN '".$_GET['t1']."-".str_pad($_GET['m1'],2,'0',STR_PAD_LEFT)."-01 00:00:00' AND '".$_GET['t2']."-".str_pad($_GET['m2'],2,'0',STR_PAD_LEFT)."-".$lastD2." 23:59:59'";
			break;
		}
		
		if($_GET['s']<>'' && $_GET['s']<>'0') $status = 'Status: <strong>'.$_GET['s'].'</strong>';
		elseif($_GET['m1']<>'') $status .= ' yang dibuat ';
		if($_GET['m1']<>'' && $_GET['m1']==$_GET['m2'] && $_GET['t1']==$_GET['t2']) $status .= ' pada bulan <strong>'.$months[$_GET['m1']-1].' '.$_GET['t1'].'</strong>';
		elseif($_GET['m1']<>'') $status .= ' dari bulan <strong>'.$months[$_GET['m1']-1].' '.$_GET['t1'].'</strong> s/d <strong>'.$months[$_GET['m2']-1].' '.$_GET['t2'].'</strong>';
		
		if($user->role=='spvr' || $user->role=='mgrr' ) {
			//$and .= " AND `user_id`='".$user->user_id."' ";			
			$and.= " AND kode_region='".$user->regional."'";
		}elseif($_GET['r']!=''){
			$and.= " AND kode_region='".$_GET['r']."'";
		}

		$SQL = "SELECT ast2.kode_region,region,EXTRACT(YEAR FROM tgl_kejadian) thn,ast2.* FROM `ast2` 
		WHERE 1 ".$and." ORDER BY thn DESC ,`updated_at` DESC";
		$resast = $db->get_results($SQL);
	?>
		
	<?php
				if(is_array($resast)&&!empty($resast))
				{?>
				<h3>Laporan Klaim AST <?=$status?></h3>
				<?php	$i=1;foreach($resast as $ast){ ?>
				<?php if($ast->thn<>$tempThn){ ?>
				<?php $endTable=($ast->thn<>$tempThn && $tempThn<>'')?1:0 ?>
				<?php if($endTable==1){ ?>
				</table>
				<?php } ?>
				<strong>Tahun Kejadian <?=$ast->thn?></strong><br />			
				<table width="98%" class="tabel" border="0" cellpadding="0" cellspacing="0">
					<tr>
						<th rowspan="2">No Laporan</th>
						<th rowspan="2">Status Claim</th>
						<th rowspan="2">Cause</th>
						<th colspan="6">Dokumen-dokumen</th>						
						<th rowspan="2">Progress</th>
                        <th rowspan="2">Status Klaim</th>					
					</tr>
					<tr>
						<th>Lap. Awal</th>
						<th>BA Kerugian</th>
						<th>BA Kronologis</th>
						<th>Foto</th>
						<th>Rincian Kerusakan</th>						
						<th>Dokumen khusus</th>							
					</tr>
				      <?php	
				         $sebab= $ast->sebab;
						 function sebab($sebab)
						 { 
						   if($sebab=='nds') echo 'Natural Dissaster (Bencana Alam)';
						   elseif ($sebab=='thf') { echo 'Theft (Pencurian)';}
						   elseif ($sebab=='lit') { echo 'Lightning (Petir)';}
						   elseif ($sebab=="etv") { echo "Earthquake, Tsunami, Volcano Erruption";}
						   elseif ($sebab=='fre') { echo 'Fire (Terbakar/ Kebakaran)';}
						   elseif ($sebab=='trp') { echo 'Third Party (Tuntutan Pihak ketiga)';}
						   else { echo 'Other Losses (Lainnya..)';}
						 } 
									 
				         $status_c=$ast->status_progress;
				         function status($status_c)
						 {
				          if ($status_c==0){ echo "<B> - </B>";}
				          elseif ($status_c==1){ echo "<B> OUTSTANDING </B>";}
				          elseif ($status_c==2){ echo "<B> UNDER DEDUCTIBLE </B>";}
				          else{ echo "<B> SETTLEMENT </B>";}
				         }
					  ?>  
					
          <tr class="<?=($i%2==0?'even':'odd')?>">
						<td nowrap="nowrap"><?=$ast->no_laporan?></td>
						<td><?=$ast->status_claim=='total'?'Totally':'Partial'?> lost</td>
						<td nowrap> <?php $sebab= $ast->sebab; sebab($sebab); ?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_lap=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_hil=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_kro=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_fo=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_rinci=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap">   
						 <?php 
						   if($ast->sebab=='fre') { echo $ast->doc_pmk=='0000-00-00'?'Tidak Ada':'OK';}
						   else if ($sebab=='nds'||$sebab=='lit'||$sebab=="etv") { echo $ast->doc_bmkg=='0000-00-00'?'Tidak Ada':'OK';}
						   else if ($sebab=='thf'||$sebab=='rio'||$sebab=='trp') { echo $ast->doc_pol=='0000-00-00'?'Tidak Ada':'OK';}
						   else { echo "Tidak ada";} ?>
                        </td>
                        <td style="text-align:center"><?=$ast->status?></td>
                        <td style="text-align:center">
			            <?php 
			             //if($ast->status=='UNAPPROVED'||$ast->status=='APPROVED'||$ast->status=='SUBMITTED')
			             //{echo "<B> OUTSTANDING </B>";} 
			             status($status_c); ?>
                        </td>
					</tr>
          <?php 
					}
					else
					{ ?>
          <tr class="<?=($i%2==0?'even':'odd')?>">
						<td nowrap="nowrap"><?=$ast->no_laporan?></td>
						<td><?=$ast->status_claim=='total'?'Totally':'Partial'?> lost</td>
						<td>
						<?php 
						 $sebab= $ast->sebab; sebab($sebab); ?>
						</td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_lap=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_hil=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_kro=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_fo=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap"><?=$ast->doc_rinci=='0000-00-00'?'Tidak Ada':'OK'?></td>
						<td align="center" nowrap="nowrap">   
						   <?php 
						   if($ast->sebab=='fre') { echo $ast->doc_pmk=='0000-00-00'?'Tidak Ada':'OK';}
						   else if ($sebab=='nds'||$sebab=='lit'||$sebab=="etv") { echo $ast->doc_bmkg=='0000-00-00'?'Tidak Ada':'OK';}
						   else if ($sebab=='thf'||$sebab=='rio'||$sebab=='trp') { echo $ast->doc_pol=='0000-00-00'?'Tidak Ada':'OK';}
						   else { echo "Tidak ada";} ?>
            </td>
            <td style="text-align:center"><?=$ast->status?></td>
            <td style="text-align:center">
			        <?php 
              $status_c=$ast->status_progress;
			        status($status_c);
			        ?>
            </td>
		      </tr>
					<?php } ?>
					<?php $tempThn=$ast->thn;$i++;} ?>
				<?php
				}else{
					echo 'No data found';
				}
			?>
		</td>
	</tr>
</table>
</div>
<?php include "footer.php"?>