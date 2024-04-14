<?php
require 'init.php';
require 'priviledges.php';
include "headerast.php";

$err = array();
if ($_POST) {
  if (empty($err)):
    $res = $db->get_row("SELECT no_laporan FROM ast2 WHERE kode_region='" . $user->regional . "'  ORDER BY created_at DESC");

    if ($res->no_laporan <> '') {
      $aNoLap = explode('/', $res->no_laporan);
      (int) $no_laporan = $aNoLap[0];
      $no_laporan++;
    } else
      $no_laporan = 1;

    /**
     Kode Laporan: laporan: [Nomor]/[Regional]/[Kode Klaim]/[Tanggal]/[Bulan]/[Tahun] 
     [Nomor] : 4 digit 
     [Regional] : 4 digit (Rxxx) 
     [Kode Klaim] : 3 digit (CGL / AST) 
     [Tanggal] : 2 digit (01 � 31) 
     [Bulan] : 3 digit (Jan � Dec) 
     [Tahun] : 2 digit (xx)
     */

    $kode_laporan = str_pad($no_laporan, 2, "0", STR_PAD_LEFT) . '/' . $user->regional . '/AST/' . date("d") . '/' . date("m") . '/' . date("y");
    $prefixFile = str_replace('/', '', $kode_laporan);
    if ($_FILES['doc_tun']['name'] <> '') {
      $d_tun = $prefixFile . '_1_' . basename($_FILES['doc_tun']['name']);
      $d_tun_tgl = "NOW()";
    } else {
      $d_tun_tgl = "1";
    }
    if ($_FILES['doc_hil']['name'] <> '') {
      $d_hil = $prefixFile . '_2_' . basename($_FILES['doc_hil']['name']);
      $d_hil_tgl = "NOW()";
    } else {
      $d_hil_tgl = "1";
    }
    if ($_FILES['doc_kro']['name'] <> '') {
      $d_kro = $prefixFile . '_3_' . basename($_FILES['doc_kro']['name']);
      $d_kro_tgl = "NOW()";
    } else {
      $d_kro_tgl = "1";
    }
    if ($_FILES['doc_po']['name'] <> '') {
      $d_po = $prefixFile . '_4_' . basename($_FILES['doc_po']['name']);
      $d_po_tgl = "NOW()";
    } else {
      $d_po_tgl = "1";
    }
    if ($_FILES['doc_fo']['name'] <> '') {
      $d_fo = $prefixFile . '_5_' . basename($_FILES['doc_fo']['name']);
      $d_fo_tgl = "NOW()";
    } else {
      $d_fo_tgl = "1";
    }
    if ($_FILES['doc_rinci']['name'] <> '') {
      $d_rinci = $prefixFile . '_6_' . basename($_FILES['doc_rinci']['name']);
      $d_rinci_tgl = "NOW()";
    } else {
      $d_rinci_tgl = "1";
    }
    if ($_FILES['doc_lap']['name'] <> '') {
      $d_lap = $prefixFile . '_7_' . basename($_FILES['doc_lap']['name']);
      $d_lap_tgl = "NOW()";
    } else {
      $d_lap_tgl = "1";
    }
    if ($_FILES['doc_pol']['name'] <> '') {
      $d_pol = $prefixFile . '_8_' . basename($_FILES['doc_pol']['name']);
      $d_pol_tgl = "NOW()";
    } else {
      $d_pol_tgl = "1";
    }
    if ($_FILES['doc_pmk']['name'] <> '') {
      $d_pmk = $prefixFile . '_9_' . basename($_FILES['doc_pmk']['name']);
      $d_pmk_tgl = "NOW()";
    } else {
      $d_pmk_tgl = "1";
    }
    if ($_FILES['doc_bmkg']['name'] <> '') {
      $d_bmkg = $prefixFile . '_10_' . basename($_FILES['doc_bmkg']['name']);
      $d_bmkg_tgl = "NOW()";
    } else {
      $d_bmkg_tgl = "1";
    }
    $uploaddir = 'docs/ast/';
    $uploadfile1 = $uploaddir . $d_tun;
    $uploadfile2 = $uploaddir . $d_hil;
    $uploadfile3 = $uploaddir . $d_kro;
    $uploadfile4 = $uploaddir . $d_po;
    $uploadfile5 = $uploaddir . $d_fo;
    $uploadfile6 = $uploaddir . $d_rinci;
    $uploadfile7 = $uploaddir . $d_lap;
    $uploadfile8 = $uploaddir . $d_pol;
    $uploadfile9 = $uploaddir . $d_pmk;
    $uploadfile10 = $uploaddir . $d_bmkg;

    if ($_FILES['doc_tun']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_tun']['tmp_name'], $uploadfile1))
        echo 'File Upload Error: ' . $_FILES['doc_tun']['error'];
    }
    if ($_FILES['doc_hil']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_hil']['tmp_name'], $uploadfile2))
        echo 'File Upload Error: ' . $_FILES['doc_hil']['error'];
    }
    if ($_FILES['doc_kro']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_kro']['tmp_name'], $uploadfile3))
        echo 'File Upload Error: ' . $_FILES['doc_kro']['error'];
    }
    if ($_FILES['doc_po']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_po']['tmp_name'], $uploadfile4))
        echo 'File Upload Error: ' . $_FILES['doc_po']['error'];
    }
    if ($_FILES['doc_fo']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_fo']['tmp_name'], $uploadfile5))
        echo 'File Upload Error: ' . $_FILES['doc_fo']['error'];
    }
    if ($_FILES['doc_rinci']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_rinci']['tmp_name'], $uploadfile6))
        echo 'File Upload Error: ' . $_FILES['doc_rinci']['error'];
    }
    if ($_FILES['doc_lap']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_lap']['tmp_name'], $uploadfile7))
        echo 'File Upload Error: ' . $_FILES['doc_lap']['error'];
    }
    if ($_FILES['doc_pol']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_pol']['tmp_name'], $uploadfile8))
        echo 'File Upload Error: ' . $_FILES['doc_pol']['error'];
    }
    if ($_FILES['doc_pmk']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_pmk']['tmp_name'], $uploadfile9))
        echo 'File Upload Error: ' . $_FILES['doc_pmk']['error'];
    }
    if ($_FILES['doc_bmkg']['name'] <> '') {
      if (!move_uploaded_file($_FILES['doc_bmkg']['tmp_name'], $uploadfile10))
        echo 'File Upload Error: ' . $_FILES['doc_bmkg']['error'];
    }

    switch ($_POST['cod']) {
      case 'nds':
        $sebab = 'Natural Dissaster (Bencana Alam)';
        $deduct = '140000000';
        break;
      case 'rio':
        $sebab = 'Riots/ Strikes, Malicious Damage (Kerusuhan)';
        $deduct = '140000000';
        break;
      case 'thf':
        $sebab = 'Theft (Pencurian)';
        $deduct = '100000000';
        break;
      case 'lit':
        $sebab = 'Lightning (Petir)';
        $deduct = '140000000';
        break;
      case 'etv':
        $sebab = 'Earthquake, Tsunami, Volcano Erruption';
        $deduct = '?';
        break;
      case 'fre':
        $sebab = 'Fire (Terbakar/ Kebakaran)';
        $deduct = '50000000';
        break;
      case 'trp':
        $sebab = 'Third Party (Tuntutan Pihak ketiga)';
        $deduct = '20000000';
        break;
      case 'oth':
        $sebab = 'Other Losses (Lainnya..)';
        $deduct = '75000000';
        break;
    }

    $query = "SELECT st_site_id,st_name,kode_region,st_region FROM `site` WHERE `st_site_id` = '{$_POST['site']}'";
    $r = $db->get_row($query);

    $item = "SELECT item1 from category WHERE id_item='" . $_POST['item'] . "'";
    $it = $db->get_row($item);


    $SQL = "INSERT INTO ast2 (`no_laporan`,`inisial`,`user_id`,`kode_region`,`region`,`st_name`,`st_site_id`,`pic_region`,`telp`,`hp`,
				`tgl_kejadian`,`created_at`,`updated_at`,`status_claim`,`item1`,`status`,`sebab`,
				`doc_lap_file`,`doc_hil_file`,`doc_kro_file`,`doc_po_file`,`doc_fo_file`,`doc_pol_file`,`doc_pmk_file`,`doc_bmkg_file`,
			    `doc_lap`,`doc_hil`,`doc_kro`,`doc_po`,`doc_fo`,`doc_pol`,`doc_pmk`,`doc_bmkg`,`doc_rinci`,`doc_rinci_file`,`deduct`)
	             VALUES 
	            ('" . $kode_laporan . "','" . $user->inisial . "','" . $user->user_id . "','" . $r->kode_region . "','" . $r->st_region . "', '" . $r->st_name . "','" . $_POST['site'] . "','" . $_POST['pic_reg'] . "','" . $_POST['telp'] . "','" . $_POST['hp'] . "','" . $_POST['tgl_kejadian'] . "',NOW(),NOW(),																																										'" . $_POST['sclaim'] . "','" . $it->item1 . "','UNAPPROVED','" . $_POST['cod'] . "',
	'" . $d_lap . "','" . $d_hil . "','" . $d_kro . "','" . $d_po . "','" . $d_fo . "','" . $d_pol . "','" . $d_pmk . "','" . $d_bmkg . "',																																																																																																																	" . $d_lap_tgl . "," . $d_hil_tgl . "," . $d_kro_tgl . "," . $d_po_tgl . "," . $d_fo_tgl . "," . $d_pol_tgl . "," . $d_pmk_tgl . "," . $d_bmkg_tgl . "," . $d_rinci_tgl . ",
'" . $d_rinci . "','" . $deduct . "')";

    $db->query("UPDATE `site` SET  `st_longitude` ='" . $_POST['long'] . "',`st_latitude` ='" . $_POST['lat'] . "',`st_address`='" . $_POST['adr'] . "' WHERE  `st_site_id` ='" . $_POST['site'] . "'");

    $db->query($SQL);
    $assetgroups = array();
    $assetgroups = $_POST['asetgrup'];

    if (!empty($assetgroups)) {
      $i = 0;
      foreach ($assetgroups as $ag) {
        if ($ag <> '') {
          if (!empty($_POST['lin' . $ag])) {
            $tarik = $_POST['lin' . $ag] . "X" . $_POST['len' . $ag];
          } else {
            $tarik = '-';
          }

          if (empty($_POST['item' . $ag])) {
            $item1 = $_POST['other_cat' . $ag];
            if (empty($_POST['other_cat' . $ag])) {
              $item1 = 'Other Item1';
            }
          } else {
            $item1 = $_POST['item' . $ag];
            $resitem = $db->get_row("SELECT distinct(item1) FROM `category` WHERE id_item='" . $item1 . "'");
            $item1 = $resitem->item1;
          }

          if (empty($_POST['merk' . $ag])) {
            $merk1 = $_POST['other_merk' . $ag];
            if (empty($_POST['other_merk' . $ag])) {
              $merk1 = 'Other Merk1';
            }
          } else {
            $merk1 = $_POST['merk' . $ag];
          }

          if (empty($_POST['type' . $ag])) {
            $type1 = $_POST['other_type' . $ag];
            if (empty($_POST['other_type' . $ag])) {
              $type1 = 'Other Type1';
            }
          } else {
            $type1 = $_POST['type' . $ag];
          }

          if (empty($_POST['sat' . $ag])) {
            $sat1 = $_POST['other_sat' . $ag];
            if (empty($_POST['other_sat' . $ag])) {
              $sat1 = 'Other Satuan';
            }
          } else {
            $sat1 = $_POST['sat' . $ag];
          }

          $i++;

          $SQL2 = "INSERT INTO ast_detail2 (`no_laporan`,`id`,`item1`,`merk`,`type`,`quantity`,`satuan`,`tarikan`,`note`) VALUES 
	('" . $kode_laporan . "','" . $i . "','" . $item1 . "','" . $merk1 . "','" . $type1 . "','" . $_POST['quan' . $ag] . "','" . $sat1 . "',
	 '" . $tarik . "','" . $_POST['note' . $ag] . "')";
          $db->query($SQL2);
        }
      }
    }

    $assetgroups2 = array();
    $assetgroups2 = $_POST['asetgrup2'];

    if (!empty($assetgroups2)) {
      $x = $i;

      foreach ($assetgroups2 as $ag2) {
        echo "test" . $ag2;
        if ($ag2 == '0') {
          if (!empty($_POST['lin_a' . $ag2])) {
            $tarik2 = $_POST['lin_a' . $ag2] . "X" . $_POST['len_a' . $ag2];
          } else {
            $tarik2 = '-';
          }

          if (empty($_POST['item_a' . $ag2])) {
            $item2 = $_POST['other_cat_a' . $ag2];
            if (empty($_POST['other_cat_a' . $ag2])) {
              $item2 = 'Other Item2';
            }
          } else {
            $item2 = $_POST['item_a' . $ag2];
          }

          if (empty($_POST['merk_a' . $ag2])) {
            $merk2 = $_POST['other_merk_a' . $ag2];
            if (empty($_POST['other_merk_a' . $ag2])) {
              $merk2 = 'Other Merk2';
            }
          } else {
            $merk2 = $_POST['merk_a' . $ag2];
          }

          if (empty($_POST['type_a' . $ag2])) {
            $type2 = $_POST['other_type_a' . $ag2];
            if (empty($_POST['other_type_a' . $ag2])) {
              $type2 = 'Other type2';
            }
          } else {
            $type2 = $_POST['type_a' . $ag2];
          }

          if (empty($_POST['sat_a' . $ag])) {
            $sat2 = $_POST['other_sat_a' . $ag];
            if (empty($_POST['other_sat_a' . $ag])) {
              $sat2 = 'Other Satuan';
            }
          } else {
            $sat2 = $_POST['sat_a' . $ag];
          }

          $x++;

          echo "X :" . $i . "ag2 ->" . $ag2 . "</br>";
          $SQL3 = "INSERT INTO ast_detail2 (`no_laporan`,`id`,`item1`,`merk`,`type`,`quantity`,`satuan`,`tarikan`,`note`) VALUES 
	('" . $kode_laporan . "','" . $x . "','" . $item2 . "','" . $merk2 . "','" . $type2 . "','" . $_POST['quan_a' . $ag2] . "',
	 '" . $sat2 . "','" . $tarik2 . "','" . $_POST['note_a' . $ag2] . "')";
          $db->query($SQL3);
        }
      }
    }

    //-----------------------SEND EMAIL
    $query = "SELECT * FROM `site` WHERE `st_site_id` = '{$rast->st_site_id}'";
    $r = $db->get_row($query);

    $raw = file_get_contents('ast_unapproved.email.htm');
    $pattern = array(
      '%%NODOKUMEN%%',
      '%%TAHUN%%',
      '%%TGLKEJADIAN%%',
      ': %%TGLLAPOR%%',
      '%%NAMASITE%%',
      '%%ALAMATSITE%%',
      '%%REGIONAL%%',
      '%%LONGLAT%%',
      '%%SEBAB%%',
      '%%RINCIAN%%',
      '%%CPNAMA%%',
      '%%CPTELP%%',
      '%%CPHP%%',
      '%%NAMA%%',
      '%%JABATAN%%'
    );
    $replaceWith = array(
      $kode_laporan,
      date("Y"),
      date("l/ j F Y", strtotime($rast->tgl_kejadian)),
      date("l/ j F Y", strtotime($rast->approve_at)),
      '[' . $r->st_site_id . ']' . $r->st_name,
      $rast->st_address,
      $r->st_region,
      $r->st_latitude . '/' . $r->st_longitude,
      $sebab,
      $rast->rincian,
      $rast->pic_region,
      $rast->telp,
      $rast->hp,
      $user->nama,
      $user->posisi
    );
    $emailBody = str_replace($pattern, $replaceWith, $raw);

    //get recipients
    $recipients = $db->get_results("SELECT nama,email1 FROM user WHERE `regional`='" . $user->regional . "' AND `role`='mgrr'");

    if (!empty($recipients)) {
      foreach ($recipients as $recipient) {
        $to[$recipient->nama] = $recipient->email1;
      }
      require 'initMail.php';
      sendMail('Klaim AST [' . $r->st_site_id . ']' . $r->st_name . ' ' . date("d/m/Y", strtotime($_POST['tgl_kejadian'])), $emailBody, $to, $cc, $bcc);
    }
    $insast = 1;
  endif;
}
?>

