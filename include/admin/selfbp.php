<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$design = new design('Ilch Admin-Control-Panel :: Eigene Box/Page', '', 2);
// func einbinden
$funcs = read_ext('include/admin/inc/selfbp', 'php');

foreach ($funcs as $file) {
    require_once('include/admin/inc/selfbp/' . $file);
}

switch ($menu->get(1)) {
    case 'imagebrowser':
        $file = 'imagebrowser.php';
        break;
    case 'overview':
        $file = 'overview.php';
        break;
    default:
        $file = 'selfbp.php';
        break;
}
require_once("include/admin/inc/selfbp/pages/" . $file);
// check ob selfp und selfbp beschreibbar sind
$f = false;
if (!is_writable('./include/contents/selfbp/selfp')) {
    $f = true;
    echo 'Das include/contents/selfbp/selfp Verzeichnis braucht chmod 777 Rechte damit du eine eigene Datei erstellen kannst!<br /><br />';
}
if (!is_writable('./include/contents/selfbp/selfb')) {
    echo 'Das include/contents/selfbp/selfb Verzeichnis braucht chmod 777 Rechte damit du eine eigene Box erstellen kannst!<br /><br />';
    if ($f == true) {
        exit('Entweder das include/contents/selfbp/selfb oder das include/contents/selfbp/selfp Verzeichnis brauchen Schreibrechte sonst kann hier nicht gearbeitet werden');
    }
}

?>