<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

$design = new design ('Admins Area', 'Admins Area', 2);
$design->header( 'jquery/templates/redmond/jquery-ui-1.7.2.allg.css' );

if (!is_admin()) {
    echo 'Dieser Bereich ist nicht fuer dich...';
    $design->footer();
    exit();
}

// Load needed functions
$funcs = read_ext ('include/admin/inc/allg', 'php');

foreach ( $funcs as $file ){
	require_once('include/admin/inc/allg/'.$file);
}

if (empty ($_POST['submit'])) {

	// Template laden
	$tpl = new tpl ('allg', 1);
	
	// Template-Header ausgeben
	$tpl->out( 0 );
	
	$katid = 0;
    $katname = '';

    $abf = 'SELECT * FROM `prefix_config` ORDER BY `kat`,`pos`,`typ` ASC';
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        if ($katname != $row['kat']) {
			$katid++;
			$tpl->set_ar_out( Array( 'katid' => $katid, 'kat' => $row['kat'] ), 1 );
			$katname = $row['kat'];
        }

        if ($row['typ'] == 'input') {
            $input = '<input size="50" type="text" name="' . $row['schl'] . '" value="' . $row['wert'] . '">';
        } elseif ($row['typ'] == 'r2') {
            $checkedj = '';
            $checkedn = '';
            if ($allgAr[$row['schl']] == 1) {
                $checkedj = 'checked';
                $checkedn = '';
            } else {
                $checkedn = 'checked';
                $checkedj = '';
            }
            $input = '<input type="radio" name="' . $row['schl'] . '" value="1" ' . $checkedj . ' > ja'
					.'&nbsp;&nbsp;'
					.'<input type="radio" name="' . $row['schl'] . '" value="0" ' . $checkedn . ' > nein';
        } elseif ($row['typ'] == 's') {
            $vname = $row['schl'];
            $input = '<select name="' . $row['schl'] . '">' . $$vname . '</select>';
        } elseif ($row['typ'] == 'textarea') {
            $input = '<textarea cols="55" rows="3" name="' . $row['schl'] . '">' . $row['wert'] . '</textarea>';
        } elseif ($row['typ'] == 'grecht') {
            $grl = dblistee($allgAr[$row['schl']], "SELECT id,name FROM prefix_grundrechte ORDER BY id ASC");
            $input = '<select name="' . $row['schl'] . '">' . $grl . '</select>';
        } elseif ($row['typ'] == 'grecht2') {
            $grl = dblistee($allgAr[$row['schl']], "SELECT id,name FROM prefix_grundrechte WHERE id >= -2 ORDER BY id ASC");
            $input = '<select name="' . $row['schl'] . '">' . $grl . '</select>';
        } elseif ($row['typ'] == 'password') {
            $input = '<input size="50" type="password" name="' . $row['schl'] . '" value="***" />';
        }

		$tpl->set_ar_out( Array( 'frage' => $row['frage'], 'input' => $input ), 2 );
    }
	
	// Template-Footer ausgeben
    $tpl->out( 3 );
	
} else {
    $abf = 'SELECT * FROM `prefix_config` ORDER BY `kat`';
    $erg = db_query($abf);
    while ($row = db_fetch_assoc($erg)) {
        if ($row['typ'] == 'password' AND $_POST[$row['schl']] == '***') {
            continue;
        } elseif ($row['typ'] == 'password') {
            require_once('include/includes/class/AzDGCrypt.class.inc.php');
            $cr64 = new AzDGCrypt(DBDATE . DBUSER . DBPREF);
            $_POST[$row['schl']] = $cr64->crypt($_POST[$row['schl']]);
        }
        db_query('UPDATE `prefix_config` SET wert = "' . escape($_POST[$row['schl']], 'textarea') . '" WHERE schl = "' . $row['schl'] . '"');
    }
    wd ('admin.php?allg', 'Erfolgreich ge&auml;ndert' , 2);
}
// -----------------------------------------------------------|
$design->footer();

?>