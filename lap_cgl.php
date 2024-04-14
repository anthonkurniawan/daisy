<?php
require 'init.php';
require 'priviledges.php';
include "header.php"
  ?>
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
  <?php include "webheader.php"; ?>
  <tr valign="top">
    <td style="width:200px">
      <ul style="list-style:none;padding-left:5px">
        <?php include "menusuper.php" ?>
      </ul>
    </td>
    <td>
      <?php
      if ($_GET['m1'] && $_GET['m2'] && $_GET['t1'] && $_GET['t2']):
        $tempThn = '';
        if ($_GET['s'] <> '')
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
          case 'CLOSED':
            $and .= " AND caseclosed_at BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
            break;
        }

        if ($_GET['s'] <> '')
          $status = 'Status: <strong>' . $_GET['s'] . '</strong>';
        elseif ($_GET['m1'] <> '')
          $status .= ' yang dibuat ';
        if ($_GET['m1'] <> '' && $_GET['m1'] == $_GET['m2'] && $_GET['t1'] == $_GET['t2'])
          $status .= ' pada bulan <strong>' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'] . '</strong>';
        elseif ($_GET['m1'] <> '')
          $status .= ' dari bulan <strong>' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'] . '</strong> s/d <strong>' . $months[$_GET['m2'] - 1] . ' ' . $_GET['t2'] . '</strong>';

        if ($_GET['r'] <> '') {
          $and .= " AND r.kode_region='" . $_GET['r'] . "'";
          $rx = $db->get_row("SELECT * FROM region WHERE kode_region='" . $_GET['r'] . "'");
          $status .= " Regional <strong>" . $rx->region . "</strong>";
        }

        $rescgl = $db->get_results("SELECT cgl.kode_region,region,DATE_FORMAT(tgl_kejadian,'%Y') thn, cgl.*  FROM `cgl` 
				WHERE 1 " . $and . " ORDER BY thn DESC ,`updated_at` DESC");

        $status .= ' adalah sebanyak:<strong>' . $db->num_rows . '</strong> klaim.';
        //$db->debug();
      endif;
      ?>
      <div style="border:1px solid #ccc;margin:3px;padding:3px">
        <form method="get" action="">
          Status:
          <select name="s">
            <option value="">ALL</option>
            <option value="UNAPPROVED" <?= ($_GET['s'] == 'UNAPPROVED' ? 'selected="selected"' : '') ?>>UNAPPROVED</option>
            <option value="APPROVED" <?= ($_GET['s'] == 'APPROVED' ? 'selected="selected"' : '') ?>>APPROVED</option>
            <option value="SUBMITTED" <?= ($_GET['s'] == 'SUBMITTED' ? 'selected="selected"' : '') ?>>SUBMITTED</option>
            <option value="SURVEY" <?= ($_GET['s'] == 'SURVEY' ? 'selected="selected"' : '') ?>>SURVEY</option>
            <option value="PAYMENT" <?= ($_GET['s'] == 'PAYMENT' ? 'selected="selected"' : '') ?>>PAYMENT</option>
            <option value="INVOICE" <?= ($_GET['s'] == 'INVOICE' ? 'selected="selected"' : '') ?>>INVOICE</option>
            <option value="SETTLED" <?= ($_GET['s'] == 'SETTLED' ? 'selected="selected"' : '') ?>>SETTLED</option>
            <option value="CLOSED" <?= ($_GET['s'] == 'CLOSED' ? 'selected="selected"' : '') ?>>CLOSED</option>
          </select>

          Bulan
          <select name="m1">
            <?php for ($i = 1; $i <= 12; $i++): ?>
              <option value="<?= $i ?>" <?= ($_GET['m1'] == $i ? 'selected="selected"' : '') ?>>
                <?= $months[$i - 1] ?>
              </option>
            <?php endfor; ?>
          </select>

          <select name="t1">
            <?php for ($i = 2003; $i <= 2012; $i++) { ?>
              <option value="<?= $i ?>" <?= ($_GET['t1'] == $i || ($_GET['t1'] == '' && date("Y") == $i) ? 'selected="selected"' : '') ?>>
                <?= $i ?>
              </option>
            <?php } ?>
          </select>
          -
          <select name="m2">
            <?php for ($i = 1; $i <= 12; $i++): ?>
              <option value="<?= $i ?>" <?= ($_GET['m2'] == $i ? 'selected="selected"' : '') ?>>
                <?= $months[$i - 1] ?>
              </option>
            <?php endfor; ?>
          </select>

          <select name="t2">
            <?php for ($i = 2003; $i <= 2012; $i++) { ?>
              <option value="<?= $i ?>" <?= ($_GET['t2'] == $i || ($_GET['t2'] == '' && date("Y") == $i) ? 'selected="selected"' : '') ?>>
                <?= $i ?>
              </option>
            <?php
            } ?>
          </select>

          Regional:
          <select name="r">
            <option <?= ($_GET['r'] == $r->kode_region ? 'selected="selected"' : '') ?> value="">ALL (NASIONAL)</option>
            <?php
            $res = $db->get_results("SELECT * FROM `region` ORDER BY kode_region ASC");
            foreach ($res as $r) { ?>
              <option <?= ($_GET['r'] == $r->kode_region ? 'selected="selected"' : '') ?> value="<?= $r->kode_region ?>">
                <?= $r->region ?>
              </option>
            <?php
            }
            ?>
          </select>
          <input type="submit" value="Filter" />
          <?php if ($_GET['m1'] && $_GET['m2'] && $_GET['t1'] && $_GET['t2']): ?>
            <div style="text-align:right;margin-top:5px;">
              <input type="button" value="Export ke Excel 2003 (.xls)"
                onclick="window.open ('laporan_cgl_xls.php?s=<?= $_GET['s'] ?>&m1=<?= $_GET['m1'] ?>&t1=<?= $_GET['t1'] ?>&m2=<?= $_GET['m2'] ?>&t2=<?= $_GET['t2'] ?>&ui=<?= $user->user_id ?>&r=<?= $_GET['r'] ?>','daisy<?= rand() ?>','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
              <input type="button" value="Export ke Excel 2007 (.xlsx)"
                onclick="window.open ('laporan_cgl_xlsx.php?s=<?= $_GET['s'] ?>&m1=<?= $_GET['m1'] ?>&t1=<?= $_GET['t1'] ?>&m2=<?= $_GET['m2'] ?>&t2=<?= $_GET['t2'] ?>&ui=<?= $user->user_id ?>&r=<?= $_GET['r'] ?>','daisy<?= rand() ?>','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
              <input type="button" value="Print"
                onclick="window.open ('printTabelCGL.php?s=<?= $_GET['s'] ?>&m1=<?= $_GET['m1'] ?>&t1=<?= $_GET['t1'] ?>&m2=<?= $_GET['m2'] ?>&t2=<?= $_GET['t2'] ?>&r=<?= $_GET['r'] ?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
            </div>
          <?php endif; ?>
        </form>
      </div>
      <?php if ($_GET['m1'] && $_GET['m2'] && $_GET['t1'] && $_GET['t2']): ?>
        <div>Laporan CGL
          <?= $status ?>
        </div>
        <?php
        if (is_array($rescgl) && !empty($rescgl)) { ?>
          <?php $i = 1;
          foreach ($rescgl as $cgl) { ?>
            <?php if ($cgl->thn <> $tempThn) { ?>
              <?php $endTable = ($cgl->thn <> $tempThn && $tempThn <> '') ? 1 : 0 ?>
              <?php if ($endTable == 1) { ?>
              </td>
            </tr>
          </table>
        <?php } ?>
        <strong>Tahun Kejadian <?= $cgl->thn ?></strong><br />
        <table width="98%" class="tabel" cellpadding="0" cellspacing="0" border="0">
          <tr>
            <th>No.</th>
            <th>Regional</th>
            <th>No Laporan</th>
            <th>Tgl Kejadian</th>
            <th>Tgl Diketahui Telkomsel</th>
            <th>Site ID</th>
            <th>Sebab Kerusakan</th>
            <th>Rincian Kerusakan</th>
            <th>Estimasi Kerugian</th>
            <th>Contact Person</th>
            <th>Status</th>
            <th>Aksi</th>
          </tr>

          <tr class="<?= ($i % 2 == 0 ? 'even' : 'odd') ?>" valign="top">
            <td>
              <?= $i ?>.
            </td>
            <td nowrap="nowrap"><?= $cgl->region ?></td>
            <td nowrap="nowrap"><?= $cgl->no_laporan ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_kejadian)) ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_tuntutan)) ?></td>
            <td><?= $cgl->st_site_id ?><br /><?= $cgl->st_name ?></td>
            <td><?= $cgl->sebab ?>
            <td><?= $cgl->rincian ?></td>
            <td><?= $cgl->estimasi ?></td>
            </td>
            <td>Nama:<?= $cgl->cp_nama ?><br />
              Telp:<?= $cgl->cp_telp ?><br />
              HP:<?= $cgl->cp_hp ?>
            </td>
            <td style="text-align:center"> <?= ucfirst(strtolower($cgl->status)) ?></td>
            <td style="text-align:center">
              <?php
              switch ($cgl->status) {
                case 'UNAPPROVED':
                  switch ($user->role) {
                    case 'mgrr':
                      $aksi = '<strong>Approval</strong>';
                      break;
                    case 'spvr':
                      $aksi = 'Revisi';
                      break;
                    case 'stfp':
                    case 'spvp':
                    case 'gmp':
                      $aksi = 'View';
                      break;
                  }
                  break;
                case 'REJECTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Revisi</strong>' : 'View';
                  break;
                case 'APPROVED':
                  $aksi = $user->role == 'stfp' || $user->role == 'spvp' ? '<strong>Commit Submit</strong>' : 'View';
                  break;
                case 'SUBMITTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Survey</strong>' : 'View';
                  break;
                case 'SURVEY':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Payment</strong>' : 'View';
                  break;
                case 'PAYMENT':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Invoice</strong>' : 'View';
                  break;
                case 'INVOICE':
                  $aksi = $user->role == 'spvp' ? '<strong>Commit Settled</strong>' : 'View';
                  break;
                case 'SETTLED':
                case 'CLOSED':
                default:
                  $aksi = 'View';
                  break;
              } ?>
              <a href="view_cgl.php?i=<?= $cgl->cgl_id ?>">
                <?= $aksi ?>
              </a>
            </td>
          </tr>
        <?php
            } else { ?>
          <tr class="<?= ($i % 2 == 0 ? 'even' : 'odd') ?>" valign="top">
            <td>
              <?= $i ?>.
            </td>
            <td nowrap="nowrap"><?= $cgl->region ?></td>
            <td nowrap="nowrap"><?= $cgl->no_laporan ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_kejadian)) ?></td>
            <td nowrap="nowrap"><?= date("d-m-Y", strtotime($cgl->tgl_tuntutan)) ?></td>
            <td><?= $cgl->st_site_id ?><br /><?= $cgl->st_name ?></td>
            <td><?= $cgl->sebab ?>
            <td><?= $cgl->rincian ?></td>
            <td><?= $cgl->estimasi ?></td>
            </td>
            <td>Nama:<?= $cgl->cp_nama ?><br />
              Telp:<?= $cgl->cp_telp ?><br />
              HP:<?= $cgl->cp_hp ?>
            </td>
            <td style="text-align:center"> <?= ucfirst(strtolower($cgl->status)) ?> </td>
            <td style="text-align:center">
              <?php
              switch ($cgl->status) {
                case 'UNAPPROVED':
                  switch ($user->role) {
                    case 'mgrr':
                      $aksi = '<strong>Approval</strong>';
                      break;
                    case 'spvr':
                      $aksi = 'Revisi';
                      break;
                    case 'stfp':
                    case 'spvp':
                    case 'gmp':
                      $aksi = 'View';
                      break;
                  }
                  break;
                case 'REJECTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Revisi</strong>' : 'View';
                  break;
                case 'APPROVED':
                  $aksi = $user->role == 'stfp' || $user->role == 'spvp' ? '<strong>Commit Submit</strong>' : 'View';
                  break;
                case 'SUBMITTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Survey</strong>' : 'View';
                  break;
                case 'SURVEY':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Payment</strong>' : 'View';
                  break;
                case 'PAYMENT':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Invoice</strong>' : 'View';
                  break;
                case 'INVOICE':
                  $aksi = $user->role == 'spvp' ? '<strong>Commit Settled</strong>' : 'View';
                  break;
                case 'SETTLED':
                case 'CLOSED':
                default:
                  $aksi = 'View';
                  break;
              } ?>
              <a href="view_cgl.php?i=<?= $cgl->cgl_id ?>">
                <?= $aksi ?>
              </a>
            </td>
          </tr>
        <?php } ?>
        <?php $tempThn = $cgl->thn;
        $i++;
          } ?>
      <?php
        }
      endif;
      ?>
  </td>
  </tr>
</table>
<?php include "footer.php" ?>