<?php
require 'init.php';
$query = "SELECT st_site_id,st_name,st_region,st_longitude,st_latitude,st_address
	 FROM `site` WHERE `st_site_id` = '{$_GET['siteId']}'";
$r = $db->get_row($query);
?>
<table>
  <tr class="odd">
    <td>Site ID</td>
    <td><?= $r->st_site_id ?></td>

  </tr>
  <tr class="even">
    <td>Site Name</td>
    <td><?= $r->st_name ?></td>

  </tr>
  <tr class="odd">
    <td>Region</td>
    <td><?= $r->st_region ?></td>

  </tr>
  <tr class="even">
    <td nowrap="nowrap">Longitude/ Latitude</td>
    <td>
      <?= $_GET['long'] ?>/
      <?= $_GET['lat'] ?>
    </td>
  </tr>

  <tr valign="top" class="odd">
    <td>Alamat Site</td>
    <td>
      <?= $_GET['a'] ?>
    </td>
  </tr>

</table>