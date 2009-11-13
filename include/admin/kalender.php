<?php
// Kalender Script © by Nickel
// ueberarbeitet von Manuel
defined( 'main' ) or die( 'no direct access' );
defined( 'admin' ) or die( 'only admin access' );
// Funktionen
function XAJAX_showCalendar( $m, $j, $f )
{
    if ( empty( $m ) ) {
        $m = date( 'n' );
    }
    if ( empty( $j ) ) {
        $j = date( 'Y' );
    }
    
    $objResponse = new xajaxResponse();
    
    $content = '<table border="0" cellpadding="1" cellspacing="1" class="border"><tr><td class="Cnorm"><a href="javascript:close' . $f . '();">schliessen</a></td></tr></table>';
    $content .= getCalendar( $m, $j, 'javascript:void(0);" onclick="set' . $f . '(\'{jahr}-{mon}-{tag}\')', 'javascript:void(0);" onclick="xajax_XAJAX_showCalendar({mon},{jahr},\'' . $f . '\')', '' );
    
    $objResponse->assign( 'skalender' . $f, 'style.display', 'block' );
    $objResponse->assign( 'skalender' . $f, 'innerHTML', $content );
    // return object
    return $objResponse;
}

function checkzyklusins( $x, $i0, $i1, $i2, $z, $sar )
{
    $ts = mktime( 0, 0, 0, $i1, $i2, $i0 );
    $wt = date( 'w', $ts );
    if ( $z == 'wer' AND ( $wt > 0 AND $wt < 6 ) ) {
        return ( true );
    } elseif ( $z == 'wek' AND ( $wt == 0 OR $wt == 6 ) ) {
        return ( true );
    } elseif ( $z == 'woc' AND ( ( $x % 7 ) == 1 ) ) {
        return ( true );
    } elseif ( $z == '14t' AND ( ( $x % 14 ) == 1 ) ) {
        return ( true );
    } elseif ( $z == 'mon' AND ( $i2 == $sar[ 2 ] ) ) {
        return ( true );
    } elseif ( $z == 'jae' AND ( $i1 == $sar[ 1 ] AND $i2 == $sar[ 2 ] ) ) {
        return ( true );
    }
    
    return ( false );
}

function zyklusinsert( $sar, $ear, $z, $_POST )
{
    $x        = 1;
    $first_id = 0;
    for ( $i0 = $sar[ 0 ]; $i0 <= $ear[ 0 ]; $i0++ ) {
        $sm = 1;
        $em = 12;
        if ( $sar[ 0 ] == $i0 ) {
            $sm = $sar[ 1 ];
        }
        if ( $ear[ 0 ] == $i0 ) {
            $em = $ear[ 1 ];
        }
        for ( $i1 = $sm; $i1 <= $em; $i1++ ) {
            $st = 1;
            $et = date( 't', mktime( 0, 0, 0, $i1, 1, $i0 ) );
            if ( $sar[ 0 ] == $i0 AND $sar[ 1 ] == $i1 ) {
                $st = $sar[ 2 ];
            }
            if ( $ear[ 0 ] == $i0 AND $ear[ 1 ] == $i1 ) {
                $et = $ear[ 2 ];
            }
            for ( $i2 = $st; $i2 <= $et; $i2++ ) {
                if ( checkzyklusins( $x, $i0, $i1, $i2, $z, $sar ) ) {
                    $time = mktime( $_POST[ 'stunde' ], $_POST[ 'minute' ], 0, $i1, $i2, $i0 );
                    db_query( "INSERT INTO `prefix_kalender` (`time`,`gid`,`title`,`text`,`recht`) VALUES (" . $time . "," . $first_id . ",'" . escape( $_POST[ 'title' ], 'string' ) . "','" . escape( $_POST[ 'txt' ], 'string' ) . "','" . escape( $_POST[ 'recht' ], 'integer' ) . "')" );
                    if ( $first_id == 0 ) {
                        $first_id = db_last_id();
                        db_query( "UPDATE `prefix_kalender` SET `gid` = " . $first_id . " WHERE `id` = " . $first_id );
                    }
                }
                $x++;
            }
        }
    }
}
// AJAX Start
$xajax = new xajax( 'http://' . $_SERVER[ "HTTP_HOST" ] . $_SERVER[ "SCRIPT_NAME" ] . '?kalender=0' );
$xajax->registerFunction( "XAJAX_showCalendar" );
$xajax->processRequest();
// DESIGN
$design = new design( 'Ilch Admin-Control-Panel :: Kalender', '', 2 );
$design->header();
// AJAX ausgabe
echo $xajax->printJavascript();

