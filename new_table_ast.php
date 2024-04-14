<table cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
  <tr>
    <th nowrap="nowrap" rowspan="3">No.</th>
    <th nowrap="nowrap" rowspan="3">Nomor Laporan</th>
    <th nowrap="nowrap" rowspan="3">Regional</th>
    <th nowrap="nowrap" rowspan="3">Site Name</th>
    <th nowrap="nowrap" rowspan="3">Site ID</th>
    <th nowrap="nowrap" rowspan="3">PIC Regional</th>
    <th nowrap="nowrap" rowspan="2" colspan="3">Tanggal</th>
    <th nowrap="nowrap" rowspan="3">Penyebab kerugian</th>
    <th nowrap="nowrap" rowspan="3">Deductible</th>
    <th nowrap="nowrap" rowspan="2" colspan="7">Aset Tetap</th>
    <th nowrap="nowrap" rowspan="3" colspan="1">Quantity</th>
    <th nowrap="nowrap" rowspan="3" colspan="1">Satuan</th>
    <th nowrap="nowrap" rowspan="2" colspan="3">Or. Curr</th>
    <th nowrap="nowrap" rowspan="3">Rate IDR</th>
    <th nowrap="nowrap" rowspan="3">Total Amount</th>
    <th nowrap="nowrap" rowspan="2" colspan="10">Dokumen</th>
    <th nowrap="nowrap" rowspan="2" colspan="2">Proposed Adjustment</th>
    <th nowrap="nowrap" rowspan="2" colspan="2">Konfirmasi Proposed Adjustment</th>
    <th nowrap="nowrap" rowspan="1" colspan="6">Status Klaim</th>
  </tr>
  <tr>
    <th nowrap="nowrap" rowspan="1" colspan="2">Under Deductible</th>
    <th nowrap="nowrap" rowspan="1" colspan="2">Outstanding</th>
    <th nowrap="nowrap" rowspan="1" colspan="2">Setlement</th>
  </tr>
  <tr>
    <th nowrap="nowrap" rowspan="1">Kejadian</th>
    <th nowrap="nowrap" rowspan="1">Lapor HO</th>
    <th nowrap="nowrap" rowspan="1">Lapor SJU</th>

    <th nowrap="nowrap" rowspan="1" align="center">Kategori 1</th>
    <th nowrap="nowrap" rowspan="1" align="center">Kategori 2</th>
    <th nowrap="nowrap" rowspan="1" align="center">Kategori 3</th>
    <th nowrap="nowrap" rowspan="1" align="center">Kategori 4 (Item)</th>
    <th nowrap="nowrap" rowspan="1" align="center">Kategori 5(Merk)</th>
    <th nowrap="nowrap" rowspan="1" align="center">Kategori 6(Type)</th>
    <th nowrap="nowrap" rowspan="1" align="center">Kategori 7</th>
    <th nowrap="nowrap" rowspan="1">IDR</th>
    <th nowrap="nowrap" rowspan="1">EUR</th>
    <th nowrap="nowrap" rowspan="1">USD</th>
    <th nowrap="nowrap" rowspan="1">Surat tuntutan</th>
    <th nowrap="nowrap" rowspan="1">Laporan Awal</th>
    <th nowrap="nowrap" rowspan="1">BA Kehilangan</th>
    <th nowrap="nowrap" rowspan="1">BA Kronologi</th>
    <th nowrap="nowrap" rowspan="1">Rincian Kerugian</th>
    <th nowrap="nowrap" rowspan="1">BA Kepolisian</th>
    <th nowrap="nowrap" rowspan="1">Surat PMK</th>
    <th nowrap="nowrap" rowspan="1">Surat BMKG</th>
    <th nowrap="nowrap" rowspan="1">Foto</th>
    <th nowrap="nowrap" rowspan="1">PO</th>
    <th nowrap="nowrap" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="1">Amount</th>
    <th nowrap="nowrap" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="1">Amount</th>
    <th nowrap="nowrap" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="1">Amount</th>
    <th nowrap="nowrap" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="1">Amount</th>
    <th nowrap="nowrap" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="1">Amount</th>
  </tr>

  <?php $i = 1;
  foreach ($ast as $c): ?>
    <tr class="<?= $i % 2 == 0 ? 'odd' : 'even' ?>">
      <td>
        <?= $i ?>.
      </td>
      <td nowrap="nowrap"><?= $c->no_laporan ?></td>
      <td nowrap="nowrap" align="center"><?= $c->region ?></td>
      <td nowrap="nowrap" align="center"><?= $c->st_name ?></td>
      <td nowrap="nowrap" align="center"><?= $c->st_site_id ?></td>
      <td nowrap="nowrap" align="center"><?= $c->pic_region ?></td>
      <td nowrap="nowrap" align="center"><?= date("d/m/Y", strtotime($c->tgl_kejadian)) ?></td>
      <td nowrap="nowrap" align="center"><?= date("d/m/Y", strtotime($c->approve_at)) ?></td>
      <td nowrap="nowrap" align="center"><?= $c->submit_at ?></td>

      <?php if ($c->sebab == "nds") {
        $sebab = "Natural Dissaster (Bencana Alam)";
      } elseif ($c->sebab == "riot") {
        $sebab = "Riots/ Strikes, Malicious Damage (Kerusuhan)";
      } elseif ($c->sebab == "thf") {
        $sebab = "Theft (Pencurian)";
      } elseif ($c->sebab == "lit") {
        $sebab = "Lightning (Petir)";
      } elseif ($c->sebab == "etve") {
        $sebab = "Earthquake, Tsunami, Volcano Erruption";
      } elseif ($c->sebab == "fire") {
        $sebab = "Fire (Terbakar/ Kebakaran)";
      } elseif ($c->sebab == "3p") {
        $sebab = "Third Party (Tuntutan Pihak ketiga)";
      } else {
        $sebab = "Other Losses (Lainnya..)";
      }
      ?>
      <td nowrap="nowrap" align="center">
        <?= $sebab ?>
      </td>
      <td nowrap="nowrap" align="center"><?= $c->deduct ?></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"><?= $c->total ?></td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc1 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc1;
        }
        ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_lap == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_lap;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc2 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc2;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc3 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc3;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_rinci == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_rinci;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_pol == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_pol;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_pmk == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_pmk;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_bmkg == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_bmkg;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc5 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc5;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc4 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc4;
        } ?>
      </td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
    </tr>
    <?php $i++; endforeach; ?>
