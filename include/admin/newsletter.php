<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );
defined( 'admin' ) or die( 'only admin access' );

function XAJAX_changeList( $select )
{
    $objResponse = new xajaxResponse();

    if ( $select == 'Normal' ) {
        $auswahl = array(
             'u0' => 'an alle User'
        );

        $erg = db_query( "SELECT `name`,`id` FROM `prefix_groups` ORDER BY `id`" );
        while ( $RRrow = db_fetch_object( $erg ) ) {
            $auswahl[ 'g' . $RRrow->id ] = $RRrow->name;
        }

        $listeB = '';
        $listeT = '';

        foreach ( $auswahl as $k => $v ) {
            if ( strpos( $k, 'u' ) !== false ) {
                $listeB .= '<option value="P' . $k . '">' . $v . ' PrivMsg</option>' . "\n";
                $listeB .= '<option value="E' . $k . '">' . $v . ' eMail</option>' . "\n";
            } elseif ( strpos( $k, 'g' ) !== false ) {
                $listeT .= '<option value="P' . $k . '">' . $v . ' PrivMsg</option>' . "\n";
                $listeT .= '<option value="E' . $k . '">' . $v . ' eMail</option>' . "\n";
            }
        }

        $content = <<<END
            <select id="nl_auswahl" name="auswahl">
                <option value="Enews" selected="selected">eMail Newsletter</option>
                <optgroup label="Benutzer">
                    {$listeB}
                </optgroup>
                <optgroup label="Gruppen">
                    {$listeT}
                </optgroup>
    		</select>
END;
        $objResponse->assign( 'cb_html_cont', 'style.display', '' );
    } else {
        $erg    = db_query( "SELECT * FROM `prefix_grundrechte` ORDER BY `id` ASC" );
        $listeG = '';

        while ( $row = db_fetch_assoc( $erg ) ) {
            $listeG .= '<optgroup label="' . $row[ 'name' ] . '">';
            $listeG .= '<option value="Pr' . $row[ 'id' ] . '"> PrivMsg</option>';
            $listeG .= '<option value="Er' . $row[ 'id' ] . '"> eMail</option>';
            $listeG .= '</optgroup>';
        }

        $content = <<<END
            <select name="auswahl" id="nl_auswahl">
                <option selected="selected" disabled="disabled">Bitte treffen Sie eine Auswahl</option>
                    {$listeG}
            </select>
			<input type="checkbox" name="andhigher" id="cb_andhigher" value="1" />
			<label for="cb_andhigher">und für alle höheren Rechte</label>
END;
    }

    $objResponse->assign( 'list', 'innerHTML', $content );
    $objResponse->setEvent( 'nl_auswahl', 'onchange', 'checkEmail();' );
    return $objResponse;
}

$xajax = new xajax( 'http://' . $_SERVER[ 'HTTP_HOST' ] . $_SERVER[ 'SCRIPT_NAME' ] . '?newsletter=0' );
$xajax->configureMany( array(
     'characterEncoding' => 'ISO-8859-1',
    'decodeUTF8Input' => true
) );

$xajax->registerFunction( 'XAJAX_changeList' );
$xajax->processRequest();

$design = new design( 'Ilch Admin-Control-Panel :: Newsletter', '', 2 );
$design->header();

if ( isset( $_POST[ 'SEND' ] ) and chk_antispam( 'newsletter', true ) ) {
    $mailopm = substr( $_POST[ 'auswahl' ], 0, 1 );
    $usrogrp = substr( $_POST[ 'auswahl' ], 1, 1 );

    if ( $_POST[ 'auswahl' ] == 'Enews' ) {
        $q = "SELECT `email` FROM `prefix_newsletter`";
    } elseif ( $usrogrp == 'u' ) {
        $q = "SELECT `email`,`name` as `uname`,`id` as `uid` FROM `prefix_user` WHERE `recht` <= '-1'";
    } elseif ( $usrogrp == 'g' ) {
        $gid = substr( $_POST[ 'auswahl' ], 2, strlen( $_POST[ 'auswahl' ] ) - 1 );
        $q   = "SELECT `b`.`email`, `b`.`name` as `uname`, `b`.`id` as `uid` FROM `prefix_groupusers` `a` LEFT JOIN `prefix_user` `b` ON `a`.`uid` = `b`.`id` WHERE `a`.`gid` = '$gid'";
    } elseif ( $usrogrp == 'r' ) {
        $q = "SELECT `email`,`id` as `uid` FROM `prefix_user` WHERE `recht` " . ( isset( $_POST[ 'andhigher' ] ) ? '<' : '' ) . "= '" . substr( $_POST[ 'auswahl' ], 2, strlen( $_POST[ 'auswahl' ] ) - 1 ) . "'";
    }

    $erg = db_query( $q );

    $zahler = 0;

    if ( db_num_rows( $erg ) > 0 ) {
        if ( $mailopm == 'E' ) {
            $emails = array(
                 'bbc',
                $allgAr[ 'adminMail' ]
            );
            while ( $row = db_fetch_object( $erg ) ) {
                if ( !in_array( $row->email, $emails ) and preg_match( '/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])' . '(([a-z0-9-])*([a-z0-9]))+' . '(\.([a-z0-9])([-a-z0-9_-])?([a-z0-9])+)+$/i', $row->email ) == 1 ) {
                    $emails[ ] = $row->email;
                    $zahler++;
                }
            }
            icmail( $emails, $_POST[ 'bet' ], $_POST[ 'txt' ], '', isset( $_POST[ 'html' ] ) );
        } elseif ( $mailopm == 'P' ) {
            $uids = array( );
            while ( $row = db_fetch_object( $erg ) ) {
                $uids[ ] = $row->uid;
                $zahler++;
            }
            sendpm( $_SESSION[ 'authid' ], $uids, escape( $_POST[ 'bet' ], 'string' ), escape( $_POST[ 'txt' ], 'string' ), -1 );
        }

        if ( $mailopm == 'E' ) {
            $eMailorPmsg = 'eMail(s)';
        } elseif ( $mailopm == 'P' ) {
            $eMailorPmsg = 'Private Nachrichte(n)';
        }

        wd( 'admin.php?newsletter', 'Es wurde(n) ' . $zahler . ' ' . $eMailorPmsg . ' verschickt.', 5 );
    } else {
        wd( 'admin.php?newsletter', 'F&uuml;r diese Auswahl konnte nichts gefunden werden.', 5 );
    }
} else {
    echo $xajax->printJavascript();
    $tpl = new tpl( 'newsletter', 1 );
    $tpl->set_out( 'antispam', get_antispam( 'newsletter', 0, true ), 0 );
}

$design->footer();

?>