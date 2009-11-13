<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );

$title  = $allgAr[ 'title' ] . ' :: Forum :: Private Nachrichten';
$hmenu  = $extented_forum_menu . '<a class="smalfont" href="index.php?forum">Forum</a><b> &raquo; </b><a class="smalfont" href="index.php?forum-privmsg">Private Nachrichten</a>' . $extented_forum_menu_sufix;
$design = new design( $title, $hmenu, 1 );
$load   = Array(
     'jquery/auto-complete.js',
    'jquery/auto-complete.css' 
);
$design->header( $load );

if ( $allgAr[ 'Fpmf' ] != 1 ) {
    echo 'Private Nachrichten wurden von dem Administrator komplet gesperrt';
    echo '<br><a href="javascript:history.back(-1)">zurück</a>';
    $design->footer( 1 );
} elseif ( !loggedin() ) {
    echo '<br>Gäste dürfen keine Privaten Nachrichten Verschicken!';
    $tpl = new tpl( 'user/login' );
    $tpl->set_out( 'WDLINK', 'index.php', 0 );
    $design->footer( 1 );
} elseif ( db_result( db_query( "SELECT `opt_pm` FROM `prefix_user` WHERE `id` = " . $_SESSION[ 'authid' ] ), 0 ) == 0 ) {
    echo 'Im <a href="index.php?user-profil">Profil</a> einstellen das du die PrivMsg Funktion nutzen m&ouml;chtest';
    $design->footer( 1 );
}

