<?php
include 'init.php';
    $query = "SELECT st_site_id,st_name,st_region,st_longitude  FROM `site` WHERE `st_site_id` = '{$_GET['siteId']}'";
    $r = $db->get_row($query);	
	$q2 = "SELECT st_address,st_longitude,st_latitude FROM cgl WHERE `st_site_id` = '{$_GET['siteId']}' AND (`st_longitude`<>'' OR `st_latitude`<>'') ORDER BY cgl_id DESC";
	$r2 = $db->get_row($q2);	
?>
<table>
	<tr class="odd">
		<td>Site ID / Site Name</td>
		<td><?=$r->st_site_id?> / <?=$r->st_name?></td>
	</tr>
    
	<tr class="odd">
		<td>Region</td>
		<td><?=$r->st_region?></td>
	</tr>
    
	<tr class="even">
		<td nowrap="nowrap">Longitude</td>
		<td><input type="text" name="st_longitude" value="<?=$_GET['long']?$_GET['long']:$r2->st_longitude?>"</td>
	</tr>
    
	<tr class="odd">
		<td nowrap="nowrap">Latitude</td>
		<td><input type="text" name="st_latitude" value="<?=$_GET['lat']?$_GET['lat']:$r2->st_latitude?>" /></td>
	</tr>
	<tr valign="top" class="even">
		<td>Alamat</td>
		<td><input type="text" name="st_address" value="<?=$_GET['a']?$_GET['a']:$r2->st_address?>" /></td>
	</tr>
	<tr valign="top" class="odd">
		<td>Catatan</td>
		<td><input type="text" name="catatan" value="<?=$_GET['c']?>" />
		<div class="keterangan">* Diisi jika perlu</div></td>
	</tr>	
</table>