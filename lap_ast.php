<?php
require 'init.php';
require 'priviledges.php';
include "header.php"
?>
<table width="1250" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
  <?php include "webheader.php"; ?>
  <tr valign="top">
    <td width="200" style="width:150px">
      <ul style="list-style:none;padding-left:5px">
        <?php include "menusuper.php" ?>
      </ul>
    </td>
    <td width="814">
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

        if ($_GET['k'] <> '') {
          $and .= " AND status_progress='" . $_GET['k'] . "'";
        }

        $resast = $db->get_results("SELECT ast2.kode_region,region,DATE_FORMAT(tgl_kejadian,'%Y') thn, ast2.*  FROM `ast2` 
				WHERE 1 " . $and . " ORDER BY thn DESC ,`updated_at` DESC");

        $status .= ' adalah sebanyak:<strong>' . $db->num_rows . '</strong> klaim.';
      endif;
      ?>
      <div style="border:1px solid #ccc;margin:3px;padding:3px">
        <form method="get" action="">
          Status Klaim :
          <select name="k">
            <option value="">ALL</option>
            <option value="1" <?= ($_GET['k'] == '1' ? 'selected="selected"' : '') ?>>Outstanding</option>
            <option value="2" <?= ($_GET['k'] == '2' ? 'selected="selected"' : '') ?>>Under Deductible</option>
            <option value="3" <?= ($_GET['k'] == '3' ? 'selected="selected"' : '') ?>>Settlement</option>
          </select>

          Progress:
          <select name="s">
            <option value="">ALL</option>
            <option value="UNAPPROVED" <?= ($_GET['s'] == 'UNAPPROVED' ? 'selected="selected"' : '') ?>>UNAPPROVED</option>
            <option value="APPROVED" <?= ($_GET['s'] == 'APPROVED' ? 'selected="selected"' : '') ?>>APPROVED</option>
            <option value="SUBMITTED" <?= ($_GET['s'] == 'SUBMITTED' ? 'selected="selected"' : '') ?>>SUBMITTED</option>
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
            <?php } ?>
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
            <?php } ?>
          </select>

          <input type="submit" value="Filter" />
          <?php if ($_GET['m1'] && $_GET['m2'] && $_GET['t1'] && $_GET['t2']): ?>
            <div style="text-align:right;margin-top:5px;">
              <input type="button" value="Export ke Excel 2003 (.xls)"
                onclick="window.open ('laporan_ast_xls.php?s=<?= $_GET['s'] ?>&m1=<?= $_GET['m1'] ?>&t1=<?= $_GET['t1'] ?>&m2=<?= $_GET['m2'] ?>&t2=<?= $_GET['t2'] ?>&ui=<?= $user->user_id ?>&r=<?= $_GET['r'] ?>','daisy<?= rand() ?>','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
              <input type="button" value="Export ke Excel 2007 (.xlsx)"
                onclick="window.open ('laporan_ast_xlsx.php?s=<?= $_GET['s'] ?>&m1=<?= $_GET['m1'] ?>&t1=<?= $_GET['t1'] ?>&m2=<?= $_GET['m2'] ?>&t2=<?= $_GET['t2'] ?>&ui=<?= $user->user_id ?>&r=<?= $_GET['r'] ?>','daisy<?= rand() ?>','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
              <input type="button" value="Print"
                onclick="window.open ('printTabelAST.php?s=<?= $_GET['s'] ?>&m1=<?= $_GET['m1'] ?>&t1=<?= $_GET['t1'] ?>&m2=<?= $_GET['m2'] ?>&t2=<?= $_GET['t2'] ?>&r=<?= $_GET['r'] ?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
            </div>
          <?php endif; ?>
        </form>
      </div>
      <?php if ($_GET['m1'] && $_GET['m2'] && $_GET['t1'] && $_GET['t2']): ?>
        <div>Laporan AST
          <?= $status ?>
        </div>
        <?php
        if (is_array($resast) && !empty($resast)) { ?>
          <?php $i = 1;
          foreach ($resast as $ast) { ?>
            <?php if ($ast->thn <> $tempThn) { ?>
              <?php $endTable = ($ast->thn <> $tempThn && $tempThn <> '') ? 1 : 0 ?>
              <?php if ($endTable == 1) { ?>
              </td>
            </tr>
          </table>
        <?php
              } ?>

        <strong>Tahun Kejadian <?= $ast->thn ?></strong><br />

        <table width="98%" class="tabel" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <th rowspan="2">No Laporan</th>
            <th rowspan="2">Status Claim</th>
            <th rowspan="2">Cause</th>
            <th colspan="6">Dokumen-dokumen</th>
            <th rowspan="2">Progress</th>
            <th rowspan="2">Status Klaim</th>
            <th rowspan="2">Aksi</th>
          </tr>
          <tr>
            <th>Lap. Awal</th>
            <th>BA Kerugian</th>
            <th>BA Kronologis</th>
            <th>Foto</th>
            <th>Rincian Kerusakan</th>
            <th>Dokumen khusus</th>
          </tr>
          <?php
          $sebab = $ast->sebab;
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

          $status_c = $ast->status_progress;
          function status($status_c)
          {
            if ($status_c == 0) {
              echo "<B> - </B>";
            } elseif ($status_c == 1) {
              echo "<B> OUTSTANDING </B>";
            } elseif ($status_c == 2) {
              echo "<B> UNDER DEDUCTIBLE </B>";
            } else {
              echo "<B> SETTLEMENT </B>";
            }
          }
          ?>

          <tr class="<?= ($i % 2 == 0 ? 'even' : 'odd') ?>">
            <td nowrap="nowrap"><?= $ast->no_laporan ?></td>
            <td><?= $ast->status_claim == 'total' ? 'Totally' : 'Partial' ?> lost</td>
            <td nowrap>
              <?php $sebab = $ast->sebab;
              sebab($sebab); ?>
            </td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_lap == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_hil == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_kro == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_fo == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_rinci == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap">
              <?php
              if ($ast->sebab == 'fre') {
                echo $ast->doc_pmk == '0000-00-00' ? 'Tidak Ada' : 'OK';
              } else if ($sebab == 'nds' || $sebab == 'lit' || $sebab == "etv") {
                echo $ast->doc_bmkg == '0000-00-00' ? 'Tidak Ada' : 'OK';
              } else if ($sebab == 'thf' || $sebab == 'rio' || $sebab == 'trp') {
                echo $ast->doc_pol == '0000-00-00' ? 'Tidak Ada' : 'OK';
              } else {
                echo "Tidak ada";
              } ?>
            </td>
            <td style="text-align:center"><?= $ast->status ?></td>
            <td style="text-align:center">
              <?php
              status($status_c);
              ?>
            </td>
            <td style="text-align:center">
              <?php
              switch ($ast->status) {
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
                  $aksi = $user->role == 'stfp' || $user->role == 'spvp' ? '<strong>SUBMITTED</strong>' : 'View';
                  break;
                case 'SUBMITTED':
                  $aksi = $user->role == 'spvp' ? '<strong>Estimasi</strong>' : 'revisi';

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
              <a href="view_ast.php?i=<?= $ast->no_laporan ?>">
                <?= $aksi ?>
              </a>
            </td>
          </tr>
        <?php
            } else { ?>
          <tr class="<?= ($i % 2 == 0 ? 'even' : 'odd') ?>">
            <td nowrap="nowrap"><?= $ast->no_laporan ?></td>
            <td><?= $ast->status_claim == 'total' ? 'Totally' : 'Partial' ?> lost</td>
            <td>
              <?php
              $sebab = $ast->sebab;
              sebab($sebab); ?>
            </td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_lap == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_hil == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_kro == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_fo == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap"><?= $ast->doc_rinci == '0000-00-00' ? 'Tidak Ada' : 'OK' ?></td>
            <td align="center" nowrap="nowrap">
              <?php
              if ($ast->sebab == 'fre') {
                echo $ast->doc_pmk == '0000-00-00' ? 'Tidak Ada' : 'OK';
              } else if ($sebab == 'nds' || $sebab == 'lit' || $sebab == "etv") {
                echo $ast->doc_bmkg == '0000-00-00' ? 'Tidak Ada' : 'OK';
              } else if ($sebab == 'thf' || $sebab == 'rio' || $sebab == 'trp') {
                echo $ast->doc_pol == '0000-00-00' ? 'Tidak Ada' : 'OK';
              } else {
                echo "Tidak ada";
              } ?>
            </td>
            <td style="text-align:center"><?= $ast->status ?></td>
            <td style="text-align:center">
              <?php
              $status_c = $ast->status_progress;
              status($status_c);
              ?>
            </td>
            <td style="text-align:center">
              <?php
              switch ($ast->status) {
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
                  $aksi = $user->role == 'stfp' || $user->role == 'spvp' ? '<strong>SUBMITTED</strong>' : 'View';
                  break;
                case 'SUBMITTED':
                  $aksi = $user->role == 'spvp' ? '<strong>Estimasi</strong>' : 'View';
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
              <a href="view_ast.php?i=<?= $ast->no_laporan ?>">
                <?= $aksi ?>
              </a>
            </td>
          </tr>
        <?php } ?>
        <?php $tempThn = $ast->thn;
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