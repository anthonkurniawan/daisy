<?php
require 'init.php';
require 'priviledges.php';
include "header.php";

$_SESSION['gets'] = $_GET;  //add
function tgl($tgl)
{
  if($tgl <> '0000-00-00'){ echo date('l/ j F Y',strtotime($tgl)); }
  else {echo "-";}
}
?>
<table width="<?=in_array($_GET['l'],array(1,2))?'1800':'1000'?>" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto;" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:250px"><ul style="list-style:none;padding-left:5px">
		  <?php include "menu.php" ?>
	    </ul></td>
		<td>
			<h3>Membuat Laporan Klaim AST</h3>
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
								<option value="1" <?=($_GET['l']=='1'?'selected="selected"':'')?>>Rekapitulasi Klaim AST</option>
								<option value="2" <?=($_GET['l']=='2'?'selected="selected"':'')?>>Detail Progress AST</option>
								<option value="3" <?=($_GET['l']=='3'?'selected="selected"':'')?>>Summary Report Klaim AST</option>
							</select>
						</td>
					</tr>
                    <tr></tr>
					<tr>
						<td>Regional:</td>
						<td>
							<?php if(!in_array($user->role,array('gmp','spvp','stfp'))){ ?>
							<?php 
								$r = $db->getRow("SELECT region from region WHERE kode_region='".$user->regional."'");
								echo $r->region;
							?>
							<input type="hidden" name="r" value="<?=$user->regional?>" />
							<?php }
							else
							{ ?>
							<select name="r">
								<option <?=($_GET['r']==$r->kode_region?'selected="selected"':'')?> value="">ALL (NASIONAL)</option>
								<?php
									$res = $db->getArray("SELECT * FROM `region` ORDER BY kode_region ASC");
									foreach($res as $r)
									{ ?>
									<option <?=($_GET['r']==$r->kode_region?'selected="selected"':'')?> value="<?=$r->kode_region?>"><?=$r->region?></option>
									<?php 
									}
								?>
							</select>
							<?php 
							} ?>
						</td>
					</tr>
                    <tr></tr>
					 <tr>
						<td>Status Klaim :</td>
						<td>
						<select name="k">
						  <option value="">ALL</option>
                          <option value="1" <?=($_GET['k']=='1'?'selected="selected"':'')?>>Outstanding</option>
                          <option value="2" <?=($_GET['k']=='2'?'selected="selected"':'')?>>Under Deductible</option>
						  <option value="3" <?=($_GET['k']=='3'?'selected="selected"':'')?>>Settlement</option>
					      </select>
						</td>
					</tr>
                    <tr>
						<td>Sebab Kerusakan :</td>
						<td>
						  <select name="cod">
                    		<option value=""> ALL </option>
                    		<option value="thf" <?=($_GET['cod']=='thf'?'selected="selected"':'')?>>Theft (Pencurian) </option>
                   			<option value="lit" <?=($_GET['cod']=='lit'?'selected="selected"':'')?>>Lightning (Petir) </option>
                   			<option value="fre" <?=($_GET['cod']=='fre'?'selected="selected"':'')?>>Fire (Terbakar/ Kebakaran) </option>
                    		<option value="nds" <?=($_GET['cod']=='nds'?'selected="selected"':'')?>>Natural Dissaster (Bencana Alam)</option>
                    		<option value="rio" <?=($_GET['cod']=='rio'?'selected="selected"':'')?>>Riots/ Strikes, Malicious Damage (Kerusuhan) </option>
                    		<option value="trp" <?=($_GET['cod']=='trp'?'selected="selected"':'')?>>Third Party (Tuntutan Pihak ketiga) </option>
                    		<option value="etv" <?=($_GET['cod']=='etv'?'selected="selected"':'')?>>Earthquake, Tsunami, Volcano Erruption </option>
                    		<option value="oth" <?=($_GET['cod']=='oth'?'selected="selected"':'')?>>Other Losses (Lainnya..) </option>
                    	   </select>
						</td>
					</tr>
                    <tr>
						<td>No Polis: </td>
						<td>
							<select name="p">
                            	<option <?=($_GET['p']=='2'?'selected="selected"':'')?> value="2">31 Dec-10 s/d 30 Juni 2012 Earthquake [No. Polis: 202.203.300.10.00032]</option>
								<option <?=($_GET['p']=='1'?'selected="selected"':'')?> value="1">31 Dec-10 s/d 30 Juni 2012 All Risk [No. Polis: 202.204.300.10.00017]</option>
							</select>						
						</td>
					</tr>
                    <tr></tr>
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
								<option value="<?=$i?>" <?=($_GET['t2']==$i ||($_GET['t2']==''&&date("Y")==$i) ?'selected="selected"':'')?>><?=$i?></option>
								<?php } ?>
							</select>
							
						</td>
					</tr>
                    <tr></tr>
					<tr>
						<td>Urut berdasarkan:</td>						
						<td>
							#1 <select name="s1">
								<option value="" <?=($_GET['s1']==''?'selected="selected"':'')?>>No. Laporan</option>
								<option value="1" <?=($_GET['s1']=='1'?'selected="selected"':'')?>>Site ID</option>
								<option value="4" <?=($_GET['s1']=='4'?'selected="selected"':'')?>>Tanggal Kejadian</option>
								<option value="5" <?=($_GET['s1']=='5'?'selected="selected"':'')?>>Sebab Kerugian</option>
								<option value="6" <?=($_GET['s1']=='6'?'selected="selected"':'')?>>Status Klaim</option>
							</select>
							<!--#2 <select name="s2">
								<option value="" <?=($_GET['s2']==''?'selected="selected"':'')?>>No. Laporan</option>
								<option value="1" <?=($_GET['s2']=='1'?'selected="selected"':'')?>>Site ID</option>
								<option value="4" <?=($_GET['s2']=='4'?'selected="selected"':'')?>>Tanggal Kejadian</option>
								<option value="5" <?=($_GET['s2']=='5'?'selected="selected"':'')?>>Tanggal Diketahui</option>
								<option value="6" <?=($_GET['s2']=='6'?'selected="selected"':'')?>>Status Klaim</option>
							</select>-->
						</td>
					</tr>
					<tr>
						<td colspan="2">
						<input type="submit" value="Create" /> <!-------- BUTTON 'PRINT' N 'EXPORT EXCEL' NOT YE CREATED!!! ------>
						<input type="button" value="Print" onclick="window.open ('u_printReportAST.php?l=<?=$_GET['l']?>&r=<?=$_GET['r']?>&k=<?=$_GET['k']?>&cod=<?=$_GET['cod']?>&p=<?=$_GET['p']?>&t1=<?=$_GET['t1']?>&t2=<?=$_GET['t2']?>&m1=<?=$_GET['m1']?>&m2=<?=$_GET['m2']?>&s1=<?=$_GET['s1']?>&s2=<?=$_GET['s2']?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
					<!--	<input type="button" value="Export ke excel" onclick="window.open ('su_lap_ast_excel.php?s=<?=$_GET['s']?>&m1=<?=$_GET['m1']?>&t1=<?=$_GET['t1']?>&m2=<?=$_GET['m2']?>&t2=<?=$_GET['t2']?>&ui=<?=$user->user_id?>&r=<?=$user->regional?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" /> -->
                    <input type="button" value="Export ke Excel" onclick="window.open ('u_lap_ast_excel.php?f=xls','daisy<?=rand()?>','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
					</td>
					</tr>
				</table>
			</form> 	
            		
			<?php
				/*if($_GET['p']=='1')
					$and.=" AND sebab<>'etv'";
				else
				    $and.="AND sebab='etv'"; */
				
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
					$rx = $db->getRow("SELECT * FROM region WHERE kode_region='".$_GET['r']."'");
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
				) FROM `ast2` WHERE 1 ".$and." ORDER BY {$order} ast_id ASC";
					
					/* as st FROM `ast2` 
					JOIN site ON site.st_site_id=ast2.st_site_id 
					JOIN region r ON r.kode_region=site.kode_region
					
				    WHERE 1 ".$and." ORDER BY {$order} ast_id DESC"; */
					
		//------------------------- ADDED -----------------------------------------------
			$rdetail = $db->getArray("SELECT * FROM ast_asset ca
							JOIN asset a ON a.asset_id=ca.asset_id
							JOIN asset_vendor v ON v.vendor_id=ca.vendor_id
							JOIN asset_merk m ON m.merk_id=ca.merk_id WHERE ast_id='".$rast->ast_id."'
							");	
			//------------------------- END -----------------------------------------------
				
				if($_GET['l']!='3') $ast = $db->getArray($SQL1);
				if($ast){
			//echo "test"; print_r($rdetail)?>
			<?php if($_GET['l']=='1')
			{ ?>
			<br /><strong>REKAPITULASI KLAIM AST PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>'')
				{
					$reg = $db->getRow("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
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
					$ast_detail=$db->getArray ("SELECT * from ast_detail2 
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
					//$ast_detail=$db->getArray ("SELECT * from ast_detail2 
												  //where no_laporan='".$c->no_laporan."';");
					
					$a=1;foreach($ast_detail as $d):
					$ast_group=$db->getRow ("SELECT group_name from category where item1='".$d->item1."';");
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
					//$ast_detail=$db->getArray ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->getRow ("SELECT sub_cat1 from category 
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
					//$ast_detail=$db->getArray ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->getRow ("SELECT sub_cat2 from category 
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
					$ast_detail=$db->getArray ("SELECT item1 from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT * from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT * from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT quantity from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT satuan from ast_detail2 
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
					$reg = $db->getRow("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
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
					$ast_detail=$db->getArray ("SELECT * from ast_detail2 
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
					//$ast_detail=$db->getArray ("SELECT * from ast_detail2 
												  //where no_laporan='".$c->no_laporan."';");
					
					$a=1;foreach($ast_detail as $d):
					$ast_group=$db->getRow ("SELECT group_name from category 
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
					//$ast_detail=$db->getArray ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->getRow ("SELECT sub_cat1 from category 
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
					//$ast_detail=$db->getArray ("SELECT * from ast_detail2 
						//						  where no_laporan='".$c->no_laporan."';");
					
					foreach($ast_detail as $d):
					$ast_group=$db->getRow ("SELECT sub_cat2 from category 
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
					$ast_detail=$db->getArray ("SELECT item1 from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT * from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT * from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT quantity from ast_detail2 
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
					$ast_detail=$db->getArray ("SELECT satuan from ast_detail2 
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
      else {echo "<p>Tidak Ada Laporan</p>";}
      ?>
            
            
          	<?php if($_GET['l']=='3'){ 
			$SQL3  = "SELECT COUNT(1) jml, `status`,EXTRACT(MONTH FROM `created_at`) bln FROM ast2 
					WHERE 1 ".$and." 
					GROUP BY `status`, bln";
			$res3 = $db->getArray($SQL3);
			//$db->debug();
			$summ = array();
			foreach($res3 as $r3){
				$summ[$r3->status][$r3->bln] = $r3->jml>0?$r3->jml:'0';
			}
			?>
			<br /><strong>SUMMARY REPORT KLAIM AST PT TELKOMSEL</strong><br />
			<strong>REGIONAL : <?php
				if($_GET['r']<>''){
					$reg = $db->getRow("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
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
