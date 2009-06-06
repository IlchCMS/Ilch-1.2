<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

$design = new design ('Admins Area', 'Admins Area', 2);
$design->header();

if (empty ($_POST['SEND'])) {
    $auswahl = array (
        'u0' => 'an alle User',
        );

    $erg = db_query("SELECT name,id FROM prefix_groups ORDER BY id");
    while ($RRrow = db_fetch_object($erg)) {
        $auswahl['g' . $RRrow->id] = $RRrow->name;
    }
    $listeB = '';
    $listeT = '';
    foreach ($auswahl as $k => $v) {
        if (strpos($k, 'u') !== false) {
            $listeB .= '<option value="P' . $k . '">' . $v . ' PrivMsg</option>' . "\n";
            $listeB .= '<option value="E' . $k . '">' . $v . ' eMail</option>' . "\n";
        } elseif (strpos($k, 'g') !== false) {
            $listeT .= '<option value="P' . $k . '">' . $v . ' PrivMsg</option>' . "\n";
            $listeT .= '<option value="E' . $k . '">' . $v . ' eMail</option>' . "\n";
        }
    }

    ?>

<table cellpadding="0" cellspacing="0" border="0"><tr><td><img src="include/images/icons/admin/newsletter.png" /></td><td width="30"></td><td valign="bottom"><h1>Newsletter</h1></td></tr></table>


<form action="admin.php?newsletter" method="POST">


<table width="100%" border="0" cellspacing="1" cellpadding="2" class="border">
    <tr>
      <td colspan="2" height="25" class="Cdark">Hier kannst du einen Newsletter verschicken!</td>
    </tr>
    <tr>
      <td class="Cmite"><b>Ausw&auml;hlen</b></td>
      <td class="Cnorm">
			    <select name="auswahl">
						  <option value="Enews">eMail Newsletter</option>
							<optgroup label="Benutzer">
							  <?php echo $listeB; ?>
							</optgroup>
							<optgroup label="Gruppen">
							  <?php echo $listeT; ?>
							</option>
			    </select>
			</td>
    </tr>
    <tr>
      <td class="Cmite"><b>Betreff</b></td>
      <td class="Cnorm">
        <input type="text" name="bet" size="50">
      </td>
    </tr>
    <tr>
      <td class="Cmite" valign="top"><b>Text</b></td>
      <td class="Cnorm">
        <textarea cols="50" rows="10" name="txt"></textarea>
      </td>
    </tr>
    <tr class="Cdark">
      <td>&nbsp;</td>
      <td>
        <input type="submit" value="Absenden" name="SEND">
      </td>
    </tr>
  </table>
</form>

<?php

} else {
    $mailopm = substr($_POST['auswahl'], 0, 1);
    $usrogrp = substr($_POST['auswahl'], 1, 1);

    if ($_POST['auswahl'] == 'Enews') {
        $q = "SELECT email FROM prefix_newsletter";
    }elseif ($usrogrp == 'u') {
        $q = "SELECT email, name as uname, id as uid FROM prefix_user WHERE recht <= -1";
    }elseif (true == strpos($_POST['auswahl'], 'g')) {
        $gid = substr ($_POST['auswahl'], 2 , strlen ($_POST['auswahl']) - 1);
        $q = "SELECT b.email, b.name as uname, b.id as uid FROM prefix_groupusers a LEFT JOIN prefix_user b ON a.uid = b.id WHERE a.gid = " . $gid;
    }

    $erg = db_query ($q);

    $zahler = 0;
    if (db_num_rows($erg) > 0) {
        while ($row = db_fetch_object($erg)) {
            if ($mailopm == 'E') {
                icmail ($row->email , $_POST['bet'], $_POST['txt']);
            }elseif ($mailopm == 'P') {
                sendpm($_SESSION['authid'], $row->uid, escape($_POST['bet'], 'string'), escape($_POST['txt'], 'string'));
            }
            $zahler++;
        }
        if ($mailopm == 'E') {
            $eMailorPmsg = 'eMail(s)';
        } elseif ($mailopm == 'P') {
            $eMailorPmsg = 'Private Nachrichte(n)';
        }
        echo 'Es wurde(n) ' . $zahler . ' ' . $eMailorPmsg . ' verschickt';
    }else {
        echo 'F&uuml;r diese Auswahl konnte nichts gefunden werden';
    }
}

$design->footer();

?>