<table width="1100" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
  <?php include "webheader.php" ?>
  <tr valign="top">
    <td style="width:250px">
      <ul style="list-style:none;padding-left:5px">
        <?php include "menu.php" ?>
    </td>
    <td>
      <?php if ($insast == 1) { ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;">
          <p>Laporan AST berhasil di submit!</p>
        </div>
        <?php
      } else { ?>
        <h3>
          <center>Laporan Awal AST</center>
        </h3>
        <?php if (!empty($err)) { ?>
          <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;">
            <p>Mohon isi/ perbaiki data berikut:
            <ul>
              <?php foreach ($err as $e):
                ?>
                <li>
                  <?= ucfirst($e) ?>
                </li>
              <?php endforeach; ?>
            </ul>
            </p>
          </div>
        <?php } ?>

        <form method="post" action="" name="" enctype="multipart/form-data" autocomplete="off">
          <table id="fast" width="100%" border="0">
            <tr class="even" valign="top">
              <td width="13%" align="left">Hari / tanggal kejadian</td>
              <td colspan="2">
                <input type="text" name="tgl_kejadian_show" value="<?= $_POST['tgl_kejadian_show'] ?>"
                  id="tgl_kejadian_show" />
                <input type="hidden" name="tgl_kejadian" id="tgl_kejadian" value="<?= $_POST['tgl_kejadian'] ?>" />
              </td>
            </tr>

            <tr class="odd">
              <td align="left">Site ID</td>
              <td colspan="2">
                <select name="site" id="lokasite">
                  <option value="">-Pilih Site ID-</option>

                  <?php $resSite = $db->get_results("SELECT st_name,st_site_id FROM `site` WHERE kode_region='" .
                    $user->regional . "' GROUP BY st_site_id ORDER BY st_site_id ASC");
                  foreach ($resSite as $site): ?>
                    <option value="<?= $site->st_site_id ?>" <?= ($site->st_site_id == $_POST['lokasite'] ? 'selected="selected"' : '') ?>>
                      <?= $site->st_site_id ?> /
                      <?= $site->st_name ?>
                    </option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>

            <tr class="odd" valign="top">
              <td align="left">Site Detail</td>
              <td colspan="1">
                <div id="siteDetailAST"></div>
              </td>
            </tr>

            <tr>
              <td colspan="3">&nbsp;</td>
            </tr>
            <tr valign="top" class="odd">
              <td align="left">Contact person</td>
              <td colspan="1">
                <table>
                  <tr class="even">
                    <td>Nama</td>
                    <td>: <input type="text" name="pic_reg" style="width:220px" /></td>
                  </tr>
                  <tr class="odd">
                    <td>No telepon/Fax</td>
                    <td>: <input type="text" name="telp" style="width:220px" /></td>
                  </tr>
                  <tr class="even">
                    <td>No HP</td>
                    <td>: <input type="text" name="hp" style="width:220px" /></td>
                  </tr>
                </table>
              </td>
            </tr>

            <tr>
              <td colspan="2">&nbsp;</td>
            </tr>
            <tr class="even" valign="top">
              <th align="left">Cause of damage</th>
              <td colspan="1">
                <select name="cod">
                  <option value=""> -Sebab- </option>
                  <option value="thf">Theft (Pencurian) </option>
                  <option value="lit">Lightning (Petir) </option>
                  <option value="fre">Fire (Terbakar/ Kebakaran) </option>
                  <option value="nds">Natural Dissaster (Bencana Alam)</option>
                  <option value="rio">Riots/ Strikes, Malicious Damage (Kerusuhan) </option>
                  <option value="trp">Third Party (Tuntutan Pihak ketiga) </option>
                  <option value="etv">Earthquake, Tsunami, Volcano Erruption </option>
                  <option value="oth">Other Losses (Lainnya..) </option>
                </select>

                <?= $site->st_site_id ?>"
                <?= ($site->st_site_id == $_POST['lokasite'] ? 'selected="selected"' : '') ?>>
              </td>
            </tr>

            <tr class="odd" valign="top">
              <th align="left">Status Claim</th>
              <td colspan="1">

                <input type="radio" name="sclaim" value="total" id="total" onchange="toogleAssetCtgor()" /> <label
                  for="total">Totally lost</label>
                <br />
                <input type="radio" name="sclaim" value="partial" id="partial" onchange="toogleAssetCtgor()" /> <label
                  for="partial">Partial lost</label>
              </td>
            </tr>
            <script type="text/javascript" language="javascript">
              function toogleAssetCtgor() {
                var rdsub = document.getElementById("c<?= $row->asset_group_id ?>");
                var rdTotal = document.getElementById("total");
                var rdPartial = document.getElementById("partial");
                if (rdTotal.checked) {
                  document.getElementById("divAssetCtgor1").style.display = "block";
                  document.getElementById("divAssetCtgor2").style.display = "none";
                }
                else {
                  document.getElementById("divAssetCtgor1").style.display = "none";
                  document.getElementById("divAssetCtgor2").style.display = "block";
                }
              }
            </script>

            <tr>
              <th align="left" colspan="2"> Asset Category</th>
            </tr>
            <tr>
              <td>&nbsp;</td>
              <td colspan="">
                <div class="subtabel" id="divAssetCtgor1"> <!------ disable script -->
                  <div id="total_lost"></div>
                </div>

                <div class="subtabel" id="divAssetCtgor2"> <!------ disable script -->
                  <div id="partial_lost"></div>
                </div>

              </td>
            </tr>

            <tr>
              <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
              <th colspan="1" rowspan="1" nowrap="nowrap" align="left">Dokumen-dokumen</th>
              <td colspan="2">
                <select name="doc" id="docid" onChange="changeDoc()">
                  <option value="">- Dokumen-dokumen - </option>
                  <option value="doc_hil">BA Kerugian Asset </option>
                  <option value="doc_kro">BA Kronologis </option>
                  <option value="doc_fo">Foto </option>
                  <option value="doc_rinci">Rincian Kerusakan </option>

                  <option value="doc_pol">BA Kepolisian </option>
                  <option value="doc_pmk">Surat PMK </option>
                  <option value="doc_bmkg">Surat BMKG </option>
                </select>
              </td>
            </tr>

            <script type="text/javascript">
              function changeDoc() {
                var block0 = document.getElementById("purchaseDecisionData0");
                var block0a = document.getElementById("purchaseDecisionData0a");
                var block0b = document.getElementById("purchaseDecisionData0b");
                var block0c = document.getElementById("purchaseDecisionData0c");

                var block1 = document.getElementById("purchaseDecisionData1");
                var block1a = document.getElementById("purchaseDecisionData1a");
                var block1b = document.getElementById("purchaseDecisionData1b");
                var block1c = document.getElementById("purchaseDecisionData1c");

                var block2 = document.getElementById("purchaseDecisionData2");
                var block2a = document.getElementById("purchaseDecisionData2a");
                var block2b = document.getElementById("purchaseDecisionData2b");
                var block2c = document.getElementById("purchaseDecisionData2c");

                var block3 = document.getElementById("purchaseDecisionData3");
                var block3a = document.getElementById("purchaseDecisionData3a");
                var block3b = document.getElementById("purchaseDecisionData3b");
                var block3c = document.getElementById("purchaseDecisionData3c");

                var block4 = document.getElementById("purchaseDecisionData4");
                var block4a = document.getElementById("purchaseDecisionData4a");
                var block4b = document.getElementById("purchaseDecisionData4b");
                var block4c = document.getElementById("purchaseDecisionData4c");

                var block5 = document.getElementById("purchaseDecisionData5");
                var block5a = document.getElementById("purchaseDecisionData5a");
                var block5b = document.getElementById("purchaseDecisionData5b");
                var block5c = document.getElementById("purchaseDecisionData5c");

                var block6 = document.getElementById("purchaseDecisionData6");
                var block6a = document.getElementById("purchaseDecisionData6a");
                var block6b = document.getElementById("purchaseDecisionData6b");
                var block6c = document.getElementById("purchaseDecisionData6c");

                if (document.getElementById('docid').value == "doc_hil") {
                  block0.style.display = "block";
                  block0a.style.display = "block";
                  block0b.style.display = "block";
                  block0c.style.display = "block";
                }
                else if (document.getElementById('docid').value == "doc_kro") {
                  block1.style.display = "block";
                  block1a.style.display = "block";
                  block1b.style.display = "block";
                  block1c.style.display = "block";
                }
                else if (document.getElementById('docid').value == "doc_fo") {
                  block2.style.display = "block";
                  block2a.style.display = "block";
                  block2b.style.display = "block";
                  block2c.style.display = "block";
                }
                else if (document.getElementById('docid').value == "doc_rinci") {
                  block3.style.display = "block";
                  block3a.style.display = "block";
                  block3b.style.display = "block";
                  block3c.style.display = "block";
                }
                else if (document.getElementById('docid').value == "doc_pol") {
                  block4.style.display = "block";
                  block4a.style.display = "block";
                  block4b.style.display = "block";
                  block4c.style.display = "block";
                }
                else if (document.getElementById('docid').value == "doc_pmk") {
                  block5.style.display = "block";
                  block5a.style.display = "block";
                  block5b.style.display = "block";
                  block5c.style.display = "block";
                }
                else if (document.getElementById('docid').value == "doc_bmkg") {
                  block6.style.display = "block";
                  block6a.style.display = "block";
                  block6b.style.display = "block";
                  block6c.style.display = "block";
                }
                else {
                  block.style.display = "none";
                  block.style.display = "none";
                  block.style.display = "none";
                  block.style.display = "none";
                }
              }
            </script>
            <tr>
              <td colspan="3">&nbsp;</td>
            </tr>

            <tr>
              <td colspan="3" rowspan="1">
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <div id="purchaseDecisionData0" style="display:none; margin-left:20px">
                    <tr class="even">
                      <td>
                        <div id="purchaseDecisionData0a" style="display:none">Dokumen #1 Berita acara kerusakan atau
                          kehilangan Aktiva Tetap (Rincian Objek yang Mengalami Kerugian)</div>
                        <div id="purchaseDecisionData0b" style="display:none" class="keterangan">
                          <?= $rast->doc_hil_file == '' ? '-belum ada-' : $rast->doc_hil_file ?>
                        </div>
                      </td>
                      <td colspan="2">
                        <div id="purchaseDecisionData0c" style="display:none"><input type="file" name="doc_hil" /></div>
                      </td>
                    </tr>
                  </div>
                  <div id="purchaseDecisionData1" style="display:none; margin-left:20px">
                    <tr class="even">
                      <td>
                        <div id="purchaseDecisionData1a" style="display:none">Dokumen #2 Kronologi kejadian/ kerugian
                        </div>
                        <div id="purchaseDecisionData1b" style="display:none" class="keterangan">
                          <?= $rast->doc_kro_file == '' ? '-belum ada-' : $rast->doc_kro_file ?>
                        </div>
                      </td>
                      <td colspan="2">
                        <div id="purchaseDecisionData1c" style="display:none"><input type="file" name="doc_kro" /></div>
                      </td>
                    </tr>
                  </div>
                  <div id="purchaseDecisionData2" style="display:none; margin-left:20px">
                    <tr class="even">
                      <td>
                        <div id="purchaseDecisionData2a" style="display:none">Dokumen #3 Foto Objek Kerugian </div>
                        <div id="purchaseDecisionData2b" style="display:none" class="keterangan">
                          <?= $rast->doc_fo_file == '' ? '-belum ada-' : $rast->doc_fo_file ?>
                        </div>
                      </td>
                      <td colspan="2">
                        <div id="purchaseDecisionData2c" style="display:none"><input type="file" name="doc_fo" /></div>
                      </td>
                    </tr>
                  </div>

                  <div id="purchaseDecisionData3" style="display:none; margin-left:20px">
                    <tr class="even">
                      <td>
                        <div id="purchaseDecisionData3a" style="display:none">Dokumen #4 Rincian Kerugian</div>
                        <div id="purchaseDecisionData3b" style="display:none" class="keterangan">
                          <?= $rast->doc_rinci_file == '' ? '-belum ada-' : $rast->doc_rinci_file ?>
                        </div>
                      </td>
                      <td colspan="2">
                        <div id="purchaseDecisionData3c" style="display:none"><input type="file" name="doc_rinci" /></div>
                      </td>
                    </tr>
                  </div>

                  <div id="purchaseDecisionData4" style="display:none; margin-left:20px">
                    <tr class="even">
                      <td>
                        <div id="purchaseDecisionData4a" style="display:none">Dokumen Khusus (BA Kepolisian)</div>
                        <div id="purchaseDecisionData4b" style="display:none" class="keterangan">
                          <?= $rast->doc_pol_file == '' ? '-belum ada-' : $rast->doc_pol_file ?>
                        </div>
                      </td>
                      <td colspan="2">
                        <div id="purchaseDecisionData4c" style="display:none"><input type="file" name="doc_pol" /></div>
                      </td>
                    </tr>
                  </div>

                  <div id="purchaseDecisionData5" style="display:none; margin-left:20px">
                    <tr class="even">
                      <td>
                        <div id="purchaseDecisionData5a" style="display:none">Dokumen Khusus (Surat PMK)</div>
                        <div id="purchaseDecisionData5b" style="display:none" class="keterangan">
                          <?= $rast->doc_pmk_file == '' ? '-belum ada-' : $rast->doc_pmk_file ?>
                        </div>
                      </td>
                      <td colspan="2">
                        <div id="purchaseDecisionData5c" style="display:none"><input type="file" name="doc_pmk" /></div>
                      </td>
                    </tr>
                  </div>

                  <div id="purchaseDecisionData6" style="display:none; margin-left:20px">
                    <tr class="even">
                      <td>
                        <div id="purchaseDecisionData6a" style="display:none">Dokumen Khusus (Surat BMKG)</div>
                        <div id="purchaseDecisionData6b" style="display:none" class="keterangan">
                          <?= $rast->doc_bmkg_file == '' ? '-belum ada-' : $rast->doc_bmkg_file ?>
                        </div>
                      </td>
                      <td colspan="2">
                        <div id="purchaseDecisionData6c" style="display:none"><input type="file" name="doc_bmkg" /></div>
                      </td>
                    </tr>
                  </div>
                </table>
              </td>
            </tr>

            <tr class="even">
              <td>&nbsp;</td>
              <td style="text-align:right" colspan="2"><input type="submit" value="Submit laporan AST" /></td>
            </tr>
          </table>
        </form>
      <?php } ?>
    </td>
  </tr>
</table>
<?php include "footer.php" ?>