$uum = $menu->get( 2 );
switch ( $uum ) {
    case 'new':
        // neue pm schreiben und eintragen
        $show_formular = true;
        $txt           = '';
        $bet           = '';
        
        if ( isset( $_POST[ 'sub' ] ) ) {
            $txt  = escape( $_POST[ 'txt' ], 'textarea' );
            $bet  = escape( $_POST[ 'bet' ], 'string' );
            $name = escape( $_POST[ 'name' ], 'string' );
            if ( 1 == db_result( db_query( "SELECT count(*) FROM `prefix_user` WHERE `name` = BINARY '" . $name . "'" ), 0 ) ) {
                $show_formular = false;
            } else {
                echo 'Dieser Empf&auml;nger konnte nicht gefunden werden';
            }
        }
        
        if ( $show_formular === true ) {
            $name   = '';
            $empfid = 0;
            if ( isset( $_REQUEST[ 'empfid' ] ) ) {
                $empfid = escape( $_REQUEST[ 'empfid' ], 'integer' );
            }
            $empfid = escape( $empfid, 'integer' );
            if ( $empfid > 0 ) {
                $name = db_result( db_query( "SELECT `name` FROM `prefix_user` WHERE `id` = " . $empfid ), 0 );
            }
            $ar = array(
                 'name' => $name,
                'SMILIES' => getsmilies(),
                'TXT' => $txt,
                'BET' => $bet 
            );
            
            if ( isset( $_REQUEST[ 'text' ] ) ) {
                $ar[ 'TXT' ] = unescape( escape( $_REQUEST[ 'text' ], 'textarea' ) );
            }
            if ( isset( $_REQUEST[ 'anhang' ] ) ) {
                $x = explode( "\n", unescape( escape( urldecode( $_REQUEST[ 'anhang' ] ), 'textarea' ) ) );
                $n = '';
                for ( $i = 0; $i <= count( $x ); $i++ ) {
                    if ( empty( $x[ $i ] ) ) {
                        continue;
                    }
                    $n .= '> ' . $x[ $i ] . "\n";
                }
                $ar[ 'TXT' ] .= "\n\n" . $n;
            }
            if ( isset( $_POST[ 'bet' ] ) ) {
                $ar[ 'BET' ] = unescape( escape( $_REQUEST[ 'bet' ], 'string' ) );
            }
            if ( isset( $_POST[ 're' ] ) AND strpos( $ar[ 'BET' ], 're' ) === false AND strpos( $ar[ 'BET' ], 'Re' ) === false AND strpos( $ar[ 'BET' ], 'RE' ) === false ) {
                $ar[ 'BET' ] = 'Re(1): ' . $ar[ 'BET' ];
            } elseif ( isset( $_POST[ 're' ] ) ) {
                $x = preg_replace( "/re\((\d+)\):.*/i", "\\1", trim( $ar[ 'BET' ] ) );
                if ( is_numeric( $x ) ) {
                    $x           = $x + 1;
                    $ar[ 'BET' ] = preg_replace( "/(re)\(\d+\):(.*)/i", "\\1(" . $x . "):\\2", $ar[ 'BET' ] );
                }
            }
            
            $tpl = new tpl( 'forum/pm/new' );
            $tpl->set_ar_out( $ar, 0 );
        } else {
            $eid = db_result( db_query( "SELECT `id` FROM `prefix_user` WHERE `name` = BINARY '" . $name . "'" ), 0 );
            sendpm( $_SESSION[ 'authid' ], $eid, $bet, $txt );
            wd( 'index.php?forum-privmsg', 'Die Nachricht wurde erfolgreich gesendet' );
        }
        break;
    case 'showmsg':
        // message anzeigen lassen
        $pid   = escape( $menu->get( 3 ), 'integer' );
        $soeid = ( $menu->get( 4 ) == 's' ? 'eid' : 'sid' );
        $erg   = db_query( "SELECT `a`.`gelesen`, `a`.`eid`, `a`.`sid`, `a`.`id`, `b`.`name`, `a`.`titel`, `a`.`time`, `a`.`txt` FROM `prefix_pm` `a` LEFT JOIN `prefix_user` `b` ON `a`.`" . $soeid . "` = `b`.`id` WHERE `a`.`id` = " . $pid );
        $row   = db_fetch_assoc( $erg );
        if ( ( $row[ 'sid' ] != $_SESSION[ 'authid' ] AND $menu->get( 4 ) == 's' ) OR ( $row[ 'eid' ] != $_SESSION[ 'authid' ] AND $menu->get( 4 ) != 's' ) ) {
            $design->footer( 1 );
        }
        if ( $row[ 'gelesen' ] == 0 AND $menu->get( 4 ) != 's' ) {
            db_query( "UPDATE `prefix_pm` SET `gelesen` = 1 WHERE `id` = " . $pid );
        }
        $row[ 'time' ]   = date( 'd M. Y - H:i', $row[ 'time' ] );
        $row[ 'anhang' ] = urlencode( $row[ 'txt' ] );
        $row[ 'txt' ]    = bbcode( unescape( $row[ 'txt' ] ) );
        if ( $menu->get( 4 ) == 's' ) {
            $tpl = new tpl( 'forum/pm/show_mess_send' );
        } else {
            $tpl = new tpl( 'forum/pm/show_mess' );
        }
        $tpl->set_ar_out( $row, 0 );
        break;
    case 'delete':
        // löschen von nachrichten
        if ( $menu->get( 3 ) != '' AND $menu->get( 4 ) == '' ) {
            $_POST[ 'delids' ][ ] = $menu->get( 3 );
        } elseif ( $menu->get( 3 ) != '' AND $menu->get( 4 ) == 's' ) {
            $_POST[ 'delsids' ][ ] = $menu->get( 3 );
        }
        if ( empty( $_POST[ 'delids' ] ) AND empty( $_POST[ 'delsids' ] ) ) {
            echo 'Es wurde keine Nachricht zum l&ouml;schen gew&auml;hlt <br /><br />';
            echo '<a href="javascript:history.back(-1)"><b>&laquo;</b> zur&uuml;ck</a>';
        } else {
            if ( ( empty( $_POST[ 'delids' ] ) AND empty( $_POST[ 'delsids' ] ) ) OR empty( $_POST[ 'sub' ] ) ) {
                $delids = ( empty( $_POST[ 'delids' ] ) ? $_POST[ 'delsids' ] : $_POST[ 'delids' ] );
                $s      = ( empty( $_POST[ 'delids' ] ) ? '' : 's' );
                echo '<form action="index.php?forum-privmsg-delete" method="POST">';
                $i = 0;
                if ( !is_array( $delids ) ) {
                    $delids = array(
                         $delids 
                    );
                }
                foreach ( $delids as $a ) {
                    $i++;
                    echo '<input type="hidden" name="del' . $s . 'ids[]" value="' . $a . '">';
                }
                echo '<br>Wollen Sie ';
                echo ( $i > 1 ? 'die (' . $i . ') Nachrichten ' : 'die Nachricht ' );
                echo 'wirklich löschen ?<br><br><input type="submit" value=" Ja " name="sub"> &nbsp; &nbsp; <input type="button" value="Nein" onclick="document.location.href =\'?forum-privmsg\'"></form>';
            } else {
                $delids = ( empty( $_POST[ 'delids' ] ) ? $_POST[ 'delsids' ] : $_POST[ 'delids' ] );
                $s      = ( empty( $_POST[ 'delids' ] ) ? '' : 's' );
                $soeid  = ( $s == 's' ? 'sid' : 'eid' );
                $stat1  = ( $s == 's' ? 1 : -1 );
                $stat2  = $stat1 * -1;
                $i      = 0;
                if ( !is_array( $delids ) ) {
                    $delids = Array(
                         $delids 
                    );
                }
                foreach ( $delids as $a ) {
                    if ( is_numeric( $a ) AND $a != 0 ) {
                        db_query( "DELETE FROM `prefix_pm` WHERE `id` = " . $a . " AND " . $soeid . " = " . $_SESSION[ 'authid' ] . " AND `status` = " . $stat1 );
                        db_query( "UPDATE prefix_pm SET `status` = " . $stat2 . " WHERE `id` = " . $a . " AND " . $soeid . " = " . $_SESSION[ 'authid' ] );
                        $i++;
                    }
                }
                echo 'Es wurd';
                echo ( $i > 1 ? 'en (' . $i . ') Nachrichten ' : 'e eine Nachricht ' );
                echo 'erfolgreich gelöscht <br /><br /><a href="index.php?forum-privmsg">zum Nachrichten Eingang</a>';
            }
        }
        break;
    case 'showsend':
        $tpl = new tpl( 'forum/pm/showsend' );
        $ad  = $menu->getA( 3 ) == 'a' ? 'ASC' : 'DESC';
        $tpl->set_out( 'ad', $ad == 'ASC' ? 'd' : 'a', 0 );
        $class = 'Cmite';
        switch ( $menu->getE( 3 ) ) {
            default:
            case '3':
                $order = "`a`.`time` " . $ad;
                break;
            case '2':
                $order = "`b`.`name` " . $ad . ", `a`.`time` DESC";
                break;
            case '1':
                $order = "`a`.`titel` " . $ad . ", `a`.`time` DESC";
                break;
        }
        $abf = "SELECT `a`.`titel`, `b`.`name` as `empf`, `a`.`id`, `a`.`time` FROM `prefix_pm` `a` LEFT JOIN `prefix_user` `b` ON `a`.`eid` = `b`.`id` WHERE `a`.`sid` = " . $_SESSION[ 'authid' ] . " AND `a`.`status` >= 0 ORDER BY " . $order;
        $erg = db_query( $abf );
        while ( $row = db_fetch_assoc( $erg ) ) {
            $class          = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
            $row[ 'class' ] = $class;
            $row[ 'date' ]  = date( 'd.m.Y', $row[ 'time' ] );
            $row[ 'time' ]  = date( 'H:i', $row[ 'time' ] );
            $row[ 'BET' ]   = ( trim( $row[ 'titel' ] ) == '' ? ' -- kein Nachrichtentitel -- ' : $row[ 'titel' ] );
            $tpl->set_ar_out( $row, 1 );
        }
        $tpl->out( 2 );
        break;
    default:
        // message übersicht.
        $tpl = new tpl( 'forum/pm/show' );
        $ad  = $menu->getA( 2 ) == 'a' ? 'ASC' : 'DESC';
        $tpl->set_out( 'ad', $ad == 'ASC' ? 'd' : 'a', 0 );
        $class = 'Cmite';
        switch ( $menu->getE( 2 ) ) {
            default:
            case '3':
                $order = "`a`.`time` " . $ad;
                break;
            case '2':
                $order = "`b`.`name` " . $ad . ", `a`.`time` DESC";
                break;
            case '1':
                $order = "`a`.`titel` " . $ad . ", `a`.`time` DESC";
                break;
        }
        $abf = "SELECT `a`.`titel` as `BET`, `a`.`gelesen` as `NEW`, `b`.`name` as `ABS`, `a`.`id` as `ID`, `a`.`time` FROM `prefix_pm` `a` LEFT JOIN `prefix_user` `b` ON `a`.`sid` = `b`.`id` WHERE `a`.`eid` = " . $_SESSION[ 'authid' ] . " AND `a`.`status` <= 0 ORDER BY " . $order;
        $erg = db_query( $abf );
        while ( $row = db_fetch_assoc( $erg ) ) {
            $class          = ( $class == 'Cmite' ? 'Cnorm' : 'Cmite' );
            $row[ 'NEW' ]   = ( $row[ 'NEW' ] == 0 ? '<b><i>neu</i></b>' : '' );
            $row[ 'CLASS' ] = $class;
            $row[ 'BET' ]   = ( trim( $row[ 'BET' ] ) == '' ? ' -- kein Nachrichtentitel -- ' : $row[ 'BET' ] );
            $row[ 'date' ]  = date( 'd.m.Y', $row[ 'time' ] );
            $row[ 'time' ]  = date( 'H:i', $row[ 'time' ] );
            $tpl->set_ar_out( $row, 1 );
        }
        $tpl->out( 2 );
        break;
}
$design->footer();

?>