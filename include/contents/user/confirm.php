<?php
// Copyright by: Manuel
// Support: www.ilch.de
defined( 'main' ) or die( 'no direct access' );

$title  = $allgAr[ 'title' ] . ' :: User :: Confirm';
$hmenu  = $extented_forum_menu . '<a class="smalfont" href="?user">User</a><b> &raquo; </b>Confirm' . $extented_forum_menu_sufix;
$design = new design( $title, $hmenu, 1 );
$design->header();

$abgelaufen = time() - 2592000; // 30 tage
$abgelaufen = date( 'Y-m-d H:i:s', $abgelaufen );
db_query( "DELETE FROM `prefix_usercheck` WHERE `datime` < '" . $abgelaufen . "'" );

$erg = db_query( "SELECT * FROM `prefix_usercheck` WHERE `check` = '" . escape( $_GET[ 'check' ], 'string' ) . "'" );
if ( db_num_rows( $erg ) == 1 ) {
    $row = db_fetch_assoc( $erg );
    switch ( $row[ 'ak' ] ) {
        // confirm regist
        case 1:
            $lower = get_lower( $row );
            if ( 0 == db_count_query( "SELECT COUNT(*) FROM `prefix_user` WHERE `name_clean` = BINARY '" . $lower[ 'name' ] . "'" ) ) {
                db_query( "INSERT INTO `prefix_user` (`name`,`name_clean`,`pass`,`recht`,`regist`,`llogin`,`email`,`status`,`opt_mail`,`opt_pm`)
			  VALUES('" . $row[ 'name' ] . "','" . $lower[ 'name' ] . "','" . $row[ 'pass' ] . "',-1,'" . time() . "','" . time() . "','" . $lower[ 'email' ] . "',1,1,1)" );
                
                echo $lang[ 'confirmregist' ];
            } else {
                echo $lang[ 'confirmregistfailed' ];
            }
            break;
        // confirm new pass
        case 2:
            db_query( "UPDATE `prefix_user` SET `pass` = '" . $row[ 'pass' ] . "' WHERE `name` = BINARY '" . $row[ 'name' ] . "'" );
            echo $lang[ 'confirmpassword' ];
            break;
        // confirm new email
        case 3:
            list( $id, $muell ) = explode( '||', $row[ 'check' ] );
            db_query( "UPDATE `prefix_user` SET `email` = '" . get_lower( $row[ 'email' ] ) . "' WHERE `id` = " . escape( $id, 'integer' ) );
            echo $lang[ 'confirmemail' ];
            break;
        // ak 4 wurde besetzt fuer joinus anfragen...
        case 4:
            break;
        // ak 5 remove account
        case 5:
            list( $id, $muell ) = explode( '-remove-', $row[ 'check' ] );
            if ( $id != $_SESSION[ 'authid' ] ) {
                break;
            }
            user_remove( $id );
            wd( 'index.php', 'Dein Account wurde gel&ouml;scht. Du wirst nun auf die Startseite geleitet.', 7 );
            break;
    }
    db_query( "DELETE FROM `prefix_usercheck` WHERE `check` = '" . $row[ 'check' ] . "'" );
} else {
    echo $lang[ 'confirmfailure' ];
}

$design->footer();

?>