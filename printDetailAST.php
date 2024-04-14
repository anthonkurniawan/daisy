<?php
require 'init.php';
include "headerPrint.php";
require 'priviledges.php';
?>
<div style="margin:2px;padding:3px;background:#fff;border:1px solid #ccc">

  <?php
  $SQL = "SELECT * FROM `ast2` WHERE 1 " . $and . " AND ast_id ='" . $_GET['ast'] . "'";
  $rast = $db->get_row($SQL);

  $sebab = $rast->sebab;
  function sebab($sebab)
  {
    if ($sebab == 'nds')
      echo 'Natural Dissaster (Bencana Alam)';
    elseif ($sebab == 'thf') {
      echo 'Theft (Pencurian)';
    } elseif ($sebab == 'lit') {
      echo 'Lightning (Petir)';
    } elseif ($sebab == "etv") {
      echo "Earthquake, Tsunami, Volcano Erruption";
    } elseif ($sebab == 'fre') {
      echo 'Fire (Terbakar/ Kebakaran)';
    } elseif ($sebab == 'trp') {
      echo 'Third Party (Tuntutan Pihak ketiga)';
    } else {
      echo 'Other Losses (Lainnya..)';
    }
  }
  ?>

  <h3>Klaim AST <?= $rast->no_laporan ?></h3>
  <table id="fast">
    <tr class="even">
      <td width="558">Hari, tanggal kejadian</td>
      <td width="425">
        <?= date("l, d F Y", strtotime($rast->tgl_kejadian)) ?>
      </td>
    </tr>
    <tr class="odd">
      <td>Hari, tanggal Lapor SJU</td>
      <td>
        <?php if ($rast->submit_at == '' || $rast->submit_at == '0000-00-00') {
          echo "-";
        } else {
          echo date("l, d F Y", strtotime($rast->submit_at));
        } ?>
      </td>
    </tr>
    <tr class="even">
      <td>Tempat / lokasi kerugian</td>
      <td>
        <?php
        $r = $db->get_row("SELECT * FROM `site` WHERE st_site_id='" . $rast->st_site_id . "'");
        echo $rast->st_site_id . ' / ' . $r->st_name;
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
              <td><?= $rast->st_longitude ?></td>
            </tr>
            <tr class="odd">
              <td nowrap="nowrap">Latitude</td>
              <td><?= $rast->st_latitude ?></td>
            </tr>
            <tr valign="top" class="even">
              <td>Alamat Site</td>
              <td><?= $rast->st_address ?></td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
    <tr class="even" valign="top">
      <td>Status Claim</td>
      <td><?= $rast->status_claim == 'total' ? 'Total Loss' : 'Partial Loss' ?></td>
    </tr>
    <tr valign="top" class="odd">
      <td>Cause of Damage</td>
      <td>
        <?php sebab($sebab) ?>
      </td>
    </tr>
    <tr valign="top" class="even">
      <td>Rincian Kerusakan</td>
      <td>
        <?php
        $ast_detail = $db->get_results("SELECT * from ast_detail2 WHERE no_laporan='" . $rast->no_laporan . "'");
        ?>
        <table width="100%">
          <tr>
            <th>Item</th>
            <th>Merk</th>
            <th>Type</th>
            <th>Quantity</th>
            <th>Satuan</th>
            <th>Tarikan</th>
            <td>&nbsp;</td>
            <?php if ($rast->status == 'APPROVED' || $rast->status == 'SUBMITTED' && $user->role == 'spvp') { ?>
              <th>Currency</th>
              <th>Price/item</th>
              <th>Jumlah</th>
            <?php } ?>
          </tr>

          <?php
          $c = 1;
          foreach ($ast_detail as $d) { ?>
            <tr class="<?= $i++;
            $c % 2 == 0 ? 'odd' : 'even' ?>">
              <td nowrap="nowrap"><?= $d->item1 ?></td>
              <td nowrap="nowrap"><?= $d->merk ?></td>
              <td nowrap="nowrap"><?= $d->type ?></td>
              <td align="center">
                <?= $d->quantity ?>
                <input name="quantity<?= $i ?>" type="hidden" id="quan" value="<?= $d->quantity ?>" />
              </td>
              <td align="center"><?= $d->satuan ?></td>
              <td align="center"><?= $d->tarikan ?></td>
              <td>&nbsp;</td>
              <?php
              if ($rast->status == 'APPROVED' || $rast->status == 'SUBMITTED' && $user->role == 'spvp') { ?>
                <td align="center">
                  <?= $d->currency ?>
                </td>
                <td align="center">
                  <?= number_format($d->price) ?>
                </td>
                <td align="center">
                  <?= number_format($d->jumlah) ?>
                </td>
              <?php } ?>
            </tr>

            <?php $c++;
          } ?>
          <?php if ($rast->status == 'APPROVED' || $rast->status == 'SUBMITTED' && $user->role == 'spvp') { ?>
            <tr>
              <td colspan="8">&nbsp;</td>
              <td align="right"><strong>Total :</strong></td>
              <td align="center">
                <?= number_format($rast->estimasi) ?>
              </td>
            </tr>
          <?php } ?>
        </table>
        <input name="count" type="hidden" id="count" value="<?= $i ?>" />
      </td>
    </tr>

    <?php if ($rast->status == 'APPROVED' || $rast->status == 'SUBMITTED' && $user->role == 'spvp') { ?>
      <tr class="odd">
        <td>Nilai Estimasi</td>
        <td><?= number_format($rast->estimasi) ?></td>
      </tr>
    <?php } ?>
    <tr class="odd">
      <td>Nilai Deductible</td>
      <td>
        <?= number_format($rast->deduct) ?>
      </td>
    </tr>
    <tr class="even">
      <td>Dokumen 1
        <div class="keterangan">Berita acara kerusakan atau kehilangan Aktiva Tetap (Rincian Objek yang Mengalami
          Kerugian)</div>
      </td>
      <td><?= $rast->doc_hil_file == '0000-00-00' ? 'Tidak Ada' : 'Ada' ?></td>
    </tr>
    <tr class="even">
      <td>Dokumen 2
        <div class="keterangan">Kronologi kejadian/ kerugian</div>
      </td>
      <td><?= $rast->doc_kro_file == '0000-00-00' ? 'Tidak Ada' : 'Ada' ?></td>
    </tr>
    <tr class="even">
      <td>Dokumen 3
        <div class="keterangan">Foto Objek Kerugian</div>
      </td>
      <td><?= $rast->doc_fo_file == '0000-00-00' ? 'Tidak Ada' : 'Ada' ?></td>
    </tr>
    <tr class="even">
      <td>Dokumen 4
        <div class="keterangan">Dokumen Rincian Kerugian</div>
      </td>
      <td><?= $rast->doc_rinci_file == '0000-00-00' ? 'Tidak Ada' : 'Ada' ?></td>
    </tr>

    <?php if ($rast->sebab == 'thf' || $rast->sebab == 'rio' || $rast->sebab == 'trp') { ?>
      <tr class="even">
        <td>Dokumen
          <div class="keterangan">Dokumen Khusus (BA Kepolisian)</div>
        </td>
        <td><?= $rast->doc_pol_file == '0000-00-00' ? 'Tidak Ada' : 'Ada' ?></td>
      </tr>
    <?php }
    if ($rast->sebab == 'fre') { ?>
      <tr class="even">
        <td>Dokumen
          <div class="keterangan">Dokumen Khusus (Surat PMK)</div>
        </td>
        <td><?= $rast->doc_pmk_file == '0000-00-00' ? 'Tidak Ada' : 'Ada' ?></td>
      </tr>
    <?php }

    if ($rast->sebab == 'lit' || $rast->sebab == 'nds' || $rast->sebab == 'etv') { ?>
      <tr class="even">
        <td>Dokumen
          <div class="keterangan">Dokumen Khusus (Surat BMKG)</div>
        </td>
        <td><?= $rast->doc_bmkg_file == '0000-00-00' ? 'Tidak Ada' : 'Ada' ?></td>
      </tr>
    <?php } ?>
    <tr>
      <td>&nbsp;</td>
    </tr>

    <!--------------------------- DOKUMENT OLEH HO ---------------------------------------------------------->
    <?php if ($rast->status == 'SUBMITTED' && $user->role == 'spvp') { ?>
      <tr>
        <td>
          <div>Dokumen </div>Surat tuntutan/ pengajuan klaim dari tertanggung
        </td>
        <td>
          <div class="keterangan"><?= $rast->doc_tun_file == '' ? '-belum ada-' : 'Ada' ?></div>
        </td>
      </tr>
      <tr class="even">
        <td>
          <div>Dokumen</div>PO/ Kontrak/ Price list/ Kwitansi perbaikan/ pembelian perangkat/ Dokumen lain yang
          menjelaskan nilai kerugian
        </td>
        <td>
          <div class="keterangan"><?= $rast->doc_po_file == '' ? '-belum ada-' : 'Ada' ?></div>
  </div>
  </td>
  </tr>
<?php } ?>
<tr class="odd">
  <td><strong>Status</strong></td>
  <td style="text-align:right"><?= $rast->status ?></td>
</tr>
</table>
</div>
<?php include "footer.php" ?>