<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
$err = array();

if($_POST){
	if($_POST['sid']=='') $err[]="Site ID";
	if($_POST['sname']=='') $err[]="site Name";
	//if($_POST['neteid']=='') $err[]="net element ID";
	//if($_POST['r']=='') $err[]="region";
	//if($_POST['long']=='') $err[]="longitude";
	//if($_POST['lat']=='') $err[]="latitude";
	//if($_POST['address']=='') $err[]="alamat";
	//if($_POST['cellid']=='') $err[]="cellid";
	//if($_POST['v']=='') $err[]="vendor";
	//if($_POST['lsize']=='') $err[]="land size";
	//if($_POST['rentS']=='') $err[]="tgl mulai sewa";
	//if($_POST['rentE']=='') $err[]="tgl berakhir sewa";
	
	if(empty($err)){
		$region = $db->get_row("SELECT region from region WHERE kode_region='{$_POST['r']}'");
		$db->query("INSERT INTO `site` (
		`st_site_id`, `st_name`,  `st_net_element_id`,  `kode_region`,  `st_region`,  `st_longitude`,  `st_latitude`,  
		`st_address`,  `st_postcode`,  `vendor`,  `cell_id`,  `land_status`, `land_size`,  `building_ownership`,  
		`rent_start`,  `rent_end`,  `pln_cap1`,  `pln_contract1`,  `pln_cap2`, `pln_contract2`) VALUES 
		('".$_POST['sid']."','".$_POST['sname']."','".$_POST['neteid']."','".$_POST['r']."','".$region->region."','".$_POST['longi']."','".$_POST['lat']."',
		'".$_POST['address']."','".$_POST['kodepos']."','".$_POST['v']."','".$_POST['cellid']."','".$_POST['ls']."','".$_POST['bo']."',
		'".$_POST['rentS']."','".$_POST['rentE']."','".$_POST['plncap1']."','".$_POST['plnco1']."','".$_POST['plncap2']."','".$_POST['plnco2']."'
		)");
		$sukses=1;
	}
}
?>
<table width="800" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto;" id="badan">
	<?php include "webheader.php"; ?>
	<tr valign="top">
		<td style="width:140px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menusuper.php" ?>
			</ul>
		</td>
		<td>
			<h3>DAISY - Input Data Site Baru</h3>
				<?php if(!empty($err)){ ?>
			<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;"> 
				<p>Mohon isi/ perbaiki data berikut:
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
			<?php } ?>
				<?php if($sukses==1): ?>
				<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;;width:450px;margin:30px auto;"> 
					<p>Site berhasil ditambahkan !<br />
					<a href="su_site.php">Kembali ke list site</a></p>
				</div>
				<?php else: ?>
						<form method="post" action="">
						<table width="80%" border="0" style="border:1px solid #ccc;margin:0px auto;margin-bottom:50px;" cellpadding="3" cellspacing="0">
							<tr class="even">
								<td>SITE ID</td>
								<td>
									<input type="text" name="sid" size="10" /> *
								</td>
							</tr>
							<tr class="odd">
								<td>Nama Site</td>
								<td><input type="text" name="sname" size="30" value="<?=$_POST['sname']?>" /> *</td>
							</tr>
							<tr class="even">
								<td>Site Net Element ID</td>
								<td><input type="text" name="neteid" size="30" value="<?=$_POST['neteid']?>" /></td>
							</tr>
							<tr class="even">
								<td>Region</td>
								<td>
									<?php $res = $db->get_results("SELECT * FROM region ORDER BY kode_region ASC")?>
									<select name="r">
										<?php foreach($res as $x):?>
										<option value="<?=$x->kode_region?>"><?=$x->kode_region?> - <?=$x->region?></option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
							<tr class="odd">
								<td>Posisi Site<br />(Longitude/ Latitude)</td>
								<td>
									<input type="text" name="longi" size="20" value="<?=$_POST['longi']?>" /> / <input type="text" name="lat" size="20" value="<?=$_POST['lat']?>" />
								</td>
							</tr>
                            
							<tr class="even">
								<td>Alamat Site</td>
								<td>
									<textarea name="address" cols="30"><?=$_POST['address']?></textarea>
									<br />Kodepos: <input type="text" name="kodepos" size="5" maxlength="5" value="<?=$_POST['kodepos']?>" />
								</td>
							</tr>
                            
							<tr class="odd">
								<td>Cell ID</td>
								<td><input type="text" name="cellid" size="50" value="<?=$_POST['cellid']?>" />
								</td>
							</tr>
							<tr class="even">
								<td>Vendor</td>
								<td>
									<input type="text" name="v" size="30" value="<?=$_POST['v']?>" /> 
								</td>
							</tr>
							<tr class="odd">
								<td>Land Status</td>
								<td>
									<select name="ls">
										<option value="TSEL">TSEL</option>
										<option value="LEASE">LEASE</option>										
									</select>
								</td>
							</tr>
							<tr class="even">
								<td>Land Size</td>
								<td>
									<input type="text" name="lsize" size="30" value="<?=$_POST['lsize']?>" />
								</td>
							</tr>
							<tr class="odd">
								<td>Building Ownership</td>
								<td>
									<input type="text" name="bo" size="30" value="<?=$_POST['bo']?>" />
								</td>
							</tr>
							<tr class="even">
								<td>Rent Start - End</td>
								<td>
									<input type="text" name="rentS" value="<?=$_POST['rentS']?>" /> -
									<input type="text" name="rentE" value="<?=$_POST['rentE']?>" />
									<div class="keterangan">Format tanggal: YYYY-mm-dd. Contoh:<?=date("Y-m-d")?></div>
								</td>
							</tr>
							<tr class="odd">
								<td>PLN Capacity 1</td>
								<td>
									<input type="text" name="plncap1" size="30" value="<?=$_POST['plncap1']?>" />
								</td>
							</tr>
							<tr class="even">
								<td>PLN Contract 1</td>
								<td>
									<input type="text" name="plnco1" size="30" value="<?=$_POST['plnco1']?>" />
								</td>
							</tr>
							
							<tr class="odd">
								<td>PLN Capacity 2</td>
								<td>
									<input type="text" name="plncap2" size="30" value="<?=$_POST['plncap2']?>" />
								</td>
							</tr>
							
							<tr class="even">
								<td>PLN Contract 2</td>
								<td>
									<input type="text" name="plnco2" size="30" value="<?=$_POST['plnco2']?>" />
								</td>
							</tr>
							<tr>
								<td>&nbsp;</td>
								<td><input value="Simpan" type="submit"/></td>
							</tr>
						</table>
						
						</form>
				<?php endif; ?>									
		</td>
	</tr>
</table>
<?php include "footer.php"?>