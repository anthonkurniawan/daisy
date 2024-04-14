<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
?>
<table width="800" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
		<?php include "webheader.php"; ?>
		<tr valign="top">
		<td style="width:140px">
			<ul style="list-style:none;padding-left:5px">
				<?php include "menusuper.php" ?>
			</ul>
		</td>
		<td>
			<h3>DATA INDUK: SITE</h3>
			<a href="su_site_add.php">+ Input data SITE baru</a>
				<table width="98%" class="tabel" cellpadding="0" cellspacing="0">
					<tr>
						<th>SITE ID</th>
						<th>NAMA SITE</th>
						<th>REGIONAL</th>
						<th>LONGITUDE/ LATITUDE</th>
						<th>ALAMAT SITE</th>
						<th>VENDOR</th>
						<th>LAND STATUS</th>
						<th>&nbsp;</th>
					</tr>				
			<?php
				$res= $db->get_results("SELECT region,site.* FROM `site` JOIN region r ON site.kode_region=r.kode_region ORDER BY `site_id` ASC");
				if(is_array($res)&&!empty($res))
				{?>
				<?php	$i=0;foreach($res as $ga){ ?>
				<tr class="<?=($i%2==0?'even':'odd')?>">					
					<td><?=$ga->st_site_id?></td>
					<td><?=$ga->st_name?></td>
					<td><?=$ga->region ?></td>
					<td><?=$ga->st_longitude?>/ <?=$ga->st_latitude?></td>
					<td><?=$ga->st_address?> - <?=$ga->st_postcode?></td>
					<td><?=$ga->vendor?></td>
					<td><?=$ga->land_status?></td>
					<td><a href="su_site_details.php?i=<?=$ga->st_site_id?>">Details</a></td>
				</tr>
					<?php $i++;} ?>
				<?php
				}
			?>	
				</table>						
		</td>
	</tr>
</table>
<?php include "footer.php"?>