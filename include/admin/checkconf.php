<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');
defined ('admin') or die ('only admin access');

if ($menu->get(1) == "phpinfo") {
    phpinfo();
} else {
    $design = new design ('Admins Area', 'Admins Area', 2);
    $design->header();

    $tpl = new tpl('checkconf', 1);
    $tpl->out(0);
    // # Server conf
    $tpl->set_out('head', $lang['phpserverconf'], 1);
    $tpl->set_ar_out(array('class' => 'Cmite', 'opt' => 'version', 'val' => phpversion()), 3);
    $confstrings = array("safe_mode",
        "display_errors",
        "max_execution_time",
        "memory_limit",
        "register_globals",
        "file_uploads",
        "upload_max_filesize",
        "post_max_size",
        "disable_functions"
        );
    $class = 'Cmite';
    foreach($confstrings as $str) {
        if ($class == 'Cmite') {
            $class = 'Cnorm';
        } else {
            $class = 'Cmite';
        }
        $tpl->set("class", $class);
        $tpl->set("opt", $str);
        $tpl->set("val", ini_get($str));
        $tpl->out(3);
    }
    // sockets
    if ($class == 'Cmite') {
        $class = 'Cnorm';
    } else {
        $class = 'Cmite';
    }
    $tpl->set("class", $class);
    $tpl->set("opt", 'sockets');
    $tpl->set("val", defined('AF_INET')? 1 : 0);
    $tpl->out(3);
    $tpl->out(2);
    // chmod
    $tpl->set_out('head', $lang['filesystemrights'], 1);

    $files = array('include/backup',
        'include/images/avatars',
        'include/images/gallery',
        'include/images/usergallery',
        'include/downs/downloads',
        'include/downs/downloads/user_upload',
        'include/images/wars',
        'include/contents/selfbp/selfp',
        'include/contents/selfbp/selfb',
        'include/images/smiles'
        );
    asort($files);
    $class = 'Cmite';
    foreach($files as $f) {
        if ($class == 'Cmite') {
            $class = 'Cnorm';
        } else {
            $class = 'Cmite';
        }
        $tpl->set("class", $class);
        $tpl->set("opt", $f);
        if (@is_writeable ($f)) {
            $val = $lang['correct'];
        } else {
            $val = '<span style="background-color: #f00;">' . $lang['incorrect'] . '</span>';
        }
        $tpl->set("val", $val);
        $tpl->out(3);
    }
    $tpl->out(2);
    // Server
    $result = db_query("SHOW TABLE STATUS");
    $dbsize = 0;
    while ($row = mysql_fetch_assoc($result)) {
        $dbsize += $row['Data_length'];
    }

    $tpl->set_out('head', 'Informationen', 1);
    $infos = array(
        'Serversoftware' => $_SERVER["SERVER_SOFTWARE"],
        'Server (PHP) Zeit' => date('Y-m-d H:i:s'),
        'SQL Zeit' => db_result(db_query("SELECT NOW()")),
        'MySQL-Version' => db_result(db_query("SELECT VERSION()")),
        'Datenbankgr&ouml;&szlig;e' => nicebytes($dbsize),
        'Avatarordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/avatars/')),
        'Galleryordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/gallery/')),
        'Usergalleryordnergr&ouml;&szlig;e' => nicebytes(dirsize('include/images/usergallery/'))
        );
    foreach($infos as $k => $str) {
        if ($class == 'Cmite') {
            $class = 'Cnorm';
        } else {
            $class = 'Cmite';
        }
        $tpl->set("class", $class);
        $tpl->set("opt", $k);
        $tpl->set("val", $str);
        $tpl->out(3);
    }
    $tpl->out(2);

    $tpl->out(5);
    $design->footer();
}

?>