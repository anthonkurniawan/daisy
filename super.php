<?php
require 'init.php';
require 'priviledges.php';
include "header.php"
?>
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
	<?php include "webheader.php"; ?>
    
	<tr valign="top">
		<td style="width:250px" rowspan="3">
			<?php include "menusuper.php" ?>			
		</td>
        
		<td>
		<h2>SUMMARY PROGRESS REPORT CGL</h2>
			<?php //$res = $db->get_row("SELECT (SELECT COUNT(1) FROM cgl) AS total,(SELECT COUNT(1) FROM cgl WHERE `status`='APPROVED') AS approved,(SELECT COUNT(1) FROM cgl WHERE `status`='SUBMITTED') AS submitted, (SELECT COUNT(1) FROM cgl WHERE `status`='SURVEY') AS survey,(SELECT COUNT(1) FROM cgl WHERE `status`='PAYMENT') AS payment,(SELECT COUNT(1) FROM cgl WHERE `status`='INVOICE') AS invoice,(SELECT COUNT(1) FROM cgl WHERE `status`='SETTLED') AS settled,(SELECT COUNT(1) FROM cgl WHERE `status`='CLOSED') AS caseclosed "); ?>Per Tanggal: <?=date("d-m-Y")?> <br />
			<?php $res = $db->get_results("SELECT COUNT(1) AS jml,`status` FROM cgl GROUP BY `status`"); 
			foreach($res as $aRes){
				$aStatus[$aRes->status] = $aRes->jml;
				$total += $aRes->jml;
			}
			?>
			Regional : (<?=$user->regional?>) <?php $r = $db->get_row("SELECT `region` FROM region WHERE kode_region='".$user->regional."'");echo $r->region ?>
			<br /><br />
			<table border="5" style="tabel">
				<tr>
					<td bgcolor="grey"><strong>Status Laporan</strong></td>
					<td bgcolor="grey" colspan="5"><strong>Jumlah</strong></td>
				</tr><tr></tr>
				<tr>
					<td colspan="4">Total laporan: </td>
					<td colspan="2" style="text-align:right"><?=$total ?></td>
				</tr><tr></tr>

				<tr>
					<td colspan="3" style="padding-left:30px;">1. Laporan dibuat (belum approved): </td>
					<td style="text-align:right"><?=$aStatus['UNAPPROVED']?$aStatus['UNAPPROVED']:0 ?></td>
				</tr><tr></tr>
				<tr>
					<td colspan="3" style="padding-left:30px;">2. Laporan approved (belum disubmit):</td>
					<td style="text-align:right"><?=$aStatus['APPROVED']?$aStatus['APPROVED']:0 ?></td>
				</tr>
				<tr>
					<td colspan="3" style="padding-left:30px;">3. Submitted klaim:</td>
					<td colspan="1" style="text-align:right"><?=$aStatus['SUBMITTED']?></td>
				</tr><tr></tr>
				<tr>
					<td colspan="2" style="padding-left:60px;">3.1 Laporan Outstanding:</td>
					<td nowrap="nowrap" style="text-align:right"><?=($aStatus['SUBMITTED']+$aStatus['SURVEY']+$aStatus['PAYMENT']+$aStatus['INVOICE']+1)?></td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.1.1 Belum Survey: </td>
					<td style="text-align:right"><?=$aStatus['SUBMITTED']?></td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.1.2 Survey: </td>
					<td style="text-align:right"><?=$aStatus['SURVEY']?></td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.1.3 Payment to community: </td>
					<td style="text-align:right"><?=$aStatus['PAYMENT'] ?> </td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.1.3 Invoice to Insurance company: </td>
					<td style="text-align:right"><?=($aStatus['INVOICE']+1) ?> </td>
				</tr><tr></tr>
				<tr>
					<td colspan="2" style="padding-left:60px;">3.2 Laporan yang telah dibayar (Settled): </td>
					<td style="text-align:right"><?=$aStatus['SETTLED'] ?> </td>
				</tr><tr></tr>
				<tr>
					<td colspan="2" nowrap="nowrap" style="padding-left:60px;">3.3 Laporan yang dibatalkan (Closed): </td>
					<td style="text-align:right"><?=$aStatus['CLOSED'] ?> </td>
				</tr>
			</table>
			&nbsp
			<?php
			$SQL ="SELECT DATEDIFF(NOW(),submit_at) outstanding FROM cgl  WHERE `status` IN ('SUBMITTED','SURVEY','PAYMENT','INVOICE')";
			$res = $db->get_results($SQL); 
			$jmlo=0;
			foreach($res as $r){
				if($r->outstanding<=30) $o[1]++;
				if($r->outstanding>=31 && $r->outstanding<=60) $o[2]++;
				if($r->outstanding>=61 && $r->outstanding<=90) $o[3]++;
				if($r->outstanding>=91 && $r->outstanding<=120) $o[4]++;
				if($r->outstanding>120) $o[5]++;
				$jmlo++;
			}
			
			$SQL ="SELECT DATEDIFF(settlement_date,submit_at) settled FROM cgl WHERE `status`='SETTLED'";
			$res = $db->get_results($SQL); 
			$jmls=0;
			foreach($res as $r){
				if($r->settled<=30) $s[1]++;
				if($r->settled>=31 && $r->settled<=60) $s[2]++;
				if($r->settled>=61 && $r->settled<=90) $s[3]++;
				if($r->settled>=91 && $r->settled<=120) $s[4]++;
				if($r->settled>120) $s[5]++;
				$jmls++;
			}
			
			$SQL ="SELECT DATEDIFF(caseclosed_at,submit_at) closed FROM cgl WHERE `status`='CLOSED'";
			$res = $db->get_results($SQL); 
			$jmlc=0;
			foreach($res as $r){
				if($r->closed<=30) $c[1]++;
				if($r->closed>=31 && $r->closed<=60) $c[2]++;
				if($r->closed>=61 && $r->closed<=90) $c[3]++;
				if($r->closed>=91 && $r->closed<=120) $c[4]++;
				if($r->closed>120) $c[5]++;
				$jmlc++;
			}
			
			
			?>

			<table border="5" style="tabel">
				<tr class="even">
					<td bgcolor="grey"><strong>Aging class</strong></td>
					<td bgcolor="grey"><strong>Outstanding</strong></td>
					<td bgcolor="grey"><strong>Settled</strong></td>
					<td bgcolor="grey"><strong>Closed</strong></td>
					<td bgcolor="grey"><strong>Total</strong></td>
				</tr>
				<tr class="odd">
					<td>0-30: </td>
					<td style="text-align:center"><?=$o[1]?></td>
					<td style="text-align:center"><?=$s[1]?></td>
					<td style="text-align:center"><?=$c[1]?></td>
					<td style="text-align:center"><?=($o[1]+$s[1]+$c[1])?></td>
				</tr><tr></tr>
				<tr class="even">
					<td>31-60: </td>
					<td style="text-align:center"><?=$o[2]?></td>
					<td style="text-align:center"><?=$s[2]?></td>
					<td style="text-align:center"><?=$c[2]?></td>
					<td style="text-align:center"><?=($o[2]+$s[2]+$c[2])?></td>
				</tr><tr></tr>

				<tr class="odd">
					<td>61-90: </td>
					<td style="text-align:center"><?=$o[3]?></td>
					<td style="text-align:center"><?=$s[3]?></td>
					<td style="text-align:center"><?=$c[3]?></td>
					<td style="text-align:center"><?=($o[3]+$s[3]+$c[3])?></td>
				</tr><tr></tr>

				<tr class="even">
					<td>91-120: </td>
					<td style="text-align:center"><?=$o[4]?></td>
					<td style="text-align:center"><?=$s[4]?></td>
					<td style="text-align:center"><?=$c[4]?></td>
					<td style="text-align:center"><?=($o[4]+$s[4]+$c[4])?></td>
				</tr><tr></tr>

				<tr class="odd">
					<td>121-: </td>
					<td style="text-align:center"><?=$o[5]?></td>
					<td style="text-align:center"><?=$s[5]?></td>
					<td style="text-align:center"><?=$c[5]?></td>
					<td style="text-align:center"><?=($o[5]+$s[5]+$c[5])?></td>
				</tr><tr></tr>

				<tr class="even">
					<td bgcolor="green" >Total:</td>
					<td bgcolor="green" style="text-align:center"><?=$jmlo?></td>
					<td bgcolor="green" style="text-align:center"><?=$jmls?></td>
					<td bgcolor="green" style="text-align:center"><?=$jmlc?></td>
					<td bgcolor="green" style="text-align:center"><?=($jmlo+$jmls+$jmlc)?></td>
				</tr>
                
			</table><p>
		</td>
	</tr>
    
    <!--------------------------------- SUMMARY PROGRESS REPORT AST ------------------------------------------------->
    <tr> <td> <p><p> <hr color="#000000" /> <hr color="#000000" /> </td> </tr>
    <tr valign="top">
		
		<td>
		<h2>SUMMARY PROGRESS REPORT AST</h2>
			<?php //$res = $db->get_row("SELECT (SELECT COUNT(1) FROM cgl) AS total,(SELECT COUNT(1) FROM cgl WHERE `status`='APPROVED') AS approved,(SELECT COUNT(1) FROM cgl WHERE `status`='SUBMITTED') AS submitted, (SELECT COUNT(1) FROM cgl WHERE `status`='SURVEY') AS survey,(SELECT COUNT(1) FROM cgl WHERE `status`='PAYMENT') AS payment,(SELECT COUNT(1) FROM cgl WHERE `status`='INVOICE') AS invoice,(SELECT COUNT(1) FROM cgl WHERE `status`='SETTLED') AS settled,(SELECT COUNT(1) FROM cgl WHERE `status`='CLOSED') AS caseclosed "); ?>Per Tanggal: <?=date("d-m-Y")?> <br />
			<?php 
			$res_est = $db->get_results("SELECT COUNT( 1 ) AS jml FROM ast2 WHERE estimasi <> 'NULL' "); 
			foreach($res_est as $es) 
			{ $esti=$es->jml;}
			
			$resast = $db->get_results("SELECT COUNT(1) AS jml,`status` FROM ast2 GROUP BY `status`"); 
			
			foreach($resast as $aRes2){
				$aStatus2[$aRes2->status] = $aRes2->jml;
				$total2 += $aRes2->jml;
			}
			?>
			Regional : (<?=$user->regional?>) <?php $r = $db->get_row("SELECT `region` FROM region WHERE kode_region='".$user->regional."'");echo $r->region ?>
			<br /><br />
			<table border="1" style="tabel">
				<tr>
					<td><strong>Status Laporan</strong></td>
					<td colspan="4"><strong>Jumlah</strong></td>
				</tr><tr></tr>
				<tr>
					<td colspan="4">Total laporan: </td>
					<td style="text-align:right"><?=$total2?></td>
				</tr><tr></tr>
				<tr>
					<td colspan="3" style="padding-left:30px;">1. Laporan dibuat (belum approved): </td>
					<td style="text-align:right"><?=$aStatus2['UNAPPROVED']?$aStatus2['UNAPPROVED']:0 ?></td>
				</tr><tr></tr>
				<tr>
					<td colspan="3" style="padding-left:30px;">2. Laporan approved (belum disubmit):</td>
					<td style="text-align:right"><?=$aStatus2['APPROVED']?$aStatus2['APPROVED']:0 ?></td>
				</tr>
                <tr>
					<td colspan="3" style="padding-left:30px;">3. Submitted klaim:</td>
					<td style="text-align:right"><?=$aStatus2['SUBMITTED']?$aStatus2['SUBMITTED']:0 ?></td>
				</tr><tr></tr>
				<tr>
					<td colspan="2" style="padding-left:60px;">3.1 Laporan Outstanding:</td>
					<td nowrap="nowrap" style="text-align:right"><?=($aStatus2['UNAPPROVED']+$aStatus2['APPROVED']+$aStatus2['SUBMITTED'])>0?($aStatus2['UNAPPROVED']+$aStatus2['APPROVED']+$aStatus2['SUBMITTED']):0?></td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.1 Estimasi & Dokumen : </td>
					<td style="text-align:right"><?=$es->jml?></td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.2 Proposed Adjustment 1 :</td>
					<td style="text-align:right"><?=$rec['SURVEY']?$rec['SURVEY']:0?></td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.3 Konfirmasi Adjustment 1 : </td>
					<td style="text-align:right"><?=$rec['PAYMENT']?$rec['PAYMENT']:0 ?> </td>
				</tr><tr></tr>
				<tr>
					<td style="padding-left:90px;">3.4 Proposed Adjustment 2 : </td>
					<td style="text-align:right"><?=$rec['INVOICE']?$rec['INVOICE']:0 ?> </td>
				</tr><tr></tr>
				<tr>
                <tr>
					<td style="padding-left:90px;">3.5 Konfirmasi Adjustment 2 : </td>
					<td style="text-align:right"><?=$rec['INVOICE']?$rec['INVOICE']:0 ?> </td>
				</tr><tr></tr>
				<tr>
					<td colspan="3" style="padding-left:30px;">4. Settlement :</td>
					<td style="text-align:right"><?=$rec2['SETTLED']?$rec2['SETTLED']:0 ?></td>
				</tr>
			</table>			&nbsp
			<?php
			$SQL2 ="SELECT DATEDIFF(NOW(),submit_at) outstanding FROM ast2  WHERE `status` IN ('SUBMITTED','SURVEY','PAYMENT','INVOICE')";
			$res2 = $db->get_results($SQL2); 
			$jmlo2=0;
			foreach($res2 as $r2){
				if($r2->outstanding<=30) $o2[1]++;
				if($r2->outstanding>=31 && $r2->outstanding<=60) $o2[2]++;
				if($r2->outstanding>=61 && $r2->outstanding<=90) $o2[3]++;
				if($r2->outstanding>=91 && $r2->outstanding<=120) $o2[4]++;
				if($r2->outstanding>120) $o2[5]++;
				$jmlo2++;
			}
			
			$SQL2 ="SELECT DATEDIFF(settlement_date,submit_at) settled FROM ast2 WHERE `status`='SETTLED'";
			$res2 = $db->get_results($SQL2); 
			$jmls2=0;
			foreach($res2 as $r2){
				if($r2->settled<=30) $s2[1]++;
				if($r2->settled>=31 && $r2->settled<=60) $s2[2]++;
				if($r2->settled>=61 && $r2->settled<=90) $s2[3]++;
				if($r2->settled>=91 && $r2->settled<=120) $s2[4]++;
				if($r2->settled>120) $s2[5]++;
				$jmls2++;
			}
			
			$SQL2 ="SELECT DATEDIFF(caseclosed_at,submit_at) closed FROM cgl WHERE `status`='CLOSED'";
			$res2 = $db->get_results($SQL2); 
			$jmlc2=0;
			foreach($res2 as $r2){
				if($r2->closed<=30) $c2[1]++;
				if($r2->closed>=31 && $r2->closed<=60) $c2[2]++;
				if($r2->closed>=61 && $r2->closed<=90) $c2[3]++;
				if($r2->closed>=91 && $r2->closed<=120) $c2[4]++;
				if($r2->closed>120) $c2[5]++;
				$jmlc2++;
			}
			
			
			?>

			<table border="5" style="tabel">
				<tr class="even">
					<td bgcolor="grey"><strong>Aging class</strong></td>
					<td bgcolor="grey"><strong>Outstanding</strong></td>
					<td bgcolor="grey"><strong>Settled</strong></td>
					<td bgcolor="grey"><strong>Closed</strong></td>
					<td bgcolor="grey"><strong>Total</strong></td>
				</tr>
				<tr class="odd">
					<td>0-30: </td>
					<td style="text-align:center"><?=$o2[1]?></td>
					<td style="text-align:center"><?=$s2[1]?></td>
					<td style="text-align:center"><?=$c2[1]?></td>
					<td style="text-align:center"><?=($o2[1]+$s2[1]+$c2[1])?></td>
				</tr><tr></tr>
				<tr class="even">
					<td>31-60: </td>
					<td style="text-align:center"><?=$o2[2]?></td>
					<td style="text-align:center"><?=$s2[2]?></td>
					<td style="text-align:center"><?=$c2[2]?></td>
					<td style="text-align:center"><?=($o2[2]+$s2[2]+$c2[2])?></td>
				</tr><tr></tr>

				<tr class="odd">
					<td>61-90: </td>
					<td style="text-align:center"><?=$o2[3]?></td>
					<td style="text-align:center"><?=$s2[3]?></td>
					<td style="text-align:center"><?=$c2[3]?></td>
					<td style="text-align:center"><?=($o2[3]+$s2[3]+$c2[3])?></td>
				</tr><tr></tr>

				<tr class="even">
					<td>91-120: </td>
					<td style="text-align:center"><?=$o2[4]?></td>
					<td style="text-align:center"><?=$s2[4]?></td>
					<td style="text-align:center"><?=$c2[4]?></td>
					<td style="text-align:center"><?=($o2[4]+$s2[4]+$c2[4])?></td>
				</tr><tr></tr>

				<tr class="odd">
					<td>121-: </td>
					<td style="text-align:center"><?=$o2[5]?></td>
					<td style="text-align:center"><?=$s2[5]?></td>
					<td style="text-align:center"><?=$c2[5]?></td>
					<td style="text-align:center"><?=($o2[5]+$s2[5]+$c2[5])?></td>
				</tr><tr></tr>

				<tr class="even">
					<td bgcolor="green" >Total:</td>
					<td bgcolor="green" style="text-align:center"><?=$jmlo2?></td>
					<td bgcolor="green" style="text-align:center"><?=$jmls2?></td>
					<td bgcolor="green" style="text-align:center"><?=$jmlc2?></td>
					<td bgcolor="green" style="text-align:center"><?=($jmlo2+$jmls2+$jmlc2)?></td>
				</tr>
			</table>
		</td>
	</tr>

</table>
<?php include "footer.php"?>