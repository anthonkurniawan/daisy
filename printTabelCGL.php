<?php
require 'init.php';
include "headerPrint.php";
require 'priviledges.php'; ?>
<div style="margin:2px;padding:3px;background:#fff;border:1px solid #ccc">
  <?php
  $tempThn = '';
  if ($_GET['s'] <> '' && $_GET['s'] <> '0')
    $and .= " AND `status`='{$_GET['s']}'";
  $lastD2 = 30;
  if (in_array($_GET['m2'], array(1, 3, 5, 7, 8, 10, 12)))
    $lastD2 = 31;
  if ($_GET['m2'] == 2 && $_GET['t2'] % 4 == 0)
    $lastD2 = 29;
  if ($_GET['m2'] == 2 && $_GET['t2'] % 4 != 0)
    $lastD2 = 28;

  switch ($_GET['s']) {
    case '':
    case '0':
      if ($_GET['m1'] <> '')
        $and .= " AND created_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'UNAPPROVED':
    case 'REJECTED':
      $and .= " AND updated_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'APPROVED':
      $and .= " AND approve_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'SUBMITTED':
      $and .= " AND submit_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'SURVEY':
      $and .= " AND survey_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'PAYMENT':
      $and .= " AND payment_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'INVOICE':
      $and .= " AND invoice_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'SETTLED':
      $and .= " AND settlement_date BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
    case 'CASECLOSED':
      $and .= " AND caseclosed_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
      break;
  }
  if ($_GET['s'] <> '' && $_GET['s'] <> '0')
    $status = 'Status: <strong>' . $_GET['s'] . '</strong>';
  elseif ($_GET['m1'] <> '')
    $status .= ' yang dibuat ';
  if ($_GET['m1'] <> '' && $_GET['m1'] == $_GET['m2'] && $_GET['t1'] == $_GET['t2'])
    $status .= ' pada bulan <strong>' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'] . '</strong>';
  elseif ($_GET['m1'] <> '')
    $status .= ' dari bulan <strong>' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'] . '</strong> s/d <strong>' . $months[$_GET['m2'] - 1] . ' ' . $_GET['t2'] . '</strong>';


  if ($user->role == 'spvr' || $user->role == 'mgrr') {
    $and .= " AND `user_id`='" . $user->user_id . "' ";
    $and .= " AND kode_region='" . $user->regional . "'";
  } elseif ($_GET['r'] != '') {
    $and .= " AND kode_region='" . $_GET['r'] . "'";
  }
  $SQL = "SELECT r.kode_region,region,EXTRACT(YEAR FROM tgl_kejadian) thn,cgl.* FROM `cgl` 
		WHERE 1 " . $and . " ORDER BY thn DESC ,`updated_at` DESC";

  $rescgl = $db->get_results($SQL);
  ?>

  <?php
  if (is_array($rescgl) && !empty($rescgl)) { ?>
    <h3>Laporan Klaim CGL
      <?= $status ?>
    </h3>
    <?php $i = 1;
    foreach ($rescgl as $cgl) { ?>
      <?php if ($cgl->thn <> $tempThn) { ?>
        <?php $endTable = ($cgl->thn <> $tempThn && $tempThn <> '') ? 1 : 0 ?>
        <?php if ($endTable == 1) { ?>
          </table>
        <?php } ?>
        <strong>Tahun Kejadian <?= $cgl->thn ?></strong><br />
        <table width="98%" cellpadding="3" cellspacing="0" border="1">
          <tr style="background:#efefef">
            <th>No.</th>
            <th>Regional</th>
            <th>No Laporan</th>
            <th>Tgl Kejadian</th>
            <th>Tgl Diketahui</th>
            <th>Site ID</th>
            <th>Sebab Kerusakan</th>
            <th>Rincian Kerusakan</th>
            <th>Estimasi Kerugian</th>
            <th>Contact Person</th>
            <th>Status</th>
          </tr>
          <tr valign="top">
            <td>
              <?= $i ?>.
            </td>
            <td nowrap="nowrap"><?= $cgl->region ?></td>
            <td nowrap="nowrap"><?= $cgl->no_laporan ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_kejadian)) ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_tuntutan)) ?></td>
            <td><?= $cgl->st_site_id ?></td>
            <td><?= $cgl->sebab ?>
            <td><?= $cgl->rincian ?></td>
            <td><?= $cgl->estimasi ?></td>
            </td>
            <td>Nama:<?= $cgl->cp_nama ?><br />
              Telp:<?= $cgl->cp_telp ?><br />
              HP:<?= $cgl->cp_hp ?>
            </td>
            <td style="text-align:center">
              <?= ucfirst(strtolower($cgl->status)) ?>
            </td>
          </tr>
        <?php } else { ?>
          <tr valign="top">
            <td>
              <?= $i ?>.
            </td>
            <td nowrap="nowrap"><?= $cgl->region ?></td>
            <td nowrap="nowrap"><?= $cgl->no_laporan ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_kejadian)) ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_tuntutan)) ?></td>
            <td><?= $cgl->st_site_id ?></td>
            <td><?= $cgl->sebab ?>
            <td><?= $cgl->rincian ?></td>
            <td><?= $cgl->estimasi ?></td>
            </td>
            <td>Nama:<?= $cgl->cp_nama ?><br />
              Telp:<?= $cgl->cp_telp ?><br />
              HP:<?= $cgl->cp_hp ?>
            </td>
            <td style="text-align:center">
              <?= ucfirst(strtolower($cgl->status)) ?>
            </td>
          </tr>
        <?php } ?>
        <?php $tempThn = $cgl->thn;
        $i++;
    } ?>
      <?php
  } else {
    echo 'No data found';
  }
  ?>
    </td>
    </tr>
  </table>
</div>
<?php include "footer.php" ?>