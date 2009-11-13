<?php
// Copyright by Manuel
// Support www.ilch.de
defined( 'main' ) or die( 'no direct access' );

$tpl = new tpl( 'user/boxen_login.htm' );

if ( loggedin() ) {
    if ( user_has_admin_right( $menu, false ) ) {
        $tpl->set( 'ADMIN', '<a class="box" href="admin.php?admin">' . $lang[ 'adminarea' ] . '</a>' );
    } else {
        $tpl->set( 'ADMIN', '' );
    }
    
    if ( $allgAr[ 'Fpmf' ] == 1 ) {
        $erg              = db_query( "SELECT COUNT(id) FROM `prefix_pm` WHERE `gelesen` = 0 AND `status` < 1 AND `eid` = " . $_SESSION[ 'authid' ] );
        $check_pm         = db_result( $erg, 0 );
        $nachrichten_link = '<a class="box" href="index.php?forum-privmsg">' . $lang[ 'messages' ] . '</a>&nbsp;(' . $check_pm . ')<br />';
    } else {
        $nachrichten_link = '';
    }
    
    $tpl->set( 'SID', session_id() );
    $tpl->set( 'NACHRICHTEN', $nachrichten_link );
    $tpl->set( 'NAME', $_SESSION[ 'authname' ] );
    $tpl->out( 0 );
} else {
    if ( empty( $_POST[ 'login_email' ] ) ) {
        $_POST[ 'login_email' ] = 'E-Mail';
    }
    if ( empty( $_POST[ 'login_pw' ] ) ) {
        $_POST[ 'login_pw' ] = 'הההההההה';
    }
    $regist = '';
    if ( $allgAr[ 'forum_regist' ] == 1 ) {
        $regist = ' &nbsp; &nbsp; <a href="index.php?user-regist">Regist</a>';
    }
    $tpl->set_ar_out( array(
         'regist' => $regist,
        'wdlink' => '?' . $allgAr[ 'smodul' ],
        'PASS' => $_POST[ 'login_pw' ],
        'EMAIL' => $_POST[ 'login_email' ] 
    ), 1 );
}
unset( $tpl );

?>