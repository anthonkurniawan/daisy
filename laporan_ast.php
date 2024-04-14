<?php
require 'init.php';
require 'priviledges.php';
include 'header.php';


?>
<table width="1250" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
  <?php include "webheader.php"; ?>
  <tr valign="top">
    <td style="width:150px">
      <ul style="list-style:none;padding-left:5px">
        <?php include "menu.php" ?>
      </ul>
    </td>

    <td>
      <?php
      if ($_GET['k'] <> '') {
        $and .= " AND status_progress='" . $_GET['k'] . "'";
      }
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
          case 'SETTLED':
            $and .= " AND tgl_settled BETWEEN '" . $_GET['t1'] . "-" . str_pad($_GET['m1'], 2, '0', STR_PAD_LEFT) . "-01 00:00:00' AND '" . $_GET['t2'] . "-" . str_pad($_GET['m2'], 2, '0', STR_PAD_LEFT) . "-" . $lastD2 . " 23:59:59'";
            break;
        }
        if ($_GET['s'] <> '')
          $status = 'Status ' . $_GET['s'];
        elseif ($_GET['m1'] <> '')
          $status .= ' yang dibuat ';
        if ($_GET['m1'] <> '' && $_GET['m1'] == $_GET['m2'] && $_GET['t1'] == $_GET['t2'])
          $status .= ' pada bulan ' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'];
        elseif ($_GET['m1'] <> '')
          $status .= ' dari bulan ' . $months[$_GET['m1'] - 1] . ' ' . $_GET['t1'] . ' s/d ' . $months[$_GET['m2'] - 1] . ' ' . $_GET['t2'];

        $SQL1 = "SELECT EXTRACT(YEAR FROM tgl_kejadian) thn,ast2.* FROM `ast2` 
					WHERE 1 AND kode_region='" . $user->regional . "' " . $and . " ORDER BY thn DESC ,`updated_at` DESC";
        $resast = $db->get_results($SQL1);
        $status .= ' adalah sebanyak: <strong>' . $db->num_rows . '</strong> laporan.';
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

        <?
        if (is_array($resast) && !empty($resast)) { ?>
          <?php $i = 1;
          foreach ($resast as $ast) { ?>
            <?php if ($ast->thn <> $tempThn) { ?>
              <?php $endTable = ($ast->thn <> $tempThn && $tempThn <> '') ? 1 : 0 ?>
              <?php if ($endTable == 1) { ?>
          </table>
        <?php } ?>

        <strong>Tahun Kejadian <?= $ast->thn ?></strong><br />
        <?php echo $status_c; ?>

        <table width="98%" class="tabel" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <th rowspan="2">No Laporan</th>
            <th rowspan="2">Status Claim</th>
            <th rowspan="2">Cause</th>
            <th colspan="6">Dokumen-dokumen</th>
            <th rowspan="2">Progress</th>
            <th rowspan="2">Status Klaim</th>
            <th rowspan="2">Revisi</th>
          </tr>
          <tr>
            <th>Lap. Awal</th>
            <th>BA Kerugian</th>
            <th>BA Kronologis</th>
            <th>Foto</th>
            <th>Rincian Kerusakan</th>
            <th>Dokumen Khusus</th>
          </tr>
          <tr class="<?= ($i % 2 == 0 ? 'even' : 'odd') ?>" valign="top">
            <td nowrap="nowrap"><?= $ast->no_laporan ?></td>
            <td nowrap><?= $ast->status_claim == 'total' ? 'Totally' : 'Partial' ?> lost</td>
            <?php
            function sebab($sebab)
            {
              if ($sebab == 'nds') {
                echo 'Natural Dissaster (Bencana Alam)';
              } elseif ($sebab == 'thf') {
                echo 'Theft (Pencurian)';
              } elseif ($sebab == 'lit') {
                echo 'Lightning (Petir)';
              } elseif ($sebab == "etv") {
                echo "Earthquake, Tsunami, Volcano Erruption";
              } elseif ($sebab == 'fre') {
                echo 'Fire (Terbakar/ Kebakaran)';
              } elseif ($sebab == 'trp') {
                echo 'Third Party (Tuntutan Pihak ketiga)';
              } elseif ($sebab == 'rio') {
                echo 'Riots/ Strikes, Malicious Damage (Kerusuhan)';
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

            <td align="center" valign="middle" style="text-align:center"><?= $ast->status ?></td>
            <td align="center" valign="middle" style="text-align:center">
              <?php status($status_c); ?>
            </td>
            <td style="text-align:center">
              <?php
              $page = 'revisiast';
              switch ($ast->status) {
                case 'UNAPPROVED':
                  $aksi = $user->role == 'mgrr' ? '<strong>Approval</strong>' : 'Revisi';
                  break;
                case 'REJECTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Revisi</strong>' : 'View';
                  break;
                case 'APPROVED':
                  $aksi = $user->role == 'stfp' ? '<strong>SUBMITTED</strong>' : 'View';
                  break;
                case 'SUBMITTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Dokumen</strong>' : 'View';
                  break;
                case 'SETTLED':
                case 'CASECLOSED':
                default:
                  $aksi = 'View';
                  break;
              } ?>
              <a href="<?= $page ?>.php?revisi=<?= $ast->no_laporan ?>">
                <?= $aksi ?>
              </a>
            </td>
          </tr>
        <?php } else { ?>
          <tr class="<?= ($i % 2 == 0 ? 'even' : 'odd') ?>" valign="top">
            <td nowrap="nowrap"><?= $ast->no_laporan ?></td>
            <td nowrap><?= $ast->status_claim == 'total' ? 'Totally' : 'Partial' ?> lost</td>
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
            <td align="center" valign="middle" style="text-align:center"><?= $ast->status ?></td>
            <td align="center" valign="middle" style="text-align:center">
              <?php $status_c = $ast->status_progress;
              status($status_c); ?>
            </td>
            <td style="text-align:center">
              <?php
              $page = 'revisiast';
              switch ($ast->status) {
                case 'UNAPPROVED':
                  $aksi = $user->role == 'mgrr' ? '<strong>Approval</strong>' : 'Revisi';
                  break;
                case 'REJECTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Revisi</strong>' : 'View';
                  break;
                case 'APPROVED':
                  $aksi = $user->role == 'stfp' ? '<strong>Commit Submit</strong>' : 'View';
                  break;
                case 'SUBMITTED':
                  $aksi = $user->role == 'spvr' ? '<strong>Commit Dokument</strong>' : 'View';
                  break;
                case 'SETTLED':
                case 'CASECLOSED':
                default:
                  $aksi = 'View';
                  break;
              } ?>
              <a href="<?= $page ?>.php?revisi=<?= $ast->no_laporan ?>">
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
        ?>
  <?php endif; ?>
  </td>
  </tr>
</table>
<?php include "footer.php" ?>