</table>

<p>
<table cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">

  <tr>
    <th nowrap="nowrap" rowspan="3">No.</th>
    <th nowrap="nowrap" rowspan="3">Nomor Laporan</th>
    <th nowrap="nowrap" rowspan="3">Regional</th>
    <th nowrap="nowrap" rowspan="3">Site Name</th>
    <th nowrap="nowrap" rowspan="3">Site ID</th>
    <th nowrap="nowrap" rowspan="3">PIC Regional</th>
    <th nowrap="nowrap" colspan="3" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="3">Penyebab kerugian</th>
    <th nowrap="nowrap" rowspan="3">Deductible</th>
    <th nowrap="nowrap" rowspan="3" colspan="1">Aset Tetap</th>
    <th nowrap="nowrap" rowspan="2" colspan="3">Or. Curr</th>
    <th nowrap="nowrap" rowspan="3">Rate IDR</th>
    <th nowrap="nowrap" rowspan="3">Total Amount</th>
    <th nowrap="nowrap" rowspan="2" colspan="10">Dokumen</th>
    <th nowrap="nowrap" rowspan="2" colspan="2">Proposed Adjustment</th>
    <th nowrap="nowrap" rowspan="2" colspan="2">Konfirmasi Proposed Adjustment</th>
    <th nowrap="nowrap" rowspan="1" colspan="2">Status Klaim</th>
  </tr>
  <tr>
    <th nowrap="nowrap" rowspan="1">Kejadian</th>
    <th nowrap="nowrap" rowspan="1">Lapor HO</th>
    <th nowrap="nowrap" rowspan="1">Lapor SJU</th>
    <th nowrap="nowrap" rowspan="1">IDR</th>
    <th nowrap="nowrap" rowspan="1">EUR</th>
    <th nowrap="nowrap" rowspan="1">USD</th>
    <th nowrap="nowrap" rowspan="1">Surat tuntutan</th>
    <th nowrap="nowrap" rowspan="1">Laporan Awal</th>
    <th nowrap="nowrap" rowspan="1">BA Kehilangan</th>
    <th nowrap="nowrap" rowspan="1">BA Kronologi</th>
    <th nowrap="nowrap" rowspan="1">Rincian Kerugian</th>
    <th nowrap="nowrap" rowspan="1">BA Kepolisian</th>
    <th nowrap="nowrap" rowspan="1">Surat PMK</th>
    <th nowrap="nowrap" rowspan="1">Surat BMKG</th>
    <th nowrap="nowrap" rowspan="1">Foto</th>
    <th nowrap="nowrap" rowspan="1">PO</th>
    <th nowrap="nowrap" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="1">Amount</th>
    <th nowrap="nowrap" rowspan="1">Tanggal</th>
    <th nowrap="nowrap" rowspan="1">Amount</th>
    <th nowrap="nowrap" rowspan="2">Tanggal</th>
    <th nowrap="nowrap" rowspan="2">Amount</th>
  </tr>

  <?php $i = 1;
  foreach ($ast as $c): ?>
    <tr class="<?= $i % 2 == 0 ? 'odd' : 'even' ?>">
      <td>
        <?= $i ?>.
      </td>
      <td nowrap="nowrap"><?= $c->no_laporan ?></td>
      <td nowrap="nowrap" align="center"><?= $c->region ?></td>
      <td nowrap="nowrap" align="center"><?= $c->st_name ?></td>
      <td nowrap="nowrap" align="center"><?= $c->st_site_id ?></td>
      <td nowrap="nowrap" align="center"><?= $c->pic_region ?></td>
      <td nowrap="nowrap" align="center"><?= date("d/m/Y", strtotime($c->tgl_kejadian)) ?></td>
      <td nowrap="nowrap" align="center"><?= date("d/m/Y", strtotime($c->approve_at)) ?></td>
      <td nowrap="nowrap" align="center"><?= $c->submit_at ?></td>

      <?php if ($c->sebab == "nds") {
        $sebab = "Natural Dissaster (Bencana Alam)";
      } elseif ($c->sebab == "riot") {
        $sebab = "Riots/ Strikes, Malicious Damage (Kerusuhan)";
      } elseif ($c->sebab == "thf") {
        $sebab = "Theft (Pencurian)";
      } elseif ($c->sebab == "lit") {
        $sebab = "Lightning (Petir)";
      } elseif ($c->sebab == "etve") {
        $sebab = "Earthquake, Tsunami, Volcano Erruption";
      } elseif ($c->sebab == "fire") {
        $sebab = "Fire (Terbakar/ Kebakaran)";
      } elseif ($c->sebab == "3p") {
        $sebab = "Third Party (Tuntutan Pihak ketiga)";
      } else {
        $sebab = "Other Losses (Lainnya..)";
      }
      ?>
      <td nowrap="nowrap" align="center">
        <?= $sebab ?>
      </td>
      <td nowrap="nowrap" align="center"><?= $c->deduct ?></td>
      <td colspan="1" rowspan="1">
        <table width="" border="0" cellpadding="1" cellspacing="1">
          <tr class="even">
            <th nowrap="nowrap" rowspan="1" align="center">No.</th>
            <th nowrap="nowrap" rowspan="1" align="center">Kategori 1</th>
            <th nowrap="nowrap" rowspan="1" align="center">Kategori 2</th>
            <th nowrap="nowrap" rowspan="1" align="center">Kategori 3</th>
            <th nowrap="nowrap" rowspan="1" align="center">Kategori 4 (Item)</th>
            <th nowrap="nowrap" rowspan="1" align="center">Kategori 5(Merk)</th>
            <th nowrap="nowrap" rowspan="1" align="center">Kategori 6(Type)</th>
            <th nowrap="nowrap" rowspan="1" align="center">Kategori 7</th>
            <th nowrap="nowrap" rowspan="1" align="center">Quantity</th>
            <th nowrap="nowrap" rowspan="1" align="center">Satuan</th>
          </tr>
          <?php
          $ast_detail = $db->get_results("SELECT * from ast_detail2 where ast_id='" . $c->no_laporan . "';");
          $a = 1;
          foreach ($ast_detail as $d):
            $ast_group = $db->get_row("SELECT * from category where item1='" . $c->item1 . "';");
          ?>
            <tr class="<?= $a % 2 == 0 ? 'even' : 'odd' ?>">
              <td>
                <?= $a ?>.
              </td>
              <td nowrap="nowrap" align="center"><?= $ast_group->group_name ?></td>
              <td nowrap="nowrap" align="center"><?= $ast_group->sub_cat1 ?></td>
              <td nowrap="nowrap" align="center"><?= $ast_group->sub_cat2 ?></td>
              <td nowrap="nowrap" align="center"><?= $d->item1 ?></td>
              <td nowrap="nowrap" align="center"><?= $d->merk ?></td>
              <td nowrap="nowrap" align="center"><?= $d->type ?></td>
              <td nowrap="nowrap" align="center"><?= $d->sub_cat7 ?></td>
              <td nowrap="nowrap" align="center"><?= $d->quantity ?></td>
              <td nowrap="nowrap" align="center"><?= $d->satuan ?></td>
            </tr>
            <?php $a++; endforeach; ?>
        </table>
      </td>

      <td nowrap="nowrap" align="center">&nbsp;</td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"><?= $c->total ?></td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc1 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc1;
        }
        ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_lap == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_lap;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc2 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc2;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc3 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc3;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_rinci == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_rinci;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_pol == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_pol;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_pmk == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_pmk;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc_bmkg == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc_bmkg;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc5 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc5;
        } ?>
      </td>
      <td nowrap="nowrap" align="center">
        <?php if ($c->doc4 == '0000-00-00' and 'NULL') {
          echo "Tidak Ada";
        } else {
          echo $c->doc4;
        } ?>
      </td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
      <td nowrap="nowrap" align="center"></td>
    </tr>
    <?php $i++; endforeach; ?>
</table>