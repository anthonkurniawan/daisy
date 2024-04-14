<?php
require 'init.php';
include "headerPrint.php" ?>
<div style="margin:2px;padding:3px;background:#fff;border:1px solid #ccc">
  <?php
  if ($_GET['p'] == '1')
    $and .= " AND created_at BETWEEN '" . ($_GET['t1'] - 1) . "-12-01 00:00:00' AND '" . $_GET['t2'] . "-06-30 23:59:59'";
  else
    $and .= " AND created_at BETWEEN '" . $_GET['t1'] . "-08-02 00:00:00' AND '" . $_GET['t2'] . "-12-31 23:59:59'";

  if ($_GET['r'] <> '') {
    $and .= " AND kode_region='" . $_GET['r'] . "'";
    $rx = $db->get_row("SELECT * FROM region WHERE kode_region='" . $_GET['r'] . "'");
    $status .= " Regional <strong>" . $rx->region . "</strong>";
  }

  switch ($_GET['s1']) {
    case '1':
      $order .= " site.st_site_id ASC,";
      break;
    case '2':
      $order .= " region ASC ASC,";
      break;
    case '3':
      $order .= " submit_at DESC,";
      break;
    case '4':
      $order .= " tgl_kejadian DESC,";
      break;
    case '5':
      $order .= " tgl_tuntutan DESC,";
      break;
    case '6':
      $order .= " st ASC,";
      break;
  }
  switch ($_GET['s2']) {
    case '1':
      $order .= " site.st_site_id ASC,";
      break;
    case '2':
      $order .= " region ASC ASC,";
      break;
    case '3':
      $order .= " submit_at DESC,";
      break;
    case '4':
      $order .= " tgl_kejadian DESC,";
      break;
    case '5':
      $order .= " tgl_tuntutan DESC,";
      break;
    case '6':
      $order .= " st ASC,";
      break;
  }

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
				) as st FROM `cgl` 
				JOIN cgl_vendor v ON v.id_cglv=cgl.id_cglv
				WHERE 1 " . $and . " ORDER BY {$order} cgl_id DESC";

  if ($_GET['l'] != '3')
    $cgl = $db->get_results($SQL1);
  ?>
  <?php if ($_GET['l'] == '1') { ?>
    <br /><strong>REKAPITULASI KLAIM CGL PT TELKOMSEL</strong><br />
    <strong>REGIONAL :
      <?php
      if ($_GET['r'] <> '') {
        $reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
        echo $reg->region;
      } else
        echo 'NASIONAL';
      ?><br />
      TAHUN:
      <?= $_GET['t'] ?> (periode Polis
      <?= $_GET['p'] == 1 ? '31 Des 2010 - 30 Jun 2012' : '2 Ags 2010 - 31 Des 2010' ?>)<br />
      NO. POLIS:
      <?= $_GET['p'] == '1' ? '202.718.300.10.00020/000/000' : '202.718.300.10.00012/000/000' ?>
    </strong><br />
    <table cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
      <tr>
        <th nowrap="nowrap" rowspan="2">No.</th>
        <th nowrap="nowrap" rowspan="2">Nomor Laporan</th>
        <th nowrap="nowrap" rowspan="2">Site Name</th>
        <th nowrap="nowrap" rowspan="2">Site ID</th>
        <th nowrap="nowrap" rowspan="2">Regional</th>
        <th nowrap="nowrap" rowspan="2">Tanggal Lapor SJU</th>
        <th nowrap="nowrap" rowspan="2">Tanggal kejadian</th>
        <th nowrap="nowrap" rowspan="2">Tanggal diketahui</th>
        <th nowrap="nowrap" rowspan="2">Penyebab Kerugian</th>
        <th nowrap="nowrap" rowspan="2">Estimasi Kerugian (BoQ)</th>
        <th nowrap="nowrap" rowspan="2">Nilai ganti rugi (Invoice)</th>
        <th nowrap="nowrap" rowspan="2">Vendor Pelaksana</th>
        <th nowrap="nowrap" rowspan="2">Status Klaim</th>
        <th nowrap="nowrap" colspan="3">Dokumen Pendukung</th>
      </tr>
      <tr>
        <th nowrap="nowrap">Surat<br />Tuntutan Warga</th>
        <th nowrap="nowrap">Dokumen<br />BoQ</th>
        <th nowrap="nowrap">Surat<br />Tuntutan Telkomsel</th>
      </tr>
      <?php $i = 1;
      foreach ($cgl as $c): ?>
        <tr class="<?= $i % 2 == 0 ? 'odd' : 'even' ?>">
          <td>
            <?= $i ?>.
          </td>
          <td nowrap="nowrap"><?= $c->no_laporan ?></td>
          <td><?= $c->st_name ?></td>
          <td><?= $c->st_site_id ?></td>
          <td><?= $c->region ?></td>
          <td><?= $c->submit_at ?></td>
          <td>
            <?= date("d / m / Y", strtotime($c->tgl_kejadian)) ?>
          </td>
          <td>
            <?= date("d / m / Y", strtotime($c->tgl_tuntutan)) ?>
          </td>
          <td><?= $c->sebab ?>     <?= $c->rincian ?></td>
          <td><?= $c->estimasi ?></td>
          <td><?= $c->nilai_invoice ?></td>
          <td><?= $c->nama_vendor ?></td>
          <td><?= $c->status ?></td>
          <td><?= $c->file_surat_tuntutan != '' ? 'ADA' : 'BELUM ADA' ?></td>
          <td><?= $c->file_boq != '' ? 'ADA' : 'BELUM ADA' ?></td>
          <td><?= $c->file_invoice != '' ? 'ADA' : 'BELUM ADA' ?></td>
        </tr>
        <?php $i++; endforeach; ?>
    </table>
  <?php } ?>
  <?php if ($_GET['l'] == '2') { ?>
    <br /><strong>REPORT DETAIL PROGRESS CGL PT TELKOMSEL</strong><br />
    <strong>REGIONAL :
      <?php
      if ($_GET['r'] <> '') {
        $reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
        echo $reg->region;
      } else
        echo 'NASIONAL';
      ?><br />
      TAHUN:
      <?= $_GET['t'] ?> (periode Polis
      <?= $_GET['p'] == 1 ? '31 Desember - 30 Juni' : '2 Agustus - 31 Desember' ?>)<br />
      NO. POLIS:
      <?= $_GET['no'] ?>
    </strong><br />
    <table width="100%" cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
      <tr>
        <th nowrap="nowrap" rowspan="2">No.</th>
        <th nowrap="nowrap" rowspan="2">Nomor Laporan</th>
        <th nowrap="nowrap" rowspan="2">Site Name</th>
        <th nowrap="nowrap" rowspan="2">Site ID</th>
        <th nowrap="nowrap" rowspan="2">Regional</th>
        <th nowrap="nowrap" rowspan="2">Tanggal Lapor SJU</th>
        <th nowrap="nowrap" rowspan="2">Tanggal kejadian</th>
        <th nowrap="nowrap" rowspan="2">Tanggal diketahui</th>
        <th nowrap="nowrap" rowspan="2">Penyebab Kerugian</th>
        <th nowrap="nowrap" rowspan="2">Estimasi Kerugian (BoQ)</th>
        <th nowrap="nowrap" rowspan="2">Nilai ganti rugi (Invoice)</th>
        <th nowrap="nowrap" rowspan="2">Vendor Pelaksana</th>
        <th nowrap="nowrap" rowspan="2">Status Klaim</th>
        <th nowrap="nowrap" rowspan="2">Dokumen BoQ</th>
        <th nowrap="nowrap" colspan="8">Timeline Progress</th>
      </tr>
      <tr>
        <th nowrap="nowrap">Create</th>
        <th nowrap="nowrap">Approve</th>
        <th nowrap="nowrap">Submit</th>
        <th nowrap="nowrap">Survey</th>
        <th nowrap="nowrap">Payment</th>
        <th nowrap="nowrap">Claim Letter</th>
        <th nowrap="nowrap">Settled</th>
        <th nowrap="nowrap">Closed</th>
      </tr>
      <?php $i = 1;
      foreach ($cgl as $c): ?>
        <tr class="<?= $i % 2 == 0 ? 'odd' : 'even' ?>">
          <td>
            <?= $i ?>.
          </td>
          <td nowrap="nowrap"><?= $c->no_laporan ?></td>
          <td><?= $c->st_name ?></td>
          <td><?= $c->st_site_id ?></td>
          <td><?= $c->region ?></td>
          <td><?= $c->submit_at ?></td>
          <td>
            <?= date("d / m / Y", strtotime($c->tgl_kejadian)) ?>
          </td>
          <td>
            <?= date("d / m / Y", strtotime($c->tgl_tuntutan)) ?>
          </td>
          <td><?= $c->sebab ?>     <?= $c->rincian ?></td>
          <td><?= $c->estimasi ?></td>
          <td><?= $c->nilai_invoice ?></td>
          <td><?= $c->nama_vendor ?></td>
          <td><?= $c->status ?></td>
          <td><?= $c->file_boq <> '' ? 'ADA' : 'BELUM ADA' ?></td>
          <td><?= $c->created_at ? date("d/m/Y", strtotime($c->created_at)) : '' ?></td>
          <td><?= $c->approve_at ? date("d/m/Y", strtotime($c->approve_at)) : '' ?></td>
          <td><?= $c->submit_at ? date("d/m/Y", strtotime($c->submit_at)) : '' ?></td>
          <td><?= $c->survey_date ? date("d/m/Y", strtotime($c->survey_date . ' 00:00:00')) : '' ?></td>
          <td><?= $c->payment_date ? date("d/m/Y", strtotime($c->payment_date . ' 00:00:00')) : '' ?></td>
          <td><?= $c->invoice_date ? date("d/m/Y", strtotime($c->invoice_date . ' 00:00:00')) : '' ?></td>
          <td><?= $c->settled_date ? date("d/m/Y", strtotime($c->settled_date . ' 00:00:00')) : '' ?></td>
          <td><?= $c->caseclosed_at ? date("d/m/Y", strtotime($c->caseclosed_at)) : '' ?></td>
        </tr>
        <?php $i++; endforeach; ?>
    </table>
  <?php } ?>
  <?php if ($_GET['l'] == '3') {
    $SQL3 = "SELECT COUNT(1) jml, `status`,EXTRACT(MONTH FROM `created_at`) bln FROM cgl 
					JOIN site ON site.st_site_id=cgl.st_site_id 
					JOIN region r ON r.kode_region=site.kode_region
					JOIN cgl_vendor v ON v.id_cglv=cgl.id_cglv
					WHERE 1 " . $and . " 
					GROUP BY `status`, bln";
    $res3 = $db->get_results($SQL3);
    //$db->debug();
    $summ = array();
    foreach ($res3 as $r3) {
      $summ[$r3->status][$r3->bln] = $r3->jml;
    }
    ?>
    <br /><strong>SUMMARY REPORT KLAIM CGL PT TELKOMSEL</strong><br />
    <strong>REGIONAL :
      <?php
      if ($_GET['r'] <> '') {
        $reg = $db->get_row("SELECT * FROM region WHERE kode_region='{$_GET['r']}'");
        echo $reg->region;
      } else
        echo 'NASIONAL';
      ?><br />
      TAHUN:
      <?= $_GET['t'] ?> (periode Polis
      <?= $_GET['p'] == 1 ? '31 Desember - 30 Juni' : '2 Agustus - 31 Desember' ?>)<br />
      NO. POLIS:
      <?= $_GET['no'] ?>
    </strong><br />
    <table cellpadding="3" cellspacing="0" border="1" style="font-size:8pt">
      <tr>
        <th rowspan="2">Status</th>
        <th colspan="12">Bulan</th>
      </tr>
      <tr>
        <?php foreach ($months as $m): ?>
          <th style="width:40px;">
            <?= $m ?>
          </th>
        <?php endforeach; ?>
      </tr>
      <tr>
        <td>Created</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['UNAPPROVED'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Approve</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['APPROVED'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Submitted (Total Klaim)</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['SUBMITTED'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Outstanding</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['UNAPPROVED'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Survey</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['SURVEY'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Payment</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['PAYMENT'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Invoice</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['INVOICE'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Settled</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['SETTLED'][$i] ?>
          </td>
        <?php } ?>
      </tr>
      <tr>
        <td>Closed Case</td>
        <?php for ($i = 1; $i <= 12; $i++) { ?>
          <td style="text-align:right">
            <?= $summ['CLOSED'][$i] ?>
          </td>
        <?php } ?>
      </tr>
    </table>
  <?php } ?>
</div>
<?php include "footer.php" ?>