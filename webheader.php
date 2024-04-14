<tr>
  <td colspan="2" id="header">
    <h2>D A I S Y (DAta Insurance SYstem)</h2>
  </td>
</tr>
<tr>
  <td colspan="2" id="userinfo">
    Selamat datang,
    <?php
    switch ($user->role) {
      case 'spvr':
        echo 'Supervisor Regional';
        break;
      case 'mgrr':
        echo 'Manager Regional';
        break;
      case 'stfp':
        echo 'Staff';
        break;
      case 'spvp':
        echo 'Supervisor';
        break;
      case 'gmp':
        echo 'Manager / GM';
        break;
    }

    if ($user["role"] == 'spvp' || $user["role"] == 'gmp') {
      ?>
      <?php
      $r = $db->getRow("SELECT region FROM `region` WHERE kode_region='" . $user['regional'] . "'");
      echo $r['region'];
      ?> &bull;
      <strong>
        <?= $user['nama'] ?>
        <?php if ($user['inisial']) {
          echo $user['inisial'];
        } ?>
      </strong>
      &bull; <a href="cpass.php">Ubah password</a> &bull; <a href="webhelp.php">Panduan</a> &bull; <a
        href="webhelp.php">Dokumen</a>&bull; <a href="logout.php">Logout &raquo;</a>
    <?php
    } else {
      $r = $db->getRow("SELECT region FROM `region` WHERE kode_region='" . $user['regional'] . "'");
      echo $r['region'];
    }
    ?> &bull;
    <?php echo $user['nama'] ?>
    <?php if ($user['inisial']) {
      echo $user['inisial'];
    } ?> <a href="cpass.php">Ubah password</a> &bull;
    <a href="webhelp.php">Panduan</a> &bull;
    <a href="logout.php">Logout &raquo;</a>
  </td>
</tr>