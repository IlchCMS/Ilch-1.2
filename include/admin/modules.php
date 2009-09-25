<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined ('main') or die ('no direct access');

switch ($menu->get(1)) {
#    default : 				$file = 'imi';			break;

// Vorlaeufiges DEFAULT
    default : 				header('Location: admin.php?modules-adminmenu'); exit;			break;

    case 'adminmenu' :		$file = 'adminmenu';	break;
	case 'database' :		$file = 'database';		break;
}

require_once('include/admin/modules/' . $file . '.php');

?>