if ( !empty( $_REQUEST[ 'um' ] ) ) {
    $sar = explode( '-', $_POST[ 'begind' ] );
    if ( !@checkdate( $sar[ 1 ], $sar[ 2 ], $sar[ 0 ] ) ) {
        echo 'Das eingegebene Datum ist nicht g&uuml;ltig ';
        echo '<a href="javascript:history.back()">zur&uuml;ck</a>';
        $design->footer( 1 );
    }
    if ( !empty( $_POST[ 'zende' ] ) ) {
        $ear = explode( '-', $_POST[ 'zende' ] );
        if ( !@checkdate( $ear[ 1 ], $ear[ 2 ], $ear[ 0 ] ) ) {
            echo 'Das eingegebene Datum f&uuml;r das Zyklusende ist nicht g&uuml;ltig ';
            echo '<a href="javascript:history.back()">zur&uuml;ck</a>';
            $design->footer( 1 );
        }
    }
    $z = '';
    if ( isset( $_POST[ 'zyklus' ] ) ) {
        $z = $_POST[ 'zyklus' ];
    }
    $text = escape( $_POST[ 'txt' ], 'string' );
    
    $time = mktime( $_POST[ 'stunde' ], $_POST[ 'minute' ], 0, $sar[ 1 ], $sar[ 2 ], $sar[ 0 ] );
    // Einfuegen
    if ( $_REQUEST[ 'um' ] == 'insert' ) {
        if ( !empty( $z ) ) {
            zyklusinsert( $sar, $ear, $z, $_POST );
        } else {
            db_query( "INSERT INTO `prefix_kalender` (`time`,`title`,`text`,`recht`) VALUES (" . $time . ",'" . escape( $_POST[ 'title' ], 'string' ) . "','" . $text . "','" . escape( $_POST[ 'recht' ], 'integer' ) . "')" );
        }
        // Aendern
    } elseif ( $_REQUEST[ 'um' ] == 'change' ) {
        if ( isset( $_POST[ 'gid' ] ) AND $_POST[ 'gid' ] == 'yes' ) {
            $gid1 = db_result( db_query( "SELECT `gid` FROM `prefix_kalender` WHERE `id` = " . escape( $_POST[ 'EID' ], 'integer' ) ), 0, 0 );
        }
        
        if ( isset( $_POST[ 'gid' ] ) AND $_POST[ 'gid' ] == 'yes' AND $gid1 > 0 ) {
            $_POST[ 'title' ] = escape( $_POST[ 'title' ], 'string' );
            $_POST[ 'recht' ] = escape( $_POST[ 'recht' ], 'integer' );
            db_query( "UPDATE `prefix_kalender` SET
				  `title`	= '" . $_POST[ 'title' ] . "',
				  `text`	= '" . $text . "',
				  `recht`	= '" . $_POST[ 'recht' ] . "'
			  WHERE `gid` = " . $gid1 );
        } else {
            db_query( "UPDATE `prefix_kalender` SET
			  	`time`	= '" . $time . "',
				  `title`	= '" . $_POST[ 'title' ] . "',
				  `text`	= '" . $text . "',
				  `recht`	= '" . $_POST[ 'recht' ] . "'
			  WHERE `id` = " . $_POST[ 'EID' ] . " LIMIT 1" );
        }
    }
}
// Loeschen
if ( !empty( $_GET[ 'del' ] ) AND $_GET[ 'del' ] == intval( $_GET[ 'del' ] ) ) {
    db_query( "DELETE FROM `prefix_kalender` WHERE `id` = " . escape( $_GET[ 'del' ], 'integer' ) . " LIMIT 1" );
}
if ( !empty( $_GET[ 'del_gid' ] ) AND $_GET[ 'del_gid' ] == intval( $_GET[ 'del_gid' ] ) ) {
    db_query( "DELETE FROM `prefix_kalender` WHERE `gid` = " . escape( $_GET[ 'del_gid' ], 'integer' ) );
}
// -----------------------------------------------------------|
if ( isset( $_GET[ 'edit' ] ) ) {
    $row     = db_fetch_assoc( db_query( "SELECT * FROM `prefix_kalender` WHERE `id` = " . escape( $_GET[ 'edit' ], 'integer' ) ) );
    $Faktion = 'change';
    $Fid     = $row[ 'id' ];
    $Ftitle  = $row[ 'title' ];
    $Ftext   = unescape( $row[ 'text' ] );
    $Fbegind = date( 'Y-n-j', $row[ 'time' ] );
    $Fzende  = $Fbegind;
    $Fhours  = date( 'G', $row[ 'time' ] );
    $Fmins   = date( 'i', $row[ 'time' ] );
    $Frecht  = $row[ 'recht' ];
    $Fsub    = '&Auml;ndern';
} else {
    $pubdate = getdate();
    $Faktion = 'insert';
    $Fid     = '';
    $Ftitle  = '';
    $Ftext   = '';
    $Fbegind = date( 'Y-n-j' );
    $Fzende  = $Fbegind;
    $Fhours  = $pubdate[ 'hours' ];
    $Fmins   = $pubdate[ 'minutes' ];
    $Fgrecht = '';
    $Frecht  = '';
    $Fsub    = 'Eintragen';
}

$arm = array( );
for ( $i = 0; $i < 60; $i++ ) {
    $arm[ $i ] = $i;
}
$ars = array( );
for ( $i = 0; $i < 24; $i++ ) {
    $ars[ $i ] = $i;
}

$tpl = new tpl( 'kalender.htm', 1 );

$limit  = 30; // Limit
$page   = ( $menu->getA( 1 ) == 'p' ? $menu->getE( 1 ) : 1 );
$MPL    = db_make_sites( $page, '', $limit, "?kalender", 'kalender' );
$anfang = ( $page - 1 ) * $limit;

$aus = array(
     'AKTION' => $Faktion,
    'MPL' => $MPL,
    'EID' => $Fid,
    'TITLE' => $Ftitle,
    'TEXT' => $Ftext,
    'stunden' => arliste( $Fhours, $ars, $tpl, 'stunden' ),
    'minuten' => arliste( $Fmins, $arm, $tpl, 'minuten' ),
    'zende' => $Fzende,
    'begind' => $Fbegind,
    'recht' => dbliste( $Frecht, $tpl, 'recht', "SELECT `id`,`name` FROM `prefix_grundrechte` ORDER BY `id` DESC" ),
    'FSUB' => $Fsub 
);

$tpl->set_ar_out( $aus, 0 );
if ( !isset( $_GET[ 'edit' ] ) ) {
    $tpl->out( 1 );
} else {
    $tpl->out( 2 );
}
$tpl->out( 3 );
unset( $aus );
// Liste
$result = db_query( 'SELECT `gid`,`id`,`title`,`time` FROM `prefix_kalender` ORDER BY `time` DESC LIMIT ' . $anfang . ',' . $limit );
while ( $row = db_fetch_assoc( $result ) ) {
    $aus = array(
         'ID' => $row[ 'id' ],
        'DATE' => date( 'd.m.Y', $row[ 'time' ] ),
        'TIME' => date( 'H:i', $row[ 'time' ] ),
        'TITLE' => $row[ 'title' ],
        'GID' => $row[ 'gid' ] 
    );
    $tpl->set_ar_out( $aus, 4 );
}

$tpl->set_ar_out( array(
     'MPL' => $MPL 
), 5 );

$design->footer();

?>