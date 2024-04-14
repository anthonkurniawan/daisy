<?php
require 'init.php';
require 'priviledges.php';
include "headercgl.php";

$rcgl = $db->get_row("SELECT * FROM `cgl` WHERE cgl_id='" . $_GET['i'] . "'");
if ($_POST && $rcgl->status == 'INVOICE') {
  if ($_POST['tgl_settlement'] == '0000-00-00')
    $err[] = "tanggal settlement";
  if (strtotime($_POST['tgl_settlement']) < strtotime($rcgl->invoice_date))
    $err[] = "Tanggal settlement tidak boleh kurang dari tanggal invoice";

  if (empty($err)):
    $newStatus = 'SETTLED';
    $set = "`settlement_date`='" . $_POST['tgl_settlement'] . "', `settlement_at`=NOW()";
    $db->query("UPDATE cgl SET `status`='{$newStatus}',{$set} WHERE cgl_id='" . $_POST['i'] . "'");

    $db->query("INSERT INTO `status_log` 
		(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
		('cgl','" . $_POST['i'] . "','" . $rcgl->no_laporan . "','" . $user->user_id . "','{$newStatus}',NOW())");
    $upd = 1;

    //---- send emails
    $query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
    $r = $db->get_row($query);
    $r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");

    $raw = file_get_contents('cgl_survey.email.htm');
    $pattern = array(
      '%%NODOKUMEN%%',
      '%%TAHUN%%',
      '%%TGLKEJADIAN%%',
      '%%TGLDIKETAHUI%%',
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
      '%%JABATAN%%',
      '%%VENDOR%%',
      '%%PICNAMA%%',
      '%%PICTELP%%',
      '%%PICHP%%'
    );
    $replaceWith = array(
      $kode_laporan,
      date("Y"),
      date("l/ j F Y", strtotime($rcgl->tgl_kejadian)),
      date(
        "l/ j F Y",
        strtotime($rcgl->tgl_tuntutan)
      ),
      '[' . $r->st_site_id . ']' . $r->st_name,
      $rcgl->st_address,
      $r->st_region,
      $rcgl->st_latitude . '/' . $rcgl->st_longitude,
      $sebab . '.' . $rcgl->oth_sebab,
      $rcgl->rincian,
      $rcgl->cp_nama,
      $rcgl->cp_telp,
      $rcgl->cp_hp,
      $user->nama,
      $user->posisi,
      $r2->nama_vendor,
      $rcgl->vendor_pic,
      $rcgl->vendor_telp,
      $rcgl->vendor_hp
    );
    $emailBody = str_replace($pattern, $replaceWith, $raw);

    //get recipients
    $recipients = $db->get_results("SELECT nama,email1 FROM user WHERE `regional`='" . $user->regional . "' AND `role`='mgrr'");
    if (!empty($recipients)) {
      foreach ($recipients as $recipient) {
        $to[$recipient->nama] = $recipient->email1;
      }

      require 'initMail.php';
      sendMail('Klaim CGL [' . $r->st_site_id . ']' . $r->st_name . ' ' . date("d/m/Y", strtotime($rcgl->tgl_kejadian)) . ': SURVEY', $emailBody, $to, $cc, $bcc);
    }

    // sms
    $phone1 = $db->get_row("SELECT `phone` FROM `user` WHERE `role`='mgrr'");
    $phone2 = $db->get_row("SELECT `phone` FROM `user` WHERE `role`='spvr'");
    $text = $db->get_row("SELECT * FROM `cgl` WHERE  cgl_id='" . $_POST['i'] . "'");
    exec('c:\gammu\gammu-smsd-inject.exe -c c:\gammu\smsdrc EMS ' . $phone1->phone . ' -text "No.Lap= "' . $text->no_laporan . '", Side ID= "' . $text->st_site_id . '", Site Name= "' . $text->st_site_id . '", Status= "' . $text->status . '');
    exec('c:\gammu\gammu-smsd-inject.exe -c c:\gammu\smsdrc EMS ' . $phone2->phone . ' -text "No.Lap= "' . $text->no_laporan . '", Side ID= "' . $text->st_site_id . '", Site Name= "' . $text->st_site_id . '", Status= "' . $text->status . '');

  endif;
}

if ($_POST && $rcgl->status == 'APPROVED') {
  if ($_POST['isReject'] == '1') {
    $newStatus = 'REJECTED';
    $set = '`reject_at`=NOW()';
    $db->query("DELETE FROM cgl WHERE cgl_id='" . $_POST['i'] . "'");
  } else {
    $newStatus = 'SUBMITTED';  //----- EDIT TO SUBMITTED -----    
    $set = '`submit_at`=NOW()';
    $db->query("UPDATE cgl SET `status`='{$newStatus}',{$set} WHERE cgl_id='" . $_POST['i'] . "'");

    // sms
    $phone = $db->get_row("SELECT `phone` FROM `user` WHERE `role`='stfp'");
    $text = $db->get_row("SELECT * FROM `cgl` WHERE  cgl_id='" . $_POST['i'] . "'");
    $db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
	('cgl','" . $_POST['i'] . "','" . $rcgl->no_laporan . "','" . $user->user_id . "','{$newStatus}',NOW())");
    $upd = 1;

    // send emails
    $query = "SELECT st_site_id,st_name,st_region FROM `site` WHERE `st_site_id` = '{$rcgl->st_site_id}'";
    $r = $db->get_row($query);
    $r2 = $db->get_row("SELECT * FROM cgl_vendor WHERE id_cglv='{$rcgl->id_cglv}'");

    //$raw 			= file_get_contents('cgl_survey.email.htm');
    $raw = file_get_contents('cgl_submitted.email.htm');
    $pattern = array(
      '%%NODOKUMEN%%',
      '%%TAHUN%%',
      '%%TGLKEJADIAN%%',
      '%%TGLDIKETAHUI%%',
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
      '%%JABATAN%%',
      '%%VENDOR%%',
      '%%PICNAMA%%',
      '%%PICTELP%%',
      '%%PICHP%%',
      '%%TGLLAPOR%%',
      '%%ESTIMASI%%'
    );
    $replaceWith = array(
      $rcgl->no_laporan,
      date("Y"),
      date("l/ j F Y", strtotime($rcgl->tgl_kejadian)),
      date(
        "l/ j F Y",
        strtotime($rcgl->tgl_tuntutan)
      ),
      '[' . $r->st_site_id . ']' . $r->st_name,
      $rcgl->st_address,
      $r->st_region,
      $rcgl->st_latitude . '/' . $rcgl->st_longitude,
      $rcgl->sebab . '.' . $rcgl->other_sebab,
      $rcgl->rincian,
      $rcgl->cp_nama,
      $rcgl->cp_telp,
      $rcgl->cp_hp,
      $user->nama,
      $user->posisi,
      $r2->nama_vendor,
      $rcgl->vendor_pic,
      $rcgl->vendor_telp,
      $rcgl->vendor_hp,
      date("l/ j F Y"),
      $rcgl->estimasi
    );
    $emailBody = str_replace($pattern, $replaceWith, $raw);
    $recipients = $db->get_results("SELECT nama,email1,email2 FROM user WHERE (`regional`='" . $user->regional . "' AND `role`='mgrr') OR role='spvp'");
    if (!empty($recipients)) {
      foreach ($recipients as $recipient) {
        if ($recipient->email2 <> '') {
          $to[$recipient->nama] = $recipient->email1;
          if ($recipient->email2)
            $to[$recipient->nama . ' 2'] = $recipient->email2;
        } else {
          $to[$recipient->nama] = $recipient->email1;
        }
      }

      require 'initMail.php';
      sendMail('Klaim CGL [' . $r->st_site_id . ']' . $r->st_name . ' ' . date("d/m/Y", strtotime($rcgl->tgl_kejadian)) . ': SURVEY', $emailBody, $to, $cc, $bcc);
    }
  }

  //------------------------------------------------------- # 3.JIKA STATUS CLOSED
  if ($_POST['isCc'] == '1')
  {
    $newStatus = "CLOSED";
    $set = "caseclosed_at=NOW(), caseclosed_by='" . $user->user_id . "'";
    $db->query("UPDATE cgl SET `status`='{$newStatus}',{$set} WHERE cgl_id='" . $_POST['i'] . "'");
    $db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
	('cgl','" . $_POST['i'] . "','" . $rcgl->no_laporan . "','" . $user->user_id . "','{$newStatus}',NOW())");
    $upd = 1;
  }
}

//---------------------------------------------- JIKA STATUS ( isCc==1 )
if ($_POST['isCc'] == '1')       // KIRIM EMAIL???
{
  $newStatus = "CLOSED";
  $set = "caseclosed_at=NOW(), caseclosed_by='" . $user->user_id . "'";

  $db->query("UPDATE cgl SET `status`='{$newStatus}',{$set} WHERE cgl_id='" . $_POST['i'] . "'");

  $db->query("INSERT INTO `status_log` 
	(`ast_cgl`,`doc_id`,`no_dokumen`,`updated_by`,`doc_status`,`updated_at`) VALUES 
	('cgl','" . $_POST['i'] . "','" . $rcgl->no_laporan . "','" . $user->user_id . "','{$newStatus}',NOW())");
  $upd = 1;
}
?>

<table width="1000" border="0" cellpadding="0" cellspacing="0" style="margin:0px auto" id="badan">
  <?php include "webheader.php"; ?>
  <tr valign="top">
    <td style="width:140px">
      <?php include "menusuper.php" ?>
    </td>
    <td>
      <?php if ($upd == 1) { ?>
        <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;">
          <p>Laporan CGL telah di
            <?= $newStatus ?>!
          <div style="text-align:right"><a href="lap_cgl.php">&laquo; Kembali</a></div>
          </p>
        </div>
      <?php
      } else { ?>

        <h3>Detail Laporan CGL</h3>
        <?php if (!empty($err)) { ?>
          <div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;width:300px;margin:30px auto;">
            <p>Mohon isi / perbaiki data berikut:
            <ul>
              <?php foreach ($err as $e):
                ?>
                <li>
                  <?= ucfirst($e) ?>
                </li>
              <?
              endforeach;
              ?>
            </ul>
            </p>
          </div>
        <?php
        } ?>

        <form method="post" action="">
          <input type="hidden" name="i" value="<?= $rcgl->cgl_id ?>" /> <!------- VAR= i ------------>
          <table>
            <tr class="odd">
              <td style="width:200px">Nomor Laporan</td>
              <td><?= $rcgl->no_laporan ?></td>
            </tr>
            <tr class="even">
              <td>Tangal Kejadian</td>
              <td>
                <?= date("l, j F Y", strtotime($rcgl->tgl_kejadian)) ?>
              </td>
            </tr>
            <tr class="odd">
              <td>Tanggal diketahui Telkomsel</td>
              <td>
                <?= date("l, j F Y", strtotime($rcgl->tgl_tuntutan)) ?>
              </td>
            </tr>
            <tr class="even">
              <td>Tempat/ Lokasi Kerugian</td>
              <td><?= $rcgl->st_site_id ?></td>
            </tr>
            <tr class="odd">
              <td>Penyebab Kerugian</td>
              <td><?= $rcgl->sebab ?></td>
            </tr>
            <tr class="even">
              <td><strong>Rincian Kerusakan</strong></td>
              <td><?= $rcgl->rincian ?></td>
            </tr>

            <tr class="odd">
              <td><strong>Estimasi Kerugian</strong></td>
              <td>
                <?php
                if ($rcgl->kerugian_survey <> '')
                  echo number_format($rcgl->kerugian_survey, 0, '', '.');
                else
                  echo number_format($rcgl->estimasi, 0, '', '.');
                ?>
              </td>
            </tr>

            <tr class="even">
              <td>Contact Person</td>
              <td>
                Nama: <?= $rcgl->cp_nama ?><br />
                Telepon: <?= $rcgl->cp_telp ?><br />
                HP: <?= $rcgl->cp_hp ?>
              </td>
            </tr>

            <tr valign="top" class="odd">
              <td><strong>Vendor Pelaksana</strong></td>
              <td>
                <?php $resVendor = $db->get_row("SELECT * FROM cgl_vendor WHERE `id_cglv`='" . $rcgl->id_cglv . "'"); ?>
                <strong><?= $resVendor->nama_vendor; ?></strong>
              </td>
            </tr>
            <tr class="even">
              <td>&nbsp;</td>
              <td>
                PIC: <?= $rcgl->vendor_pic ?><br />
                Telepon: <?= $rcgl->vendor_telp ?><br />
                HP: <?= $rcgl->vendor_hp ?>
              </td>
            </tr>
            <tr class="odd">
              <td>Lampiran Berkas Surat Tuntutan</td>
              <td>
                <?php if ($rcgl->file_surat_tuntutan == ''): ?>
                  <cite>- belum ada -</cite>
                <?php else: ?>
                  <a href="docs/cgl/<?= $rcgl->file_surat_tuntutan ?>"><?= $rcgl->file_surat_tuntutan ?></a>
                <?php endif; ?>
              </td>
            </tr>
            <tr class="even">
              <td>Lampiran Berkas BoQ Vendor</td>
              <td>
                <?php if ($rcgl->file_boq == ''): ?>
                  <cite>- belum ada -</cite>
                <?php else: ?>
                  <a href="docs/cgl/<?= $rcgl->file_boq ?>"><?= $rcgl->file_boq ?></a>
                </td>
              <?php endif; ?>
            </tr>
            <tr class="odd">
              <td>Tanggal Disetujui:</td>
              <td><?= $rcgl->approve_at <> '' ? date("l, j F Y", strtotime($rcgl->approve_at)) : '-' ?></td>
            </tr>
            <tr class="even">
              <td>Tanggal Submit ke SJU:</td>
              <td><?= $rcgl->submit_at <> '' ? date("l, j F Y", strtotime($rcgl->submit_at)) : '-' ?></td>
            </tr>

            <tr class="odd">
              <td>Tanggal Survey:</td>
              <td><?= $rcgl->survey_date <> '' ? date("l, j F Y", strtotime($rcgl->survey_date)) : '-' ?></td>
            </tr>
            <tr class="even">
              <td>Tanggal Payment:</td>
              <td><?= $rcgl->payment_date <> '' ? date("l, j F Y", strtotime($rcgl->payment_date)) : '-' ?></td>
            </tr>
            <tr class="odd">
              <td>Tanggal Invoice:</td>
              <td><?= $rcgl->invoice_date <> '' ? date("l, j F Y", strtotime($rcgl->invoice_date)) : '-' ?></td>
            </tr>

            <?php if ($rcgl->status == 'INVOICE'): ?>
              <tr class="odd">
                <td>Tanggal Settlement:</td>
                <td>
                  <input type="text" name="tgl_settlement_show" id="tgl_settlement_show" class="narr" />
                  <input type="hidden" name="tgl_settlement" id="tgl_settlement" value="<?= $rcgl->settlement_date ?>" />
                </td>
              </tr>

            <?php endif; ?>

            <tr class="even">
              <td>
                <?php if ($user->role == 'gmp' && $rcgl->status <> 'CLOSED') { ?>
                  <div style="margin:10px;background:#fcc;padding:5px;text-align:center;">
                    <input type="hidden" value="0" name="isCc" />
                    <input
                      onclick="if(confirm('Case Closed Laporan CGL <?= $rcgl->no_laporan ?>'))isCc.value=1;else return false;"
                      type="submit" value="Set CASE CLOSED" />
                  </div>
                <?php
                } ?>

                <input style="cursor:pointer;" type="button" onclick="document.location.href='lap_cgl.php'"
                  value="Kembali" />
                <input style="cursor:pointer;" type="button" value="Print"
                  onclick="window.open ('printDetailCGL.php?cgl=<?= $rcgl->cgl_id ?>','daisy','status=0,menubar=1,scrollbars=1,resizable=1,width=794,height=1123');" />
              </td>

              <td>
                <?php if (in_array($rcgl->status, array('SUBMITTED', 'SURVEY', 'PAYMENT', 'SETTLEMENT', 'CLOSED', 'REJECTED')))
                  echo 'Current status: ' . $rcgl->status . '<br />'; ?>
                <?php if ($rcgl->status == 'APPROVED') { ?>
                  <input type="hidden" value="0" name="isReject" />
                  <input onclick="isReject.value=1" type="submit" value="Set REJECTED" />
                  <input type="submit" value="Set SUBMITTED" />
                <?php
                } ?>

                <?php if ($rcgl->status == 'INVOICE') { ?>
                  <input type="submit" value="Set Klaim : SETTLED" />
                <?php } ?>
              </td>
            </tr>
          </table>
        </form>
      <?php
      } ?>
    </td>
  </tr>
</table>

<?php include "footer.php" ?>