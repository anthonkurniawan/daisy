<?php
require 'init.php';
include "headerPrint.php";
require 'priviledges.php'; ?>
<div style="margin:2px;padding:3px;background:#fff;border:1px solid #ccc">

  <?php
  $SQL = "SELECT * FROM `cgl` 
		WHERE 1 " . $and . " AND cgl_id ='" . $_GET['cgl'] . "'";
  $rcgl = $db->get_row($SQL);
  ?>

  <h3>Klaim CGL <?= $rcgl->no_laporan ?></h3>
  <table id="fcgl">
    <tr class="even">
      <td><strong>Hari, tanggal kejadian</strong></td>
      <td>
        <?= date("l, d F Y", strtotime($rcgl->tgl_kejadian)) ?>
      </td>
    </tr>
    <tr class="odd">
      <td><strong>Hari, tanggal diketahui Telkomsel</strong></td>
      <td>
        <?= date("l, d F Y", strtotime($rcgl->tgl_tuntutan)) ?>
      </td>
    </tr>
    <tr class="even">
      <td><strong>Tempat / lokasi kerugian</strong></td>
      <td>
        <?php //if($rcgl->status=='UNAPPROVED' || $rcgl->status=='REJECTED'){
        $r = $db->get_row("SELECT * FROM `site` WHERE st_site_id='" . $rcgl->st_site_id . "'");
        echo $rcgl->st_site_id . ' / ' . $r->st_name;
        ?>
      </td>
    </tr>
    <tr class="odd" valign="top">
      <td><strong>Detail lokasi kerugian<strong></td>
      <td>
        <div id="siteDetail">
          <table width="100%">
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
              <td nowrap="nowrap">Longitude</td>
              <td><?= $rcgl->st_longitude ?></td>
            </tr>
            <tr class="odd">
              <td nowrap="nowrap">Latitude</td>
              <td><?= $rcgl->st_latitude ?></td>
            </tr>
            <tr valign="top" class="even">
              <td>Alamat Site</td>
              <td><?= $rcgl->st_address ?></td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
    <tr class="even" valign="top">
      <td><strong>Penyebab kerugian</strong></td>
      <td>
        <?= $rcgl->sebab ?>
        <?= $rcgl->other_sebab ?>
      </td>
    </tr>
    <tr valign="top" class="odd">
      <td><strong>Rincian kerusakan</strong></td>
      <td>
        <?= $rcgl->rincian ?>
      </td>
    </tr>
    <tr valign="top" class="even">
      <td><strong>Estimasi kerugian</strong></td>
      <td>
        <?= $rcgl->kerugian_survey ?> <!-- $rcgl->estimasi (EDITED) -->
      </td>
    </tr>
    <tr valign="top" class="odd">
      <td><strong>Contact person</strong></td>
      <td>
        <table>
          <tr>
            <td>Nama</td>
            <td>:
              <?= $rcgl->cp_nama ?>
          </tr>
          <tr>
            <td>No telepon</td>
            <td>:
              <?= $rcgl->cp_telp ?>
            </td>
          </tr>
          <tr>
            <td>No HP</td>
            <td>:
              <?= $rcgl->cp_hp ?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr valign="top" class="even">
      <td><strong>Vendor Pelaksana</strong></td>
      <td>
        <?php $resVendor = $db->get_row("SELECT * FROM cgl_vendor WHERE `id_cglv`='" . $rcgl->id_cglv . "'"); ?>
        <strong><?= $resVendor->nama_vendor; ?></strong>
        <table>
          <tr>
            <td>Nama</td>
            <td>:
              <?= $rcgl->vendor_pic ?>
          </tr>
          <tr>
            <td>No telepon</td>
            <td>:
              <?= $rcgl->vendor_telp ?>
            </td>
          </tr>
          <tr>
            <td>No HP</td>
            <td>:
              <?= $rcgl->vendor_hp ?>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <tr class="odd">
      <td>Tanggal Survey</td>
      <td>
        <?= date("l, d F Y", strtotime($rcgl->survey_date)) ?>
      </td>
    </tr>
    <tr class="odd">
      <td>File Surat Tuntutan</td>
      <td><?= $rcgl->file_surat_tuntutan ? 'ADA' : 'BELUM ADA' ?></td>
    </tr>
    <tr class="even">
      <td>Nilai BoQ yang telah<br />disetujui oleh Telkomsel</td>
      <td>
        <?= 'Rp ' . number_format($rcgl->kerugian_survey, 0, '', '.') ?>
      </td>
    </tr>
    <tr class="odd">
      <td>File BoQ</td>
      <td><?= $rcgl->file_boq ? 'ADA' : 'BELUM ADA' ?></td>
    </tr>
    <?php if ($rcgl->payment_date <> '' && $rcgl->payment_date <> '0000-00-00') { ?>
      <tr class="even">
        <td>Tanggal Payment</td>
        <td>
          <?= date("l, d F Y", strtotime($rcgl->payment_date . ' 00:00:00')) ?>
        </td>
      </tr>
    <?php } ?>
    <?php if ($rcgl->invoice_date <> '' && $rcgl->invoice_date <> '0000-00-00') { ?>
      <tr class="odd">
        <td>Tanggal Invoice</td>
        <td>
          <?= date("l, d F Y", strtotime($rcgl->invoice_date . ' 00:00:00')) ?>
        </td>
      </tr>
    <?php } ?>
    <tr class="even" valign="top">
      <td>Nilai Invoice</td>
      <td>
        <?= $rcgl->nilai_invoice ?>
      </td>
    </tr>
    <tr class="odd">
      <td><strong>Status</strong></td>
      <td style="text-align:right">
        <?= $rcgl->status ?>
      </td>
    </tr>
  </table>
</div>
<?php include "footer.php" ?>