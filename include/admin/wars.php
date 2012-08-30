<?php
/**
 * @license http://opensource.org/licenses/gpl-2.0.php The GNU General Public License (GPL)
 * @copyright (C) 2000-2010 ilch.de
 * @version $Id$
 */
defined('main') or die('no direct access');
defined('admin') or die('only admin access');

$um = $menu->get(1);


$_REQUEST[ 'mid' ] = (array_key_exists('mid',$_REQUEST)) ? escape($_REQUEST[ 'mid' ], 'integer'):'';
$_REQUEST[ 'wid' ] = (array_key_exists('wid',$_REQUEST)) ? escape($_REQUEST[ 'wid' ], 'integer'):'';
$_POST[ 'add_uid' ] = (array_key_exists('add_uid',$_POST)) ? escape($_POST[ 'add_uid' ], 'integer'):'';
$_GET[ 'delete_uid' ] = (array_key_exists('delete_uid',$_GET)) ? escape($_GET[ 'delete_uid' ], 'integer'):'';
$_GET[ 'delete' ] = (array_key_exists('delete',$_GET)) ? escape($_GET[ 'delete' ], 'integer'):'';
$_GET[ 'pkey' ] = (array_key_exists('pkey',$_GET)) ? escape($_GET[ 'pkey' ], 'integer'):'';
// get Flag list
// 1 akt flag
function get_wlp_array() {
    $ar = array(1 => 'gewonnen',
        2 => 'verloren',
        3 => 'unentschieden'
        );
    return ($ar);
}

function get_datime() {
    $own = true;
    $_POST['datum'] = explode('-',$_POST['datum']);
    $_POST[ 'day' ] = escape($_POST['datum'][1], 'integer');
    $_POST[ 'mon' ] = escape($_POST['datum'][2], 'integer');
    $_POST[ 'jahr' ] = escape($_POST['datum'][0], 'integer');
    $_POST[ 'stu' ] = escape($_POST[ 'stu' ], 'integer');
    $_POST[ 'min' ] = escape($_POST[ 'min' ], 'integer');
    $_POST[ 'sek' ] = escape($_POST[ 'sek' ], 'integer');
    if (checkdate($_POST[ 'mon' ], $_POST[ 'day' ], $_POST[ 'jahr' ]) == false)
    {
        $own = false;
    } elseif ($_POST[ 'stu' ] > 24 OR $_POST[ 'min' ] > 60 OR $_POST[ 'sek' ] > 60)
    {
        $own = false;
    }
    if ($own) 
    {
        return ($_POST[ 'jahr' ] . '-' . $_POST[ 'mon' ] . '-' . $_POST[ 'day' ] . ' ' . $_POST[ 'stu' ] . ':' . $_POST[ 'min' ] . ':' . $_POST[ 'sek' ]);
    }
    else
    {
        return (date('Y-m-d H:i:s'));
    }
}

switch ($um)
{
    default:
        include ('include/admin/contents/wars/default.php');
    break;
    // last wars
    case 'last':
        include ('include/admin/contents/wars/lastwars.php');
    break;
    // Next wars
    case 'next':
        include ('include/admin/contents/wars/nextwars.php');
    break;
    case 'info':
        include ('include/admin/contents/wars/wardetails.php');
    break;
}
?>