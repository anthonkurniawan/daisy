<?php
require 'init.php';
require 'priviledges.php';
include "header.php";
?>
<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
  <?php include "webheader.php"; ?>

  <tr valign="top">

    <td style="width:250px" rowspan="3">
      <ul style="list-style:none;padding-left:5px">
        <?php include "menu.php" ?>
      </ul>
    </td>

    <td style="padding-left:50px;">
      <h2>SUMMARY PROGRESS REPORT CGL</h2>
      Per Tanggal: <?= date("d-m-Y") ?> <br />
      Regional : (
      <?php echo $user['regional'] ?>)
      <?php $db->getRow("SELECT `region` FROM region WHERE kode_region='" . $user['regional'] . "'")->region; ?>
      <br />

      <?php $res = $db->getArray("SELECT COUNT(1) AS jml,`status`,EXTRACT(YEAR FROM tgl_kejadian) thn FROM cgl WHERE kode_region='" . $user['regional'] . "' GROUP BY thn, `status` ORDER BY thn DESC");

      if ($res):
        foreach ($res as $aRes) {
          $aStatus[$aRes->thn][$aRes->status] = $aRes->jml;
          $aStatus[$aRes->thn]['total'] += $aRes->jml;
        }
      endif;
      ?>

      <?php foreach ($aStatus as $thn => $rec) { ?>
        <strong>
          <?= $thn ?>
        </strong>
        <table border="1" style="tabel">
          <tr>
            <td><strong>Status Laporan</strong></td>
            <td colspan="4"><strong>Jumlah</strong></td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="4">Total laporan: </td>
            <td style="text-align:right">
              <?= ($rec['total']) ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="3" style="padding-left:30px;">1. Laporan dibuat (belum approved): </td>
            <td style="text-align:right">
              <?= $rec['UNAPPROVED'] ? $rec['UNAPPROVED'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="3" style="padding-left:30px;">2. Laporan approved (belum disubmit):</td>
            <td style="text-align:right">
              <?= $rec['APPROVED'] ? $rec['APPROVED'] : 0 ?>
            </td>
          </tr>
          <tr>
            <td colspan="3" style="padding-left:30px;">3. Submitted klaim:</td>
            <td style="text-align:right">
              <?= $rec['SUBMITTED'] ? $rec['SUBMITTED'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="2" style="padding-left:60px;">3.1 Laporan Outstanding:</td>
            <td nowrap="nowrap" style="text-align:right">
              <?= ($rec['SUBMITTED'] + $rec['SURVEY'] + $rec['PAYMENT'] + $rec['INVOICE']) > 0 ? ($rec['SUBMITTED'] + $rec['SURVEY'] + $rec['PAYMENT'] + $rec['INVOICE']) : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.1.1 Belum Survey: </td>
            <td style="text-align:right">
              <?= $rec['SUBMITTED'] ? $rec['SUBMITTED'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.1.2 Survey: </td>
            <td style="text-align:right">
              <?= $rec['SURVEY'] ? $rec['SURVEY'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.1.3 Payment to community: </td>
            <td style="text-align:right">
              <?= $rec['PAYMENT'] ? $rec['PAYMENT'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.1.4 Invoice to Insurance company: </td>
            <td style="text-align:right">
              <?= $rec['INVOICE'] ? $rec['INVOICE'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="2" style="padding-left:60px;">3.2 Laporan yang telah dibayar (Settled): </td>
            <td style="text-align:right">
              <?= $rec['SETTLED'] ? $rec['SETTLED'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="2" nowrap="nowrap" style="padding-left:60px;">3.3 Laporan yang dibatalkan (Closed): </td>
            <td style="text-align:right">
              <?= $rec['CLOSED'] ? $rec['CLOSED'] : 0 ?>
            </td>
          </tr>
        </table><br />
      <?php } ?>
      &nbsp

      <?php
      $SQL = "SELECT DATEDIFF(NOW(),submit_at) outstanding FROM cgl  WHERE kode_region='" . $user['regional'] . "' AND `status` IN ('SUBMITTED','SURVEY','PAYMENT','INVOICE')";
      $res = $db->getArray($SQL);
      $jmlo = 0;
      if ($res) {
        foreach ($res as $r) {
          if ($r->outstanding <= 30)
            $o[1]++;
          if ($r->outstanding >= 31 && $r->outstanding <= 60)
            $o[2]++;
          if ($r->outstanding >= 61 && $r->outstanding <= 90)
            $o[3]++;
          if ($r->outstanding >= 91 && $r->outstanding <= 120)
            $o[4]++;
          if ($r->outstanding > 120)
            $o[5]++;
          $jmlo++;
        }
      }

      $SQL = "SELECT DATEDIFF(settlement_date,submit_at) settled FROM cgl WHERE kode_region='" . $user['regional'] . "' AND `status`='SETTLED'";
      $res = $db->getArray($SQL);
      $jmls = 0;
      if ($res) {
        foreach ($res as $r) {
          if ($r->settled <= 30)
            $s[1]++;
          if ($r->settled >= 31 && $r->settled <= 60)
            $s[2]++;
          if ($r->settled >= 61 && $r->settled <= 90)
            $s[3]++;
          if ($r->settled >= 91 && $r->settled <= 120)
            $s[4]++;
          if ($r->settled > 120)
            $s[5]++;
          $jmls++;
        }
      }

      $SQL = "SELECT DATEDIFF(caseclosed_at,submit_at) closed FROM cgl WHERE kode_region='" . $user['regional'] . "' AND `status`='CLOSED'";
      $res = $db->getArray($SQL);
      $jmlc = 0;
      if ($res) {
        foreach ($res as $r) {
          if ($r->closed <= 30)
            $c[1]++;
          if ($r->closed >= 31 && $r->closed <= 60)
            $c[2]++;
          if ($r->closed >= 61 && $r->closed <= 90)
            $c[3]++;
          if ($r->closed >= 91 && $r->closed <= 120)
            $c[4]++;
          if ($r->closed > 120)
            $c[5]++;
          $jmlc++;
        }
      }
      ?>
      <table border="5" style="tabel">
        <tr class="even" style="background:#aaaaaa">
          <th>Aging class</th>
          <th>Outstanding</th>
          <th>Settled</th>
          <th>Closed</th>
          <th>Total</th>
        </tr>
        <tr class="odd">
          <th>0-30</th>
          <td style="text-align:center">
            <?= $o[1] ?>
          </td>
          <td style="text-align:center">
            <?= $s[1] ?>
          </td>
          <td style="text-align:center">
            <?= $c[1] ?>
          </td>
          <td style="text-align:center">
            <?= ($o[1] + $s[1] + $c[1]) ?>
          </td>
        </tr>
        <tr class="even">
          <th>31-60 </th>
          <td style="text-align:center">
            <?= $o[2] ?>
          </td>
          <td style="text-align:center">
            <?= $s[2] ?>
          </td>
          <td style="text-align:center">
            <?= $c[2] ?>
          </td>
          <td style="text-align:center">
            <?= ($o[2] + $s[2] + $c[2]) ?>
          </td>
        </tr>
        <tr class="odd">
          <th>61-90</th>
          <td style="text-align:center">
            <?= $o[3] ?>
          </td>
          <td style="text-align:center">
            <?= $s[3] ?>
          </td>
          <td style="text-align:center">
            <?= $c[3] ?>
          </td>
          <td style="text-align:center">
            <?= ($o[3] + $s[3] + $c[3]) ?>
          </td>
        </tr>
        <tr class="even">
          <th>91-120 </th>
          <td style="text-align:center">
            <?= $o[4] ?>
          </td>
          <td style="text-align:center">
            <?= $s[4] ?>
          </td>
          <td style="text-align:center">
            <?= $c[4] ?>
          </td>
          <td style="text-align:center">
            <?= ($o[4] + $s[4] + $c[4]) ?>
          </td>
        </tr>
        <tr class="odd">
          <th>121- </th>
          <td style="text-align:center">
            <?= $o[5] ?>
          </td>
          <td style="text-align:center">
            <?= $s[5] ?>
          </td>
          <td style="text-align:center">
            <?= $c[5] ?>
          </td>
          <td style="text-align:center">
            <?= ($o[5] + $s[5] + $c[5]) ?>
          </td>
        </tr>
        <tr class="even" style="background:#ccffcc">
          <th>Total:</th>
          <td bgcolor="green" style="text-align:center">
            <?= $jmlo ?>
          </td>
          <td bgcolor="green" style="text-align:center">
            <?= $jmls ?>
          </td>
          <td bgcolor="green" style="text-align:center">
            <?= $jmlc ?>
          </td>
          <td bgcolor="green" style="text-align:center">
            <?= ($jmlo + $jmls + $jmlc) ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <p>
      <p>
        <hr color="#000000" />
        <hr color="#000000" />
    </td>
  </tr>


  <!-------------------------------------------------- SUMMARY PROGRESS REPORT AST -------------------------------------->
  <tr valign="top">

    <td style="padding-left:50px;">
      <h2>SUMMARY PROGRESS REPORT AST</h2>
      Per Tanggal: <?= date("d-m-Y") ?> <br />
      Regional xx: (
      <?= $user['regional'] ?>)
      <?php $res = $db->getRow("SELECT `region` FROM region WHERE kode_region='" . $user['regional'] . "'");
      echo $res['region']; ?>
      <br />
      <?php $res2 = $db->getArray("SELECT COUNT(1) AS jml,`status`,EXTRACT(YEAR FROM tgl_kejadian) thn FROM ast WHERE kode_region='" . $user['regional'] . "' GROUP BY thn, `status` ORDER BY thn DESC");
      var_dump($res2); //die();
      $res_est = $db->getArray("SELECT COUNT( 1 ) AS jml FROM ast WHERE estimasi <> 'NULL' AND  `kode_region` =  '" . $user['regional'] . "'");
      foreach ($res_est as $es) {
        $esti = $es->jml;
      }

      if ($res2):
        foreach ($res2 as $aRes2) {
          //$aStatus[$aRes->status] = $aRes->jml;
          $total += $aRes2->jml;
          $aStatus2[$aRes2->thn][$aRes2->status] = $aRes2->jml;
          $aStatus2[$aRes2->thn]['total'] += $aRes2->jml;
        }
      endif;
      ?>

      <?php foreach ($aStatus2 as $thn2 => $rec2) { ?>
        <strong>
          <?= $thn2 ?>
        </strong>
        <table border="1" style="tabel">
          <tr>
            <td><strong>Status Laporan</strong></td>
            <td colspan="4"><strong>Jumlah</strong></td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="4">Total laporan: </td>
            <td style="text-align:right">
              <?= $total ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="3" style="padding-left:30px;">1. Laporan dibuat (belum approved): </td>
            <td style="text-align:right">
              <?= $rec2['UNAPPROVED'] ? $rec2['UNAPPROVED'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="3" style="padding-left:30px;">2. Laporan approved (belum disubmit):</td>
            <td style="text-align:right">
              <?= $rec2['APPROVED'] ? $rec2['APPROVED'] : 0 ?>
            </td>
          </tr>
          <tr>
            <td colspan="3" style="padding-left:30px;">3. Submitted klaim:</td>
            <td style="text-align:right">
              <?= $rec2['SUBMITTED'] ? $rec2['SUBMITTED'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="2" style="padding-left:60px;">3.1 Laporan Outstanding:</td>
            <td nowrap="nowrap" style="text-align:right">
              <?= ($rec2['UNAPPROVED'] + $rec2['APPROVED'] + $rec2['SUBMITTED']) > 0 ? ($rec2['UNAPPROVED'] + $rec2['APPROVED'] + $rec2['SUBMITTED']) : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.1 Estimasi & Dokumen : </td>
            <td style="text-align:right"><?= $es->jml ?></td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.2 Proposed Adjustment 1 :</td>
            <td style="text-align:right">
              <?= $rec['SURVEY'] ? $rec['SURVEY'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.3 Konfirmasi Adjustment 1 : </td>
            <td style="text-align:right">
              <?= $rec['PAYMENT'] ? $rec['PAYMENT'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td style="padding-left:90px;">3.4 Proposed Adjustment 2 : </td>
            <td style="text-align:right">
              <?= $rec['INVOICE'] ? $rec['INVOICE'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
          <tr>
            <td style="padding-left:90px;">3.5 Konfirmasi Adjustment 2 : </td>
            <td style="text-align:right">
              <?= $rec['INVOICE'] ? $rec['INVOICE'] : 0 ?>
            </td>
          </tr>
          <tr></tr>
          <tr>
            <td colspan="3" style="padding-left:30px;">4. Settlement :</td>
            <td style="text-align:right">
              <?= $rec2['SETTLED'] ? $rec2['SETTLED'] : 0 ?>
            </td>
          </tr>
        </table><br />
      <?php } ?>
      &nbsp

      <?php
      $SQL3 = "SELECT DATEDIFF(NOW(),submit_at) outstanding FROM ast  WHERE kode_region='" . $user['regional'] . "' AND `status` IN ('SUBMITTED','SURVEY','PAYMENT','INVOICE')";
      $res3 = $db->getArray($SQL3);
      $jmlo2 = 0;
      if ($res3) {
        foreach ($res3 as $r3) {
          if ($r3->outstanding <= 30)
            $o2[1]++;
          if ($r3->outstanding >= 31 && $r3->outstanding <= 60)
            $o3[2]++;
          if ($r3->outstanding >= 61 && $r3->outstanding <= 90)
            $o3[3]++;
          if ($r3->outstanding >= 91 && $r3->outstanding <= 120)
            $o3[4]++;
          if ($r3->outstanding > 120)
            $o2[5]++;
          $jmlo2++;
        }
      }

      $SQL4 = "SELECT DATEDIFF(settlement_date,submit_at) settled FROM ast WHERE kode_region='" . $user['regional'] . "' AND `status`='SETTLED'";
      $res4 = $db->getArray($SQL4);
      $jmls2 = 0;
      if ($res4) {
        foreach ($res4 as $r4) {
          if ($r4->settled <= 30)
            $s2[1]++;
          if ($r4->settled >= 31 && $r4->settled <= 60)
            $s4[2]++;
          if ($r4->settled >= 61 && $r4->settled <= 90)
            $s4[3]++;
          if ($r4->settled >= 91 && $r4->settled <= 120)
            $s4[4]++;
          if ($r4->settled > 120)
            $s2[5]++;
          $jmls2++;
        }
      }

      $SQL5 = "SELECT DATEDIFF(caseclosed_at,submit_at) closed FROM ast WHERE kode_region='" . $user['regional'] . "' AND `status`='CLOSED'";
      $res5 = $db->getArray($SQL5);
      $jmlc2 = 0;
      if ($res5) {
        foreach ($res5 as $r5) {
          if ($r5->closed <= 30)
            $c2[1]++;
          if ($r5->closed >= 31 && $r5->closed <= 60)
            $c5[2]++;
          if ($r5->closed >= 61 && $r5->closed <= 90)
            $c5[3]++;
          if ($r5->closed >= 91 && $r5->closed <= 120)
            $c5[4]++;
          if ($r5->closed > 120)
            $c2[5]++;
          $jmlc2++;
        }
      }
      ?>
      <table border="5" style="tabel">
        <tr class="even" style="background:#aaaaaa">
          <th>Aging class</th>
          <th>Outstanding</th>
          <th>Settled</th>
          <th>Closed</th>
          <th>Total</th>
        </tr>
        <tr class="odd">
          <th>0-30</th>
          <td style="text-align:center">
            <?= $o2[1] ?>
          </td>
          <td style="text-align:center">
            <?= $s2[1] ?>
          </td>
          <td style="text-align:center">
            <?= $c2[1] ?>
          </td>
          <td style="text-align:center">
            <?= ($o[1] + $s2[1] + $c2[1]) ?>
          </td>
        </tr>
        <tr class="even">
          <th>31-60 </th>
          <td style="text-align:center">
            <?= $o2[2] ?>
          </td>
          <td style="text-align:center">
            <?= $s2[2] ?>
          </td>
          <td style="text-align:center">
            <?= $c2[2] ?>
          </td>
          <td style="text-align:center">
            <?= ($o2[2] + $s2[2] + $c2[2]) ?>
          </td>
        </tr>
        <tr class="odd">
          <th>61-90</th>
          <td style="text-align:center">
            <?= $o2[3] ?>
          </td>
          <td style="text-align:center">
            <?= $s2[3] ?>
          </td>
          <td style="text-align:center">
            <?= $c2[3] ?>
          </td>
          <td style="text-align:center">
            <?= ($o2[3] + $s2[3] + $c2[3]) ?>
          </td>
        </tr>
        <tr class="even">
          <th>91-120 </th>
          <td style="text-align:center">
            <?= $o2[4] ?>
          </td>
          <td style="text-align:center">
            <?= $s2[4] ?>
          </td>
          <td style="text-align:center">
            <?= $c2[4] ?>
          </td>
          <td style="text-align:center">
            <?= ($o2[4] + $s2[4] + $c2[4]) ?>
          </td>
        </tr>
        <tr class="odd">
          <th>121- </th>
          <td style="text-align:center">
            <?= $o2[5] ?>
          </td>
          <td style="text-align:center">
            <?= $s2[5] ?>
          </td>
          <td style="text-align:center">
            <?= $c2[5] ?>
          </td>
          <td style="text-align:center">
            <?= ($o2[5] + $s2[5] + $c2[5]) ?>
          </td>
        </tr>
        <tr class="even" style="background:#ccffcc">
          <th>Total:</th>
          <td bgcolor="green" style="text-align:center">
            <?= $jmlo2 ?>
          </td>
          <td bgcolor="green" style="text-align:center">
            <?= $jmls2 ?>
          </td>
          <td bgcolor="green" style="text-align:center">
            <?= $jmlc2 ?>
          </td>
          <td bgcolor="green" style="text-align:center">
            <?= ($jmlo2 + $jmls2 + $jmlc2) ?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php include "footer.php" ?>