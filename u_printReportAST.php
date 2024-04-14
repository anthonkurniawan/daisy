<?php
require 'init.php';
include "headerPrint.php"?>
<div style="margin:2px;padding:3px;background:#fff;border:1px solid #ccc">
	 <?php
/*	 echo "get_p =".$_GET['p']."</br>";
	 echo "get_k =".$_GET['k']."</br>";
	 echo "get_r =".$_GET['r']."</br>";
	 echo "get_s1 =".$_GET['s1']."</br>";
	 echo "get_m1 =".$_GET['m1']."</br>";
	 echo "get_t1 =".$_GET['t1']."</br>";
	 echo "get_m2 =".$_GET['m2']."</br>";
	 echo "get_t2 =".$_GET['t2']."</br>"; */

function tgl($tgl)
{
  if($tgl <> '0000-00-00'){ echo date('l/ j F Y',strtotime($tgl)); }
  else {echo "-";}
}
	 
if($_GET['p']=='1') 
					$and .=" AND sebab<>'etv' AND tgl_kejadian BETWEEN '".$_GET['t1']."-".str_pad($_GET['m1'],2,'0',STR_PAD_LEFT)."-01 00:00:00' AND '".$_GET['t2']."-".str_pad($_GET['m2'],2,'0',STR_PAD_LEFT)."-31 23:59:59'";
					//$and .=" AND created_at BETWEEN '".($_GET['t1']-1)."-12-01 00:00:00' AND '".$_GET['t2']."-06-31 23:59:59'";
				else
					$and .=" AND sebab='etv' AND tgl_kejadian BETWEEN '".$_GET['t1']."-".str_pad($_GET['m1'],2,'0',STR_PAD_LEFT)."-02 00:00:00' AND '".$_GET['t2']."-".str_pad($_GET['m2'],2,'0',STR_PAD_LEFT)."-31 23:59:59'";
					//$and .=" AND created_at BETWEEN '".$_GET['t1']."-08-02 00:00:00' AND '".$_GET['t2']."-12-31 23:59:59'";
				
				switch($_GET['k']){
					case '0':$and.=" AND status_progress='0'";break;
					case '1':$and.=" AND status_progress='1'";break;
					case '2':$and.=" AND status_progress='2'";break;
					case '3':$and.=" AND status_progress='3'";break;
				}
				
 				if($_GET['r']<>'')
				{
					$and.= " AND kode_region='".$_GET['r']."'";
					$rx = $db->get_row("SELECT * FROM region WHERE kode_region='".$_GET['r']."'");
					$status.= " Regional <strong>".$rx->region."</strong>";
				} 
				if($_GET['cod']<>''){$and.= " AND sebab='".$_GET['cod']."'";} 
				
				switch($_GET['s1']){
					case '1':$order.=" st_site_id ASC,";break;
					//case '2':$order.=" region ASC ASC,";break;
					//case '3':$order.=" submit_at DESC,";break;
					case '4':$order.=" tgl_kejadian DESC,";break;
					case '5':$order.=" sebab DESC,";break;  // ------- EDITED------- FROM TGL_TUNTUTAN
					case '6':$order.=" status ASC,";break;
				}
				/*
				switch($_GET['s2']){
					case '1':$order.=" site.st_site_id ASC,";break;
					//case '2':$order.=" region ASC ASC,";break;
					//case '3':$order.=" submit_at DESC,";break;
					case '4':$order.=" tgl_kejadian DESC,";break;
					case '5':$order.=" tgl_lapor DESC,";break; // ------- EDITED------- FROM TGL_TUNTUTAN
					case '6':$order.=" st ASC,";break;
				}		
				*/
				
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
					) as st FROM `ast2` WHERE 1 ".$and." ORDER BY {$order} ast_id ASC";
				
				//  JOIN ast_detail2 v ON v.no_laporan = ast2.no_laporan
				
				//added
				//$rdetail = $db->get_results("SELECT * FROM ast_detail2 
							//JOIN category a ON a.item1=ca.item1
							//JOIN ast_detail2 v ON v.item1=ca.item1
							//WHERE ast_id='1'
							//");
				
				
				//echo $SQL1;
				if($_GET['l']!='3') $ast = $db->get_results($SQL1);
          if($ast){
				//$db->debug();
			//echo "test"; print_r($rdetail)?>
			<?php if($_GET['l']=='1')
			{ ?>
			<br /><strong>REKAPITULASI KLAIM AST PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>'')
				{
					$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
					echo $reg->region;
				}
				else echo 'NASIONAL';
			?><br />
			<!--TAHUN: <?=$_GET['t']?> (periode Polis <?=$_GET['p']==1?'31 Des 2010 - 30 Jun 2012':'2 Ags 2010 - 31 Des 2010'?>)<br />-->
			NO. POLIS: <?=$_GET['p']=='1'?'All Risk[202.204.300.10.00017]':'Earthquake[202.203.300.10.00032]'?></strong><br />
	  
      <table width="100%" border="1" cellpadding="3" cellspacing="0" style="font-size:8pt">
            
				<tr>
					<th nowrap="nowrap"  rowspan="3">No.</th>
					<th nowrap="nowrap"  rowspan="3">Nomor Laporan</th>
					<th nowrap="nowrap"  rowspan="3">Regional</th>
					<th nowrap="nowrap"  rowspan="3">Site Name</th>
					<th nowrap="nowrap"  rowspan="3">Site ID</th>
					<th nowrap="nowrap"  rowspan="3">PIC Regional</th>
					<th nowrap="nowrap"  rowspan="2" colspan="3">Tanggal</th>
					<th nowrap="nowrap"  rowspan="3">Penyebab kerugian</th>
					<th nowrap="nowrap"  rowspan="3">Deductible</th>
					<th nowrap="nowrap"  rowspan="2" colspan="8">Aset Tetap</th>				
					<th nowrap="nowrap"  rowspan="3" colspan="1">Quantity</th>
                    <th nowrap="nowrap"  rowspan="3" colspan="1">Satuan</th>
                <!--    <th nowrap="nowrap"  rowspan="2" colspan="3">Or. Curr</th>
					<th nowrap="nowrap"  rowspan="3">Rate IDR</th>
					<th nowrap="nowrap"  rowspan="3">Total Amount</th> -->
					<th nowrap="nowrap"  rowspan="2" colspan="10">Dokumen</th>
				<!--	<th nowrap="nowrap"  rowspan="2" colspan="2">Proposed Adjustment</th>
					<th nowrap="nowrap"  rowspan="2" colspan="2">Konfirmasi Proposed Adjustment</th>-->
					<th nowrap="nowrap"  rowspan="1" colspan="6">Status Klaim</th>
			  </tr>
              <tr>
              <th nowrap="nowrap" rowspan="2" colspan="1">Under Deductible</th>
              <th nowrap="nowrap" rowspan="2" colspan="1">Outstanding</th>
              <th nowrap="nowrap" rowspan="1" colspan="2">Setlement</th>
              </tr>
					<tr>
					<th nowrap="nowrap"  rowspan="1">Kejadian</th>
					<th nowrap="nowrap"  rowspan="1">Lapor HO</th>
					<th nowrap="nowrap"  rowspan="1">Lapor SJU</th>
                    <th nowrap="nowrap"  rowspan="1" align="center">No.</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 1</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 2</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 3</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 4 (Item)</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 5(Merk)</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 6(Type)</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 7</th>
                    
                 <!--   <th nowrap="nowrap"  rowspan="1">IDR</th>
					<th nowrap="nowrap"  rowspan="1">EUR</th>
					<th nowrap="nowrap"  rowspan="1">USD</th> -->
					<th nowrap="nowrap"  rowspan="1">Surat tuntutan</th>
					<th nowrap="nowrap"  rowspan="1">Laporan Awal</th>
					<th nowrap="nowrap"  rowspan="1">BA Kehilangan</th>
					<th nowrap="nowrap"  rowspan="1">BA Kronologi</th>
					<th nowrap="nowrap"  rowspan="1">Rincian Kerugian</th>
					<th nowrap="nowrap"  rowspan="1">BA Kepolisian</th>
					<th nowrap="nowrap"  rowspan="1">Surat PMK</th>
					<th nowrap="nowrap"  rowspan="1">Surat BMKG</th>
					<th nowrap="nowrap"  rowspan="1">Foto</th>
					<th nowrap="nowrap"  rowspan="1">PO</th>
				<!--	<th nowrap="nowrap"  rowspan="1">Tanggal</th>
					<th nowrap="nowrap"  rowspan="1">Amount</th>
					<th nowrap="nowrap"  rowspan="1">Tanggal</th>
					<th nowrap="nowrap"  rowspan="1">Amount</th> -->
					<th nowrap="nowrap"  rowspan="1">Tanggal</th>
					<th nowrap="nowrap"  rowspan="1">Amount</th>
                  	
				</tr>
                            
    <?php
          $i=1;foreach($ast as $c):?>
 
				<tr class="<?=$i%2==0?'odd':'even'?>">
					<td><?=$i?>.</td>
					<td nowrap="nowrap"><a href="view_ast.php?i=<?=$c->no_laporan?>"><?=$c->no_laporan?></a> </td>
					<td nowrap="nowrap" align="center"><?=$c->region?></td>
					<td nowrap="nowrap" align="center"><a href="su_lap_astsite.php?i=<?=$c->st_name?>"><?=$c->st_name?></a></td>
					<td nowrap="nowrap" align="center"><?=$c->st_site_id?></td>
					<td nowrap="nowrap" align="center"><?=$c->pic_region?></td>
					<td nowrap="nowrap" align="center"><?php tgl($c->tgl_kejadian); ?></td>
					<td nowrap="nowrap" align="center"><?= tgl($c->approve_at)?></td>
					<td nowrap="nowrap" align="center"><?= tgl($c->submit_at)?></td>
				       <?php if ($c->sebab=="nds") {$sebab="Natural Dissaster (Bencana Alam)";}
                       elseif ($c->sebab=="rio") {$sebab="Riots/ Strikes, Malicious Damage (Kerusuhan)";}
		               elseif ($c->sebab=="thf") {$sebab="Theft (Pencurian)";}
		               elseif ($c->sebab=="lit") {$sebab="Lightning (Petir)";}
		               elseif ($c->sebab=="etv") {$sebab="Earthquake, Tsunami, Volcano Erruption";}
		               elseif ($c->sebab=="fre") {$sebab="Fire (Terbakar/ Kebakaran)";}
		               elseif ($c->sebab=="3p") {$sebab="Third Party (Tuntutan Pihak ketiga)";}
		               else {$sebab="Other Losses (Lainnya..)";}	
	                 	?>

                    <td nowrap="nowrap" align="center"><?=$sebab?>
                    </td>
					<td nowrap="nowrap" align="center">
					<?php echo number_format($c->deduct);	?> 
				    </td>
                    
                    <!------------------------------------------ edit??------------------------------>
					<td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT * from ast_detail2 
												  where no_laporan='".$c->no_laporan."';");
					
					$a=1;foreach($ast_detail as $d):
					
					?>
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
         <td><?=$a?>.</td>
        </tr>
                    <?php $a++;endforeach;?>
      </table>
      </td>

      <td colspan="1" rowspan="1">
      <table>
					<?php  
					//$ast_detail=$db->get_results ("SELECT * from ast_detail2 
												  //where no_laporan='".$c->no_laporan."';");
					
					$a=1;foreach($ast_detail as $d):
					$ast_group=$db->get_row ("SELECT group_name from category where item1='".$d->item1."';");
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"><?=$ast_group->group_name?></td>
                    </tr>
                    <?php $a++;endforeach;?>
            </table>
                  </td>
                    
                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					//$ast_detail=$db->get_results ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->get_row ("SELECT sub_cat1 from category 
												  where item1='".$d->item1."';");
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                  
                    <td nowrap="nowrap" align="center"><?=$ast_group->sub_cat1?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    </table>
                    </td>

                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					//$ast_detail=$db->get_results ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->get_row ("SELECT sub_cat2 from category 
												  where item1='".$d->item1."';");
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"><?=$ast_group->sub_cat2?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    </table>
                    </td>

                   <td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT item1 from ast_detail2 
											  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d): ?>
                    <!-- <tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"> <?=$d->item1?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>
                  </td>

                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT * from ast_detail2 
											  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					?>
                    
                  <!--  <tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"><?=$d->merk?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    </table>
                    </td>

                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT * from ast_detail2 
											  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					?>
                    
                  <!--  <tr class="<?=$a%2==0?'even':'odd'?>">-->
                   
                    <td nowrap="nowrap"><?=$d->type?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>
                    </td>

                    <td colspan="1" rowspan="1">&nbsp;</td>
				
                    <td align="center">
                    <table  border="0" cellpadding="1" cellspacing="1">
                    <?php //function tgldoc($doc) {if ($doc=='0000-00-00'and'NULL') {echo "Tidak Ada";} else {echo $doc;} } //ADDED!!?>
                    <?php  
					$ast_detail=$db->get_results ("SELECT quantity from ast_detail2 
												  where no_laporan='".$c->no_laporan."';");
	                foreach($ast_detail as $d):
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                    <td nowrap="nowrap" align="center"><?=$d->quantity?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>

                    </td>
                    
                    <td nowrap="nowrap" align="center">
                    <table width="" border="0" cellpadding="1" cellspacing="1">
                    <?php //function tgldoc($doc) {if ($doc=='0000-00-00'and'NULL') {echo "Tidak Ada";} else {echo $doc;} } //ADDED!!?>
                    <?php  
					$ast_detail=$db->get_results ("SELECT satuan from ast_detail2 
												  where no_laporan='".$c->no_laporan."';");
					foreach($ast_detail as $d):
					?>
                    
                   <!-- <tr class="<?=$a%2==0?'even':'odd'?>">-->
                    <td align="center"><?=$d->satuan?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>

                    </td>
                    
                <!--    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">
                    <?php echo number_format($c->estimasi);	?> 
                  </td>-->
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_tun=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_tun;} 
					?>
					
                    </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_lap=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_lap;}?> 
                    
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_hil=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_hil;}?>
                    
                    </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_kro=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_kro;} ?>
                    
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_rinci=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_rinci;} ?>
                    
                    </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_pol=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_pol;} ?>
                   
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_pmk=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_pmk;} ?>
                    
                    </td>
                    <td nowrap="nowrap" align="center"> <?php if ($c->doc_bmkg=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_bmkg;} ?>
                   
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_fo=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_fo;} ?></td>
                    <td nowrap="nowrap" align="center"> <?php if ($c->doc_po=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_po;} ?></td>
                     <td nowrap="nowrap" align="center">
                    <?php if ($c->estimasi <= $c->deduct && $c->status_progress=='2'){echo  "<b>&#10003;</b>";} else {echo "<b> - </b>";}?>
                    </td>
                    <td nowrap="nowrap" align="center">
					  <?php if ($c->estimasi >= $c->deduct && $c->status_progress=='1'){echo "<b>&#10003;</b>";} else {echo "<b> - </b>";}?>
                    </td>
                    <td nowrap="nowrap" align="center"><?php tgl($c->tgl_settled)?> </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->settled!=0 ){echo $c->settled;} else {echo "<b>-</b>";}?></td>
			  </tr>
				<?php $i++;endforeach;?>
		  </table>			
			<?php } ?>
              
            <!--------------------------------------------- UNDITTED!!! ----------------------------------------------------->
			<?php if($_GET['l']=='2'){ ?>
  <br /><strong>REPORT DETAIL PROGRESS AST PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>''){
					$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
					echo $reg->region;
				}else echo 'NASIONAL';
			?><br />
			
			NO. POLIS: <?=$_GET['p']=='1'?'All Risk[202.204.300.10.00017]':'Earthquake[202.203.300.10.00032]'?></strong><br />
		  			<table width="100%" border="1" cellpadding="3" cellspacing="0" style="font-size:8pt">
            
				<tr>
					<th nowrap="nowrap"  rowspan="3">No.</th>
					<th nowrap="nowrap"  rowspan="3">Nomor Laporan</th>
					<th nowrap="nowrap"  rowspan="3">Regional</th>
					<th nowrap="nowrap"  rowspan="3">Site Name</th>
					<th nowrap="nowrap"  rowspan="3">Site ID</th>
					<th nowrap="nowrap"  rowspan="3">PIC Regional</th>
					<th nowrap="nowrap"  rowspan="2" colspan="3">Tanggal</th>
					<th nowrap="nowrap"  rowspan="3">Penyebab kerugian</th>
					<th nowrap="nowrap"  rowspan="3">Deductible</th>
					<th nowrap="nowrap"  rowspan="2" colspan="8">Aset Tetap</th>				
					<th nowrap="nowrap"  rowspan="3" colspan="1">Quantity</th>
                    <th nowrap="nowrap"  rowspan="3" colspan="1">Satuan</th>
                <!--    <th nowrap="nowrap"  rowspan="2" colspan="3">Or. Curr</th>
					<th nowrap="nowrap"  rowspan="3">Rate IDR</th>
					<th nowrap="nowrap"  rowspan="3">Total Amount</th> -->
					<th nowrap="nowrap"  rowspan="2" colspan="10">Dokumen</th>
				<!--	<th nowrap="nowrap"  rowspan="2" colspan="2">Proposed Adjustment</th>
					<th nowrap="nowrap"  rowspan="2" colspan="2">Konfirmasi Proposed Adjustment</th>-->
					<th nowrap="nowrap"  rowspan="1" colspan="6">Status Klaim</th>
			  </tr>
              <tr>
              <th nowrap="nowrap" rowspan="2" colspan="1">Under Deductible</th>
              <th nowrap="nowrap" rowspan="2" colspan="1">Outstanding</th>
              <th nowrap="nowrap" rowspan="1" colspan="2">Setlement</th>
              </tr>
					<tr>
					<th nowrap="nowrap"  rowspan="1">Kejadian</th>
					<th nowrap="nowrap"  rowspan="1">Lapor HO</th>
					<th nowrap="nowrap"  rowspan="1">Lapor SJU</th>
                    <th nowrap="nowrap"  rowspan="1" align="center">No.</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 1</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 2</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 3</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 4 (Item)</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 5(Merk)</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 6(Type)</th>
					<th nowrap="nowrap"  rowspan="1" align="center">Kategori 7</th>
                    
                 <!--   <th nowrap="nowrap"  rowspan="1">IDR</th>
					<th nowrap="nowrap"  rowspan="1">EUR</th>
					<th nowrap="nowrap"  rowspan="1">USD</th> -->
					<th nowrap="nowrap"  rowspan="1">Surat tuntutan</th>
					<th nowrap="nowrap"  rowspan="1">Laporan Awal</th>
					<th nowrap="nowrap"  rowspan="1">BA Kehilangan</th>
					<th nowrap="nowrap"  rowspan="1">BA Kronologi</th>
					<th nowrap="nowrap"  rowspan="1">Rincian Kerugian</th>
					<th nowrap="nowrap"  rowspan="1">BA Kepolisian</th>
					<th nowrap="nowrap"  rowspan="1">Surat PMK</th>
					<th nowrap="nowrap"  rowspan="1">Surat BMKG</th>
					<th nowrap="nowrap"  rowspan="1">Foto</th>
					<th nowrap="nowrap"  rowspan="1">PO</th>
				<!--	<th nowrap="nowrap"  rowspan="1">Tanggal</th>
					<th nowrap="nowrap"  rowspan="1">Amount</th>
					<th nowrap="nowrap"  rowspan="1">Tanggal</th>
					<th nowrap="nowrap"  rowspan="1">Amount</th> -->
					<th nowrap="nowrap"  rowspan="1">Tanggal</th>
					<th nowrap="nowrap"  rowspan="1">Amount</th>
                  	
				</tr>
                            
    <?php
          $i=1;foreach($ast as $c):?>
 
				<tr class="<?=$i%2==0?'odd':'even'?>">
					<td><?=$i?>.</td>
					<td nowrap="nowrap"><a href="view_ast.php?i=<?=$c->no_laporan?>"><?=$c->no_laporan?></a> </td>
					<td nowrap="nowrap" align="center"><?=$c->region?></td>
					<td nowrap="nowrap" align="center"><a href="su_lap_astsite.php?i=<?=$c->st_name?>"><?=$c->st_name?></a></td>
					<td nowrap="nowrap" align="center"><?=$c->st_site_id?></td>
					<td nowrap="nowrap" align="center"><?=$c->pic_region?></td>
					<td nowrap="nowrap" align="center"><?php tgl($c->tgl_kejadian); ?></td>
					<td nowrap="nowrap" align="center"><?= tgl($c->approve_at)?></td>
					<td nowrap="nowrap" align="center"><?= tgl($c->submit_at)?></td>
				       <?php if ($c->sebab=="nds") {$sebab="Natural Dissaster (Bencana Alam)";}
                       elseif ($c->sebab=="rio") {$sebab="Riots/ Strikes, Malicious Damage (Kerusuhan)";}
		               elseif ($c->sebab=="thf") {$sebab="Theft (Pencurian)";}
		               elseif ($c->sebab=="lit") {$sebab="Lightning (Petir)";}
		               elseif ($c->sebab=="etv") {$sebab="Earthquake, Tsunami, Volcano Erruption";}
		               elseif ($c->sebab=="fre") {$sebab="Fire (Terbakar/ Kebakaran)";}
		               elseif ($c->sebab=="3p") {$sebab="Third Party (Tuntutan Pihak ketiga)";}
		               else {$sebab="Other Losses (Lainnya..)";}	
	                 	?>

                    <td nowrap="nowrap" align="center"><?=$sebab?>
                    </td>
					<td nowrap="nowrap" align="center">
					<?php echo number_format($c->deduct);	?> 
				    </td>
                    
                    <!------------------------------------------ edit??------------------------------>
					<td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT * from ast_detail2 
												  where no_laporan='".$c->no_laporan."';");
					
					$a=1;foreach($ast_detail as $d):
					
					?>
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
         <td><?=$a?>.</td>
        </tr>
                    <?php $a++;endforeach;?>
      </table>
      </td>

      <td colspan="1" rowspan="1">
      <table>
					<?php  
					//$ast_detail=$db->get_results ("SELECT * from ast_detail2 
												  //where no_laporan='".$c->no_laporan."';");
					
					$a=1;foreach($ast_detail as $d):
					$ast_group=$db->get_row ("SELECT group_name from category 
												  where item1='".$d->item1."';");
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"><?=$ast_group->group_name?></td>
                    </tr>
                    <?php $a++;endforeach;?>
            </table>
                  </td>
                    
                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					//$ast_detail=$db->get_results ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->get_row ("SELECT sub_cat1 from category 
												  where item1='".$d->item1."';");
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                  
                    <td nowrap="nowrap" align="center"><?=$ast_group->sub_cat1?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    </table>
                    </td>

                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					//$ast_detail=$db->get_results ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->get_row ("SELECT sub_cat2 from category 
												  where item1='".$d->item1."';");
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"><?=$ast_group->sub_cat2?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    </table>
                    </td>

                   <td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT item1 from ast_detail2 
											  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d): ?>
                    <!-- <tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"> <?=$d->item1?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>
                  </td>

                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT * from ast_detail2 
											  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					?>
                    
                  <!--  <tr class="<?=$a%2==0?'even':'odd'?>">-->
                    
                    <td nowrap="nowrap" align="center"><?=$d->merk?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    </table>
                    </td>

                    <td colspan="1" rowspan="1">
                    <table>
					<?php  
					$ast_detail=$db->get_results ("SELECT * from ast_detail2 
											  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					?>
                    
                  <!--  <tr class="<?=$a%2==0?'even':'odd'?>">-->
                   
                    <td nowrap="nowrap"><?=$d->type?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>
                    </td>

                    <td colspan="1" rowspan="1">&nbsp;</td>
				
                    <td align="center">
                    <table  border="0" cellpadding="1" cellspacing="1">
                    <?php //function tgldoc($doc) {if ($doc=='0000-00-00'and'NULL') {echo "Tidak Ada";} else {echo $doc;} } //ADDED!!?>
                    <?php  
					$ast_detail=$db->get_results ("SELECT quantity from ast_detail2 
												  where no_laporan='".$c->no_laporan."';");
	                foreach($ast_detail as $d):
					?>
                    
                    <!--<tr class="<?=$a%2==0?'even':'odd'?>">-->
                    <td nowrap="nowrap" align="center"><?=$d->quantity?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>

                    </td>
                    
                    <td nowrap="nowrap" align="center">
                    <table width="" border="0" cellpadding="1" cellspacing="1">
                    <?php //function tgldoc($doc) {if ($doc=='0000-00-00'and'NULL') {echo "Tidak Ada";} else {echo $doc;} } //ADDED!!?>
                    <?php  
					$ast_detail=$db->get_results ("SELECT satuan from ast_detail2 
												  where no_laporan='".$c->no_laporan."';");
					foreach($ast_detail as $d):
					?>
                    
                   <!-- <tr class="<?=$a%2==0?'even':'odd'?>">-->
                    <td align="center"><?=$d->satuan?></td>
                    </tr>
                    <?php $a++;endforeach;?>
                    <?php //$a++;}?>
                    </table>

                    </td>
                    
                <!--    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">1</td>
                    <td nowrap="nowrap" align="center">
                    <?php echo number_format($c->estimasi);	?> 
                  </td>-->
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_tun=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_tun;} 
					?>
					
                    </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_lap=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_lap;}?> 
                    
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_hil=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_hil;}?>
                    
                    </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_kro=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_kro;} ?>
                    
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_rinci=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_rinci;} ?>
                    
                    </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_pol=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_pol;} ?>
                   
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_pmk=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_pmk;} ?>
                    
                    </td>
                    <td nowrap="nowrap" align="center"> <?php if ($c->doc_bmkg=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_bmkg;} ?>
                   
                </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->doc_fo=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_fo;} ?></td>
                    <td nowrap="nowrap" align="center"> <?php if ($c->doc_po=='0000-00-00'and'NULL'){echo "Tidak Ada";}
					else {echo $c->doc_po;} ?></td>
                    <td nowrap="nowrap" align="center">
                    <?php if ($c->estimasi <= $c->deduct && $c->status_progress=='2'){echo  "<b>&#10003;</b>";} else {echo "<b> - </b>";}?>
                    </td>
                    <td nowrap="nowrap" align="center">
					  <?php if ($c->estimasi >= $c->deduct && $c->status_progress=='1'){echo "<b>&#10003;</b>";} else {echo "<b> - </b>";}?></td>
                    <td nowrap="nowrap" align="center"><?php tgl($c->tgl_settled)?> </td>
                    <td nowrap="nowrap" align="center"><?php if ($c->settled!=0 ){echo $c->settled;} else {echo "<b>-</b>";}?></td>
			  </tr>
				<?php $i++;endforeach;?>
		  </table>				
		 <?php } 
      }
      else
     {echo "Tidak Ada laporan";}
      ?>
            
            
          	<?php if($_GET['l']=='3'){ 
			$SQL3  = "SELECT COUNT(1) jml, `status`,EXTRACT(MONTH FROM `created_at`) bln FROM ast2 
					WHERE 1 ".$and." 
					GROUP BY `status`, bln";
			$res3 = $db->get_results($SQL3);
			//$db->debug();
			$summ = array();
			foreach($res3 as $r3){
				$summ[$r3->status][$r3->bln] = $r3->jml>0?$r3->jml:'0';
			}
			?>
			<br /><strong>SUMMARY REPORT KLAIM AST PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>''){
					$reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
					echo $reg->region;
				}else echo 'NASIONAL';
			?><br />
			NO. POLIS: <?=$_GET['p']=='1'?'All Risk[202.204.300.10.00017]':'Earthquake[202.203.300.10.00032]'?></strong><br />
			<table cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
				<tr>
					<th rowspan="2">Status</th>
					<th colspan="12">Bulan</th>
                    <th rowspan="2" bgcolor="#999999">Total</th>
				</tr>
				<tr>
					<?php foreach($months as $m): ?>
					<th style="width:40px;"><?=$m?></th>
					<?php endforeach; ?>
				</tr>
				<tr class="even">
					<td>Created (UnApproved)</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['UNAPPROVED'][$i] ?></td>
					<?php $un+=$summ['UNAPPROVED'][$i]; }?>
                        <td style="text-align:right" bgcolor="#999999"><?=$un?></td>
				</tr>
				<tr class="odd">
					<td>Approve</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['APPROVED'][$i] ?></td>    
					<?php $ap+=$summ['APPROVED'][$i]; }?>
                        <td style="text-align:right" bgcolor="#999999"><?=$ap?></td>
				</tr>
                <tr class="even">
					<td>Rejected</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['REJECTED'][$i] ?></td>    
					<?php $rej+=$summ['REJECTED'][$i]; }?>
                        <td style="text-align:right" bgcolor="#999999"><?=$rej?></td>
				</tr>
				<tr class="odd">
					<td>Under Deductible</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['UNDER DEDUCTIBLE'][$i] ?></td>
                    <?php $dec+=$summ['UNDER DEDUCTIBLE'][$i]; }?>
                     <td style="text-align:right" bgcolor="#999999"><?=$dec?></td>
				</tr>
				<tr class="even">
					<td>Outstanding (Submitted)</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['SUBMITTED'][$i] ?></td>
					<?php $sub+=$summ['SUBMITTED'][$i]; }?>
                    <td style="text-align:right" bgcolor="#999999"><?=$sub ?></td>
				</tr>			
				<tr class="odd">
					<td>Settled</td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=(int)$summ['SETTLED'][$i] ?></td>
					<?php $set+=$summ['SETTLED'][$i]; }?>
                    <td style="text-align:right" bgcolor="#999999"><?=$set?></td>
				</tr>
				
				<tr bgcolor="#999999">
					<td><strong>Total</strong></td>
					<?php for($i=1;$i<=12;$i++){ ?>
						<td style="text-align:right"><?=$summ['UNAPPROVED'][$i]+$summ['APPROVED'][$i]+$summ['SUBMITTED'][$i]+$summ['SETTLED'][$i]+$summ['REJECTED'][$i]?></td>
					<?php $tot+=$summ['UNAPPROVED'][$i]+$summ['APPROVED'][$i]+$summ['SUBMITTED'][$i]+$summ['SETTLED'][$i]+$summ['REJECTED'][$i]; }?>
                     <td style="text-align:right"><?=$tot?></td>
				</tr>               
			</table>
			<?php } ?>
	  </td>
	</tr>
</table>
<?php include "footer.php"?>